<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once('shipmondo_shipping_main.php');
/**
 * Sample instance based method.
 */
class Shipmondo_Shipping_DAO extends Shipmondo_Shipping_Main
{

	public $agent = 'dao';

    /**
     * Constructor. The instance ID is passed to this.
     */
    public function __construct( $instance_id = 0 )
    {
        $this->id                    = 'shipmondo_shipping_dao';
        $this->instance_id 			 = absint( $instance_id );
        $this->method_title          = __('DAO Pickup Point', 'pakkelabels-for-woocommerce');
        $this->method_description    = __( 'Adds the option to ship with the DAO pickup point to the checkout', 'pakkelabels-for-woocommerce');
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
        //runs the code after the choosen shipping method
        add_action('woocommerce_after_shipping_rate', array($this, 'check_choosen_shipping_method'), 10, 2);
    }


    function addFilters()
    {

    }


    /* Register the shipping method in WooCommerce*/
    function register_shipping_method( $methods ) {
        $methods['shipmondo_shipping_dao'] = 'Shipmondo_Shipping_DAO';
        return $methods;
    }

    /* Checks the choosen shipping method, and adds */
    function check_choosen_shipping_method($rate, $index) {
	    if($this->isChosenShippingMethod($rate, $index)) {
            $this->HTML_zipAndFind($rate, $index);
        }
    }
}

$shipmondo_DAO = new Shipmondo_shipping_DAO();
$shipmondo_DAO->mainAddAction();
$shipmondo_DAO->addActions();
$shipmondo_DAO->addFilters();
