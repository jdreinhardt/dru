<?php
    //function that accepts a string of active page.
    //Draws Nav Bar on page with active page higlighted
    function navBar($activePage) {
        include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/authorize.php');
        // update activity counter or logout depending on idle time
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/timeout.php');

        $admin = '';
        $search = '';
        $archive = '';
        $export = '';
        $detail = '';
        $restore = '';
        $dashboard = '';

        switch($activePage) {
            case 'admin':
                $admin = 'active';
                break;
            case 'search':
                $search = 'active';
                break;
            case 'archive':
                $archive = 'active';
                break;
            case 'export':
                $export = 'active';
                break;
            case 'detail':
                $detail = 'active';
                break;
            case 'restore':
                $restore = 'active';
                break;
            case 'dashboard':
                $dashboard = 'active';
                break;
            default:
                break;
        }; ?>

        <ul class="header">
            <li class="header"><a class="header home">
                <img src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/images/dru.png" class="home"></a></li>
            <li class="header">
                <a class="header <?= $search ?> padded" href="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/search">Search</a></li>
            <?php
                include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
                if ( readConfig("allowArchives") == "true" || (isset($_SESSION['SESSION_ID']) && getSession($_SESSION['SESSION_ID'])['Admin'] == "true" )) { 
                    //user must be signed in to archive content. 
                    //TODO: change this to a permission check 
                    if ( isset($_SESSION['SESSION_ID']) && getSession($_SESSION['SESSION_ID'])['Archive'] == "true" ) { ?>
                        <li class="header">
                            <a class="header <?= $archive ?> padded" href="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/archive">Archive</a></li>
                <?php } } ?>
            <li class="header">
                <a class="header <?= $export ?> padded" href="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/reports">Reports</a></li>
            <li class="header">
                <a class="header <?= $dashboard ?> padded" href="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/dashboard">Dashboard</a></li>

        <?php
        //Do no display login/logout or Admin buttons on the login page
        if (strpos($_SERVER['PHP_SELF'], 'login') === FALSE) {
            if(isset($_SESSION['SESSION_ID'])) {
                echo "<li style=\"float:right\" class=\"header\">
                    <a class=\"header padded\" href=\"http://" . $_SERVER['SERVER_NAME'] . '/diva' . "\login.php?out&redirect=" . $_SERVER['REQUEST_URI'] . "\">" . "Logout" . "</a></li>";
                    echo "<li class=\"header\" style=\"float:right\"><a class=\"header padded user\">" . $_SESSION['USER'] . "</a></li>";
                if(getSession($_SESSION['SESSION_ID'])['Admin'] == "true") {
                    echo "<li class=\"header\"><a class=\"header " . $admin . " padded\" href=\"http://" . $_SERVER['SERVER_NAME'] . '/diva' . "/admin\">Admin</a></li>";
                }
            } else {
                echo "<li style=\"float:right\" class=\"header\"><a class=\"header padded\" href=\"http://" . $_SERVER['SERVER_NAME'] . '/diva' . "\login.php?redirect=" . $_SERVER['REQUEST_URI'] . "\">Login</a></li>";
            }
        }
        //If called from detail view then display restore option if authenticated
        // if ($detail != '') {
        //     if ( isset($_GET['archiveID']) ) {
        //         if(isset($_SESSION['SESSION_ID'])) {
        //             if( readConfig("allowRestores") == "true" && getSession($_SESSION['SESSION_ID'])['Restore'] == "true" ) {
        //                 echo "<li style=\"float:right\" class=\"header\">
        //                     <a class=\"header padded\" href=\"http://" . $_SERVER['SERVER_NAME'] . '/diva' . "/restore/index.php?archiveID=" . urlencode($_GET['archiveID']) . "&objectCategory=" . urlencode($_GET['objectCategory']) . "\" target=\"_blank\">Restore</a></li>";
        //             }
        //         }
        //     }
        // } 
        echo "</ul>";

    }
?>