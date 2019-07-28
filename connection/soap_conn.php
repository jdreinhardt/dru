<?php
    //This page is not included on pages using the includes to prevent long load times on initial page call
    include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');

    $apiKey = readConfig("apiKeyDIVA");
    $divaInfo = json_decode(readConfig("divaConfig"), TRUE)[0];

    //WSDL location to be used for the SOAP connection
    $client = new SoapClient($divaInfo['diva_wsdl']);
    //API Endpoint if different from the WSDL location
    $client->__setLocation($divaInfo['diva_endpoint']);
?>