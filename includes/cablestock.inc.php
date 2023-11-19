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

$redirect_url = $_SESSION['redirect_url'];
$queryChar = strpos($redirect_url, "?") !== false ? '&' : '?';

include 'changelog.inc.php';

function image_upload($field, $stock_id, $redirect_url, $redirect_queries) {
    $timedate = date("dmyHis");

    $uploadDirectory = "../assets/img/stock/";
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
            $moveName = "stock-$stock_id-img-$timedate.$fileExtension";
            $uploadPath = $uploadDirectory.$moveName;
            $uploadFileName = $moveName;
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
            if ($didUpload) {
                include 'dbh.inc.php';
                $sql = "INSERT INTO stock_img (stock_id, image) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ".$redirect_url.$redirect_queries."&error=imageSQL");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $stock_id, $uploadFileName);
                    mysqli_stmt_execute($stmt);
                    $new_stock_img_id = mysqli_insert_id($conn);
                    // update changelog
                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_img", $new_stock_img_id, "image", null, $uploadFileName);
                }

            } else {
                $errors[] = "uploadFailed";
                print_r($errors);
                // header("Location: ".$redirect_url.$redirect_queries."&error=imageUpload");
                exit();
                // return $errors;
            }
        } else {
            print_r($errors);
            // header("Location: ".$redirect_url.$redirect_queries."&error=imageUpload");
            exit();
            // return $errors;
        } 
    }
}

function updateCableTransactions($stock_id, $item_id, $type, $quantity, $reason, $date, $time, $username, $shelf_id) {
    global $redirect_url, $queryChar;
    include 'dbh.inc.php';
    $cost = 0;
    $sql_trans = "INSERT INTO cable_transaction (stock_id, item_id, type, quantity, reason, date, time, username, shelf_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_trans = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
        header("Location: ../".$redirect_url.$queryChar."error=cable_transactionConnectionSQL");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt_trans, "sssssssss", $stock_id, $item_id, $type, $quantity, $reason, $date, $time, $username, $shelf_id);
        mysqli_stmt_execute($stmt_trans);
        echo ("transaction added");
    }  
} 

function getCableItemRow($cable_item_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT * FROM cable_item WHERE id=$cable_item_id";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_itemTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=noRowsFound");
            exit();
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=tooManyRowsFound");
            exit();
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
}

function getCableStockInfo($stock_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT * FROM stock WHERE id=$stock_id";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."stockID=$stock_id&error=stockTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            header("Location: ../".$redirect_url.$queryChar."stockID=$stock_id&error=noRowsFound");
            exit();
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."stockID=$stock_id&error=tooManyRowsFound");
            exit();
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
}

function getItemLocation($shelf_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT site.id AS site_id, site.name AS site_name, 
                    area.id AS area_id, area.name AS area_name,
                    shelf.id AS shelf_id, shelf.name AS shelf_name
            FROM site 
            INNER JOIN area ON area.site_id=site.id
            INNER JOIN shelf ON shelf.area_id=area.id
            WHERE shelf.id=$shelf_id";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."shelfID=$shelf_id&error=siteTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            header("Location: ../".$redirect_url.$queryChar."shelfID=$shelf_id&error=noRowsFound");
            exit();
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."shelfID=$shelf_id&error=tooManyRowsFound");
            exit();
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
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

function addQuantity($stock_id, $cable_item_id) {
    global $redirect_url, $queryChar, $_SESSION, $current_smtp_enabled, $config_smtp_from_name, $current_system_name, $loggedin_fullname, $loggedin_email, $current_base_url;
    
    $shelf_id = getCableItemRow($cable_item_id)['shelf_id'];
    $type = "add";
    $reason = "Added via Fixed Cable page";
    $date = date('Y-m-d'); // current date in YYY-MM-DD format
    $time = date('H:i:s'); // current time in HH:MM:SS format
    $username = $_SESSION['username'];

    $item = getCableItemRow($cable_item_id);
    $quantity = $item['quantity'];
    $new_quantity = $quantity +1;

    if ($cable_item_id == $item['id']) {
        include 'dbh.inc.php';

        $sql = "UPDATE cable_item SET quantity=?
                WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_itemTableSQLConnection-AddQuantity");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $new_quantity, $cable_item_id);
            mysqli_stmt_execute($stmt);
            
            updateCableTransactions($stock_id, $cable_item_id, $type, $new_quantity, $reason, $date, $time, $username, $shelf_id);

            $stock_info = getCableStockInfo($item['stock_id']);
            $item_location = getItemLocation($item['shelf_id']);
            $base_url = getCurrentURL();
            
            $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Added";
            $email_body = "<p>Fixed cable stock added, for <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 8);
            // update changelog
            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add Quantity", "cable_item", $cable_item_id, "quantity", $quantity, $new_quantity);

            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&success=quantityAdded");
            exit();
        }  
    } else {
        header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_item_id-missmatch");
        exit();
    }
}

