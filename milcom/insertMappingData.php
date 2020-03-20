<?php 
	define('WP_USE_THEMES', false);
	require('../wp-load.php');
	global $wpdb;

	

$shippingAgentCommonServiceCodeResult = $wpdb->get_results('SELECT shipping_agent_servicecode FROM shipping_agent WHERE shipping_agent_desc="gls pakkeshop" ');

	if(count($shippingAgentCommonServiceCodeResult) > 0) {
            $commonCode = $shippingAgentCommonServiceCodeResult[0]->shipping_agent_servicecode;
       } else {
            $commonCode = "";
       }
       exit($commonCode);

	$items = $_POST;

	$wpdb->query('TRUNCATE TABLE milcom_mapping');

	foreach($items as $milcomColumn => $webshopColumnn) {
		$sql = $wpdb->insert("milcom_mapping", array('milcom_column' => $milcomColumn, 'webshop_column' => $webshopColumnn));
	}
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>