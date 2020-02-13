<?php
	/**
	 * @var $shipping_method WC_Shipping_Rate
	 * @var $index string
	 * @var $agent string
	 */
?>
<div class="shipmondo-shipping-field-wrap" data-shipping_agent="<?php echo $agent ?>" data-shipping_index="<?php echo $index; ?>">
    <div class="shipmondo-clearfix" id="shipmondo_shipping_button">
        <div class="shipmondo_stores">
            <div>
                <input type="text" id="shipmondo_zipcode_field_<?php echo $index; ?>" name="shipmondo_zipcode[<?php echo $index; ?>]" class="input shipmondo_zipcode" placeholder="<?php echo __('Zipcode', 'pakkelabels-for-woocommerce'); ?>" data-current="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('zip', $shipping_method, $index); ?>">
                <script>
                    jQuery(document).trigger('shipmondo_zipcode_field_loaded');
                </script>
            </div>
            <div>
                <input disabled type="button" id="shipmondo_find_shop_btn_<?php echo $index; ?>" name="shipmondo_find_shop[<?php echo $index; ?>]" class="button alt shipmondo_select_button" value="<?php echo __('Find nearest pickup point', 'pakkelabels-for-woocommerce' ); ?>" data-shipping-method="<?php echo $shipping_method->method_id; ?>" data-selection-type="modal">
            </div>
        </div>
    </div>
	<div class="hidden_chosen_shop">
		<input type="hidden" name="shipmondo[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('id', $shipping_method, $index); ?>">
		<input type="hidden" name="shop_name[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('name', $shipping_method, $index); ?>">
		<input type="hidden" name="shop_address[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('address', $shipping_method, $index); ?>">
		<input type="hidden" name="shop_zip[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('zip', $shipping_method, $index); ?>">
		<input type="hidden" name="shop_city[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('city', $shipping_method, $index); ?>">
		<input type="hidden" name="shop_ID[<?php echo $index; ?>]" value="<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('id_string', $shipping_method, $index); ?>">
	</div>
	<div class="selected_shop_context shipmondo-clearfix<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::isCurrentSelection($shipping_method, $index) ? ' active' : ''; ?>">
		<div class="shipmondo-shop-header"><?php echo __('Currently choosen pickup point:', 'pakkelabels-for-woocommerce'); ?></div>
		<div class="shipmondo-shop-name"><?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('name', $shipping_method, $index); ?></div>
		<div class="shipmondo-shop-address"><?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('address', $shipping_method, $index); ?></div>
		<div class="shipmondo-shop-zip-and-city"><?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('zip_city', $shipping_method, $index); ?></div>
		<div class="shipmondo-shop-id"><?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::getCurrentSelection('id_string', $shipping_method, $index); ?></div>
	</div>
	<div class="shipmondo_zicode_error_text<?php echo \ShipmondoForWooCommerce\Plugin\Controllers\PickupPoint::isCurrentSelection($shipping_method, $index) ? '' : ' active'; ?>">
        <?php echo __('Please enter a zipcode to select a pickup point','pakkelabels-for-woocommerce'); ?>
    </div>
</div>