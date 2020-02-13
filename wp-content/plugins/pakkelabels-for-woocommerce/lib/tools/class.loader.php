<?php namespace ShipmondoForWooCommerce\Lib\Tools;

class Loader {

    public static function addAction($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $callback = ($component !== null ? array($component, $callback) : $callback);
        \add_action($hook, $callback , $priority, $accepted_args);
    }

    public static function addFilter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $callback = ($component !== null ? array($component, $callback) : $callback);
        \add_filter($hook, $callback , $priority, $accepted_args);
    }

    public static function removeFilter($hook, $component, $callback, $priority = 10) {
	    $callback = ($component !== null ? array($component, $callback) : $callback);
	    \remove_filter($hook, $callback , $priority);
    }
}
