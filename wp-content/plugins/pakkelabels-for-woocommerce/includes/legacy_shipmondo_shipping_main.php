<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 *
 * @class 		Shipmondo_Shipping_Main
 * @version		0.1.0
 * @author 		Magnus Vejlø - Boostr
 */
abstract class Legacy_Shipmondo_Shipping_Main extends WC_Shipping_Method
{

    /* Abstract Methods */
    /* All class's exentind this class, wil be forced to load these methods! */
    abstract protected function register_shipping_method($methods);
    abstract protected function addActions();
    abstract protected function addFilters();

    /* Variabels */


    /* Methods */
    function init() {
        //Load the settings API default-settings.php
        $this->form_fields                                  = include( 'method_settings/legacy-settings-default.php' );
        $this->title		  		                        = $this->get_option('title');
        $this->shipping_price 		                        = $this->get_option('shipping_price');
        $this->availability 		                        = $this->get_option('availability');
        $this->countries	 		                        = $this->get_option('countries');
        $this->tax_status                                   = $this->get_option( 'tax_status' );
        $this->enable_free_shipping                         = $this->get_option('enable_free_shipping');
        $this->free_shipping_total                          = $this->get_option('free_shipping_total');
        $this->differentiated_price_type                    = $this->get_option('differentiated_price_type');
        $this->enable_free_shipping_with_coupon             = $this->get_option('enable_free_shipping_with_coupon');

        //This is part of the settings API. Loads settings you previously init.
        $this->init_settings();
        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );


