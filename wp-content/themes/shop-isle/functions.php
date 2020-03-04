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

// SOFTINFORM: Added new code
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
            $reordered_columns['my-column1'] = __( 'Milcom Status','theme_domain');
            // $reordered_columns['my-column2'] = __( 'Milcom Order Status','theme_domain');
        }
    }
    return $reordered_columns;
}

/*
function wc_new_order_column( $columns ) {
	$columns['approve_btn_column'] = 'Approve';
	return $columns;
}
add_filter( 'manage_edit-shop_order_columns', 'wc_new_order_column' );
*/

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
                echo '<span style="color:red">Item no missing</span>';//$objMilcomOrder->getMilcomeOrderStatus($post_id);

            break;

        case 'my-column2' :
            // Get custom post meta data
            $my_var_two = get_post_meta( $post_id, '_the_meta_key2', true );
            if(!empty($my_var_two))
                echo $my_var_two;

            // Testing (to be removed) - Empty value case
            else
                echo 'Second Column';
            break;

        case 'approve_btn_column' :
            
            // Get custom post meta data
            $my_var_three = get_post_meta( $post_id, '_the_meta_key3', true );
            if(!empty($my_var_three))
                echo $my_var_three;

            // Testing (to be removed) - Empty value case
            else
            	echo 'Second Column';
                //echo '<mark class="order-status status-processing tips"><span>Approve '.get_theme_file_uri().' </span></mark>';
            break;    
    }
}

/**
 * Enqueue a script in the WordPress admin, excluding edit.php.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function Milcom_widget_enqueue_script() {   
    wp_enqueue_script( 'my_custom_script', get_site_url() . '/js/milcom-order-ajax.js', array('jquery'), '1.0' );
}
add_action('admin_enqueue_scripts', 'Milcom_widget_enqueue_script');

 function my_admin_menu() 
{
    add_menu_page('Milcom mapping','Milcom mapping','manage_options','std-regd','registration_callback','dashicons-welcome-write-blog',98);
    //add_submenu_page('std-regd','Event Registration', 'Event Registration', 'manage_options', 'std-regd','registration_callback');
    //add_submenu_page('std-regd','Course Registration', 'Course Registration', 'manage_options', 'course-registration','course_reg_callback'); 
}
add_action('admin_menu','my_admin_menu');

function registration_callback()
{
 require_once(get_template_directory().'/inc/admin/milcom_mapping.php');
}

