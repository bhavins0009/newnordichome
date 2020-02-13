<?php
define('WP_USE_THEMES', false);
require('../../../../../wp-load.php');

global $wpdb;
// $wpdb->prepare("UPDATE `clk_42491aa6f3_wp_wc_order_stats` SET is_milcom_approved='Yes' 
// 	WHERE order_id=".$_REQUEST['orderId']." ");

$wpdb->query("UPDATE `clk_42491aa6f3_wp_wc_order_stats` SET is_milcom_approved='Yes' WHERE order_id=".$_REQUEST['orderId']." ");
header('Location: ' . $_SERVER['HTTP_REFERER']);