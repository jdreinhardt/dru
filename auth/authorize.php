<?php
    //functions to simplify permission and authorization checking

    function setSession($session_id, $permissions, $ipAddress) {
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $sql = "INSERT INTO `sessions`(`session_id`, `permissions`, `ip_address`) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $sessId, $perms, $ipAdd);
        
        $sessId = $session_id;
        $perms = json_encode($permissions);
        $ipAdd = $ipAddress;

        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    function getSession($sessionId) {
        //returns an array of permissions associated with the requested session id
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $sql = "SELECT `permissions` FROM `sessions` WHERE `session_id` =  ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sessId);
        
        $sessId = $sessionId;

        $stmt->execute();
        $stmt->bind_result($key);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        return json_decode($key, TRUE);
    }

    function deleteSession($sessionId) {
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/sql_conn.php');

        $sql = "DELETE FROM `sessions` WHERE `session_id` =  ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sessId);
        
        $sessId = $sessionId;

        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    function logout() {
        // destroy session
        deleteSession($_SESSION['SESSION_ID']);
        session_unset();
        $_SESSION = array();
        unset($_SESSION['SESSION_ID'],$_SESSION['USER'], $_SESSION['LAST_ACTIVITY']);
        session_destroy();

        header( "Location: /diva/search/" );
    }
?>