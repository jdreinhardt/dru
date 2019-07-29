<?php
    if (isset($_GET['f'])) {
        if(function_exists($_GET['f'])) { // get function name and parameter  
            $value = $_GET['f']();
            print $value;
        }
    }

    function getCurrentKey() { 
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $sql = "SELECT config_value FROM `adm_config` WHERE config_key = ?";

        $config_key = "apiKeyDIVA";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $config_key);    
        $stmt->execute();
        echo $stmt->error;
        $stmt->bind_result($key);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        return $key;
    }

?>