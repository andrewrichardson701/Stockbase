<?php
// print_r($_POST);
$redirect_url = '../profile.php';

if (isset($_POST['submit']) && $_POST['submit'] == 'Re-sync') {
    if (isset($_POST['password']) && $_POST['password'] !== '') {
        include '../session.php';
        include 'get-config.inc.php';

        // Check if the login type is LDAP or local
        if ($loggedin_auth == 'ldap') {
            
            include 'dbh.inc.php';
            $sql_ldap = "SELECT * FROM users WHERE username=?";
            $stmt_ldap = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_ldap, $sql_ldap)) {
                header("Location: ".$redirect_url."?resyncError=usersTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_ldap, "s", $loggedin_username);
                mysqli_stmt_execute($stmt_ldap);
                $result_ldap = mysqli_stmt_get_result($stmt_ldap);
                $rowCount_ldap = $result_ldap->num_rows;
                if ($rowCount_ldap < 1) {
                    header("Location: ".$redirect_url."?resyncError=noUserMatchInTable");
                    exit();
                } elseif ($rowCount_ldap > 1) {
                    header("Location: ".$redirect_url."?resyncError=multipleUserMatchesInTable");
                    exit();
                } else {
                    // only one match - as expected
                    $row_ldap = $result_ldap->fetch_assoc();
                    $user_id = $row_ldap['id'];


                    // DO LDAP SEARCH
                    
                    $sql_ldap_d = "SELECT * FROM config_default WHERE id=1";
                    $stmt_ldap_d = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_ldap_d, $sql_ldap_d)) {
                        header("Location: ../profile.php?sqlerror=getLDAPconfigDefault");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_ldap_d);
                        $result_ldap_d = mysqli_stmt_get_result($stmt_ldap_d);
                        $rowCount_ldap_d = $result_ldap_d->num_rows;
                        if ($rowCount_ldap_d < 1) {
                            header("Location: ../profile.php?sqlerror=missingConfigDefault");
                            exit(); 
                        } elseif ($rowCount_ldap_d == 1) {
                            while ( $row_ldap_d = $result_ldap_d->fetch_assoc() ) {
                                $ldap_d_username  = $row_ldap_d['ldap_username'];
                                $ldap_d_password = base64_decode($row_ldap_d['ldap_password']);
                                $ldap_d_domain = $row_ldap_d['ldap_domain'];
                                $ldap_d_host = $row_ldap_d['ldap_host'];
                                $ldap_d_host_secondary = $row_ldap_d['ldap_host_secondary'];
                                $ldap_d_port = $row_ldap_d['ldap_port'];
                                $ldap_d_basedn = $row_ldap_d['ldap_basedn'];
                                $ldap_d_usergroup = $row_ldap_d['ldap_usergroup'];
                                $ldap_d_userfilter = $row_ldap_d['ldap_userfilter'];
                            }
                        } else {
                            header("Location: ../profile.php?sqlerror=multipleEntriesDefault");
                            exit();
                        }
                    }

                    $sql_ldap = "SELECT * FROM config WHERE id=1";
                    $stmt_ldap = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_ldap, $sql_ldap)) {
                        header("Location: ../profile.php?sqlerror=getLDAPconfig");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_ldap);
                        $result_ldap = mysqli_stmt_get_result($stmt_ldap);
                        $rowCount_ldap = $result_ldap->num_rows;
                        if ($rowCount_ldap < 1) {
                            // MISSING CUSTOM CONFIG, USE DEFAULT
                        } elseif ($rowCount_ldap == 1) {
                            while ( $row_ldap = $result_ldap->fetch_assoc() ) {
                                $ldap_username  = $row_ldap['ldap_username'];
                                $ldap_password = base64_decode($row_ldap['ldap_password']);
                                $ldap_domain = $row_ldap['ldap_domain'];
                                $ldap_host = $row_ldap['ldap_host'];
                                $ldap_host_secondary = $row_ldap['ldap_host_secondary'];
                                $ldap_port = $row_ldap['ldap_port'];
                                $ldap_basedn = $row_ldap['ldap_basedn'];
                                $ldap_usergroup = $row_ldap['ldap_usergroup'];
                                $ldap_userfilter = $row_ldap['ldap_userfilter'];
                            }
                        } else {
                            header("Location: ../profile.php?sqlerror=multipleEntries");
                            exit();
                        }
                    }
                    
                    if (!isset($ldap_d_username) || !isset($ldap_d_password) || !isset($ldap_d_domain) || !isset($ldap_d_host) || !isset($ldap_d_port) || !isset($ldap_d_basedn) || !isset($ldap_d_usergroup) || !isset($ldap_d_userfilter)) {
                        header("Location: ../profile.php?error=ldapDefaultConfigMissingFields");
                        exit();
                    } else {
                        if ($ldap_d_username === '' || $ldap_d_password === '' || $ldap_d_domain === '' || $ldap_d_host === ''|| $ldap_d_port === '' || $ldap_d_basedn === '' || $ldap_d_usergroup === '' || $ldap_d_userfilter === '' ) {
                            header("Location: ../profile.php?error=ldapDefaultConfigMissingEntries");
                            exit();
                        } else { // ALL default SET AND NONE EMPTY - check if custom config isset, and not empty, if not set as default.
                            if (isset($ldap_username      )) { if ($ldap_username       === '') { $ldap_username       = $ldap_d_username      ; } } else { $ldap_username       = $ldap_d_username      ; }
                            if (isset($ldap_password      )) { if ($ldap_password       === '') { $ldap_password       = $ldap_d_password      ; } } else { $ldap_password       = $ldap_d_password      ; } 
                            if (isset($ldap_domain        )) { if ($ldap_domain         === '') { $ldap_domain         = $ldap_d_domain        ; } } else { $ldap_domain         = $ldap_d_domain        ; }
                            if (isset($ldap_host          )) { if ($ldap_host           === '') { $ldap_host           = $ldap_d_host          ; } } else { $ldap_host           = $ldap_d_host          ; }
                            if (isset($ldap_host_secondary)) { if ($ldap_host_secondary === '') { $ldap_host_secondary = $ldap_d_host_secondary; } } else { $ldap_host_secondary = $ldap_d_host_secondary; }
                            if (isset($ldap_port          )) { if ($ldap_port           === '') { $ldap_port           = $ldap_d_port          ; } } else { $ldap_port           = $ldap_d_port          ; }
                            if (isset($ldap_basedn        )) { if ($ldap_basedn         === '') { $ldap_basedn         = $ldap_d_basedn        ; } } else { $ldap_basedn         = $ldap_d_basedn        ; }
                            if (isset($ldap_usergroup     )) { if ($ldap_usergroup      === '') { $ldap_usergroup      = $ldap_d_usergroup     ; } } else { $ldap_usergroup      = $ldap_d_usergroup     ; }
                            if (isset($ldap_userfilter    )) { if ($ldap_userfilter     === '') { $ldap_userfilter     = $ldap_d_userfilter    ; } } else { $ldap_userfilter     = $ldap_d_userfilter    ; }
                        }

                        $login_username = $loggedin_username;
                        $login_password = $_POST['password'];
                        function ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password) {
                            global $_SESSION, $redirect_url, $user_id;
                            include 'dbh.inc.php';
                            
                            $ldap_conn = ldap_connect($ldap_host, $ldap_port);
                            if (!$ldap_conn) {
                                error_log("Could not connect to LDAP server: $ldap_host at line: ".__LINE__);
                                if ($ldap_host !== $ldap_host_secondary && $ldap_host !== '' && $ldap_host !== null) {
                                    ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host_secondary, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password);
                                    exit();
                                } else {
                                    die("Could not connect to LDAP server: $ldap_host at line: ".__LINE__);
                                }
                            }

                            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

                            $ldap_dn = $ldap_usergroup.",".$ldap_basedn;
                            $ldap_bind = ldap_bind($ldap_conn, $ldap_username, $ldap_password);
                            if (!$ldap_bind) {
                                error_log("Could not connect to LDAP server: $ldap_host at line: ".__LINE__);
                                if ($ldap_host !== $ldap_host_secondary && $ldap_host !== '' && $ldap_host !== null) {
                                    ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host_secondary, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password);
                                    exit();
                                } else {
                                    die("Could not bind to LDAP server: $ldap_host at line: ".__LINE__);
                                }
                            }

                            if (filter_var($login_username, FILTER_VALIDATE_EMAIL )) {
                                $ldap_filter = '(&'.$ldap_userfilter.'(userPrincipalName=' . $login_username . '))';
                            } else {
                                $ldap_filter = '(&'.$ldap_userfilter.'(sAMAccountName=' . $login_username. '))';
                            }
                            $ldap_search = ldap_search($ldap_conn, $ldap_dn, $ldap_filter);
                            $ldap_info = ldap_get_entries($ldap_conn, $ldap_search);

                            if ($ldap_info['count'] == 1) {
                                $ldap_bind = ldap_bind($ldap_conn, $ldap_info[0]['dn'], $login_password);
                                if ($ldap_bind) {
                                    $ldap_info_samAccountName = $ldap_info[0]['samaccountname'][0];
                                    $ldap_info_upn = $ldap_info[0]['userprincipalname'][0];
                                    $ldap_info_firstName = $ldap_info[0]['givenname'][0];
                                    $ldap_info_lastName = $ldap_info[0]['sn'][0];
                                    
                                    // UPDATE TABLE ROW
                                    $sql_update = "UPDATE users SET first_name=?, last_name=?, email=? WHERE id=$user_id";
                                    $stmt_update = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                                        header("Location: ".$redirect_url."&error=usersTableSQLConnection");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt_update, "sss", $ldap_info_firstName, $ldap_info_lastName, $ldap_info_upn);
                                        mysqli_stmt_execute($stmt_update);
                                        $rows_update = $conn->affected_rows;
                                        if ($rows_update == 1) {
                                            // Expected 
                                            // update changelog
                                            if ($_SESSION['first_name'] !== $ldap_info_firstName) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "LDAP resync", "users", $user_id, "first_name", $_SESSION['first_name'], $ldap_info_firstName); }
                                            if ($_SESSION['last_name'] !== $ldap_info_lastName)   { addChangelog($_SESSION['user_id'], $_SESSION['username'], "LDAP resync", "users", $user_id, "last_name", $_SESSION['last_name'], $ldap_info_lastName); }
                                            if ($_SESSION['email'] !== $ldap_info_upn)            { addChangelog($_SESSION['user_id'], $_SESSION['username'], "LDAP resync", "users", $user_id, "email", $_SESSION['email'], $ldap_info_upn); }
                                            
                                            $_SESSION['first_name'] = $ldap_info_firstName;
                                            $_SESSION['last_name'] = $ldap_info_lastName;
                                            $_SESSION['email'] = $ldap_info_upn;

                                            header("Location: $redirect_url?resync=success");
                                            exit();
                                        } elseif ($rows_update > 1) {
                                            // shouldnt be possible - header with error for too many rows
                                            header("Location: $redirect_url?resyncError=tooManyRowsUpdated");
                                            exit();
                                        } else {
                                            // no rows - header with error for no rows
                                            header("Location: $redirect_url?resyncError=noRowsUpdated");
                                            exit();
                                        }
                                    }
                                } else{
                                    // LDAP FAILED
                                    header("Location: $redirect_url?resyncError=ldapBindFailure");
                                    exit();
                                }
                            } else {
                                // TOO MANY LDAP ROWS
                                header("Location: $redirect_url?resyncError=tooManyLdapRows");
                                exit();
                            }
                        }

                        ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password);
                        
                    }
                }
            }
        } else {
            header("Location: $redirect_url?resyncError=wrongAuthType");
            exit();
        }
    } else {
        header("Location: $redirect_url?error=noPassword");
        exit();
    }
} else {
    header("Location: $redirect_url?error=noSubmit");
    exit();
}