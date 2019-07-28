<!DOCTYPE html>

<html>
    <head>
        <title>DRU - Restore</title>
        <?php   
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');

            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/soap_conn.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/restore/restore_func.php');
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
            $allowRestore = readConfig('allowRestore');

            if ( isset($_GET['requestID']) ) { 
                if ( !isset($_SESSION['restoring']) ) {
                    $_SESSION['restoring'] = 'TRUE';
                }
                if ($_SESSION['restoring'] == 'TRUE') {
                    echo "<meta http-equiv=\"refresh\" content=\"30\" >";
                } else {
                    unset($_SESSION['restoring']);
                }
            }
        ?>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

        <script type="text/javascript">
            function validate() {
                document.getElementById("submit").style.display = "none"; // to undisplay
                document.getElementById("buttonreplacement").style.display = "";
            }
        </script>
    </head>

    <body>
        <?php navBar('restore'); ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">Object Restore</h1>
        </div>

        <?php
            if(!isset($_SESSION['SESSION_ID'])) {
                // user is not logged in, do something like redirect to login.php
                header("Location: ../login.php?redirect=" . $_SERVER['REQUEST_URI']);
                die();
            }

            if (getSession($_SESSION['SESSION_ID'])['Restore'] != "true" && getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                echo "<div align=\"center\">";
                die("Access Denied");
                echo "</div>";
            }

            //Admins can always restore. Limit others to admin restriction
            if ( !isset($_SESSION['SESSION_ID']) ) {
                if (getSession($_SESSION['SESSION_ID'])['Restore'] == "true" && getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                    if ($allowRestore != 'true') {
                        echo "<div align=\"center\">";
                        die("Restores have been disabled by an Admin");
                        echo "</div>";
                    }
                }
            }

            if (!empty($_GET)) { 
                $archiveID = "";
                $objectCategory = "";
                $restorePath = "";
                $requestID = "";

                if ( isset($_GET['requestID']) ) { //get status of request
                    $requestID = $_GET['requestID'];
                    $_SESSION['restoring'] = '';

                    $restorePath = "";
                    if ( isset($_GET['restorePath']) ) {
                        $restorePath = $_GET['restorePath'];
                    }
                    
                    $listParams = array(
                        'sessionCode' => $apiKey,
                        'requestNumber' => $requestID,
                    );

                    $response = $client->getRequestInfo($listParams);

                    $reqState = "";
                    $stateTemp = $response->return->divaRequestInfo->requestState;
                    
                    switch ($stateTemp) {
                        case '3':
                            $reqState = "Completed Successfully";
                            $_SESSION['restoring'] = 'FALSE';
                            break;
                        case '4':
                            $reqState = "Aborted";
                            $_SESSION['restoring'] = 'FALSE';
                            break;
                        case '5':
                            $reqState = "Cancelled";
                            $_SESSION['restoring'] = 'FALSE';
                            break;
                        case '6':
                            $reqState = "Unknown";
                            break;
                        case '11':
                            $reqState = "Partially Aborted";
                            $_SESSION['restoring'] = 'FALSE';
                            break;
                        case '12':
                            $reqState = "Running";
                            unset($_SESSION['restoring']);
                            break;
                    }

                    $reqType = "";
                    $typeTemp = $response->return->divaRequestInfo->requestType;
                    switch ($typeTemp) {
                        case '0':
                            $reqType = "Archive";
                            break;
                        case '1':
                            $reqType = "Restore";
                            break;
                        case '5':
                            $reqType = "Copy";
                            break;
                    }

                    echo "<div align=\"center\" style=\"width: 100%;\">";
                    echo "<br><h2>" . $reqType . " Status</h2>";
                    if ($response->return->divaRequestInfo->abortionReason->code != '0') {
                        echo "<h2 style=\"color: red\">ERROR</h2>";
                        echo "<pstyle=\"color: red\">" . $response->return->divaRequestInfo->abortionReason->description . "</p>";
                    }
                    echo "<h3>Object Title (Archive ID)</h3>";
                    echo "<p>" . $response->return->divaRequestInfo->objectSummary->objectName . "</p>";
                    echo "<h3>Object Category</h3>";
                    echo "<p>" . $response->return->divaRequestInfo->objectSummary->objectCategory . "</p>";
                    if ( $restorePath != "") {
                        echo "<h3>Restore Path</h3>";
                        echo "<p>" . $restorePath . "</p>";
                    }
                    echo "<h3>Process State</h3>";
                    echo "<p>" . $reqState . "</p>";
                    $completePercentage = $response->return->divaRequestInfo->progress;
                    echo "<div class=\"container\"><div class=\"progress\" style=\"width: 80%;\"><div class=\"progress-bar progress-bar-striped progress-bar-info\" style=\"width: " . $completePercentage . "%\">" . $completePercentage . "%</div></div></div>";
                    if ($stateTemp != '3') {
                        echo "<p><input type=\"button\" onclick=\"location.href='index.php?cancelID=" . urlencode($requestID) . "'\" value=\"Cancel Restore\" />";
                    }
                    echo "</div>";

                } elseif (isset($_GET['cancelID']) ) {
                    // Request to cancel 
                    $listParams = array(
                        'sessionCode' => $apiKey,
                        'requestNumber' => $_GET['cancelID'],
                    );

                    $response = $client->cancelRequest($listParams);

                    echo "<div align=\"center\">Cancel request successfully sent</div>";
                } else { //submit restore request to DIVA
                    if ( isset($_GET['restorePath']) ) {
                        if ( isset($_GET['archiveID']) ) {
                            $archiveID = urldecode($_GET['archiveID']);
                        }
                        if ( isset($_GET['objectCategory']) ) {
                            $objectCategory = $_GET['objectCategory'];
                        }
                        if ( isset($_GET['restorePath']) ) {
                            if ( substr($archiveID, -4, 1 ) != '.' ) {
                                $restorePath = $_GET['restorePath'] . "\\" . $archiveID . "\\";
                            } else {
                                $restorePath = $_GET['restorePath'] . "\\dru_restores\\";                                
                            }
                        }
                        if ($archiveID == "" || $objectCategory == "" || $restorePath == "") {
                            echo "<div align=\"center\">";
                            die("Improperly formatted request");
                            echo "</div>";
                        }

                        $listParams = array(
                            'sessionCode' => $apiKey,
                            'objectName' => $archiveID,
                            'objectCategory' => $objectCategory,
                            'destination' => 'dru_restores',
                            'filesPathRoot' => $restorePath,
                            'qualityOfService' => '',
                            'priorityLevel' => '50',
                            'restoreOptions' => '',
                        );

                        $response = $client->restoreObject($listParams);

                        //request submitted successfully. View status?
                        $requestID = $response->return->requestNumber;
                        echo "<div align=\"center\">";
                        echo "<h4>Request successfully submitted</h4>";
                        echo "<p><input type=\"button\" onclick=\"location.href='index.php?requestID=" . urlencode($requestID) . "&restorePath=" . urlencode($restorePath) . "'\" value=\"View Status\" />"; 
                        echo "</div>";
                    } else { //set restore path and send to submit
                        if ( isset($_GET['archiveID']) ) {
                            $archiveID = urldecode($_GET['archiveID']);
                        }
                        if ( isset($_GET['objectCategory']) ) {
                            $objectCategory = $_GET['objectCategory'];
                        }

                        if ($archiveID == "" || $objectCategory == "") {
                            echo "<div align=\"center\">";
                            die("Improperly formatted request");
                            echo "</div>";
                        }

                        $restorePathsArray = readConfig('restorePaths');
                        $restorePathsNames = [];
                        $restorePathsPaths = [];

                        $obj = json_decode($restorePathsArray);
                        for ($i=0; $i<count($obj); $i++) {
                            $vars = get_object_vars($obj[$i][0]);
                            array_push($restorePathsNames, $vars['Name']);
                            array_push($restorePathsPaths, $vars['Path']);
                        }
                        restoreOptions($archiveID, $objectCategory, $restorePathsNames, $restorePathsPaths);
                    }
                }
            } else {
                echo "<div align=\"center\">No object information found</div>";
            }

        ?>


    </body>
</html>