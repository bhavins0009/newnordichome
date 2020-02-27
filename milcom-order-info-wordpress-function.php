<?php
$order_id = $_POST['place_order_milcome'];

// Get an instance of the WC_Order object
$order = wc_get_order( $order_id );
$order_data = $order->get_data();


$order_id = $_POST['place_order_milcome'];

// Get an instance of the WC_Order object
$order = wc_get_order( $order_id );

$order_data = $order->get_data(); // The Order data

$order_id = $order_data['id'];
$order_parent_id = $order_data['parent_id'];
$order_status = $order_data['status'];
$order_currency = $order_data['currency'];
$order_version = $order_data['version'];
$order_payment_method = $order_data['payment_method'];
$order_payment_method_title = $order_data['payment_method_title'];
$order_payment_method = $order_data['payment_method'];
$order_payment_method = $order_data['payment_method'];

## Creation and modified WC_DateTime Object date string ##

// Using a formated date ( with php date() function as method)
$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');

// Using a timestamp ( with php getTimestamp() function as method)
$order_timestamp_created = $order_data['date_created']->getTimestamp();
$order_timestamp_modified = $order_data['date_modified']->getTimestamp();

$order_discount_total = $order_data['discount_total'];
$order_discount_tax = $order_data['discount_tax'];
$order_shipping_total = $order_data['shipping_total'];
$order_shipping_tax = $order_data['shipping_tax'];
$order_total = $order_data['cart_tax'];
$order_total_tax = $order_data['total_tax'];
$order_customer_id = $order_data['customer_id']; // ... and so on

## BILLING INFORMATION:

$order_billing_first_name = $order_data['billing']['first_name'];
$order_billing_last_name = $order_data['billing']['last_name'];
$order_billing_company = $order_data['billing']['company'];
$order_billing_address_1 = $order_data['billing']['address_1'];
$order_billing_address_2 = $order_data['billing']['address_2'];
$order_billing_city = $order_data['billing']['city'];
$order_billing_state = $order_data['billing']['state'];
$order_billing_postcode = $order_data['billing']['postcode'];
$order_billing_country = $order_data['billing']['country'];
$order_billing_email = $order_data['billing']['email'];
$order_billing_phone = $order_data['billing']['phone'];

## SHIPPING INFORMATION:

$order_shipping_first_name = $order_data['shipping']['first_name'];
$order_shipping_last_name = $order_data['shipping']['last_name'];
$order_shipping_company = $order_data['shipping']['company'];
$order_shipping_address_1 = $order_data['shipping']['address_1'];
$order_shipping_address_2 = $order_data['shipping']['address_2'];
$order_shipping_city = $order_data['shipping']['city'];
$order_shipping_state = $order_data['shipping']['state'];
$order_shipping_postcode = $order_data['shipping']['postcode'];
$order_shipping_country = $order_data['shipping']['country'];



// Get an instance of the WC_Order object
$order = wc_get_order($order_id);

//print_r($order->get_items());

// Iterating through each WC_Order_Item_Product objects
foreach ($order->get_items() as $item_key => $item ):

    ## Using WC_Order_Item methods ##

    // Item ID is directly accessible from the $item_key in the foreach loop or
    $item_id = $item->get_id();

    ## Using WC_Order_Item_Product methods ##

    $product      = $item->get_product(); // Get the WC_Product object

    $product_id   = $item->get_product_id(); // the Product id
    $variation_id = $item->get_variation_id(); // the Variation id

    $item_type    = $item->get_type(); // Type of the order item ("line_item")

    $item_name    = $item->get_name(); // Name of the product
    $quantity     = $item->get_quantity();  
    $tax_class    = $item->get_tax_class();
    $line_subtotal     = $item->get_subtotal(); // Line subtotal (non discounted)
    $line_subtotal_tax = $item->get_subtotal_tax(); // Line subtotal tax (non discounted)
    $line_total        = $item->get_total(); // Line total (discounted)
    $line_total_tax    = $item->get_total_tax(); // Line total tax (discounted)

    ## Access Order Items data properties (in an array of values) ##
    $item_data    = $item->get_data();

    $product_name = $item_data['name'];
    $product_id   = $item_data['product_id'];
    $variation_id = $item_data['variation_id'];
    $quantity     = $item_data['quantity'];
    $tax_class    = $item_data['tax_class'];
    $line_subtotal     = $item_data['subtotal'];
    $line_subtotal_tax = $item_data['subtotal_tax'];
    $line_total        = $item_data['total'];
    $line_total_tax    = $item_data['total_tax'];

    // Get data from The WC_product object using methods (examples)
    $product        = $item->get_product(); // Get the WC_Product object

    $product_type   = $product->get_type();
    $product_sku    = $product->get_sku();
    $product_price  = $product->get_price();
    $stock_quantity = $product->get_stock_quantity();

endforeach;



