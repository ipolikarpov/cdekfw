<?php
/**
 * Settings for CDEK shipping.
 *
 * @package CDEK/Classes/Shipping
 */

defined( 'ABSPATH' ) || exit;

$post_index_message = '';

if ( ! CDEKFW::is_pro_active() ) {
	$post_index_message = '<br><br><span style="color: red">Пожалуйста, обратите внимание,</span><span style="color: #007cba"> что расчет доставки происходит только от индекса отправителя до индекса получателя. Убедитесь, что в вашем магазине поле индекс при оформлении заказа не отключено и является обязательным для заполнения, иначе расчет будет невозможно произвести.</span>';
}

$settings = array(
	'title'              => array(
		'title'       => __( 'Method title', 'cdek-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'cdek-for-woocommerce' ),
		'default'     => __( 'CDEK', 'cdek-for-woocommerce' ),
	),
	'tax_status'         => array(
		'title'   => __( 'Tax status', 'cdek-for-woocommerce' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'taxable',
		'options' => array(
			'taxable' => __( 'Taxable', 'cdek-for-woocommerce' ),
			'none'    => _x( 'None', 'Tax status', 'cdek-for-woocommerce' ),
		),
	),
	'tariff'             => array(
		'title'       => __( 'Tariff', 'cdek-for-woocommerce' ),
		'description' => __( 'Please note. Not all tariffs available for some particular destinations. For example international shipment will work only for specific countries. So please always check what tariffs for what destination are available by checking official calculator.', 'cdek-for-woocommerce' ) . ' <a href="https://cdek.ru/calculate" target="_blank">https://cdek.ru/calculate</a>' . $post_index_message,
		'type'        => 'select',
		'class'       => 'wc-enhanced-select',
		'default'     => 'taxable',
		'options'     => array(
			1   => 'Экспресс лайт дверь-дверь (до 30 кг)',
			3   => 'Супер-экспресс до 18 дверь-дверь (до 30 кг)',
			5   => 'Экономичный экспресс склад-склад',
			10  => 'Экспресс лайт склад-склад (до 30 кг)',
			11  => 'Экспресс лайт склад-дверь (до 30 кг)',
			12  => 'Экспресс лайт дверь-склад (до 30 кг)',
			15  => 'Экспресс тяжеловесы склад-склад (до 30 кг)',
			16  => 'Экспресс тяжеловесы склад-дверь (до 30 кг)',
			17  => 'Экспресс тяжеловесы дверь-склад (до 30 кг)',
			18  => 'Экспресс тяжеловесы дверь-дверь (до 30 кг)',
			57  => 'Супер-экспресс до 9 дверь-дверь (до 30 кг)',
			58  => 'Супер-экспресс до 10 дверь-дверь (до 30 кг)',
			59  => 'Супер-экспресс до 12 дверь-дверь (до 30 кг)',
			60  => 'Супер-экспресс до 14 дверь-дверь (до 30 кг)',
			61  => 'Супер-экспресс до 16 дверь-дверь (до 30 кг)',
			62  => 'Магистральный экспресс склад-склад',
			63  => 'Магистральный супер-экспресс склад-склад',
			118 => 'Экономичный экспресс дверь-дверь',
			119 => 'Экономичный экспресс склад-дверь',
			120 => 'Экономичный экспресс дверь-склад',
			121 => 'Магистральный экспресс дверь-дверь',
			122 => 'Магистральный экспресс склад-дверь',
			123 => 'Магистральный экспресс дверь-склад',
			124 => 'Магистральный супер-экспресс дверь-дверь',
			125 => 'Магистральный супер-экспресс склад-дверь',
			126 => 'Магистральный супер-экспресс дверь-склад',
			// Экспресс-доставка за/из-за границы документов и писем.
			7   => 'Международный экспресс документы дверь-дверь (до 5 кг)',
			// Экспресс-доставка за/из-за границы грузов и посылок до 30 кг.
			8   => 'Международный экспресс грузы дверь-дверь (до 30 кг)',
			// Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.
			136 => 'Посылка склад-склад (до 30 кг)',
			137 => 'Посылка склад-дверь (до 30 кг)',
			138 => 'Посылка дверь-склад (до 30 кг)',
			139 => 'Посылка дверь-дверь (до 30 кг)',
			// Экспресс-доставка за/из-за границы грузов и посылок до 30 кг.
			178 => 'Международный экспресс грузы склад-склад (до 30 кг)',
			179 => 'Международный экспресс грузы склад-дверь (до 30 кг)',
			180 => 'Международный экспресс грузы дверь-склад (до 30 кг)',
			// Экспресс-доставка за/из-за границы документов и писем.
			181 => 'Международный экспресс документы склад-склад (до 5 кг)',
			182 => 'Международный экспресс документы склад-дверь (до 5 кг)',
			183 => 'Международный экспресс документы дверь-склад (до 5 кг)',
			// Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.
			// Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.
			231 => 'Экономичная посылка дверь-дверь (до 50 кг)',
			232 => 'Экономичная посылка дверь-склад (до 50 кг)',
			233 => 'Экономичная посылка склад-дверь (до 50 кг)',
			234 => 'Экономичная посылка склад-склад (до 50 кг)',
			// Тарифы Китайский экспресс.
			243 => 'Китайский экспресс склад-склад',
			245 => 'Китайский экспресс дверь-дверь',
			246 => 'Китайский экспресс склад-дверь',
			247 => 'Китайский экспресс дверь-склад',
			// Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.
			291 => 'CDEK Express склад-склад',
			293 => 'CDEK Express дверь-дверь',
			294 => 'CDEK Express склад-дверь',
			295 => 'CDEK Express дверь-склад',
		),
	),
	'services'           => array(
		'title'   => __( 'Additional Services', 'cdek-for-woocommerce' ),
		'type'    => 'multiselect',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			3  => 'Доставка в выходной день',
			7  => 'Опасный груз',
			24 => 'Упаковка 1',
			30 => 'Примерка на дому',
			36 => 'Частичная доставка',
			37 => 'Осмотр вложения',
		),
	),
	'add_cost'           => array(
		'title' => __( 'Additional Cost', 'cdek-for-woocommerce' ),
		'type'  => 'number',
	),
	'add_weight'         => array(
		'title'       => __( 'Additional Weight', 'cdek-for-woocommerce' ),
		'description' => __( 'Set additional weight. It could be package weight for example.', 'cdek-for-woocommerce' ),
		'type'        => 'number',
	),
	'show_delivery_time' => array(
		'title' => __( 'Show delivery time', 'cdek-for-woocommerce' ),
		'type'  => 'checkbox',
	),
	'add_delivery_time'  => array(
		'title' => __( 'Additional Time for Delivery', 'cdek-for-woocommerce' ),
		'type'  => 'number',
	),
);

return $settings;
