<?php

    function generateLog($time) {
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $today = getdate();
        $filePath = "";
        $sql = "";

        if ($time == 'today') {
            $filePath = "/var/www/html/diva/logs/Usage_" . $today['mday'] . "-" . $today['mon'] . "-" . $today['year'] . ".csv";
            $sql = "SELECT * FROM site_tracker WHERE visit_time >= CURDATE() INTO OUTFILE '" . $filePath . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";
        }
        if ($time == 'month') {
            $filePath = "/var/www/html/diva/logs/Usage_" . $today['month'] . ".csv";
            $sql = "SELECT * FROM site_tracker WHERE visit_time >= CURDATE()-" . $today['mday'] . " INTO OUTFILE '" . $filePath . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";
        }
        if ($time == 'all') {
            $filePath = "/var/www/html/diva/logs/Usage_all.csv";
            $sql = "SELECT * FROM site_tracker INTO OUTFILE '" . $filePath . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";
        }

        clearstatcache();
        if (file_exists($filePath)) {
            unlink($filePath);
        } 

        if ( $conn->query($sql) === TRUE ) { } else { echo "Error: " . $conn->error; }
        $conn->close();

        return substr($filePath, 19);
    }
?>