function get_order_details($order_id){

    // 1) Get the Order object
    $order = wc_get_order( $order_id );

    // OUTPUT
    echo '<h3>RAW OUTPUT OF THE ORDER OBJECT: </h3>';
    print_r($order);
    echo '<br><br>';
    echo '<h3>THE ORDER OBJECT (Using the object syntax notation):</h3>';
    echo '$order->order_type: ' . $order->order_type . '<br>';
    echo '$order->id: ' . $order->id . '<br>';
    echo '<h4>THE POST OBJECT:</h4>';
    echo '$order->post->ID: ' . $order->post->ID . '<br>';
    echo '$order->post->post_author: ' . $order->post->post_author . '<br>';
    echo '$order->post->post_date: ' . $order->post->post_date . '<br>';
    echo '$order->post->post_date_gmt: ' . $order->post->post_date_gmt . '<br>';
    echo '$order->post->post_content: ' . $order->post->post_content . '<br>';
    echo '$order->post->post_title: ' . $order->post->post_title . '<br>';
    echo '$order->post->post_excerpt: ' . $order->post->post_excerpt . '<br>';
    echo '$order->post->post_status: ' . $order->post->post_status . '<br>';
    echo '$order->post->comment_status: ' . $order->post->comment_status . '<br>';
    echo '$order->post->ping_status: ' . $order->post->ping_status . '<br>';
    echo '$order->post->post_password: ' . $order->post->post_password . '<br>';
    echo '$order->post->post_name: ' . $order->post->post_name . '<br>';
    echo '$order->post->to_ping: ' . $order->post->to_ping . '<br>';
    echo '$order->post->pinged: ' . $order->post->pinged . '<br>';
    echo '$order->post->post_modified: ' . $order->post->post_modified . '<br>';
    echo '$order->post->post_modified_gtm: ' . $order->post->post_modified_gtm . '<br>';
    echo '$order->post->post_content_filtered: ' . $order->post->post_content_filtered . '<br>';
    echo '$order->post->post_parent: ' . $order->post->post_parent . '<br>';
    echo '$order->post->guid: ' . $order->post->guid . '<br>';
    echo '$order->post->menu_order: ' . $order->post->menu_order . '<br>';
    echo '$order->post->post_type: ' . $order->post->post_type . '<br>';
    echo '$order->post->post_mime_type: ' . $order->post->post_mime_type . '<br>';
    echo '$order->post->comment_count: ' . $order->post->comment_count . '<br>';
    echo '$order->post->filter: ' . $order->post->filter . '<br>';
    echo '<h4>THE ORDER OBJECT (again):</h4>';
    echo '$order->order_date: ' . $order->order_date . '<br>';
    echo '$order->modified_date: ' . $order->modified_date . '<br>';
    echo '$order->customer_message: ' . $order->customer_message . '<br>';
    echo '$order->customer_note: ' . $order->customer_note . '<br>';
    echo '$order->post_status: ' . $order->post_status . '<br>';
    echo '$order->prices_include_tax: ' . $order->prices_include_tax . '<br>';
    echo '$order->tax_display_cart: ' . $order->tax_display_cart . '<br>';
    echo '$order->display_totals_ex_tax: ' . $order->display_totals_ex_tax . '<br>';
    echo '$order->display_cart_ex_tax: ' . $order->display_cart_ex_tax . '<br>';
    echo '$order->formatted_billing_address->protected: ' . $order->formatted_billing_address->protected . '<br>';
    echo '$order->formatted_shipping_address->protected: ' . $order->formatted_shipping_address->protected . '<br><br>';
    echo '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br><br>';

    // 2) Get the Order meta data
    $order_meta = get_post_meta($order_id);

    echo '<h3>RAW OUTPUT OF THE ORDER META DATA (ARRAY): </h3>';
    print_r($order_meta);
    echo '<br><br>';
    echo '<h3>THE ORDER META DATA (Using the array syntax notation):</h3>';
    echo '$order_meta[_order_key][0]: ' . $order_meta[_order_key][0] . '<br>';
    echo '$order_meta[_order_currency][0]: ' . $order_meta[_order_currency][0] . '<br>';
    echo '$order_meta[_prices_include_tax][0]: ' . $order_meta[_prices_include_tax][0] . '<br>';
    echo '$order_meta[_customer_user][0]: ' . $order_meta[_customer_user][0] . '<br>';
    echo '$order_meta[_billing_first_name][0]: ' . $order_meta[_billing_first_name][0] . '<br><br>';
    echo 'And so on ……… <br><br>';
    echo '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br><br>';

    // 3) Get the order items
    $items = $order->get_items();

    echo '<h3>RAW OUTPUT OF THE ORDER ITEMS DATA (ARRAY): </h3>';

    foreach ( $items as $item_id => $item_data ) {

        echo '<h4>RAW OUTPUT OF THE ORDER ITEM NUMBER: '. $item_id .'): </h4>';
        print_r($item_data);
        echo '<br><br>';
        echo 'Item ID: ' . $item_id. '<br>';
        echo '$item_data["product_id"] <i>(product ID)</i>: ' . $item_data['product_id'] . '<br>';
        echo '$item_data["name"] <i>(product Name)</i>: ' . $item_data['name'] . '<br>';

        // Using get_item_meta() method
        echo 'Item quantity <i>(product quantity)</i>: ' . $order->get_item_meta($item_id, '_qty', true) . '<br><br>';
        echo 'Item line total <i>(product quantity)</i>: ' . $order->get_item_meta($item_id, '_line_total', true) . '<br><br>';
        echo 'And so on ……… <br><br>';
        echo '- - - - - - - - - - - - - <br><br>';
    }
    echo '- - - - - - E N D - - - - - <br><br>';
}

//get_order_details($order_id);
