<?php
    //Connection information for the SQL database
    include_once 'sql_conn.php';

    //Check if a referer page is set, if not, then use blank data in the insert
    $referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "not set";

    //Grab all data submitted to the page and add it to the insert string
    $postgetData = "";
    if ( !empty($_POST) ) {
        if ( strpos($_SERVER['PHP_SELF'], 'login') === FALSE ) {
            $postgetData .= "POST: " . print_r($_POST, TRUE) . " ";
        }
    }
    if ( !empty($_GET) ) {
        $postgetData .= "GET: " . print_r($_GET, TRUE);
    }

    //Submit user information to database if user is signed in.
    $sql = "INSERT INTO `site_tracker`(`page_visited`, `page_referer`, `submitted_data`, `browser`, `ip_address`, `user`) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $pgVisited, $pgReferer, $pgData, $browser, $ipAddress, $user);
    
    $pgVisited = $_SERVER['PHP_SELF'];
    $pgReferer = $referer;
    $pgData = $postgetData;
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if ( isset($_SESSION['USER']) ) {
        $user = $_SESSION['USER'];
    } else {
        $user = NULL;
    }

    $stmt->execute();
    $stmt->close();
    //if ( $conn->query($sql) === TRUE ) { } else { echo "Error: " . $conn->error; }
    $conn->close();
?>