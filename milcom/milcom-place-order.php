<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<?php
error_reporting(0);
ini_set("display_errors", 0);

require('../wp-load.php');
global $wpdb;
define('USERPWD', 'NNH1:N3wN0rdiCHow3');

$order_id = $_POST['order_id'];

if(!empty($order_id)) {
    // Get an instance of the WC_Order object
    $order = wc_get_order( $order_id );
    $order_data = $order->get_data();

    $result = $wpdb->get_results('SELECT * FROM milcom_mapping');
    $mappingData = array();

    // MAPPING CODE
    if(count($result)>0){

        foreach ($result as $key => $value) {
            
                if('shipping-agent' !== $value->webshop_column){
                    $column = explode("-", $value->webshop_column);
                    switch ($column[0]) {
                        case 'external':
                            $mappingData[$value->milcom_column] = $_POST[$value->milcom_column];
                            break;
                        case 'orderdata':
                            $mappingData[$value->milcom_column] = $order_data[$column[1]];
                            break;
                        case 'billing':
                            $mappingData[$value->milcom_column] = $order_data['billing'][$column[1]];
                            break;    
                        case 'shipping':
                            $mappingData[$value->milcom_column] = $order_data['shipping'][$column[1]];
                            break;    
                        case 'item':
                            $mappingData[$value->milcom_column] = $value->webshop_column;
                            break;
                        default:
                            $mappingData[$value->milcom_column] = "";
                            break;
                    }
                    if($value->milcom_column == 'orderDate') {
                        $mappingData[$value->milcom_column] = $mappingData[$value->milcom_column]->date('mdY');
                    }
                }
        }
            
    }


  if(!empty($_POST['shippingAgentServiceCode'])){
      $shippingAgentResult = $wpdb->get_results('SELECT shipping_agent_name FROM shipping_agent 
          WHERE shipping_agent_servicecode="'.$_POST['shippingAgentServiceCode'].'" ');
      $shippingAgentName = $shippingAgentResult[0]->shipping_agent_name;
      $shippingAgentServiceCode = $_POST['shippingAgentServiceCode'];
  } else {
      $shippingAgentName = '';
      $shippingAgentServiceCode = '';
  }

  // SOAP CODE
  $headerArray = array('orderNo' => $order_id, 
                      'externalDocNo'=> $mappingData['externalDocNo'], 
                      'sellToCustomerNo'=> $mappingData['sellToCustomerNo'],
                      'shippingAgent' => $shippingAgentName,
                      'shippingAgentServiceCode' => $shippingAgentServiceCode,
                      'orderDate' => $mappingData['orderDate'], 
                      'currency' => $mappingData['currency'],
                      'phoneNo' => $mappingData['phoneNo'],
                      'email'=> $mappingData['email'],
                      'yourReference' => $order_id
                  );

  $shipToAddress = array('name' => $mappingData['name'],
                      'address1' => $mappingData['address1'], 
                      'address2' => $mappingData['address2'], 
                      'postalNo' => $mappingData['postalNo'],
                      'city' => $mappingData['city'],
                      'county' => $mappingData['county'],
                      'country' => $mappingData['country'],
                      'contactName' => $mappingData['contactName'],
                      'pakkeShopID' => $mappingData['pakkeShopID']);

            $xmlPostData = '<?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:microsoft-dynamics-schemas/codeunit/NewNordicHome"><SOAP-ENV:Body><ns1:CreateSalesOrder><ns1:order><header><orderNo>'.$order_id.'</orderNo><externalDocNo>'.$order_id.'</externalDocNo><sellToCustomerNo>31786178</sellToCustomerNo><shippingAgent>'.$shippingAgentName.'</shippingAgent><shippingAgentServiceCode>'.$shippingAgentServiceCode.'</shippingAgentServiceCode><orderDate>'.$mappingData['orderDate'].'</orderDate><currency>'. $mappingData['currency'].'</currency><phoneNo>'.$mappingData['phoneNo'].'</phoneNo><email>'.$mappingData['email'].'</email><yourReference>'.$order_id.'</yourReference></header><shipToAddress><name>'.$mappingData['name'].'</name><address1>'.$mappingData['address1'].'</address1><address2>'.$mappingData['address2'].'</address2><postalNo>'.$mappingData['postalNo'].'</postalNo><city>'.$mappingData['city'].'</city><county>NotIMP</county><country>'.$mappingData['country'].'</country><contactName>'.$mappingData['contactName'].'</contactName><pakkeShopID>'.$mappingData['pakkeShopID'].'</pakkeShopID></shipToAddress><orderLineList>';

    /*  $request = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:microsoft-dynamics-schemas/codeunit/NewNordicHome"><SOAP-ENV:Body><ns1:CreateSalesOrder><ns1:order><header><orderNo>18224</orderNo><externalDocNo>248147452</externalDocNo><sellToCustomerNo>31786178</sellToCustomerNo><shippingAgent>GLS</shippingAgent><shippingAgentServiceCode>GLS-SHOP</shippingAgentServiceCode><orderDate>02192020</orderDate><currency>DKK</currency><phoneNo>22665544</phoneNo><email>seb@bullerbox.dk</email><yourReference>18009</yourReference></header><shipToAddress><name>Sebastian Salinas Frødin</name><address1>BullerBox ApS, CVR 35803866</address1><address2>Sebastian Salinas Frødin</address2><postalNo>2630</postalNo><city>Taastrup</city><county>MN</county><country>Denmark</country><contactName>Sebastian</contactName><pakkeShopID>GLS-95060</pakkeShopID></shipToAddress><orderLineList><orderLine><lineType>Vare</lineType><itemNo>101</itemNo><vendorItemNo>10000</vendorItemNo><eANNo>5714243002381</eANNo><vendorCode>1011</vendorCode><itemName>Flow vase large light Gray - Speckrum</itemName><quantity>1</quantity><price>287.28</price><total>287.28</total></orderLine><orderLine><lineType>Vare</lineType><itemNo>101</itemNo><vendorItemNo>10000</vendorItemNo><eANNo>5714243002381</eANNo><vendorCode>1011</vendorCode><itemName>Flow vase large light Gray - Speckrum</itemName><quantity>1</quantity><price>287.28</price><total>287.28</total></orderLine></orderLineList></ns1:order></ns1:CreateSalesOrder></SOAP-ENV:Body></SOAP-ENV:Envelope>';
*/
    function getItemValue($item_data, $product, $itemKey){
      $itemValue = '';
        switch ($itemKey) {
          case 'item-sku':
              $itemValue = $product->get_sku();
            break;
          case 'item-name':
              $itemValue = $item_data['name'];
            break;
          case 'item-quantity':
              $itemValue = $item_data['quantity'];
            break;
          case 'item-price':
              $itemValue = $product->get_price();
            break;
          case 'item-total':
              $itemValue = $item_data['total'];
            break;
          default:
              $itemValue = "";
            break;
        }
        return $itemValue;
    }

    foreach ($order->get_items() as $item_key => $item ) {

             $item_data    = $item->get_data();
             $product      = $item->get_product(); // Get the WC_Product object

            /* $item_name = $item_data['name'];
             $product_id   = $item_data['product_id'];
             $variation_id = $item_data['variation_id'];
             $quantity     = $item_data['quantity'];
             $tax_class    = $item_data['tax_class'];
             $line_subtotal     = $item_data['subtotal'];
             $line_subtotal_tax = $item_data['subtotal_tax'];
             $line_total        = $item_data['total'];
             $line_total_tax    = $item_data['total_tax'];

             // Get data from The WC_product object using methods (examples)
             $product        = $item->get_product(); // Get the WC_Product object

             $product_type   = $product->get_type();
             $product_sku    = $product->get_sku();
             $product_price  = $product->get_price();
             $stock_quantity = $product->get_stock_quantity();

             $orderLine = array( 
                             'lineType' => 'Vare',
                             'itemNo' => $product_sku,
                             'vendorItemNo' => '10000',
                             'eANNo' => $product_sku,
                             'vendorCode' => '1011',
                             'itemName' => $item_name,
                             'quantity' => $quantity, 
                             'price' => $product_price,
                             'total' => $line_total);

             $xmlPostData .= '<orderLine><lineType>Vare</lineType><itemNo>'.$product_sku.'</itemNo><itemName>'.$item_name.'</itemName><quantity>'.$quantity.'</quantity><price>'.$product_price.'</price><total>'.$line_total.'</total></orderLine>';   */                      

             $itemNo = getItemValue($item_data, $product, $mappingData['itemNo']);
             $itemName = getItemValue($item_data, $product, $mappingData['itemName']);
             $quantity = getItemValue($item_data, $product, $mappingData['quantity']);
             $price = getItemValue($item_data, $product, $mappingData['price']);
             $total = getItemValue($item_data, $product, $mappingData['total']);
             $xmlPostData .= '<orderLine><lineType>Vare</lineType><itemNo>'.$itemNo.'</itemNo><itemName>'.$itemName.'</itemName><quantity>'.$quantity.'</quantity><price>'.$price.'</price><total>'.$total.'</total></orderLine>';  

    }

    $xmlPostData .= '</orderLineList></ns1:order></ns1:CreateSalesOrder></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    $action = 'urn:microsoft-dynamics-schemas/codeunit/NewNordicHome:CreateSalesOrder';
    $location = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
    $headers = array(
        'Method: POST',
        'Connection: Keep-Alive',
        'User-Agent: PHP-SOAP-CURL',
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: "' . $action . '"',
        'soap_version: SOAP_1_2'
    ); 
        
    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlPostData);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
    curl_setopt($ch, CURLOPT_USERPWD, USERPWD);

    $response = curl_exec($ch);        
    echo '<pre>';

    if (preg_match("/a:Microsoft.Dynamics.Nav.Types/", $response))
    {
        // Error
        //echo "the url contains a:Microsoft.Dynamics.Nav.Types";
    }
    else
    {
        //echo "the url contains not a:Microsoft.Dynamics.Nav.Types";
        // Success
        $wpdb->query("UPDATE `clk_42491aa6f3_wp_wc_order_stats` SET is_milcom_approved='Yes' WHERE order_id=".$order_id." ");
    }
}
//header('Location: ' . $_SERVER['HTTP_REFERER']);
?>

