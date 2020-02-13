<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once('shipmondo_shipping_main.php');
/**
 * Sample instance based method.
 */
class Shipmondo_Shipping_Custom extends Shipmondo_Shipping_Main
{

    /**
     * Constructor. The instance ID is passed to this.
     */
    public function __construct($instance_id = 0)
    {
        $this->id                 = 'shipmondo_shipping_custom';
        $this->instance_id        = absint($instance_id);
        $this->method_title       = __('Other', 'pakkelabels-for-woocommerce');
        $this->method_description = __('Adds a custom option for shipping in the checkout', 'pakkelabels-for-woocommerce');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
        );
        $this->init();
    }


    /* add the diffrent actions */
    function addActions()
    {
        //adds the shipping method to the WooCommerce
        add_filter('woocommerce_shipping_methods', array($this, 'register_shipping_method'));

        add_action('woocommerce_checkout_update_order_meta', array($this, 'shipmondo_shipping_checkout_update_order_meta_custom_method'));
    }


    function addFilters()
    {

    }


    function shipmondo_shipping_checkout_update_order_meta_custom_method($order_id)
    {
        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if ($choosen_shipping_method1 == 'shipmondo_shipping_custom' || $choosen_shipping_method2 == 'shipmondo_shipping_custom') {
            if ($_POST['billing_address_1'] && !$_POST['shipping_address_1']) {
                update_post_meta($order_id, '_shipping_address_1', $_POST['billing_address_1']);
                update_post_meta($order_id, '_shipping_city', $_POST["billing_city"]);
                update_post_meta($order_id, '_shipping_postcode', $_POST["billing_postcode"]);
            } elseif ($_POST['shipping_address_1']) {
                update_post_meta($order_id, '_shipping_address_1', $_POST['shipping_address_1']);
                update_post_meta($order_id, '_shipping_city', $_POST["shipping_city"]);
                update_post_meta($order_id, '_shipping_postcode', $_POST["shipping_postcode"]);
            }
            add_post_meta($order_id, 'CustomShippingOption', 'yes', true);
        }
    }

    /* Register the shipping method in WooCommerce*/
    function register_shipping_method($methods)
    {
        $methods['shipmondo_shipping_custom'] = 'Shipmondo_Shipping_Custom';
        return $methods;
    }
}


$Shipmondo_Shipping_Custom = new Shipmondo_Shipping_Custom();
$Shipmondo_Shipping_Custom->mainAddAction();
$Shipmondo_Shipping_Custom->addActions();
$Shipmondo_Shipping_Custom->addFilters();