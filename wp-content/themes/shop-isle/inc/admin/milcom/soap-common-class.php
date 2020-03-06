<?php
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

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope
                                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                                <soap:Body>
                                    <CreateSalesOrder xmlns="urn:microsoft-dynamics-schemas/codeunit/NewNordicHome">
                                        <order>
                                            <header xmlns="urn:microsoft-dynamics-nav/xmlports/x50031">
                                                <orderNo>test159</orderNo>
                                                <externalDocNo>OrderRef3</externalDocNo>
                                                <sellToCustomerNo>12665546</sellToCustomerNo>
                                                <shippingAgent>POSTDK</shippingAgent>
                                                <shippingAgentServiceCode></shippingAgentServiceCode>
                                                <orderDate>07162019</orderDate>
                                                <currency>DKK</currency>
                                                <phoneNo>12665546</phoneNo>
                                                <email>info@test.dk</email>
                                                <yourReference>customerReference</yourReference>
                                            </header>
                                            <shipToAddress xmlns="urn:microsoft-dynamics-nav/xmlports/x50031">
                                                <name>Company Z</name>
                                                <address1>Algade 17</address1>
                                                <address2></address2>
                                                <postalNo>1234</postalNo>
                                                <city>KBH</city>
                                                <county />
                                                <country>DK</country>
                                                <contactName>Tine Svendsen</contactName>
                                                <pakkeShopID>1</pakkeShopID>
                                            </shipToAddress>
                                            <orderLineList xmlns="urn:microsoft-dynamics-nav/xmlports/x50031">
                                                <orderLine>
                                                    <lineType>Item</lineType>
                                                    <itemNo>test3</itemNo>
                                                    <itemName>test name</itemName>
                                                    <quantity>1</quantity>
                                                    <price>110</price>
                                                    <total>100</total>
                                                </orderLine>
                                            </orderLineList>
                                        </order>
                                    </CreateSalesOrder>
                                </soap:Body>
                            </soap:Envelope>';

    // [message:protected] => Imported XML cannot validate with the schema: The element 'orderLineList' in namespace 'urn:microsoft-dynamics-nav/xmlports/x50031' has incomplete content. List of possible elements expected: 'orderLine' in namespace 'urn:microsoft-dynamics-nav/xmlports/x50031'.

        $this->__last_request_headers = $headers;

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
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
stream_wrapper_restore('http');