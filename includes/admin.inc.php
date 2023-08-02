<?php
// SUBMITTING THE ADMIN CONFIG CHANGES. WILL UPDATE THE INFO IN THE CONFIG TABLE ONLY 
// LEAVING THE CONFIG_DEFAULT TABLE UNTOUCHED
// print_r($_POST);
//         exit();

if (!isset($_POST['global-submit']) && !isset($_POST['global-restore-defaults']) && !isset($_POST['ldap-submit']) && !isset($_POST['ldap-restore-defaults']) && !isset($_POST['smtp-submit']) && !isset($_POST['smtp-restore-defaults']) && !isset($_POST['user_role_submit']) && !isset($_POST['user_enabled_submit'])) {
    header("Location: ../admin.php?error=noSubmit");
    exit();
} else {
    if (isset($_POST['global-submit'])) { // GLOBAL saving
        $errors = [];
         
        $config_system_name   = isset($_POST['system_name'])        ? $_POST['system_name']        : '';
        $config_banner_color  = isset($_POST['banner_color'])       ? $_POST['banner_color']       : '';
        $config_logo_image    = isset($_FILES['logo_image'])        ? $_FILES['logo_image']        : '';
        $config_favicon_image = isset($_FILES['favicon_image'])     ? $_FILES['favicon_image']     : '';
        $config_currency      = isset($_POST['currency_selection']) ? $_POST['currency_selection'] : '';
        $config_sku_prefix    = isset($_POST['sku_prefix'])         ? $_POST['sku_prefix']         : '';

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
            }
        }

        if ( isset($_POST['banner_color']) && $config_banner_color !== '') {
            $post_banner_color = $config_banner_color;
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
                }
                
            } else {
                $errors[] = "invalidHexFormat";
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
            $sql_upload = "UPDATE config SET currency=? WHERE id=1";
            $stmt_upload = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                $errors[] = "currencySqlError";
            } else {
                mysqli_stmt_bind_param($stmt_upload, "s", $post_currency);
                mysqli_stmt_execute($stmt_upload);
                $queryStrings[] = "currencyUpload=success";
            }
        }

        if (isset($_POST['sku_prefix']) && $config_sku_prefix !== '') {
            $post_sku_prefix = $_POST['sku_prefix'];
            include 'dbh.inc.php';

            $sql_prefix = "SELECT sku_prefix FROM config WHERE id=1";
            $stmt_prefix = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_prefix, $sql_prefix)) {
                $errors[] = "configTableSQLConnection";
            } else {
                mysqli_stmt_execute($stmt_prefix);
                $result_prefix = mysqli_stmt_get_result($stmt_prefix);
                $rowCount_prefix = $result_prefix->num_rows;
                if ($rowCount_prefix == 1) {
                    $row_prefix = $result_prefix->fetch_assoc();
                    $current_sku_prefix = $row_prefix['sku_prefix'];

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
                        }
                    }
                }
            }           
        }

        if (count($queryStrings) < 1) {
            $queryString = '?'.implode('&', array_slice($queryStrings));
        } elseif (count($queryStrings) == 1) {
            $queryString = '?'.$queryStrings[0];
        } else {
            $queryString = '';
        }
        header("Location: ../admin.php$queryString#global-settings");
        exit();

    } elseif (isset($_POST['global-restore-defaults'])) {
        include 'dbh.inc.php';

        $sql_config = "SELECT system_name, banner_color, logo_image, favicon_image, currency, sku_prefix FROM config_default ORDER BY id LIMIT 1";
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
                    $restore_banner_color  = $config['banner_color'];
                    $restore_logo_image    = $config['logo_image'];
                    $restore_favicon_image = $config['favicon_image'];
                    $restore_currency      = $config['currency'];
                    $restore_sku_prefix    = $config['sku_prefix'];
                }
                $sql_upload = "UPDATE config SET system_name=?, banner_color=?, logo_image=?, favicon_image=?, currency=?, sku_prefix=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    header("Location: ../admin.php?sqlerror=config_noUpdate#global-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "ssssss", $restore_system_name, $restore_banner_color, $restore_logo_image, $restore_favicon_image, $restore_currency, $restore_sku_prefix);
                    mysqli_stmt_execute($stmt_upload);
                    header("Location: ../admin.php?restore=globalSuccess#global-settings");
                    exit();
                }
            }
        }

    } elseif (isset($_POST['ldap-submit'])) { // LDAP saving

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
                    header("Location: ../admin.php?ldapUpload=success#ldap-settings");
                    exit();
                }
            } else { 
                header("Location: ../admin.php?error=passwordMatch");
                exit();
            }
            
        }
    } elseif (isset($_POST['ldap-restore-defaults'])) { 
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
                    header("Location: ../admin.php?ldapUpload=configRestored#ldap-settings");
                    exit();
                }
            }
        }
    } elseif (isset($_POST['smtp-submit'])) {
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
                header("Location: ../admin.php?smtpUpload=success#smtp-settings");
                exit();
            }
        }
    } elseif (isset($_POST['smtp-restore-defaults'])) { 
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
                    header("Location: ../admin.php?smtpUpload=configRestored#smtp-settings");
                    exit();
                }
            }
        }
    } elseif (isset($_POST['user_role_submit'])) { 
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
    } elseif (isset($_POST['user_enabled_submit'])) { 
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
    } else {
        header("Location: ../admin.php?error=submitIssue");
        exit();
    }
}








?>