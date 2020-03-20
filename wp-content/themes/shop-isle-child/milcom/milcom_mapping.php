<style type="text/css">
	.mapping-table { width:100%;border:1px solid #ccd0d4;border-left:0;   }
	.mapping-td{border: 1px solid;}
	.width50{width:50%;}
	table.mapping-table .heading { height: 30px; border-color:#ccd0d4; font-weight: 700 }
	table.mapping-table .heading-one { height: 30px;color:#0073aa; border-color:#ccd0d4; font-weight: 500 }
	table.mapping-table { border:1px solid #ccd0d4; border-left:0; border-top:0;width: 60%;  margin: 15px 0 15px }
	.mapping-table td { border-top:0; border-left:1px solid #ccd0d4;border-right:0;border-bottom:1px solid #ccd0d4  }
	.mapping-table th { border-right:0;}
	.bottom-btn { width: 60%; float: left; text-align: center }
	.bottom-btn .button-primary{ padding-left: 40px; min-height: 35px; margin-left: 107px;  padding-right:40px; float: none }
	.wp-core-ui select { max-width:240px; width: 100% }
</style>
<?php
	include_once('get_mapping_status.php');
	$insertMappingUrl = get_stylesheet_directory_uri().'/milcom/insertMappingData.php';
	$key = '';
    $dropdown_end = '</select>';
?>
<form action="" id="frmMilcomMapping" name="frmMilcomMapping" method="POST">

	<table  class="mapping-table wp-list-table widefat fixed striped posts">
		<thead>
			<thead>
				<th class="mapping-td width50 heading-one">Felter hos Milcom <!--Milcom fields --></th>
				<th class="mapping-td width50 heading-one">Felter i min webshop <!-- Webshop fields --> </th>
			</thead>
			
		</thead>
		<tr>
			<td class="mapping-td width50 heading" colspan="2">Overskrift <!-- Header --></td>
		</tr>
		<?php $key = 'externalDocNo';?>
		<tr>
			<td align="left" class="mapping-td width50">externalDocNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="externalDocNo" name="externalDocNo">' . ''. getMappingDropdownValues('externalDocNo'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">sellToCustomerNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="sellToCustomerNo" name="sellToCustomerNo">' . ''. getMappingDropdownValues('sellToCustomerNo'). ''. $dropdown_end;?>
			</td>
		</tr>

		<!--
			<tr>
				<td align="left" class="mapping-td width50">shippingAgent</td>
				<td class="mapping-td width50">
					<?php echo '<select id="shippingAgent" name="shippingAgent">' . ''. getMappingDropdownValues('shippingAgent'). ''. $dropdown_end;?>
				</td>
			</tr>
		-->

		<tr>
			<td align="left" class="mapping-td width50">shippingAgentServiceCode</td>
			<td class="mapping-td width50">
				<?php echo '<select id="shippingAgentServiceCode" name="shippingAgentServiceCode">' . ''. getMappingDropdownValues('shippingAgentServiceCode'). ''. $dropdown_end;?>
			</td>
		</tr>

		<tr>
			<td align="left" class="mapping-td width50">orderDate</td>
			<td class="mapping-td width50">
				<?php echo '<select id="orderDate" name="orderDate">' . ''. getMappingDropdownValues('orderDate'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">currency</td>
			<td class="mapping-td width50">
				<?php echo '<select id="currency" name="currency">' . ''. getMappingDropdownValues('currency'). ''. $dropdown_end;?>
			</td>
		</tr>
		
		<tr>
			<td align="left" class="mapping-td width50">phoneNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="phoneNo" name="phoneNo">' . ''. getMappingDropdownValues('phoneNo'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">email</td>
			<td class="mapping-td width50">
				<?php echo '<select id="email" name="email">' . ''. getMappingDropdownValues('email'). ''. $dropdown_end;?>
			</td>
		</tr>

		<tr>
			<td class="mapping-td width50 heading" colspan="2">Leverings oplysninger <!--Ship To Address--> </td></tr>
		<tr>
			<td align="left" class="mapping-td width50">name</td>
			<td class="mapping-td width50">
				<?php echo '<select id="name" name="name">' . ''. getMappingDropdownValues('name'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">address1</td>
			<td class="mapping-td width50">
				<?php echo '<select id="address1" name="address1">' . ''. getMappingDropdownValues('address1'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">address2</td>
			<td class="mapping-td width50">
				<?php echo '<select id="address2" name="address2">' . ''. getMappingDropdownValues('address2'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">postalNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="postalNo" name="postalNo">' . ''. getMappingDropdownValues('postalNo'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">city</td>
			<td class="mapping-td width50">
				<?php echo '<select id="city" name="city">' . ''. getMappingDropdownValues('city'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">county</td>
			<td class="mapping-td width50">
				<?php echo '<select id="county" name="county">' . ''. getMappingDropdownValues('county'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">country</td>
			<td class="mapping-td width50">
				<?php echo '<select id="country" name="country">' . ''. getMappingDropdownValues('country'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">contactName</td>
			<td class="mapping-td width50">
				<?php echo '<select id="contactName" name="contactName">' . ''. getMappingDropdownValues('contactName'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">pakkeShopID</td>
			<td class="mapping-td width50">
				<?php echo '<select id="pakkeShopID" name="pakkeShopID">' . ''. getMappingDropdownValues('pakkeShopID'). ''. $dropdown_end;?>
			</td>
		</tr>

		<tr>
			<td class="mapping-td width50 heading" colspan="2">Ordre linie</td></tr>
		
		<tr>
			<td align="left" class="mapping-td width50">itemNo</td>
			<td class="mapping-td width50">
				<?php echo '<select id="itemNo" name="itemNo">' . ''. getMappingDropdownValues('itemNo', 'orderline'). ''. $dropdown_end;?>
			</td>
		</tr>
		
		<tr>
			<td align="left" class="mapping-td width50">itemName</td>
			<td class="mapping-td width50">
				<?php echo '<select id="itemName" name="itemName">' . ''. getMappingDropdownValues('itemName', 'orderline'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">quantity</td>
			<td class="mapping-td width50">
				<?php echo '<select id="quantity" name="quantity">' . ''. getMappingDropdownValues('quantity', 'orderline'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">price</td>
			<td class="mapping-td width50">
				<?php echo '<select id="price" name="price">' . ''. getMappingDropdownValues('price', 'orderline'). ''. $dropdown_end;?>
			</td>
		</tr>
		<tr>
			<td align="left" class="mapping-td width50">total</td>
			<td class="mapping-td width50">
				<?php echo '<select id="total" name="total">' . ''. getMappingDropdownValues('total', 'orderline'). ''. $dropdown_end;?>
			</td>
		</tr>

	</table>
	<div class="bottom-btn"><input class="button-primary" type="submit" id="save" name="save" value="Gem"></div>
</form>	
	
