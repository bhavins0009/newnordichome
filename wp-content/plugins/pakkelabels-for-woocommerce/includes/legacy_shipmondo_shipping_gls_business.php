<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


require_once('legacy_shipmondo_shipping_main.php');

/**
 * @class 		Shipmondo_Shipping_GLS_Business_Legacy
 * @version		0.1.0
 * @author 		Magnus Vejlø - Shipmondo
 */
class Legacy_Shipmondo_Shipping_GLS_Business extends Legacy_Shipmondo_Shipping_Main
{

    public function __construct($instance_id = 0)
    {
        $this->id = 'legacy_shipmondo_shipping_gls_business';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('GLS Business ', 'pakkelabels-for-woocommerce');
        $this->method_description = __('Adds the option to ship with the GLS business to the checkout', 'pakkelabels-for-woocommerce');
        $this->init();
    }


    /* add the diffrent actions */
    function addActions()
    {
        //adds the shipping method to the WooCommerce
        add_filter('woocommerce_shipping_methods', array($this, 'register_shipping_method'));

        add_action('woocommerce_after_shipping_rate', array($this, 'shipmondo_shipping_gls_business_show_below_shipping'));

        add_action('woocommerce_checkout_process', array($this, 'shipmondo_shipping_gls_business_field_process'));
    }


    function addFilters()
    {

    }


    function shipmondo_shipping_gls_business_field_process() {

        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if((isset($_POST['ship_to_different_address']) &&  ($_POST['shipping_company'] == '' || !isset($_POST['shipping_company']))) && ($choosen_shipping_method1 == "legacy_shipmondo_shipping_gls_business" || $choosen_shipping_method2 == "legacy_shipmondo_shipping_gls_business")){
            if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
                $woocommerce->add_error(__('Please fill out the Shipping company', 'pakkelabels-for-woocommerce'));
            } else {
                wc_add_notice( __('Please fill out the Shipping company', 'pakkelabels-for-woocommerce') , 'error');
            }
        }
        if((!isset($_POST['ship_to_different_address']) && ($_POST['billing_company'] == '' || !isset($_POST['billing_company']))) && ($choosen_shipping_method1 == "legacy_shipmondo_shipping_gls_business" || $choosen_shipping_method2 == "legacy_shipmondo_shipping_gls_business")){
            if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
                $woocommerce->add_error(__('Please fill out the billing company', 'pakkelabels-for-woocommerce'));
            } else {
                wc_add_notice( __('Please fill out the billing company', 'pakkelabels-for-woocommerce') , 'error');
            }
        }
    }




    function shipmondo_shipping_gls_business_show_below_shipping($rate){
        global $woocommerce;

        global $woocommerce;
        $choosen_shipping_method1 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods[0] );
        $choosen_shipping_method2 = preg_replace('/\d/', '', $woocommerce->session->chosen_shipping_methods );
        if($choosen_shipping_method1 == "legacy_shipmondo_shipping_gls_business" || $choosen_shipping_method2 == "legacy_shipmondo_shipping_gls_business"){
            if($rate->method_id == 'legacy_shipmondo_shipping_gls_business'){
                echo '<div class="gls_shipping_method_text shipping_company_required">'  . __('The company name is required.', 'pakkelabels-for-woocommerce').'</div>';
            }
        }
    }


    /* Register the shipping method in WooCommerce*/
    function register_shipping_method($methods)
    {
        $methods['legacy_shipmondo_shipping_gls_business'] = 'Legacy_Shipmondo_Shipping_GLS_Business';
        return $methods;
    }
}


$shipmondo_GLS_Business_Legacy = new Legacy_Shipmondo_Shipping_GLS_Business();
$shipmondo_GLS_Business_Legacy->mainAddActions();
$shipmondo_GLS_Business_Legacy->addActions();
$shipmondo_GLS_Business_Legacy->addFilters();