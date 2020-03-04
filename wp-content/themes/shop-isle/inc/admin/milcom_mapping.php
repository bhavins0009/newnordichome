<style type="text/css">
	.mapping-table { width:100%;border: 1px solid;padding:25px; }
	.mapping-td{border: 1px solid;}
	.width50{width:50%;}
</style>
<?php
	// $order_id = 13993; //$_POST['order_id'];
	// $order = wc_get_order( $order_id );
	// $order_data = $order->get_data();
	// echo '<pre>';
	// print_r($order->data);
	// exit();

$insertMappingUrl = get_site_url().'/insertMappingData.php';

$dropdown_value = '
  <optgroup label="External Data">
	    <option value="external-field">External field</option>
  </optgroup>
  <optgroup label="Order Data">
	    <option value="orderdata-status">status</option>
	    <option value="orderdata-currency">currency</option>
	    <option value="orderdata-prices_include_tax">prices_include_tax</option>
	    <option value="orderdata-date_created">date_created</option>
	    <option value="orderdata-date_modified">date_modified</option>
	    <option value="orderdata-date_paid">date_paid</option>	    
	    <option value="orderdata-discount_total">discount_total</option>
	    <option value="orderdata-shipping_total">shipping_total</option>
	    <option value="orderdata-shipping_tax">shipping_tax</option>
	    <option value="orderdata-cart_tax">cart_tax</option>
	    <option value="orderdata-total">total</option>
	    <option value="orderdata-total_tax">total_tax</option>
	    <option value="orderdata-payment_method">payment_method</option>
	    <option value="orderdata-payment_method_title">payment_method_title</option>
  </optgroup>
  <optgroup label="Billing Address">
	    <option value="billing-first_name">first_name</option>
	    <option value="billing-last_name">last_name</option>
	    <option value="billing-company">company</option>
	    <option value="billing-address_1">address_1</option>
	    <option value="billing-address_2">address_2</option>
	    <option value="billing-city">city</option>
	    <option value="billing-state">state</option>
	    <option value="billing-postcode">postcode</option>
	    <option value="billing-country">country</option>
	    <option value="billing-email">email</option>
	    <option value="billing-phone">phone</option>
  </optgroup>
  <optgroup label="Shipping Address">
	    <option value="shipping-first_name">first_name</option>
	    <option value="shipping-last_name">last_name</option>
	    <option value="shipping-company">company</option>
	    <option value="shipping-address_1">address_1</option>
	    <option value="shipping-address_2">address_2</option>
	    <option value="shipping-city">city</option>
	    <option value="shipping-state">state</option>
	    <option value="shipping-postcode">postcode</option>
	    <option value="shipping-country">country</option>
  </optgroup>
  <optgroup label="Line Items">
	    <option value="lineitem-product_id">product_id</option>
	    <option value="lineitem-variation_id">variation_id</option>
	    <option value="lineitem-quantity">company</option>
	    <option value="lineitem-tax_class">address_1</option>
	    <option value="lineitem-subtotal">address_2</option>
	    <option value="lineitem-subtotal_tax">city</option>
	    <option value="lineitem-total">state</option>
	    <option value="lineitem-total_tax">postcode</option>
	    <option value="lineitem-taxes">country</option>
  </optgroup>';
  $dropdown_end = '</select>';

?>

<form action="<?php echo $insertMappingUrl; ?>" id="frmMilcomMapping" name="frmMilcomMapping" method="POST">

	<table class="mapping-table">
		<tr><td class="mapping-td width50" colspan=2>Header</td></tr>
		<tr>
			<td align="right" class="mapping-td width50">externalDocNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="externalDocNo" name="externalDocNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">sellToCustomerNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="sellToCustomerNo" name="sellToCustomerNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">shippingAgent</td>
			<td class="mapping-td width50">
				<?php echo '<select id="shippingAgent" name="shippingAgent">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">shippingAgentServiceCode</td>
			<td class="mapping-td width50">
				<?php echo '<select id="shippingAgentServiceCode" name="shippingAgentServiceCode">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">orderDate</td>
			<td class="mapping-td width50">
				<?php echo '<select id="orderDate" name="orderDate">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">currency</td>
			<td class="mapping-td width50">
				<?php echo '<select id="currency" name="currency">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">phoneNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="phoneNo" name="phoneNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">email</td>
			<td class="mapping-td width50">
				<?php echo '<select id="email" name="email">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">yourReference</td>
			<td class="mapping-td width50">
				<?php echo '<select id="yourReference" name="yourReference">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>

		<tr>
			<td class="mapping-td width50" colspan=2>shipToAddress</td></tr>
		<tr>
			<td align="right" class="mapping-td width50">name</td>
			<td class="mapping-td width50">
				<?php echo '<select id="name" name="name">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">address1</td>
			<td class="mapping-td width50">
				<?php echo '<select id="address1" name="address1">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">address2</td>
			<td class="mapping-td width50">
				<?php echo '<select id="address2" name="address2">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">postalNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="postalNo" name="postalNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">city</td>
			<td class="mapping-td width50">
				<?php echo '<select id="city" name="city">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">county</td>
			<td class="mapping-td width50">
				<?php echo '<select id="county" name="county">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">country</td>
			<td class="mapping-td width50">
				<?php echo '<select id="country" name="country">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">contactName</td>
			<td class="mapping-td width50">
				<?php echo '<select id="contactName" name="contactName">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">pakkeShopID</td>
			<td class="mapping-td width50">
				<?php echo '<select id="pakkeShopID" name="pakkeShopID">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>

		<tr>
			<td class="mapping-td width50" colspan=2>orderLineList => orderLine</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">lineType</td>
			<td class="mapping-td width50">
				<?php echo '<select id="lineType" name="lineType">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">itemNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="itemNo" name="itemNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">vendorItemNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="vendorItemNo" name="vendorItemNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">eANNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="eANNo" name="eANNo">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">itemName</td>
			<td class="mapping-td width50">
				<?php echo '<select id="itemName" name="itemName">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">quantity</td>
			<td class="mapping-td width50">
				<?php echo '<select id="quantity" name="quantity">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">price</td>
			<td class="mapping-td width50">
				<?php echo '<select id="price" name="price">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="right" class="mapping-td width50">total</td>
			<td class="mapping-td width50">
				<?php echo '<select id="total" name="total">' . ''. $dropdown_value. ''. $dropdown_end;?>
			</td>
		</tr>

		<tr><td align="right">&nbsp</td>
				<td>
					<input class="button-primary" type="submit" id="save" name="save" value="Save">
				</td>
		</tr>
		
	</table>
</form>