<?php
add_filter( 'woocommerce_admin_order_preview_get_order_details', 'admin_order_preview_add_custom_meta_data', 10, 2 );
function admin_order_preview_add_custom_meta_data( $data, $order ) {
   $data['item_html'] = get_order_preview_item_html($order);
    return $data;
}

function get_order_preview_item_html( $order ) {
      
        //////////////////// BHAVIN ///////////////////////////////////
        require_once(get_stylesheet_directory().'/milcom/soap-common-class.php');
        
        // require_once(get_template_directory_uri().'/inc/admin/milcom/ntlm2.php');
        $baseURL = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
        $client = new NTLMSoapClient($baseURL);
         
        global $wpdb;
    
        $milcomTableResult = $wpdb->get_results('SELECT * FROM milcom_mapping WHERE ( (webshop_column="external-field") OR (webshop_column="shipping-agent") )');
        $shippingAgentResult = $wpdb->get_results('SELECT DISTINCT shipping_agent_name FROM shipping_agent ORDER BY display_order ASC');
        
        $shippingAgentDropdown = '<select id="shippingAgentServiceCode" name="shippingAgentServiceCode">';
        
        $shippingAgentDropdown .= '<option value="" selected>Vælg forsendelse</option>';

        foreach($shippingAgentResult as $shippingAgent) {
             $shippingAgentDropdown  .= '<optgroup label="'.$shippingAgent->shipping_agent_name.'">';
                     $shippingAgentServiceCodeResult = $wpdb->get_results('SELECT * FROM shipping_agent WHERE shipping_agent_name="'.$shippingAgent->shipping_agent_name.'" ');
                     foreach($shippingAgentServiceCodeResult as $shippingAgentServiceCode) {
                         $shippingAgentDropdown .= '<option value="'.$shippingAgentServiceCode->shipping_agent_servicecode.'">'.$shippingAgentServiceCode->shipping_agent_servicecode.'</option>';
                     }
             $shippingAgentDropdown .= '</optgroup>';
        }
        $shippingAgentDropdown .= '</select>';
        //////////////////// BHAVIN ///////////////////////////////////
        
        $hidden_order_itemmeta = apply_filters(
            'woocommerce_hidden_order_itemmeta',
            array(
                '_qty',
                '_tax_class',
                '_product_id',
                '_variation_id',
                '_line_subtotal',
                '_line_subtotal_tax',
                '_line_total',
                '_line_tax',
                'method_id',
                'cost',
                '_reduced_stock',
            )
        );

        $line_items = apply_filters( 'woocommerce_admin_order_preview_line_items', $order->get_items(), $order );
        $columns    = apply_filters(
            'woocommerce_admin_order_preview_line_item_columns',
            array(
                'product'  => __( 'Product', 'woocommerce' ),
                'quantity' => __( 'Quantity', 'woocommerce' ),
                'tax'      => __( 'Tax', 'woocommerce' ),
                'total'    => __( 'Total', 'woocommerce' ),
            ),
            $order
        );

        if ( ! wc_tax_enabled() ) {
            unset( $columns['tax'] );
        }

        //////////////////// BHAVIN ///////////////////////////////////
        if(!empty($milcomTableResult) && count($milcomTableResult)>0){
            $html = '
                <table cellspacing="0" cellspadding="0" class="wc-order-preview-table wp-list-table widefat fixed striped posts" style="border:1px solid #eee">
                    <thead>';
                    $i=0;
                    foreach ($milcomTableResult as $key => $value) {
                        
                        $color = ($i%2==0) ? 'background-color:#f9f9f9' : '';

                        if('shipping-agent' == $value->webshop_column) {
                            $html .= '<tr style="'.$color.'"><td style="border-bottom:1px solid #eee"> <span style="color:red">*</span><span style="max-width:190px; margin-top:px; width:100%; float:left">'.$value->milcom_column.'</span>&nbsp;&nbsp; '.$shippingAgentDropdown.' </td></tr>';
                        } else {
                            $html .= '<tr style="'.$color.'"><td style="border-bottom:1px solid #eee"> <span style="color:red">*</span><span style="max-width:190px; margin-top:px; width:100%; float:left">'.$value->milcom_column.'</span> <input type="text" id="'.$value->milcom_column.'" name="'.$value->milcom_column.'"> </td></tr>';
                        }
                        $i++;
                    }
            $html .= '</thead>
                </table>';      
        }
        //////////////////// BHAVIN ///////////////////////////////////

        $html .= '
        <div class="wc-order-preview-table-wrapper">
            <table cellspacing="0" class="wc-order-preview-table">
                <thead>
                    <tr>';

        foreach ( $columns as $column => $label ) {
            $html .= '<th class="wc-order-preview-table__column--' . esc_attr( $column ) . '">' . esc_html( $label ) . '</th>';
        }

        $html .= '
                    </tr>
                </thead>
                <tbody>';

        $k=0;           
        foreach ( $line_items as $item_id => $item ) {

            $product_object = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
            $row_class = apply_filters( 'woocommerce_admin_html_order_preview_item_class', '', $item, $order );

            $html .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--' . esc_attr( $item_id ) . ( $row_class ? ' ' . esc_attr( $row_class ) : '' ) . '">';

            foreach ( $columns as $column => $label ) {
                $html .= '<td class="wc-order-preview-table__column--' . esc_attr( $column ) . '">';
                switch ( $column ) {
                    case 'product':
                        $html .= wp_kses_post( $item->get_name() );

                        if ( $product_object ) {
                            $html .= ' (' . esc_html( $product_object->get_sku() ) . ')';
                        }

                        //////////////////// BHAVIN ///////////////////////////////////
                        $milcomItemId = "milcom_item_".$item_id;

                        $ourParamsArray = array('items' => array('Item' => ''), 'no' => $product_object->get_sku() );
                        $response = $client->__soapCall('GetItems', array('parameters' => $ourParamsArray));

                        $milComeItem = array();
                        if(!empty($response->items->Item->no)) {
                            $milComeItem = $response->items->Item;
                            $isMilcomItem = "Yes";
                        } else {
                            $isMilcomItem = "No";
                        }

                        if($isMilcomItem == "Yes"){
                            $html .= '<div class="wc-order-item-sku" id="milcom_item_'.$item_id.'" style="text-align:left"> 
                                        <div> Lager = '.$milComeItem->inventory.' </div>
                                  </div>';

                            if((int) $milComeItem->inventory < (int) $item->get_quantity()){
                                $html .= '<div class="wc-order-item-sku" id="milcom_item_'.$item_id.'" style="text-align:left"> 
                                        <div style="color:red;"> 
                                            <strong> Der mangler varer på lager</strong>
                                        </div>
                                  </div>';
                            }     


                        } else {
                            $html .= '<div class="wc-order-item-sku" id="milcom_item_'.$item_id.'" style="text-align:left"> 
                                        <div style="color:red;"> 
                                            <strong>Varenr. er ikke oprettet hos Milcom</strong>
                                        </div>
                                  </div>';
                        }   
                        //////////////////// BHAVIN ///////////////////////////////////

                        $meta_data = $item->get_formatted_meta_data('');

                        if ( $meta_data ) {
                            $html .= '<table cellspacing="0" class="wc-order-item-meta">';

                            foreach ( $meta_data as $meta_id => $meta ) {
                                if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
                                    continue;
                                }
                                $html .= '<tr><th>' . wp_kses_post( $meta->display_key ) . ':</th><td>' . wp_kses_post( force_balance_tags( $meta->display_value ) ) . '</td></tr>';
                            }
                            $html .= '</table>';
                        }
                        break;
                    case 'quantity':
                        $html .= esc_html( $item->get_quantity() );
                        break;
                    case 'tax':
                        $html .= wc_price( $item->get_total_tax(), array( 'currency' => $order->get_currency() ) );
                        break;
                    case 'total':
                        $html .= wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
                        break;
                    default:
                        $html .= apply_filters( 'woocommerce_admin_order_preview_line_item_column_' . sanitize_key( $column ), '', $item, $item_id, $order );
                        break;
                }
                $html .= '</td>';
            }

            $html .= '</tr>';
            //$html .= '<tr><td> '.ABSPATH.' - '.$test.'  </td></tr>'; 

            $k++;
        }
        $html .= '
                </tbody>
            </table>
        </div>';

        return $html;
    }

