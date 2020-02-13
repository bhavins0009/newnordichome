<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

	use ShipmondoForWooCommerce\Lib\Tools\Loader;
	use ShipmondoForWooCommerce\Plugin\Controllers\Legacy;
	use ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint;
/**
 *
 * @class 		Shipmondo_Shipping_Main
 * @version		0.1.0
 * @author 		Magnus Vejlø - Boostr
 */
abstract class Shipmondo_Shipping_Main extends WC_Shipping_Method
{
	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	protected $cart_total = null;

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	protected $free_shipping_total = null;

    /* Abstract Methods */
    /* All class's exentind this class, wil be forced to load these methods! */
    abstract protected function register_shipping_method($methods);
    abstract protected function addActions();
    abstract protected function addFilters();

    /* Variabels */


    /* Methods */
    function init() {
        //Load the settings API default-settings.php
        $this->instance_form_fields                     = include( 'method_settings/settings-default.php' );
        $this->title                                    = $this->get_option( 'title' );
        $this->tax_status                               = $this->get_option( 'tax_status' );
		$this->shipping_price 	            	        = $this->get_option('shipping_price');
		$this->enable_free_shipping                     = $this->get_option('enable_free_shipping');
		$this->free_shipping_total                      = $this->get_option('free_shipping_total');
		$this->enable_free_shipping_with_coupon         = $this->get_option('enable_free_shipping_with_coupon');
        $this->type                                     = $this->get_option( 'type', 'class' );

        //This is part of the settings API. Loads settings you previously init.
         $this->init_settings();
        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

        if (!class_exists('Shipmondo_Shipping_Admin'))
        {
            include_once('shipmondo_shipping_admin.php');
            $Shipmondo_Shipping_Admin = new Shipmondo_Shipping_Admin($this);
        }
    } // function init ends

    function mainAddAction()
    {
        //ajax for the pakkeshop list
        add_action('woocommerce_checkout_create_order_shipping_item', array($this, 'woocommerce_checkout_update_order_meta_method_shipping_shipmondo'), 10, 4);
    }

    //Updates the order with the shipping information of the choosen packetshop
    function woocommerce_checkout_update_order_meta_method_shipping_shipmondo($item, $package_key, $package, $order) {
    	$agent = isset($this->agent) ? $this->agent : null;
    	$method_id = !empty($item->get_data()['method_id']) ? $item->get_data()['method_id'] : (!empty($item->get_changes()['method_id']) ? $item->get_changes()['method_id'] : null);

		if($method_id !== $this->id || (empty($_POST['shipmondo'][$package_key]) && !PickupPoint::isCurrentSelection($agent, $package_key))) {
			return;
		}

		$shipping_info = array(
			'first_name' => (!empty($order->get_shipping_first_name()) ? $order->get_shipping_first_name() : $order->get_billing_first_name()),
			'last_name' => (!empty($order->get_shipping_last_name()) ? $order->get_shipping_last_name() : $order->get_billing_last_name()),
			'company' => (!empty($_POST['shop_name'][$package_key]) ? $_POST['shop_name'][$package_key] : PickupPoint::getCurrentSelection('name', $agent, $package_key)),
			'address_1' => (!empty($_POST['shop_address'][$package_key]) ? $_POST['shop_address'][$package_key] : PickupPoint::getCurrentSelection('address', $agent, $package_key)),
			'address_2' => (!empty($_POST['shop_ID'][$package_key]) ? $_POST['shop_ID'][$package_key] : PickupPoint::getCurrentSelection('id_string', $agent, $package_key)),
			'city' => (!empty($_POST['shop_city'][$package_key]) ? $_POST['shop_city'][$package_key] : PickupPoint::getCurrentSelection('city', $agent, $package_key)),
			'postcode' => (!empty($_POST['shop_zip'][$package_key]) ? $_POST['shop_zip'][$package_key] : PickupPoint::getCurrentSelection('zip', $agent, $package_key)),
		);


		// Update shipping info
	    $order->set_address($shipping_info, 'shipping');

		$order->update_meta_data(__('Pakkeshop', 'pakkelabels-for-woocommerce'), (!empty($_POST['shipmondo'][$package_key]) ? $_POST['shipmondo'][$package_key] : PickupPoint::getCurrentSelection('id', $agent, $package_key)));
    }



