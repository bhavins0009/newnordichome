<?php
/**
 * Plugin Name: Shipmondo for WooCommerce
 * Plugin URI: https://shipmondo.com
 * Description: Bring, DAO365, GLS and PostNord Shipping for WooCommerce
 * Version: 3.1.0
 * Text Domain: pakkelabels-for-woocommerce
 * Domain Path: /languages
 * Author:  Shipmondo
 * Author URI: https://shipmondo.com
 * Requires at least: 4.5.2
 * Tested up to: 5.3
 * WC requires at least: 2.6.0
 * WC tested up to: 3.8
 */

	use ShipmondoForWooCommerce\Plugin\Controllers\Legacy;
	use ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint;

	if(!function_exists('is_plugin_active_for_network')) {
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

if(shipmondo_is_woocommerce_active()) {
	/* Start on MVC and OOP in the plugin - Added by Morning Train */
	require_once(plugin_dir_path(__FILE__) . 'lib/tools/class.autoloader.php');
	new \ShipmondoForWooCommerce\Lib\Tools\Autoloader(plugin_dir_path(__FILE__));
    $shipmondo_for_woocommerce = new \ShipmondoForWooCommerce\Plugin\Plugin(__FILE__);

    /* Inits the diffrent filters, actions, scripts and styles */
	function shipmondo_addAction()	{
		//loads the shipping methods on init
		add_action('init', 'shipmondo_load_shipping_methods_init');
		//Makes sure you cant buy without selecting a packet shop
		add_action('woocommerce_checkout_process', 'shipmondo_checkout_process_shipping');
		//Notice if frontend key is not set
		add_action('admin_notices', 'no_frontend_key_admin_notice');
		add_action('admin_notices', 'no_google_api_key_admin_notice');
		//register_activation_hook( __FILE__, 'installDB' );
	}

	function shipmondo_addFilter() {

	}

	function shipmondo_init() {
		shipmondo_addAction();
		shipmondo_addFilter();
		load_plugin_textdomain('pakkelabels-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

    /***********/
    /* Defines */
    /***********/
    $current_server_host = $_SERVER[ 'HTTP_HOST' ];
	$current_root_host = parse_url(plugins_url('', __FILE__))['host'];
	$plugin_url = $current_server_host === $current_root_host ? plugins_url( '', __FILE__ ) : str_replace( $current_root_host, $current_server_host, plugins_url( '', __FILE__ ) );
	
	define('SHIPMONDO_PLUGIN_URL', $plugin_url );
	define('SHIPMONDO_PLUGIN_DIR', dirname(__FILE__));

	shipmondo_init();

    /*******************/
    /* Require/Include */
    /*******************/
    include_once('settings/settings-plugin.php');


    /***********/
    /* Methods */
    /***********/



    function installDB()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

//        $sql_type_table = "CREATE TABLE {$wpdb->prefix}shipmondo_shipping_types (
//                iId bigint(20) NOT NULL auto_increment,
//                sType varchar(200) NOT NULL UNIQUE KEY,
//                PRIMARY KEY  (iId),
//                UNIQUE (sType)
//                ) $charset_collate;";
//        dbDelta( $sql_type_table );

/*        $sql_diffrentiated_table = "CREATE TABLE {$wpdb->prefix}shipmondo_shipping_diffrentiated (
                iId bigint(20) NOT NULL auto_increment,
                iId_shipping_method bigint(20) NOT NULL,
                iMin_price_range bigint(20) NOT NULL,
                iMax_price_range bigint(20) NOT NULL,
                sType varchar(200) NOT NULL,
                iShipping_price bigint(20) NOT NULL,
                PRIMARY KEY  (iId)
                ) $charset_collate;";
        dbDelta( $sql_diffrentiated_table );*/

//        $sql_type_quantity = "INSERT INTO {$wpdb->prefix}shipmondo_shipping_types (sType) VALUES ('Quantity')";
//        dbDelta( $sql_type_quantity );
//
//        $sql_type_price = "INSERT INTO {$wpdb->prefix}shipmondo_shipping_types (sType) VALUES ('Price')";
//        dbDelta( $sql_type_price );
//
//        $sql_type_weight = "INSERT INTO {$wpdb->prefix}shipmondo_shipping_types (sType) VALUES ('Weight')";
//        dbDelta( $sql_type_weight );

    }

    function no_frontend_key_admin_notice()
    {
        $aOptions = get_option('shipmondo_settings');
        $frontend_key = $aOptions['shipmondo_text_field_0'];
        if (empty($frontend_key) || strlen($frontend_key) < 2) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php $shipmondo_admin_url = '<a href="'. admin_url() .'/admin.php?page=shipmondo">Shipmondo Settings</a>'; ?>
                    <?php printf( esc_html__('Please go to the WooCommerce -> %s, and set a valid frontend key', 'pakkelabels-for-woocommerce'), $shipmondo_admin_url); ?>
                </p>
            </div>
        <?php }
    }


    function no_google_api_key_admin_notice()
    {
        $aOptions = get_option( 'shipmondo_settings' );
        $google_api_key = $aOptions['shipmondo_google_api_key'];
        if (empty($google_api_key) || strlen($google_api_key) < 5) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php $shipmondo_admin_url = '<a href="'. admin_url() .'/admin.php?page=shipmondo">Shipmondo Settings</a>'; ?>
                    <?php printf( esc_html__('Please go to the WooCommerce -> %s, and set a valid Google Map API key', 'pakkelabels-for-woocommerce'), $shipmondo_admin_url); ?>
                </p>
            </div>
        <?php }
    }


    //Checks if a packetshop is selected, and its one of the valid shipping methods
    function shipmondo_checkout_process_shipping()
    {
        global $woocommerce;
        if(!$woocommerce->cart->needs_shipping()) {
            return;
        }

        foreach($woocommerce->session->chosen_shipping_methods as $index => $rate_id) {
        	$shipping_method = substr($rate_id, 0, strpos($rate_id, ':'));

	        if(!in_array($shipping_method, array(
        		'shipmondo_shipping_gls',
		        'shipmondo_shipping_pdk',
		        'shipmondo_shipping_dao',
		        'shipmondo_shipping_bring',
		        'legacy_shipmondo_shipping_gls',
		        'legacy_shipmondo_shipping_pdk',
		        'legacy_shipmondo_shipping_dao',
		        'legacy_shipmondo_shipping_bring',
	        ))) {
        		continue;
	        }

	        if((empty($_POST['shipmondo']) || empty($_POST['shipmondo'][$index])) && !PickupPoint::isCurrentSelection($shipping_method, $index)) {
		        wc_add_notice(__('Please select a pickup point before placing your order.', 'pakkelabels-for-woocommerce'), 'error');
	        }
        }
    }

    /* Loads the shipping methods */
    //Refactor to use a loop with an array of methods and class if time
    function shipmondo_load_shipping_methods_init()
    {

        $frontend_key = get_option('shipmondo_settings')['shipmondo_text_field_0'];
        $google_api_key = get_option( 'shipmondo_settings' )['shipmondo_google_api_key'];
        if(!empty($frontend_key) && strlen($frontend_key) > 2 && !empty($google_api_key) && strlen($google_api_key) > 5)
        {
            if(Legacy::getWooCommerceVersion() >= '2.6') {
				//GLS
                if (!class_exists('Shipmondo_Shipping_GLS'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_gls.php');
                }
                if (!class_exists('Shipmondo_Shipping_GLS_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_gls_business.php');
                }
                if (!class_exists('Shipmondo_Shipping_GLS_Private'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_gls_private.php');
                }

				//Postnord
                if (!class_exists('Shipmondo_Shipping_PDK'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_pdk.php');
                }
                if (!class_exists('Shipmondo_Shipping_PostNord_Private'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_postnord_private.php');
                }
                if (!class_exists('Shipmondo_Shipping_PostNord_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_postnord_business.php');
                }

				//DAO
                if (!class_exists('Shipmondo_Shipping_DAO_Direct'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_dao_direct.php');
                }
                if (!class_exists('Shipmondo_Shipping_DAO'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_dao.php');
                }
				
				//Bring
				if (!class_exists('shipmondo_shipping_bring'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_bring.php');
                }
                if (!class_exists('shipmondo_shipping_bring_private'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_bring_private.php');
                }
				if (!class_exists('Shipmondo_Shipping_Bring_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_bring_business.php');
                }

				// Custom
                if (!class_exists('Shipmondo_Shipping_Custom'))
                {
                    require_once(dirname(__FILE__) . '/includes/shipmondo_shipping_custom.php');
                }
            }
            else
            {
				//GLS
                if (!class_exists('Legacy_Shipmondo_Shipping_GLS'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_gls.php');
                }
                if (!class_exists('Legacy_Shipmondo_Shipping_GLS_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_gls_business.php');
                }
                if (!class_exists('Legacy_Shipmondo_Shipping_GLS_Private'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_gls_private.php');
                }

				//Postnord
                if (!class_exists('Legacy_Shipmondo_Shipping_PDK'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_pdk.php');
                }
                if (!class_exists('Legacy_Shipmondo_Shipping_PostNord_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_postnord_business.php');
                }
                if (!class_exists('Legacy_Shipmondo_Shipping_PostNord_Private'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_postnord_private.php');
                }

				//DAO
                if (!class_exists('Legacy_Shipmondo_Shipping_DAO'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_dao.php');
                }
                if (!class_exists('Legacy_Shipmondo_Shipping_DAO_Direct'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_dao_direct.php');
                }
				
				//Bring
				if (!class_exists('legacy_shipmondo_shipping_bring'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_bring.php');
                }
                if (!class_exists('legacy_shipmondo_shipping_bring_private'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_bring_private.php');
                }
				if (!class_exists('Legacy_Shipmondo_Shipping_Bring_Business'))
                {
                    require_once(dirname(__FILE__) . '/includes/legacy_shipmondo_shipping_bring_business.php');
                }
				
            }
        }
    }
}//end plugin is active if



	/**
	 * Is WooCommerce active
	 * @return bool
	 */
	function shipmondo_is_woocommerce_active() {
		return (
		        class_exists('WooCommerce')
	            || in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
		        || is_plugin_active( 'woocommerce/woocommerce.php')
		        || is_plugin_active_for_network( 'woocommerce/woocommerce.php' )
		        || is_plugin_active( '__woocommerce/woocommerce.php')
		        || is_plugin_active_for_network( '__woocommerce/woocommerce.php' )
		);
	}