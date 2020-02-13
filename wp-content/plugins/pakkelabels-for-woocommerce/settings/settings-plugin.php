<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

//created with http://wpsettingsapi.jeroensormani.com/

add_action( 'admin_menu', 'shipmondo_add_admin_menu' );
add_action( 'admin_init', 'shipmondo_settings_init' );


function shipmondo_add_admin_menu(  )
{
    add_submenu_page('woocommerce', __('Shipmondo', 'woocommerce-shipmondo'), __('Shipmondo', 'woocommerce-shipmondo'), 'manage_options', 'shipmondo', 'shipmondo_options_page');
}


function shipmondo_settings_init(  ) {

    register_setting( 'ShipmondoPluginPage', 'shipmondo_settings' );

    add_settings_section(
        'shipmondo_pluginPage_section',
        __('Shipmondo shipping module settings', 'pakkelabels-for-woocommerce'),
        'shipmondo_settings_section_callback',
        'ShipmondoPluginPage'
    );

    add_settings_field(
        'shipmondo_text_field_0',
        __('Shipping Module key:', 'pakkelabels-for-woocommerce'),
        'shipmondo_text_field_0_render',
        'ShipmondoPluginPage',
        'shipmondo_pluginPage_section'
    );

    add_settings_field(
        'shipmondo_google_api_key',
        __('Google Maps API key:', 'pakkelabels-for-woocommerce'),
        'shipmondo_google_api_key',
        'ShipmondoPluginPage',
        'shipmondo_pluginPage_section'
    );

    add_settings_field(
        'shipmondo_pickup_point_selection_type',
	    __('Show Pickup Points in:', 'pakkelabels-for-woocommerce'),
	    'shipmondo_pickup_point_selection_type',
	    'ShipmondoPluginPage',
	    'shipmondo_pluginPage_section'
    );


}


function shipmondo_pickup_point_selection_type() {
	$settings = get_option('shipmondo_settings');
	$option = empty($settings['shipmondo_pickup_point_selection_type']) ? 'modal' : $settings['shipmondo_pickup_point_selection_type'];

	\ShipmondoForWooCommerce\Plugin\Plugin::getTemplate('settings.fields.select', array(
		'name' => 'shipmondo_settings[shipmondo_pickup_point_selection_type]',
		'value' => $option,
		'options' => array(
			array(
				'title' => __('Modal', 'pakkelabels-for-woocommerce'),
				'value' => 'modal',
			),
			array(
				'title' => __('Drop Down', 'pakkelabels-for-woocommerce'),
				'value' => 'dropdown',
			)
		)
	));
}

function shipmondo_text_field_0_render(  )
{
    $options = get_option( 'shipmondo_settings' );
    ?>
    <input type='text' name='shipmondo_settings[shipmondo_text_field_0]' value='<?php echo $options['shipmondo_text_field_0']; ?>' style="width: 400px; max-width: 100%;">
    <?php
}

function shipmondo_google_api_key(  )
{
    $options = get_option( 'shipmondo_settings' );
    ?>
    <input type='text' name='shipmondo_settings[shipmondo_google_api_key]' value='<?php echo $options['shipmondo_google_api_key']; ?>' style="width: 400px; max-width: 100%;">
    <?php
}


function shipmondo_settings_section_callback(  ) {

    echo __('Generate a shipping module key - click <a target="_blank" href="https://kundecenter.pakkelabels.dk/integration/fragtmodul/opret-en-fragtmodul-nogle">here</a> ', 'pakkelabels-for-woocommerce') . '</br>';
    echo __('Generate a personal Google Maps API key - click <a target="_blank" href="https://kundecenter.pakkelabels.dk/integration/fragtmodul/opret-en-gratis-google-api-key">here</a>', 'pakkelabels-for-woocommerce');

}


function shipmondo_options_page(  ) {

    ?>
    <form action='options.php' method='post'>
        <?php
        settings_fields( 'ShipmondoPluginPage' );
        do_settings_sections( 'ShipmondoPluginPage' );
        submit_button();
        ?>

    </form>
    <?php

}


?>