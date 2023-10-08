<?php
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USED FOR SUBMITTING FORMS AND DOING SQL CHANGES FOR THE ADMIN PAGE AND SOME OTHER PAGES WITH SIMILAR PROPERTIES
// PROFILE PAGE USES THIS ALSO FOR ITS SAVING
// ADDING NEW ROWS AND AREAS ETC ALSO USES THIS FROM INDEX PAGE WHEN THERE IS NO SITE/AREA/SHELF

// USED BY: admin.php, index.php, profile.php

// print_r($_POST);
//         exit();

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

if (!isset($_POST['global-submit']) && !isset($_POST['global-restore-defaults']) && !isset($_POST['ldap-submit']) 
    && !isset($_POST['ldap-restore-defaults']) && !isset($_POST['smtp-submit']) && !isset($_POST['smtp-restore-defaults']) 
    && !isset($_POST['user_role_submit']) && !isset($_POST['user_enabled_submit']) && !isset($_POST['ldap-toggle-submit']) 
    && !isset($_POST['admin-pwreset-submit']) && !isset($_POST['location-submit']) && !isset($_POST['stocklocation-submit']) 
    && !isset($_POST['profile-submit']) && !isset($_POST['location-delete-submit']) && !isset($_POST['location-edit-submit'])
    && !isset($_POST['smtp-toggle-submit']) && !isset($_POST['imagemanagement-submit'])
    && !isset($_POST['theme-upload']) && !isset($_GET['mail-notification']) && !isset($_POST['card-modify']) 
    && !isset($_POST['card-remove'])) {

    header("Location: ../admin.php?error=noSubmit");
    exit();
} else {
    include 'changelog.inc.php'; // for updating the changelog table

    // check the row is there to be edited. if not, add it.
    include 'dbh.inc.php';
    $sql = "SELECT * FROM config";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $errors[] = "configTableSQLConnection";
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount > 1) {
            header("Location: ../admin.php?error=tooManyConfigRows");
            exit();
        } elseif ($rowCount < 1) {
            // add a blank row to the table
            $sql = "INSERT INTO config (id) 
                                VALUES (1)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../admin.php?error=configConnectionSQL");
                exit();
            } else {
                mysqli_stmt_execute($stmt);
                    
                $sql = "SELECT * FROM config";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $errors[] = "configTableSQLConnection";
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    $configCurrent = $result->fetch_assoc();
                }
                
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Create config", "config", 1, "id", null, 1);

            }  
        } else {
            // all good, continue.
            $configCurrent = $result->fetch_assoc();
        }
    }
    if (isset($_POST['global-submit'])) { // GLOBAL saving in admin
        $queryStrings = [];
        $errors = [];
         
        $config_system_name   = isset($_POST['system_name'])             ? $_POST['system_name']             : '';
        $config_banner_color  = isset($_POST['banner_color'])            ? $_POST['banner_color']            : '';
        $config_logo_image    = isset($_FILES['logo_image'])             ? $_FILES['logo_image']             : '';
        $config_favicon_image = isset($_FILES['favicon_image'])          ? $_FILES['favicon_image']          : '';
        $config_currency      = isset($_POST['currency_selection'])      ? $_POST['currency_selection']      : '';
        $config_sku_prefix    = isset($_POST['sku_prefix'])              ? $_POST['sku_prefix']              : '';
        $config_base_url      = isset($_POST['base_url'])                ? $_POST['base_url']                : '';
        $config_default_theme = isset($_POST['default_theme']) ? $_POST['default_theme'] : '';

        if (isset($_POST['system_name']) && $config_system_name !== '') {
            $post_system_name = $_POST['system_name'];
            include 'dbh.inc.php';
            $sql_upload = "UPDATE config SET system_name=? WHERE id=1";
            $stmt_upload = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                $errors[] = "system_nameSqlError";
            } else {
                mysqli_stmt_bind_param($stmt_upload, "s", $post_system_name);
                mysqli_stmt_execute($stmt_upload);
                $queryStrings[] = "system_nameUpload=success";
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "system_name", $configCurrent['system_name'], $post_system_name);
            }
        }

        if ( isset($_POST['banner_color']) && $config_banner_color !== '') {
            $post_banner_color = $config_banner_color;
            if ($post_banner_color !== $configCurrent['banner_color']) {
                if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $post_banner_color)) {
                    include 'dbh.inc.php';
                    $sql_upload = "UPDATE config SET banner_color=? WHERE id=1";
                    $stmt_upload = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                        $errors[] = "bannercolorSqlError";
                    } else {
                        mysqli_stmt_bind_param($stmt_upload, "s", $post_banner_color);
                        mysqli_stmt_execute($stmt_upload);
                        $queryStrings[] = "bannercolorUpload=success";
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "banner_color", $configCurrent['banner_color'], $post_banner_color);

                    }
                    
                } else {
                    $errors[] = "invalidHexFormat";
                }
            }
        }

        function image_upload($field) {
            $timedate = date("dmyHis");

            $uploadDirectory = "../assets/img/config/custom/";
            $errors = [];                                                   // Store errors here
            $fileExtensionsAllowed = ['png', 'gif', 'jpg', 'jpeg', 'ico'];  // These will be the only file extensions allowed 
            $fileName = $_FILES[$field]['name'];                            // Get uploaded file name
            $fileSize = $_FILES[$field]['size'];                            // Get uploaded file size
            $fileTmpName  = $_FILES[$field]['tmp_name'];                    // Get uploaded file temp name
            $fileType = $_FILES[$field]['type'];                            // Get uploaded file type
            $explode = explode('.',$fileName);                              // Get file extension explode
            $fileNameShort = str_replace(" ", "_", implode('.', array_slice(explode('.', $fileName), 0, -1)));                             
            $fileExtension = strtolower(end($explode));                     // Get file extension

            if ($_FILES[$field]['name'] !== '') {
                if (!isset($_FILES[$field]))                          { $errors[] = "notSet-File";          }
                if ($_FILES[$field]['name'] == '')                    { $errors[] = "notSet-File-name";     }
                if ($_FILES[$field]['size'] == '')                    { $errors[] = "notSet-File-size";     }
                if ($_FILES[$field]['tmp_name'] == '')                { $errors[] = "notSet-File-tmp_name"; }
                if ($_FILES[$field]['type'] == '')                    { $errors[] = "notSet-File-type";     }
                if (!in_array($fileExtension,$fileExtensionsAllowed)) { $errors[] = "wrongFileExtension";   } // File extenstion match?
                if ($fileSize > 10000000)                             { $errors[] = "above10MB";            } // Within Filesize limits?
                
                if (empty($errors)) { // IF file is existing and all fields exist:
                    $moveName = $timedate.'-'.$fileNameShort.'.'.$fileExtension;
                    $uploadPath = $uploadDirectory.$moveName;
                    $uploadFileName = 'custom/'.$moveName;
                    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
                    if ($didUpload) {
                        return $uploadFileName;
                    } else {
                        $errors[] = "uploadFailed";
                        return $errors;
                    }
                } else {
                    return $errors;
                } 
            }
        }

        // LOGO IMAGE UPLOAD
        if (isset($_FILES['logo_image']) && $config_logo_image !== '') {
            if ($_FILES['logo_image']['name'] !== '') {
                $logo_image_name = image_upload('logo_image');
            
                if ($logo_image_name !== '' && !is_array($logo_image_name)) {
                    include 'dbh.inc.php';
                    $sql_upload = "UPDATE config SET logo_image=? WHERE id=1";
                    $stmt_upload = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                        $errors[] = "logoSqlError";
                    } else {
                        mysqli_stmt_bind_param($stmt_upload, "s", $logo_image_name);
                        mysqli_stmt_execute($stmt_upload);
                        $queryStrings[] = "logoUpload=success";
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "logo_image", $configCurrent['logo_image'], $logo_image_name);
                    }
                } else {
                    $errors[] = "FAVICON ERROR START";
                    if (is_array($logo_image_name)) {
                        foreach ($logo_image_name as $error) {
                            $errors[] = $error;
                        }
                    } else {
                        $errors[] = "emptyFileName";
                    }
                    $errors[] = "LOGO ERROR END";
                }
            }
        }
        

        // FAVICON IMAGE UPLOAD
        if (isset($_FILES['favicon_image']) && $config_favicon_image !== '') {
            if ($_FILES['favicon_image']['name'] !== '') {
                $favicon_image_name = image_upload('favicon_image');

                if ($favicon_image_name !== '' && !is_array($favicon_image_name)) {
                    include 'dbh.inc.php';
                    $sql_upload = "UPDATE config SET favicon_image=? WHERE id=1";
                    $stmt_upload = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                        $errors[] = "faviconSqlError";
                    } else {
                        mysqli_stmt_bind_param($stmt_upload, "s", $favicon_image_name);
                        mysqli_stmt_execute($stmt_upload);
                        $queryStrings[] = "faviconUpload=success";
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "favicon_image", $configCurrent['favicon_image'], $favicon_image_name);
                    }
                } else {
                    $errors[] = "FAVICON ERROR START";
                    if (is_array($favicon_image_name)) {
                        foreach ($favicon_image_name as $error) {
                            $errors[] = $error;
                        }
                    } else {
                        $errors[] = "faviconEmptyFileName";
                    }
                    $errors[] = "FAVICON ERROR END";
                }
            }
        }

        if (isset($_POST['currency_selection']) && $config_currency !== '') {
            $post_currency = $_POST['currency_selection'];
            include 'dbh.inc.php';
            if ($post_currency !== $configCurrent['currency']) {
                $sql_upload = "UPDATE config SET currency=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "currencySqlError";
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "s", $post_currency);
                    mysqli_stmt_execute($stmt_upload);
                    $queryStrings[] = "currencyUpload=success";
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "currency", $configCurrent['currency'], $post_currency);
                }
            }
        }

        if (isset($_POST['sku_prefix']) && $config_sku_prefix !== '') {
            $post_sku_prefix = $_POST['sku_prefix'];
            $current_sku_prefix = $configCurrent['sku_prefix']; 
            if ($current_sku_prefix !== '') {
                $sql_upload = "UPDATE config SET sku_prefix=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "SKU_prefixSqlError";
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "s", $post_sku_prefix);
                    mysqli_stmt_execute($stmt_upload);
                    
                    $sql_change = "UPDATE stock SET sku = REPLACE(sku, '$current_sku_prefix', '$post_sku_prefix');";
                    $stmt_change = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_change, $sql_change)) {
                        $errors[] = "SKU_prefixSqlError";
                    } else {
                        mysqli_stmt_execute($stmt_change);
                        $queryStrings[] = "SKU_prefixChange=success";
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "sku_prefix", $configCurrent['sku_prefix'], $post_sku_prefix);
                    }
                }     
            } 
        }

        if (isset($_POST['base_url']) && $config_base_url !== '') {
            $post_base_url = $_POST['base_url'];
            include 'dbh.inc.php';
            $sql_upload = "UPDATE config SET base_url=? WHERE id=1";
            $stmt_upload = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                $errors[] = "base_urlSqlError";
            } else {
                mysqli_stmt_bind_param($stmt_upload, "s", $post_base_url);
                mysqli_stmt_execute($stmt_upload);
                $queryStrings[] = "base_urlUpload=success";
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "base_url", $configCurrent['base_url'], $post_base_url);
            }
        }

        if (isset($_POST['default_theme']) && $config_default_theme !== '') {
            $post_default_theme = $_POST['default_theme'];
            include 'dbh.inc.php';
            if ($post_default_theme !== $configCurrent['default_theme_id']) {
                $sql_upload = "UPDATE config SET default_theme_id=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "default_themeSqlError";
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "s", $post_default_theme);
                    mysqli_stmt_execute($stmt_upload);
                    $queryStrings[] = "default_themeUpload=success";
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "default_theme", $configCurrent['default_theme_id'], $post_default_theme);
                }
            }
        }
        
        if (is_array($queryStrings)) {
            if (count($queryStrings) > 1) {
                $queryString = implode('&', $queryStrings);
            } elseif (count($queryStrings) == 1) {
                $queryString = $queryStrings[0];
            } else {
                $queryString = '';
            }
        } else {
            $queryString = '';
        }

        if (is_array($errors)) {
            if (count($errors) >= 1) {
                $error = implode('&', $errors);
            } elseif (count($errors) == 1) {
                $error = $errors[0];
            }  else {
                $error = '';
            }
        } else {
            $error = '';
        }
        
        if ($error !== '') {
            if ($queryString !== '') {
                $error = '&error='.$error;
            } else {
                $error = 'error='.$error;
            }
        }
        
        $queryString = '?'.$queryString.$error;

        header("Location: ../admin.php$queryString#global-settings");
        exit();

    } elseif (isset($_POST['global-restore-defaults'])) { // restore global settings in admin
        include 'dbh.inc.php';

        $sql_config = "SELECT system_name, base_url, banner_color, logo_image, favicon_image, currency, sku_prefix FROM config_default ORDER BY id LIMIT 1";
        $stmt_config = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
            header("Location: ../admin.php?sqlerror=config_default_getEntries#global-settings");
            exit();
        } else {
            mysqli_stmt_execute($stmt_config);
            $result_config = mysqli_stmt_get_result($stmt_config);
            $rowCount_config = $result_config->num_rows;
            if ($rowCount_config < 1) {
                header("Location: ../admin.php?sqlerror=config_default_noID1#global-settings");
                exit();
            } else {
                while ( $config = $result_config->fetch_assoc() ) {
                    $restore_system_name   = $config['system_name'];
                    $restore_base_url      = $config['base_url'];
                    $restore_banner_color  = $config['banner_color'];
                    $restore_logo_image    = $config['logo_image'];
                    $restore_favicon_image = $config['favicon_image'];
                    $restore_currency      = $config['currency'];
                    $restore_sku_prefix    = $config['sku_prefix'];
                }
                $sql_upload = "UPDATE config SET system_name=?, banner_color=?, logo_image=?, favicon_image=?, currency=?, sku_prefix=?, base_url-? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    header("Location: ../admin.php?sqlerror=config_noUpdate#global-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "sssssss", $restore_system_name, $restore_banner_color, $restore_logo_image, $restore_favicon_image, $restore_currency, $restore_sku_prefix, $restore_base_url);
                    mysqli_stmt_execute($stmt_upload);
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "system_name", $configCurrent['system_name'], $restore_system_name);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "banner_color", $configCurrent['banner_color'], $restore_banner_color);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "logo_image", $configCurrent['logo_image'], $restore_logo_image);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "favicon_image", $configCurrent['favicon_image'], $restore_favicon_image);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "currency", $configCurrent['currency'], $restore_currency);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "sku_prefix", $configCurrent['sku_prefix'], $restore_sku_prefix);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "base_url", $configCurrent['base_url'], $restore_base_url);

                    $sql_sku = "SELECT sku_prefix FROM config ORDER BY id LIMIT 1";
                    $stmt_sku = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_sku, $sql_sku)) {
                        header("Location: ../admin.php?sqlerror=config_getEntries#global-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_sku);
                        $result_sku = mysqli_stmt_get_result($stmt_sku);
                        $rowCount_sku = $result_sku->num_rows;
                        if ($rowCount_sku < 1) {
                            header("Location: ../admin.php?sqlerror=config_noID1#global-settings");
                            exit();
                        } else {
                            $row_sku = $result_sku->fetch_assoc();
                            $current_sku_prefix = $row_sku['sku_prefix'];
                        }
                    }

                    $sql_change = "UPDATE stock SET sku = REPLACE(sku, '$current_sku_prefix', '$restore_sku_prefix');";
                    $stmt_change = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_change, $sql_change)) {
                        header("Location: ../admin.php?sqlerror=failedToChangeSkuPrefixInTable#global-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_change);
                        header("Location: ../admin.php?restore=globalSuccess#global-settings");
                        exit();
                    }
                    
                }
            }
        }

    } elseif (isset($_POST['admin-pwreset-submit'])) { // resetting a user's password in the admin section
        if (isset($_POST['user-id'])) {

            include 'get-config.inc.php';
            if (in_array($_SESSION['role'], $config_admin_roles_array)) {

                $user_id = $_POST['user-id'];
                $new_password = $_POST['password'];
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $admin_roles = "'".implode("', '", $config_admin_roles_array)."'";

                $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, 
                                    users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, 
                                    users.enabled as enabled, users.password AS users_password, users.password_expired as password_expired
                                FROM users 
                                INNER JOIN users_roles ON users.role_id = users_roles.id
                                WHERE users.id=? AND users_roles.name NOT IN ($admin_roles)";
                $stmt_users = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                    header("Location: ../admin.php?error=usersTableIssue#users-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_users, "s", $user_id);
                    mysqli_stmt_execute($stmt_users);
                    $result = mysqli_stmt_get_result($stmt_users);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        header("Location: ../admin.php?error=noUserFound#users-settings");
                        exit();
                    } elseif ($rowCount > 1) {
                        header("Location: ../admin.php?error=tooManyUserFound#users-settings");
                        exit();
                    } else {
                        // 1 user found - continue

                        $row = $result->fetch_assoc();
                        $current_password_hash = $row['users_password'];
                        $current_password_expired = $row['password_expired'];

                        if ($current_password_hash === $new_password_hash) {
                            header("Location: ../admin.php?error=passwordMatchesCurrent#users-settings");
                            exit();
                        } else {
                            $sql_upload = "UPDATE users SET password='$new_password_hash', password_expired=1 WHERE id=?";
                            $stmt_upload = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                                header("Location: ../admin.php?error=passwordResetSQLError#users-settings");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_upload, "s", $user_id);
                                mysqli_stmt_execute($stmt_upload);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Reset Password", "users", $user_id, "password", '****', '****');
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Reset Password", "users", $user_id, "password_expired", $current_password_expired, 1);
                                header("Location: ../admin.php?success=PasswordChanged#users-settings");
                                exit();
                            }
                        }
                        
                    }
                }
            } else {
                header("Location: ../admin.php?error=wrongUserRole#users-settings");
                exit();
            }
        } else {
            header("Location: ../admin.php?error=userIdMissing#users-settings");
            exit();
        }
    } elseif (isset($_POST['ldap-toggle-submit'])) { // enable/disable LDAP
        print_r($_POST);
        $ldap_enabled_post = isset($_POST['ldap-enabled']) ? $_POST['ldap-enabled'] : "off";

        if ($ldap_enabled_post == "on") {
            $ldap_enabled = 1;
        } else {
            $ldap_enabled = 0;
        }

        include 'dbh.inc.php';
        $sql_upload = "UPDATE config SET ldap_enabled=? WHERE id=1";
        $stmt_upload = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
            $errors[] = "ldapUploadSQLerror";
            header("Location: ../admin.php?error=ldapEnabled#ldap-settings");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt_upload, "s", $ldap_enabled);
            mysqli_stmt_execute($stmt_upload);
            // update changelog
            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_enabled", $configCurrent['ldap_enabled'], $ldap_enabled);
            header("Location: ../admin.php?ldapEnabled=success#ldap-settings");
            exit();
        }
    } elseif (isset($_POST['ldap-submit'])) { // LDAP saving in admin page

        if (isset($_POST['auth-username']))         { $config_ldap_username         = $_POST['auth-username'];         } else { $config_ldap_username         = ''; }
        if (isset($_POST['auth-password']))         { $config_ldap_password         = base64_encode(($_POST['auth-password']));         } else { $config_ldap_password         = ''; }
        if (isset($_POST['auth-password-confirm'])) { $config_ldap_password_confirm = base64_encode($_POST['auth-password-confirm']); } else { $config_ldap_password_confirm = ''; }
        if (isset($_POST['auth-domain']))           { $config_ldap_domain           = $_POST['auth-domain'];           } else { $config_ldap_domain           = ''; }
        if (isset($_POST['auth-host']))             { $config_ldap_host             = $_POST['auth-host'];             } else { $config_ldap_host             = ''; }
        if (isset($_POST['auth-host-secondary']))   { $config_ldap_host_secondary   = $_POST['auth-host-secondary'];   } else { $config_ldap_host_secondary   = ''; }
        if (isset($_POST['auth-port']))             { $config_ldap_port             = $_POST['auth-port'];             } else { $config_ldap_port             = ''; }
        if (isset($_POST['auth-basedn']))           { $config_ldap_basedn           = $_POST['auth-basedn'];           } else { $config_ldap_basedn           = ''; }
        if (isset($_POST['auth-usergroup']))        { $config_ldap_usergroup        = $_POST['auth-usergroup'];        } else { $config_ldap_usergroup        = ''; }
        if (isset($_POST['auth-userfilter']))       { $config_ldap_userfilter       = $_POST['auth-userfilter'];       } else { $config_ldap_userfilter       = ''; }

        if ($config_ldap_username === '' || $config_ldap_password === '' || $config_ldap_password_confirm === '' || $config_ldap_domain === '' || $config_ldap_host === '' || $config_ldap_port === '') {
            // DO NOT CONTINUE
            if ($config_ldap_password !== '') { $passwordSet = 'Set'; } else { $passwordSet = 'NotSet'; }
            if ($config_ldap_password === $config_ldap_password_confirm) { $passwordMatch = 'Match'; } else { $passwordMatch ='NoMatch'; }
            header("Location: ../admin.php?error=emptyFields&auth-domain=$config_ldap_username&auth-password=$passwordSet&auth-password-confirm=$passwordMatch&auth-domain=$config_ldap_domain&auth-host=$config_ldap_host&auth-port=$config_ldap_port&auth-basedn=$config_ldap_basedn&auth-usergroup=$config_ldap_usergroup&auth-userfliter=$config_ldap_userfilter#ldap-settings");
            exit();
        } else {
            if ($config_ldap_password === $config_ldap_password_confirm) { 
                $passwordMatch = 'Match'; 
                include 'dbh.inc.php';
                $sql_upload = "UPDATE config SET ldap_username=?, ldap_password=?, ldap_domain=?, ldap_host=?, ldap_host_secondary=?, ldap_port=?, ldap_basedn=?, ldap_usergroup=?, ldap_userfilter=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "ldapUploadSQLerror";
                    header("Location: ../admin.php?error=ldapUpload#ldap-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "sssssssss", $config_ldap_username, $config_ldap_password, $config_ldap_domain, $config_ldap_host, $config_ldap_host_secondary, $config_ldap_port, $config_ldap_basedn, $config_ldap_usergroup, $config_ldap_userfilter);
                    mysqli_stmt_execute($stmt_upload);
                    // update changelog
                    if ($config_ldap_username       !== $configCurrent['ldap_username'])       { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_username", $configCurrent['ldap_username'], $config_ldap_username); }
                    if ($config_ldap_password       !== $configCurrent['ldap_password'])       { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_password", $configCurrent['ldap_password'], $config_ldap_password); }
                    if ($config_ldap_domain         !== $configCurrent['ldap_domain'])         { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_domain", $configCurrent['ldap_domain'], $config_ldap_domain); }
                    if ($config_ldap_host           !== $configCurrent['ldap_host'])           { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_host", $configCurrent['ldap_host'], $config_ldap_host); }
                    if ($config_ldap_host_secondary !== $configCurrent['ldap_host_secondary']) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_host_secondary", $configCurrent['ldap_host_secondary'], $config_ldap_host_secondary); }
                    if ($config_ldap_port           !== $configCurrent['ldap_port'])           { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_port", $configCurrent['ldap_port'], $config_ldap_port); }
                    if ($config_ldap_basedn         !== $configCurrent['ldap_basedn'])         { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_basedn", $configCurrent['ldap_basedn'], $config_ldap_basedn); }
                    if ($config_ldap_usergroup      !== $configCurrent['ldap_usergroup'])      { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_usergroup", $configCurrent['ldap_usergroup'], $config_ldap_usergroup); }
                    if ($config_ldap_userfilter     !== $configCurrent['ldap_userfilter'])     { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "ldap_userfilter", $configCurrent['ldap_userfilter'], $config_ldap_userfilter); }

                    header("Location: ../admin.php?ldapUpload=success#ldap-settings");
                    exit();
                }
            } else { 
                header("Location: ../admin.php?error=passwordMatch");
                exit();
            }
            
        }
    } elseif (isset($_POST['ldap-restore-defaults'])) { // restore LDAP default settings
        // RESTORE LDAP
        include 'dbh.inc.php';

        $sql_config = "SELECT ldap_username, ldap_password, ldap_domain, ldap_host, ldap_host_secondary, ldap_port, ldap_basedn, ldap_usergroup, ldap_userfilter FROM config_default ORDER BY id LIMIT 1";
        $stmt_config = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
            header("Location: ../admin.php?sqlerror=config_default_getEntries_ldap#ldap-settings");
            exit();
        } else {
            mysqli_stmt_execute($stmt_config);
            $result_config = mysqli_stmt_get_result($stmt_config);
            $rowCount_config = $result_config->num_rows;
            if ($rowCount_config < 1) {
                header("Location: ../admin.php?sqlerror=config_default_noID1_ldap#ldap-settings");
                exit();
            } else {
                while ( $config = $result_config->fetch_assoc() ) {
                    $restore_ldap_username       = $config['ldap_username'];       
                    $restore_ldap_password       = $config['ldap_password'];      
                    $restore_ldap_domain         = $config['ldap_domain'];          
                    $restore_ldap_host           = $config['ldap_host'];
                    $restore_ldap_host_secondary = $config['ldap_host_secondary'];               
                    $restore_ldap_port           = $config['ldap_port'];            
                    $restore_ldap_basedn         = $config['ldap_basedn'];          
                    $restore_ldap_usergroup      = $config['ldap_usergroup'];      
                    $restore_ldap_userfilter     = $config['ldap_userfilter'];     
                }
                $sql_upload = "UPDATE config SET ldap_username=?, ldap_password=?, ldap_domain=?, ldap_host=?, ldap_host_secondary=?, ldap_port=?, ldap_basedn=?, ldap_usergroup=?, ldap_userfilter=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "ldapUploadSQLerror";
                    header("Location: ../admin.php?error=ldapRestoreUpload#ldap-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "sssssssss", $restore_ldap_username, $restore_ldap_password, $restore_ldap_domain, $restore_ldap_host, $restore_ldap_host_secondary, $restore_ldap_port, $restore_ldap_basedn, $restore_ldap_usergroup, $restore_ldap_userfilter);
                    mysqli_stmt_execute($stmt_upload);
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_username", $configCurrent['ldap_username'], $restore_ldap_username);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_password", $configCurrent['ldap_password'], $restore_ldap_password);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_domain", $configCurrent['ldap_domain'], $restore_ldap_domain);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_host", $configCurrent['ldap_host'], $restore_ldap_host);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_host_secondary", $configCurrent['ldap_host_secondary'], $restore_ldap_host_secondary);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_port", $configCurrent['ldap_port'], $restore_ldap_port);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_basedn", $configCurrent['ldap_basedn'], $restore_ldap_basedn);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_usergroup", $configCurrent['ldap_usergroup'], $restore_ldap_usergroup);
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "ldap_userfilter", $configCurrent['ldap_userfilter'], $restore_ldap_userfilter);
                    header("Location: ../admin.php?ldapUpload=configRestored#ldap-settings");
                    exit();
                }
            }
        }
    } elseif (isset($_POST['smtp-toggle-submit'])) { // enable/disable SMTP
        print_r($_POST);
        $smtp_enabled_post = isset($_POST['smtp-enabled']) ? $_POST['smtp-enabled'] : "off";

        if ($smtp_enabled_post == "on") {
            $smtp_enabled = 1;
        } else {
            $smtp_enabled = 0;
        }

        include 'dbh.inc.php';
        $sql_upload = "UPDATE config SET smtp_enabled=? WHERE id=1";
        $stmt_upload = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
            $errors[] = "smtpUploadSQLerror";
            header("Location: ../admin.php?error=smtpEnabled#smtp-settings");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt_upload, "s", $smtp_enabled);
            mysqli_stmt_execute($stmt_upload);
            header("Location: ../admin.php?smtpEnabled=success#smtp-settings");
            exit();
        }
    } elseif (isset($_POST['smtp-submit'])) { // save smtp info in smtp section of admin page
        if (isset($_POST['smtp-username']))   { $config_smtp_username   = $_POST['smtp-username'];   } else { $config_smtp_username   = ''; }
        if (isset($_POST['smtp-password']))   { $config_smtp_password   = base64_encode($_POST['smtp-password']);   } else { $config_smtp_password   = ''; }
        if (isset($_POST['smtp-encryption'])) { $config_smtp_encryption = $_POST['smtp-encryption']; } else { $config_smtp_encryption = ''; }
        if (isset($_POST['smtp-host']))       { $config_smtp_host       = $_POST['smtp-host'];       } else { $config_smtp_host       = ''; }
        if (isset($_POST['smtp-port']))       { $config_smtp_port       = $_POST['smtp-port'];       } else { $config_smtp_port       = ''; }
        if (isset($_POST['smtp-from-email'])) { $config_smtp_from_email = $_POST['smtp-from-email']; } else { $config_smtp_from_email = ''; }
        if (isset($_POST['smtp-from-name']))  { $config_smtp_from_name  = $_POST['smtp-from-name'];  } else { $config_smtp_from_name  = ''; }
        if (isset($_POST['smtp-backup-to']))  { $config_smtp_to_email   = $_POST['smtp-backup-to'];  } else { $config_smtp_to_email   = ''; }

        if ($config_smtp_username === '' || $config_smtp_password === '' || $config_smtp_encryption === '' || $config_smtp_host === '' || $config_smtp_port === '' || $config_smtp_from_email === '' || $config_smtp_from_name === '' || $config_smtp_to_email === '') {
            // DO NOT CONTINUE
            header("Location: ../admin.php?error=emptyFields");
            exit();
        } else {
            include 'dbh.inc.php';
            $sql_upload = "UPDATE config SET smtp_username=?, smtp_password=?, smtp_encryption=?, smtp_host=?, smtp_port=?, smtp_from_email=?, smtp_from_name=?, smtp_to_email=? WHERE id=1";
            $stmt_upload = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                $errors[] = "smtpUploadSQLerror";
                header("Location: ../admin.php?error=smtpUpload#smtp-settings");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_upload, "ssssssss", $config_smtp_username, $config_smtp_password, $config_smtp_encryption, $config_smtp_host, $config_smtp_port, $config_smtp_from_email, $config_smtp_from_name, $config_smtp_to_email);
                mysqli_stmt_execute($stmt_upload);
                // update changelog
                if ($config_smtp_username   !== $configCurrent['smtp_username'])   { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_username", $configCurrent['smtp_username'], $config_smtp_username); }
                if ($config_smtp_password   !== $configCurrent['smtp_password'])   { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_password", $configCurrent['smtp_password'], $config_smtp_password); }
                if ($config_smtp_encryption !== $configCurrent['smtp_encryption']) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_encryption", $configCurrent['smtp_encryption'], $config_smtp_encryption); }
                if ($config_smtp_host       !== $configCurrent['smtp_host'])       { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_host", $configCurrent['smtp_host'], $config_smtp_host); }
                if ($config_smtp_port       !== $configCurrent['smtp_port'])       { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_port", $configCurrent['smtp_port'], $config_smtp_port); }
                if ($config_smtp_from_email !== $configCurrent['smtp_from_email']) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_from_email", $configCurrent['smtp_from_email'], $config_smtp_from_email); }
                if ($config_smtp_from_name  !== $configCurrent['smtp_from_name'])  { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_from_name", $configCurrent['smtp_from_name'], $config_smtp_from_name); }
                if ($config_smtp_to_email   !== $configCurrent['smtp_to_email'])   { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "config", 1, "smtp_to_email", $configCurrent['smtp_to_email'], $config_smtp_to_email); }
                header("Location: ../admin.php?smtpUpload=success#smtp-settings");
                exit();
            }
        }
    } elseif (isset($_POST['smtp-restore-defaults'])) { // restore default smtp info
        // RESTORE SMTP
        include 'dbh.inc.php';

        $sql_config = "SELECT smtp_username, smtp_password, smtp_encryption, smtp_host, smtp_port, smtp_from_email, smtp_from_name, smtp_to_email FROM config_default ORDER BY id LIMIT 1";
        $stmt_config = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
            header("Location: ../admin.php?sqlerror=config_default_getEntries_smtp#smtp-settings");
            exit();
        } else {
            mysqli_stmt_execute($stmt_config);
            $result_config = mysqli_stmt_get_result($stmt_config);
            $rowCount_config = $result_config->num_rows;
            if ($rowCount_config < 1) {
                header("Location: ../admin.php?sqlerror=config_default_noID1_smtp#smtp-settings");
                exit();
            } else {
                while ( $config = $result_config->fetch_assoc() ) {
                    $restore_smtp_username   = $config['smtp_username'];       
                    $restore_smtp_password   = $config['smtp_password'];      
                    $restore_smtp_encryption = $config['smtp_encryption'];          
                    $restore_smtp_host       = $config['smtp_host'];            
                    $restore_smtp_port       = $config['smtp_port'];            
                    $restore_smtp_from_email = $config['smtp_from_email'];          
                    $restore_smtp_from_name  = $config['smtp_from_name'];      
                    $restore_smtp_to_email   = $config['smtp_to_email'];     
                }
                $sql_upload = "UPDATE config SET smtp_username=?, smtp_password=?, smtp_encryption=?, smtp_host=?, smtp_port=?, smtp_from_email=?, smtp_from_name=?, smtp_to_email=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "smtpUploadSQLerror";
                    header("Location: ../admin.php?error=smtpRestoreUpload#smtp-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "ssssssss", $restore_smtp_username, $restore_smtp_password, $restore_smtp_encryption, $restore_smtp_host, $restore_smtp_port, $restore_smtp_from_email, $restore_smtp_from_name, $restore_smtp_to_email);
                    mysqli_stmt_execute($stmt_upload);
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_username", $configCurrent['smtp_username'], $restore_smtp_username); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_password", $configCurrent['smtp_password'], $restore_smtp_password); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_encryption", $configCurrent['smtp_encryption'], $restore_smtp_encryption); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_host", $configCurrent['smtp_host'], $restore_smtp_host); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_port", $configCurrent['smtp_port'], $restore_smtp_port); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_from_email", $configCurrent['smtp_from_email'], $restore_smtp_from_email); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_from_name", $configCurrent['smtp_from_name'], $restore_smtp_from_name); 
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore defaults", "config", 1, "smtp_to_email", $configCurrent['smtp_to_email'], $restore_smtp_to_email); 
                    header("Location: ../admin.php?smtpUpload=configRestored#smtp-settings");
                    exit();
                }
            }
        }
    } elseif (isset($_POST['user_role_submit'])) { // changing users roles in admin users section
        if (isset($_POST['user_id']) && isset($_POST['user_new_role'])) {
            $user_id = $_POST['user_id'];
            $user_new_role = $_POST['user_new_role'];

            include 'dbh.inc.php';
            $sql_user = "SELECT * FROM users WHERE id='$user_id';";
            $stmt_user = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_user, $sql_user)) {
                echo('Error: SQL Issue with `users` table.');
            } else {
                mysqli_stmt_execute($stmt_user);
                $result_user = mysqli_stmt_get_result($stmt_user);
                $rowCount_user = $result_user->num_rows;
                if ($rowCount_user < 1) {
                    echo ('Error: No Users in table: `users`.');
                } else {
                    $row_user = $result_user->fetch_assoc();
                    $user_sql_id = $row_user['id'];
                    $user_username = $row_user['username'];
                    $user_first_name = $row_user['first_name'];
                    $user_last_name = $row_user['last_name'];
                    $user_role = $row_user['role_id'];
                    $user_email = $row_user['email'];
                    $user_auth = $row_user['auth'];
                    $user_enabled = $row_user['enabled'];
                    
                    if ($user_id == $user_sql_id) {
                        if ($user_role != $user_new_role) {
                            $sql_user_update = "UPDATE users SET role_id='$user_new_role' WHERE id='$user_id';";
                            $stmt_user_update = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_user_update, $sql_user_update)) {
                                echo('<or class="red">SQL Error in Users Table</or>');
                            } else {
                                mysqli_stmt_execute($stmt_user_update);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $user_id, "role_id", $user_role, $user_new_role);
                                echo("User updated: id: $user_id, username: $user_username, role_id: $user_new_role.");
                            }
                        } else {
                            echo('Error: No change to user role.');
                        }
                    } else {
                        echo('Error: ID does not match.');
                    }
                }
            }
        } else {
            header("Location: ../admin.php?error=noUserSubmit#Users");
            exit();
        }
    } elseif (isset($_POST['user_enabled_submit'])) { // enabling / disabling users from the admin users section
        if (isset($_POST['user_id']) && isset($_POST['user_new_enabled'])) {
            $user_id = $_POST['user_id'];
            $user_new_enabled = $_POST['user_new_enabled'];

            include 'dbh.inc.php';
            $sql_user = "SELECT * FROM users WHERE id='$user_id';";
            $stmt_user = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_user, $sql_user)) {
                echo('Error: SQL Issue with `users` table.');
            } else {
                mysqli_stmt_execute($stmt_user);
                $result_user = mysqli_stmt_get_result($stmt_user);
                $rowCount_user = $result_user->num_rows;
                if ($rowCount_user < 1) {
                    echo ('Error: No Users in table: `users`.');
                } else {
                    $row_user = $result_user->fetch_assoc();
                    $user_sql_id = $row_user['id'];
                    $user_username = $row_user['username'];
                    $user_first_name = $row_user['first_name'];
                    $user_last_name = $row_user['last_name'];
                    $user_role = $row_user['role_id'];
                    $user_email = $row_user['email'];
                    $user_auth = $row_user['auth'];
                    $user_enabled = $row_user['enabled'];
                    
                    if ($user_id == $user_sql_id) {
                        if ($user_enabled != $user_new_enabled) {
                            $sql_user_update = "UPDATE users SET enabled='$user_new_enabled' WHERE id='$user_id';";
                            $stmt_user_update = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_user_update, $sql_user_update)) {
                                echo('<or class="red">SQL Error in Users Table</or>');
                            } else {
                                mysqli_stmt_execute($stmt_user_update);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $user_id, "enabled", $user_enabled, $user_new_enabled);
                                echo("User updated: id: $user_id, username: $user_username, enabled: $user_new_enabled.");
                            }
                        } else {
                            echo('Error: No change to user enabled state.');
                        }
                    } else {
                        echo('Error: ID does not match.');
                    }
                }
            }
        } else {
            header("Location: ../admin.php?error=noUserSubmit#Users");
            exit();
        }
    } elseif (isset($_POST['location-submit'])) { // adding locations e.g. sites/areas/shelves
        if (isset($_POST['index'])) { // come from the index page - this only happens when there are no sites/areas/shelves
            // print_r($_POST);
            // exit();

            if (!isset($_POST['site-name']))        { header("Location: ../index.php?error=missingSiteName");        exit(); }
            if (!isset($_POST['site-description'])) { header("Location: ../index.php?error=missingSiteDescription"); exit(); }
            if (!isset($_POST['area-name']))        { header("Location: ../index.php?error=missingAreaName");        exit(); }
            if (!isset($_POST['area-description'])) { header("Location: ../index.php?error=missingAreaDescription"); exit(); }
            if (!isset($_POST['shelf-name']))       { header("Location: ../index.php?error=missingShelfName");       exit(); }

            $site_name        = $_POST['site-name'];
            $site_description = $_POST['site-description'];
            $area_name        = $_POST['area-name'];
            $area_description = $_POST['area-description'];
            $shelf_name       = $_POST['shelf-name'];

            include 'dbh.inc.php';

            //insert to site
            $sql_site = "INSERT INTO site (name, description) 
                            VALUES (?, ?)";
            $stmt_site = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                header("Location: ../index.php?error=siteSQLConnection");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_site, "ss", $site_name, $site_description);
                mysqli_stmt_execute($stmt_site);
                // get new site id
                $site_id = mysqli_insert_id($conn); // ID of the new row in the table.
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "site", $site_id, "name", null, $site_name);

                //insert to area 
                $sql_area = "INSERT INTO area (name, description, site_id) 
                            VALUES (?, ?, ?)";
                $stmt_area = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                    header("Location: ../index.php?error=areaSQLConnection");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_area, "sss", $area_name, $area_description, $site_id);
                    mysqli_stmt_execute($stmt_area);
                    // get new area id
                    $area_id = mysqli_insert_id($conn); // ID of the new row in the table.
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "area", $area_id, "name", null, $area_name);
                    

                    //insert to area 
                    $sql_shelf = "INSERT INTO shelf (name, area_id) 
                                VALUES (?, ?)";
                    $stmt_shelf = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_shelf, $sql_shelf)) {
                        header("Location: ../index.php?error=shelfSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_shelf, "ss", $shelf_name, $area_id);
                        mysqli_stmt_execute($stmt_shelf);
                        // get new shelf id
                        $shelf_id = mysqli_insert_id($conn); // ID of the new row in the table.
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "shelf", $shelf_id, "name", null, $shelf_name);

                        // redirect back - all worked
                        header("Location: ../index.php?success=allAdded&site_id=$site_id&area_id=$area_id&shelf_id=$shelf_id");
                        exit();
                    }
                }
            }
        } elseif (isset($_POST['admin'])) { // come from the admin page - this is for adding new sites/areas/shelves
            if (isset($_POST['type']) && $_POST['type'] !== '') {
                $location_type = $_POST['type'];
                $location_name = $_POST['name'];
                if ($location_type == "site") {
                    $location_description = $_POST['description'];
                    $sql_location = "INSERT INTO $location_type (name, description) VALUES('$location_name', '$location_description')";
                } elseif ($location_type == "area") {
                    $location_parent = $_POST['parent'];
                    $location_description = $_POST['description'];
                    $sql_location = "INSERT INTO $location_type (name, description, site_id) VALUES('$location_name', '$location_description', $location_parent)";
                } elseif ($location_type == "shelf") {
                    $location_parent = $_POST['parent'];
                    $sql_location = "INSERT INTO $location_type (name, area_id) VALUES('$location_name', $location_parent)";
                } else {
                    header("Location: ../admin.php?error=incorrectType#stocklocations-settings");
                    exit();
                }
                $stmt_location = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_location, $sql_location)) {
                    header("Location: ../admin.php?error=".$type."SQLConnection#stocklocations-settings");
                    exit();
                } else {
                    mysqli_stmt_execute($stmt_location);
                    // get new site id
                    $location_id = mysqli_insert_id($conn); // ID of the new row in the table.
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "$location_type", $location_id, "name", null, $location_name);

                    header("Location: ../admin.php?success=".$location_type."LocationAdded&".$location_type."-id=$location_id#stocklocations-settings");
                    exit();
                }
            } else {
                header("Location: ../admin.php?error=typeMissing#stocklocations-settings");
                exit();
            }
        } else {
            header("Location: ../admin.php?error=location-submitIssue#stocklocations-settings");
            exit();
        }
    } elseif (isset($_POST['location-delete-submit'])) { // section for the location deleting in admin.inc.php
        // for the Stock Location Settings section on admin.inc.php page. This is only the deleting of the site/area/shelf
        if ($_POST['location-delete-submit'] == "site") {
            $site_id = $_POST['location-id'];
            $sql_check = "SELECT * FROM area WHERE site_id=$site_id AND deleted=0;";
            $stmt_check = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                exit();
            } else {
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $rowCount_check = $result_check->num_rows;

                if ($rowCount_check == 0) {
                    $sql_check2 = "SELECT * site WHERE id=$site_id AND deleted=0;";
                    $stmt_check2 = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_check2, $sql_check2)) {
                        header("Location: ../admin.php?error=sqlIssueReachingTable2#stocklocations-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_check2);
                        $result_check2 = mysqli_stmt_get_result($stmt_check2);
                        $rowCount_check2 = $result_check2->num_rows;
                        $row_check2 = $result_check2->fetch_assoc();

                        $current_stock_name = $row_check2['name'];

                        $sql_site = "UPDATE site SET deleted=1 WHERE id=$site_id;";
                        $stmt_site = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                            header("Location: ../admin.php?error=sqlIssueReachingTable2#stocklocations-settings");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt_site);
                            // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "site", $site_id, "deleted", 0, 1);

                            header("Location: ../admin.php?success=siteDeleted&id=$site_id#stocklocations-settings");
                            exit();
                        }
                    }
                         
                } else {
                    header("Location: ../admin.php?error=siteHasDependencies#stocklocations-settings");
                    exit();
                }
            }
        } elseif ($_POST['location-delete-submit'] == "area") {
            $area_id = $_POST['location-id'];
            $sql_check = "SELECT * FROM shelf WHERE area_id=$area_id AND deleted=0;";
            $stmt_check = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                exit();
            } else {
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $rowCount_check = $result_check->num_rows;

                if ($rowCount_check == 0) {
                    $sql_check1 = "SELECT * FROM area WHERE id=$area_id AND deleted=0;";
                    $stmt_check1 = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_check1);
                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                        $rowCount_check1 = $result_check1->num_rows;
                        $row_check1 = $result_check1->fetch_assoc();

                        $current_area_name = $row_check1['name'];
                        
                        $sql_area = "UPDATE area SET deleted=1 WHERE id=$area_id;";
                        $stmt_area = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                            header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt_area);
                            // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "area", $area_id, "deleted", 0, 1);

                            header("Location: ../admin.php?success=areaDeleted&id=$area_id#stocklocations-settings");
                            exit();
                        }
                    }
                } else {
                    header("Location: ../admin.php?error=areaHasDependencies#stocklocations-settings");
                    exit();
                }
            }
        } elseif ($_POST['location-delete-submit'] == "shelf") {
            $shelf_id = $_POST['location-id'];
            $sql_check = "SELECT * FROM item WHERE shelf_id=$shelf_id AND deleted=0;";
            $stmt_check = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                exit();
            } else {
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $rowCount_check = $result_check->num_rows;

                if ($rowCount_check == 0) {

                    $sql_check1 = "SELECT * FROM cable_item WHERE shelf_id=$shelf_id AND deleted=0;";
                    $stmt_check1 = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_check1);
                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                        $rowCount_check1 = $result_check1->num_rows;

                        if ($rowCount_check1 == 0) {

                            $sql_check2 = "SELECT * FROM shelf WHERE id=$shelf_id AND deleted=0;";
                            $stmt_check2 = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_check2, $sql_check2)) {
                                header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                                exit();
                            } else {
                                mysqli_stmt_execute($stmt_check2);
                                $result_check2 = mysqli_stmt_get_result($stmt_check2);
                                $rowCount_check2 = $result_check2->num_rows;
                                $row_check2 = $result_check2->fetch_assoc();

                                $current_shelf_name = $row_check2['name'];

                                $sql_shelf = "UPDATE shelf SET deleted=1 WHERE id=$shelf_id;";
                                $stmt_shelf = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_shelf, $sql_shelf)) {
                                    header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                                    exit();
                                } else {
                                    mysqli_stmt_execute($stmt_shelf);
                                    // update changelog
                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "shelf", $shelf_id, "deleted", 0, 1);

                                    header("Location: ../admin.php?success=shelfDeleted&id=$shelf_id#stocklocations-settings");
                                    exit();
                                }
                            }
                        } else {
                            header("Location: ../admin.php?error=shelfHasDependencies1#stocklocations-settings");
                            exit();
                        }
                    }
                } else {
                    header("Location: ../admin.php?error=shelfHasDependencies2#stocklocations-settings");
                    exit();
                }
            }
        } else {
            header("Location: ../admin.php?error=unknownLocationDeleteType#stocklocations-settings");
            exit();
        }

    } elseif (isset($_POST['location-edit-submit'])) { // editing location descriptions on admin.php modal edit popup
        if (isset($_POST['location-type'])) {
            if ($_POST['location-type'] == "site" || $_POST['location-type'] == "area" || $_POST['location-type'] == "shelf") {
                if (isset($_POST['location-id'])) {
                    if (isset($_POST['location-name'])) {
                        if (isset($_POST['location-description'])) {
                            $location_type = $_POST['location-type'];
                            $location_id = $_POST['location-id'];
                            $location_name = $_POST['location-name'];
                            $location_description = $_POST['location-description'];

                            if ($location_type == "shelf") {
                                // change the area_id
                                if (isset($_POST['location-parent-area'])) {
                                    $location_area_id = $_POST['location-parent-area'];

                                    $sql_check1 = "SELECT * FROM shelf WHERE id=$location_id;";
                                    $stmt_check1 = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                                        exit();
                                    } else {
                                        mysqli_stmt_execute($stmt_check1);
                                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                                        $rowCount_check1 = $result_check1->num_rows;
                                        $row_check1 = $result_check1->fetch_assoc();

                                        $current_area_id = $row_check1['area_id'];

                                        $sql = "UPDATE shelf 
                                                SET area_id=?
                                                WHERE id=?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../admin.php?error=sqlError&table=shelf#stocklocations-settings");
                                            
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "si", $location_area_id, $location_id);
                                            mysqli_stmt_execute($stmt);
                                            // update changelog
                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "shelf", $location_id, "area_id", $current_area_id, $location_area_id);
                                        }  
                                    }

                                    
                                }
                                
                            } elseif ($location_type == "area") {
                                // change the site_id
                                if (isset($_POST['location-parent-site'])) {
                                    $location_site_id = $_POST['location-parent-site'];

                                    $sql_check1 = "SELECT * FROM area WHERE id=$location_id;";
                                    $stmt_check1 = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                                        exit();
                                    } else {
                                        mysqli_stmt_execute($stmt_check1);
                                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                                        $rowCount_check1 = $result_check1->num_rows;
                                        $row_check1 = $result_check1->fetch_assoc();

                                        $current_site_id = $row_check1['site_id'];

                                        $sql = "UPDATE area 
                                                SET site_id=?
                                                WHERE id=?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../admin.php?error=sqlError&table=area#stocklocations-settings");
                                            
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "si", $location_site_id, $location_id);
                                            mysqli_stmt_execute($stmt);
                                            // update changelog
                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "area", $location_id, "site_id", $current_site_id, $location_site_id);
                                        } 
                                    }
                                     
                                }
                            }
                            
                            $sql = "SELECT *
                                    FROM $location_type
                                    WHERE id=?";
                            
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../admin.php?error=sqlError&table=$location_type#stocklocations-settings");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "i", $location_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $rowCount = mysqli_num_rows($result);
                                if ($rowCount < 1) {
                                    header("Location: ../admin.php?error=noRowsFound&table=$location_type#stocklocations-settings");
                                    exit();
                                } else {
                                    $row = $result->fetch_assoc();

                                    if ($location_type !== "shelf") {
                                        $sql = "UPDATE $location_type 
                                            SET name=?, description=? 
                                            WHERE id=?";
                                    } else {
                                        $sql = "UPDATE $location_type 
                                            SET name=?
                                            WHERE id=?";
                                    }
                                    
                                    
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        header("Location: ../admin.php?error=sqlError&table=$location_type#stocklocations-settings");
                                        exit();
                                    } else {
                                        if ($location_type !== "shelf") {
                                            mysqli_stmt_bind_param($stmt, "ssi", $location_name, $location_description, $location_id);
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "si", $location_name, $location_id);
                                        }   
                                        mysqli_stmt_execute($stmt);

                                        // update changelog
                                        if ($row['name'] !== $location_name) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "$location_type", $location_id, "name", $row['name'], $location_name); }
                                        if ($location_type !== "shelf") { 
                                            if ($row['description'] !== $location_description) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "$location_type", $location_id, "description", $row['description'], $location_description); }
                                        }
                        
                                        header("Location: ../admin.php?success=updated&type=$location_type&id=$location_id#stocklocations-settings");
                                        exit();
                                    }  
                                }
                            }
                        } else {
                            header("Location: ../admin.php?error=missingLocationDescription#stocklocations-settings");
                            exit();
                        }
                    } else {
                        header("Location: ../admin.php?error=missingLocationName#stocklocations-settings");
                        exit();
                    }
                } else {
                    header("Location: ../admin.php?error=missingLocationId#stocklocations-settings");
                    exit();
                }
            } else {
                header("Location: ../admin.php?error=incorrectLocationType#stocklocations-settings");
                exit();
            }
        } else {
            header("Location: ../admin.php?error=missingLocationType#stocklocations-settings");
            exit();
        }
    } elseif (isset($_POST['imagemanagement-submit'])) { // image management section in the admin.php page
        if (isset($_POST['file-name'])) {
            if (isset($_POST['file-links'])) {
                if ($_POST['file-links'] == 0) {
                    $filename = $_POST['file-name'];
                    exec("rm ../assets/img/stock/$filename");
                    header("Location: ../admin.php?success=imageDeleted#imagemanagement-settings");
                    exit();
                } else {
                    header("Location: ../admin.php?error=linksExist#imagemanagement-settings");
                    exit();
                }
            } else {
                header("Location: ../admin.php?error=missingFileLinks#imagemanagement-settings");
                exit();
            }
        } else {
            header("Location: ../admin.php?error=missingFileName#imagemanagement-settings");
            exit();
        }
    } elseif (isset($_GET['mail-notification'])) { // mail notification section in admin.php page. this is ajax'd
        $results = [];

        function msg($text, $type) {
            if ($type == 'error') {
                $class="red";
            } else {
                $class="green";
            }
            $head = '<or class="'.$class.'">';
            $foot = '</or>';

            return $head.$text.$foot;
        }

        if (isset($_GET['notification'])) {
            $notification = $_GET['notification'];
            if (isset($_GET['value'])) {
                $value = $_GET['value'];
                if ($value == 0 || $value == '0' || $value == 1 || $value == '1') {
                    include 'dbh.inc.php';

                    $sql = "SELECT * FROM notifications WHERE id='$notification'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $results[] = msg('SQL connection issue.', 'error');
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount == 1) {
                            $row = $result->fetch_assoc();
                            $id = $row['id'];
                            $title = $row['title'];
                            $prev_value = $row['enabled'];

                            $state = $value == 1 ? 'enabled' : 'disabled';
                            
                            $sql_update = "UPDATE notifications SET enabled=? WHERE id='$id'";
                            $stmt_update = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                                $results[] = msg('SQL connection issue.', 'error');
                            } else {
                                mysqli_stmt_bind_param($stmt_update, "s", $value);
                                mysqli_stmt_execute($stmt_update);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "notifications", $id, 'enabled', $prev_value, $value);
                                $results[] = msg("$title notificaiton $state!", 'success');
                            }
                        } else {

                        }
                    }
                } else {
                    $results[] = msg('Invalid value specified.', 'error');
                }
            } else {
                $results[] = msg('No value specified.', 'error');
            }

        } else {
            $results[] = msg('No notification type specified.', 'error');
        }

        echo(json_encode($results));

    } elseif (isset($_POST['stocklocation-submit'])) { // editing location info from admin.php
        if (isset($_POST['type'])) {
            $typesArray = ['site', 'area', 'shelf'];
            if (in_array($_POST['type'], $typesArray)) {
                if (isset($_POST['id']) && isset($_POST['name'])) {
                    $type = $_POST['type'];
                    $id = $_POST['id'];
                    $name = $_POST['name'];

                    $sql_check1 = "SELECT * FROM $type WHERE id=$id;";
                    $stmt_check1 = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_check1);
                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                        $rowCount_check1 = $result_check1->num_rows;
                        $row_check1 = $result_check1->fetch_assoc();

                        $current_name = $row_check1['name'];

                        $sql = "UPDATE $type SET name='$name' WHERE id=$id";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../admin.php?error=".$type."ConnectionSQL#stocklocations-settings");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt);
                            // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "$type", $id, "name", $current_name, $name);

                            header("Location: ../admin.php?success=updated&type=$type&id=$id#stocklocations-settings");
                            exit();
                        }  
                    }
                    
                } else {
                    header("Location: ../admin.php?error=propertyMissing#stocklocations-settings");
                    exit();
                }            
            } else {
                header("Location: ../admin.php?error=unknwonType#stocklocations-settings");
                exit();
            }
        } else {
            header("Location: ../admin.php?error=noType#stocklocations-settings");
            exit();
        }
    } elseif (isset($_POST['profile-submit'])) { // profile.php info e.g. email and name updates

        if (isset($_POST['id'])) {
            if ($_POST['id'] == $_SESSION['user_id']) {
                // user matches
                if (isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['email'])) {
                    if ($_POST['first-name'] !== '' && $_POST['last-name'] !== '' && $_POST['email'] !== '') {
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                            $id = $_POST['id'];
                            $first_name = $_POST['first-name'];
                            $last_name = $_POST['last-name'];
                            $email = strtolower($_POST['email']);
                            
                            $sql_users = "SELECT users.email
                                            FROM users 
                                            WHERE email='$email' AND auth='local' AND id!=$id";
                            $stmt_users = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                                header("Location: ../profile.php?error=usersConnectionSQL&email=$email&first_name=$first_name&last_name=$last_name");
                                exit();
                            } else {
                                mysqli_stmt_execute($stmt_users);
                                $result = mysqli_stmt_get_result($stmt_users);
                                $rowCount = $result->num_rows;
                                if ($rowCount > 0) {
                                    header("Location: ../profile.php?error=emailExists&email=$email&first_name=$first_name&last_name=$last_name");
                                    exit();
                                } else {
                                    // no emails exist in the row, can be used.
                                    $sql_check1 = "SELECT first_name, last_name, email FROM users WHERE id=$id;";
                                    $stmt_check1 = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_check1, $sql_check1)) {
                                        header("Location: ../admin.php?error=sqlIssueReachingTable#stocklocations-settings");
                                        exit();
                                    } else {
                                        mysqli_stmt_execute($stmt_check1);
                                        $result_check1 = mysqli_stmt_get_result($stmt_check1);
                                        $rowCount_check1 = $result_check1->num_rows;
                                        $row_check1 = $result_check1->fetch_assoc();

                                        $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE id=$id";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../profile.php?error=usersConnectionSQL&email=$email&first_name=$first_name&last_name=$last_name");
                                            exit();
                                        } else {
                                            mysqli_stmt_execute($stmt);
                                            // update changelog
                                            if ($row_check1['first_name'] !== $first_name) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $id, "first_name", $row_check1['first_name'], $first_name); }
                                            if ($row_check1['last_name'] !== $last_name) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $id, "last_name", $row_check1['last_name'], $last_name); }
                                            if ($row_check1['email'] !== $email) { addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $id, "email", $row_check1['email'], $email); }
                                            header("Location: ../profile.php?success=profileUpdated");
                                            exit();
                                        }  
                                    }
                                    
                                }
                            }
                        } else {
                            header("Location: ../profile.php?error=emailFormat");
                            exit();
                        }
                    } else {
                        header("Location: ../profile.php?error=emptyFields");
                        exit();
                    }
                } else {
                    header("Location: ../profile.php?error=missingFields");
                    exit();
                }
            } else {
                header("Location: ../profile.php?error=idMissmatch");
                exit();
            }
        } else {
            header("Location: ../profile.php?error=idMissing");
            exit();
        }
    } elseif (isset($_POST['card-modify'])) { // profile.php swipe cards - assigning and re-assigning.
        if (isset($_POST['type'])) {
            if (isset($_POST['card'])) {
                if (isset($_POST['cardData'])) {
                    $user_id = $_SESSION['user_id'];
                    $card_number = $_POST['cardData'];
                    $card_card = $_POST['card'];
                    $card_type= $_POST['type'];
                    if ($card_type == 'assign') {
                        $return_q_text = 'Assigned';
                    } else {
                        $return_q_text = 'Reassigned';
                    }
                    if (is_numeric($card_number)) {
                        if ($card_card == 1) {
                            $card_field = 'card_primary';
                        } elseif ($card_card == 2) {
                            $card_field = 'card_secondary';
                        } else {
                            header("Location: ../profile.php?error=cardCardNoMatch");
                            exit();
                        }

                        $sql_check = "SELECT $card_field AS card FROM users WHERE id=$user_id";
                        $stmt_check = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                            header("Location: ../profile.php?sqlerror=usersTableError");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt_check);
                            $result_check = mysqli_stmt_get_result($stmt_check);
                            $rowCount_check = $result_check->num_rows;
                            if ($rowCount_check != 1) {
                                header("Location: ../profile.php?error=incorrectCheckRowCount");
                                exit();
                            } else {
                                $row_check = $result_check->fetch_assoc();
                                $card_current = $row_check['card'];
                            }
                        }

                        $sql = "UPDATE users SET $card_field='$card_number' WHERE id=$user_id";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../profile.php?error=usersConnectionSQL");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt);
                            // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $user_id, $card_field, $card_current, $card_number);
                            header("Location: ../profile.php?success=card$return_q_text&card=$card_card&card_number=$card_number");
                            exit();
                        }  

                    } else {
                        header("Location: ../profile.php?error=cardNumberNotNumeric");
                        exit();
                    }
                } else {
                    header("Location: ../profile.php?error=missingCardData");
                    exit();
                }
            } else {
                header("Location: ../profile.php?error=missingCard");
                exit();
            }
        } else {
            header("Location: ../profile.php?error=missingType");
            exit();
        }
    } elseif (isset($_POST['card-remove'])) { // profile.php swipe cards - deassigning.
        if (isset($_POST['card'])) {
            $card_card = $_POST['card'];
            $user_id = $_SESSION['user_id'];
            if ($card_card == 1) {
                $card_field = 'card_primary';
            } elseif ($card_card == 2) {
                $card_field = 'card_secondary';
            } else {
                header("Location: ../profile.php?error=cardCardNoMatch");
                exit();
            }
            $sql_check = "SELECT $card_field AS card FROM users WHERE id=$user_id";
            $stmt_check = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                header("Location: ../profile.php?sqlerror=usersTableError");
                exit();
            } else {
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $rowCount_check = $result_check->num_rows;
                if ($rowCount_check != 1) {
                    header("Location: ../profile.php?error=incorrectCheckRowCount");
                    exit();
                } else {
                    $row_check = $result_check->fetch_assoc();
                    $card_current = $row_check['card'];
                }
            }

            $sql = "UPDATE users SET $card_field=null WHERE id=$user_id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../profile.php?error=usersConnectionSQL");
                exit();
            } else {
                mysqli_stmt_execute($stmt);
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "users", $user_id, $card_field, $card_current, null);
                header("Location: ../profile.php?success=cardDeassigned&card=$card_card");
                exit();
            }
        } else {
            header("Location: ../profile.php?error=missingCard");
            exit();
        }
    } elseif (isset($_POST['theme-upload'])) { // theme uploading from the theme-test page
        if (isset($_POST['submit']) && $_POST['submit'] == 'Upload Theme') {
            if (isset($_POST['theme-name'])) {
                if (isset($_POST['theme-file-name'])) {
                    if (isset($_FILES['css-file']['name']) && $_FILES['css-file']['name'] !== '') {
                        $errors = [];
                        
                        if (!isset($_FILES['css-file']))           { $errors[] = "notSet-File";          }
                        if ($_FILES['css-file']['name'] == '')     { $errors[] = "notSet-File-name";     }
                        if ($_FILES['css-file']['size'] == '')     { $errors[] = "notSet-File-size";     }
                        if ($_FILES['css-file']['tmp_name'] == '') { $errors[] = "notSet-File-tmp_name"; }
                        if ($_FILES['css-file']['type'] == '')     { $errors[] = "notSet-File-type";     }

                        $fileName = $_FILES['css-file']['name'];                            // Get uploaded file name
                        $fileSize = $_FILES['css-file']['size'];                            // Get uploaded file size
                        $explode = explode('.',$fileName);                              // Get file extension explode
                        $fileExtension = strtolower(end($explode));                     // Get file extension

                        if ($fileExtension !== "css")                { $errors[] = "wrongFileExtension";   } // File extenstion match?
                        if ($fileSize > 10000000)                   { $errors[] = "above10MB";            } // Within Filesize limits?
                        
                        if (str_contains($_POST['theme-file-name'], '.'))         { $errors[] = "File-name-includes-extension"; }
                        if (str_contains($_POST['theme-file-name'], '/')) { $errors[] = "File-name-includes-path"; }
                        if (preg_match('/[^\p{L}\p{N}]+/u', $_POST['theme-name'])) { $errors[] = "theme-name-includes-speical-chars"; }
        
                        $theme_name = $_POST['theme-name'];
                        $theme_file_name = $_POST['theme-file-name'].'.css';
                        $file_tmp_name = $_FILES['css-file']['tmp_name'];
        
                        include 'dbh.inc.php';
        
                        $sql = "SELECT * FROM theme WHERE name='$theme_name' OR file_name='$theme_file_name'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../test-theme.php?sqlerror=SQLconnection");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            if ($rowCount != 0) {
                                $errors[] = 'theme-matches-existing';
                            }
                        }
        
                        if (empty($errors)) { // IF file is existing and all fields exist:;
                            $uploadPath = '../assets/css/'.$theme_file_name;
                            $didUpload = move_uploaded_file($_FILES['css-file']['tmp_name'], $uploadPath);
                            if ($didUpload) {
                                $sql_theme = "INSERT INTO theme (name, file_name) 
                                                VALUES (?, ?)";
                                $stmt_theme = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
                                    header("Location: ../theme-test.php?error=SQLconnection&theme-name=$theme_name");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_theme, "ss", $theme_name, $theme_file_name);
                                    mysqli_stmt_execute($stmt_theme);
                                    // get new theme id
                                    $theme_id = mysqli_insert_id($conn); // ID of the new row in the table.
                                    // update changelog
                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "theme", $theme_id, "name", null, $theme_name);
                                    header("Location: ../theme-test.php?success=uploaded&theme-name=$theme_name");
                                    exit();
                                }
                            } else {
                                $errors[] = "uploadFailed";
                                $errorQ = implode('+', $errors);
                                header("Location: ../theme-test.php?errors=$errorQ");
                                exit();
                            }
                        } else {
                            $errorQ = implode('+', $errors);
                            header("Location: ../theme-test.php?errors=$errorQ");
                            exit();
                        } 
                    } else {
                        header("Location: ../theme-test.php?error=uploadedFileNameMissing");
                        exit();
                    }
                } else {
                    header("Location: ../theme-test.php?error=fileNameMissing");
                    exit();
                }
            } else {
                header("Location: ../theme-test.php?error=themeNameMissing");
                exit();
            }
        } else {
            header("Location: ../theme-test.php?error=submitMissing");
            exit();
        }
    } else {
        header("Location: ../admin.php?error=submitIssue");
        exit();
    }
}

?>