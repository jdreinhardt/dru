<!DOCTYPE html>

<html>
    <title>DRU - Archive</title>
    <head>
        <?php
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
        ?>
    </head>
    <body>
        <?php navBar('archive'); ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">Coming Soon</h1>
        </div>

        <?php
            if (isset($_SESSION['SESSION_ID'])) {
                if (getSession($_SESSION['SESSION_ID'])['Archive'] != "true" && getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                    echo "<div align=\"center\">";
                    die("Access Denied");
                    echo "</div>";
                }
            } else {
                echo "<div align=\"center\">";
                die("Must be logged in to archive");
                echo "</div>";
            }

            if (readConfig('allowArchives') != 'true' && getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                echo "<div align=\"center\">";
                die("Archives have been disabled by an Admin");
                echo "</div>";
            }
        ?>
</html>