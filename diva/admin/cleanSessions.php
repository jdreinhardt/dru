<?php
    include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

    $sql = "DELETE FROM `sessions` WHERE `session_start` < CURDATE()-7";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
    $stmt->close();
    $conn->close();
?>