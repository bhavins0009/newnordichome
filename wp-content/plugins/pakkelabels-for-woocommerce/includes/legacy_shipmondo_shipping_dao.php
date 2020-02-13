<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Flat Rate Shipping Method.
 *
 * This class is here for backwards commpatility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version		2.4.0
 * @package		WooCommerce/Classes/Shipping
 * @author 		WooThemes
 */

require_once('legacy_shipmondo_shipping_main.php');

class Legacy_Shipmondo_Shipping_DAO extends Legacy_Shipmondo_Shipping_Main
{

    public function __construct() {
        $this->id = 'legacy_shipmondo_shipping_dao'; // Id for your shipping method. Should be unique.
        $this->method_title = __('DAO Pickup Point', 'pakkelabels-for-woocommerce'); // Title shown in admin
        $this->method_description = __('Adds the option to ship with the DAO pickup point to the checkout', 'pakkelabels-for-woocommerce'); // Description shown in admin
        $this->init();
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */

    function addActions()
    {
        //adds the shipping method to the WooCommerce
        add_filter('woocommerce_shipping_methods', array($this, 'register_shipping_method'));
        //runs the code after the choosen shipping method
        add_action('woocommerce_after_shipping_rate', array($this, 'check_choosen_shipping_method') );
    }

    function addFilters()
    {

    }

    function register_shipping_method( $methods ) {
        $methods['legacy_shipmondo_shipping_dao'] = 'Legacy_Shipmondo_Shipping_DAO';
        return $methods;
    }

    function check_choosen_shipping_method($rate)
    {
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = preg_replace('/[0-9]+/', '', $chosen_methods[0]);;
        if($chosen_shipping === 'legacy_shipmondo_shipping_dao')
        {
            if ($rate->method_id == 'legacy_shipmondo_shipping_dao') {
                $this->HTML_zipAndFind('dao');
            }
        }
    }
}

$shipmondo_LegacyDAO = new Legacy_Shipmondo_Shipping_DAO();
$shipmondo_LegacyDAO->mainAddActions();
$shipmondo_LegacyDAO->addActions();
$shipmondo_LegacyDAO->addFilters();
