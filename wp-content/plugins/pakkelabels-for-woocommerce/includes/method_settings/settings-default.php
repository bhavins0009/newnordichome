<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// $this->instance_form_fields = include( 'settings/settings-default.php' );

/**
 * Settings for flat rate shipping.
 */
$settings_default = array(
	'hidden_post_field' => array(
		'type' 			=> 'hidden',
		'class'         => 'hidden_post_field',
	),
	'title' => array(
		'title' 		=> __( 'Method Name', 'pakkelabels-for-woocommerce'),
		'type' 			=> 'text',
		'description' 	=> __( 'This controls the title which customer will be presented for during checkout.', 'pakkelabels-for-woocommerce' ),
		'default'		=> $this->method_title,
		'desc_tip'		=> true
	),
	'tax_status' => array(
		'title' 		=> __( 'Tax Status', 'pakkelabels-for-woocommerce' ),
		'type' 			=> 'select',
		'class'         => 'wc-enhanced-select',
		'default' 		=> 'taxable',
		'options'		=> array(
			'taxable' 	=> __( 'Taxable', 'pakkelabels-for-woocommerce' ),
			'none' 		=> _x( 'None', 'Tax status', 'pakkelabels-for-woocommerce' )
		)
	),
	'differentiated_price_type' => array(
		'title' 		=> __( 'Differentiated Price Type', 'pakkelabels-for-woocommerce'),
		'type' 			=> 'select',
		'description' 	=> __( 'Choose what the shipping price is based on', 'pakkelabels-for-woocommerce' ),
		'default' 		=> 'Normal',
		'class'         => 'differentiated_price_type',
		'options'		=> array(
			'Quantity' 		=> __( 'Normal', 'pakkelabels-for-woocommerce'),
			'Weight' 	    => __( 'Weight', 'pakkelabels-for-woocommerce'),
			'Price' 	    => __( 'Price', 'pakkelabels-for-woocommerce'),
		),
	),
	'shipping_price' => array(
		'title' 		=> __( 'Shipping Price', 'pakkelabels-for-woocommerce'),
		'type' 			=> 'text',
		'description' 	=> __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce' ),
		'class'         => 'shipping_price',
		'default'		=> 0,
		'desc_tip'		=> true
	),
	'hide_shipping_method_if_outside_parameters' => array(
		'title'         => __( 'Hide if outside conditions', 'pakkelabels-for-woocommerce'),
		'type'          => 'checkbox',
		'class'         => 'hide_shipping_method_if_outside_parameters',
		'description'   => __( 'Mark this, to hide this shipping method, if the conditions under Differentiated Price Type not is fulfilled.', 'pakkelabels-for-woocommerce' ),
		'label'			=> ' ',
		'default'       => 0,
		'desc_tip'      => true
	),
	'enable_free_shipping' => array(
		'title'         => __( 'Enable Free Shipping', 'pakkelabels-for-woocommerce'),
		'type'          => 'select',
		'class'         =>  'enable_free_shipping',
		'default'       => 'taxable',
		'options'       => array(
			'No'        => __( 'No', 'pakkelabels-for-woocommerce'),
			'Yes'       => __( 'Yes', 'pakkelabels-for-woocommerce'),
		),
	),
	'free_shipping_total' => array(
		'title'         => __( 'Minimum Purchase For Free Shipping', 'pakkelabels-for-woocommerce'),
		'type'          => 'text',
		'class'         => 'free_shipping_total',
		'description'   => __( 'This control the minimum amount the customer will have to purchase (subtotal) for to get free shipping. <br/><br/><strong>This rule will overrule any differentiated price ranges if the condition is met.</strong>', 'pakkelabels-for-woocommerce' ),
		'default'       => 0,
		'desc_tip'      => true
	),
	'enable_free_shipping_with_coupon' => array(
		'title'         => __( 'Free shipping when a shipping coupon is used', 'pakkelabels-for-woocommerce'),
		'type'          => 'checkbox',
		'class'         => 'enable_free_shipping_with_coupon',
		'description'   => __( 'Check this to enable customers to enabled free shipping for this shipping method, when a shipping coupon is used.', 'pakkelabels-for-woocommerce' ),
		'label'			=> ' ',
		'default'       => 0,
		'desc_tip'      => true
	),
);

$cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce' );

$shipping_classes = WC()->shipping->get_shipping_classes();

if ( ! empty( $shipping_classes ) ) {
    $settings_default['class_costs'] = array(
        'title'       => __( 'Shipping class costs', 'woocommerce' ),
        'type'        => 'title',
        'default'     => '',
        /* translators: %s: URL for link. */
        'description' => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
    );
    foreach ( $shipping_classes as $shipping_class ) {
        if ( ! isset( $shipping_class->term_id ) ) {
            continue;
        }
        $settings_default[ 'class_cost_' . $shipping_class->term_id ] = array(
            /* translators: %s: shipping class name */
            'title'             => sprintf( __( '"%s" shipping class cost', 'woocommerce' ), esc_html( $shipping_class->name ) ),
            'type'              => 'text',
            'placeholder'       => __( 'N/A', 'woocommerce' ),
            'description'       => $cost_desc,
            'default'           => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names.
            'desc_tip'          => true,
            'sanitize_callback' => array( $this, 'sanitize_cost' ),
        );
    }

    $settings_default['no_class_cost'] = array(
        'title'             => __( 'No shipping class cost', 'woocommerce' ),
        'type'              => 'text',
        'placeholder'       => __( 'N/A', 'woocommerce' ),
        'description'       => $cost_desc,
        'default'           => '',
        'desc_tip'          => true,
        'sanitize_callback' => array( $this, 'sanitize_cost' ),
    );

    $settings_default['type'] = array(
        'title'   => __( 'Calculation type', 'woocommerce' ),
        'type'    => 'select',
        'class'   => 'wc-enhanced-select',
        'default' => 'class',
        'options' => array(
            'class' => __( 'Per class: Charge shipping for each shipping class individually', 'woocommerce' ),
            'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'woocommerce' ),
        ),
    );
}

return $settings_default;
// global $woocommerce;
