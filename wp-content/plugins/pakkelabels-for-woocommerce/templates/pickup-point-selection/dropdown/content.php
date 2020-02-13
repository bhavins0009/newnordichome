<div class="shipmondo-dropdown-content">
    <div class="shipmondo-list-wrapper">
        <ul class="shipmondo-shoplist-ul">
            <?php
                foreach($pickup_points as $pickup_point) {
                    ?>
                    <li class="shipmondo-shop-list" data-id="<?php echo $pickup_point->number; ?>">
	                    <input type="hidden" class="input_shop_id" id="<?php echo 'shop_id_' . $pickup_point->number; ?>" name="<?php echo 'shop_id_' . $pickup_point->number; ?>" value="<?php echo 'ID: ' . strtoupper($pickup_point->agent) . '-' . trim($pickup_point->number); ?>">
	                    <input type="hidden" class="input_shop_name" id="<?php echo 'shop_name_' . $pickup_point->number; ?>" name="<?php echo 'shop_name_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->company_name; ?>">
	                    <input type="hidden" class="input_shop_address" id="<?php echo 'shop_address_' . $pickup_point->number; ?>" name="<?php echo 'shop_address_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->address; ?>">
	                    <input type="hidden" class="input_shop_zip" id="<?php echo 'shop_zip_' . $pickup_point->number; ?>" name="<?php echo 'shop_zip_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->zipcode; ?>">
	                    <input type="hidden" class="input_shop_city" id="<?php echo 'shop_city_' . $pickup_point->number; ?>" name="<?php echo 'shop_city_' . $pickup_point->number; ?>" value="<?php echo $pickup_point->city; ?>">

	                    <img class="agent_icon" src="<?php echo \ShipmondoForWooCommerce\Plugin\Plugin::getImgURL('picker_icon_' . $agent . '.png')?>">
                        <div class="shipmondo-pickup-point-info">
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