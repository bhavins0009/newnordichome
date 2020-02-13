<div class="shipmondo-modal-content">
    <?php \ShipmondoForWooCommerce\Plugin\Plugin::getTemplate('pickup-point-selection.modal.partials.close-button'); ?>
    <div class="shipmondo-modal-header">
        <h4><?php echo __('Choose pickup point', 'pakkelabels-for-woocommerce') ?></h4>
        <p class="shipmondo-pickoup-point-counter" id="shipmondo-pickup-point-counter"><?php echo sprintf(_n('%s pickup point found', '%s pickup points found', $pickup_points_number,'pakkelabels-for-woocommerce'), $pickup_points_number); ?></p>
    </div>
    <div class="shipmondo-modal-body">
        <div id="shipmondo-map-wrapper">
            <div id="shipmondo-map"></div>
            <input type="hidden" name="shipmondo_pickup_points_json" value='<?php echo htmlentities(json_encode($pickup_points), ENT_QUOTES, 'UTF-8'); ?>'>
            <script>
                jQuery(document).trigger('shipmondo_pickup_point_modal_loaded');
            </script>
        </div>
        <div class="shipmondo-list-wrapper">
            <ul class="shipmondo-shoplist-ul">
                <?php
                    foreach($pickup_points as $pickup_point) {
                        ?>
                        <li class="shipmondo-shop-list" data-id="<?php echo $pickup_point->number; ?>">
                            <div class="shipmondo-pickup-point-info">
	                            <input type="hidden" class="input_shop_id" id="<?php echo 'shop_id_' . $pickup_point->number; ?>" name="<?php echo 'shop_id_' . $pickup_point->number; ?>" value="<?php echo 'ID: ' . strtoupper($pickup_point->agent) . '-' . trim($pickup_point->number); ?>">
	                            <input type="hidden" class="input_shop_name" id="<?php echo 'shop_name_' . $pickup_point->number; ?>" name="<?php echo 'shop_name_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->company_name; ?>">
	                            <input type="hidden" class="input_shop_address" id="<?php echo 'shop_address_' . $pickup_point->number; ?>" name="<?php echo 'shop_address_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->address; ?>">
	                            <input type="hidden" class="input_shop_zip" id="<?php echo 'shop_zip_' . $pickup_point->number; ?>" name="<?php echo 'shop_zip_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->zipcode; ?>">
	                            <input type="hidden" class="input_shop_city" id="<?php echo 'shop_city_' . $pickup_point->number; ?>" name="<?php echo 'shop_city_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->city; ?>">

	                            <div class="shipmondo-radio-button"></div>
                                <div class="shipmondo-pickup-point-name"><?php echo $pickup_point->company_name; ?></div>
                                <div class="shipmondo-pickup-point-address"><?php echo $pickup_point->address; ?></div>
                                <div class="shipmondo-pickup-point-zipcode-city">
                                    <span class="shipmondo-pickup-point-zipcode"><?php echo $pickup_point->zipcode; ?></span>, <span class="shipmondo-pickup-point-city"><?php echo $pickup_point->city; ?></span>
                                </div>
                                <div class="shipmondo-pickup-point-id"><?php echo 'ID: ' . strtoupper($pickup_point->agent) . '-' . trim($pickup_point->number); ?></div>
                            </div>
                        </li>
                        <?php
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="shipmondo-modal-footer">
        <?php echo __('Powered by Shipmondo', 'pakkelabels-for-woocommerce') ?>
    </div>
</div>
<div class="shipmondo-modal-checkmark">
    <svg class="shipmondo-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="shipmondo-checkmark_circle" cx="26" cy="26" r="25" fill="none"/><path class="shipmondo-checkmark_check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
</div>