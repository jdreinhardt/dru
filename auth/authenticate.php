<?php
    function generate_guid() {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    function authenticate($user, $password) {
        include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
        include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/auth/authorize.php');

        if(empty($user) || empty($password)) return false;

        // get configs from database
        $ldapConfig = json_decode( readConfig('ldapConfig'), true )[0];
        $groupPermissions = json_decode( readConfig('groupPermissions'), true );
        $userPermissions = json_decode( readConfig('userPermissions'), true );

        // active airectory server
        $ldap_host = $ldapConfig['ldap_host'];

        // active directory DN (base location of ldap search)
        $ldap_dn = $ldapConfig['ldap_dn'];

        // domain, for purposes of constructing $user
        $ldap_usr_dom = $ldapConfig['ldap_domain'];

        // get list of all authorized active directory groups
        $groups = [];
        foreach($groupPermissions as $group) {
            array_push($groups, $group['Group Name']);
        }

        // get list of specific authorized users
        $users = [];
        foreach($userPermissions as $usr) {
            array_push($users, $usr['User Name']);
        }

        // connect to active directory
        $ldap = ldap_connect($ldap_host);

        // configure ldap params
        ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($ldap,LDAP_OPT_REFERRALS,0);

        // verify user and password
        if($bind = @ldap_bind($ldap, $user.$ldap_usr_dom, $password)) {
            // valid
            // check presence in groups
            $filter = "(sAMAccountName=".$user.")";
            $attr = array("memberof");
            $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
            $entries = ldap_get_entries($ldap, $result);
            ldap_unbind($ldap);

            // check if single user
            $useUser = false;
            foreach($users as $usr) {
                if ($user == $usr) {
                    $useUser = true;
                    break;
                }
            }

            // check groups
            $memberof = [];
            foreach($entries[0]['memberof'] as $member) {
                foreach($groups as $group) {
                    if (strpos($member, $group)) {
                        array_push($memberof, $group);
                    }
                }
            }

            $permissions = json_decode('{}');
            if ($useUser) {
                //get user permissions
                foreach($userPermissions as $usr) {
                    if ($usr['User Name'] == $user) {
                        unset($usr['User Name']);
                        $permissions = $usr;
                    }
                }
            } else {
                //get group permissions
                $calculatedPermission = array(
                    "Access" => "true",
                    "Details" => "true",
                    "Restore" => "true",
                    "Archive" => "true",
                    "Admin" => "true"
                );
                $arrayKeys = array_keys($calculatedPermission);
                foreach($groupPermissions as $grpPerm) {
                    foreach($memberof as $grp) {
                        if ($grpPerm['Group Name'] == $grp) {
                            foreach($arrayKeys as $akey) {
                                if ($grpPerm[$akey] != $calculatedPermission[$akey]) {
                                    // model of least priviledge
                                    $calculatedPermission[$akey] = "false";
                                }
                            }
                        }
                    }
                }
                $permissions = $calculatedPermission;
            }
            
            if(count($permissions) > 0) {
                $sessionID = generate_guid();
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                setSession($sessionID, $permissions, $ipAddress);

                // establish session variables
                $_SESSION['USER'] = $user;
                $_SESSION['SESSION_ID'] = $sessionID;
                $_SESSION['LAST_ACTIVITY'] = $_SERVER['REQUEST_TIME'];
                return true;
            } else {
                // user has no rights
                return false;
            }
        } else {
            if (readConfig('localAdmin') == "true") {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/admin/adminUser.php')) {
                    include_once($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/admin/adminUser.php');
                    if ($user == $admUsr && $password == $admPwd) {
                        $sessionID = generate_guid();
                        $permissions = array(
                            "Access" => "true",
                            "Details" => "true",
                            "Restore" => "true",
                            "Archive" => "true",
                            "Admin" => "true"
                        );
                        $ipAddress = $_SERVER['REMOTE_ADDR'];
                        setSession($sessionID, $permissions, $ipAddress);

                        // establish session variables
                        $_SESSION['USER'] = $user;
                        $_SESSION['SESSION_ID'] = $sessionID;
                        $_SESSION['LAST_ACTIVITY'] = $_SERVER['REQUEST_TIME'];
                        return true;
                    }
                } else {
                    echo "No local admin defined";
                    return false;
                }
            } else {
            // invalid name or password
            return false;
            }
        }
    }
?>