<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// ALL OF THE STOCK MODIFICATION ACTIONS
// THESE DO THE ADDING, REMOVAL, MOVING AND EDITING OF STOCK/ITEMS

// THIS USED TO BE THE COLLECTION OF stock-add-new.inc.php, stock-edit-action.inc.php, stock-remove-existing.inc.php, stock-move-existing.inc.php

// Tested and working:
//    - stock-add
//    - stock-remove
//    - stock-edit
//    - stock-delete
//    - stock-move

// This should all be working now, but will be leaving the old files in the /includes/old folder

// 28-08-23 - added changelog for all sql queries here that need it.

if(session_status() !== PHP_SESSION_ACTIVE) { // start the session
    session_start();
}  

// include '../session.php';
include 'changelog.inc.php';

// check the redirect url for the file name and for ?
if (str_contains($_SESSION['redirect_url'], basename(__FILE__))) {
    $redirect_url = 'index.php';
    $query_char = '?';
} else {
    $redirect_url = $_SESSION['redirect_url'];
    if (str_contains($_SESSION['redirect_url'], '?')) {
        $query_char = '&';
    } else {
        $query_char = '?';
    }
}

// FUNCTIONS

// image upload from the stock-add-new.inc.php - the image_upload script from stock-edit.inc.php page is not needed.
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
                    header("Location: ../".$redirect_url.$redirect_queries."&error=imageSQL");
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
                // header("Location: ../".$redirect_url.$redirect_queries."&error=imageUpload");
                exit();
                // return $errors;
            }
        } else {
            print_r($errors);
            // header("Location: ../".$redirect_url.$redirect_queries."&error=imageUpload");
            exit();
            // return $errors;
        } 
    }
}
function getItemRow($item_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT * FROM item WHERE id=$item_id";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../".$redirect_url.$queryChar."itemID=$item_id&error=itemTableSQLConnection");
        exit();
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return '';
        } elseif ($rowCount > 1) {
            return '';
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
}
function getItemStockInfo($stock_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT * FROM stock WHERE id=$stock_id";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return '';
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return '';
        } elseif ($rowCount > 1) {
            return '';
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
}
function getItemQuantity($stock_id, $shelf_id) {
    global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT sum(quantity) AS total_quantity 
            FROM item
            INNER JOIN stock ON item.stock_id=stock.id
            WHERE stock.id=$stock_id AND item.shelf_id=$shelf_id 
                AND stock.deleted=0 AND item.deleted=0";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return '';
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return '';
        } elseif ($rowCount > 1) {
            return '';
        } else {
            $row = $result->fetch_assoc();
            return $row['total_quantity'];
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
        return '';
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return '';
        } elseif ($rowCount > 1) {
            header("Location: ../".$redirect_url.$queryChar."shelfID=$shelf_id&error=tooManyRowsFound");
            return '';
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

// MAIN SCRIPTS
// print_r($_POST);
// exit();
if (isset($_POST['submit'])) { // standard submit button name - this should be the case on all forms.
    include 'smtp.inc.php';

    if (isset($_SESSION['username']) && $_SESSION['username'] != '' && $_SESSION['username'] != null) {
        if (isset($_POST['stock-add'])) { // bits from the stock-add-new.inc.php page - need to add a hidden input with name="stock-add" for this
            if ($_POST['submit'] == 'Add Stock') {

                // print_r('<pre>');
                // print_r($_POST);
                // print_r($_SESSION);
                // print_r('</pre>');

                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                $time = date('H:i:s'); // current time in HH:MM:SS format

                // item
                $upc = $_POST['upc']; // upc
                $manufacturer = $_POST['manufacturer']; // manufacturer_id
                $site = $_POST['site']; // shouldnt be needed
                $area = $_POST['area']; // shouldnt be needed
                $shelf = $_POST['shelf']; // site_id
                $cost = $_POST['cost']; // cost
                $comments = isset($_POST['comments']) ? $POST['comments'] : '' ; // comments
                
                // transaction
                $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : ''; 
                $serial_number = isset($_POST['serial-number']) ? $_POST['serial-number'] : ''; 
                $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

                $username = $_SESSION['username'];

                $redirect_queries = $query_char."manufacturer=$manufacturer&site=$site&area=$area&shelf=$shelf&quantity=$quantity&serial-number=$serial_number&reason=$reason";
                
                if (!isset($_POST['shelf']) || $_POST['shelf'] == '' || $_POST['shelf'] == 0 || $_POST['shelf'] == '0') {
                    header("Location: ../$redirect_url$redirect_queries&error=shelfRequired");
                    exit();
                }



                include 'dbh.inc.php';
                if (!isset($_POST['id']) || $_POST['id'] == 0 || $_POST['id'] == '0') {
                    // adding new stock
                    $name = $_POST['name'];
                    $sku = $_POST['sku'];
                    $description = $_POST['description'];
                    $min_stock = $_POST['min-stock'] == '' ? 0 : $_POST['min-stock'];
                    $labels = isset($_POST['labels']) ? $_POST['labels'] : '';
                    $image = $_FILES['image'];
                    if (is_array($labels)) {
                        $labelsQ = implode(',', $labels);
                    } else {
                        $labelsQ = $labels;
                    }
                    $redirect_queries .= "&name=$name&sku=$sku&description=$description&min_stock=$min_stock&labels=$labelsQ";

                    // check if SKU is set
                    // if it is set, check it doesnt already exist, divert back with error if it does.
                    // if it doesnt, check highest default stock sku and add 1 and use it

                    // GET SKU LIST
                    $skus = [];
                    
                    $sql_sku = "SELECT DISTINCT sku FROM stock
                                ORDER BY sku";
                    $stmt_sku = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_sku, $sql_sku)) {
                        header("Location: ../$redirect_url$redirect_queries&error=stockTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_sku);
                        $result_sku = mysqli_stmt_get_result($stmt_sku);
                        $rowCount_sku = $result_sku->num_rows;
                        if ($rowCount_sku < 1) {
                            // header("Location: ../".$redirect_url.$redirect_queries."&error=noSkusInTable");
                            // exit();
                        } else {
                            while ($row_sku = $result_sku->fetch_assoc() ){
                                array_push($skus, $row_sku['sku']);
                            }
                        }
                    }

                    $sql_d_config = "SELECT sku_prefix FROM config_default WHERE id=1";
                    $stmt_d_config = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_d_config, $sql_d_config)) {
                        header("Location: ../$redirect_url$redirect_queries&error=stockTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_d_config);
                        $result_d_config = mysqli_stmt_get_result($stmt_d_config);
                        $rowCount_d_config = $result_d_config->num_rows;
                        if ($rowCount_d_config < 1) {
                            // header("Location: ../".$redirect_url.$redirect_queries."&error=noSkusInTable");
                            // exit();
                        } else {
                            while ($row_d_config = $result_d_config->fetch_assoc() ){
                                $config_d_sku_prefix = isset($row_d_config['sku_prefix']) ? $row_d_config['sku_prefix'] : 'ITEM-';
                            }
                        }
                    }

                    // if default sku is not set, set it to ITEM- 
                    $config_d_sku_prefix = isset($config_d_sku_prefix) ? $config_d_sku_prefix : 'ITEM-';

                    $sql_config = "SELECT sku_prefix FROM config WHERE id=1";
                    $stmt_config = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
                        header("Location: ../$redirect_url$redirect_queries&error=stockTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_execute($stmt_config);
                        $result_config = mysqli_stmt_get_result($stmt_config);
                        $rowCount_config = $result_config->num_rows;
                        if ($rowCount_config < 1) {
                            // header("Location: ../".$redirect_url.$redirect_queries."&error=noSkusInTable");
                            // exit();
                        } else {
                            while ($row_config = $result_config->fetch_assoc() ){
                                $config_sku_prefix = isset($row_config['sku_prefix']) ? $row_config['sku_prefix'] : $config_d_sku_prefix;
                            }
                        }
                    }
                    
                    $current_sku_prefix = isset($config_sku_prefix) ? $config_sku_prefix : 'ITEM-';

                    $regex = '/^'.$current_sku_prefix.'\d{5}$/';
                    $PRE_skus = preg_grep($regex, $skus);
                    if (isset($sku) && $sku !== '') {
                        if (str_contains($sku, $current_sku_prefix)) {
                            // prefix is in the predefined sku. Error due to this creating potential errors.
                            header("Location: ../$redirect_url$redirect_queries&error=SKUcontainsSKU-prefix");
                            exit();
                        }
                        // SKU is not blank
                        if (in_array($sku, $skus)) {
                            // SKU already exists
                            header("Location: ../$redirect_url$redirect_queries&error=SKUexists");
                            exit();
                        }
                    } else {
                        if (empty($PRES_skus) || count($PRE_skus) == 0) {
                            $new_PRE_sku_number = 1;
                        } else {
                            usort($PRE_skus, function($a, $b) { // sort the array 
                                return strnatcmp($a, $b);
                            });
                            preg_match_all('/\d+/', end($PRE_skus), $max_sku_number_temp);
                            if (!empty($max_sku_number_temp)) {
                                $max_sku_number = $max_sku_number_temp[0][0];
                                $new_PRE_sku_number = $max_sku_number +1; 
                            } else {
                                $new_PRE_sku_number = 1;
                            }
                        }

                        $new_PRE_skus = $current_sku_prefix . str_pad($new_PRE_sku_number, 5, '0', STR_PAD_LEFT);
                        $sku = $new_PRE_skus;
                    }
                    
                    // echo("To be added:<br>name = $name<br>description = $description<br>sku = $sku<br>min_stock = $min_stock");

                    // ADD STOCK to stock table
                    $sql = "INSERT INTO stock (name, description, sku, min_stock) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$redirect_queries."&error=stockTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "ssss", $name, $description, $sku, $min_stock);
                        mysqli_stmt_execute($stmt);
                        $insert_id = mysqli_insert_id($conn); // ID of the new row in the table
                        $id = $insert_id;

                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock", $id, "name", null, $name);

                        // image upload
                        if (isset($_FILES['image'])) {
                            if ($_FILES['image']['name'] !== '') {
                                image_upload("image", $id, $redirect_url, $redirect_queries);
                            }
                        }

                        // label linking
                        if (is_array($labels)) {
                            foreach($labels as $label) {
                                $sql = "INSERT INTO stock_label (stock_id, label_id) VALUES (?, ?)";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: ../".$redirect_url.$redirect_queries."&error=stockTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "ss", $id, $label);
                                    mysqli_stmt_execute($stmt);
                                    $label_insert_id = mysqli_insert_id($conn); // ID of the new row in the table

                                    // update changelog
                                    addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_label", $label_insert_id, "stock_id", null, $id);
                                }
                            }
                        } elseif ($labels !== '') {
                            $sql = "INSERT INTO stock_label (stock_id, label_id) VALUES (?, ?)";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../".$redirect_url.$redirect_queries."&error=stockTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "ss", $id, $labels);
                                mysqli_stmt_execute($stmt);
                                $label_insert_id = mysqli_insert_id($conn); // ID of the new row in the table
                                    
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_label", $label_insert_id, "stock_id", null, $id);
                            }
                        }
                    }

                    $id = $insert_id;
                } else {
                    $id = $_POST['id'];
                }