if(isset($_GET['post_type']) && $_GET['post_type'] == 'shop_order'){
    add_action( 'admin_footer', 'order_preview_template');
}

function order_preview_template() {
        ?>
        <script type="text/template" id="tmpl-wc-modal-view-order">
            <?php $customOrderId = '{{ data.order_number }}'; ?>
            <form action="<?php echo get_site_url().'/milcom/milcom-place-order.php';?>" id="place_order_to_milcom" name="place_order_to_milcom" method="post" style="float:right; margin-bottom:0">

            <div class="wc-backbone-modal wc-order-preview">
                <div class="wc-backbone-modal-content">
                    
                    <section class="wc-backbone-modal-main" role="main">
                        <header class="wc-backbone-modal-header">
                            <mark class="order-status status-{{ data.status }}"><span>{{ data.status_name }}</span></mark>
                            <?php /* translators: %s: order ID */ ?>
                            <h1>Ordrenr. {{ data.order_number }}<?php //echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), '{{ data.order_number }}' ) ); ?></h1>
                            <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                                <span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
                            </button>
                        </header>

                        <article>
                            <?php do_action( 'woocommerce_admin_order_preview_start' ); ?>
                            <div class="wc-order-preview-addresses">
                                <div class="wc-order-preview-address">
                                    <h2><?php esc_html_e( 'Kundeoplysninger', 'woocommerce' ); ?></h2>
                                    {{{ data.formatted_billing_address }}}

                                    <# if ( data.data.billing.email ) { #>
                                        <strong><?php echo 'Email';//esc_html_e( 'Email', 'woocommerce' ); ?></strong>
                                        <a href="mailto:{{ data.data.billing.email }}">{{ data.data.billing.email }}</a>
                                    <# } #>

                                    <# if ( data.data.billing.phone ) { #>
                                        <strong><?php esc_html_e( 'Phone', 'woocommerce' ); ?></strong>
                                        <a href="tel:{{ data.data.billing.phone }}">{{ data.data.billing.phone }}</a>
                                    <# } #>

                                    <# if ( data.payment_via ) { #>
                                        <strong>Betaling med<?php //esc_html_e( 'Payment via', 'woocommerce' ); ?></strong>
                                        {{{ data.payment_via }}}
                                    <# } #>
                                </div>
                                <# if ( data.needs_shipping ) { #>
                                    <div class="wc-order-preview-address">
                                        <h2>Leveringsoplysninger <?php //esc_html_e( 'Shipping details', 'woocommerce' ); ?></h2>
                                        <?php /* ?>
                                        <# if ( data.ship_to_billing ) { #>
                                            {{{ data.formatted_billing_address }}}
                                        <# } else { #>
                                            <a href="{{ data.shipping_address_map_url }}" target="_blank">{{{ data.formatted_shipping_address }}}</a>
                                        <# } #> 
                                        <?php */ ?>
                                        
                                        <a href="{{ data.shipping_address_map_url }}" target="_blank">
                                            {{ data.data.shipping.company }} <br/>
                                            {{ data.data.shipping.address_1 }} <br/>
                                            {{ data.data.shipping.postcode }} {{ data.data.shipping.city }} 
                                        </a>

                                        <# if ( data.shipping_via ) { #>
                                            <strong><?php esc_html_e( 'Shipping method', 'woocommerce' ); ?></strong>
                                            {{ data.shipping_via }} <br/>
                                            {{ data.data.shipping.address_2 }}
                                        <# } #>
                                    </div>
                                <# } #>

                                <# if ( data.data.customer_note ) { #>
                                    <div class="wc-order-preview-note">
                                        <strong><?php esc_html_e( 'Note', 'woocommerce' ); ?></strong>
                                        {{ data.data.customer_note }}
                                    </div>
                                <# } #>
                            </div>

                            {{{ data.item_html }}}

                            <?php do_action( 'woocommerce_admin_order_preview_end' ); ?>
                        </article>
                        <footer>
                            <div class="inner">
                                {{{ data.actions_html }}}
                                <a class="button button-primary button-large" aria-label="<?php esc_attr_e( 'Edit this order', 'woocommerce' ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit' ) ); ?>&post={{ data.data.id }}">
                                    Rediger <?php //esc_html_e( 'Edit', 'woocommerce' ); ?></a>

                                    <input type="hidden" id="order_id" name="order_id" value="{{ data.order_number }}">
                                    <input class="button button-primary button-large" type="button" id="place_order_milcome" name="place_order_milcome" value="Send ordre til Milcom" onClick="place_order_milcome()">  
                            </div>

                            <div>
                        </footer>
                    </section>
                    
                </div>
            </div>
            <div class="wc-backbone-modal-backdrop modal-close"></div>
            </form>
            <script> 
                jQuery("#place_order_milcome").on('click', function () {
                        var code = jQuery("#shippingAgentServiceCode").val();
                        if(code == ""){
                            alert("Husk at vælge forsendelse");
                        } else {
                            jQuery("#place_order_to_milcom").submit();
                        }

                });
            </script>
        </script>
        <?php
    }


