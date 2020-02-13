<?php namespace Premmerce\SeoAddon\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SeoAddon\Admin\Pages\Info;
use Premmerce\SeoAddon\Admin\Pages\Metadata;
use Premmerce\SeoAddon\Admin\Pages\Settings;
use Premmerce\SeoAddon\Admin\Pages\Social;
use Premmerce\SeoAddon\Core\Config\Config;

/**
 * Class Admin
 * @package Premmerce\SeoAddon\Admin
 */
class Admin
{
    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * Admin constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct($fileManager)
    {
        $this->fileManager = $fileManager;
        $this->config      = new Config('premmerce_seo');
        $this->registerFilters();
    }

    /**
     * Register filter
     */
    private function registerFilters()
    {
        add_filter('config_premmerce_seo', array($this, 'registerSettings'));
        add_filter('admin_menu', array($this, 'addMenuItem'));
        add_filter('admin_footer_text', array($this, 'addRateUsText'));
    }

    /**
     * Register settings pages
     *
     * @param array $settings
     *
     * @return array
     */
    public function registerSettings(array $settings)
    {
        $settings[] = new Settings();
        $settings[] = new Info();
        $settings[] = new Social();
        $settings[] = new Metadata();

        return $settings;
    }

    /**
     * Add seo item to menu
     */
    public function addMenuItem()
    {
        $svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="16" style="fill:#82878c" viewBox="0 0 20 16"><g id="Rectangle_7"> <path d="M17.8,4l-0.5,1C15.8,7.3,14.4,8,14,8c0,0,0,0,0,0H8h0V4.3C8,4.1,8.1,4,8.3,4H17.8 M4,0H1C0.4,0,0,0.4,0,1c0,0.6,0.4,1,1,1 h1.7C2.9,2,3,2.1,3,2.3V12c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1V1C5,0.4,4.6,0,4,0L4,0z M18,2H7.3C6.6,2,6,2.6,6,3.3V12 c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1v-1.7C8,10.1,8.1,10,8.3,10H14c1.1,0,3.2-1.1,5-4l0.7-1.4C20,4,20,3.2,19.5,2.6 C19.1,2.2,18.6,2,18,2L18,2z M14,11h-4c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1h4c0.6,0,1-0.4,1-1C15,11.4,14.6,11,14,11L14,11z M14,14 c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1C15,14.4,14.6,14,14,14L14,14z M4,14c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1 c0.6,0,1-0.4,1-1C5,14.4,4.6,14,4,14L4,14z"/></g></svg>';
        $svg = 'data:image/svg+xml;base64,' . base64_encode($svg);

        add_menu_page('SEO', 'SEO', 'manage_options', 'premmerce_seo', array($this, 'renderPage'), $svg);
    }

    /**
     * Render page
     */
    public function renderPage()
    {
        $tabs = array();
        foreach ($this->config->getAll() as $item) {
            $tabs[$item->getId()] = $item->getTitle();
        }

        $this->fileManager->includeTemplate(
            'admin/main.php',
            array(
                'config'  => $this->config,
                'current' => ! empty($_GET['tab']) ? $_GET['tab'] : 'premmerce_seo_settings',
                'page'    => $_GET['page'],
                'tabs'    => $tabs
            )
        );
    }

    /**
     * Add rate us text to admin footer
     *
     * @param string $text
     *
     * @return string
     */
    public function addRateUsText($text)
    {
        if (function_exists('get_current_screen') && get_current_screen() && get_current_screen()->parent_base === 'premmerce_seo') {
            $stars  = '&#9733;&#9733;&#9733;&#9733;&#9733;';
            $plugin = 'WooCommerce SEO Addon';
            $text   = sprintf(
                __('If you like %s, please rate us %s on WordPress.org directory.', 'woo-seo-addon'),
                "<strong>$plugin</strong>",
                "<a href='https://wordpress.org/support/plugin/woo-seo-addon/reviews?rate=5#new-post' target='_blank'>{$stars}</a>"
            );
        }


        return $text;
    }
}
