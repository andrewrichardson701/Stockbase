<?php
// SUBMITTING THE ADMIN CONFIG CHANGES. WILL UPDATE THE INFO IN THE CONFIG TABLE ONLY 
// LEAVING THE CONFIG_DEFAULT TABLE UNTOUCHED

if (!isset($_POST['global-submit']) && !isset($_POST['global-restore-defaults']) && !isset($_POST['ldap-submit']) && !isset($_POST['ldap-restore-defaults'])) {
    header("Location: ../admin.php?error=noSubmit");
    exit();
} else {

    if (isset($_POST['global-submit'])) { // GLOBAL saving
        $errors = [];
        
        if (isset($_POST['banner_color']))   { $config_banner_color  = $_POST['banner_color'];  }
        if (isset($_FILES['logo_image']) )   { $config_logo_image    = $_FILES['logo_image'];   }
        if (isset($_FILES['favicon_image'])) { $config_favicon_image = $_FILES['favicon_image']; }

        if ( isset($_POST['banner_color'])) {
            $post_banner_color = $_POST['banner_color'];
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
                    $moveName = $fileNameShort.'-'.$timedate.'.'.$fileExtension;
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
        if (isset($_FILES['logo_image'])) {
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
        if (isset($_FILES['favicon_image'])) {
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

        if (count($queryStrings) < 1) {
            $queryString = '?'.implode('&', array_slice($queryStrings));
        } elseif (count($queryStrings) == 1) {
            $queryString = '?'.$queryStrings[0];
        } else {
            $queryString = '';
        }
        header("Location: ../admin.php$queryStringid#global-settings");
        exit();

    } elseif (isset($_POST['global-restore-defaults'])) {
        include 'dbh.inc.php';

        $sql_config = "SELECT banner_color, logo_image, favicon_image FROM config_default ORDER BY id LIMIT 1";
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
                    $restore_banner_color = $config['banner_color'];
                    $restore_logo_image = $config['logo_image'];
                    $restore_favicon_image = $config['favicon_image'];
                }
                $sql_upload = "UPDATE config SET banner_color=?, logo_image=?, favicon_image=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    header("Location: ../admin.php?sqlerror=config_noUpdate#global-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "sss", $restore_banner_color, $restore_logo_image, $restore_favicon_image);
                    mysqli_stmt_execute($stmt_upload);
                    header("Location: ../admin.php?restore=globalSuccess#global-settings");
                    exit();
                }
            }
        }

    } elseif (isset($_POST['ldap-submit'])) { // LDAP saving

        if (isset($_POST['auth-username']))         { $config_ldap_username         = $_POST['auth-username'];         } else { $config_ldap_username         = ''; }
        if (isset($_POST['auth-password']))         { $config_ldap_password         = $_POST['auth-password'];         } else { $config_ldap_password         = ''; }
        if (isset($_POST['auth-password-confirm'])) { $config_ldap_password_confirm = $_POST['auth-password-confirm']; } else { $config_ldap_password_confirm = ''; }
        if (isset($_POST['auth-domain']))           { $config_ldap_domain           = $_POST['auth-domain'];           } else { $config_ldap_domain           = ''; }
        if (isset($_POST['auth-host']))             { $config_ldap_host             = $_POST['auth-host'];             } else { $config_ldap_host             = ''; }
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
            include 'dbh.inc.php';
            $sql_upload = "UPDATE config SET ldap_username=?, ldap_password=?, ldap_domain=?, ldap_host=?, ldap_port=?, ldap_basedn=?, ldap_usergroup=?, ldap_userfilter=? WHERE id=1";
            $stmt_upload = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                $errors[] = "ldapUploadSQLerror";
                header("Location: ../admin.php?error=ldapUpload#ldap-settings");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_upload, "ssssssss", $config_ldap_username, $config_ldap_password, $config_ldap_domain, $config_ldap_host, $config_ldap_port, $config_ldap_basedn, $config_ldap_usergroup, $config_ldap_userfilter);
                mysqli_stmt_execute($stmt_upload);
                header("Location: ../admin.php?ldapUpload=success#ldap-settings");
                exit();
            }
        }
    } elseif (isset($_POST['ldap-restore-defaults'])) { 
        // RESTORE LDAP
        include 'dbh.inc.php';

        $sql_config = "SELECT ldap_username, ldap_password, ldap_domain, ldap_host, ldap_port, ldap_basedn, ldap_usergroup, ldap_userfilter FROM config_default ORDER BY id LIMIT 1";
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
                    $restore_ldap_username   = $config['ldap_username'];       
                    $restore_ldap_password   = $config['ldap_password'];      
                    $restore_ldap_domain     = $config['ldap_domain'];          
                    $restore_ldap_host       = $config['ldap_host'];            
                    $restore_ldap_port       = $config['ldap_port'];            
                    $restore_ldap_basedn     = $config['ldap_basedn'];          
                    $restore_ldap_usergroup  = $config['ldap_usergroup'];      
                    $restore_ldap_userfilter = $config['ldap_userfilter'];     
                }
                $sql_upload = "UPDATE config SET ldap_username=?, ldap_password=?, ldap_domain=?, ldap_host=?, ldap_port=?, ldap_basedn=?, ldap_usergroup=?, ldap_userfilter=? WHERE id=1";
                $stmt_upload = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                    $errors[] = "ldapUploadSQLerror";
                    header("Location: ../admin.php?error=ldapRestoreUpload#ldap-settings");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_upload, "ssssssss", $restore_ldap_username, $restore_ldap_password, $restore_ldap_domain, $restore_ldap_host, $restore_ldap_port, $restore_ldap_basedn, $restore_ldap_usergroup, $restore_ldap_userfilter);
                    mysqli_stmt_execute($stmt_upload);
                    header("Location: ../admin.php?ldapUpload=configRestoreds#ldap-settings");
                    exit();
                }
            }
        }

    } else {
        header("Location: ../admin.php?error=submitIssue");
        exit();
    }


}








?>