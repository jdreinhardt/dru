<?php
    include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

    $sql = "DELETE FROM `site_tracker` WHERE `visit_time` < CURRENT_DATE - INTERVAL 60 DAY";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
    $stmt->close();
    $conn->close();
?>