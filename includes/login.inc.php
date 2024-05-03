<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// LOGIN BACKEND - CHECKS IF THE USER SELECTED LOCAL OR NOT AND LOGS IN VIA EITHER LDAP OR LOCAL USER.


// C:\Users\Administrator>dsquery user -samid *
// "CN=Administrator,CN=Users,DC=ajrich,DC=co,DC=uk"

// INSTALL PHP LDAP:    apt install php8.1-ldap       or       apt intall php-ldap
include 'login-functions.inc.php';

if (isset($_POST['submit'])) {
    include 'get-config.inc.php'; // global config stuff
    include 'session.inc.php'; // session management stuff

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: ../login.php?error=emptyFields");
        exit();
    } else {

        $login_username = trim($_POST["username"]); // remove white space before or after the username
        $login_password = $_POST["password"];

        if (isset($_POST['local'])) {
            if ($_POST['local'] == "on") {
                if (queryLoginBlocked($_POST, 'local') == "blocked") {
                    $loginlog_id = updateLoginLog($_POST, 'failed', 'local'); // add an entry to the login_log
                    addChangelog(0, 'Root', "Login failed", "login_log", $loginlog_id, "type", NULL, 'failed');
                    header("Location: ../login.php?error=loginBlocked");
                    exit(); 
                }
                // Check if the user exists already in the users table with a password and if match, login
                include 'dbh.inc.php';

                if (filter_var($login_username, FILTER_VALIDATE_EMAIL)) {
                    $uid_type = "email";
                } else {
                    $uid_type = "username";
                }

                $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.enabled as enabled, users.password as password, users.password_expired AS password_expired, users.theme_id AS users_theme_id, 
                                    users_roles.name as role, users.theme_id AS users_theme_id,
                                    theme.name as theme_name, theme.file_name as theme_file_name
                                FROM users 
                                INNER JOIN users_roles ON users.role_id = users_roles.id
                                LEFT JOIN theme ON users.theme_id = theme.id
                                WHERE $uid_type=? AND auth='local'";
                $stmt_users = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                    header("Location: ../login.php?error=sqlerror_getUsersList");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_users, "s", $login_username);
                    mysqli_stmt_execute($stmt_users);
                    $result = mysqli_stmt_get_result($stmt_users);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        $userFound = 0;
                        header("Location: ../login.php?error=invalidCredentials1");
                        exit();
                    } elseif ($rowCount == 1) {
                        if ($row = mysqli_fetch_assoc($result)) {
                            $passwordCheck = password_verify($login_password, $row['password']); //check the password
                            if ($passwordCheck == false) {
                                header("Location: ../login.php?error=invalidCredentials2");
                                exit();
                            } else if ($passwordCheck == true) {
                                if ($row['enabled'] != 1) {
                                    header("Location: ../login.php?error=userDisabled");
                                    exit();
                                }
                                session_start();
                                $userFound = 1; // not needed, but useful for debugging

                                $_POST['user_id'] = $row['users_id'];
                                $log_id = updateLoginLog($_POST, 'login', 'local'); // add an entry to the login_log
                                addChangelog($row['users_id'], $row['username'], "Login success", "login_log", $log_id, "type", NULL, 'login');
                                $faildelete_id = deleteLoginFail($_POST, 'ldap');
                                if (is_numeric($faildelete_id)) { addChangelog(0, 'Root', "Delete record", "login_failure", $faildelete_id, "id", $faildelete_id, NULL); }

                                $_SESSION['login_log_id'] = $log_id;
                                $_SESSION['user_id'] = $row['users_id'];
                                $_SESSION['username'] = $row['username'];
                                $_SESSION['first_name'] = $row['first_name'];
                                $_SESSION['last_name'] = $row['last_name'];
                                $_SESSION['email'] = $row['email'];
                                $_SESSION['role'] = $row['role'];
                                $_SESSION['auth'] = $row['auth'];
                                $_SESSION['theme_id'] = $row['users_theme_id'];
                                $_SESSION['theme_name'] = $row['theme_name'];
                                $_SESSION['theme_file_name'] = $row['theme_file_name'];
                                $_SESSION['password_expired'] = $row['password_expired'];
                                $_SESSION['impersonate'] = 0;
                                sessionLogin();
                                if (isset($_SESSION['redirect_url'])) {
                                    if (str_contains($_SESSION['redirect_url'], "?")) {
                                        header("Location: ../".$_SESSION['redirect_url']."&login=success");
                                    } else {
                                        header("Location: ../".$_SESSION['redirect_url']."?login=success");
                                    }
                                    exit();
                                } else {
                                    header("Location: ../?login=success");
                                    exit();
                                }
                            } else {
                                $loginlog_id = updateLoginLog($_POST, 'failed', 'local'); // add an entry to the login_log
                                addChangelog(0, 'Root', "Login failed", "login_log", $loginlog_id, "type", NULL, 'failed');
                                $faillog = insertLoginFail($_POST, 'local');
                                if ($faillog['count'] == 1) {
                                    addChangelog(0, 'Root', "New record", "login_failure", $faillog['id'], "count", NULL, $faillog['count']);
                                } else {
                                    addChangelog(0, 'Root', "Update record", "login_failure", $faillog['id'], "count", $faillog['count']-1, $faillog['count']);
                                }
                                
                                header("Location: ../login.php?error=invalidCredentials3");
                                exit();
                            }
                        } else {
                            $loginlog_id = updateLoginLog($_POST, 'failed', 'local'); // add an entry to the login_log
                            addChangelog(0, 'Root', "Login failed", "login_log", $loginlog_id, "type", NULL, 'failed');
                            $faillog = insertLoginFail($_POST, 'local');
                            if ($faillog['count'] == 1) {
                                addChangelog(0, 'Root', "New record", "login_failure", $faillog['id'], "count", NULL, $faillog['count']);
                            } else {
                                addChangelog(0, 'Root', "Update record", "login_failure", $faillog['id'], "count", $faillog['count']-1, $faillog['count']);
                            }
                            header("Location: ../login.php?error=invalidCredentials4");
                            exit();
                        } 
                    } else { // if there are somehow too many rows matching the sql
                        header("Location: ../login.php?sqlerror=multipleEntries");
                        exit();
                    }
                }
            } else {
                header("Location: ../login.php?local=".$_POST['local']);
                exit();
            }
        } else { // LDAP login 
            if (queryLoginBlocked($_POST, 'ldap') == "blocked") {
                $log_id = updateLoginLog($_POST, 'failed', 'ldap'); // add an entry to the login_log
                addChangelog(0, 'Root', "Login failed", "login_log", $log_id, "type", NULL, 'failed');
                header("Location: ../login.php?error=loginBlocked");
                exit(); 
            }
            include 'dbh.inc.php';
            $login_username = ldap_escape($login_username, '', LDAP_ESCAPE_FILTER);
            // commented out because this was changing brackets into other characters. this is not needed because thre LDAP is a prepared statement.
            // $login_password = ldap_escape($login_password, '', LDAP_ESCAPE_FILTER); 

            $sql_ldap_d = "SELECT * FROM config_default WHERE id=1";
            $stmt_ldap_d = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_ldap_d, $sql_ldap_d)) {
                header("Location: ../login.php?sqlerror=getLDAPconfigDefault");
                exit();
            } else {
                mysqli_stmt_execute($stmt_ldap_d);
                $result_ldap_d = mysqli_stmt_get_result($stmt_ldap_d);
                $rowCount_ldap_d = $result_ldap_d->num_rows;
                if ($rowCount_ldap_d < 1) {
                    header("Location: ../login.php?sqlerror=missingConfigDefault");
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
                    header("Location: ../login.php?sqlerror=multipleEntriesDefault");
                    exit();
                }
            }

            $sql_ldap = "SELECT * FROM config WHERE id=1";
            $stmt_ldap = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_ldap, $sql_ldap)) {
                header("Location: ../login.php?sqlerror=getLDAPconfig");
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
                    header("Location: ../login.php?sqlerror=multipleEntries");
                    exit();
                }
            }

            if (!isset($ldap_d_username) || !isset($ldap_d_password) || !isset($ldap_d_domain) || !isset($ldap_d_host) || !isset($ldap_d_port) || !isset($ldap_d_basedn) || !isset($ldap_d_usergroup) || !isset($ldap_d_userfilter)) {
                header("Location: ../login.php?error=ldapDefaultConfigMissingFields");
                exit();
            } else {
                if ($ldap_d_username === '' || $ldap_d_password === '' || $ldap_d_domain === '' || $ldap_d_host === ''|| $ldap_d_port === '' || $ldap_d_basedn === '' || $ldap_d_usergroup === '' || $ldap_d_userfilter === '' ) {
                    header("Location: ../login.php?error=ldapDefaultConfigMissingEntries");
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
                function ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password) {
                    global $_SESSION, $log_id;
                    
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
                    if (!$ldap_search) {
                        error_log("LDAP search failed");
                        header("Location: ../login.php?error=ldapSearchFailed");
                        exit();
                    }

                    $ldap_info = ldap_get_entries($ldap_conn, $ldap_search);

                    if ($ldap_info['count'] == 1) {
                        $ldap_bind = ldap_bind($ldap_conn, $ldap_info[0]['dn'], $login_password);
                        if ($ldap_bind) {
                            $ldap_info_samAccountName = $ldap_info[0]['samaccountname'][0];
                            $ldap_info_upn = $ldap_info[0]['userprincipalname'][0];
                            $ldap_info_firstName = $ldap_info[0]['givenname'][0];
                            $ldap_info_lastName = isset($ldap_info[0]['sn'][0]) ? $ldap_info[0]['sn'][0] : '';

                            // Check if the user exists already in the users table
                            include 'dbh.inc.php';

                            $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.enabled as enabled, users.theme_id AS users_theme_id,
                                                theme.name as theme_name, theme.file_name as theme_file_name
                                            FROM users 
                                            INNER JOIN users_roles ON users.role_id = users_roles.id
                                            LEFT JOIN theme ON users.theme_id = theme.id
                                            WHERE username=? AND auth='ldap'";
                            $stmt_users = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                                header("Location: ../login.php?sqlerror=sqlerror_getUsersList");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_users, "s", $ldap_info_samAccountName);
                                mysqli_stmt_execute($stmt_users);
                                $result = mysqli_stmt_get_result($stmt_users);
                                $rowCount = $result->num_rows;
                                if ($rowCount < 1) {
                                    $userFound = 0; // not needed, but useful for debugging

                                    // ADD user to table
                                    $default_role = 1;
                                    $auth = 'ldap';

                                    $ldap_info_firstName = mysqli_real_escape_string($conn, $ldap_info_firstName); // escape the special characters
                                    $ldap_info_lastName = mysqli_real_escape_string($conn, $ldap_info_lastName); // escape the special characters
                                    
                                    $sql_upload = "INSERT INTO users (username, first_name, last_name, email, role_id, auth) VALUES (?,?,?,?,?,?)";
                                    $stmt_upload = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                                        header("Location: ../upload.php?sqlerror=sqlerror");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt_upload, "ssssss", $ldap_info_samAccountName, $ldap_info_firstName, $ldap_info_lastName, $ldap_info_upn, $default_role, $auth);
                                        mysqli_stmt_execute($stmt_upload);
                                        $insert_id = mysqli_insert_id($conn);
                                        include 'changelog.inc.php';
                                        // update changelog
                                        addChangelog($insert_id, $ldap_info_samAccountName, "LDAP resync", "users", $insert_id, "username", null, $ldap_info_samAccountName);
                                    }
                                    session_start();
                                    $_POST['user_id'] = $insert_id;
                                    $log_id = updateLoginLog($_POST, 'login', 'ldap'); // add an entry to the login_log
                                    $faildelete_id = deleteLoginFail($_POST, 'ldap');
                                    if (is_numeric($faildelete_id)) { addChangelog(0, 'Root', "Delete record", "login_failure", $faildelete_id, "id", $faildelete_id, NULL); }
                                    $_SESSION['login_log_id'] = $log_id;
                                    $_SESSION['user_id'] = $insert_id;
                                    $_SESSION['username'] = $ldap_info_samAccountName;
                                    $_SESSION['first_name'] = $ldap_info_firstName;
                                    $_SESSION['last_name'] = $ldap_info_lastName;
                                    $_SESSION['email'] = $ldap_info_upn;
                                    $_SESSION['role'] = $default_role;
                                    $_SESSION['auth'] = $auth;
                                    $_SESSION['theme_id'] = $current_default_theme_id;
                                    $_SESSION['theme_name'] = $current_default_theme_name;
                                    $_SESSION['theme_file_name'] = $current_default_theme_file_name;
                                    $_SESSION['password_expired'] = 0;
                                    $_SESSION['impersonate'] = 0;
                                    sessionLogin();
                                    if (isset($_SESSION['redirect_url'])) {
                                        if (str_contains($_SESSION['redirect_url'], "?")) {
                                            header("Location: ../".$_SESSION['redirect_url']."&login=success");
                                        } else {
                                            header("Location: ../".$_SESSION['redirect_url']."?login=success");
                                        }
                                        exit();
                                    } else {
                                        header("Location: ../?login=success");
                                        exit();
                                    }
                                } elseif ($rowCount == 1) {
                                    
                                    session_start();
                                    $userFound = 1; // not needed, but useful for debugging
                                    while ($row = $result->fetch_assoc()){
                                        if ($row['enabled'] != 1) {
                                            header("Location: ../login.php?error=userDisabled");
                                            exit();
                                        }
                                        $_POST['user_id'] = $row['users_id'];
                                        $log_id = updateLoginLog($_POST, 'login', 'ldap'); // add an entry to the login_log
                                        $faildelete_id = deleteLoginFail($_POST, 'ldap');
                                        if (is_numeric($faildelete_id)) { addChangelog(0, 'Root', "Delete record", "login_failure", $faildelete_id, "id", $faildelete_id, NULL); }
                                        $_SESSION['login_log_id'] = $log_id;
                                        $_SESSION['user_id'] = $row['users_id'];
                                        $_SESSION['username'] = $row['username'];
                                        $_SESSION['first_name'] = $row['first_name'];
                                        $_SESSION['last_name'] = $row['last_name'];
                                        $_SESSION['email'] = $row['email'];
                                        $_SESSION['role'] = $row['role'];
                                        $_SESSION['auth'] = $row['auth'];
                                        $_SESSION['theme_id'] = $row['users_theme_id'];
                                        $_SESSION['theme_name'] = $row['theme_name'];
                                        $_SESSION['theme_file_name'] = $row['theme_file_name'];
                                        $_SESSION['password_expired'] = 0;
                                        $_SESSION['impersonate'] = 0;
                                        sessionLogin();
                                        if (isset($_SESSION['redirect_url'])) {
                                            if (str_contains($_SESSION['redirect_url'], "?")) {
                                                header("Location: ../".$_SESSION['redirect_url']."&login=success");
                                            } else {
                                                header("Location: ../".$_SESSION['redirect_url']."?login=success");
                                            }
                                            exit();
                                        } else {
                                            header("Location: ../?login=success");
                                            exit();
                                        }
                                    }  
                                } else { // if there are somehow too many rows matching the sql
                                    header("Location: ../login.php?sqlerror=multipleEntries");
                                    exit();
                                }
                            }
                        } else {
                            // echo("Invalid username or password.");
                            $loginlog_id = updateLoginLog($_POST, 'failed', 'ldap'); // add an entry to the login_log
                            addChangelog(0, 'Root', "Login failed", "login_log", $loginlog_id, "type", NULL, 'failed');
                            $faillog = insertLoginFail($_POST, 'ldap');
                            if ($faillog['count'] == 1) {
                                addChangelog(0, 'Root', "New record", "login_failure", $faillog['id'], "count", NULL, $faillog['count']);
                            } else {
                                addChangelog(0, 'Root', "Update record", "login_failure", $faillog['id'], "count", $faillog['count']-1, $faillog['count']);
                            }
                            header("Location: ../login.php?error=invalidCredentials5");
                            exit();
                        }
                    } else {
                        // echo("Invalid username or password.");
                        $loginlog_id = updateLoginLog($_POST, 'failed', 'ldap'); // add an entry to the login_log
                        addChangelog(0, 'Root', "Login failed", "login_log", $loginlog_id, "type", NULL, 'failed');
                        $faillog = insertLoginFail($_POST, 'ldap');
                        if ($faillog['count'] == 1) {
                            addChangelog(0, 'Root', "New record", "login_failure", $faillog['id'], "count", NULL, $faillog['count']);
                        } else {
                            addChangelog(0, 'Root', "Update record", "login_failure", $faillog['id'], "count", $faillog['count']-1, $faillog['count']);
                        }
                        header("Location: ../login.php?error=invalidCredentials6");
                        exit();
                    }
                }

                ldapConnection($ldap_username, $ldap_password, $ldap_domain, $ldap_host, $ldap_host_secondary, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter, $login_username, $login_password);

            }
        }
    } 
} else {
    header("Location: ../login.php?error=submitNotSet");
    exit();
}
