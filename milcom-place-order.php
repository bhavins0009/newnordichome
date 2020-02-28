<?php
require('wp-load.php');

define('USERPWD', 'NNH1:N3wN0rdiCHow3');

class NTLMStream
{
    private $path;
    private $mode;
    private $options;
    private $opened_path;
    private $buffer;
    private $pos;

    /**
     * Open the stream
     *
     * @param unknown_type $path
     * @param unknown_type $mode
     * @param unknown_type $options
     * @param unknown_type $opened_path
     * @return unknown
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->opened_path = $opened_path;
        $this->createBuffer($path);
        return true;
    }

    /**
     * Close the stream
     *
     */
    public function stream_close()
    {
        curl_close($this->ch);
    }

    /**
     * Read the stream
     *
     * @param int $count number of bytes to read
     * @return content from pos to count
     */
    public function stream_read($count)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }
        $read = substr($this->buffer, $this->pos, $count);
        $this->pos += $count;
        return $read;
    }

    /**
     * write the stream
     *
     * @param int $count number of bytes to read
     * @return content from pos to count
     */
    public function stream_write($data)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }
        return true;
    }

    /**
     *
     * @return true if eof else false
     */
    public function stream_eof()
    {
        return ($this->pos > strlen($this->buffer));
    }

    /**
     * @return int the position of the current read pointer
     */
    public function stream_tell()
    {
        return $this->pos;
    }

    /**
     * Flush stream data
     */
    public function stream_flush()
    {
        $this->buffer = null;
        $this->pos = null;
    }

    /**
     * Stat the file, return only the size of the buffer
     *
     * @return array stat information
     */
    public function stream_stat()
    {
        $this->createBuffer($this->path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }

    /**
     * Stat the url, return only the size of the buffer
     *
     * @return array stat information
     */
    public function url_stat($path, $flags)
    {
        $this->createBuffer($path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }

    /**
     * Create the buffer by requesting the url through cURL
     *
     * @param unknown_type $path
     */
    private function createBuffer($path)
    {
        if ($this->buffer) {
            return;
        }
        $this->ch = curl_init($path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($this->ch, CURLOPT_USERPWD, USERPWD);
        $this->buffer = curl_exec($this->ch);
        $this->pos = 0;
    }
}


class NTLMSoapClient extends SoapClient
{
    function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $headers = array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
            'soap_version: SOAP_1_2'
        ); 

        $this->__last_request_headers = $headers;

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, USERPWD);
        $response = curl_exec($ch);        

        print_r($response);

        return $response;
    }

    function __getLastRequestHeaders()
    {
        return implode("\n", $this->__last_request_headers) . "\n";
    }
}

// we unregister the current HTTP wrapper
stream_wrapper_unregister('http');

// we register the new HTTP wrapper
stream_wrapper_register('http', 'NTLMStream') or die("Failed to register protocol");

// Initialize Soap Client
$baseURL = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
$soap = $client = new NTLMSoapClient($baseURL);

$order_id = $_POST['order_id'];


// Get an instance of the WC_Order object
$order = wc_get_order( $order_id );
$order_data = $order->get_data();

// echo '<pre>';
// print_r($order->order_date);
// print_r($order);
// exit();

$headerArray = array('orderNo' => $order_id, 
                    'externalDocNo'=> '248147452', 
                    'sellToCustomerNo'=>'31786178',
                    'shippingAgent' => 'GLS',
                    'shippingAgentServiceCode' => 'GLS-SHOP',
                    'orderDate' => $order_data['date_created']->date('Y-m-d H:i:s'), 
                    'currency' => $order_data['currency'],
                    'phoneNo' => $order_data['billing']['phone'],
                    'email'=> $order_data['billing']['email'],
                    'yourReference' => $order_id
                );

$shipToAddress = array('name' => $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'],
                    'address1' => $order_data['shipping']['address_1'], 
                    'address2' => $order_data['shipping']['address_2'], 
                    'postalNo' => $order_data['shipping']['postcode'],
                    'city' => $order_data['shipping']['city'],
                    'county' => 'MN',
                    'country' => $order_data['shipping']['country'],
                    'contactName' => $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'],
                    'pakkeShopID' => $order_data['billing']['address_2']);

$j = 0;
foreach ($order->get_items() as $item_key => $item ) {

         $item_data    = $item->get_data();

         $product_name = $item_data['name'];
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


     $orderLine[$j] = array( 
                         'lineType' => 'Vare',
                         'itemNo' => $item->get_id(),
                         'vendorItemNo' => '10000',
                         'eANNo' => $product_sku,
                         'vendorCode' => '1011',
                         'itemName' => $product_name,
                         'quantity' => $quantity, 
                         'price' => $product_price,
                         'total' => $line_total);
     $j++;                    
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
    exit();

    $result = $client->__soapCall('CreateSalesOrder', array('parameters' => array('order' => $out) )  );
    // echo 'I am in try';
    
    
} catch(SoapFault $e) {
    // echo 'I am in catch';
    // echo '<pre>';
    // print_r($e);   
}
// exit();

stream_wrapper_restore('http');

header('Location: ' . $_SERVER['HTTP_REFERER']);