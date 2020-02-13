<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 *
 * @class 		boostr_shipping_admin
 * @version		0.1.0
 * @author 		Magnus Vejlø - Boostr
 */
class Legacy_Shipmondo_Shipping_Admin
{
    function __construct()
    {
        $this->init();
    }

    function init()
    {

        $this->addActions();
        $this->addFilters();
    }

    function addActions()
    {
        global  $woocommerce;

        //Save the shipping range data, add hook for AJAX call
        //add_action('wp_ajax_shipmondo_save_price_ranges', array($this, 'save_price_ranges_callback'));
        //add_action('wp_ajax_nopriv_shipmondo_save_price_ranges', array($this, 'save_price_ranges_callback'));

        //Get the shipping range data, add hook for AJAX call
        add_action('wp_ajax_shipmondo_get_price_ranges', array($this, 'get_price_ranges_callback'));
        add_action('wp_ajax_nopriv_shipmondo_get_price_ranges', array($this, 'get_price_ranges_callback'));


        $aAdminParams = array(
            'ajax_url'                              => admin_url('admin-ajax.php'),
            'sWeightTranslation'                    => __('Weight', 'pakkelabels-for-woocommerce'),
            'sPriceTranslation'                     => __('Price', 'pakkelabels-for-woocommerce'),
            'sQuantityTranslation'                  => __('Quantity', 'pakkelabels-for-woocommerce'),
            'sTitleTranslation'                     => __('Title for Shipmondo', 'pakkelabels-for-woocommerce'),
            'sMinimumTranslation'                   => __('Minimum cart total', 'pakkelabels-for-woocommerce'),
            'sMaximumTranslation'                   => __('Maximum cart total', 'pakkelabels-for-woocommerce'),
            'sShippingPriceTranslation'             => __('Shipping Price', 'pakkelabels-for-woocommerce'),
            'sBtnAddNewPriceRangeRowTranslation'    => __('Add row', 'pakkelabels-for-woocommerce'),
            'sCartTotalTranslation'                 => __('Cart Total', 'pakkelabels-for-woocommerce'),
            'sCurrencySymbol'                       => get_woocommerce_currency(),
            'sWeightUnit'                           => get_option('woocommerce_weight_unit'),
            'sShippingPriceTranslation'             => __('Shipping Price', 'pakkelabels-for-woocommerce'),
            'sShippingRangeHelperTextTranslation'   => __('In the price table below, you can choose to setup different shipping prices, that will be based on the carts total of your choosen type.<br/>If the cart total falls outside of any of the choosen ranges, the shipping price will default to the highest shipping price.<br/>Please make sure to follow the woocommerce standard, and use a period (.) as a decimal seperator.', 'pakkelabels-for-woocommerce'),
        );

        wp_enqueue_style('shipmondo-admin-shipping-settings.css', SHIPMONDO_PLUGIN_URL . '/css/shipmondo-admin-shipping-settings.css', array(), filemtime(SHIPMONDO_PLUGIN_DIR . '/css/shipmondo-admin-shipping-settings.css'));
        wp_enqueue_script('legacy-shipmondo-admin-shipping-settings.js', SHIPMONDO_PLUGIN_URL . '/js/legacy-shipmondo-admin-shipping-settings.js', array('jquery'), filemtime(SHIPMONDO_PLUGIN_DIR . '/js/legacy-shipmondo-admin-shipping-settings.js'));
        wp_localize_script('legacy-shipmondo-admin-shipping-settings.js', 'ShipmondoAdminParams', $aAdminParams);
        add_action('woocommerce_update_options', array($this, "update_range_prices"));
    }








    function addFilters()
    {

    }

    function update_range_prices()
    {
        $aPostKeys = array_keys($_POST);
        $oShippingData = json_decode(stripslashes_deep($_POST[$aPostKeys[0]]));
        if(isset($oShippingData->sShippingSection))
        {
            $sShippingSection = $oShippingData->sShippingSection;
            $sRangeType = $oShippingData->sRangeType;
            $oShippingRangeRow = json_decode($oShippingData->oShippingRows)->oRows;

            update_option($sRangeType . '_' . $sShippingSection, $oShippingRangeRow);


        }
    }


    function get_price_ranges_callback()
    {
        $sShippingSection = $_POST['sShippingSection'];
        $sRangeType = $_POST['sRangeType'];


        $response['oData'] = get_option($sRangeType . '_' . $sShippingSection);
        $response['status'] = "success";
        echo json_encode($response);
        wp_die();
    }
}