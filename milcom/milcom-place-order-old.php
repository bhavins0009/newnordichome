<?php
require('../wp-load.php');
global $wpdb;

$order_id = $_POST['order_id'];
// Get an instance of the WC_Order object
$order = wc_get_order( $order_id );
$order_data = $order->get_data();

$result = $wpdb->get_results('SELECT * FROM milcom_mapping');
$mappingData = array();

// MAPPING CODE
if(count($result)>0){
    foreach ($result as $key => $value) {
        
            $column = explode("-", $value->webshop_column);
            switch ($column[0]) {
                case 'external':
                    $mappingData[$value->milcom_column] = !empty($_POST[$value->milcom_column]) ? $_POST[$value->milcom_column] : 'notImportant';
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
                default:
                    $mappingData[$value->milcom_column] = "";
                    break;
            }
            if($value->milcom_column == 'orderDate'){
                $mappingData[$value->milcom_column] = $mappingData[$value->milcom_column]->date('Y-m-d H:i:s');
            }
    }
        
}

// SOAP CODE
$headerArray = array('orderNo' => $order_id, 
                    'externalDocNo'=> $mappingData['externalDocNo'], 
                    'sellToCustomerNo'=> $mappingData['sellToCustomerNo'],
                    'shippingAgent' => $mappingData['shippingAgent'],
                    'shippingAgentServiceCode' => $mappingData['shippingAgentServiceCode'],
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


foreach ($order->get_items() as $item_key => $item ) {

         $item_data    = $item->get_data();

         $item_name = $item_data['name'];
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
}



$orderLineList =  array('orderLine' => $orderLine);

$orderArray = array('header' => $headerArray,
                  'shipToAddress' => $shipToAddress,
                  'orderLineList' => $orderLineList);
$order = $orderArray;

$obj1 = new \stdClass;
$obj1->orderLine = $orderLine;

try {
    //$parm = array();
    $parm['header'] = new SoapVar($headerArray, SOAP_ENC_OBJECT, null, 'tns', 'header', null );
    $parm['shipToAddress'] = new SoapVar($shipToAddress, SOAP_ENC_OBJECT, null, 'tns', 'shipToAddress', null );

    $test = array();
    $test['orderLine'] = new SoapVar($orderLine, SOAP_ENC_OBJECT, null, 'tns', 'orderLine', null);
    
    $parm['orderLineList']  = new SoapVar($test, SOAP_ENC_OBJECT, null, 'tns', 'orderLineList', null);
    $out = new SoapVar($parm, SOAP_ENC_OBJECT);

    echo '<pre>';
    print_r($out);   
    exit('99999');

    // Initialize Soap Client
    $baseURL = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
    $soap = $client = new NTLMSoapClient($baseURL);
    $result = $client->__soapCall('CreateSalesOrder', array('parameters' => array('order' => $out) )  );
    // echo 'I am in try';        
    $isPlacedMilcomOrder = true;
} catch(SoapFault $e) {
    // echo 'I am in catch';
    // echo '<pre>';
    // print_r($e);   
    $isPlacedMilcomOrder = false;
}

stream_wrapper_restore('http');


if(false === $isPlacedMilcomOrder){
    $wpdb->query("UPDATE `clk_42491aa6f3_wp_wc_order_stats` SET is_milcom_approved='Yes' WHERE order_id=".$order_id." ");
}

header('Location: ' . $_SERVER['HTTP_REFERER']);