// #588 feedback changes
                // get the individual serial numbers from the input field
                $serial_number_array = [];
                if ($serial_number !== '') {
                    $serial_number_array = array_map('trim', explode(',', $serial_number));
                }
                // add new row 
                for ($i = 1; $i <= (int)$quantity; $i++) {
                    // array number for serial number
                    $j = $i-1;
                    $serial_number_input = key_exists($j, $serial_number_array) ? $serial_number_array[$j] : ''; // get the serial that matches

                    $quantity_one = 1;
                    $sql = "INSERT INTO item (stock_id, upc, quantity, cost, serial_number, comments, manufacturer_id, shelf_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url.$redirect_queries."&error=itemTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "ssssssss", $id, $upc, $quantity_one, $cost, $serial_number_input, $comments, $manufacturer, $shelf);
                        mysqli_stmt_execute($stmt);
                        $item_id = mysqli_insert_id($conn); // ID of the new row in the table.

                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "item", $item_id, "stock_id", null, $id);

                        // Transaction update
                        $type = 'add';
                        $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, quantity, price, serial_number, reason,  date, time, username, shelf_id) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_trans = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                            header("Location: ../".$redirect_url.$redirect_queries."&error=transactionConnectionSQL");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $id, $item_id, $type, $quantity_one, $cost, $serial_number_input, $reason, $date, $time, $username, $shelf);
                            mysqli_stmt_execute($stmt_trans);
                            if ($i == (int)$quantity) {
                                $stock_info = getItemStockInfo($id);
                                $item_location = getItemLocation($shelf);
                                $new_quantity = getItemQuantity($id, $shelf);
                                $base_url = getCurrentURL();
            
                                $email_subject = ucwords($current_system_name)." - Stock inventory added";
                                $email_body = "<p>Stock inventory added, for <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 1);
                                header("Location: ../stock.php?stock_id=$id&item_id=$item_id&success=stockAdded");
                                exit();
                            }
                        }
                    }
                }
            } else {
                header("Location: ".$redirect_url.$redirect_queries."&error=addStock");
                exit();
            }
        } elseif (isset($_POST['stock-remove'])) { // stock removal bits from the stock-remove-existing.inc.php - need to add a hidden input with name="stock-remove"
            $redirect_url = "../stock.php?modify=remove&stock_id=".$_POST['stock_id'];
            $errors = [];

            if ($_POST['submit'] == 'Remove Stock') {
                print_r('<pre>');
                print_r($_POST);
                print_r('</pre>');
                
                $stock_id                 = isset($_POST['stock_id'])         ? $_POST['stock_id']         : '' ;
                $stock_sku                = isset($_POST['stock_sku'])        ? $_POST['stock_sku']        : '' ;
                $stock_manufacturer       = isset($_POST['manufacturer'])     ? $_POST['manufacturer']     : '' ;
                $stock_shelf              = isset($_POST['shelf'])            ? $_POST['shelf']            : '' ;
                $stock_price              = isset($_POST['price'])            ? $_POST['price']            : '' ;
                $stock_transaction_date   = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : '' ;
                $stock_quantity           = isset($_POST['quantity'])         ? $_POST['quantity']         : 1 ;
                $stock_serial_number      = isset($_POST['serial-number'])    ? $_POST['serial-number']    : '' ;
                $stock_transaction_reason = isset($_POST['reason'])           ? $_POST['reason']           : '' ;

                // function to check the current row and if 0 quantity, remove it.
                
        
                if ($stock_id !== '' && $stock_sku !== '' && $stock_manufacturer !== '' && $stock_shelf !== '' && $stock_price !== '' && $stock_transaction_date !== '' && $stock_quantity !== '' && $stock_transaction_reason !== '') {
                    // all info is as expected - serial_number is not needed to be checked.
        
                    include 'dbh.inc.php';

                    // check if the stock item exists and is not deleted.
                    $sql_checkID = "SELECT * FROM stock
                                    WHERE id=?
                                    AND deleted=0
                                    ORDER BY id";
                    $stmt_checkID = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_checkID, $sql_checkID)) {
                        $errors[] = 'checkID stock table error - SQL connection';
                        header("Location: $redirect_url&error=stockTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_checkID, "s", $stock_id);
                        mysqli_stmt_execute($stmt_checkID);
                        $result_checkID = mysqli_stmt_get_result($stmt_checkID);
                        $rowCount_checkID = $result_checkID->num_rows;
                        if ($rowCount_checkID < 1) {
                            $errors[] = 'checkID stock table error - no IDs found';
                            header("Location: $redirect_url&error=noIDInTable");
                            exit();
                        } elseif ($rowCount_checkID == 1) { 

                            // GET TOTAL STOCK COUNT

                            $sql_itemQuantity = "SELECT * FROM item WHERE stock_id=? AND serial_number=? AND deleted=0 AND shelf_id=?";
                            $stmt_itemQuantity = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_itemQuantity, $sql_itemQuantity)) {
                                $errors[] = 'itemQuantity stock table error - SQL connection';
                                header("Location: $redirect_url&error=stockTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_itemQuantity, "sss", $stock_id, $stock_serial_number, $stock_shelf);
                                mysqli_stmt_execute($stmt_itemQuantity);
                                $result_itemQuantity = mysqli_stmt_get_result($stmt_itemQuantity);
                                $rowCount_itemQuantity = $result_itemQuantity->num_rows;
                                if ($rowCount_itemQuantity < 1) {
                                    $errors[] = 'itemQuantity item table error - no quantity found (should not be able to get here)';
                                    header("Location: $redirect_url&error=noQuantityInTable&serial=$stock_serial_number");
                                    exit();
                                } else {
                                    $totalQuantity = $rowCount_itemQuantity;

                                    if ($stock_quantity <= $totalQuantity) {
                                        $itemIdArray = [];

                                        while ($row_item = $result_itemQuantity->fetch_assoc()) {
                                            $removeId = $row_item['id'];
                                            $itemIdArray[] = $removeId;
                                        }

                                        for ($i=0; $i<=($stock_quantity-1); $i++) {
                                            $delete_id = $itemIdArray[$i];
                                            
                                            $sql_remove_row = "UPDATE item SET quantity=0, deleted=1 WHERE id=?";
                                            $stmt_remove_row = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_remove_row, $sql_remove_row)) {
                                                $errors[] = 'remove_row item table error - SQL connection';
                                                header("Location: $redirect_url&error=itemTableSQLConnection");
                                                exit();
                                            } else {
                                                mysqli_stmt_bind_param($stmt_remove_row, "s", $delete_id);
                                                mysqli_stmt_execute($stmt_remove_row);

                                                // update changelog
                                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Remove quantity", "item", $delete_id, "quantity", 1, 0);

                                                //ADD TRANSACTION'
                                                $type = 'remove';
                                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                                $username = $_SESSION['username'];
                                                $neg_stock_quantity = -1;
                                                $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, quantity, price, serial_number, reason,  date, time, username, shelf_id) 
                                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                                $stmt_trans = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                                                    $errors[] = 'trans transaction table error - SQL connection';
                                                    header("Location: $redirect_url&error=TransactionConnectionIssue");
                                                    exit();
                                                } else {
                                                    mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $stock_id, $delete_id, $type, $neg_stock_quantity, $stock_price, $stock_serial_number, $stock_transaction_reason, $date, $time, $username, $stock_shelf);
                                                    mysqli_stmt_execute($stmt_trans);
                                                    echo("Transaction Added");
                                                }


                                            }

                                        }

                                        // check minimum stock

                                        // get site id
                                        $sql_getSite = "SELECT site.id AS site_id, site.name AS site_name
                                                        FROM site
                                                        INNER JOIN area on area.site_id = site.id
                                                        INNER JOIN shelf on shelf.area_id = area.id
                                                        WHERE shelf.id=?";
                                        $stmt_getSite = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_getSite, $sql_getSite)) {
                                            $errors[] = 'min_stock stock table error - SQL connection';
                                            header("Location: $redirect_url&error=stockTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_getSite, "s", $stock_shelf);
                                            mysqli_stmt_execute($stmt_getSite);
                                            $result_getSite = mysqli_stmt_get_result($stmt_getSite);
                                            $rowCount_getSite = $result_getSite->num_rows;

                                            $row_getSite = $result_getSite->fetch_assoc();
                                            $site_id = $row_getSite['site_id'];
                                            $site_name = $row_getSite['site_name'];
                                        }
                                        
                                        // get site stock count
                                        $sql_getSiteStock = "SELECT item.id AS item_id, item.quantity AS item_quantity, item.shelf_id AS item_shelf_id, item.deleted AS item_deleted,
                                                                    stock.id AS stock_id, stock.name AS stock_name
                                                            FROM item
                                                            INNER JOIN stock ON stock.id = item.stock_id
                                                            INNER JOIN shelf ON shelf.id = item.shelf_id
                                                            INNER JOIN area  ON area.id = shelf.area_id
                                                            INNER JOIN site  ON site.id=area.site_id
                                                            WHERE site.id=? AND stock.id=? AND item.deleted=0 AND item.quantity!=0";
                                        $stmt_getSiteStock  = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_getSiteStock , $sql_getSiteStock )) {
                                            $errors[] = 'min_stock stock table error - SQL connection';
                                            header("Location: $redirect_url&error=stockTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_getSiteStock , "ss", $site_id, $stock_id);
                                            mysqli_stmt_execute($stmt_getSiteStock );
                                            $result_getSiteStock  = mysqli_stmt_get_result($stmt_getSiteStock );
                                            $rowCount_getSiteStock  = $result_getSiteStock ->num_rows;
                                            $new_quantity = $rowCount_getSiteStock;
                                        }

                                        // check count 
                                        $stock_info = getItemStockInfo($stock_id);
                                        $item_location = getItemLocation($stock_shelf);
                                        $new_quantity = getItemQuantity($stock_id, $stock_shelf);
                                        $base_url = getCurrentURL();
            
                                        if (isset( $stock_info['id'])) {
                                            $stock_name = $stock_info['name'];
                                            $stock_min_stock = $stock_info['min_stock'];
                                            
                                            if ($stock_min_stock > $new_quantity) {
                                                $email_subject = ucwords($current_system_name)." - Stock Needs Re-ordering at $site_name.";
                                                $email_body = "<p>Stock is below minimum stock count, for <strong><a href=\"https://$base_url?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p><p style='color:red'>Please raise a PO to order more!</p>";
                                                send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 9);
                                            }
                                
                                            $email_subject = ucwords($current_system_name)." - Stock inventory removed.";
                                            $email_body = "<p>Stock removed, from <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> in <strong>".$item_location['site_name']."</strong>, <strong>".$item_location['area_name']."</strong>, <strong>".$item_location['shelf_name']."</strong>!<br>New stock count: <strong>$new_quantity</strong>.</p>";
                                                send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 2);
                                            header("Location: $redirect_url&success=stockRemoved");
                                            exit();
                                        } else {
                                            header("Location: $redirect_url&error=noIDInTable");
                                            exit();
                                        }
                    
                                        

                                    } else {
                                        $errors[] = 'not enough stock quantity found to remove (total stored = '.$totalQuantity.', total to remove = '.$stock_quantity.')';
                                        header("Location: $redirect_url&error=notEnoughStockQuantity");
                                        exit();
                                    }


                                }
                            }
                        } else { // too many rows (checkID query)
                            $errors[] = 'checkID stock table error - too many rows with same ID';
                            header("Location: $redirect_url&error=tooManyWithSameID");
                            exit();
                        }
                    }
        
                } else {
                    // Error: One or more variables is empty
                    header("Location: $redirect_url&error=missingInfoInPOST");
                    exit();
                }
        
        
        
            } else {
                header("Location: $redirect_url&error=incorrectSubmitValue");
                exit();
            }
        } elseif (isset($_POST['stock-edit'])) { // stock edit bits from the stock-edit-action.inc.php - need to add a hidden input with name="stock-edit"
            // Main Info Form - id, name, description sku etc.
            if (isset($_POST['submit']) && ($_POST['submit'] == 'Save')) {

                // print_r($_POST);
                // exit();

                if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['sku'])) {
                    $stock_id = $_POST['id'];
                    $stock_name = $_POST['name'];
                    $stock_sku = $_POST['sku'];
                    $stock_description = isset($_POST['description'])? $_POST['description'] : '';
                    $stock_labels = isset($_POST['labels'])? $_POST['labels'] : '';
                    $stock_labels_init = isset($_POST['labels-init'])? $_POST['labels-init'] : '';
                    $stock_labels_selected = isset($_POST['labels-selected'])? $_POST['labels-selected'] : '';
                    $stock_min_stock = isset($_POST['min-stock'])? $_POST['min-stock'] : 0;
                    
                    $stock_labels_selected = explode(', ', $stock_labels_selected);

                    $labels_temp_array = [];
                    $labels_selected_temp_array = [];

                    if (is_array($stock_labels_selected)) {
                        foreach ($stock_labels_selected as $l) {
                            array_push($labels_temp_array, $l);
                        }
                    } else {
                        array_push($labels_temp_array, $stock_labels_selected);
                    }

                    if (is_array($stock_labels)) {
                        foreach ($stock_labels as $ll) {
                            array_push($labels_temp_array, $ll);
                        }
                    } else {
                        array_push($labels_temp_array, $stock_labels);
                    }

                    $stock_labels_selected = array_unique(array_merge($labels_selected_temp_array, $labels_temp_array), SORT_REGULAR);

                    // echo ("<br>id: $stock_id<br>name: $stock_name<br>sku: $stock_sku<br>description: $stock_description<br>min stock: $stock_min_stock<br>");
                    
                    // check if SKU is used - if it is error back to page
                    include 'dbh.inc.php';
                    $sql_stock = "SELECT * FROM stock WHERE sku=? AND id!=?";
                    $stmt_stock = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_bind_param($stmt_stock, "ss", $stock_sku, $stock_id);
                        mysqli_stmt_execute($stmt_stock);
                        $result_stock = mysqli_stmt_get_result($stmt_stock);
                        $rowCount_stock = $result_stock->num_rows;
                        if ($rowCount_stock > 0) {
                            // SKU exists, issue.
                            header("Location: ".$_SERVER['REQUEST_URI']."&error=duplicateSKU");
                            exit();
                        } else {
                            //SKU not found, continue.

                            //update the content
                            $sql_update = "UPDATE stock SET name=?, description=?, sku=?, min_stock=? WHERE id=?";
                            $stmt_update = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                                header("Location: ".$_SERVER['REQUEST_URI']."&error=updateStockSQL");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_update, "sssss", $stock_name, $stock_description, $stock_sku, $stock_min_stock, $stock_id);
                                mysqli_stmt_execute($stmt_update);

                                // add labels to the stock_labels table
                                function addLabel($stock_id, $label_id) {
                                    include 'dbh.inc.php';
                                    $sql_add = "INSERT INTO stock_label (stock_id, label_id) VALUES (?, ?)";
                                    $stmt_add = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_add, $sql_add)) {
                                        echo ("error");
                                    } else {
                                        mysqli_stmt_bind_param($stmt_add, "ss", $stock_id, $label_id);
                                        mysqli_stmt_execute($stmt_add);
                                        $insert_id = mysqli_insert_id($conn);

                                        // update changelog
                                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_label", $insert_id, "stock_id", null, $stock_id);
                                    }
                                }
                                function cleanupLabels($stock_id) {
                                    include 'dbh.inc.php';
                                    include 'changelog.inc.php';

                                    $sql = "SELECT id FROM stock_label WHERE stock_id=$stock_id";
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        $rowCount = $result->num_rows;

                                        while ($row = $result->fetch_assoc()) {
                                            $id = $row['id'];
                                            $sql_clean = "DELETE FROM stock_label WHERE stock_id=$stock_id AND id=$id";
                                            $stmt_clean = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_clean, $sql_clean)) {
                                                echo ("error");
                                            } else {
                                                mysqli_stmt_execute($stmt_clean);

                                                // update changelog
                                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "stock_label", $id, "stock_id", $stock_id, null);
                                            }
                                        }
                                    }
                                }

                            

                                if ($stock_labels_selected != '') { 
                                    cleanupLabels($stock_id);
                                    if (is_array($stock_labels_selected) && !empty($stock_labels_selected)) {
                                        foreach ($stock_labels_selected as $label) {
                                            if ($label != '' && $label != null) {
                                                addLabel($stock_id, $label);
                                            }
                                        }
                                    } else {
                                        if ($stock_labels_selected != '' && $stock_labels_selected != null) {
                                            addLabel($stock_id, $stock_labels_selected);
                                        }
                                    }
                                }
                                $stock_info = getItemStockInfo($stock_id);
                                $base_url = getCurrentURL();
            
                                $email_subject = ucwords($current_system_name)." - Stock information edited";
                                $email_body = "<p>Stock details updated for <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong> and successfully saved!</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 5);
                                header("Location: ../stock.php?stock_id=$stock_id&modify=edit&success=changesSaved");
                                exit();
                            }

                        }
                    }
                }
                
                
            } elseif (isset($_POST['submit']) && ($_POST['submit'] == 'image-delete')) {
                
                // echo('Delete<br>');
                // print_r($_POST);

                $redi_url = '../stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';

                if (isset($_POST['stock_id'])) {
                    if (isset($_POST['img_id'])) {
                        $stock_id = $_POST['stock_id'];
                        $img_id = $_POST['img_id'];

                        include 'dbh.inc.php';
                        $sql_delete_stock_img = "DELETE FROM stock_img WHERE stock_id=? AND id=?";
                        $stmt_delete_stock_img = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_delete_stock_img, $sql_delete_stock_img)) {
                            header("Location: ".$redi_url."&error=stock_imgTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_delete_stock_img, "ss", $stock_id, $img_id);
                            mysqli_stmt_execute($stmt_delete_stock_img);
                            $rows_delete_stock_img = $conn->affected_rows;
                            if ($rows_delete_stock_img == 0) {
                                // No Rows deleted.
                                header("Location: ".$redi_url."&error=noImgRemoved");
                                exit();
                            } else {
                                // Rows Deleted.
                                $stock_info = getItemStockInfo($stock_id);
                                $base_url = getCurrentURL();
            
                                $email_subject = ucwords($current_system_name)." - Image unlinked from stock";
                                $email_body = "<p>Image with ID: <strong>$img_id</strong> unlinked from <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong>.</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 6);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "stock_img", $img_id, "stock_id", $stock_id, null);

                                header("Location: ".$redi_url."&success=ImgRemoved&count=".$rows_delete_stock_img);
                                exit();
                            }
                            
                        }
                    } else {
                        // no img_id set
                        header("Location: ".$redi_url."&error=no-img_id");
                        exit();  
                    }
                } else {
                    // no stock_id set
                    header("Location: ".$redi_url."&error=no-stock_id");
                    exit();
                }
            } elseif (isset($_POST['submit']) && ($_POST['submit'] == 'Add Image')) {
                
                // echo('Add<br>');
                // print_r($_POST);

                $redi_url = '../stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';

                if (isset($_POST['img-file-name'])) {
                    if (isset($_POST['stock_id'])) {
                        
                        // Check if the relationship already exists in the DB (if image is already linked)

                        include 'dbh.inc.php';
                        $sql_imgCheck = "SELECT * FROM stock_img WHERE image=? AND stock_id=?";
                        $stmt_imgCheck = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_imgCheck, $sql_imgCheck)) {
                            echo("ERROR getting entries");
                        } else {
                            mysqli_stmt_bind_param($stmt_imgCheck, "ss", $_POST['img-file-name'], $_POST['stock_id']);
                            mysqli_stmt_execute($stmt_imgCheck);
                            $result_imgCheck = mysqli_stmt_get_result($stmt_imgCheck);
                            $rowCount_imgCheck = $result_imgCheck->num_rows;
                            if ($rowCount_imgCheck > 0) {
                                // Image already linked, throw error and return.
                                header("Location: ".$redi_url."&error=imageAlreadyLinked");
                                exit();
                            } else {
                                // Can be linked - continue.

                                $sql = "INSERT INTO stock_img (image, stock_id) VALUES (?, ?)";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: ".$redi_url."&error=stock_imgTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "ss", $_POST['img-file-name'], $_POST['stock_id']);
                                    mysqli_stmt_execute($stmt);
                                    $new_stock_img_id = mysqli_insert_id($conn);

                                    $modified_rows = $conn->affected_rows;
                                    if ($modified_rows == 0) {
                                        // No rows changed - error
                                        header("Location: ".$redi_url."&error=stock_imgNoRowsChanged");
                                        exit();
                                    } else if ($modified_rows == 1) {
                                        // correct number of rows change - success
                                        $stock_info = getItemStockInfo($_POST['stock_id']);
                                        $base_url = getCurrentURL();
            
                                        $email_subject = ucwords($current_system_name)." - Stock image added";
                                        $email_body = "<p>Image successfully added to <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong>!</p>";
                                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 6);
                                        // update changelog
                                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_img", $new_stock_img_id, "image", null, $_POST['img-file-name']);

                                        header("Location: ".$redi_url."&success=stock_imgAdded");
                                        exit();
                                    } else {
                                        // Too many rows changed - error
                                        header("Location: ".$redi_url."&error=stock_imgTooManyRowsChanged_".$modified_rows);
                                        exit();
                                    }
                                }

                            }
                        }

                    } else {
                        // redirect and error for no stock_id
                        // echo('<or class="red">Error: no stock_id.</or><br>');
                        header("Location: ".$redi_url."&error=no-stock_id");
                        exit();
                    }   
                } else {
                    // redirect and error for no file name
                    // echo('<or class="red">Error: no img-file-name.</or><br>');
                    header("Location: ".$redi_url."&error=no-img-file-name");
                    exit();
                }

            } elseif (isset($_POST['submit']) && ($_POST['submit'] == 'Upload')) {

                // echo('Upload<br>');
                // print_r($_POST);
                // print_r($_FILES);

                $redi_url = '../stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';
                if (isset($_POST['stock_id'])) {
                    if (isset($_FILES['image'])) {
                        image_upload('image', $_POST['stock_id'], $redi_url, '');
                        $stock_info = getItemStockInfo($_POST['stock_id']);
                        $base_url = getCurrentURL();
            
                        $email_subject = ucwords($current_system_name)." - Stock image uploaded";
                        $email_body = "<p>Image successfully uploaded for <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong>!</p>";
                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 6);
                        header("Location: ".$redi_url."&success=fileUploaded");
                        exit();
                    } else {
                        // no $_FILES['image']
                        header("Location: ".$redi_url."&error=no-uploadedImage");
                        exit();
                    }
                } else {
                    // no stock_id
                    header("Location: ".$redi_url."&error=no-stock_id");
                    exit();
                }
                

            } else {
                if(session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                } 
                header("Location: ../".$redirect_url."&error=noSubmit&line=".__LINE__);
                exit();
            }
        } elseif (isset($_POST['stock-move'])) { // stock move bits from the stock-move-existing.inc.php - need to add a hidden input with name="stock-move"
            
            $stock_id = isset($_POST['current_stock']) ? $_POST['current_stock'] : '';
            $redirect_url = "../stock.php?stock_id=$stock_id&modify=move";

            if (isset($_POST['submit'])) {

                if ($_POST['submit'] == 'Move') {
                    // print_r($_POST);
                    // include 'get-config.inc.php';
                    
                    $to = $current_smtp_to_email;
                    $toName = 'Root';
                    $fromName = $current_smtp_from_email;

                    $current_date = date('Y-m-d'); // current date in YYY-MM-DD format
                    $current_time = date('H:i:s'); // current time in HH:MM:SS format

                    $current_stock_id = $_POST['current_stock'];
                    $current_manufacturer_id = $_POST['current_manufacturer'];
                    $current_upc = $_POST['current_upc'];
                    $current_serial_number = $_POST['current_serial'];
                    $current_quantity = $_POST['current_quantity'];
                    $current_cost = $_POST['current_cost'];
                    $current_comments = $_POST['current_comments'];
                    $current_site_id = $_POST['current_site'];
                    $current_area_id = $_POST['current_area'];
                    $current_shelf_id = $_POST['current_shelf'];

                    $current_i = $_POST['current_i'];

                    $new_site_id = $_POST['site'];
                    $new_area_id = $_POST['area'];
                    $new_shelf_id = $_POST['shelf'];
                    $new_serial_number = isset($_POST['serial']) ? $_POST['serial'] : '';

                    $move_quantity = $_POST['quantity'];

                    // Transaction updates
                    function updateTransactions($stock_id, $item_id, $type, $quantity, $shelf_id, $serial_number, $reason, $date, $time, $username) {
                        include 'dbh.inc.php';
                        $cost = 0;
                        $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, shelf_id, quantity, price, serial_number, reason,  date, time, username) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_trans = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                            header("Location: ../".$redirect_url."&error=transactionConnectionSQL");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $stock_id, $item_id, $type, $shelf_id, $quantity, $cost, $serial_number, $reason, $date, $time, $username);
                            mysqli_stmt_execute($stmt_trans);
                            // echo ("transaction added");
                        }  
                    } 

