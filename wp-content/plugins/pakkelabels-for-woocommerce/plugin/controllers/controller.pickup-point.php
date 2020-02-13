<?php namespace ShipmondoForWooCommerce\Plugin\Controllers;

use ShipmondoForWooCommerce\Lib\Tools\Loader;
use ShipmondoForWooCommerce\Plugin\ShipmondoAPI;
use ShipmondoForWooCommerce\Plugin\Plugin;

class PickupPoint {
    /*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.2.0
	 */
    public function __construct() {
        $this->registerActions();
    }

    /*
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.2.0
	 */
    public function registerActions() {
        Loader::addAction('wp_enqueue_scripts', $this, 'enqueueScripts');
        Loader::addAction('wp_footer', $this, 'includeModalHTML');
        Loader::addAction('wp_ajax_shipmondo_get_pickup_points', $this, 'getPickupPointsSelectionHTML');
        Loader::addAction('wp_ajax_nopriv_shipmondo_get_pickup_points', $this, 'getPickupPointsSelectionHTML');
	    Loader::addAction('wp_ajax_shipmondo_set_selection_session', $this, 'setSelectionSession');
	    Loader::addAction('wp_ajax_nopriv_shipmondo_set_selection_session', $this, 'setSelectionSession');
    }

	/**
	 * Set session with current pickup point selection

	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 2.2.0
	 */
    public function setSelectionSession() {
    	$session = WC()->session->get('shipmondo_current_selection');

    	$session[$_POST['shipping_index']] = array(
    		'agent' => $_POST['agent'],
    		'selection' => $_POST['selection']
	    );

    	WC()->session->set('shipmondo_current_selection', $session);

    	exit();
    }

    /*
     * Enqueue Scripts and styling if on checkout page
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.2.0
	 */
    public function enqueueScripts() {
        if($this->isCheckout()) {
            Plugin::addScript('https://maps.googleapis.com/maps/api/js?key=' . get_option( 'shipmondo_settings' )['shipmondo_google_api_key']);
            Plugin::addScript('shipmondo-pickup-point');
            Plugin::addStyle('shipmondo-pickup-point');
            Plugin::localizeScript('shipmondo-pickup-point', 'shipmondo', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'gls_icon_url' => Plugin::getFileURL('picker_icon_gls.png', array('images')),
                'bring_icon_url' => Plugin::getFileURL('picker_icon_bring.png', array('images')),
                'dao_icon_url' => Plugin::getFileURL('picker_icon_dao.png', array('images')),
                'pdk_icon_url' => Plugin::getFileURL('picker_icon_pdk.png', array('images')),
                'select_shop_text' => __('Choose pickup point', 'pakkelabels-for-woocommerce')
            ));
        }
    }

    /*
     * Include modal box in footer when on the checkout page
	 * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.2.0
	 */
    public function includeModalHTML() {
        if($this->isCheckout()) {
	        $settings = get_option('shipmondo_settings');
	        $option = empty($settings['shipmondo_pickup_point_selection_type']) ? 'modal' : $settings['shipmondo_pickup_point_selection_type'];
        	if($option == 'modal') {
		        Plugin::getTemplate('pickup-point-selection.modal.modal');
	        }
        }
    }

    /*
     * Check if is checkout and not payment page and order recieved page
     */
    private function isCheckout() {
    	return is_checkout() && !is_wc_endpoint_url('order-received') && !is_wc_endpoint_url('order-pay');
    }

    /*
     * Return HTML for pickuppoint selection
     * @author Morning Train - Martin Schadegg Brønniche <ms@morningtrain.dk>
	 * @since 1.2.0
     */
    public function getPickupPointsSelectionHTML() {
        $agent = $_POST['agent'];
        $zipcode = $_POST['zipcode'];
        $country = $_POST['country'];
        $selection_type = $_POST['selection_type'];

        $pickup_points = ShipmondoAPI::getPickupPoints($agent, $zipcode, $country);

        if (is_wp_error($pickup_points)) {
            wp_send_json_error(array(
                'error' => 'shipmondo_api_error',
                'html' => Plugin::getTemplate('pickup-point-selection.' . $selection_type . '.error', array(
                    'error' => __('Something went wrong, please try again!', 'pakkelabels-for-woocommerce')
                ), false)
            ));
        } elseif (!empty($pickup_points)) {
            Plugin::getTemplate('pickup-point-selection.' . $selection_type . '.content', array('pickup_points' => $pickup_points, 'pickup_points_number' => count($pickup_points), 'agent' => $agent));
        } else {
            Plugin::getTemplate('pickup-point-selection.' . $selection_type . '.error', array(
                'error' => __('No pickup points found, please try another zipcode!', 'pakkelabels-for-woocommerce')
            ));
        }

        die();
    }

	/**
	 * Get current selection
	 *
	 * @param      $field_name
	 * @param null $agent
	 *
	 * @return string
	 */
    public static function getCurrentSelection($field_name, $shipping_method, $index = 0, $default = '') {
    	$current_selection = WC()->session->get('shipmondo_current_selection', array());

    	if(!isset($current_selection[$index]) || !static::isCurrentSelection($shipping_method, $index)) {
    		return $default;
	    }

    	if(isset($current_selection[$index]['selection'][$field_name])) {
    		return $current_selection[$index]['selection'][$field_name];
	    }

    	if($field_name == 'zip_city') {
    		$parts = array(
			    static::getCurrentSelection('zip', $shipping_method, $index),
			    static::getCurrentSelection('city', $shipping_method, $index)
		    );
    		return implode(', ', $parts);
	    }

    	return '';
    }

	/**
	 * @param $shipping_method \WC_Shipping_Rate
	 *
	 * @return bool
	 */
    public static function isCurrentSelection($shipping_method, $index = 0) {
	    $current_selection = WC()->session->get('shipmondo_current_selection', array());
		$agent = is_a($shipping_method, 'WC_Shipping_Method') ? static::getAgent($shipping_method->method_id) : $shipping_method;

	    if( !isset($current_selection[$index]['agent']) ||
		    $current_selection[$index]['agent'] !== $agent ||
		    !isset($current_selection[$index]['selection'])) {
		    return false;
	    }

	    $required_fields = array(
	    	'id',
	        'name',
		    'address',
		    'zip',
		    'city',
		    'id_string'
	    );

	    foreach($required_fields as $field) {
	    	if(!isset($current_selection[$index]['selection'][$field])) {
	    		return false;
		    }
	    }

	    return true;
    }

	public static function getAgent($shipping_method_id) {
    	$shipping_methods = WC()->shipping()->get_shipping_methods();

    	$method = null;
    	foreach($shipping_methods as $shipping_method) {
    		if($shipping_method->id === $shipping_method_id) {
    			$method = $shipping_method;
		        break;
    		}
	    }

    	if(!empty($method) && isset($method->agent)) {
    		return $method->agent;
	    }

    	return $shipping_method_id;
	}
}