// SOFTINFORM: Added new code
// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
// Added new column 'Milcom Status' into order list
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __( 'order','woocommerce');
            // $reordered_columns['my-column1'] = __( 'Milcom Status','theme_domain');
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
// Added values of new column 'Milcom Status' into order list
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'my-column1' :
            require_once(get_stylesheet_directory().'/milcom/milcom_order_status.php');
            require_once(get_stylesheet_directory().'/milcom/soap-common-class.php');
            $objMilcomOrder = new Milcom_Order_Table();

            // Get custom post meta data
            $my_var_one = get_post_meta( $post_id, '_the_meta_key1', true );
            if(!empty($my_var_one)){
                echo $my_var_one;
            } else {
                // $isMilcomItem = "No";
                // // SOAP
                // $baseURL = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
                // $client = new NTLMSoapClient($baseURL);

                // $order_id = $post_id;
                // $order = wc_get_order( $order_id );
                // $order_data = $order->get_data();
                // $isMilcomItem = '';
                // if(!empty($order->get_items())){
                //     foreach ($order->get_items() as $item_key => $item ) {
                //         $product = $item->get_product();
                //         $product_sku    = $product->get_sku();

                //         // SOAP
                //         $ourParamsArray = array('items' => array('Item' => ''), 'no' => $product_sku );
                //         $response = $client->__soapCall('GetItems', array('parameters' => $ourParamsArray));

                //         $milComeItem = array();
                //         if(!empty($response->items->Item->no)) {
                //             $isMilcomItem = "Yes";
                //         } else {
                //             $isMilcomItem = "No";
                //             break;
                //         }

                //     }
                // }
                
                // if($isMilcomItem == "No"){
                //     echo "<span style='color:red'> Mangler varenr. <!-- Item no missing --> </span>";
                // } else {
                //     echo $objMilcomOrder->getMilcomeOrderStatus($post_id);
                // }

                echo $objMilcomOrder->getMilcomeOrderStatus($post_id);
                
            }    
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

// Below is creating New Admin menu 'Milcom mapping'
function my_admin_menu() 
{
    add_menu_page('Milcom mapping','Milcom mapping','manage_options','std-regd','registration_callback','dashicons-welcome-write-blog',98);
    //add_submenu_page('std-regd','Event Registration', 'Event Registration', 'manage_options', 'std-regd','registration_callback');
    //add_submenu_page('std-regd','Course Registration', 'Course Registration', 'manage_options', 'course-registration','course_reg_callback'); 
}
add_action('admin_menu','my_admin_menu');

// Below is display the page content of Milcom Mapping
// registration_callback() called into my_admin_menu()
// Added template directory into this registration callback
function registration_callback()
{
    require_once(get_stylesheet_directory().'/milcom/milcom_mapping.php');
    require_once(get_stylesheet_directory().'/milcom/insertMappingData.php');

}