function removeQuantity($stock_id, $cable_item_id) {
    global $redirect_url, $queryChar, $_SESSION, $current_smtp_enabled, $current_smtp_to_email, $config_smtp_from_name, $current_system_name, $loggedin_fullname, $loggedin_email, $current_base_url, $current_base_url;
    
    $shelf_id = getCableItemRow($cable_item_id)['shelf_id'];
    $type = "remove";
    $reason = "Removed via Fixed Cable page";
    $date = date('Y-m-d'); // current date in YYY-MM-DD format
    $time = date('H:i:s'); // current time in HH:MM:SS format
    $username = $_SESSION['username'];

    $item = getCableItemRow($cable_item_id);
    $quantity = $item['quantity'];
    if ($quantity > 0) {
        $new_quantity = $quantity -1;

        if ($cable_item_id == $item['id']) { 
            include 'dbh.inc.php';

            $sql = "UPDATE cable_item SET quantity=?
                    WHERE id=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_itemTableSQLConnection-AddQuantity");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $new_quantity, $cable_item_id);
                mysqli_stmt_execute($stmt);
                
                updateCableTransactions($stock_id, $cable_item_id, $type, $new_quantity, $reason, $date, $time, $username, $shelf_id);

                $stock_info = getCableStockInfo($item['stock_id']);
                $item_location = getItemLocation($item['shelf_id']);
                $base_url = getCurrentURL();
            
                $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Removed";
                $email_body = "<p>Fixed cable stock removed, from <strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                // update changelog
                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Remove Quantity", "cable_item", $cable_item_id, "quantity", $quantity, $new_quantity);

                // Check if the quantity is below minimum
                if ($new_quantity < $stock_info['min_stock']) {
                    $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Below Minimum Stock Count at ".$item_location['site_name'].". Please Order More!";
                    $email_body = "<p>Fixed cable stock below minimum stock count, for <strong><a href='\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p><p style='color:red'>Please raise a PO to order more!</p>";
            
                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 10);
                    send_email($current_smtp_to_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 10);
                }

                header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&success=quantityRemoved");
                exit();
            }  
        } else {
            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_item_id-missmatch");
            exit();
        }
    } else {
        header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=quantityZero");
        exit();
    }
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add-cables-submit'])) {
        if (isset($_POST['shelf']) && isset($_POST['stock-name']) && isset($_POST['stock-description']) && isset($_POST['cable-type']) && isset($_POST['stock-min-stock']) && isset($_POST['item-quantity']) && isset($_POST['item-cost'])) {
            $site_id = $_POST['site'];
            $area_id = $_POST['area'];
            $shelf_id = $_POST['shelf'];
            $stock_name = $_POST['stock-name'];
            $stock_description = $_POST['stock-description'];
            $cable_type = $_POST['cable-type'];
            $stock_min_stock = $_POST['stock-min-stock'];
            $item_quantity = $_POST['item-quantity'];
            $item_cost = $_POST['item-cost'];
            $sku_prefix = "CABLE-";

            include 'dbh.inc.php';
            
            // check for duplicate names
            $stock_name = mysqli_real_escape_string($conn, $stock_name);

            $sql_stock = "SELECT * FROM stock WHERE name='$stock_name' LIMIT 1";
            $stmt_stock = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                header("Location: ../".$redirect_url.$queryChar."sqlerror=stockConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt_stock);
                $result_stock = mysqli_stmt_get_result($stmt_stock);
                $rowCount_stock = $result_stock->num_rows;
                if ($rowCount_stock > 0) {
                    // duplicate name found
                    $row_stock = $result_stock->fetch_assoc();
                    $stock_id = $row_stock['id'];

                    // insert row into cable_item
                    $sql_cable_item = "INSERT INTO cable_item (stock_id, quantity, cost, shelf_id, type_id) VALUES (?, ?, ?, ?, ?)";
                    $stmt_cable_item = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_cable_item, $sql_cable_item)) {
                        header("Location: ../".$redirect_url.$queryChar."sqlerror=cable_itemConnectionInsert");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_cable_item, "sssss", $stock_id, $item_quantity, $item_cost, $shelf_id, $cable_type);
                        mysqli_stmt_execute($stmt_cable_item);

                        $cable_item_id= mysqli_insert_id($conn);

                        $type = "add";
                        $reason = "New Stock and Cable Item added";
                        $date = date('Y-m-d'); // current date in YYY-MM-DD format
                        $time = date('H:i:s'); // current time in HH:MM:SS format
                        $username = $_SESSION['username'];
                        updateCableTransactions($stock_id, $cable_item_id, $type, $item_quantity, $reason, $date, $time, $username, $shelf_id);
                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "cable_item", $cable_item_id, "stock_id", null, $stock_id);

                        header("Location: ../".$redirect_url.$queryChar."success=cableAdded&shelf_id=$shelf_id&stock_id=$stock_id&item_id=$cable_item_id&transaction=added");
                        exit();
                    }
                } else {
                    // name is okay, continue

                    // get the next sku with CABLE- prefix
                    $sql_sku = "SELECT sku FROM stock WHERE sku LIKE 'CABLE-%' ORDER BY sku DESC LIMIT 1";
                    $stmt_sku = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_sku, $sql_sku)) {
                        header("Location: ../".$redirect_url.$queryChar."sqlerror=stockConnectionSku");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_sku);
                        $result_sku = mysqli_stmt_get_result($stmt_sku);
                        $rowCount_sku = $result_sku->num_rows;
                        if ($rowCount_sku < 1) {
                            $sku_number = '0000';
                        } else {
                            $row_sku = $result_sku->fetch_assoc();
                            $sku_max = $row_sku['sku'];
                            $sku_number = substr($sku_max, strlen($sku_prefix));
                        }

                        $sku_number_numeric = intval($sku_number);
                        $new_sku_number_numeric = $sku_number_numeric + 1;
                        $new_formatted_sku_number = sprintf('%04d', $new_sku_number_numeric);
                        $new_sku = $sku_prefix.$new_formatted_sku_number;



                        // insert into stock table
                        $stock_name = mysqli_real_escape_string($conn, $stock_name); // escape the special characters
                        $stock_description = mysqli_real_escape_string($conn, $stock_description); // escape the special characters
                        $sql_add = "INSERT INTO stock (name, description, sku, min_stock, is_cable) VALUES (?, ?, ?, ?, 1)";
                        $stmt_add = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_add, $sql_add)) {
                            header("Location: ../".$redirect_url.$queryChar."sqlerror=stockConnectionInsert");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_add, "ssss", $stock_name, $stock_description, $new_sku, $stock_min_stock);
                            mysqli_stmt_execute($stmt_add);

                            $stock_id = mysqli_insert_id($conn);
                            // update changelog
                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock", $stock_id, "name", null, $stock_name);
                            // announce new stock added
                            $stock_info = getCableStockInfo($stock_id);
                            $item_location = getItemLocation($shelf);
                            $base_url = getCurrentURL();
                        
                            $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Created";
                            $email_body = "<p>Fixed cable stock created:<strong><a href=\"https://$current_base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong>.</p>";
                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 8);


                            if (isset($_FILES['stock-img']) && $_FILES['stock-img']['name'] !== '') {
                                image_upload('stock-img', $stock_id, $redirect_url, '');
                            }


                            // insert row into cable_item
                            $sql_cable_item = "INSERT INTO cable_item (stock_id, quantity, cost, shelf_id, type_id) VALUES (?, ?, ?, ?, ?)";
                            $stmt_cable_item = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_cable_item, $sql_cable_item)) {
                                header("Location: ../".$redirect_url.$queryChar."sqlerror=cable_itemConnectionInsert");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_cable_item, "sssss", $stock_id, $item_quantity, $item_cost, $shelf_id, $cable_type);
                                mysqli_stmt_execute($stmt_cable_item);
    
                                $cable_item_id= mysqli_insert_id($conn);

                                $type = "add";
                                $reason = "New Stock and Cable Item added";
                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                $username = $_SESSION['username'];
                                updateCableTransactions($stock_id, $cable_item_id, $type, $item_quantity, $reason, $date, $time, $username, $shelf_id);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "cable_item", $cable_item_id, "quantity", null, $item_quantity);

                                header("Location: ../".$redirect_url.$queryChar."success=cableAdded&shelf_id=$shelf_id&stock_id=$stock_id&item_id=$cable_item_id&transaction=added");
                                exit();
                            }
                        }
                    }
                }
            }

            

        } else {
            header("Location: ../".$redirect_url.$queryChar."error=missingFields");
            exit();
        }
    } elseif (isset($_POST['action'])) {
        $action = $_POST['action'];

        if (isset($_POST['cable-item-id'])) {
            if ($_POST['cable-item-id'] !== '' && $_POST['cable-item-id'] !== 0) {
                $cable_item_id = $_POST['cable-item-id'];
                $stock_id = $_POST['stock-id'];
                include 'smtp.inc.php';
                if ($action == "add") {
                    addQuantity ($stock_id, $cable_item_id);
                } elseif ($action == "remove") {
                    removeQuantity ($stock_id, $cable_item_id);
                } elseif ($action == "new") {
                    // to be created

                    header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=newToBeCreated");
                    exit();
                } elseif ($action == "delete") {
                    // to be created

                    header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=deleteToBeCreated");
                    exit();
                } else {
                    header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=unknownAction");
                    exit();
                }
            } else { // cable-item-id IS blank or 0
                header("Location: ../".$redirect_url.$queryChar."error=cable-item-id_error");
                exit();
            }
        } else { // cable-item-id not set
            header("Location: ../".$redirect_url.$queryChar."error=noCable-item-id");
            exit();
        }
    } elseif (isset($_POST['new-type'])) {
        if (isset($_POST['type-parent'])) {
            if (isset($_POST['type-name'])) {
                if (isset($_POST['type-description'])) {
                    $name = $_POST['type-name'];
                    $parent = $_POST['type-parent'];
                    $description = $_POST['type-description'];

                    $name = mysqli_real_escape_string($conn, $name); // escape special characters
                    $description = mysqli_real_escape_string($conn, $description); // escape special characters
                    
                    // check if it already exists
                    include 'dbh.inc.php';

                    $sql = "SELECT * FROM cable_types
                            WHERE name=? AND parent=?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$queryChar."&error=cable_typesTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "ss", $name, $parent);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount < 1) {
                            // no match found, continue to add.
                            $name = mysqli_real_escape_string($conn, $name); // escape the special characters
                            $description = mysqli_real_escape_string($conn, $description); // escape the special characters
                            $sql_insert = "INSERT INTO cable_types (name, , parent) VALUES (?, ?, ?)";
                            $stmt_insert = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
                                header("Location: ".$redirect_url.$redirect_queries."&error=cable_typesTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_insert, "sss", $name, $description, $parent);
                                mysqli_stmt_execute($stmt_insert);
                                $insert_id = mysqli_insert_id($conn);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "cable_types", $insert_id, "name", null, $name);
                                header("Location: ../".$redirect_url.$queryChar."&success=typeAdded&type-id=$insert_id");
                                exit();
                            }
                        } elseif ($rowCount > 1) {
                            header("Location: ../".$redirect_url.$queryChar."&error=typeAlreadyExists");
                            exit();
                        }
                    }
                } else { // no description selected
                    header("Location: ../".$redirect_url.$queryChar."error=missingDescription");
                    exit();
                }
            } else { // no name selected
                header("Location: ../".$redirect_url.$queryChar."error=missingName");
                exit();
            }
        } else { // no parent selected
            header("Location: ../".$redirect_url.$queryChar."error=missingParent");
            exit();
        }

    } else { // no page set.
        header("Location: ../".$redirect_url.$queryChar."error=noAction");
        exit();
    }
} else { // not POST
    header("Location: ../".$redirect_url.$queryChar."error=notPOST");
    exit();
}





?>