<?php if (preg_match("/a:Microsoft.Dynamics.Nav.Types/", $response)) { ?>
<!-- Error is coming here -->
<script>
Swal.fire({
  html:
  '<div style="font-size: 32px;width: 100%;float: left;padding-bottom: 5px;margin-bottom:0;border-bottom: 1px solid #b9b9b9;text-align: left;"><strong>Milcom Response</strong></div>, ' +
    '<div style="font-size:20px; text-align:left"><strong>Order No: </strong><?php echo $order_id;?></div>, ' +
    '<div style="font-size:24px;text-align:left"><strong style="font-size:18px; font-weight:normal"><strong>Status:</strong> Your order is failed in Milcom</strong></div>, ' +
    
    '<div style="text-align:left"><strong style="font-size:18px; font-weight:normal"><strong>Error Response From Milcom:</strong></div>' +    
    '<div style="color:red;font-size:18px; text-align:left; border:1px solid #aaa; margin-top:15px; padding:10px; height:62px; overflow-y:auto; font-weight:normal"><?php print_r(addslashes($response));?></div><br/>' + 
    '<div style="text-align:left; margin-top:5px"><a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>"><input style="cursor:pointer;background:#007cba;border-color:#007cba;color:#fff;text-decoration:none;text-shadow:none;border-radius: 3px;white-space: nowrap; box-sizing: border-box; border:0;padding: 0 10px!important;min-height: 35px;" type="submit" id="save" name="save" value="Back To Order"></a> </div>' ,
  showCloseButton: false,
  showCancelButton: false,
  focusConfirm: false,
  showConfirmButton: false
})
</script>

<?php } else { ?>
<!-- Success Order -->
<script>
Swal.fire({
  html:
  '<div style="font-size: 32px;width: 100%;float: left;padding-bottom: 5px;margin-bottom:0;border-bottom: 1px solid #b9b9b9;text-align: left;"><strong>Milcom Response</strong></div>, ' +
    '<div style="font-size:20px; text-align:left"><strong>Order No: </strong><?php echo $order_id;?></div>, ' +
    '<div style="font-size:24px;text-align:left"><strong style="font-size:18px; font-weight:normal"><strong>Status:</strong> Your order is placed succeessfully in Milcom </strong></div>, ' +    
    '<div style="text-align:left"><strong style="font-size:18px; font-weight:normal"><strong>Success Response From Milcom:</strong></div>' +    
    '<div style="color:green;font-size:18px; text-align:left; border:1px solid #aaa; margin-top:15px; padding:10px; height:62px; overflow-y:auto; font-weight:normal"><?php print_r(addslashes($response));?></div><br/>' + 
    '<div style="text-align:left; margin-top:5px"><a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>"><input style="cursor:pointer;background:#007cba;border-color:#007cba;color:#fff;text-decoration:none;text-shadow:none;border-radius: 3px;white-space: nowrap; box-sizing: border-box; border:0;padding: 0 10px!important;min-height: 35px;" type="submit" id="save" name="save" value="Back To Order"></a> </div>' ,
  showCloseButton: false,
  showCancelButton: false,
  focusConfirm: false,
  showConfirmButton: false
})
</script>

<?php }  ?>



