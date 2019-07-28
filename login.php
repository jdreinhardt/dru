<!DOCTYPE html>

<html>
    <head>
        <title>DRU - Login</title>
        <?php   
            //Configuration files
            include_once 'assets/session_header.php';
            include 'connection/logger.php';

            //Stylistic files
            include_once 'assets/headers/links.php';
            include_once 'assets/headers/scripts.php';
            include_once 'assets/headers/input_style.php';

            //Page specific
            include 'auth/authenticate.php';
            include_once 'assets/headers/navbar.php';
        ?>
    </head>

    <body>

        <?php
        navBar('login');

        // check to see if user is logging out
        if(isset($_GET['out'])) {
            // destroy session
            deleteSession($_SESSION['SESSION_ID']);
            session_unset();
            $_SESSION = array();
            unset($_SESSION['SESSION_ID'],$_SESSION['USER'], $_SESSION['LAST_ACTIVITY']);
            session_destroy();
            $return = "";
            if (isset($_GET['objectCategory'])) { 
                $return = $_GET['redirect'] . "&objectCategory=" . $_GET['objectCategory']; 
            } else { 
                $return = $_GET['redirect']; 
            }
            //returns to search if logging out from restore page
            if (strpos($return, 'restore') !== FALSE ) {
                $return = "/diva/search";
            }
            header( "Location: " . $return );
        }

        // check to see if login form has been submitted
        if(isset($_POST['userLogin'])){
            // run information through authenticator
            if(authenticate($_POST['userLogin'],$_POST['userPassword']))
            {
                // authentication passed
                header( "Location: " . trim($_POST['redirect'], " \t\n\r\0\x0B") );
                die();
            } else {
                // authentication failed
                $error = 1;
            }
        }

        // output error to user
        if(isset($error)) { ?>
            <div align="center" style="width: 100%;"><p style="color: red;">Login failed: Incorrect username, password, or insufficient rights<br /></div>
        <?php }

        // output logout success
        if(isset($_GET['out'])) { ?>
            <div align="center" style="width: 100%;"><p>Logout successful<br /></div>
        <?php }
        ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">Login</h1>
        </div>
        <div align="center" style="width: 100%;">
            <form action="login.php" method="post">
                <p align="center" style="margin-bottom: 2px;">Username</p>
                <input autofocus="autofocus" style="width: 30%; margin-bottom: 10px;" autocomplete="off" type="text" name="userLogin" /><br />
                <p align="center" style="margin-bottom: 2px;">Password</p>
                <input style="width: 30%;" type="password" name="userPassword" />
                <input type="hidden" name="redirect" value="<?php 
                    if (!empty($_GET['redirect'])) { 
                        if (isset($_GET['objectCategory'])) { 
                            echo $_GET['redirect'] . "&objectCategory=" . $_GET['objectCategory']; 
                        } else { echo $_GET['redirect']; } } else { echo "/diva/"; 
                    } ?>" />
                <p align="center"><input type="submit" name="submit" value="Submit" /></p>
            </form>
        </div>
    </body>
</html>