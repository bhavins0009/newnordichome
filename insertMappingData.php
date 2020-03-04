<?php
//session_start();
/*
Template Name: Insert;
*/
?>
<?php 
 define('WP_USE_THEMES', false);
 require('./wp-load.php');
?>

<?php
	    echo '<pre>';
		print_r($_POST);
		exit;

		global $wpdb;
		$sql = $wpdb->insert("milcom_approve", array('milcom_column' => $customerId, 'webshop_column' => $userId));
	
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>



