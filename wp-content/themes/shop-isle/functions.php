<?php
/**
 * Main functions file
 *
 * @package WordPress
 * @subpackage Shop Isle
 */
$vendor_file = trailingslashit( get_template_directory() ) . 'vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

if ( ! defined( 'WPFORMS_SHAREASALE_ID' ) ) {
	define( 'WPFORMS_SHAREASALE_ID', '848264' );
}

add_filter( 'themeisle_sdk_products', 'shopisle_load_sdk' );
/**
 * Loads products array.
 *
 * @param array $products All products.
 *
 * @return array Products array.
 */
function shopisle_load_sdk( $products ) {
	$products[] = get_template_directory() . '/style.css';

	return $products;
}
/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';

/**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */



// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __( 'Milcom Item (Qty)','theme_domain');
            $reordered_columns['my-column2'] = __( 'Milcom Order','theme_domain');
        }
    }
    return $reordered_columns;
}

function wc_new_order_column( $columns ) {
	$columns['approve_btn_column'] = 'Approve';
	return $columns;
}
add_filter( 'manage_edit-shop_order_columns', 'wc_new_order_column' );

// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
	require_once(get_template_directory().'/inc/admin/milcom_order_api.php');
	$objMilcomOrder = new Milcom_Order_Table();

    switch ( $column )
    {
        case 'my-column1' :
            // Get custom post meta data
            $my_var_one = get_post_meta( $post_id, '_the_meta_key1', true );
            if(!empty($my_var_one))
                echo $my_var_one;

            // Testing (to be removed) - Empty value case
            else
                echo '<small>(<em>no value - '.$post_id.'</em>)</small>';

            break;

        case 'my-column2' :
            // Get custom post meta data
            $my_var_two = get_post_meta( $post_id, '_the_meta_key2', true );
            if(!empty($my_var_two))
                echo $my_var_two;

            // Testing (to be removed) - Empty value case
            else
                echo '<small>(<em>no value - '.$post_id.' </em>)</small>';

            break;

        case 'approve_btn_column' :
            // Get custom post meta data
            $my_var_three = get_post_meta( $post_id, '_the_meta_key3', true );
            if(!empty($my_var_three))
                echo $my_var_three;

            // Testing (to be removed) - Empty value case
            else
            	echo $objMilcomOrder->getMilcomeOrderStatus($post_id);
                //echo '<mark class="order-status status-processing tips"><span>Approve '.get_theme_file_uri().' </span></mark>';

            break;    
    }
}

