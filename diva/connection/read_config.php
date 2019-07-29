<?php
    function readConfig($parseKey) {
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $sql = "SELECT `config_key` as 'key', `config_value` as 'value' FROM `adm_config` WHERE `config_key` LIKE ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $key);

        if ($parseKey == 'all') {
            $key = '%';
        } else {
            $key = $parseKey;
        }

        $stmt->execute();
        echo $stmt->error;
        $results = $stmt->get_result();
        $stmt->close();
        $conn->close();

        $results_array = [];
        while ($row = $results->fetch_assoc()) {
            $results_array = array_push_assoc($results_array, $row['key'], $row['value']);
        }
        $results->free();
        if ( count($results_array) == 1 ) {
            return $results_array[$parseKey];
        } else {
            return $results_array;
        }
    }

    function array_push_assoc($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }
?>