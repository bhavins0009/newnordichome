<?php 
	define('WP_USE_THEMES', false);
	//require('../wp-load.php');
	global $wpdb;
	if(empty($_POST['save']))
		return;
	$items = $_POST;

	$milcomTableResult = $wpdb->get_results('SELECT * FROM milcom_mapping'); 
	
	$wpdb->query('TRUNCATE TABLE milcom_mapping');

	foreach($items as $milcomColumn => $webshopColumnn) {
		$sql = $wpdb->insert("milcom_mapping", array('milcom_column' => $milcomColumn, 'webshop_column' => $webshopColumnn));
	}
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>