    /* Returns/Echo's the HTML that is used to make a zipcode textarea & the find shop button */
    function HTML_zipAndFind($shipping_method, $index = 0) {
        if(is_checkout()) {
	        $settings = get_option('shipmondo_settings');
	        $option = empty($settings['shipmondo_pickup_point_selection_type']) ? 'modal' : $settings['shipmondo_pickup_point_selection_type'];

            \ShipmondoForWooCommerce\Plugin\Plugin::getTemplate('pickup-point-selection.' . $option . '.selection-button', array('shipping_method' => $shipping_method, 'index' => $index, 'agent' => $this->agent));
        } else {
				echo '<br/><div class="shipping_pickup_cart">' . __('Pickup point is selected during checkout','pakkelabels-for-woocommerce') . '</div>';
        }
    }


    protected function evaluate_cost($sum, $args = array()) {
		include_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );

		// Allow 3rd parties to process shipping cost arguments
	    $args           = apply_filters('woocommerce_evaluate_shipping_cost_args', $args, $sum, $this);
		$locale         = localeconv();
		$decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
	    $this->fee_cost = $args['cost'];

		// Expand shortcodes
	    add_shortcode( 'fee', array( $this, 'fee' ) );

	    $sum = do_shortcode(
		    str_replace(
			    array(
				    '[qty]',
				    '[cost]',
			    ),
			    array(
				    $args['qty'],
				    $args['cost'],
			    ),
			    $sum
		    )
	    );

	    remove_shortcode( 'fee', array( $this, 'fee' ) );

		// Remove whitespace from string
		$sum = preg_replace( '/\s+/', '', $sum );

		// Remove locale from string
		$sum = str_replace( $decimals, '.', $sum );

