<?php namespace ShipmondoForWooCommerce\Lib\Tools;

class Autoloader {

    private $lib_namespace = 'ShipmondoForWooCommerce';
    private $plugin_namespace = 'ShipmondoForWooCommerce';
    private $plugin_path;

    public function __construct($plugin_path) {
        $this->plugin_path = $plugin_path;

        $this->register();
    }


    public function unregister() {
        spl_autoload_unregister(array($this, 'autoloader'));
    }

    public function register($prepend = true) {
        spl_autoload_register(array($this, 'autoloader'), true, $prepend);
    }

    public function autoloader($class) {

        $path_parts = explode('\\', $class);

        if(count($path_parts) <= 1) {
            return;
        }

        $start = array_values($path_parts)[0];

        if($start != $this->lib_namespace && $start != $this->plugin_namespace) {
            return;
        }

        $file = $this->plugin_path;

        $end = array_pop($path_parts);

        foreach($path_parts as $part) {
            if($part !== $this->lib_namespace && $part !== $this->plugin_namespace) {
                preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $part, $matches);

                $parts = $matches[0];

                $part = implode('-', $parts);

                $file .= strtolower($part) . '/';
            }
        }

        foreach(array('class.', 'trait.', 'controller.', '') as $file_name_start) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $end, $matches);

            $end = $matches[0];

            $end = implode('-', $end);

            $file_ending = $file_name_start . strtolower($end) . '.php';

            if(is_file($file . $file_ending)) {
                include_once($file . $file_ending);

                return;
            }
        }
    }
}
