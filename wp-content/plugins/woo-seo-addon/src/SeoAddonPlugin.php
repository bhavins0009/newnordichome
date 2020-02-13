<?php namespace Premmerce\SeoAddon;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\SDK\V2\Plugin\PluginInterface;
use Premmerce\SeoAddon\Admin\Admin;
use Premmerce\SeoAddon\Core\Updates\Updater;
use Premmerce\SeoAddon\Frontend\Seo;

/**
 * Class SeoAddonPlugin
 * @package Premmerce\SeoAddon
 */
class SeoAddonPlugin implements PluginInterface
{
    /**
     * @var FileManager FileManager
     */
    private $fileManager;

    /**
     * @var AdminNotifier
     */
    private $notifier;

    const VERSION = '2.0';

    /**
     * PremmerceSeoPlugin constructor.
     *
     * @param $mainFile
     *
     */
    public function __construct($mainFile)
    {
        $this->fileManager = new FileManager($mainFile);
        $this->notifier    = new AdminNotifier();
    }

    /**
     * Run plugin part
     */
    public function run()
    {
        $this->registerHooks();

        if (is_admin()) {
            new Admin($this->fileManager);
        } else {
            new Seo();
        }
    }

    /**
     * Register plugin hooks
     */
    private function registerHooks()
    {
        add_action('init', array($this, 'loadTextDomain'));
        add_action('admin_init', array(new Updater(), 'update'));
    }

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('woo-seo-addon', false, $name . '/languages/');
    }

    /**
     * Fired when the plugin is activated
     */
    public function activate()
    {
        if (! get_option('premmerce_seo_db_version')) {
            update_option('premmerce_seo_db_version', self::VERSION);

            update_option('premmerce_seo_settings_markup_tc', 'on');
            update_option('premmerce_seo_settings_markup_ld', 'on');
            update_option('premmerce_seo_settings_markup_og', 'on');

            update_option('premmerce_seo_settings_image_alts', 'on');

            if (taxonomy_exists('product_brand')) {
                update_option('premmerce_seo_settings_brand_field', 'product_brand');
            }
        }
    }

    /**
     * Fired when the plugin is deactivated
     */
    public function deactivate()
    {
    }

    /**
     * Fired when the plugin is uninstalled
     */
    public static function uninstall()
    {
        delete_option('premmerce_seo_settings_markup_og');
        delete_option('premmerce_seo_settings_markup_tc');
        delete_option('premmerce_seo_settings_markup_ld');
        delete_option('premmerce_seo_settings_image_alts');

        delete_option('premmerce_seo_settings_brand_field');
        delete_option('premmerce_seo_address');
        delete_option('premmerce_seo_email');
        delete_option('premmerce_seo_telephone');
        delete_option('premmerce_seo_openingHours');
        delete_option('premmerce_seo_paymentAccepted');
    }
}
