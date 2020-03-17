<?php
function getMappingValStatus($milcomColumn, $dropdownValue){
	global $wpdb;
	$result = $wpdb->get_results('SELECT * FROM milcom_mapping WHERE milcom_column="'.$milcomColumn.'" ');
	
	if(!empty($result) && count($result)>0){
		$webshopColumnValue = $result[0]->webshop_column;
		if(trim($webshopColumnValue) == trim($dropdownValue)){
			return 'Selected';
		} else {
			return '';
		}
	} else {
		return '';
	}
}

// My data = Mine data
function getMappingDropdownValues($milcomColumn, $part=''){

	$dropdown_value = '';
	if('orderline' != $part){
		$dropdown_value .= '
		  <optgroup label="Min data"> 
			    <option value="blank-field" '. getMappingValStatus($milcomColumn, "blank-field").'>Ikke vigtigt </option>
			    <option value="external-field" '. getMappingValStatus($milcomColumn, "external-field").'> Specificeres manuelt på ordren </option>
			    <option value="shipping-agent" '. getMappingValStatus($milcomColumn, "shipping-agent").'> Forsendelse </option>
		  </optgroup>';	
	}

	$dropdown_value .= '
		  <optgroup label="Order Data">
			    <option value="orderdata-id" '. getMappingValStatus($milcomColumn, "orderdata-id").'>Order No</option>
			    <option value="orderdata-status" '. getMappingValStatus($milcomColumn, "orderdata-status").'>status</option>
			    <option value="orderdata-currency" '. getMappingValStatus($milcomColumn, "orderdata-currency").'> currency</option>
			    <option value="orderdata-prices_include_tax" '. getMappingValStatus($milcomColumn, "orderdata-prices_include_tax").'>prices_include_tax</option>
			    <option value="orderdata-date_created" '. getMappingValStatus($milcomColumn, "orderdata-date_created").'>date_created</option>
			    <option value="orderdata-date_modified" '. getMappingValStatus($milcomColumn, "orderdata-date_modified").'>date_modified</option>
			    <option value="orderdata-date_paid" '. getMappingValStatus($milcomColumn, "orderdata-date_paid").'>date_paid</option>	    
			    <option value="orderdata-discount_total" '. getMappingValStatus($milcomColumn, "orderdata-discount_total").'>discount_total</option>
			    <option value="orderdata-shipping_total" '. getMappingValStatus($milcomColumn, "orderdata-shipping_total").'>shipping_total</option>
			    <option value="orderdata-shipping_tax" '. getMappingValStatus($milcomColumn, "orderdata-shipping_tax").'>shipping_tax</option>
			    <option value="orderdata-cart_tax" '. getMappingValStatus($milcomColumn, "orderdata-cart_tax").'>cart_tax</option>
			    <option value="orderdata-total" '. getMappingValStatus($milcomColumn, "orderdata-total").'>total</option>
			    <option value="orderdata-total_tax" '. getMappingValStatus($milcomColumn, "orderdata-total_tax").'>total_tax</option>
			    <option value="orderdata-payment_method" '. getMappingValStatus($milcomColumn, "orderdata-payment_method").'>payment_method</option>
			    <option value="orderdata-payment_method_title" '. getMappingValStatus($milcomColumn, "orderdata-payment_method_title").'>payment_method_title</option>
		  </optgroup>
		  <optgroup label="Billing Address">
			    <option value="billing-first_name" '. getMappingValStatus($milcomColumn, "billing-first_name").'>first_name</option>
			    <option value="billing-last_name" '. getMappingValStatus($milcomColumn, "billing-last_name").'>last_name</option>
			    <option value="billing-company" '. getMappingValStatus($milcomColumn, "billing-company").'>company</option>
			    <option value="billing-address_1" '. getMappingValStatus($milcomColumn, "billing-address_1").'>address_1</option>
			    <option value="billing-address_2" '. getMappingValStatus($milcomColumn, "billing-address_2").'>address_2</option>
			    <option value="billing-city" '. getMappingValStatus($milcomColumn, "billing-city").'>city</option>
			    <option value="billing-state" '. getMappingValStatus($milcomColumn, "billing-state").'>state</option>
			    <option value="billing-postcode" '. getMappingValStatus($milcomColumn, "billing-postcode").'>postcode</option>
			    <option value="billing-country" '. getMappingValStatus($milcomColumn, "billing-country").'>country</option>
			    <option value="billing-email" '. getMappingValStatus($milcomColumn, "billing-email").'>email</option>
			    <option value="billing-phone" '. getMappingValStatus($milcomColumn, "billing-phone").'>phone</option>
		  </optgroup>
		  <optgroup label="Shipping Address">
			    <option value="shipping-first_name" '. getMappingValStatus($milcomColumn, "shipping-first_name").'>first_name</option>
			    <option value="shipping-last_name" '. getMappingValStatus($milcomColumn, "shipping-last_name").'>last_name</option>
			    <option value="shipping-company" '. getMappingValStatus($milcomColumn, "shipping-company").'>company</option>
			    <option value="shipping-address_1" '. getMappingValStatus($milcomColumn, "shipping-address_1").'>address_1</option>
			    <option value="shipping-address_2" '. getMappingValStatus($milcomColumn, "shipping-address_2").'>address_2</option>
			    <option value="shipping-city" '. getMappingValStatus($milcomColumn, "shipping-city").'>city</option>
			    <option value="shipping-state" '. getMappingValStatus($milcomColumn, "shipping-state").'>state</option>
			    <option value="shipping-postcode" '. getMappingValStatus($milcomColumn, "shipping-postcode").'>postcode</option>
			    <option value="shipping-country" '. getMappingValStatus($milcomColumn, "shipping-country").'>country</option>
		  </optgroup>
		  <optgroup label="Ordre linie">
		  		<option value="item-sku" '. getMappingValStatus($milcomColumn, "item-sku").'>Item No</option>
		  		<option value="item-name" '. getMappingValStatus($milcomColumn, "item-name").'>Item Name</option>
			    <option value="item-price" '. getMappingValStatus($milcomColumn, "item-price").'>Item price</option>
			    <option value="item-quantity" '. getMappingValStatus($milcomColumn, "item-quantity").'>Item quantity</option>
			    <option value="item-total" '. getMappingValStatus($milcomColumn, "item-total").'>Item total</option>
		  </optgroup>';
	return $dropdown_value;	  
}
?>