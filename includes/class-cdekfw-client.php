<?php
/**
 * CDEK client
 *
 * @package CDEK/Client
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Client API connection
 *
 * @class CDEKFW_Client
 */
class CDEKFW_Client {
	/**
	 * Main api url.
	 *
	 * @var string
	 */
	private static $api_url = 'https://api.cdek.ru/';

	/**
	 * Calculate shipping rate
	 *
	 * @param array $args Shipping params.
	 *
	 * @return bool|mixed|null
	 */
	public static function calculate_rate( $args ) {
		$date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );

		$args = array_merge(
			$args,
			array(
				'version'     => '1.0',
				'currency'    => get_woocommerce_currency(),
				'dateExecute' => $date,
			)
		);

		return self::get_data_from_api( 'calculator/calculate_price_by_json.php', $args );
	}

	/**
	 * Create new order https://confluence.cdek.ru/pages/viewpage.action?pageId=29923926
	 *
	 * @param array $args Orders params.
	 *
	 * @return bool|mixed|null
	 */
	public static function create_order( $args ) {
		return self::get_data_from_api( 'v2/orders', $args );
	}

	/**
	 * Get delivery points https://confluence.cdek.ru/pages/viewpage.action?pageId=36982648
	 *
	 * @return array|bool
	 */
	public static function get_pvz_list() {
		$postcode        = WC()->customer->get_shipping_postcode();
		$state           = WC()->customer->get_shipping_state();
		$city            = WC()->customer->get_shipping_city();
		$country         = WC()->customer->get_shipping_country();
		$is_cod          = 'allowed_cod';
		$delivery_points = array();

		if ( CDEKFW::is_pro_active() && $state && $city ) {
			$postcode = CDEKFW_PRO_Ru_Base::get_index_based_on_address( $state, $city );
		}

		$args = array(
			'postal_code'  => $postcode,
			'country_code' => $country,
		);

		$items = self::get_data_from_api( add_query_arg( $args, 'v2/deliverypoints' ), array(), 'GET' );

		if ( ! $items ) {
			return false;
		}

		foreach ( $items as $item ) {
			if ( isset( $item['location']['adress'] ) && isset( $item['location']['latitude'] ) ) {
				$delivery_points[] = array(
					'code'        => $item['code'],
					'name'        => $item['name'],
					'address'     => $item['location']['adress'],
					'coordinates' => $item['location']['latitude'] . ',' . $item['location']['longitude'],
				);
			}
		}

		return $delivery_points;
	}

	/**
	 * Get new updated version for delivery points from API
	 *
	 * @return bool
	 */
	public static function retrieve_all_pvz() {
		$data = self::get_data_from_api( 'v2/deliverypoints', array(), 'GET' );

		if ( ! $data ) {
			return false;
		}

		$file_all = fopen( CDEK_ABSPATH . 'includes/lists/pvz-all.json', 'w+' );
		fwrite( $file_all, json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
		fclose( $file_all );

		return true;
	}

	/**
	 * Get new updated version for delivery points from API
	 *
	 * @return bool
	 */
	public static function retrieve_all_city_codes() {
		$url  = add_query_arg(
			array(
				'country_codes' => array( 'RU' ),
				'size'          => 99999,
				'page'          => 0,
			),
			'v2/location/cities'
		);
		$data = self::get_data_from_api( $url, array(), 'GET' );

		if ( ! $data ) {
			return false;
		}

		$file_all = fopen( CDEK_ABSPATH . 'includes/lists/cities-ru.json', 'w+' );
		fwrite( $file_all, json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
		fclose( $file_all );

		return true;
	}

	/**
	 * Get new updated version for delivery points from API
	 *
	 * @return bool
	 */
	public static function retrieve_all_region_codes() {
		$url  = add_query_arg(
			array(),
			'v2/location/regions'
		);
		$data = self::get_data_from_api( $url, array(), 'GET' );

		if ( ! $data ) {
			return false;
		}

		$file_all = fopen( CDEK_ABSPATH . 'includes/lists/regions.json', 'w+' );
		fwrite( $file_all, json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
		fclose( $file_all );

		return true;
	}

	/**
	 * Get client credentials for requests
	 *
	 * If no credentials are set use test data
	 *
	 * @return array
	 */
	public static function get_client_credentials() {
		if ( get_option( 'cdek_account' ) ) {
			return array(
				'account'  => get_option( 'cdek_account' ),
				'password' => get_option( 'cdek_password' ),
				'api_url'  => 'https://api.cdek.ru/',
			);
		} else {
			return array(
				'account'  => 'EMscd6r9JnFiQ3bLoyjJY6eM78JrJceI',
				'password' => 'PjLZkKBHEiLK3YsjtNrt3TGNG0ahs3kG',
				'api_url'  => 'https://api.edu.cdek.ru/',
			);
		}
	}

	/**
	 * Get client auth token
	 *
	 * @return string|mixed
	 */
	public static function get_client_auth_token() {
		$client     = self::get_client_credentials();
		$hash       = 'cdek_cache_auth_token_' . md5( $client['account'] );
		$auth_token = get_transient( $hash );

		if ( ! $auth_token ) {
			$parameters = array(
				'grant_type'    => 'client_credentials',
				'client_id'     => $client['account'],
				'client_secret' => $client['password'],
			);

			$request         = add_query_arg( $parameters, $client['api_url'] . 'v2/oauth/token' );
			$remote_response = wp_remote_post(
				$request,
				array(
					'timeout'   => 50,
					'sslverify' => false,
					'headers'   => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
				)
			);

			$error_msg = esc_html__( 'Could not get client auth token.', 'cdek-for-woocommerce' );

			if ( ! $remote_response ) {
				CDEKFW::log_it( $error_msg . ' ' . wp_json_encode( $remote_response ), 'error' );

				return false;
			}

			$response_code = wp_remote_retrieve_response_code( $remote_response );

			if ( 200 !== $response_code ) {
				CDEKFW::log_it( $error_msg . ' ERROR: ' . wp_json_encode( $response_code ) . ' ' . wp_remote_retrieve_body( $remote_response ), 'error' );

				return false;
			}

			$response_body = json_decode( wp_remote_retrieve_body( $remote_response ), true );

			if ( ! isset( $response_body['access_token'] ) ) {
				CDEKFW::log_it( $error_msg . ' ' . wp_json_encode( $response_body ), 'error' );

				return false;
			}

			$auth_token = $response_body['access_token'];

			set_transient( $hash, $auth_token, $response_body['expires_in'] );
		}

		return $auth_token;
	}

	/**
	 * Connect to Post API and get body for requested URL
	 *
	 * @param string $url API url.
	 * @param array  $body Request body.
	 * @param string $method Type.
	 *
	 * @return bool|mixed|null
	 */
	public static function get_data_from_api( $url, $body = array(), $method = 'POST' ) {
		$client = self::get_client_credentials();
		$hash   = self::get_request_hash( $client['account'], $url, $body );
		$cache  = get_transient( $hash );

		if ( $cache ) {
			if ( isset( $cache['error'] ) ) {
				CDEKFW::log_it( esc_html__( 'API request error:', 'cdek-for-woocommerce' ) . ' ' . $url . ' ' . wp_json_encode( $cache, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . 'Body' . wp_json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

				return false;
			}

			return $cache;
		}

		$client_auth_token = self::get_client_auth_token();

		if ( ! $client_auth_token ) {
			return false;
		}

		$remote_response = wp_remote_request(
			self::$api_url . $url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $client_auth_token,
					'Accept'        => 'application/json;charset=UTF-8',
					'Content-Type'  => 'application/json',
				),
				'method'  => $method,
				'body'    => $body ? wp_json_encode( $body, JSON_UNESCAPED_UNICODE ) : '',
				'timeout' => 100, // must be that big for huge requests like getting PVZ list.
			)
		);

		CDEKFW::log_it( esc_html__( 'Making request to get:', 'cdek-for-woocommerce' ) . ' ' . $url . ' ' . esc_html__( 'with the next body:', 'cdek-for-woocommerce' ) . ' ' . wp_json_encode( $body, JSON_UNESCAPED_UNICODE ) );

		if ( is_wp_error( $remote_response ) ) {
			CDEKFW::log_it( esc_html__( 'Cannot connect to', 'cdek-for-woocommerce' ) . ' ' . $url . ' ' . $remote_response->get_error_message() . ' Body: ' . wp_json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

			return false;
		}

		$response_code = intval( wp_remote_retrieve_response_code( $remote_response ) );

		if ( ! in_array( $response_code, array( 200, 202 ), true ) ) {
			CDEKFW::log_it( esc_html__( 'Cannot connect to', 'cdek-for-woocommerce' ) . ' ' . $url . ' ' . esc_html__( 'response status code:', 'cdek-for-woocommerce' ) . ' ' . $response_code . ' ' . wp_remote_retrieve_body( $remote_response ) . ' Body: ' . wp_json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

			return false;
		}

		$response_body = json_decode( wp_remote_retrieve_body( $remote_response ), true );

		if ( isset( $response_body['error'] ) ) {
			CDEKFW::log_it( esc_html__( 'API request error:', 'cdek-for-woocommerce' ) . ' ' . $url . ' ' . wp_json_encode( $response_body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . 'Body' . wp_json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

			return false;
		}

		// Set transient before checking the errors to prevent double requests with the same error.
		set_transient( $hash, $response_body, DAY_IN_SECONDS );

		return $response_body;
	}

	/**
	 * Get hash by removing time relevant data
	 *
	 * @param string $account Account ID.
	 * @param string $url Request url.
	 * @param array  $body Request body.
	 *
	 * @return string
	 */
	public static function get_request_hash( $account, $url, $body ) {
		unset( $body['authLogin'] );
		unset( $body['secure'] );
		unset( $body['dateExecute'] );

		return 'cdek_cache_' . md5( $account . $url . wp_json_encode( $body ) );
	}
}


function cdek_test() {
	// echo gmdate('Y-m-d');
	// $client_auth_token = CDEKFW_Client::get_client_auth_token();
	// $body = json_encode( [
	// 'version'              => '1.0',
	// 'currency'    => get_woocommerce_currency(),
	// 'authLogin'   => $client['account'],
	// 'secure'      => md5( $date . '&' . $client['password'] ),
	// 'dateExecute'          => gmdate( 'Y-m-d' ),
	// 'tariffId'             => 1,
	// 'receiverCityPostCode' => 675000,
	// 'senderCityPostCode'   => 101000,
	// 'goods'                => [
	// [
	// "weight" => "0.3",
	// "length" => "5",
	// "width"  => "20",
	// "height" => "10"
	// ]
	// ]

	// 'postal_code'  => '67500',
	// 'country_code' => 'RU',
	// ] );

	// $res  = wp_remote_request( 'https://api.cdek.ru/v2/deliverypoints?postal_code=675000&country_code=RU',
	// [
	// 'headers' => [
	// 'Content-Type' => 'application/json',
	// 'Authorization' => 'Bearer ' . $client_auth_token,
	// ],
	// 'method'  => 'GET',
	// 'body'    => $body
	// ] );

	$client = CDEKFW_Client::get_client_credentials();

	$parameters = array(
		'grant_type'    => 'client_credentials',
		'client_id'     => $client['account'],
		'client_secret' => $client['password'],
	);

	$request         = add_query_arg( $parameters, $client['api_url'] . 'v2/oauth/token' );
	$remote_response = wp_remote_post(
		$request,
		array(
			'timeout'   => 50,
			'sslverify' => false,
			'headers'   => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
		)
	);

	var_dump( json_decode( wp_remote_retrieve_body( $remote_response ), true ) );
}


// add_action( 'wp_footer', 'cdek_test' );
