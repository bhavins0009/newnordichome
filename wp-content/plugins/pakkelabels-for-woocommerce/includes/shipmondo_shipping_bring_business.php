<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once('shipmondo_shipping_main.php');

class Shipmondo_Shipping_Bring_Business extends Shipmondo_Shipping_Main
{

    /**
     * Constructor. The instance ID is passed to this.
     */
    public function __construct($instance_id = 0)
    {
        $this->id                    = 'shipmondo_shipping_Bring_business';
        $this->instance_id           = absint($instance_id);
        $this->method_title          = __('Bring Business', 'pakkelabels-for-woocommerce');
        $this->method_description    = __('Adds the option to ship with the Bring business to the checkout', 'pakkelabels-for-woocommerce');
        $this->supports              = array(
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

        add_action('woocommerce_after_shipping_rate', array($this, 'shipmondo_shipping_Bring_business_show_below_shipping'));

        add_action('woocommerce_checkout_process', array($this, 'shipmondo_shipping_Bring_business_field_process'));
    }


    function addFilters()
    {

    }


    function shipmondo_shipping_Bring_business_field_process()
    {
        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if((isset($_POST['ship_to_different_address']) &&  ($_POST['shipping_company'] == '' || !isset($_POST['shipping_company']))) && ($choosen_shipping_method1 == "shipmondo_shipping_Bring_business" || $choosen_shipping_method2 == "shipmondo_shipping_Bring_business")){
            if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
                $woocommerce->add_error(__('Please fill out the Shipping company', 'pakkelabels-for-woocommerce'));
            } else {
                wc_add_notice( __('Please fill out the Shipping company', 'pakkelabels-for-woocommerce') , 'error');
            }
        }
        if((!isset($_POST['ship_to_different_address']) && ($_POST['billing_company'] == '' || !isset($_POST['billing_company']))) && ($choosen_shipping_method1 == "shipmondo_shipping_Bring_business" || $choosen_shipping_method2 == "shipmondo_shipping_Bring_business")){
            if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
                $woocommerce->add_error(__('Please fill out the billing company', 'pakkelabels-for-woocommerce'));
            } else {
                wc_add_notice( __('Please fill out the billing company', 'pakkelabels-for-woocommerce') , 'error');
            }
        }
    }





    function shipmondo_shipping_Bring_business_show_below_shipping($rate){
        global $woocommerce;

        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if($choosen_shipping_method1 == "shipmondo_shipping_Bring_business" || $choosen_shipping_method2 == "shipmondo_shipping_Bring_business"){
            if($rate->method_id == 'shipmondo_shipping_Bring_business'){
                echo '<div class="Bring_shipping_method_text shipping_company_required">' . __('The company name is required.', 'pakkelabels-for-woocommerce').'</div>';
            }
        }
    }


    /* Register the shipping method in WooCommerce*/
    function register_shipping_method($methods)
    {
        $methods['shipmondo_shipping_Bring_business'] = 'Shipmondo_Shipping_Bring_Business';
        return $methods;
    }
}


$shipmondo_Bring_Business = new Shipmondo_Shipping_Bring_Business();
$shipmondo_Bring_Business->mainAddAction();
$shipmondo_Bring_Business->addActions();
$shipmondo_Bring_Business->addFilters();