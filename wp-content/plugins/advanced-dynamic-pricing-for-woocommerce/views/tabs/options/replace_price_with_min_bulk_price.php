<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top">
    <th scope="row" class="titledesc">
		<?php _e( 'Replace price with lowest bulk price', 'advanced-dynamic-pricing-for-woocommerce' ) ?></th>
    <td class="forminp forminp-checkbox">
        <fieldset>
            <div>
                <label for="replace_price_with_min_bulk_price">
                    <input <?php checked( $options['replace_price_with_min_bulk_price'] ) ?> name="replace_price_with_min_bulk_price" id="replace_price_with_min_bulk_price" type="checkbox">
					<?php _e( 'Enable', 'advanced-dynamic-pricing-for-woocommerce' ) ?>
                </label>
            </div>
            <div>
                <label for="replace_price_with_min_bulk_price_template">
	                <?php _e( 'Output template', 'advanced-dynamic-pricing-for-woocommerce' ) ?>
                    <input value="<?php echo $options['replace_price_with_min_bulk_price_template'] ?>" name="replace_price_with_min_bulk_price_template" id="replace_price_with_min_bulk_price_template" type="text">
                </label>
                <br>
                <?php _e( 'Available tags', 'advanced-dynamic-pricing-for-woocommerce' ) ?> : <?php _e( '{{price}}, {{price_suffix}}, {{price_striked}}, {{initial_price}}', 'advanced-dynamic-pricing-for-woocommerce' ) ?>
            </div>
        </fieldset>
    </td>
</tr>