        if (!class_exists('Legacy_Shipmondo_Shipping_Admin'))
        {
            include_once('legacy_shipmondo_shipping_admin.php');
            $oLegacy_Shipmondo_Shipping_Admin = new Legacy_Shipmondo_Shipping_Admin($this);
        }
    } // function init ends

    function mainAddActions()
    {
        //ajax for the pakkeshop list
        add_action('woocommerce_checkout_update_order_meta', array($this, 'woocommerce_checkout_update_order_meta_method_shipping_shipmondo'));
    }

    //Updates the order with the shipping information of the choosen packetshop
    function woocommerce_checkout_update_order_meta_method_shipping_shipmondo($order_id)
    {
        if (!empty($_POST["shipmondo"])) {
            update_post_meta($order_id, '_shipping_first_name', $_POST['billing_first_name']);
            update_post_meta($order_id, '_shipping_last_name', $_POST['billing_last_name']);

            $shopnumber = $_POST['shipmondo'];
            update_post_meta($order_id, '_shipping_company', $_POST["shop_name"][$shopnumber]);
            update_post_meta($order_id, '_shipping_address_1', $_POST["shop_address"][$shopnumber]);
            update_post_meta($order_id, '_shipping_address_2', $_POST["shop_ID"][$shopnumber]);
            update_post_meta($order_id, '_shipping_city', $_POST["shop_city"][$shopnumber]);
            update_post_meta($order_id, '_shipping_postcode', $_POST["shop_zip"][$shopnumber]);
            add_post_meta($order_id, __('Pakkeshop', 'pakkelabels-for-woocommerce'), $shopnumber, true);
        }
    }



    /* Returns/Echo's the HTML that is used to make a zipcode textarea & the find shop button */
    function HTML_zipAndFind($shipping_type)
    {
        if(is_checkout()) {
            \ShipmondoForWooCommerce\Plugin\Plugin::getTemplate('pickup-point-selection.open-modal-button', array('shipping_type' => $shipping_type));
        } else {
            echo '<br/><div class="shipping_pickup_cart">' . __('Pickup point is selected during checkout','pakkelabels-for-woocommerce') . '</div>';
        }
    }





    protected function evaluate_cost( $sum, $args = array() )
    {
        include_once( SHIPMONDO_PLUGIN_DIR.'/lib/php/class-wc-eval-math.php' );

        // Allow 3rd parties to process shipping cost arguments
        $args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
        $locale         = localeconv();
        $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );

        // Remove whitespace from string
        $sum = preg_replace( '/\s+/', '', $sum );
        // Remove locale from string
        $sum = str_replace( $decimals, '.', $sum );
        // Trim invalid start/end characters
        $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );
        // Do the math
        return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
    }


    public function calculate_shipping( $package = array() ) 
    {
        require_once(SHIPMONDO_PLUGIN_DIR.'/lib/php/field_calculator.php');
        global $woocommerce;
	
	    $hide_shipping_method_if_outside_parameters = $this->get_option( 'hide_shipping_method_if_outside_parameters' ) === 'yes';
     
	    $show_this_method = true;
		    
        if (wc_tax_enabled())
        {
            $iCart_total = $woocommerce->cart->cart_contents_total + array_sum($woocommerce->cart->taxes);
        }
        else
        {
            $iCart_total = $woocommerce->cart->cart_contents_total;
        }

        if($this->enable_free_shipping == "Yes" && $iCart_total >= $this->free_shipping_total)
        {
            $return_price = 0;
            
        }
        else
        {

            //Price based calculations of the shipping price
            if($this->get_option('differentiated_price_type') == "Price")
            {
	            $fallthrough = true;
            	
                $oRows = get_option('Price_'.strtolower(static::class));
                foreach ($oRows as $oRow)
                {
                    if($oRow->minimum < $iCart_total && $iCart_total <= $oRow->maximum)
                    {
                        $return_price = (float)$oRow->shipping_price;
                        $fallthrough = false;
                    }
                }
	
	            if( $fallthrough ) {
		            $show_this_method = false;
	            }

                //if the price is not within the ranges, it will pick the highest priced range!
                if(!isset($return_price))
                {
                    $return_price = 0;
                    foreach ($oRows as $oRow)
                    {
                        if ((float)$oRow->shipping_price > $return_price)
                        {
                            $return_price = (float)$oRow->shipping_price;
                        }
                    }
                }
            }
            //Weight based calculation of shipping price
            else if ($this->get_option('differentiated_price_type') == "Weight")
            {
            	$fallthrough = true;
            	
                $iCartWeight = $woocommerce->cart->cart_contents_weight;
                $oRows = get_option('Weight_'.strtolower(static::class));
                foreach ($oRows as $oRow)
                {
                    if($oRow->minimum < $iCartWeight && $iCartWeight <= $oRow->maximum)
                    {
                        $return_price = (float)$oRow->shipping_price;
                        $fallthrough = false;
                    }

                }
	
	            if( $fallthrough ) {
		            $show_this_method = false;
	            }
	            
                //if the price is not within the ranges, it will pick the highest priced range!
                if(!isset($return_price))
                {
                    $return_price = 0;
                    foreach ($oRows as $oRow)
                    {
                        if ((float)$oRow->shipping_price > $return_price)
                        {
                            $return_price = (float)$oRow->shipping_price;
                        }

                    }
                }
            }
            //$this->get_option('differentiated_price_type') == "Quantity"
            else
            {
                $Cal = new Field_calculate();
                $return_price = (float)$Cal->calculate(str_replace("[qty]",WC()->cart->get_cart_contents_count(),$this->shipping_price));
            }
	
	        if( $this->enable_free_shipping_with_coupon ) {
		
		        if(count($package['applied_coupons']) > 0){
			        foreach($package['applied_coupons'] as $coupon){
				        $obj = new WC_Coupon($coupon);
				        $iCart_total = $iCart_total - $obj->amount;
				
				        // check if coupon grants free shipping
				        if($obj->free_shipping == 'yes'){
					        //				        $coupon_free_shipping = true;
					        $return_price = 0;
				        }
			        }
		        }
	        }

        }

        $return_price = $this->evaluate_cost( $return_price, array());

        $rate = array(
            'id' => $this->id,
            // 'label' => $this->title,
            'label' => $this->title.(empty($return_price) ? ' '.apply_filters('shipmondo_free_label',__('(free)','pakkelabels-for-woocommerce')) : ''),
            'cost' => $return_price,
        );
	
	    /**
	     * If the setting to "hide the method if outside parameter" is not set to true, we will show the method anyway.
	     */
	    if( ! $hide_shipping_method_if_outside_parameters ) {
		    $show_this_method = true;
	    }

        // Register the rate
	    if( $show_this_method ) {
		
		    $this->add_rate( $rate );
		
	    }
    }
}


