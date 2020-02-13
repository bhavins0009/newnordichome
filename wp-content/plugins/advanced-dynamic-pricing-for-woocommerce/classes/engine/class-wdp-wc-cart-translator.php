<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WDP_WC_Cart_Translator {
	/**
	 * @return float
	 */
	public function get_amount_saved() {
		$cart_items   = WC()->cart->cart_contents;
		$amount_saved = floatval( 0 );

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			if ( ! isset( $cart_item['wdp_rules'] ) ) {
				continue;
			}

			foreach ( $cart_item['wdp_rules'] as $id => $amount_saved_by_rule ) {
				$amount_saved += (float) $amount_saved_by_rule * (float) $cart_item['quantity'];
			}
		}

		$totals       = WC()->cart->get_totals();
		$applied_fees = isset( $totals['wdp_fees'] ) ? $totals['wdp_fees'] : array();

		foreach ( WC()->cart->get_coupons() as $coupon ) {
			/**
			 * @var $coupon WC_Coupon
			 */
			if ( $coupon->meta_exists( "is_wdp" ) ) {
				$amount_saved += WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
			}
		}

		foreach ( $applied_fees as $fee_name => $amount_per_rule ) {
			$amount_saved -= array_sum( $amount_per_rule );
		}

		return (float) apply_filters( 'wdp_amount_saved', $amount_saved, $cart_items );
	}

	public function get_external_keys( $wc_cart_item ) {
		$external_keys = array();

		$default_keys = array(
			'key',
			'product_id',
			'variation_id',
			'variation',
			'quantity',
			'data',
			'data_hash',
			'line_tax_data',
			'line_subtotal',
			'line_subtotal_tax',
			'line_total',
			'line_tax',
		);

		$wdp_keys = array(
			'wdp_gifted',
			'wdp_original_price',
			'wdp_history',
			'wdp_rules',
			'rules',
			'wdp_rules_for_singular',
		);

		foreach ( $wc_cart_item as $key => $value ) {
			if ( ! in_array( $key, array_merge( $default_keys, $wdp_keys ) ) ) {
				$external_keys[] = $key;
			}
		}

		return $external_keys;
	}
}