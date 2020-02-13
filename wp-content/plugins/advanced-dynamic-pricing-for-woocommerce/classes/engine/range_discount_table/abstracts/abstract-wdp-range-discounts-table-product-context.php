<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class WDP_Range_Discounts_Table_Product_Context_Abstract extends WDP_Range_Discounts_Table_Abstract {
	/**
	 * @param WDP_Price_Display $price_display
	 * @param integer           $product_id
	 */
	public function load_rule( $price_display, $product_id ) {
		$product = WDP_Object_Cache::get_instance()->get_wc_product( $product_id );
		if ( ! $product ) {
			return;
		}
		$available_product_ids = array_merge( array( $product->get_id() ), $product->get_children() );

		$matched_rule = null;
		foreach ( $available_product_ids as $product_id ) {
			$matched_rules = $price_display->get_calculator()->find_product_matches( $price_display->get_cart(), $product_id );
			if ( $matched_rules->is_empty() ) {
				continue;
			}

			$bulk_rules = $matched_rules->with_bulk();
			if ( $bulk_rules->is_empty() ) {
				continue;
			}

			foreach ( $bulk_rules->to_array() as $rule ) {
				/**
				 * @var WDP_Rule $rule
				 */
				if ( $rule->get_bulk_details( $price_display->get_cart()->get_context() ) && $this->is_available_to_output_table( $rule ) ) {
					$matched_rule = $rule;
					break;
				}
			}

			if ( $matched_rule ) {
				break;
			}
		}

		if ( $matched_rule ) {
			$this->fill_bulk_details( $matched_rule->get_bulk_details( $price_display->get_cart()->get_context() ) );
			$this->rule               = $matched_rule;
			$this->object_id          = $product_id;

			$calc                = new WDP_Cart_Calculator( new WDP_Rules_Collection( array( $matched_rule ) ) );
			$this->price_display = new WDP_Price_Display();
			$this->price_display->attach_calc( $calc );
			$this->price_display->apply_empty_cart();
		}
	}
}
