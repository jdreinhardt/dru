<!DOCTYPE html>

<html>
    <head>
        <title>DRU - Admin</title>
        <?php   
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/assets/headers/includes.php');

            //Page specific
            include 'logGenerator.php';
            include 'adminTemplates.php';
            include 'troubleshoot.php';
            include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
        ?>
        <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/stylesheets/admin.css">
        <script src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/javascript/admin.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        
        <?php
            if(!isset($_SESSION['SESSION_ID'])) {
                // user is not logged in, do something like redirect to login.php
                header("Location: ../login.php?redirect=" . $_SERVER['REQUEST_URI']);
                die();
            }
        ?>
    </head>

    <body style="min-height: 100%;">
        <?php navBar('admin'); ?>

        <div align="center" style="width: 100%;">
            <h1 class="title">DRU Administration</h1>
        </div>

        <?php
            if(getSession($_SESSION['SESSION_ID'])['Admin'] != "true") {
                echo "<div align=\"center\">";
                die("Access Denied");
                echo "</div>";
                die();
            }

            $configs = readConfig('all');
            //var_dump($configs);

            $filePath = "";
            if ( isset($_POST['log'])) {
                $filePath = generateLog($_POST['log']);
            } 
        ?>

        <div class="tab">
            <button class="tablinks" onclick="openConfigPane(event, 'Status')">Dashboard</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Features')">Features</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Paths')">Restore Paths</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Groups')">Access Control</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Directory')">Active Directory</button>
            <button class="tablinks" onclick="openConfigPane(event, 'DIVA')">DIVA URL</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Logs')">Logs</button>
            <button class="tablinks" onclick="openConfigPane(event, 'Troubleshooting')">Troubleshooting</button>
        </div>

        <!-- Content -->
        <div align="center" style="width:100%; height: 600px;">
            <!-- Status Section -->
            <div id="Status" class="tabcontent" style="display: block;">
                <br>
                <p>Status Dashboard</p>
            </div>

            <!-- Features Section -->
            <div id="Features" class="tabcontent" style="display: none;">
                <br>
                <?php 
                    $conf1_name = 'Allow Restores';
                    $conf1_desc = 'Allows access to object restore actions if signed in and enabled';
                    configParameter($conf1_name, $conf1_desc, 'allowRestores', $configs); 
                    $conf2_name = 'Allow Archives';
                    $conf2_desc = 'Allows access to object archive actions if signed in and enabled';
                    configParameter($conf2_name, $conf2_desc, 'allowArchives', $configs); 
                    $conf3_name = 'Login to Search';
                    $conf3_desc = 'Require user sign in to access search functionality';
                    configParameter($conf3_name, $conf3_desc, 'loginToSearch', $configs);
                    $conf4_name = 'Allow local Admin account';
                    $conf4_desc = 'Allows the defined local admin to login with full access';
                    configParameter($conf4_name, $conf4_desc, 'localAdmin', $configs);
                ?>
            </div>

            <!-- Paths Section -->
            <div id="Paths" class="tabcontent" style="display: none;">
                <br> 
                <div align="center" style="width: 90%;">
                    <div class="sp_wrapper" style="display: inline-block; float: center; width: 100%;">
                        <h3>Restores Paths</h3>
                        <div class="sp_table" style="width: 100%;">
                            <table id="restoreTable" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="39%">
                                    <col width="58%">
                                    <col width="50px">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">Location Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Location Path</th>
                                    <th style="text-align: left; padding-left: 10px;"></th>
                                </thead>
                            </table>
                            <button class="admin" id="addRow" onclick='addPathRow("restoreTable")'>Add Row</button>
                            <button class="admin" id="updateRestore" onclick="setRestorePathsToDB()">Save</button>
                            <?php
                                $restorePaths = $configs["restorePaths"];
                                echo '<script type="text/javascript">getRestorePathsFromDB(' . $restorePaths . ');</script>';
                            ?>
                            <br><br>
                            <h3>Group Restore Restrictions (NOT IMPLEMENTED)</h3>
                            <table id="restoreGroupRestrictions" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="29%">
                                    <col width="68%">
                                    <col width="50px">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">Group/User Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Authorized Locations</th>
                                    <th style="text-align: left; padding-left: 10px;"></th>
                                </thead>
                            </table>
                            <button class="admin" id="addRow" onclick='addPathRow("restoreGroupRestrictions")'>Add Row</button>
                            <button class="admin" id="updateRestore" onclick="setPathsRestrictionsToDB()">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div id="Groups" class="tabcontent" style="display: none;">
                <br>
                <div align="center" style="width: 90%;">
                    <div class="sp_wrapper" style="display: inline-block; float: center; width: 100%;">
                        <h3>Group Management</h3>
                        <div class="sp_table" style="width: 100%;">
                            <table id="groupTable" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="57%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="50px">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">Group Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Access</th>
                                    <th style="text-align: left; padding-left: 10px;">Details</th>
                                    <th style="text-align: left; padding-left: 10px;">Restore</th>
                                    <th style="text-align: left; padding-left: 10px;">Archive</th>
                                    <th style="text-align: left; padding-left: 10px;">Admin</th>
                                    <th style="text-align: left; padding-left: 10px;"></th>
                                </thead>
                            </table>
                            <button class="admin" id="addRow" onclick='addPermissionsRow("groupTable")'>Add Row</button>
                            <button class="admin" id="updateUsers" onclick='setPermissionsToDB("groupTable")'>Save</button>
                            <br><br>
                            <h3>User Management</h3>
                            <table id="usersTable" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="57%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="8%">
                                    <col width="50px">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">User Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Access</th>
                                    <th style="text-align: left; padding-left: 10px;">Details</th>
                                    <th style="text-align: left; padding-left: 10px;">Restore</th>
                                    <th style="text-align: left; padding-left: 10px;">Archive</th>
                                    <th style="text-align: left; padding-left: 10px;">Admin</th>
                                    <th style="text-align: left; padding-left: 10px;"></th>
                                </thead>
                            </table>
                            <button class="admin" id="addRow" onclick='addPermissionsRow("usersTable")'>Add Row</button>
                            <button class="admin" id="updateUsers" onclick='setPermissionsToDB("usersTable")'>Save</button>
                            <?php
                                $groupPermissions = $configs["groupPermissions"];
                                $userPermissions = $configs["userPermissions"];
                                echo '<script type="text/javascript">getPermissionsFromDB(' . $groupPermissions . ',"groupTable");</script>';
                                echo '<script type="text/javascript">getPermissionsFromDB(' . $userPermissions . ',"usersTable");</script>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AD Access Section -->
            <div id="Directory" class="tabcontent" style="display: none;">
                <div align="center" style="width: 90%;">
                    <div class="sp_wrapper" style="display: inline-block; float: center; width: 100%;">
                        <h3>Active Directory Management</h3>
                        <div class="sp_table" style="width: 100%;">
                            <table id="LDAPTable" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="29%">
                                    <col width="68%">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">Config Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Config Value</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>LDAP Host</td>
                                        <td><input type="text" id="ldap_host" style="width:98%"/></td>
                                    </tr>
                                    <tr>
                                        <td>LDAP DN</td>
                                        <td><input type="text" id="ldap_dn" style="width:98%"/></td>
                                    </tr>
                                    <tr>
                                        <td>LDAP Domain</td>
                                        <td><input type="text" id="ldap_domain" style="width:98%"/></td>
                                    </tr>
                            </table>
                            <button class="admin" id="updateLDAP" onclick="setLDAPConfigToDB()">Save</button>
                            <?php
                                $ldapConfig = $configs["ldapConfig"];
                                echo '<script type="text/javascript">getLDAPFromDB(' . $ldapConfig . ');</script>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DIVA Section -->
            <div id="DIVA" class="tabcontent" style="display: none;">
                <div align="center" style="width: 90%;">
                    <div class="sp_wrapper" style="display: inline-block; float: center; width: 100%;">
                        <h3>Active Directory Management</h3>
                        <div class="sp_table" style="width: 100%;">
                            <table id="LDAPTable" align="center" style="width: 90%">
                                <colgroup>
                                    <col width="29%">
                                    <col width="68%">
                                </colgroup>
                                <thead>
                                    <th style="text-align: left; padding-left: 10px;">Config Name</th>
                                    <th style="text-align: left; padding-left: 10px;">Config Value</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>DIVA WSDL</td>
                                        <td><input type="text" id="diva_wsdl" style="width:98%"/></td>
                                    </tr>
                                    <tr>
                                        <td>DIVA Endpoint</td>
                                        <td><input type="text" id="diva_endpoint" style="width:98%"/></td>
                                    </tr>
                            </table>
                            <button class="admin" id="updateLDAP" onclick="setDIVAConfigToDB()">Save</button>
                            <?php
                                $divaConfig = $configs["divaConfig"];
                                echo '<script type="text/javascript">getDIVAFromDB(' . $divaConfig . ');</script>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Section -->
            <div id="Logs" class="tabcontent" style="display: none;">
                <div>
                    <h3>Export Usage Logs</h3>
                    <p>Export all useage logs to a CSV file for analysis</p>
                    <div style="display: inline-block;">
                        <form action="" method="post">
                            <input type="hidden" name="log" value="today">
                            <input type="submit" id="log" value="Today" />
                        </form>
                    </div>
                    <div style="display: inline-block;">
                        <form action="" method="post">
                            <input type="hidden" name="log" value="month">
                            <input type="submit" id="log" value="This Month" />
                        </form>
                    </div>
                    <div style="display: inline-block;">
                        <form action="" method="post">
                            <input type="hidden" name="log" value="all">
                            <input type="submit" id="log" value="Everything..." />
                        </form>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Section -->
            <div id="Troubleshooting" class="tabcontent" style="display: none;">
                <br>
                <div align="center" style="width: 90%;">
                    <div style="display: inline-block; width: 80%; float: left; text-align: left;">
                        <?php 
                            $apiKeyDIVA = getCurrentKey();
                        ?>
                        <h3>Reset DIVA API Key</h3>
                        <p>Use when receiving errors related to "Invalid Session ID"<b id="divakey" style="font-size: 60%">(<?= $apiKeyDIVA ?>)</b></p>
                    </div>
                    <div style="display: inline-block; float: right;">
                    <br>
                        <button class="admin" id="api" onclick="refreshAPI();">Refresh</button>
                    </div>
                </div>
                <div align="center" style="width: 90%;">
                    <div style="display: inline-block; width: 80%; float: left; text-align: left;">
                        <h3>Delete Old Session IDs</h3>
                        <p>Use occasionally to remove stale sessions from the database</p>
                    </div>
                    <div style="display: inline-block; float: right;">
                    <br>
                        <button class="admin" id="api" onclick="cleanSessions();">Delete</button>
                    </div>
                </div>
                <div align="center" style="width: 90%;">
                    <div style="display: inline-block; width: 80%; float: left; text-align: left;">
                        <h3>Delete Old Usage Logs</h3>
                        <p>Will remove all usage stat logs older than 60 days from the database</p>
                    </div>
                    <div style="display: inline-block; float: right;">
                    <br>
                        <button class="admin" id="api" onclick="cleanSiteTracker();">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div>
            <p style="position: absolute; bottom: 0; font-size: 12px;">DRU version 1.2.0</p>
        </div>
            <?php
                if (isset($_POST['log'])) {
                    echo "<script type=\"text/javascript\">window.location = \"../" . $filePath . "\";</script>";
                }
            ?>
    </body>
</html>