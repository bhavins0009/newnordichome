<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WOOMULTI_CURRENCY_F_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woo-multi-currency' . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_ADMIN', WOOMULTI_CURRENCY_F_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_TEMPLATES', WOOMULTI_CURRENCY_F_DIR . "templates" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_FRONTEND', WOOMULTI_CURRENCY_F_DIR . "frontend" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_WIDGETS', WOOMULTI_CURRENCY_F_FRONTEND . "widgets" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_LANGUAGES', WOOMULTI_CURRENCY_F_DIR . "languages" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_INCLUDES', WOOMULTI_CURRENCY_F_DIR . "includes" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_PLUGINS', WOOMULTI_CURRENCY_F_DIR . "plugins" . DIRECTORY_SEPARATOR );
$plugin_url = plugins_url( 'woo-multi-currency' );
//$plugin_url = plugins_url( '',__FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'WOOMULTI_CURRENCY_F_CSS', $plugin_url . "/css/" );
define( 'WOOMULTI_CURRENCY_F_CSS_DIR', WOOMULTI_CURRENCY_F_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_JS', $plugin_url . "/js/" );
define( 'WOOMULTI_CURRENCY_F_JS_DIR', WOOMULTI_CURRENCY_F_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'WOOMULTI_CURRENCY_F_IMAGES', $plugin_url . "/images/" );
define( 'WOOMULTI_CURRENCY_F_FLAG', WOOMULTI_CURRENCY_F_IMAGES . "flag/" );


/*Include functions file*/
if ( is_file( WOOMULTI_CURRENCY_F_INCLUDES . "data.php" ) ) {
	require_once WOOMULTI_CURRENCY_F_INCLUDES . "data.php";
}

if ( is_file( WOOMULTI_CURRENCY_F_INCLUDES . "functions.php" ) ) {
	require_once WOOMULTI_CURRENCY_F_INCLUDES . "functions.php";
}

if ( is_file( WOOMULTI_CURRENCY_F_INCLUDES . "support.php" ) ) {
	require_once WOOMULTI_CURRENCY_F_INCLUDES . "support.php";
}

vi_include_folder( WOOMULTI_CURRENCY_F_ADMIN, 'WOOMULTI_CURRENCY_F_Admin_' );
vi_include_folder( WOOMULTI_CURRENCY_F_WIDGETS, 'WOOMULTI_CURRENCY_F_Widget_' );
vi_include_folder( WOOMULTI_CURRENCY_F_FRONTEND, 'WOOMULTI_CURRENCY_F_Frontend_' );
vi_include_folder( WOOMULTI_CURRENCY_F_PLUGINS, 'WOOMULTI_CURRENCY_F_Plugin_' );
