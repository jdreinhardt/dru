<?php
    include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');
    include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');

    $client = new SoapClient($divaInfo['diva_wsdl']);
    $client->__setLocation($divaInfo['diva_endpoint']);

    $registerParams = array(
        'appName' => 'DRU',
        'locName' => 'dru.main.sessioncode',
        'processId' => '',
    );

    $response = $client->registerClient($registerParams);

    $key = $response->return;

    $sql = "UPDATE `adm_config` SET config_value = ? WHERE config_key = 'apiKeyDIVA'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);

    $stmt->execute();
    echo $stmt->error;
    $stmt->close();
    $conn->close();
?>