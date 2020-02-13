<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top">
    <th scope="row" class="titledesc"><?php _e( 'Show debug panel at bottom of the page', 'advanced-dynamic-pricing-for-woocommerce' ) ?></th>
    <td class="forminp forminp-checkbox">
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php _e( 'Show debug panel at bottom of the page', 'advanced-dynamic-pricing-for-woocommerce' ) ?></span></legend>
            <label for="show_debug_bar">
                <input <?php checked( $options['show_debug_bar'] ) ?>
                        name="show_debug_bar" id="show_debug_bar" type="checkbox">
            </label>
        </fieldset>
    </td>
</tr>