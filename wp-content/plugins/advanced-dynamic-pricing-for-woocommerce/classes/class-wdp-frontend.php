<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WDP_Frontend {
	private $options;
	private $price_display;

	/**
	 * @var WDP_Range_Discounts_Table
	 */
	private $bulk_table;

	/**
	 * @var WDP_Discount_Message
	 */
	private $discount_message;

	/**
	 * @var WDP_Calculation_Profiler
	 */
	private $profiler;

	public function __construct() {
		//TODO: check if need load our scripts
		WDP_Loader::load_core();

		$options = WDP_Helpers::get_settings();
		$this->options = $options;

		add_action( 'wp_print_styles', array( $this, 'load_frontend_assets' ) );

		$this->price_display = new WDP_Price_Display();
		if ( $this->price_display->is_enabled() ) {
			add_action( 'wp_loaded', array( $this, 'wp_loaded_process_cart' ), PHP_INT_MAX );
		}

		if ( $options['update_prices_while_doing_cron'] && wp_doing_cron() ) {
			$this->price_display->init_while_doing_cron();
		}

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ), 10, 3 );

		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'woocommerce_checkout_update_order_review' ), 100 );

		if ( $options['support_shortcode_products_on_sale'] ) {
		    WDP_Shortcode_Products_On_Sale::register();
		}

		if( $options['support_shortcode_products_bogo'] ) {
			WDP_Shortcode_Products_Bogo::register();
		}

		// hooking nopriv ajax methods
		add_action( "wp_ajax_nopriv_get_price_product_with_bulk_table", array( $this, "ajax_get_price_product_with_bulk_table" ) );
		add_action( "wp_ajax_get_price_product_with_bulk_table", array( $this, "ajax_get_price_product_with_bulk_table" ) );

		if ( $options['suppress_other_pricing_plugins'] AND !is_admin() ) {
			add_action( "wp_loaded", array( $this, 'remove_hooks_set_by_other_plugins' ) );
		}

		add_filter( 'woocommerce_cart_id', array( $this, 'woocommerce_cart_id' ), 10, 5 );
		add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', array($this, 'woocommerce_add_to_cart_sold_individually_found_in_cart'), 10 ,5 );

		add_filter( 'woocommerce_order_again_cart_item_data', function ( $cart_item, $item, $order ) {
			$load_as_immutable = apply_filters( 'wdp_order_again_cart_item_load_with_order_deals', false, $cart_item, $item, $order );

			if ( $load_as_immutable ) {
				$rules = $item->get_meta( '_wdp_rules' );
				if ( ! empty( $rules ) ) {
					$cart_item['wdp_rules'] = $rules;
					$cart_item['wdp_immutable'] = true;
				}
			}

			return $cart_item;
		}, 10, 3 );

		add_action( 'woocommerce_checkout_create_order_line_item_object', array( $this, 'save_initial_price_to_order_item' ), 10, 4 );

		if ( $options['hide_coupon_word_in_totals'] ) {
			add_filter( 'woocommerce_cart_totals_coupon_label', function ( $html, $coupon ) {
				/**
				 * @var WC_Coupon $coupon
				 */
				if ( $coupon->get_description() && $coupon->get_virtual() ) {
					$html = $coupon->get_code();
				}

				return $html;
			}, 5, 2 );
		}

		/** Additional css class for free item line */
		add_filter( 'woocommerce_cart_item_class', function ( $str_classes, $cart_item, $cart_item_key ) {
			$classes = explode( ' ', $str_classes );
			if ( ! empty( $cart_item['wdp_gifted'] ) ) {
				$classes[] = 'wdp_free_product';
			}

			if ( ! empty( $cart_item['wdp_rules'] ) && (float) $cart_item['data']->get_price() == 0 ) {
				$classes[] = 'wdp_zero_cost_product';
			}

			return implode( ' ', $classes );
		}, 10, 3 );

		/** PHONE ORDER HOOKS START */
		add_action( 'wdp_force_process_wc_cart', function ( $wc_cart ) {
			WDP_Functions::process_cart_manually();
		} );

		add_filter( 'wpo_product_before_get_item', function ( $product, $item_data ) {
			if ( ! isset( $item_data['item_cost'] ) || ! is_numeric( $item_data['item_cost'] ) ) {
				$qty         = isset( $item_data['quantity'] ) ? (float) $item_data['quantity'] : floatval( 1 );
				$wdp_product = $this->price_display->process_product( $product, $qty );
				if ( $wdp_product ) {
					$product->set_price( $wdp_product->get_new_price() );
				}
			}

			return $product;
		}, 10, 2 );

		add_action( 'wdp_after_switch_customer_while_calc', function () {
			$this->price_display->apply_cart();
		}, 10 );

		if ( ! $options['allow_to_edit_prices_in_po'] ) {
			add_filter( 'wpo_set_original_price_after_calculation', function ( $price, $cart_item ) {
				return isset( $cart_item["wdp_original_price"] ) ? $cart_item["wdp_original_price"] : false;
			}, 10, 2 );
			add_filter( 'wpo_cart_item_is_price_readonly', '__return_true', 10, 1 );

			/**
			 * Restore initial price if pricing plugin made changes
			 */
			add_filter( 'wpo_prepare_item', function ( $item, $product ) {
				/**
				 * @var $product WC_Product
				 */
				if ( $item['item_cost'] != $product->get_price() ) {
					$item['item_cost'] = $product->get_price();
				}

				return $item;
			}, 10, 2 );
		} else {
			add_filter( 'wpo_prepare_item', function ( $item, $product ) {
				/**
				 * @var $product WC_Product
				 */
				if ( empty( $item['cost_updated_manually'] ) && ( $item['item_cost'] != $product->get_price() ) ) {
					$item['item_cost'] = $product->get_price();
				}

				return $item;
			}, 10, 2 );


			add_filter( 'wdp_prepare_cart_item', function ( $cart_item, $wc_cart_item ) {
				/**
				 * @var $product WC_Product
				 */
				if ( ! empty( $wc_cart_item['cost_updated_manually'] ) ) {
					$cart_item->make_immutable();
				}

				return $cart_item;
			}, 10, 2 );

			add_filter( 'wpo_update_cart_cart_item_meta', function ( $cart_item_meta, $item ) {
				if ( ! empty( $item['cost_updated_manually'] ) ) {
					$cart_item_meta['immutable'] = true;
				}

				return $cart_item_meta;
			}, 10, 2 );

			add_filter( 'wdp_cart_item_before_insert', function ( $item, $wc_cart_item ) {
				/**
				 * @var $item WDP_Cart_Item
				 */
				if ( ! empty( $wc_cart_item['immutable'] ) ) {
					$item->make_immutable();
				}

				return $item;
			}, 10, 2 );

			add_filter( 'wpo_cart_item_is_price_readonly', '__return_false', 10, 1 );
		}

		add_filter( 'wpo_must_switch_cart_user', '__return_true', 10, 1 );

		add_filter( 'wpo_skip_add_to_cart_item', function ( $skip, $item ) {
			return ! empty( $item['wdp_gifted'] ) ? (boolean) $item['wdp_gifted'] : $skip;
		}, 10, 2 );
		/** PHONE ORDER HOOKS FINISH */


		/** Subscription hooks */
		if ( class_exists( 'WC_Product_Subscription' ) ) {
			add_filter( 'wdp_get_discounted_price_html', function ( $price_html, $price, $new_price, $product ) {
				/**
				 * @var WDP_Product $product
				 */
				if ( ! empty( $price_html ) ) {
					$subs_price_html = WC_Subscriptions_Product::get_price_string( $product->get_wc_product(), array( 'price' => $price_html ) );
					$price_html      = ! is_null( $subs_price_html ) ? $subs_price_html : $price_html;
				}

				return $price_html;
			}, 10, 4 );
		}
	}

	public function install_bulk_range_table( $customizer ) {
		$this->bulk_table       = new WDP_Range_Discounts_Table( $this->price_display );
		$this->discount_message = new WDP_Discount_Message();
		$this->bulk_table->set_theme_options( $customizer );
	}

	public function install_profiler( $profiler ) {
		if ( $this->price_display->is_enabled() ) {
			$this->profiler = $profiler;
			$this->profiler->use_price_display( $this->price_display );
			$this->profiler->install();
		}
	}

	/**
	 * @param $item WC_Order_Item_Product
	 * @param $cart_item_key string
	 * @param $values array
	 * @param $order WC_Order
	 *
	 * @return WC_Order_Item_Product
	 */
	public function save_initial_price_to_order_item( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values['wdp_rules'] ) ) {
			$item->add_meta_data( '_wdp_rules', $values['wdp_rules'] );
		}

		return $item;
	}

	public static function is_catalog_view() {
		return ! is_product() || ! empty( $GLOBALS['woocommerce_loop']['name'] );
	}

	public function ajax_get_price_product_with_bulk_table() {
		$product_id   = ! empty( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;
		$qty          = ! empty( $_REQUEST['qty'] ) ? (int) $_REQUEST['qty'] : false;
		$page_data    = ! empty( $_REQUEST['page_data'] ) ? (array) $_REQUEST['page_data'] : array();

		if ( ! empty( $_REQUEST['custom_price'] ) ) {
			$custom_price = $_REQUEST['custom_price'];
			if ( preg_match( '/\d+\\' . wc_get_price_decimal_separator() . '\d+/', $custom_price, $matches ) !== false ) {
				$custom_price = floatval( reset( $matches ) );
			} else {
				$custom_price = false;
			};

		} else {
			$custom_price = false;
		}

		if ( ! $product_id || ! $qty ) {
			wp_send_json_error();
		}

		try {
			$wdp_product = new WDP_Product( $product_id );
		} catch ( Exception $e ) {
			wp_send_json_error();
			return;
		}

		if ( $custom_price !== false ) {
			$wdp_product->set_price( $custom_price / $qty );
			$wdp_product->get_wc_product()->set_price( $custom_price / $qty );
			$wdp_product->use_custom_initial_price();
		}

		wp_send_json_success(
			$this->price_display->get_prices_html_depends_on_page( $wdp_product, $qty, $page_data )
		);
	}

	/**
	 * Change cart item display price
	 *
	 * @access public
	 *
	 * @param string $price_html
	 * @param array  $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public function cart_item_price( $price_html, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['wdp_data']['initial_price'] ) ) {

			/** @var WC_Product $product */
			$product = $cart_item['data'];

			$intial_price    = $cart_item['wdp_data']['initial_price'];
			$processed_price = $product->get_price();

			if ( $intial_price != $processed_price ) {
				$price_html = '<del>' . wc_price( $intial_price ) . '</del>';
				$price_html .= '<ins>' . wc_price( $processed_price ) . '</ins>';
			}
		}

		return $price_html;
	}

	public function wp_loaded_process_cart() {
		$this->process_cart();

		if ( ! empty( $_GET['wc-ajax'] ) ) {
			add_action( 'woocommerce_checkout_process', array( $this, 'process_cart' ), PHP_INT_MAX );
			add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), PHP_INT_MAX );
		}
	}

	public function woocommerce_after_calculate_totals() {
		remove_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), PHP_INT_MAX );
		$this->process_cart();
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), PHP_INT_MAX );
	}

	public function woocommerce_checkout_update_order_review() {
		add_action( 'woocommerce_before_data_object_save', array( $this, 'process_cart' ), 100 );
		$this->process_cart();
		remove_action( 'woocommerce_before_data_object_save', array( $this, 'process_cart' ), 100 );
	}

	public function process_cart() {
		$wc_customer = WC()->cart->get_customer();
		$wc_session = WC()->session;

		// store tax exempt value
		if ( ! isset( $wc_session->wdp_old_tax_exempt ) ) {
			$wc_session->set( 'wdp_old_tax_exempt', $wc_customer->get_is_vat_exempt() );
		} else {
			$wc_customer->set_is_vat_exempt( $wc_session->wdp_old_tax_exempt );
		}

		$calc = self::make_wdp_calc_from_wc();
		$cart = self::make_wdp_cart_from_wc();

		if ( ! empty( $this->profiler ) ) {
			$this->profiler->attach_listener( $calc );
		}

		$this->price_display->attach_calc( $calc );

		$newcart = $calc->main_process_cart( $cart );
		if( $newcart ) {
			$newcart->apply_to_wc_cart();
		} else if ( $cart->has_immutable_changed_items() ) {
			// renewal order items
			$cart->apply_to_wc_cart();
		} else {
			unset( $wc_session->wdp_old_tax_exempt );

			//try delete gifted products ?
			$wc_cart_items = WC()->cart->get_cart();
			$cart_helper = new WDP_WC_Cart_Translator();

			foreach ( $wc_cart_items as $wc_cart_item_key => $wc_cart_item ) {
				$wc_cart_item = apply_filters( 'wdp_wc_cart_item_before_clear_from_rules_keys', $wc_cart_item, $wc_cart_item_key );

				$changed = false;

				if ( isset( $wc_cart_item['wdp_gifted'] ) ) {
					$wdp_gifted = $wc_cart_item['wdp_gifted'];
					unset( $wc_cart_item['wdp_gifted'] );
					$changed = true;
					if ( $wdp_gifted ) {
						WC()->cart->remove_cart_item( $wc_cart_item_key );
						continue;
					}
				}

				if ( isset( $wc_cart_item['wdp_original_price'] ) ) {
					unset( $wc_cart_item['wdp_original_price'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_history'] ) ) {
					unset( $wc_cart_item['wdp_history'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_rules'] ) ) {
					unset( $wc_cart_item['wdp_rules'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['rules'] ) ) {
					unset( $wc_cart_item['rules'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_rules_for_singular'] ) ) {
					unset( $wc_cart_item['wdp_rules_for_singular'] );
					$changed = true;
				}

				$product_id   = $wc_cart_item['product_id'];
				$qty          = $wc_cart_item['quantity'];
				$variation_id = $wc_cart_item['variation_id'];
				$variation    = $wc_cart_item['variation'];

				$store_keys = apply_filters( 'wdp_save_cart_item_keys', array(), $cart_helper->get_external_keys( $wc_cart_item ) );

				$cart_item_data = array();
				foreach ( $store_keys as $key ) {
					if ( isset( $wc_cart_item[ $key ] ) ) {
						$cart_item_data[ $key ] = $wc_cart_item[ $key ];
					}
				}

				if ( $changed ) {
					WC()->cart->remove_cart_item( $wc_cart_item_key );

					$exclude_hooks = apply_filters('wdp_exclude_hooks_when_add_to_cart_after_disable_pricing', array(), $wc_cart_item);
					self::process_without_hooks( function () use ( $product_id, $qty, $variation_id, $variation, $cart_item_data ) {
						WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variation, $cart_item_data );
					}, $exclude_hooks );
				}
			}

			$is_free_shipping_key = '_wdp_free_shipping';
			// clear shipping in session for triggering full calculate_shipping to replace '_wdp_free_shipping' when needed
			foreach ( WC()->session->get_session_data() as $key => $value ) {
				if ( preg_match( '/(shipping_for_package_).*/', $key, $matches ) === 1 ) {
					if ( ! isset( $matches[0] ) ) {
						continue;
					}
					$stored_rates = WC()->session->get( $matches[0] );

					if ( ! isset( $stored_rates['rates'] ) ) {
						continue;
					}
					if ( is_array( $stored_rates['rates'] ) ) {
						foreach ( $stored_rates['rates'] as $rate ) {
							if ( isset( $rate->get_meta_data()[$is_free_shipping_key] ) ) {
								unset( WC()->session->$key );
								break;
							}
						}
					}
				}
			}
		}// if no rules


		$this->price_display->apply_cart();
	}

	public static function process_cart_manually() {
		$wc_customer = WC()->cart->get_customer();
		$wc_session = WC()->session;

		// store tax exempt value
		if ( ! isset( $wc_session->wdp_old_tax_exempt ) ) {
			$wc_session->set( 'wdp_old_tax_exempt', $wc_customer->get_is_vat_exempt() );
		} else {
			$wc_customer->set_is_vat_exempt( $wc_session->wdp_old_tax_exempt );
		}

		$calc = self::make_wdp_calc_from_wc();
		$cart = self::make_wdp_cart_from_wc();

		$newcart = $calc->process_cart_new( $cart );
		if( $newcart ) {
			$newcart->apply_to_wc_cart();
		} else {
			unset( $wc_session->wdp_old_tax_exempt );

			//try delete gifted products ?
			$wc_cart_items = WC()->cart->get_cart();
			$cart_helper = new WDP_WC_Cart_Translator();

			foreach ( $wc_cart_items as $wc_cart_item_key => $wc_cart_item ) {
				$changed = false;

				if ( isset( $wc_cart_item['wdp_gifted'] ) ) {
					$wdp_gifted = $wc_cart_item['wdp_gifted'];
					unset( $wc_cart_item['wdp_gifted'] );
					$changed = true;
					if ( $wdp_gifted ) {
						WC()->cart->remove_cart_item( $wc_cart_item_key );
						continue;
					}
				}

				if ( isset( $wc_cart_item['wdp_original_price'] ) ) {
					unset( $wc_cart_item['wdp_original_price'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_history'] ) ) {
					unset( $wc_cart_item['wdp_history'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_rules'] ) ) {
					unset( $wc_cart_item['wdp_rules'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['rules'] ) ) {
					unset( $wc_cart_item['rules'] );
					$changed = true;
				}

				if ( isset( $wc_cart_item['wdp_rules_for_singular'] ) ) {
					unset( $wc_cart_item['wdp_rules_for_singular'] );
					$changed = true;
				}

				$product_id   = $wc_cart_item['product_id'];
				$qty          = $wc_cart_item['quantity'];
				$variation_id = $wc_cart_item['variation_id'];
				$variation    = $wc_cart_item['variation'];

				$store_keys = apply_filters( 'wdp_save_cart_item_keys', array(), $cart_helper->get_external_keys( $wc_cart_item ) );

				$cart_item_data = array();
				foreach ( $store_keys as $key ) {
					if ( isset( $wc_cart_item[ $key ] ) ) {
						$cart_item_data[ $key ] = $wc_cart_item[ $key ];
					}
				}

				if ( $changed ) {
					WC()->cart->remove_cart_item( $wc_cart_item_key );

					$exclude_hooks = apply_filters('wdp_exclude_hooks_when_add_to_cart_after_disable_pricing', array(), $wc_cart_item);
					self::process_without_hooks( function () use ( $product_id, $qty, $variation_id, $variation, $cart_item_data ) {
						WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variation, $cart_item_data );
					}, $exclude_hooks );
				}
			}

			// clear shipping in session for triggering full calculate_shipping to replace 'wdp_free_shipping' when needed
			foreach ( WC()->session->get_session_data() as $key => $value ) {
				if ( preg_match( '/(shipping_for_package_).*/', $key, $matches ) === 1 ) {
					if ( ! isset( $matches[0] ) ) {
						continue;
					}
					$stored_rates = WC()->session->get( $matches[0] );

					if ( ! isset( $stored_rates['rates'] ) ) {
						continue;
					}
					if ( is_array( $stored_rates['rates'] ) ) {
						foreach ( $stored_rates['rates'] as $rate ) {
							if ( isset( $rate->get_meta_data()['wdp_free_shipping'] ) ) {
								unset( WC()->session->$key );
								break;
							}
						}
					}
				}
			}
		}// if no rules
	}

	/**
	 * @return WDP_Cart_Calculator
	 */
	public static function make_wdp_calc_from_wc() {
		$rule_collection = WDP_Rules_Registry::get_instance()->get_active_rules();
		$calc            = WDP_Loader::get_cart_calculator_class( $rule_collection );

		return $calc;
	}

	/**
	 * @param bool $use_empty_cart
	 *
	 * @return WDP_Cart
	 */
	public static function make_wdp_cart_from_wc( $use_empty_cart = false ) {
		$context = self::make_wdp_cart_context_from_wc();

		WDP_Cart::purge_wdp_coupons();
		if ( $use_empty_cart ) {
			$cart    = new WDP_Cart( $context );
		} else {
			$cart    = new WDP_Cart( $context, WC()->cart );
		}

		return $cart;
	}

	/**
	 * @return array
	 */
	private function get_wc_cart_coupons() {
		$external_coupons = array();
		foreach ( WC()->cart->get_coupons() as $coupon ) {
			/**
			 * @var $coupon WC_Coupon
			 */
			if ( $coupon->get_id() ) {
				$external_coupons[] = $coupon->get_code();
			}
		}

		return $external_coupons;
	}

	/**
	 * @return WDP_Cart_Context
	 */
	public static function make_wdp_cart_context_from_wc() {
		//test code
		$environment = array(
			'timestamp'           => current_time( 'timestamp' ),
			'prices_includes_tax' => wc_prices_include_tax(),
			'tab_enabled'         => wc_tax_enabled(),
			'tax_display_shop'    => get_option( 'woocommerce_tax_display_shop' ),
		);

		$settings = WDP_Helpers::get_settings();

		if ( ! is_null( WC()->customer ) ) {
			$customer = new WDP_User_Impl( new WP_User( WC()->customer->get_id() ) );
			$customer->set_shipping_country( WC()->customer->get_shipping_country( '' ) );
			$customer->set_shipping_state( WC()->customer->get_shipping_state( '' ) );
			$customer->set_is_vat_exempt( WC()->customer->get_is_vat_exempt() );
		} else {
			$customer = new WDP_User_Impl( new WP_User() );
		}

		if ( ! is_null( WC()->session ) ) {
			if ( is_checkout() ) $customer->set_payment_method( WC()->session->get('chosen_payment_method') );
			if ( is_checkout() OR !self::is_catalog_view() ) $customer->set_shipping_methods( WC()->session->get('chosen_shipping_methods') );
		}
		$context = new WDP_Cart_Context( $customer, $environment, $settings );

		return $context;
	}


	public function checkout_order_processed( $order_id, $posted_data, $order ) {
		list( $order_stats, $product_stats ) = $this->collect_wc_cart_stats( WC() );

		$order_date = current_time( 'mysql' );

		foreach ( $order_stats as $rule_id => $stats_item ) {
			$stats_item = array_merge(
				array(
					'order_id'         => $order_id,
					'rule_id'          => $rule_id,
					'amount'           => 0,
					'extra'            => 0,
					'shipping'         => 0,
					'is_free_shipping' => 0,
					'gifted_amount'    => 0,
					'gifted_qty'       => 0,
					'date'             => $order_date,
				),
				$stats_item
			);
			WDP_Database::add_order_stats( $stats_item );
		}

		foreach ( $product_stats as $product_id => $by_rule ) {
			foreach ( $by_rule as $rule_id => $stats_item ) {
				$stats_item = array_merge( array(
					'order_id'      => $order_id,
					'product_id'    => $product_id,
					'rule_id'       => $rule_id,
					'qty'           => 0,
					'amount'        => 0,
					'gifted_amount' => 0,
					'gifted_qty'    => 0,
					'date'          => $order_date,
				), $stats_item );

				WDP_Database::add_product_stats( $stats_item );
			}
		}
	}

	/**
	 * @param WooCommerce $wc
	 *
	 * @return array
	 */
	private function collect_wc_cart_stats( $wc ) {
		$order_stats   = array();
		$product_stats = array();

		$wc_cart = $wc->cart;

		$cart_items = $wc_cart->get_cart();
		foreach ( $cart_items as $cart_item ) {
			$rules = isset( $cart_item['wdp_rules'] ) ? $cart_item['wdp_rules'] : '';

			if ( empty( $rules ) ) {
				continue;
			}

			$product_id = $cart_item['product_id'];
			foreach ( $rules as $rule_id => $amount ) {
				//add stat rows 
				if( !isset( $order_stats[ $rule_id ] ) ) {
					$order_stats[ $rule_id ] = array( 'amount'=>0, 'qty'=>0, 'gifted_qty'=>0, 'gifted_amount'=>0, 'shipping'=>0, 'is_free_shipping'=>0, 'extra'=>0 );
				}
				if( !isset( $product_stats[ $product_id ][ $rule_id ] ) ) {
					$product_stats[ $product_id ][ $rule_id ] = array( 'amount'=>0, 'qty'=>0, 'gifted_qty'=>0, 'gifted_amount'=>0 );
				}

				$prefix =   !empty( $cart_item['wdp_gifted'] ) ? 'gifted_' : "";
				// order 
				$order_stats[ $rule_id ][ $prefix . 'qty' ]    += $cart_item['quantity'];
				$order_stats[ $rule_id ][ $prefix . 'amount' ] += $amount * $cart_item['quantity'];
				// product
				$product_stats[ $product_id ][ $rule_id ][ $prefix . 'qty' ]    += $cart_item['quantity'];
				$product_stats[ $product_id ][ $rule_id ][ $prefix . 'amount' ] += $amount * $cart_item['quantity'];
			}
		}

		$this->inject_wc_cart_coupon_stats( $wc_cart, $order_stats );
		$this->inject_wc_cart_fee_stats( $wc_cart, $order_stats );
		$this->inject_wc_cart_shipping_stats( $wc, $order_stats );

		return array( $order_stats, $product_stats );
	}

	/**
	 * @param WC_Cart $wc_cart
	 * @param array   $order_stats
	 */
	private function inject_wc_cart_coupon_stats( $wc_cart, &$order_stats ) {
		$totals      = $wc_cart->get_totals();
		$wdp_coupons = isset( $totals['wdp_coupons'] ) ? $totals['wdp_coupons'] : array();
		if ( empty( $wdp_coupons ) ) {
			return;
		}

		foreach ( $wc_cart->get_coupon_discount_totals() as $coupon_code => $amount ) {
			if ( isset( $wdp_coupons['grouped'][ $coupon_code ] ) ) {
				foreach ( $wdp_coupons['grouped'][ $coupon_code ] as $rule_id => $amount_per_rule ) {
					if ( ! isset( $order_stats[ $rule_id ] ) ) {
						$order_stats[ $rule_id ] = array();
					}

					if ( ! isset( $order_stats[ $rule_id ]['extra'] ) ) {
						$order_stats[ $rule_id ]['extra'] = 0.0;
					}

					$order_stats[ $rule_id ]['extra'] += $amount_per_rule;
				}
			} elseif ( isset( $wdp_coupons['single'][ $coupon_code ] ) ) {
				$rule_id = $wdp_coupons['single'][ $coupon_code ];
				if ( ! isset( $order_stats[ $rule_id ] ) ) {
					$order_stats[ $rule_id ] = array();
				}

				if ( ! isset( $order_stats[ $rule_id ]['extra'] ) ) {
					$order_stats[ $rule_id ]['extra'] = 0.0;
				}

				$order_stats[ $rule_id ]['extra'] += $amount;
			}
		}
	}

	/**
	 * @param WC_Cart $wc_cart
	 * @param array   $order_stats
	 */
	private function inject_wc_cart_fee_stats( $wc_cart, &$order_stats ) {
		$totals   = $wc_cart->get_totals();
		$wdp_fees = isset( $totals['wdp_fees'] ) ? $totals['wdp_fees'] : '';
		if ( empty( $wdp_fees ) ) {
			return;
		}

		foreach ( $wc_cart->get_fees() as $fee ) {
			$fee_name = $fee->name; // change with care, reports are using fee->name too
			if ( isset( $wdp_fees[ $fee_name ] ) ) {
				foreach ( $wdp_fees[ $fee_name ] as $rule_id => $fee_amount_per_rule ) {
					$order_stats[ $rule_id ]['extra'] -= $fee_amount_per_rule;
				}
			}
		}
	}

	/**
	 * @param WooCommerce $wc
	 * @param array       $order_stats
	 */
	private function inject_wc_cart_shipping_stats( $wc, &$order_stats ) {
		$shippings = $wc->session->get( 'chosen_shipping_methods' );
		if ( empty( $shippings ) ) {
			return;
		}

		$applied_rules_key = '_wdp_rules';
		$is_free_shipping_key = '_wdp_free_shipping';

		foreach ( $shippings as $package_id => $shipping_rate_key ) {
			$packages = $wc->shipping()->get_packages();
			if ( isset( $packages[ $package_id ]['rates'][ $shipping_rate_key ] ) ) {
				/** @var WC_Shipping_Rate $sh_rate */
				$sh_rate      = $packages[ $package_id ]['rates'][ $shipping_rate_key ];
				$sh_rate_meta = $sh_rate->get_meta_data();

				$is_free_shipping = isset( $sh_rate_meta[ $is_free_shipping_key ] ) ? $sh_rate_meta[ $is_free_shipping_key ] : false;
				$wdp_rules        = isset( $sh_rate_meta[ $applied_rules_key ] ) ? json_decode( $sh_rate_meta[ $applied_rules_key ], true ) : false;

				if ( ! empty( $wdp_rules ) && is_array( $wdp_rules ) ) {
					foreach ( $wdp_rules as $rule_id => $amount ) {
						if ( ! isset( $order_stats[ $rule_id ] ) ) {
							$order_stats[ $rule_id ] = array();
						}

						if ( ! isset( $order_stats[ $rule_id ]['shipping'] ) ) {
							$order_stats[ $rule_id ]['shipping'] = 0.0;
						}

						$order_stats[ $rule_id ]['shipping']         += $amount;
						$order_stats[ $rule_id ]['is_free_shipping'] = $is_free_shipping;
					}
				}

			}
		}
	}

	public static function wdp_get_template( $template_name , $args = array(), $template_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$full_template_path = trailingslashit( WC_ADP_PLUGIN_PATH . 'templates' );

		if ( $template_path ) {
			$full_template_path .= trailingslashit( $template_path );
		}

		$full_external_template_path = locate_template( array(
			'advanced-dynamic-pricing-for-woocommerce/' . trailingslashit( $template_path ) . $template_name,
			'advanced-dynamic-pricing-for-woocommerce/' . $template_name,
		) );

		if ( $full_external_template_path ) {
			$full_template_path = $full_external_template_path;
		} else {
			$full_template_path .= $template_name;
		}

		ob_start();
		include $full_template_path;
		$template_content = ob_get_clean();

		return $template_content;
	}

	public function load_frontend_assets() {
		$options    = WDP_Helpers::get_settings();
		wp_enqueue_style( 'wdp_pricing-table', WC_ADP_PLUGIN_URL . '/assets/css/pricing-table.css', array(), WC_ADP_VERSION );
		wp_enqueue_style( 'wdp_deals-table', WC_ADP_PLUGIN_URL . '/assets/css/deals-table.css', array(), WC_ADP_VERSION );

		if ( is_product() || woocommerce_product_loop() ) {
			wp_enqueue_script( 'wdp_deals', WC_ADP_PLUGIN_URL . '/assets/js/frontend.js', array(), WC_ADP_VERSION );
		}

		if ( WDP_Database::is_condition_type_active( array( 'customer_shipping_method' ) ) ) {
			wp_enqueue_script( 'wdp_update_cart', WC_ADP_PLUGIN_URL . '/assets/js/update-cart.js' , array( 'wc-cart' ), WC_ADP_VERSION );
		}

		$script_data = array(
			'ajaxurl'               => admin_url( 'admin-ajax.php' ),
			'update_price_with_qty' => wc_string_to_bool( $options['update_price_with_qty'] ) && ! wc_string_to_bool( $options['do_not_modify_price_at_product_page'] ),
			'js_init_trigger'       => apply_filters( 'wdp_bulk_table_js_init_trigger', "" ),
		);

		wp_localize_script( 'wdp_deals', 'script_data', $script_data );
	}

	function remove_hooks_set_by_other_plugins() {
		global $wp_filter;

		$allowed_hooks = array(
			//Filters
			"woocommerce_get_price_html"            => array( "WDP_Price_Display|hook_get_price_html" ),
			"woocommerce_product_is_on_sale"        => array( "WDP_Price_Display|hook_product_is_on_sale" ),
			"woocommerce_product_get_sale_price"    => array( "WDP_Price_Display|hook_product_get_sale_price" ),
			"woocommerce_product_get_regular_price" => array( "WDP_Price_Display|hook_product_get_regular_price" ),
			"woocommerce_variable_price_html"       => array(),
			"woocommerce_variable_sale_price_html"  => array(),
			"woocommerce_cart_item_price"           => array( "WDP_Price_Display|woocommerce_cart_item_price_and_price_subtotal" ),
			"woocommerce_cart_item_subtotal"        => array( "WDP_Price_Display|woocommerce_cart_item_price_and_price_subtotal" ),
			//Actions
			"woocommerce_checkout_order_processed"  => array( "WDP_Frontend|checkout_order_processed" ),
			"woocommerce_before_calculate_totals"   => array(), //nothing allowed!
		);

		foreach ( $wp_filter as $hook_name => $hook_obj ) {
			if ( preg_match( '#^woocommerce_#', $hook_name ) ) {
				if ( isset( $allowed_hooks[ $hook_name ] ) ) {
					$wp_filter[ $hook_name ] = $this->remove_wrong_callbacks( $hook_obj, $allowed_hooks[ $hook_name ] );
				} else {
				}
			}
		}
	}

	public static function remove_callbacks( $hook_obj, $hooks ) {
		$new_callbacks = array();
		foreach ( $hook_obj->callbacks as $priority => $callbacks ) {
			$priority_callbacks = array();
			foreach ( $callbacks as $idx => $callback_details ) {
				if ( ! self::is_callback_match( $callback_details, $hooks ) ) {
					$priority_callbacks[ $idx ] = $callback_details;
				}
			}
			if ( $priority_callbacks ) {
				$new_callbacks[ $priority ] = $priority_callbacks;
			}
		}
		$hook_obj->callbacks = $new_callbacks;

		return $hook_obj;
	}

	function remove_wrong_callbacks( $hook_obj, $allowed_hooks ) {
		$new_callbacks = array();
		foreach ( $hook_obj->callbacks as $priority => $callbacks ) {
			$priority_callbacks = array();
			foreach ( $callbacks as $idx => $callback_details ) {
				if ( self::is_callback_match( $callback_details, $allowed_hooks ) ) {
					$priority_callbacks[ $idx ] = $callback_details;
				}
			}
			if ( $priority_callbacks ) {
				$new_callbacks[ $priority ] = $priority_callbacks;
			}
		}
		$hook_obj->callbacks = $new_callbacks;

		return $hook_obj;
	}

	//check class + function name!
	public static function is_callback_match( $callback_details, $allowed_hooks ) {
		// we add our hooks as Class:member, so "function" will be array and will have 2 elements!
		if ( !is_array($callback_details['function']) OR count( $callback_details['function'] ) != 2 ) {
			return false; // not our hook!
		}

		$callback_obj  = $callback_details['function'][0];
		$callback_func = $callback_details['function'][1];

		if ( is_object( $callback_obj ) ) {
			$callback_class = get_class( $callback_obj );
		} elseif ( is_string( $callback_obj ) && class_exists( $callback_obj ) ) {
			$callback_class = $callback_obj;
		} else {
			return false; // definitely not ours
		}

		$result = false;
		foreach ( $allowed_hooks as $callback_name ) {
			list( $class_name, $func_name ) = explode( "|", $callback_name );
			if ( $class_name == $callback_class AND $func_name == $callback_func ) {
				$result = true;
				break;// don't  remove own hooks!
			}
		}

		return $result;
	}

	private $last_variation = array();
	private $last_variation_hash = array();

	/**
	 * The only way to snatch $variation before woocommerce_add_to_cart_sold_individually_found_in_cart()
	 *
	 * @param string $hash
	 * @param int $product_id
	 * @param int $variation_id
	 * @param array $variation
	 * @param array $cart_item_data
	 *
	 * @return string
	 */
	public function woocommerce_cart_id( $hash, $product_id, $variation_id, $variation, $cart_item_data) {
		$this->last_variation = $variation;
		$this->last_variation_hash = $hash;

		return $hash;
	}

	public function woocommerce_add_to_cart_sold_individually_found_in_cart( $found, $product_id, $variation_id, $cart_item_data, $cart_id ) {
		// already found in cart
		if ( $found ) {
			return true;
		}

		$variation = array();
		if ( $this->last_variation_hash && $this->last_variation_hash === $cart_id ) {
			$variation = $this->last_variation;
		}

		$wdp_keys = array(
			'wdp_rules',
			'wdp_gifted',
			'wdp_original_price',
		);
		$cart_item_data = array_filter( $cart_item_data, function ( $key ) use ( $wdp_keys ) {
			return ! in_array( $key, $wdp_keys );
		}, ARRAY_FILTER_USE_KEY );
		$no_pricing_cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
		if ( ! $no_pricing_cart_id ) {
			return $found;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( $no_pricing_cart_id === $this->calculate_cart_item_hash_without_pricing( $cart_item ) ) {
				return true;
			}
		}

		return $found;
	}

	private function calculate_cart_item_hash_without_pricing( $cart_item_data ) {
		$product_id = isset( $cart_item_data['product_id'] ) ? $cart_item_data['product_id'] : 0;

		if ( ! $product_id ) {
			return false;
		}

		$variation_id = isset( $cart_item_data['variation_id'] ) ? $cart_item_data['variation_id'] : 0;
		$variation    = isset( $cart_item_data['variation'] ) ? $cart_item_data['variation'] : array();

		$wdp_keys = array(
			'wdp_rules',
			'wdp_gifted',
			'wdp_original_price',
		);

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

		$cart_item_data = array_filter( $cart_item_data, function ( $key ) use ( $wdp_keys, $default_keys ) {
			return ! in_array( $key, array_merge( $wdp_keys, $default_keys ) );
		}, ARRAY_FILTER_USE_KEY );

		return WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
	}

	private static function get_nopriv_ajax_actions() {
		return array(
			'get_table_with_product_bulk_table',
			'get_price_product_with_bulk_table',
		);
	}

	private static function get_ajax_actions() {
		return array_merge( self::get_nopriv_ajax_actions(), self::get_priv_ajax_actions() );
	}

	private static function get_priv_ajax_actions() {
		return array(
			'download_report',
			'get_user_report_data',
		);
	}

	public static function is_ajax_processing() {
		return wp_doing_ajax() && ! empty( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], self::get_ajax_actions() );
	}

	public static function get_gifted_cart_products() {
		return WDP_Functions::get_gifted_cart_products();
	}

	public static function get_active_rules_for_product( $product_id, $qty = 1, $use_empty_cart = false ) {
		return WDP_Functions::get_active_rules_for_product( $product_id, $qty, $use_empty_cart );
	}

	public static function get_discounted_products_for_cart( $array_of_products, $plain = false ) {
		return WDP_Functions::get_discounted_products_for_cart( $array_of_products, $plain );
	}

	public static function get_discounted_product_price( $the_product, $qty, $use_empty_cart = true ) {
		return WDP_Functions::get_discounted_product_price( $the_product, $qty, $use_empty_cart );
	}

	public static function process_without_hooks( $callback, $hooks_list ) {
		return WDP_Functions::process_without_hooks( $callback, $hooks_list );
	}
}