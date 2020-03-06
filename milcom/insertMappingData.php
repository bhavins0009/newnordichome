<?php 
	define('WP_USE_THEMES', false);
	require('../wp-load.php');
	global $wpdb;

	$items = $_POST;
	$wpdb->query('TRUNCATE TABLE milcom_mapping');

	foreach($items as $milcomColumn => $webshopColumnn) {
		$sql = $wpdb->insert("milcom_mapping", array('milcom_column' => $milcomColumn, 'webshop_column' => $webshopColumnn));
	}
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>