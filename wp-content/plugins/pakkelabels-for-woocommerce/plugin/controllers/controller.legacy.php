<?php namespace ShipmondoForWooCommerce\Plugin\Controllers;

use ShipmondoForWooCommerce\Lib\Tools\Loader;

class Legacy {

	public function __construct() {
		$this->registerActions();
	}

	private function registerActions() {
		// Load WooCommerce pre 3.0.0 legacy actions
		if(static::getWooCommerceVersion() < '3.0.0') {
			add_action('woocommerce_checkout_update_order_meta', array($this, 'UpdateOrderMeta'), 10, 2);
		}
	}

	/**
	 * Updated post meta for the order if pickup point shipping method is selected
	 * @param $order_id
	 * @param $data
	 */
	function UpdateOrderMeta($order_id, $data)	{

		// Check if shipping method is this shipping method
		if(!isset($data['shipping_method'])) {
			return;
		}

		foreach((array) $data['shipping_method'] as $index => $shipping_method) {
			$shipping_method = substr($shipping_method, 0, strpos($shipping_method, ':'));

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

			// Fix WCML (WPML) conflict
			Loader::addFilter('update_post_metadata', $this, 'shortCircuitWPMLCurrencyUpdate', 99, 5);

			$agent = PickupPoint::getAgent($shipping_method);

			$shipping_info = array(
				'first_name' => (!empty($data['shipping_first_name']) ? $data['shipping_first_name'] : $data['billing_first_name']),
				'last_name' => (!empty($data['shipping_last_name']) ? $data['shipping_last_name'] : $data['billing_last_name']),
				'company' => (!empty($_POST['shop_name'][$index]) ? $_POST['shop_name'][$index] : PickupPoint::getCurrentSelection('name', $agent, $index)),
				'address_1' => (!empty($_POST['shop_address'][$index]) ? $_POST['shop_address'][$index] : PickupPoint::getCurrentSelection('address', $agent, $index)),
				'address_2' => (!empty($_POST['shop_ID'][$index]) ? $_POST['shop_ID'][$index] : PickupPoint::getCurrentSelection('id_string', $agent, $index)),
				'city' => (!empty($_POST['shop_city'][$index]) ? $_POST['shop_city'][$index] : PickupPoint::getCurrentSelection('city', $agent, $index)),
				'postcode' => (!empty($_POST['shop_zip'][$index]) ? $_POST['shop_zip'][$index] : PickupPoint::getCurrentSelection('zip', $agent, $index)),
			);

			foreach($shipping_info as $meta_name => $meta_value) {
				update_post_meta($order_id, "_shipping_{$meta_name}", $meta_value);
			}

			Loader::removeFilter('update_post_metadata', $this, 'shortCircuitWPMLCurrencyUpdate', 99);

			break;
		}
	}

	/**
	 * Solves problem with WPML changing the currency after meta data update due to update_order_currency in woocommerce-multilanugage/inc/class-wcml-orders.php:384
	 *
	 * @param $return
	 * @param $object_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param $prev_value
	 *
	 * @return mixed
	 */
	function shortCircuitWPMLCurrencyUpdate($return, $object_id, $meta_key, $meta_value, $prev_value) {
		if($meta_key === '_order_currency') {
			if(!empty($prev_value)) {
				return false;
			}

			$old_value = get_metadata('post', $object_id, $meta_key);
			if(count($old_value) > 0) {
				if(!empty($old_value[0])) {
					return false;
				}
			}
		}

		return $return;
	}



	// HELPER FUNCTIONS

	/**
	 * Get version of WooCommerce
	 * @return string|null
	 */
	public static function getWooCommerceVersion() {
		// If get_plugins() isn't available, require it
		if(!function_exists('get_plugins')) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';

		// If the plugin version number is set, return it
		if(isset($plugin_folder[$plugin_file]['Version'])) {
			return $plugin_folder[$plugin_file]['Version'];
		}
		// Otherwise return null
		return NULL;
	}
}