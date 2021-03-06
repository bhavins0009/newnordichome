<?php

/**
 * Class WOOMULTI_CURRENCY_Frontend_Symbol
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WOOMULTI_CURRENCY_Frontend_Symbol {
	protected $settings;

	public function __construct() {
//		$this->settings = new WOOMULTI_CURRENCY_Data();
		$this->settings = WOOMULTI_CURRENCY_Data::get_ins();
		if ( $this->settings->get_enable() ) {

			/*Add order information*/
			add_filter( 'woocommerce_thankyou_order_id', array( $this, 'woocommerce_thankyou_order_id' ), 9 );
			add_filter( 'woocommerce_currency', array( $this, 'woocommerce_currency' ), 99 );
			/**
			 * Format price
			 */
			add_filter( 'wc_get_price_decimals', array( $this, 'set_decimals' ) );
			/**
			 * Symbol position
			 */
			add_filter( 'woocommerce_price_format', array( $this, 'price_format' ) );

			/*Custom Symbol*/
			add_filter( 'wc_price', array( $this, 'custom_price' ), 10, 3 );
			add_filter( 'woocommerce_currency_symbol', array( $this, 'custom_currency_symbol' ), 10002, 2 );
			add_action( 'woocommerce_my_account_my_orders_column_order-total', array(
				$this,
				'my_account_page_orders'
			) );
		}
	}

	public function my_account_page_orders( $order ) {
		$id             = $order->get_id();
		$total          = $order->get_total();
		$item_count     = $order->get_item_count();
		$checkout_curr  = $order->get_currency();
		$symbol         = array();
		$wmc_order_info = get_post_meta( $id, 'wmc_order_info', true );
		if ( $wmc_order_info ) {
			foreach ( $wmc_order_info as $key => $value ) {
				if ( $key == $checkout_curr ) {
					$symbol['currency'] = $key;
					$symbol['custom']   = $value['custom'];
				}
			}
		} else {
			$symbol['custom']   = '';
			$symbol['currency'] = $checkout_curr;
		}
		$total = $this->wmc_wc_price_order( $total, $symbol );
		printf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $total, $item_count );
	}

	function wmc_wc_price_order( $price, $symbol, $args = array() ) {
		$args = apply_filters(
			'wc_price_args',
			wp_parse_args(
				$args,
				array(
					'ex_tax_label'       => false,
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
				)
			)
		);

		$display_symbol    = $symbol['custom'] ? $symbol['custom'] : get_woocommerce_currency_symbol( $symbol['currency'] );
		$unformatted_price = $price;
		$negative          = $price < 0;
		$price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * - 1 : $price ) );
		$price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . $display_symbol . '</span>', $price );

		$return = '<span class="woocommerce-Price-amount amount">' . $formatted_price . '</span>';

		if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
			$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		}

		return $return;
	}

	/**
	 * Custom symbol price
	 *
	 * @param $return
	 * @param $price
	 * @param $args
	 *
	 * @return mixed|void
	 */
	function custom_price( $return, $price, $args ) {
		extract(
			wp_parse_args(
				$args, array(
					'ex_tax_label'       => false,
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
				)
			)
		);

		$unformatted_price = $price;
		$negative          = $price < 0;

		$currency_symbol = get_woocommerce_currency_symbol( $currency );
		$pos             = strpos( $currency_symbol, '#PRICE#' );

		if ( $pos === false ) {
			$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="woocommerce-Price-currencySymbol">' . $currency_symbol . '</span>', $price );
		} else {
			$formatted_price = str_replace( '#PRICE#', $price, $currency_symbol );

		}

		$return = '<span class="woocommerce-Price-amount amount">' . $formatted_price . '</span>';

		if ( $ex_tax_label && wc_tax_enabled() ) {
			$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		}

		/**
		 * Filters the string of price markup.
		 *
		 * @param string $return Price HTML markup.
		 * @param string $price Formatted price.
		 * @param array $args Pass on the args.
		 * @param float $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
		 */
		return apply_filters( 'wmc_wc_price', $return, $price, $args, $unformatted_price );
	}

	/**
	 * Custom current symbol
	 *
	 * @param $currency_symbol
	 * @param $currency
	 *
	 * @return mixed
	 */
	public function custom_currency_symbol( $currency_symbol, $currency ) {
		if ( is_admin() && ! is_ajax() ) {
			return $currency_symbol;
		}
		$selected_currencies = $this->settings->get_list_currencies();

		if ( isset( $selected_currencies[ $currency ] ) && isset( $selected_currencies[ $currency ]['custom'] ) && $selected_currencies[ $currency ]['custom'] != '' ) {

			$currency_symbol = $selected_currencies[ $currency ]['custom'];

		}

		return $currency_symbol;
	}

	/**
	 * @param $data
	 *
	 * @return mixed|string|void
	 */
	public function woocommerce_currency( $data ) {
		if ( is_admin() && ! is_ajax() ) {
			return $data;
		}
		if ( $this->settings->get_current_currency() ) {
			$data = $this->settings->get_current_currency();
		}

		return $data;
	}

	/**
	 * Insert information about order after checkout
	 *
	 * @param $order_id
	 *
	 * @return mixed
	 */
	public function woocommerce_thankyou_order_id( $order_id ) {

		$wmc_order_info                                                       = $this->settings->get_list_currencies();
		$wmc_order_info[ $this->settings->get_default_currency() ]['is_main'] = 1;
		update_post_meta( $order_id, 'wmc_order_info', $wmc_order_info );

		return $order_id;
	}

	/**
	 * @return string    price format of current currency
	 */
	public function price_format( $format ) {
		$selected_currencies = $this->settings->get_list_currencies();
		$currencies          = $this->settings->get_currencies();
		if ( is_order_received_page() ) {
			global $wp;
			$order_id = $wp->query_vars['order-received'];
			$order    = wc_get_order( $order_id );
			if ( is_object( $order ) ) {
				$currency    = $order->get_currency();
				$current_pos = $selected_currencies[ $currency ]['pos'];
			} else {
				return $format;
			}

		} elseif ( in_array( $this->settings->get_current_currency(), $currencies ) ) {
			$current_pos = $selected_currencies[ $this->settings->get_current_currency() ]['pos'];
		} else {
			return $format;
		}

		switch ( $current_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		return $format;
	}

	/**
	 * Format price
	 *
	 * @param $price_arg
	 *
	 * @return mixed
	 */
	public function set_decimals( $decimal ) {
		global $pagenow;
		if ( $pagenow === 'admin-ajax.php' && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'generate_wpo_wcpdf' && isset( $_REQUEST['order_ids'] ) && $_REQUEST['order_ids'] ) {
			$order_id       = intval( $_REQUEST['order_ids'] );
			$order_currency = get_post_meta( $order_id, '_order_currency', true );
			$order_info     = get_post_meta( $order_id, 'wmc_order_info', true );
			if ( isset( $order_info[ $order_currency ]['decimals'] ) ) {
				return $order_info[ $order_currency ]['decimals'];
			}
		}
		$selected_currencies = $this->settings->get_list_currencies();
		$current_currency    = $this->settings->get_current_currency();
		$decimal             = isset( $selected_currencies[ $current_currency ]['decimals'] ) ? $selected_currencies[ $current_currency ]['decimals'] : 0;

		return $decimal;
	}

}