<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// SAVING INFO FOR THE CABLESTOCK PAGE. THIS IS FOR REMOVING AND ADDING STOCK.

// print_r($_POST);
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

$redirect_url = "optics.php";
$queryChar = strpos($redirect_url, "?") !== false ? '&' : '?';

include 'changelog.inc.php';
include 'smtp.inc.php';

function updateOpticTransactions($table_name, $item_id, $type, $reason, $date, $time, $username, $site_id) {
    global $redirect_url, $queryChar;
    include 'dbh.inc.php';
    $cost = 0;
    $sql_trans = "INSERT INTO optic_transaction (table_name, item_id, type, reason, date, time, username, site_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_trans = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
        header("Location: ../".$redirect_url.$queryChar."error=optic_transactionConnectionSQL");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt_trans, "ssssssss", $table_name, $item_id, $type, $reason, $date, $time, $username, $site_id);
        mysqli_stmt_execute($stmt_trans);
        echo ("transaction added");
    }  
} 
function getCurrentURL() {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT base_url FROM config WHERE id=1";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."error=configTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
            exit();
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
            exit();
        } else {
            $row = $result->fetch_assoc();
            $config_base_url = $row['base_url'];
        }
    }

    $sql = "SELECT base_url FROM config_default WHERE id=1";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."error=configTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
            exit();
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
            exit();
        } else {
            $row = $result->fetch_assoc();
            $config_d_base_url = $row['base_url'];
        }
    }

    $base_url = isset($config_base_url) ? $config_base_url : (isset($config_d_base_url) ? $config_d_base_url : 'error.local');
    return $base_url;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'dbh.inc.php';

    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    } 
    
    if (isset($_POST['csrf_token'])) {
        if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
            header("Location: ../".$redirect_url.$queryChar."error=csrfMissmatch");
            exit();
        }
    } else {
        header("Location: ../".$redirect_url.$queryChar."error=csrfMissmatch");
        exit();
    }

    if (isset($_POST['add-optic-submit'])) {
        print_r($_POST);
        $site = isset($_POST['site']) ? $_POST['site'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $speed = isset($_POST['speed']) ? $_POST['speed'] : '';
        $connector = isset($_POST['connector']) ? $_POST['connector'] : '';
        $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
        $vendor = isset($_POST['vendor']) ? $_POST['vendor'] : '';
        $model = isset($_POST['model']) ? mysqli_real_escape_string($conn, $_POST['model']) : '';
        $serial = isset($_POST['serial']) ? mysqli_real_escape_string($conn, $_POST['serial']) : '';
        if ($_POST['add-optic-submit'] !== 1) {
            $queryString = 'add-form=1';
        } else {
            $queryString = 'add-form=0';
        }
        
        $queryString .= "&form-site=$site&form-type=$type&form-speed=$speed&form-connector=$connector&form-mode=$mode&form-vendor=$vendor&form-model=$model&form-serial=$serial";

        if (isset($_POST['site'])) {
            if (isset($_POST['type'])) {
                if (isset($_POST['speed'])) {
                    if (isset($_POST['connector'])) {
                        if (isset($_POST['mode'])) {
                            if (isset($_POST['vendor'])) {
                                if (isset($_POST['model'])) {
                                    if (isset($_POST['serial'])) {
                                        $sql = "SELECT serial_number
                                                FROM optic_item 
                                                WHERE deleted=0 AND serial_number='$serial'";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            $rowCount = $result->num_rows;
                                            if ($rowCount > 0) {
                                                header("Location: ../".$redirect_url.$queryChar.$queryString."&error=duplicateSerial");
                                                exit();
                                            } else {
                                                $sql = "SELECT id
                                                        FROM optic_item 
                                                        WHERE 
                                                            deleted = 1
                                                            AND model = '$model'
                                                            AND vendor_id = '$vendor'
                                                            AND serial_number='$serial'
                                                            AND type_id = '$type'
                                                            AND connector_id = '$connector'
                                                            AND mode = '$mode'
                                                            AND speed_id = '$speed'
                                                            AND site_id = '$site'";
                                                $stmt = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                    header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                                                    exit();
                                                } else {
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    $rowCount = $result->num_rows;
                                                    if ($rowCount > 0) {
                                                        $row = $result->fetch_assoc();
                                                        $id = $row['id'];
                                                        $sql = "UPDATE optic_item SET deleted=0, quantity=1
                                                                WHERE id=?";
                                                        $stmt = mysqli_stmt_init($conn);
                                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                            header("Location: ../".$redirect_url.$queryChar."optic_item_id=$id&error=optic_itemTableSQLConnection-RestoreItem");
                                                            exit();
                                                        } else {
                                                            mysqli_stmt_bind_param($stmt, "s", $id);
                                                            mysqli_stmt_execute($stmt);

                                                            $table_name = 'optic_item';
                                                            $type = "restore";
                                                            $reason = "Item Restored";
                                                            $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                                            $time = date('H:i:s'); // current time in HH:MM:SS format
                                                            $username = $_SESSION['username'];

                                                            updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site);
                                                        
                                                            // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                                            // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                                            // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                                            // // update changelog
                                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore Item", $table_name, $id, "deleted", 1, 0);
                                                            header("Location: ../".$redirect_url.$queryChar."success=restored");
                                                            exit();
                                                        }
                                                    } else {
                                                        $sql = "SELECT id
                                                                FROM optic_item 
                                                                WHERE 
                                                                    deleted = 1
                                                                    AND serial_number='$serial' 
                                                                LIMIT 1";
                                                        $stmt = mysqli_stmt_init($conn);
                                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                            header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                                                            exit();
                                                        } else {
                                                            mysqli_stmt_execute($stmt);
                                                            $result = mysqli_stmt_get_result($stmt);
                                                            $rowCount = $result->num_rows;
                                                            if ($rowCount > 0) {
                                                                $row = $result->fetch_assoc();
                                                                $id = $row['id'];
                                                                // update the info with the new info
                                                                $sql = "UPDATE optic_item 
                                                                        SET 
                                                                            deleted=0, 
                                                                            quantity=1,
                                                                            model = '$model',
                                                                            vendor_id = '$vendor',
                                                                            type_id = '$type',
                                                                            connector_id = '$connector',
                                                                            mode = '$mode',
                                                                            speed_id = '$speed',
                                                                            site_id = '$site'
                                                                        WHERE id=?";
                                                                $stmt = mysqli_stmt_init($conn);
                                                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                                    header("Location: ../".$redirect_url.$queryChar."optic_item_id=$id&error=optic_itemTableSQLConnection-RestoreItem");
                                                                    exit();
                                                                } else {
                                                                    mysqli_stmt_bind_param($stmt, "s", $id);
                                                                    mysqli_stmt_execute($stmt);

                                                                    $table_name = 'optic_item';
                                                                    $type = "restore";
                                                                    $reason = "Item Restored";
                                                                    $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                                                    $time = date('H:i:s'); // current time in HH:MM:SS format
                                                                    $username = $_SESSION['username'];

                                                                    updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site);
                                                                
                                                                    // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                                                    // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                                                    // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                                                    // // update changelog
                                                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore Item", $table_name, $id, "deleted", 1, 0);
                                                                    header("Location: ../".$redirect_url.$queryChar."success=restored");
                                                                    exit();
                                                                }
                                                            } else {
                                                                $sql = "INSERT INTO optic_item (model, vendor_id, serial_number, type_id, connector_id, mode, speed_id, site_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                                                $stmt = mysqli_stmt_init($conn);
                                                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                                    header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_itemConnectionInsert");
                                                                    exit();
                                                                } else {
                                                                    mysqli_stmt_bind_param($stmt, "ssssssss", $model, $vendor, $serial, $type, $connector, $mode, $speed, $site);
                                                                    mysqli_stmt_execute($stmt);
                                                                    $insert_id = mysqli_insert_id($conn); // ID of the new row in the table
                                                                    
                                                                    $table_name = 'optic_item';
                                                                    $tran_type = "add";
                                                                    $reason = "Item Added";
                                                                    $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                                                    $time = date('H:i:s'); // current time in HH:MM:SS format
                                                                    $username = $_SESSION['username'];
                                    
                                                                    updateOpticTransactions($table_name, $insert_id, $tran_type, $reason, $date, $time, $username, $site);
                                                                
                                                                    // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                                                    // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                                                    // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                                                    // // update changelog
                                                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Item", $table_name, $insert_id, "serial_number", null, $serial);

                                                                    header("Location: ../".$redirect_url.$queryChar.$queryString."&success=opticAdded");
                                                                    exit();
                                                                }
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingSerial");
                                        exit();
                                    }
                                } else {
                                    header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingModel");
                                    exit();
                                }
                            } else {
                                header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingVendor");
                                exit();
                            }
                        } else {
                            header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingMode");
                            exit();
                        }
                    } else {
                        header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingConnector");
                        exit();
                    }
                } else {
                    header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingSpeed");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingType");
                exit();
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar.$queryString."&error=missingSite");
            exit();
        }
    } elseif (isset($_POST['optic-delete-submit'])) { 
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            if (is_numeric($id)) {
                if ($id > 0) {
                    if (isset($_POST['reason']) && $_POST['reason'] !== '') {
                        $delete_reason = 'DELETED: '.$_POST['reason'];

                        $sql = "SELECT I.id AS i_id, I.site_id AS i_site_id
                                FROM optic_item AS I
                                WHERE I.deleted=0 AND I.id='$id'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            if ($rowCount < 1) {
                                header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                                exit();
                            } elseif ($rowCount > 1) {
                                header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                                exit();
                            } else {
                                // correct amount found, continue.
                                $row = $result->fetch_assoc();
                                $site_id = $row['i_site_id'];

                                $comment = $delete_reason; 
                                $datetime = time();

                                $sql = "SELECT I.site_id AS i_site_id
                                        FROM optic_item AS I
                                        WHERE I.deleted=0 AND I.id=$id";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: ../".$redirect_url.$queryChar."error=optic_commentTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $rowCount = $result->num_rows;
                                    if ($rowCount < 1) {
                                        header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                                        exit();
                                    } elseif ($rowCount > 1) {
                                        header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                                        exit();
                                    } else {
                                        // correct amount found, continue.
                                        $row = $result->fetch_assoc();
                                        $site_id = $row['i_site_id'];

                                        $sql = "INSERT INTO optic_comment (item_id, user_id, comment, timestamp) VALUES (?, ?, ?, FROM_UNIXTIME($datetime))";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_commentConnectionInsert");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "sss", $id, $_SESSION['user_id'], $comment);
                                            mysqli_stmt_execute($stmt);
                                            $insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                                            $table_name = 'optic_comment';
                                            $type = "add";
                                            $reason = "Comment Added";
                                            $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                            $time = date('H:i:s'); // current time in HH:MM:SS format
                                            $username = $_SESSION['username'];

                                            updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                                        
                                            // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                            // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                            // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                            // // update changelog
                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Comment", $table_name, $insert_id, "comment", null, $comment);
                                        }
                                    }
                                }

                                $sql = "UPDATE optic_item SET deleted=1
                                        WHERE id=?";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: ../".$redirect_url.$queryChar."optic_item_id=$id&error=optic_itemTableSQLConnection-DeleteItem");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "s", $id);
                                    mysqli_stmt_execute($stmt);

                                    $table_name = 'optic_item';
                                    $type = "delete";
                                    $reason = "Item Deleted";
                                    $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                    $time = date('H:i:s'); // current time in HH:MM:SS format
                                    $username = $_SESSION['username'];

                                    updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                                
                                    // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                    // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                    // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                    // // update changelog
                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete Item", $table_name, $id, "deleted", 0, 1);

                                    header("Location: ../".$redirect_url.$queryChar."success=deleted");
                                    exit();
                                }
                            }
                        }
                    } else {
                        header("Location: ../".$redirect_url.$queryChar."error=invalidReason");
                        exit();
                    }
                } else {
                    header("Location: ../".$redirect_url.$queryChar."error=invalidId");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar."error=nonNumericId");
                exit();
            }
        }
    } elseif (isset($_POST['optic-move-submit'])) { 
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            if (is_numeric($id)) {
                if ($id > 0) {
                    if (isset($_POST['move-site']) && $_POST['move-site'] !== '' && is_numeric($_POST['move-site'])) {
                        $move_site = $_POST['move-site'];

                        $sql = "SELECT I.id AS i_id, I.site_id AS i_site_id
                                FROM optic_item AS I
                                WHERE I.deleted=0 AND I.id='$id'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            if ($rowCount < 1) {
                                header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                                exit();
                            } elseif ($rowCount > 1) {
                                header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                                exit();
                            } else {
                                // correct amount found, continue.
                                $row = $result->fetch_assoc();
                                $site_id = $row['i_site_id'];
                                if ($site_id == $move_site) {
                                    header("Location: ../".$redirect_url.$queryChar."error=siteUnchanged");
                                    exit(); 
                                }

                                $sql = "UPDATE optic_item 
                                        SET site_id='$move_site'
                                        WHERE id=?";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: ../".$redirect_url.$queryChar."optic_item_id=$id&error=optic_itemTableSQLConnection-DeleteItem");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "s", $id);
                                    mysqli_stmt_execute($stmt);

                                    $table_name = 'optic_item';
                                    $type = "move";
                                    $reason = "Item Moved";
                                    $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                    $time = date('H:i:s'); // current time in HH:MM:SS format
                                    $username = $_SESSION['username'];

                                    updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                                
                                    // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                    // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                    // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                    // // update changelog
                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete Item", $table_name, $id, "site_id", $site_id, $move_site);

                                    header("Location: ../".$redirect_url.$queryChar."success=moved");
                                    exit();
                                }
                            }
                        }
                    } else {
                        header("Location: ../".$redirect_url.$queryChar."error=noSite");
                        exit();
                    }
                } else {
                    header("Location: ../".$redirect_url.$queryChar."error=invalidId");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar."error=nonNumericId");
                exit();
            }
        }
    } elseif (isset($_POST['optic-restore-submit'])) { 
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            if (is_numeric($id)) {
                if ($id > 0) {
                    $sql = "SELECT I.id AS i_id, I.site_id AS i_site_id
                            FROM optic_item AS I
                            WHERE I.deleted=1 AND I.id='$id'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."error=optic_itemTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount < 1) {
                            header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                            exit();
                        } elseif ($rowCount > 1) {
                            header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                            exit();
                        } else {
                            // correct amount found, continue.
                            $row = $result->fetch_assoc();
                            $site_id = $row['i_site_id'];

                            $sql = "UPDATE optic_item SET deleted=0
                                    WHERE id=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../".$redirect_url.$queryChar."optic_item_id=$id&error=optic_itemTableSQLConnection-RestoreItem");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $id);
                                mysqli_stmt_execute($stmt);

                                $table_name = 'optic_item';
                                $type = "restore";
                                $reason = "Item Restored";
                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                $username = $_SESSION['username'];

                                updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                            
                                // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                // // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Restore Item", $table_name, $id, "restored", 1, 0);

                                header("Location: ../".$redirect_url.$queryChar."success=restored");
                                exit();
                            }
                        }
                    }
                } else {
                    header("Location: ../".$redirect_url.$queryChar."error=invalidId");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar."error=nonNumericId");
                exit();
            }
        }
    } elseif (isset($_POST['optic-type-add'])) {
        $queryString = '';
        if (isset($_POST['QUERY'])){
            $QUERY = $_POST['QUERY'];
            if (is_array($QUERY) && count($QUERY) > 0) {
                $i = 0;
                foreach (array_keys($QUERY) as $key) {
                    if ($key !== 'success' && $key !== 'error') {
                        if ($i == 0) {
                            $queryString .= $key.'='.$QUERY[$key];
                        } else {
                            $queryString .= '&'.$key.'='.$QUERY[$key];
                        }
                        $i++;
                    }
                }
            }
        }
        if ($queryString !== '') {
            $queryString .= '&';
        }
        if (isset($_POST['type_name'])) {
            $type_name = mysqli_real_escape_string($conn, $_POST['type_name']);
            $sql = "SELECT name
                    FROM optic_type
                    WHERE deleted=0 AND name='$type_name'";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../".$redirect_url.$queryChar."error=optic_typeTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount > 0) {
                    header("Location: ../".$redirect_url.$queryChar.$queryString."error=typeExists");
                    exit();
                } else {
                    // correct amount found, continue.
                    $sql = "INSERT INTO optic_type (name) VALUES (?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_commentConnectionInsert");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $type_name);
                        mysqli_stmt_execute($stmt);
                        $insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                        $site_id = 0;
                        $table_name = 'optic_type';
                        $type = "add";
                        $reason = "Type Added";
                        $date = date('Y-m-d'); // current date in YYY-MM-DD format
                        $time = date('H:i:s'); // current time in HH:MM:SS format
                        $username = $_SESSION['username'];

                        updateOpticTransactions($table_name, $insert_id, $type, $reason, $date, $time, $username, $site_id);
                    
                        // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                        // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                        // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                        // // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Type", $table_name, $insert_id, "name", null, $type_name);

                        header("Location: ../".$redirect_url.$queryChar.$queryString."success=TypeAdded");
                        exit();
                    }
                }
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar.$queryString."error=missingType_name");
            exit();
        }
    } elseif (isset($_POST['optic-connector-add'])) {
        $queryString = '';
        if (isset($_POST['QUERY'])){
            $QUERY = $_POST['QUERY'];
            if (is_array($QUERY) && count($QUERY) > 0) {
                $i = 0;
                foreach (array_keys($QUERY) as $key) {
                    if ($key !== 'success' && $key !== 'error') {
                        if ($i == 0) {
                            $queryString .= $key.'='.$QUERY[$key];
                        } else {
                            $queryString .= '&'.$key.'='.$QUERY[$key];
                        }
                        $i++;
                    }
                }
            }
        }
        if ($queryString !== '') {
            $queryString .= '&';
        }
        if (isset($_POST['connector_name'])) {
            $connector_name = mysqli_real_escape_string($conn, $_POST['connector_name']);
            $sql = "SELECT name
                    FROM optic_connector
                    WHERE name='$connector_name'";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../".$redirect_url.$queryChar."error=optic_connectorTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount > 0) {
                    header("Location: ../".$redirect_url.$queryChar.$queryString."error=connectorExists");
                    exit();
                } else {
                    // correct amount found, continue.
                    $sql = "INSERT INTO optic_connector (name) VALUES (?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_commentConnectionInsert");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $connector_name);
                        mysqli_stmt_execute($stmt);
                        $insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                        $site_id = 0;
                        $table_name = 'optic_connector';
                        $type = "add";
                        $reason = "Connector Added";
                        $date = date('Y-m-d'); // current date in YYY-MM-DD format
                        $time = date('H:i:s'); // current time in HH:MM:SS format
                        $username = $_SESSION['username'];

                        updateOpticTransactions($table_name, $insert_id, $type, $reason, $date, $time, $username, $site_id);
                    
                        // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                        // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                        // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                        // // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Connector", $table_name, $insert_id, "name", null, $connector_name);

                        header("Location: ../".$redirect_url.$queryChar.$queryString."success=ConnectorAdded");
                        exit();
                    }
                }
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar.$queryString."error=missingConnector_name");
            exit();
        }
    } elseif (isset($_POST['optic-vendor-add'])) {
        $queryString = '';
        if (isset($_POST['QUERY'])){
            $QUERY = $_POST['QUERY'];
            if (is_array($QUERY) && count($QUERY) > 0) {
                $i = 0;
                foreach (array_keys($QUERY) as $key) {
                    if ($key !== 'success' && $key !== 'error') {
                        if ($i == 0) {
                            $queryString .= $key.'='.$QUERY[$key];
                        } else {
                            $queryString .= '&'.$key.'='.$QUERY[$key];
                        }
                        $i++;
                    }
                }
            }
        }
        if ($queryString !== '') {
            $queryString .= '&';
        }
        if (isset($_POST['vendor_name'])) {
            $vendor_name = mysqli_real_escape_string($conn, $_POST['vendor_name']);
            $sql = "SELECT name
                    FROM optic_vendor
                    WHERE deleted=0 AND name='$vendor_name'";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../".$redirect_url.$queryChar."error=optic_vendorTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount > 0) {
                    header("Location: ../".$redirect_url.$queryChar.$queryString."error=vendorExists");
                    exit();
                } else {
                    // correct amount found, continue.
                    $sql = "INSERT INTO optic_vendor (name) VALUES (?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_commentConnectionInsert");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $vendor_name);
                        mysqli_stmt_execute($stmt);
                        $insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                        $site_id = 0;
                        $table_name = 'optic_vendor';
                        $type = "add";
                        $reason = "Vendor Added";
                        $date = date('Y-m-d'); // current date in YYY-MM-DD format
                        $time = date('H:i:s'); // current time in HH:MM:SS format
                        $username = $_SESSION['username'];

                        updateOpticTransactions($table_name, $insert_id, $type, $reason, $date, $time, $username, $site_id);
                    
                        // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                        // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                        // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                        // // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Vendor", $table_name, $insert_id, "name", null, $vendor_name);

                        header("Location: ../".$redirect_url.$queryChar.$queryString."success=VendorAdded");
                        exit();
                    }
                }
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar.$queryString."error=missingType_name");
            exit();
        }
    } elseif (isset($_POST['optic-comment-add'])) {
        if (isset($_POST['id'])) {
            if (isset($_POST['comment'])) {
                $id = $_POST['id'];
                $comment = mysqli_real_escape_string($conn, $_POST['comment']); // escape the special characters;
                $datetime = time();

                $sql = "SELECT I.site_id AS i_site_id
                        FROM optic_item AS I
                        WHERE I.deleted=0 AND I.id=$id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../".$redirect_url.$queryChar."error=optic_commentTableSQLConnection");
                    exit();
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                        exit();
                    } elseif ($rowCount > 1) {
                        header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                        exit();
                    } else {
                        // correct amount found, continue.
                        $row = $result->fetch_assoc();
                        $site_id = $row['i_site_id'];
                        $sql = "INSERT INTO optic_comment (item_id, comment, user_id, timestamp) VALUES (?, ?, FROM_UNIXTIME($datetime))";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$queryChar."sqlerror=optic_commentConnectionInsert");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "ss", $id, $comment, $_SESSION['user_id']);
                            mysqli_stmt_execute($stmt);
                            $insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                            $table_name = 'optic_comment';
                            $type = "add";
                            $reason = "Comment Added";
                            $date = date('Y-m-d'); // current date in YYY-MM-DD format
                            $time = date('H:i:s'); // current time in HH:MM:SS format
                            $username = $_SESSION['username'];

                            updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                        
                            // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                            // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                            // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                            // // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Comment", $table_name, $insert_id, "comment", null, $comment);

                            header("Location: ../".$redirect_url.$queryChar."success=commentAdded");
                            exit();
                        }
                    }
                }

            } else {
                header("Location: ../".$redirect_url.$queryChar."error=missingComment");
                exit();
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar."error=missingId");
            exit();
        }
    } elseif (isset($_POST['optic-comment-delete'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            if (is_numeric($id)) {
                if ($id > 0) {
                    $sql = "SELECT C.id AS c_id, C.item_id AS c_item_id, I.site_id AS i_site_id
                            FROM optic_comment AS C
                            INNER JOIN optic_item AS I ON C.item_id=I.id
                            WHERE I.deleted=0 AND C.deleted=0 AND C.id='$id'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."error=optic_commentTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount < 1) {
                            header("Location: ../".$redirect_url.$queryChar."error=noRowsFound");
                            exit();
                        } elseif ($rowCount > 1) {
                            header("Location: ../".$redirect_url.$queryChar."error=tooManyRowsFound");
                            exit();
                        } else {
                            // correct amount found, continue.
                            $row = $result->fetch_assoc();
                            $site_id = $row['i_site_id'];

                            $sql = "UPDATE optic_comment SET deleted=1
                                    WHERE id=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../".$redirect_url.$queryChar."optic_comment_id=$id&error=optic_commentTableSQLConnection-DeleteComment");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $id);
                                mysqli_stmt_execute($stmt);

                                $table_name = 'optic_comment';
                                $type = "delete";
                                $reason = "Comment Deleted";
                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                $username = $_SESSION['username'];

                                updateOpticTransactions($table_name, $id, $type, $reason, $date, $time, $username, $site_id);
                            
                                // $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                                // $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                // send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                // // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete Comment", $table_name, $id, "deleted", 0, 1);

                                header("Location: ../".$redirect_url.$queryChar."success=deleted");
                            }
                        }
                    }
                } else {
                    header("Location: ../".$redirect_url.$queryChar."error=invalidId");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar."error=nonNumericId");
                exit();
            }
        }

    } else { // no page set.
        header("Location: ../".$redirect_url.$queryChar."error=noActionPost");
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['request-optic']) && $_GET['request-optic'] == 1) {
        if (isset($_GET['serial'])) {
            $serial = mysqli_real_escape_string($conn, $_GET['serial']);
            $sql = "SELECT *
                    FROM optic_item 
                    WHERE serial_number='$serial'";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                $results['error'] = "MYSQL connection issue.";
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount == 1) {
                    $row = $result->fetch_assoc();
                    $results = $row;
                    $results['count'] = $rowCount;
                    if ((int)$row['deleted'] == 0) {
                        $results['error'] = "Optic already exists.";
                    } else {
                        $results['success'] = "Found a matching deleted optic. Info auto-filled. Adding will restore/overwrite.";
                    }
                } elseif ($rowCount > 1) {
                    $results['error'] = "Multiple entries match this serial number.";
                } elseif ($rowCount < 1) {
                    $results['skip'] = 1;
                }
            }

            echo(json_encode($results));
        }
    } else { // no page set.
        header("Location: ../".$redirect_url.$queryChar."error=noActionGet");
        exit();
    }
} else { // not POST
    header("Location: ../".$redirect_url.$queryChar."error=InvalidRequest");
    exit();
}

?>