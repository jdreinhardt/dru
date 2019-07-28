<?php
    include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

    $sql = "UPDATE `adm_config` SET config_value = ? WHERE config_key = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $value, $key);

    $value = $_POST['value'];
    $key = $_POST['key'];

    $stmt->execute();
    echo $stmt->error;
    $stmt->close();
    $conn->close();
?>