<?php
    $time = $_SERVER['REQUEST_TIME'];
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        if ($time - $_SESSION['LAST_ACTIVITY'] > 1800) {
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/authorize.php');
            logout();
        } else {
            $_SESSION['LAST_ACTIVITY'] = $time;
        }
    }
?>