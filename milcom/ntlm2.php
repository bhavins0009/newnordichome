<?php
require('../wp-load.php');
include_once('soap-common-class.php');

// Initialize Soap Client
$baseURL = 'http://83.91.84.146:7049/DynamicsNAV/WS/7000%20New%20Nordic%20Home/Codeunit/NewNordicHome';
$client = new NTLMSoapClient($baseURL);