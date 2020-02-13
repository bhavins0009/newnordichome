<?php namespace ShipmondoForWooCommerce\Plugin;

use ShipmondoForWooCommerce\Plugin\Controllers\DibsEasyCompatibility;
use ShipmondoForWooCommerce\Plugin\Controllers\Legacy;
use ShipmondoForWooCommerce\Plugin\Controllers\Migrations;
use ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint;

class Plugin {
    use \ShipmondoForWooCommerce\Lib\Tools\Templating;

    static private $slug = 'shipmondo-for-woocommerce';
    static private $plugin_info;

    public function __construct($plugin_file) {
        $this->setPluginInfo('plugin', $this);
        $this->setPluginInfo('file', $plugin_file);
        $this->setPluginInfo('root', \plugin_dir_path($plugin_file));
        $this->setPluginInfo('data', static::readPluginData($plugin_file));


        $this->registerControllers();
    }

    protected static function readPluginData($plugin_file) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        return \get_plugin_data($plugin_file, false, false);
    }

    // SETTERS
    protected function setPluginInfo($name, $info) {
        static::$plugin_info[$name] = $info;
    }


    // GETTERS
    public static function getRoot($path = '') {
        return static::getPluginInfo('root') . $path;
    }

    public static function getVersion() {
        return static::getPluginData('Version');
    }

    public static function getTextDomain() {
        return static::getPluginData('TextDomain');
    }

    public static function getPluginInfo($name) {
        if(isset(static::$plugin_info[$name])) {
            return static::$plugin_info[$name];
        }

        return '';
    }

    public static function getPluginData($data_name) {
        $data = static::getPluginInfo('data');

        if(is_array($data) && isset($data[$data_name])) {
            return $data[$data_name];
        }

        return '';
    }

    public function registerControllers() {
        new PickupPoint();
        new DibsEasyCompatibility();
        new Migrations();
        new Legacy();
    }
}