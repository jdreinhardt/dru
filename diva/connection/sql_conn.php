<?php
    //MySQL connection information. This is the root database that contains
    //all the tables used for DRU.
    $dbserver = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "dru";

    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>