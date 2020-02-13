<?php

use Premmerce\SeoAddon\SeoAddonPlugin;

/**
 * Premmerce SEO for WooCommerce  plugin
 *
 * @package           Premmerce\SeoAddon
 *
 * @wordpress-plugin
 * Plugin Name:       Premmerce SEO for WooCommerce
 * Plugin URI:        https://premmerce.com/woocommerce-seo-addon-yoast/
 * Description:       Premmerce SEO for WooCommerce  plugin extends the functionality of WooCommerce for microdata management.
 * Version:           2.1.2
 * Author:            premmerce
 * Author URI:        https://premmerce.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-seo-addon
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.6
 */

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

call_user_func(function () {

    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

    require_once plugin_dir_path(__FILE__) . '/freemius.php';

    $main = new SeoAddonPlugin(__FILE__);

    register_activation_hook(__FILE__, [$main, 'activate']);

    premmerce_wsa_fs()->add_action('after_uninstall', [SeoAddonPlugin::class, 'uninstall']);

    $main->run();
});
