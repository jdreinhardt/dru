<!DOCTYPE html>

<html>
    <head>
        <title>DRU - Reports</title>
        <?php
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/authorize.php');

            //Page specific
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/reports/export_func.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
        ?>
        <script src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/javascript/export.js"></script>
        <?php
            if (!isset($_SESSION['SESSION_ID'])) {
                // user is not logged in, do something like redirect to login.php
                header("Location: ../login.php?redirect=" . $_SERVER['REQUEST_URI']);
                die();
            } elseif (getSession($_SESSION['SESSION_ID'])['Details'] != "true") {
                echo "<div align=\"center\">";
                die("Access Denied");
                echo "</div>";
            }
        ?>
    </head>
    <body>

        <?php
            navBar('export');
        ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">DIVA Ingested Records Export</h1>
        </div>
        <div align="center" style="width: 100%;">
            <form id="dates" action="index.php" method="post">
                <p style="font-size: 18px"><b>Enter Date Range</b></p>
                
                <p id="dateRange" style="width: 60%">Starting Date: <input type="text" id="datepicker1" name="startdate" style="width: 20%" value="<?php 
                    if (!empty($_POST["startdate"])) {
                        echo $_POST["startdate"];
                    } else {
                        echo "";
                    } ?>">  Ending Date: <input type="text" id="datepicker2" name="enddate" style="width: 20%" value="<?php 
                    if (!empty($_POST["enddate"])) {
                        echo $_POST["enddate"];
                    } else {
                        echo "";
                    } ?>"></p>

                <div style="width:50%; margins: 0 auto; text-align: center;">
                    <div style="display:inline-block;" id="submit">
                        <p><input type="button" onclick="validate();" value="Export"></p>
                    </div>
                    
                    <div id="buttonreplacement" style="display:none;">
                        <img src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/images/loading.gif" width="5%" height="5%" alt="loading...">
                    </div>
                <div>
            </form>
        </div>

        <?php
            date_default_timezone_set('GMT');
            //date_default_timezone_set('America/Denver');

            if ( !empty($_POST["startdate"]) ) { ?>
                <p id=\"text1\" align=\"center\" style=\"font-size: 24px\"><b>Please select a date range above.</b></p>
                <p align=\"center\">Requests on this page may take several minutes to complete. 
                    Be patient, and do not reload or close the page during a request.</p><br><br><br>

                <?php
                //End time will default to midnight of day selected. To get that day +1 day
                $epochStart = strtotime($_POST["startdate"]);
                $tempEnd = strtotime($_POST["enddate"]);
                $epochEnd = strtotime('+1 day', $tempEnd);

                $fileName = "DIVA_EXPORT_" . substr(str_replace('-', '', $_POST["startdate"]), 0, -4) . "-" . str_replace('-', '', $_POST["enddate"]) . ".csv";
                $apiKey = readConfig("apiKeyDIVA"); 
                $path = queryDIVA($epochStart, $epochEnd, $fileName, $apiKey);
                
                echo "<script type=\"text/javascript\">window.location = \"" . $path . "\";</script>"; 

            } else { ?>
                <p id=\"text1\" align=\"center\" style=\"font-size: 24px\"><b>Please select a date range above.</b></p>
                <p align=\"center\">Requests on this page may take several minutes to complete. 
                    Be patient, and do not reload or close the page during a request.</p><br><br><br>
            <?php }
        ?>

    </body>
</html>