		// Trim invalid start/end characters
		$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		// Do the math
		return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
    }

	/**
	 * Work out fee (shortcode).
	 *
	 * @param  array $atts Attributes.
	 * @return string
	 */
	public function fee( $atts ) {
		$atts = shortcode_atts(
			array(
				'percent' => '',
				'min_fee' => '',
				'max_fee' => '',
			),
			$atts,
			'fee'
		);

		$calculated_fee = 0;

		if ( $atts['percent'] ) {
			$calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
		}

		if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
			$calculated_fee = $atts['min_fee'];
		}

		if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
			$calculated_fee = $atts['max_fee'];
		}

		return $calculated_fee;
	}

	public function calculate_shipping($package = array()) {
		// Register the rate
		if($this->showShippingMethod()) {
		    if($this->eligibleFreeShipping($package)) {
				$this->add_rate(array(
					'label' => $this->title . ' ' . apply_filters('shipmondo_free_label', __('(free)', 'pakkelabels-for-woocommerce')),
                    'cost' => 0,
                    'id' => $this->get_rate_id(),
					'package' => $package
				));
            } else {
		        $rate = array(
                    'label' => $this->title,
                    'cost' => $this->getShippingPrice($package),
                    'id' => $this->get_rate_id(),
			        'package' => $package
                );
		        $rate = $this->add_class_costs($package, $rate);
				$this->add_rate($rate);
            }
		}


	}

    /**
     * @param array $package
     * @param array $rate
     * @return array $rate
     */
    private function add_class_costs($package, $rate)
    {
        // Add shipping class costs.
        $shipping_classes = WC()->shipping->get_shipping_classes();

        if ( ! empty( $shipping_classes ) ) {
            $found_shipping_classes = $this->find_shipping_classes( $package );
            $highest_class_cost     = 0;

            foreach ( $found_shipping_classes as $shipping_class => $products ) {
                // Also handles BW compatibility when slugs were used instead of ids.
                $shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                $class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

                if ( '' === $class_cost_string ) {
                    continue;
                }

                $has_costs  = true;
                $class_cost = $this->evaluate_cost(
                    $class_cost_string, array(
                        'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                        'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                    )
                );

                if ( 'class' === $this->type ) {
                    $rate['cost'] += $class_cost;
                } else {
                    $highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                }
            }

            if ( 'order' === $this->type && $highest_class_cost ) {
                $rate['cost'] += $highest_class_cost;
            }
        }
        return $rate;
	}

    /**
     * Finds and returns shipping classes and the products with said class.
     *
     * @param mixed $package Package of items from cart.
     * @return array
     */
    public function find_shipping_classes( $package ) {
        $found_shipping_classes = array();

        foreach ( $package['contents'] as $item_id => $values ) {
            if ( $values['data']->needs_shipping() ) {
                $found_class = $values['data']->get_shipping_class();

                if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
                    $found_shipping_classes[ $found_class ] = array();
                }

                $found_shipping_classes[ $found_class ][ $item_id ] = $values;
            }
        }

        return $found_shipping_classes;
    }

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	public function getShippingPrice($package = array()) {
        $price = $this->get_option('shipping_price', 0);

	    if($this->get_option('differentiated_price_type') == "Price") {
			$price_classes = get_option('Price_' . $this->instance_id);

			foreach($price_classes as $price_class) {
				if($price_class->minimum < $this->getCartTotal() && $this->getCartTotal() <= $price_class->maximum){
					$price = $price_class->shipping_price;
                    break;
				}
				if($price_class->shipping_price > $price) {
					$price = $price_class->shipping_price;
				}
			}
        } else if($this->get_option('differentiated_price_type') == "Weight") {
			$weight_total = $GLOBALS['woocommerce']->cart->cart_contents_weight;
			$weight_classes = get_option('Weight_' . $this->instance_id);
            $price = 0;

			foreach($weight_classes as $weight_class) {
				if($weight_class->minimum < $weight_total && $weight_total <= $weight_class->maximum){
					$price = $weight_class->shipping_price;
					break;
				}
				if($weight_class->shipping_price > $price) {
				    $price = $weight_class->shipping_price;
                }
			}
		}

        return $this->evaluate_cost(
        	$price,
	        array(
				'qty'  => $this->get_package_item_qty($package),
			    'cost' => $package['contents_cost'],
	        )
        );
    }

	/**
	 * Get item qty of package
	 * @param $package
	 *
	 * @return int
	 */
	public function get_package_item_qty( $package ) {
		$total_quantity = 0;
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
				$total_quantity += $values['quantity'];
			}
		}
		return $total_quantity;
	}

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	public function showShippingMethod() {
		if($this->get_option('hide_shipping_method_if_outside_parameters', 'no') !== 'yes') {
			return true;
		}

		if($this->get_option('differentiated_price_type') == 'Price') {
			$price_classes = get_option('Price_' . $this->instance_id);

			foreach($price_classes as $price_class) {
				if($price_class->minimum < $this->getCartTotal() && $this->getCartTotal() <= $price_class->maximum){
					return true;
				}
			}
        } else if($this->get_option('differentiated_price_type') == 'Weight') {
			$weight_total = $GLOBALS['woocommerce']->cart->cart_contents_weight;
			$weight_classes = get_option('Weight_' . $this->instance_id);

			foreach($weight_classes as $weight_class) {
			    if($weight_class->minimum < $weight_total && $weight_total <= $weight_class->maximum){
			        return true;
                }
            }
        } else {
		    return true;
        }

		return false;
	}

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	public function getFreeShippingTotal() {
		if(is_null($this->free_shipping_total)) {
			$this->free_shipping_total = $this->get_option('free_shipping_total');
		}

		return $this->free_shipping_total;
	}

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	public function eligibleFreeShipping($package = array()) {
	    if($this->get_option('enable_free_shipping') === 'Yes' && $this->getCartTotal() >= $this->getFreeShippingTotal()) {
			return true;
		}

		if($this->get_option('enable_free_shipping_with_coupon') === 'yes' && count($package['applied_coupons']) > 0) {
			$woo_version =  Legacy::getWooCommerceVersion();

			foreach($package['applied_coupons'] as $coupon) {
				$obj = new WC_Coupon($coupon);

				if($woo_version < '3.0.0') {
					return $obj->enable_free_shipping();
				} else {
					return $obj->get_free_shipping();
				}
			}
        }

		return false;
	}

	/*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.1.8
	 */
	public function getCartTotal() {
		if(is_null($this->cart_total)) {
			$cart = $GLOBALS['woocommerce']->cart;

			if(wc_tax_enabled()) {
				$this->cart_total = $cart->cart_contents_total + array_sum($cart->taxes);
			} else {
				$this->cart_total = $cart->cart_contents_total;
			}

			// WPML Multicurrency support
			if(isset($GLOBALS['woocommerce_wpml']) && isset($GLOBALS['woocommerce_wpml']->multi_currency) && isset($GLOBALS['woocommerce_wpml']->multi_currency->prices)) {
				$this->cart_total = $GLOBALS['woocommerce_wpml']->multi_currency->prices->unconvert_price_amount($this->cart_total);
			}
		}

		return $this->cart_total;
	}

	/**
	 * Check if current method is chosen in shipping index
	 * @param     $method
	 * @param int $index
	 *
	 * @return bool
	 */
	public function isChosenShippingMethod($method, $index = 0) {
		if($method->method_id != $this->id) {
			return false;
		}

		$chosen_methods = WC()->session->get('chosen_shipping_methods');

		return (isset($chosen_methods[$index]) && $chosen_methods[$index] === $method->id);
	}
}

