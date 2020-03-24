<?php
/**
 * Settings for CDEK shipping.
 *
 * @package CDEK/Classes/Shipping
 */

defined( 'ABSPATH' ) || exit;

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
		'title'   => __( 'Tariff', 'cdek-for-woocommerce' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'taxable',
		'options' => array(
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
			// Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.
			291 => 'CDEK Express склад-склад',
			293 => 'CDEK Express дверь-дверь',
			294 => 'CDEK Express склад-дверь',
			295 => 'CDEK Express дверь-склад',
		),
	),
	'show_delivery_time' => array(
		'title' => __( 'Show delivery time', 'cdek-for-woocommerce' ),
		'type'  => 'checkbox',
	),
	'add_delivery_time'  => array(
		'title' => __( 'Additional Time for Delivery', 'cdek-for-woocommerce' ),
		'type'  => 'number',
	),
	'add_cost'           => array(
		'title' => __( 'Additional Cost', 'cdek-for-woocommerce' ),
		'type'  => 'number',
	),
);

return $settings;
