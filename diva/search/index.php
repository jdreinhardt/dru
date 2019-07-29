<!DOCTYPE html>

<html>
    <head>
        <title>DRU - Search</title>
        <?php   
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/authorize.php');

            //Page specific
            include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/search/search_func.php');
        ?>
        <script src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/javascript/search.js"></script>
        <script src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/javascript/jQuery.sortpaginate.js"></script>

        <?php
            if(readConfig('loginToSearch') == "true") {
                if (!isset($_SESSION['SESSION_ID'])) {
                    // user is not logged in, do something like redirect to login.php
                    header("Location: ../login.php?redirect=" . $_SERVER['REQUEST_URI']);
                    die();
                } elseif (getSession($_SESSION['SESSION_ID'])['Access'] != "true") {
                    echo "<div align=\"center\">";
                    die("Access Denied");
                    echo "</div>";
                }
            }
        ?>
    </head>

    <body onload="sort(); wasAdvanced()">

        <?php navBar('search'); ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">DIVA Resource for Users</h1>
        </div>
        <div align="center" style="width: 100%;">
            <form id="search" action="index.php" method="post" onsubmit="validate();">
                <p style="font-size: 18px"><b>Enter Search Parameters</b></p>
                <p>
                    <input type="text" autofocus="autofocus" id="searchbox" name="searchterm" value="<?php if (!empty($_POST["searchterm"])) {echo $_POST["searchterm"];} else {echo "";} ?>">
                </p>
                <p id="advancedopts" style="display: none; width: 60%">
                    Object Category: <input type="text" id="categorybox" name="categoryterm" style="width: 30%" value="<?php if (!empty($_POST["categoryterm"])) {echo $_POST["categoryterm"];} else {echo "*";} ?>">
                    Starting Date: <input type="text" id="datepicker" class="datepicker" name="startdate" style="width: 20%" value="<?php if (!empty($_POST["startdate"])) {echo $_POST["startdate"];} else {echo "";} ?>"></p>
                <!--Hidden input that is used to store the date entered converted to unix epoch-->
                <p id="advancedopts" style="display: none"><input type="hidden" id="sindex" name="sindex" style="width: 30%" value="<?php if (!empty($_POST["sindex"])) {echo $_POST["sindex"];} else {echo "1";} ?>"></p>

                <div style="width:50%; margins: 0 auto; text-align: center;">
                    <div style="display:inline-block;" id="submit"><p><input type="submit" onsubmit="validate();" value="Search"></p></div>
                    <div style="display:inline-block;"><p><input id="advanced" type="button" onclick="advancedsearch();" value="Advanced Search"></p></div>
                
                    <div id="buttonreplacement" style="display:none;">
                        <img src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/images/loading.gif" width="3%" height="3%" alt="loading...">
                    </div>
                <div>
            </form>
        </div>
        </div>

        <?php 
            
            if ( !empty($_POST["searchterm"]) ) {
                //if is a search open connection to DIVA
                include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/soap_conn.php');

                //Size Stats
                echo "<div style=\"width: 100%; overflow: hidden;\">";
                echo "<div style=\"width: 100%; text-align: center; font-size: 15px;\">";

                $maxListSize = '1000';

                $startIndex = $_POST["sindex"];

                $temp = $_POST["searchterm"];
                if ($temp[0] != '*') {
                    if ($temp[0] != '?') {
                        $temp = '*' . $temp;
                    }
                }
                if ( $temp[strlen($temp)-1] != '*') {
                    if ( $temp[strlen($temp)-1] != '?') {
                        $temp = $temp . '*';
                    }
                }
                $searchterm = $temp;
                $categoryterm = $_POST["categoryterm"];
            
                $listParams = array(
                    'sessionCode' => $apiKey,
                    'isFirstTime' => 'true',
                    'initialTime' => $startIndex,
                    'listType' => '1',
                    'objectsListType' => '2',
                    'listPosition' => '',
                    'maxListSize' => $maxListSize,
                    'objectName' => $searchterm,
                    'objectCategory' => $categoryterm,
                    'mediaName' => '*',
                    'levelOfDetail' => '2',
                );

                $response = $client->getObjectDetailsList($listParams);

                $recordsFound = parseToTable($response);

                if ($recordsFound == 0) {
                    //Run additional search if special characters are encoded in non-ASCII
                    //If standard search returns 0, then attempt to convert special chars 
                    // to wildcard and run search again. 
                    $target = $searchterm;
                    $specialChars = ['@','!','.','-','_','?','#'];
                    foreach($specialChars as $char) {
                        $target = str_replace( $char, "*", $target );
                    }
                    //Only rerun search if converting to wildcards change the search term
                    if ( $target != $searchterm ) {
                        $listParams['objectName'] = $target;
                        $response = $client->getObjectDetailsList($listParams);
                        $recordsFound = parseToTable($response);
                    }
                } 

                $numberOfResults = sizeof($response->return->objectDetailsList->objectInfos);
                if ($numberOfResults == $maxListSize) {
                    echo $recordsFound . "+ results found (consider refining query)";
                } else {
                    echo $recordsFound . " results found";
                }

                echo "\n\t\t</table>\n\t</div></div>\n<br>";
            } else { ?>
                <p align="center" style="font-size: 24px"><b>Please enter a search above</b></p>
                <p align="center">Queries entered above are searching against the DIVA database. 
                    Queries are case sensitive. Wildcards are supported, but not required.</p><br><br><br>
            <?php }
        ?>
    </body>
</html>