// changes based on feedback #598

                    include 'dbh.inc.php';
                    
                    $found_ids = [];

                    $sql = "SELECT * 
                            FROM item 
                            WHERE stock_id=? AND shelf_id=? AND upc=? AND manufacturer_id=? AND serial_number=? AND cost=? AND comments=? AND deleted=0 AND quantity=1";

                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ../".$redirect_url."&error=stockTableSQLConnectionCurrentRow");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "sssssss", $stock_id, $current_shelf_id, $current_upc, $current_manufacturer_id, $current_serial_number, $current_cost, $current_comments);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount < 1) {
                            echo("no current row found");
                            // No Rows found
                            echo("<br>issue at line: ".__LINE__."<br>");
                            header("Location: ../".$redirect_url."&error=noMatchInItemTable");
                            exit();
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                $found_id = $row['id'];
                                $found_ids[] = $found_id;
                            }
                            if ($move_quantity <= count($found_ids)) {

                                for ($q=0; $q<$move_quantity; $q++) {

                                    $selected_id = $found_ids[$q];

                                    // update current row
                                    $sql_update = "UPDATE item SET deleted=1, quantity=0
                                            WHERE id=?";
                                    $stmt_update = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                                        echo("<br>issue at line: ".__LINE__."<br>");
                                        header("Location: $redirect_url&error=itemTableSQLConnectionUpdateCurrent");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt_update, "s", $selected_id);
                                        // echo($current_new_quantity.'<br>'.$current_item_id.'<br>'.$move_quantity.'<br>'.$new_serial_number_specified);
                                        mysqli_stmt_execute($stmt_update);

                                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Remove quantity", "item", $selected_id, "quantity", 1, 0);
                                        updateTransactions($current_stock_id, $selected_id, 'move', -1, $current_shelf_id, $current_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 

                                        // add new row in new location
                                        $sql = "INSERT INTO item (stock_id, upc, cost, serial_number, comments, manufacturer_id, shelf_id, quantity, deleted) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0)";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            echo("<br>issue at line: ".__LINE__."<br>");
                                            header("Location: $redirect_url&error=itemTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "sssssss", $current_stock_id, $current_upc, $current_cost, $current_serial_number, $current_comments, $current_manufacturer_id, $new_shelf_id);
                                            mysqli_stmt_execute($stmt);
                                            $new_item_id = mysqli_insert_id($conn); // ID of the new row in the table.

                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add quantity", "item", $new_item_id, "quantity", null, 1);
                                            updateTransactions($current_stock_id, $new_item_id, 'move', 1, $new_shelf_id, $current_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                                        }
                                    }
                                }
                                $stock_info = getItemStockInfo($current_stock_id);
                                $current_location = getItemLocation($current_shelf_id);
                                $new_location = getItemLocation($new_shelf_id);
                                $base_url = getCurrentURL();
            
                                $move_body = "<p><strong>".$move_quantity."</strong> stock moved from <strong>".$current_location['site_name']."</strong>, <strong>".$current_location['area_name']."</strong>, <strong>".$current_location['shelf_name']."</strong> to <strong>".$new_location['site_name']."</strong>, <strong>".$new_location['area_name']."</strong>, <strong>".$new_location['shelf_name']."</strong> for <strong><a href=\"https://$base_url/stock.php?stock_id=".$stock_info['id']."\">".$stock_info['name']."</a></strong>.</p>";
                                send_email($to, $toName, $fromName, ucwords($current_system_name).' - Stock Moved', createEmail($move_body), 4);
                                header("Location: $redirect_url&success=stockMoved&edited=$current_i&newItemId=$new_item_id"); // Final redirect - for success and stock is moved.
                                exit();
                            } else {
                                header("Location: ../".$redirect_url."&error=notEnoughStockedForRemoval");
                                exit();
                            }
                        }
                    }
                    
