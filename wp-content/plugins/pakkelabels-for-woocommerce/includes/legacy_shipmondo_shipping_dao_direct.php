<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


require_once('legacy_shipmondo_shipping_main.php');

/**
 * @class 		Shipmondo_Shipping_DAO_Business_Legacy
 * @version		0.1.0
 * @author 		Magnus Vejlø - Shipmondo
 */
class Legacy_Shipmondo_Shipping_DAO_Direct extends Legacy_Shipmondo_Shipping_Main
{

    public function __construct($instance_id = 0)
    {
        $this->id = 'legacy_shipmondo_shipping_dao_direct';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('DAO Direct', 'pakkelabels-for-woocommerce');
        $this->method_description = __('Adds the option to ship with the DAO direct to the checkout', 'pakkelabels-for-woocommerce');
        $this->init();
    }


    /* add the diffrent actions */
    function addActions()
    {
        //adds the shipping method to the WooCommerce
        add_filter('woocommerce_shipping_methods', array($this, 'register_shipping_method'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'shipmondo_shipping_checkout_update_order_meta_dao_method_direct'));
    }


    function addFilters()
    {
    }


    function shipmondo_shipping_checkout_update_order_meta_dao_method_direct($order_id)
    {
        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if ($choosen_shipping_method1 == 'legacy_shipmondo_shipping_dao_direct' || $choosen_shipping_method2 == 'legacy_shipmondo_shipping_dao_direct') {
            if ($_POST['billing_address_1'] && !$_POST['shipping_address_1']) {
                update_post_meta($order_id, '_shipping_address_1', $_POST['billing_address_1']);
                update_post_meta($order_id, '_shipping_city', $_POST["billing_city"]);
                update_post_meta($order_id, '_shipping_postcode', $_POST["billing_postcode"]);
            } elseif ($_POST['shipping_address_1']) {
                update_post_meta($order_id, '_shipping_address_1', $_POST['shipping_address_1']);
                update_post_meta($order_id, '_shipping_city', $_POST["shipping_city"]);
                update_post_meta($order_id, '_shipping_postcode', $_POST["shipping_postcode"]);
            }
            add_post_meta($order_id, 'DAO Direct', 'yes', true);
        }
    }


    /* Register the shipping method in WooCommerce*/
    function register_shipping_method($methods)
    {
        $methods['legacy_shipmondo_shipping_dao_direct'] = 'Legacy_Shipmondo_Shipping_DAO_Direct';
        return $methods;
    }
}


$shipmondo_DAO_Direct_Legacy = new Legacy_Shipmondo_Shipping_DAO_Direct();
$shipmondo_DAO_Direct_Legacy->mainAddActions();
$shipmondo_DAO_Direct_Legacy->addActions();
$shipmondo_DAO_Direct_Legacy->addFilters();