<!DOCTYPE html>

<html>
    <head>
        <title>DRU - <?php if ( isset($_GET['archiveID']) ) { echo $_GET["archiveID"]; } else { echo "Details"; } ?></title>
        <?php   
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');

            //Page specific
            include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/search/details/details_func.php');
        ?>
        <script src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/javascript/collapsibleLists.js"></script>

        <script type='text/javascript'>
            function collapseList() {
                CollapsibleLists.apply();
            }
        </script>

        <script type='text/javascript'>
            function filterLI() {
                $('input[type="text"]').keyup(function() {
                    var searchText = $(this).val().toLowerCase();
                    $('ul > li').each(function(){
                        var currentLiText = $(this).text().toLowerCase();
                        var isHeader = $(this).attr('class') == "header";
                        if (isHeader == false) {
                            var showCurrentLi = currentLiText.indexOf(searchText) !== -1;
                            $(this).toggle(showCurrentLi);
                        }
                    });     
                });
            }
        </script>
    </head>
    
    <body onload="collapseList(); filterLI()">

        <?php 
        
            navBar('detail'); 

            if (isset($_SESSION['SESSION_ID'])) {
                if (getSession($_SESSION['SESSION_ID'])['Details'] != "true" && getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                    echo "<div align=\"center\">";
                    die("Access Denied");
                    echo "</div>";
                }
            } else {
                echo "<div align=\"center\">";
                die("Must be logged in to view object details");
                echo "</div>";
            }

            if ( !isset($_GET['archiveID']) ) { ?>
                <div align="center" style="width: 100%;">
                    <h2 class="title">Oops. Something may have gone wrong.</h2>
                    <p>If you reached this page by accident please close the tab. 
                        If you think something is wrong then please contact your administrator.</p>
                </div> <?php 
                die();
            } else {
                include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/soap_conn.php');

                $detailParams = array(
                    'sessionCode' => $apiKey,
                    'objectName' => $_GET['archiveID'],
                    'objectCategory' => $_GET['objectCategory'],
                );

                $response = $client->getObjectInfo($detailParams);

                $obj = $response->return->info;
                $objName = $obj->objectSummary->objectName;
                $objCategory = $obj->objectSummary->objectCategory;
                $archiveDate = date("d-M-Y H:i:s", substr($obj->archivingDate, 0, 10));
                $objSize = number_format($obj->objectSize, 0, '.', ',');
                $objSD = $obj->objectSource;
                $objPath = $obj->rootDirectory;
                $objComments = $obj->objectComments;
                $objFiles = $obj->filesList; //array of files
            }
        ?>

        <div align="center" style="width: 100%;">
            <h2 class="title">Details for <?php echo $_GET["archiveID"]; ?></h2>
        </div>
        <div align="center" style="width: 100%;">
            <div align="left" style="float: left; padding-left: 30px; width: 40%;">
                <h3>Object Title (ArchiveID)</h3>
                <p class="wordwrap"><?php echo $objName ?></p>
                <h3>Object Category</h3>
                <p><?php echo $objCategory ?></p>
                <h3>Archive Date</h3>
                <p><?php echo $archiveDate ?></p>
                <h3>Archive Size</h3>
                <p><?php echo $objSize ?> kB</p>
                <h3>DIVA Source/Destination</h3>
                <p><?php echo $objSD ?></p>
                <h3>Archive Path</h3>
                <p class="wordwrap"><?php echo $objPath ?></p>
                <?php 
                    if ($objComments == " ") {
                    } else {
                        echo "\n\t\t\t<h3>Object Comments</h3>";
                        echo "\n\t\t\t<p>" . $objComments . "</p>";
                    }
                ?>
            </div>
            <div id="fileList" align="right" style="float: right; padding-right: 30px; width: 50%;">
                <div align="left">
                <?php
                    if(isset($_SESSION['SESSION_ID'])) {
                        if( ( readConfig("allowRestores") == "true" || getSession($_SESSION['SESSION_ID'])['Admin'] == "true" ) && getSession($_SESSION['SESSION_ID'])['Restore'] == "true" ) {?>
                            <div style="display:inline-block;" id="submit">
                                <form target="_blank" method="get" action="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/diva' . '/restore/index.php'?>">
                                    <input type="hidden" name="archiveID" value="<?php echo urlencode($_GET['archiveID'])?>">
                                    <input type="hidden" name="objectCategory" value="<?php echo urlencode($_GET['objectCategory'])?>">
                                    <input style="width: 125px" type="submit" value="Restore">
                                </form>
                                </div> <?php } } ?>
                    <h3>File List</h3>
                    <?php 
                        if (sizeof($objFiles) == 1) {
                            if (strpos($objFiles, 'ComponentContainer') !== FALSE) {
                                // object is complex and must be parsed differently
                                // get returns from getFilesAndFolders to generate object list and pass to tree builder
                                $objList = queryDIVAComplex($objName, $objCategory, $apiKey);
                                echo "<p>Search: <input type=\"text\" /></p>";
                                $tree = build_tree($objList);
                                $list = build_list($tree);
                                echo "<ul id=\"fileTree\" class=\"collapsibleList wordwrap\"><li>root" . $list . "</li></ul>";
                            } else {
                                echo "Total files: 1";
                                echo "<ul id=\"fileTree\" class=\"collapsibleList wordwrap\"><li>root<ul><li>" . $objFiles . "</li></ul></li></ul>";
                            }
                        } else {
                            echo "Total files: " . count($objFiles);
                            echo "<p>Search: <input type=\"text\" /></p>";
                            $tree = build_tree($objFiles);
                            $list = build_list($tree);
                            echo "<ul id=\"fileTree\" class=\"collapsibleList wordwrap\"><li>root" . $list . "</li></ul>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>