// end of the update
                
                } else { // else for the username checker at top of page.
                    header("Location: $redirect_url&error=unauthorised");
                    exit();
                }
            } else { // else for the submit button checker at top of page.
                header("Location: $redirect_url&error=noSubmit&line=".__LINE__);
                exit();
            }
        } elseif (isset($_POST['stock-row-submit'])) { // stock.php indivdual rows submission to edit individual rows in the table e.g. adding serial numbers
            if (isset($_POST['item-id'])) {
                $sql = "SELECT * 
                        FROM item 
                        WHERE id=?";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../".$redirect_url."&error=itemTableSQLConnectionCurrentRow");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $_POST['item-id']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        echo("no current row found");
                        // No Rows found
                        echo("<br>issue at line: ".__LINE__."<br>");
                        header("Location: ../".$redirect_url."&error=noMatchInItemTable");
                        exit();
                    } else {
                        $row = $result->fetch_assoc();

                        if ($row['upc'] !== $_POST['upc']) {
                            $upc = $_POST['upc'];
                            $sql_upc = "UPDATE item SET upc='$upc' WHERE id=?";
                            $stmt_upc = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_upc, $sql_upc)) {
                                $errors[] = 'remove_row item table error - SQL connection';
                                header("Location: $redirect_url&error=itemTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_upc, "s", $_POST['item-id']);
                                mysqli_stmt_execute($stmt_upc);

                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "item", $_POST['item-id'], "upc", $row['upc'], $_POST['upc']);
                            }
                        }

                        if ($row['serial_number'] !== $_POST['serial_number']) {
                            $serial_number = $_POST['serial_number'];
                            $sql_serial_number = "UPDATE item SET serial_number='$serial_number' WHERE id=?";
                            $stmt_serial_number = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_serial_number, $sql_serial_number)) {
                                $errors[] = 'remove_row item table error - SQL connection';
                                header("Location: $redirect_url&error=itemTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_serial_number, "s", $_POST['item-id']);
                                mysqli_stmt_execute($stmt_serial_number);

                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "item", $_POST['item-id'], "serial_number", $row['serial_number'], $_POST['serial_number']);
                            }
                        }

                        if ($row['cost'] !== $_POST['cost']) {
                            if ($_POST['cost'] == '') {
                                $cost = 0;
                            } else {
                                $cost = $_POST['cost'];
                            }
                            $sql_cost = "UPDATE item SET cost='$cost' WHERE id=?";
                            $stmt_cost = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_cost, $sql_cost)) {
                                $errors[] = 'remove_row item table error - SQL connection';
                                header("Location: $redirect_url&error=itemTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_cost, "s", $_POST['item-id']);
                                mysqli_stmt_execute($stmt_cost);

                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "item", $_POST['item-id'], "cost", $row['cost'], $_POST['cost']);
                            }
                        }

                        if ($row['comments'] !== $_POST['comments']) {
                            $comments = $_POST['comments'];
                            $sql_comments = "UPDATE item SET comments='$comments' WHERE id=?";
                            $stmt_comments = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_comments, $sql_comments)) {
                                $errors[] = 'remove_row item table error - SQL connection';
                                header("Location: $redirect_url&error=itemTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_comments, "s", $_POST['item-id']);
                                mysqli_stmt_execute($stmt_comments);

                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "item", $_POST['item-id'], "comments", $row['comments'], $_POST['comments']);
                            }
                        }

                        header("Location: ../".$redirect_url.$query_char."success=updated");
                        exit();
                    }
                }
            } else {
                header("Location: ../".$redirect_url.$query_char."error=noItemID");
                exit();
            }
        } else {
            header("Location: ../".$redirect_url.$query_char."error=unknownQuery");
            exit(); 
        }
    } else {
        header("Location: ../".$redirect_url.$query_char."error=noLogin");
        exit();
    }
} elseif (isset($_GET['type']) && $_GET['type'] == "delete") { // delete bits from the stock-remove-existing.inc.php - need to add a hidden input with name="stock-delete" for this
    //
    // STILL  NEED TO WORK THE MINIMUM STOCK INTO THE CHECKER FOR REMOVING STOCK
    //
    if (isset($_SESSION['username']) && $_SESSION['username'] != '' && $_SESSION['username'] != null) {
        $errors =[];

        if (isset($_GET['type'])) {
            if(session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            } 
            $redirect_url = "../stock.php?modify=remove&stock_id=".$_GET['stock_id'];

            // DELETE ENTIRE STOCK OBJECT
            if ( $_GET['type'] == "delete") {
                if (isset($_GET['stock_id'])) {
                    if (is_numeric($_GET['stock_id'])) {
                        // echo('Type='.$_GET['type'].'<br>ID='.$_GET['stock_id'].'<br>');

                        $stock_id = $_GET['stock_id'];

                        include 'dbh.inc.php';
                        $sql_checkID = "SELECT * FROM stock
                                        WHERE id=?
                                        ORDER BY id";
                        $stmt_checkID = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_checkID, $sql_checkID)) {
                            $errors[] = 'checkID stock table error - SQL connection';
                            header("Location: $redirect_url&error=stockTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_checkID, "s", $stock_id);
                            mysqli_stmt_execute($stmt_checkID);
                            $result_checkID = mysqli_stmt_get_result($stmt_checkID);
                            $rowCount_checkID = $result_checkID->num_rows;
                            if ($rowCount_checkID < 1) {
                                $errors[] = 'checkID stock table error - no IDs found';
                                header("Location: $redirect_url&error=noIDInTable");
                                exit();
                            } elseif ($rowCount_checkID == 1) { 
                                $row_checkID = $result_checkID->fetch_assoc();

                                $checkID_id = $row_checkID['id'];
                                $checkID_name = $row_checkID['name'];
                                $checkID_description = $row_checkID['description'];
                                $checkID_sku = $row_checkID['sku'];
                                $checkID_min_stock = $row_checkID['min_stock'];

                                // GET TOTAL ITEM COUNT BEFORE DELETE
                                $sql_totalItemCount = "SELECT sum(quantity) AS quantity FROM item
                                                WHERE stock_id=?
                                                ORDER BY id";
                                $stmt_totalItemCount = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_totalItemCount, $sql_totalItemCount)) {
                                    $errors[] = 'totalItemCount item table error - SQL connection';
                                    header("Location: $redirect_url&error=stockTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_totalItemCount, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_totalItemCount);
                                    $result_totalItemCount = mysqli_stmt_get_result($stmt_totalItemCount);
                                    $rowCount_totalItemCount = $result_totalItemCount->num_rows;
                                    if ($rowCount_totalItemCount < 1) {
                                        $errors[] = 'totalItemCount item table error - no quantity found';
                                        header("Location: $redirect_url&error=noIDInTable");
                                        exit();
                                    } elseif ($rowCount_totalItemCount == 1) { 
                                        $itemCountTotal = $result_totalItemCount->fetch_assoc()['quantity'];
                                        if ($itemCountTotal == null) {
                                            $itemCountTotal = 0;
                                        }
                                    }
                                }

                                // CLEAR ITEM TABLE
                                $sql_delete_check = "SELECT id FROM item
                                                WHERE stock_id=?
                                                ORDER BY id";
                                $stmt_delete_check = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_check, $sql_delete_check)) {
                                    $errors[] = 'delete item table error - SQL connection';
                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_check, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_check);
                                    $result_delete_check = mysqli_stmt_get_result($stmt_delete_check);
                                    $rowCount_delete_check = $result_delete_check->num_rows;

                                    while ($row_delete_check = $result_delete_check->fetch_assoc()) {
                                        $item_delete_id = $row_delete_check['id'];
                                        // CLEAR STOCK_IMG TABEL
                                        $sql_delete_item = "UPDATE item SET deleted=1 WHERE stock_id=? AND id=?";
                                        $stmt_delete_item = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_delete_item, $sql_delete_item)) {
                                            $errors[] = 'delete item table error - SQL connection';
                                            header("Location: $redirect_url&error=itemTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_delete_item, "ss", $stock_id, $item_delete_id);
                                            mysqli_stmt_execute($stmt_delete_item);
                                            $rows_delete_item = $conn->affected_rows;
                                            if ($rows_delete_item > 0) {
                                                // echo("<br>Item(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_item<br>");
                                                // update changelog for delete
                                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "item", $item_delete_id, "stock_id", $stock_id, null);
   
                                            } else {
                                                // There wont always be items related to the stock object, ignore the error

                                                // echo("<br>No Items Deleted for stock_id: $stock_id... <br>");
                                                // header("Location: $redirect_url&error=deleteItemTable-NoRowsDeleted");
                                                // exit();
                                            }
                                            
                                        }
                                    }
                                }


                                
                                // CLEAR ITEM TABLE
                                $sql_delete_img_check = "SELECT id FROM stock_img
                                                WHERE stock_id=?
                                                ORDER BY id";
                                $stmt_delete_img_check = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_img_check, $sql_delete_img_check)) {
                                    $errors[] = 'delete item table error - SQL connection';
                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_img_check, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_img_check);
                                    $result_delete_img_check = mysqli_stmt_get_result($stmt_delete_img_check);
                                    $rowCount_delete_img_check = $result_delete_img_check->num_rows;

                                    while ($row_delete_img_check = $result_delete_img_check->fetch_assoc()) {
                                        $img_delete_id = $row_delete_img_check['id'];

                                        $sql_delete_stock_img = "DELETE FROM stock_img WHERE stock_id=? AND id=?";
                                        $stmt_delete_stock_img = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_delete_stock_img, $sql_delete_stock_img)) {
                                            $errors[] = 'delete stock_img table error - SQL connection';
                                            header("Location: $redirect_url&error=stock_imgTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_delete_stock_img, "ss", $stock_id, $img_delete_id);
                                            mysqli_stmt_execute($stmt_delete_stock_img);
                                            $rows_delete_stock_img = $conn->affected_rows;
                                            if ($rows_delete_stock_img > 0) {
                                                // echo("<br>stock_img(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_stock_img<br>");
                                                // update changelog for delete
                                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "stock_img", $item_delete_id, "stock_id", $stock_id, null);
                                            } else {
                                                // There wont always be images linked, so ignore this 

                                                // echo("<br>No stock_imgs Deleted for stock_id: $stock_id... <br>");
                                                // header("Location: $redirect_url&error=deletestock_imgTable-NoRowsDeleted");
                                                // exit();
                                            }
                                            
                                        }
                                    }
                                }
                                

                                // CLEAR STOCK_LABEL TABEL
                                $sql_delete_label_check = "SELECT id FROM stock_label
                                                WHERE stock_id=?
                                                ORDER BY id";
                                $stmt_delete_label_check = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_label_check, $sql_delete_label_check)) {
                                    $errors[] = 'delete item table error - SQL connection';
                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_label_check, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_label_check);
                                    $result_delete_label_check = mysqli_stmt_get_result($stmt_delete_label_check);
                                    $rowCount_delete_label_check = $result_delete_label_check->num_rows;

                                    while ($row_delete_label_check = $result_delete_label_check->fetch_assoc()) {
                                        $label_delete_id = $row_delete_label_check['id'];

                                        $sql_delete_stock_label = "DELETE FROM stock_label WHERE stock_id=? AND id=?";
                                        $stmt_delete_stock_label = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_delete_stock_label, $sql_delete_stock_label)) {
                                            $errors[] = 'delete stock_label table error - SQL connection';
                                            header("Location: $redirect_url&error=stock_labelTableSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_delete_stock_label, "ss", $stock_id, $label_delete_id);
                                            mysqli_stmt_execute($stmt_delete_stock_label);
                                            $rows_delete_stock_label = $conn->affected_rows;
                                            if ($rows_delete_stock_label > 0) {
                                                // echo("<br>stock_label(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_stock_label<br>");
                                                // update changelog for delete
                                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "stock_label", $label_delete_id, "stock_id", $stock_id, null);
                                            } else {
                                                // There wont always be labels to delete, so ignore for now.

                                                // echo("<br>No stock_labels Deleted for stock_id: $stock_id... <br>");
                                                // header("Location: $redirect_url&error=deleteStock_labelTable-NoRowsDeleted");
                                                // exit();
                                            }
                                            
                                        }
                                    }
                                }

                                // CLEAR STOCK TABLE
                                $sql_stock_name = "SELECT name FROM stock
                                                WHERE id=?
                                                ORDER BY id";
                                $stmt_stock_name = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_stock_name, $sql_stock_name)) {
                                    $errors[] = 'delete item table error - SQL connection';
                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_stock_name, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_stock_name);
                                    $result_stock_name = mysqli_stmt_get_result($stmt_stock_name);
                                    $rowCount_stock_name = $result_stock_name->num_rows;

                                    $row_stock_name = $result_stock_name->fetch_assoc();
                                    $stock_delete_name = $row_stock_name['name'];
                                }
                                $sql_delete_stock = "UPDATE stock SET deleted=1 WHERE id=?";
                                $stmt_delete_stock = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_stock, $sql_delete_stock)) {
                                    $errors[] = 'delete stock table error - SQL connection';
                                    header("Location: $redirect_url&error=stockTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_stock, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_stock);
                                    $rows_delete_stock = $conn->affected_rows;
                                    if ($rows_delete_stock > 0) {
                                        // echo("<br>Stock Deleted for id: $stock_id , Row count: $rows_delete_stock<br>");
                                        // update changelog for delete
                                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "stock", $stock_id, "deleted", 0, 1);
                                    } else {
                                        // echo("<br>No Stock Deleted for id: $stock_id... <br>");
                                        header("Location: $redirect_url&error=deleteStockTable-NoRowsDeleted");
                                        exit();
                                    }
                                    
                                }

                                $type = 'delete';
                                $empty_item_id = 0;
                                $empty_cost = 0;
                                $empty_serial_number = '';
                                $reason = "No Stock Remaining, Deleted.";
                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                $username = $_SESSION['username'];
                                $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, shelf_id, quantity, price, serial_number, reason,  date, time, username) 
                                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt_trans = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                                    header("Location: $redirect_url&error=TransactionConnectionIssue");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $stock_id, $empty_item_id, $type, $stock_shelf, $itemCountTotal, $empty_cost, $empty_serial_number, $reason, $date, $time, $username);
                                    mysqli_stmt_execute($stmt_trans);
                                    header("Location: ../stock.php?modify=remove&success=stockDeleted&old_stock_id=$stock_id");
                                    exit();
                                } 

                                
                            } else { // too many rows (checkID query)
                                $errors[] = 'checkID stock table error - too many rows with same ID';
                                header("Location: $redirect_url&error=tooManyWithSameID");
                                exit();
                            }
                        }
                    } else {
                        $errors[] = 'Non-numeric ID';
                    }
                } else {
                    $errors[] = 'ID not set';
                }
            } else {
                $errors[] = 'type is not = delete';
            }
        } else {
            header("Location: $redirect_url&error=typeMissing");
            exit();
        }
    } else {
        header("Location: ".$redirect_url."&error=noLogin");
        exit();
    }
} else {
    header("Location: ../".$redirect_url.$query_char."error=noSubmit&line=".__LINE__);
    exit();
}