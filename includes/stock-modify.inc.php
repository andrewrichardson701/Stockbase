<?php
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

session_start(); // start the session

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

// check whether to delete the row - from the stock-remove-existing.inc.php page
function checkDeleteCurrentRow($item_id) {
    global $redirect_url, $current_system_name, $loggedin_email, $loggedin_fullname, $config_smtp_from_name;
    include 'dbh.inc.php';

    $sql = "SELECT * FROM item WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: $redirect_url&error=itemTableSQLConnectionCurrentRowCheck");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $item_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            // No Rows found
            // continue.
        } else {
            // Row found
            $row = $result->fetch_assoc();
            $quantity = $row['quantity'];
            $stock_id = $row['stock_id'];
            if ($quantity == 0 || $quantity == '0') {
                // Row has no quantity
                // Delete the row

                $sql_delete = "DELETE FROM item WHERE id=?";
                $stmt_delete = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_delete, $sql_delete)) {
                    echo("<br>issue at line: ".__LINE__."<br>");
                    header("Location: $redirect_url&error=itemTableSQLConnectionUpdateCurrent");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_delete, "s", $item_id);
                    mysqli_stmt_execute($stmt_delete);
                    $email_subject = ucwords($current_system_name)." - Stock inventory deleted.";
                    $email_body = "<p>Stock inventory deleted to stock ID: $stock_id, with item ID: <strong>$item_id</strong>!</p>";
                        send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                    header("Location: $redirect_url&success=stockRemoved&row=deleted");
                    exit();
                }
            } else {
                // something went wrong here -> should not get here
                echo ('something went wrong here... line: '.__LINE__);
            }
        }
    }
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
                        usort($PRE_skus, function($a, $b) { // sort the array 
                            return strnatcmp($a, $b);
                        });
                        preg_match_all('/\d+/', end($PRE_skus), $max_sku_number_temp);
                        $max_sku_number = $max_sku_number_temp[0][0];
                        $new_PRE_sku_number = $max_sku_number +1; 

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
                            }
                        }
                    }

                    $id = $insert_id;
                } else {
                    $id = $_POST['id'];
                }

                // Check if this content already matches an entry 
                $sql_item = "SELECT DISTINCT id, stock_id, upc, quantity, cost, serial_number, comments, manufacturer_id, shelf_id
                            FROM item
                            WHERE stock_id=? AND upc=? AND serial_number=? AND manufacturer_id=? AND cost=? AND shelf_id=?
                            ORDER BY stock_id";
                $stmt_item = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_item, $sql_item)) {
                    header("Location: ../".$redirect_url.$redirect_queries."&error=itemTableSQLConnection");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_item, "ssssss", $id, $upc, $serial_number, $manufacturer, $cost, $shelf);
                    mysqli_stmt_execute($stmt_item);
                    $result_item = mysqli_stmt_get_result($stmt_item);
                    $rowCount_item = $result_item->num_rows;
                    if ($rowCount_item < 1) {
                        // no rows exist, add new
                        $sql = "INSERT INTO item (stock_id, upc, quantity, cost, serial_number, comments, manufacturer_id, shelf_id) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$redirect_queries."&error=itemTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "ssssssss", $id, $upc, $quantity, $cost, $serial_number, $comments, $manufacturer, $shelf);
                            mysqli_stmt_execute($stmt);
                            $item_id = mysqli_insert_id($conn); // ID of the new row in the table.

                            // Transaction update
                            $type = 'add';
                            $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, quantity, price, serial_number, reason,  date, time, username) 
                                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_trans = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                                header("Location: ../".$redirect_url.$redirect_queries."&error=transactionConnectionSQL");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_trans, "ssssssssss", $id, $item_id, $type, $quantity, $cost, $serial_number, $reason, $date, $time, $username);
                                mysqli_stmt_execute($stmt_trans);
                                $email_subject = ucwords($current_system_name)." - Stock inventory added";
                                $email_body = "<p>Stock inventory added, with ID: <strong>$id</strong>!</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                                header("Location: ../stock.php?stock_id=$id&item_id=$item_id&success=stockAdded");
                                exit();
                            } 
                        }
                    } elseif ($rowCount_item == 1) {
                        // rows exist, add to existing
                        $row_item = $result_item->fetch_assoc();
                        $item_id = $row_item['id'];
                        $item_quantity = $row_item['quantity'];

                        $new_quantity = (int)$item_quantity + (int)$quantity;
                        
                        $sql = "UPDATE item SET quantity=?
                                WHERE id=?";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$redirect_queries."&error=itemTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "ss", $new_quantity, $item_id);
                            mysqli_stmt_execute($stmt);
                            // Transaction update
                            $type = 'add';
                            $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, shelf_id, quantity, price, serial_number, reason,  date, time, username) 
                                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_trans = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                                header("Location: ../".$redirect_url.$redirect_queries."&error=transactionConnectionSQL");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $id, $item_id, $type, $shelf, $quantity, $cost, $serial_number, $reason, $date, $time, $username);
                                mysqli_stmt_execute($stmt_trans);
                                $email_subject = ucwords($current_system_name)." - Stock inventory added";
                                $email_body = "<p>Stock inventory added, with ID: <strong>$id</strong>!</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                                header("Location: ../stock.php?stock_id=$id&item_id=$item_id&success=stockQuantityAdded");
                                exit();
                            }  
                        }
                    } else {
                        // too many rows!
                        header("Location: ../".$redirect_url.$redirect_queries."error=multipleItemsFound");
                        exit();
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
                // print_r('<pre>');
                // print_r($_POST);
                // print_r('</pre>');
        
                $stock_id                 = isset($_POST['stock_id'])         ? $_POST['stock_id']         : '' ;
                $stock_sku                = isset($_POST['stock_sku'])        ? $_POST['stock_sku']        : '' ;
                $stock_manufacturer       = isset($_POST['manufacturer'])     ? $_POST['manufacturer']     : '' ;
                $stock_shelf              = isset($_POST['shelf'])            ? $_POST['shelf']            : '' ;
                $stock_price              = isset($_POST['price'])            ? $_POST['price']            : '' ;
                $stock_transaction_date   = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : '' ;
                $stock_quantity           = isset($_POST['quantity'])         ? $_POST['quantity']         : '' ;
                $stock_serial_number      = isset($_POST['serial-number'])    ? $_POST['serial-number']    : '' ;
                $stock_transaction_reason = isset($_POST['reason'])           ? $_POST['reason']           : '' ;
        
                // function to check the current row and if 0 quantity, remove it.
                
        
                if ($stock_id !== '' && $stock_sku !== '' && $stock_manufacturer !== '' && $stock_shelf !== '' && $stock_price !== '' && $stock_transaction_date !== '' && $stock_quantity !== '' && $stock_transaction_reason !== '') {
                    // all info is as expected - serial_number is not needed to be checked.
        
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
        
                            // GET TOTAL STOCK COUNT
                            if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                $sql_itemQuantityCheck = "SELECT quantity FROM item WHERE stock_id=? AND serial_number LIKE '%$stock_serial_number%'";
                            } else {
                                $sql_itemQuantityCheck = "SELECT quantity FROM item WHERE stock_id=?";
                            }
                            $stmt_itemQuantityCheck = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_itemQuantityCheck, $sql_itemQuantityCheck)) {
                                $errors[] = 'itemQuantity stock table error - SQL connection';
                                header("Location: $redirect_url&error=stockTableSQLConnection");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_itemQuantityCheck, "s", $stock_id);
                                mysqli_stmt_execute($stmt_itemQuantityCheck);
                                $result_itemQuantityCheck = mysqli_stmt_get_result($stmt_itemQuantityCheck);
                                $rowCount_itemQuantityCheck = $result_itemQuantityCheck->num_rows;
                                if ($rowCount_itemQuantityCheck < 1) {
                                    if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                        $errors[] = 'itemQuantity item table error (w/ serial number) - no quantity found';
                                        header("Location: $redirect_url&error=noQuantityInTableWithSerial");
                                        exit();
                                    } else {
                                        $errors[] = 'itemQuantity item table error - no quantity found (should not be able to get here)';
                                        header("Location: $redirect_url&error=noQuantityInTable");
                                        exit();
                                    }
                                } else {
                                    // QUANTITY FOUND!
                                    $stock_total_itemQuantityCheck = ($result_itemQuantityCheck->fetch_assoc())['quantity'];
                                    
                                    // GET TOTAL QUANTITY
                                    if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                        $sql_itemQuantity = "SELECT sum(quantity) AS quantity FROM item WHERE stock_id=? AND serial_number=?";
                                    } else {
                                        $sql_itemQuantity = "SELECT sum(quantity) AS quantity FROM item WHERE stock_id=?";
                                    }
                                    $stmt_itemQuantity = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_itemQuantity, $sql_itemQuantity)) {
                                        $errors[] = 'itemQuantity stock table error - SQL connection';
                                        header("Location: $redirect_url&error=stockTableSQLConnection");
                                        exit();
                                    } else {
                                        if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                            mysqli_stmt_bind_param($stmt_itemQuantity, "ss", $stock_id, $stock_serial_number);
                                        } else {
                                            mysqli_stmt_bind_param($stmt_itemQuantity, "s", $stock_id);
                                        }
                                        mysqli_stmt_execute($stmt_itemQuantity);
                                        $result_itemQuantity = mysqli_stmt_get_result($stmt_itemQuantity);
                                        $rowCount_itemQuantity = $result_itemQuantity->num_rows;
                                        if ($rowCount_itemQuantity < 1) {
                                            $errors[] = 'itemQuantity item table error - no quantity found (should not be able to get here)';
                                            header("Location: $redirect_url&error=noQuantityInTable");
                                            exit();
                                        } else {
                                            $stock_total_itemQuantity = ($result_itemQuantity->fetch_assoc())['quantity'];
                                            if ($stock_total_itemQuantity !== 0 && $stock_total_itemQuantity !== null && $stock_total_itemQuantity !== '') {
        
                                                // GET BEST ITEM ID FOR THE JOB
                                                if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                                    $sql_itemSelectID = "SELECT id, quantity FROM item WHERE stock_id=? AND shelf_id=? serial_number=? AND quantity > 0 ORDER BY quantity LIMIT 1";
                                                } else {
                                                    $sql_itemSelectID= "SELECT id, quantity FROM item WHERE stock_id=? AND shelf_id=? AND quantity > 0 ORDER BY quantity LIMIT 1";
                                                }
                                                $stmt_itemSelectID= mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt_itemSelectID, $sql_itemSelectID)) {
                                                    $errors[] = 'itemQuantity stock table error - SQL connection';
                                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                                    exit();
                                                } else {
                                                    if ($stock_serial_number !== '' && !empty($stock_serial_number)) {
                                                        mysqli_stmt_bind_param($stmt_itemSelectID, "sss", $stock_id, $_POST['shelf'], $stock_serial_number);
                                                    } else {
                                                        mysqli_stmt_bind_param($stmt_itemSelectID, "ss", $stock_id, $_POST['shelf']);
                                                    }
                                                    mysqli_stmt_execute($stmt_itemSelectID);
                                                    $result_itemSelectID= mysqli_stmt_get_result($stmt_itemSelectID);
                                                    $rowCount_itemSelectID= $result_itemSelectID->num_rows;
                                                    if ($rowCount_itemSelectID < 1) {
                                                        $errors[] = 'itemSelectID item table error - no rows found at Line: '.__LINE__.'.';
                                                        header("Location: $redirect_url&error=itemTable0ResultsLINE".__LINE__);
                                                        exit();
                                                    } else {
                                                        // remove quantity
                                                        $stock_itemSelectID_items = $result_itemSelectID->fetch_assoc();
                                                        $stock_itemSelectID_id = $stock_itemSelectID_items['id'];
                                                        $stock_itemSelectID_quantity = $stock_itemSelectID_items['quantity'];
            
                                                        $new_quantity = $stock_itemSelectID_quantity - $stock_quantity;
                                                        if ($new_quantity < 0) {
                                                            header("Location: $redirect_url&error=quantityTooLow");
                                                            exit();
                                                        }
                                                        echo ($stock_itemSelectID_id);
                                                        $sql_stock_removeQuantity = "UPDATE item SET quantity=? WHERE id=?";
                                                        $stmt_stock_removeQuantity = mysqli_stmt_init($conn);
                                                        if (!mysqli_stmt_prepare($stmt_stock_removeQuantity, $sql_stock_removeQuantity)) {
                                                            $errors[] = 'stock_removeQuantity item table error - SQL connection';
                                                            header("Location: $redirect_url&error=itemTableSQLConnection");
                                                            exit();
                                                        } else {
                                                            mysqli_stmt_bind_param($stmt_stock_removeQuantity, "ss", $new_quantity, $stock_itemSelectID_id);
                                                            mysqli_stmt_execute($stmt_stock_removeQuantity);
                                                            $rows_stock_removeQuantity = $conn->affected_rows;
                                                            if ($rows_stock_removeQuantity == 1) {
                                                                // echo("<br>Item Updated for stock_id: $stock_id , Row count: $rows_stock_removeQuantity , Item ID: $stock_itemSelectID_id<br>");
                                                                // ADD TRANSACTION
                                                                $type = 'remove';
                                                                $date = date('Y-m-d'); // current date in YYY-MM-DD format
                                                                $time = date('H:i:s'); // current time in HH:MM:SS format
                                                                $username = $_SESSION['username'];
                                                                $neg_stock_quantity = $stock_quantity * -1;
                                                                $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, shelf_id, quantity, price, serial_number, reason,  date, time, username) 
                                                                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                                                $stmt_trans = mysqli_stmt_init($conn);
                                                                if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
                                                                    $errors[] = 'trans transaction table error - SQL connection';
                                                                    header("Location: $redirect_url&error=TransactionConnectionIssue");
                                                                    exit();
                                                                } else {
                                                                    mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $stock_id, $stock_itemSelectID_id, $type, $stock_shelf, $neg_stock_quantity, $stock_price, $stock_serial_number, $stock_transaction_reason, $date, $time, $username);
                                                                    mysqli_stmt_execute($stmt_trans);
                                                                    echo("Transaction Added");
        
                                                                    // Check if new stock quantity is less than the minimum stock quantity
                                                                    $sql_min_stock = "SELECT * FROM stock WHERE id=?";
                                                                    $stmt_min_stock = mysqli_stmt_init($conn);
                                                                    if (!mysqli_stmt_prepare($stmt_min_stock, $sql_min_stock)) {
                                                                        $errors[] = 'min_stock stock table error - SQL connection';
                                                                        header("Location: $redirect_url&error=stockTableSQLConnection");
                                                                        exit();
                                                                    } else {
                                                                        mysqli_stmt_bind_param($stmt_min_stock, "s", $stock_id);
                                                                        mysqli_stmt_execute($stmt_min_stock);
                                                                        $result_min_stock = mysqli_stmt_get_result($stmt_min_stock);
                                                                        $rowCount_min_stock = $result_min_stock->num_rows;
                                                                        if ($rowCount_min_stock < 1) {
                                                                            $errors[] = 'min_stock stock table error - no row found';
                                                                            header("Location: $redirect_url&error=noIDInTable");
                                                                            exit();
                                                                        } elseif ($rowCount_min_stock == 1) { 
                                                                            $row_min_stock = $result_min_stock->fetch_assoc();
                                                                            $stock_name = $row_min_stock['name'];
                                                                            $stock_min_stock = $row_min_stock['min_stock'];
                                                                            if ($stock_min_stock > $new_quantity) {
                                                                                $email_subject = ucwords($current_system_name)." - Stock Needs Re-ordering.";
                                                                                $email_body = "<p>Stock count for stock: <strong>$stock_name</strong> with ID: <strong>$stock_id</strong>, with item ID: <strong>$stock_itemSelectID_id</strong> is below the minimum stock count: <stong>$stock_min_stock</strong> with <strong>$new_quantity</strong>!<br>Please order more.</p>";
                                                                                send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                                                                            }
                                                                        }
                                                                    }
                                                                
                                                                    // Remove any 0 quantity entries from DB
                                                                    checkDeleteCurrentRow($stock_itemSelectID_id);
                                                                    $email_subject = ucwords($current_system_name)." - Stock inventory removed.";
                                                                    $email_body = "<p>Stock inventory added to stock ID: $stock_id, with item ID: <strong>$stock_itemSelectID_id</strong>!</p>";
                                                                        send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                                                                    header("Location: $redirect_url&success=stockRemoved");
                                                                    exit();
                                                                } 
                                                                
                                                            } elseif ($rows_stock_removeQuantity == 0) {
                                                                echo("<br>No Items Updated for stock_id: $stock_id... <br>");
                                                                header("Location: $redirect_url&error=stock_removeQuantity-NoRowsUpdated");
                                                                exit();
                                                            } else {
                                                                echo("<br>Too many rows changed for stock_id: $stock_id, Row count: $rows_stock_removeQuantity<br>");
                                                                header("Location: $redirect_url&error=stock_removeQuantity-TooManyRowsUpdated");
                                                                exit();
                                                            }
                                                            
                                                        }
        
        
                                                    }
                                                }
                                                
        
        
        
                                            }
        
        
        
                                        }
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
                                    }
                                }
                                function cleanupLabels($stock_id) {
                                    include 'dbh.inc.php';
                                    $sql_clean = "DELETE FROM stock_label WHERE stock_id=$stock_id";
                                    $stmt_clean = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_clean, $sql_clean)) {
                                        echo ("error");
                                    } else {
                                        mysqli_stmt_execute($stmt_clean);
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
                                
                                $email_subject = ucwords($current_system_name)." - Stock information edited";
                                $email_body = "<p>Stock with ID: <strong>$stock_id</strong> edited and successfully saved!</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                                header("Location: ../stock.php?stock_id=$stock_id&modify=edit&success=changesSaved");
                                exit();
                            }

                        }
                    }
                }
                
                
            } elseif (isset($_POST['submit']) && ($_POST['submit'] == 'image-delete')) {
                
                // echo('Delete<br>');
                // print_r($_POST);

                $redi_url = 'https://inventory.arpco.xyz/stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';

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
                                $email_subject = ucwords($current_system_name)." - Image unlinked from stock";
                                $email_body = "<p>Image with ID: <strong>$img_id</strong> unlinked from stock item with ID: <strong>$stock_id</strong>.</p>";
                                    send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
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
                                    $modified_rows = $conn->affected_rows;
                                    if ($modified_rows == 0) {
                                        // No rows changed - error
                                        header("Location: ".$redi_url."&error=stock_imgNoRowsChanged");
                                        exit();
                                    } else if ($modified_rows == 1) {
                                        // correct number of rows change - success
                                        $email_subject = ucwords($current_system_name)." - Stock image added";
                                        $email_body = "<p>Image successfully added to stock with ID: <strong>".$_POST['stock_id']."</strong>!</p>";
                                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
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

                $redi_url = 'https://inventory.arpco.xyz/stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';
                if (isset($_POST['stock_id'])) {
                    if (isset($_FILES['image'])) {
                        image_upload('image', $_POST['stock_id'], $redi_url, '');
                        $email_subject = ucwords($current_system_name)." - Stock image uploaded";
                        $email_body = "<p>Image successfully uploaded for stock with ID: <strong>".$_POST['stock_id']."</strong>!</p>";
                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
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
                session_start();
                header("Location: ../".$redirect_url."&error=noSubmit&line=".__LINE__);
                exit();
            }
        } elseif (isset($_POST['stock-move'])) { // stock move bits from the stock-move-existing.inc.php - need to add a hidden input with name="stock-move"
            // NEED TO CHECK IF A USER IS LOGGED IN 
            // CHECK IF SUBMIT IS PUSHED

            // MAYBE MOVE THE SERIAL NUMBERS TO SEPERATE ROWS IN THE ADD PROCESS?


            // IF ROW BECOMES 0 AFTER MOVE, DELETE THE ROW?
            $stock_id = isset($_POST['current_stock']) ? $_POST['current_stock'] : '';
            $redirect_url = "../stock.php?stock_id=$stock_id&modify=move";

            if (isset($_POST['submit'])) {
                session_start();

                if ($_POST['submit'] == 'Move') {
                    // print_r($_POST);
                    
                    $to = 'inventory@ajrich.co.uk';
                    $toName = 'Andrew';
                    $fromName = 'Inventory';

                    $current_date = date('Y-m-d'); // current date in YYY-MM-DD format
                    $current_time = date('H:i:s'); // current time in HH:MM:SS format

                    $current_item_id = $_POST['current_item'];
                    $current_manufacturer_id = $_POST['current_manufacturer'];
                    $current_upc = $_POST['current_upc'];
                    $current_serial_number = $_POST['current_serial'];
                    $current_quantity = $_POST['current_quantity'];
                    $current_site_id = $_POST['current_site'];
                    $current_area_id = $_POST['current_area'];
                    $current_shelf_id = $_POST['current_shelf'];

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
                            echo ("transaction added");
                        }  
                    } 

                    // Check if the values all match up with current DB.

                    include 'dbh.inc.php';
                    if ($current_serial_number !== '' && !empty($current_serial_number)) {
                        $sql_currentRow = "SELECT * FROM item WHERE id=? AND stock_id=? AND shelf_id=? AND upc=? AND quantity=? AND manufacturer_id=? AND serial_number LIKE '%$current_serial_number%'";
                    } else {
                        $sql_currentRow = "SELECT * FROM item WHERE id=? AND stock_id=? AND shelf_id=? AND upc=? AND quantity=? AND manufacturer_id=? AND (serial_number IS NULL OR serial_number = '')";
                    }
                    $stmt_currentRow = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_currentRow, $sql_currentRow)) {
                        header("Location: ../".$redirect_url."&error=stockTableSQLConnectionCurrentRow");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_currentRow, "ssssss", $current_item_id, $stock_id, $current_shelf_id, $current_upc, $current_quantity, $current_manufacturer_id);
                        mysqli_stmt_execute($stmt_currentRow);
                        $result_currentRow = mysqli_stmt_get_result($stmt_currentRow);
                        $rowCount_currentRow = $result_currentRow->num_rows;
                        if ($rowCount_currentRow < 1) {
                            echo("no current row found");
                            // No Rows found
                            if ($current_serial_number !== '' && !empty($current_serial_number)) {
                                echo("<br>issue at line: ".__LINE__."<br>");
                                header("Location: ../".$redirect_url."&error=noMatchInItemTableWithSerial");
                                exit();
                            } else {
                                echo("<br>issue at line: ".__LINE__."<br>");
                                header("Location: ../".$redirect_url."&error=noMatchInItemTable");
                                exit();
                            }
                        } else {
                            // Rows Found

                            $row_currentRow = $result_currentRow->fetch_assoc();
                            $currentRowSerialFull = $row_currentRow['serial_number'];
                            $new_serial_number_specified = str_replace($current_serial_number, '', $currentRowSerialFull);

                            if ($new_serial_number_specified == '') {
                                $new_serial_number_specified = null;
                            }

                            // Checks if a NEW row exists, and selects it if it does - add new if not
                            $sql_newRow = "SELECT * FROM item WHERE stock_id=? AND shelf_id=? AND upc=? AND manufacturer_id=? AND serial_number='$current_serial_number' LIMIT 1";
                            $stmt_newRow = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_newRow, $sql_newRow)) {
                                header("Location: ../".$redirect_url."&error=stockTableSQLConnectionNewRow");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_newRow, "ssss", $stock_id, $new_shelf_id, $current_upc, $current_manufacturer_id);
                                mysqli_stmt_execute($stmt_newRow);
                                $result_newRow = mysqli_stmt_get_result($stmt_newRow);
                                $rowCount_newRow = $result_newRow->num_rows;
                                if ($rowCount_newRow < 1) {
                                    // No Rows found
                                    echo("no new row exists");

                                    // Add New Row

                                    $sql = "INSERT INTO item (stock_id, upc, quantity, cost, serial_number, comments, manufacturer_id, shelf_id) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        echo("<br>issue at line: ".__LINE__."<br>");
                                        header("Location: $redirect_url&error=itemTableSQLConnection");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt, "ssssssss", $row_currentRow['stock_id'], $row_currentRow['upc'], $move_quantity, $row_currentRow['cost'], $new_serial_number, $row_currentRow['comments'], $row_currentRow['manufacturer_id'], $new_shelf_id);
                                        mysqli_stmt_execute($stmt);
                                        $new_item_id = mysqli_insert_id($conn); // ID of the new row in the table.

                                        // REMOVE quantity from OLD row

                                        $current_new_quantity = (int)$row_currentRow['quantity'] - (int)$move_quantity;

                                        $sql = "UPDATE item SET quantity=?, serial_number='$new_serial_number_specified'
                                                WHERE id=?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            echo("<br>issue at line: ".__LINE__."<br>");
                                            header("Location: $redirect_url&error=itemTableSQLConnectionUpdateCurrent");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "is", $current_new_quantity, $current_item_id);
                                            echo($current_new_quantity.'<br>'.$current_item_id.'<br>'.$move_quantity.'<br>'.$new_serial_number_specified);
                                            mysqli_stmt_execute($stmt);

                                            // Transaction update - old row
                                            $neg_move_quantity = -1*(int)$move_quantity;
                                            updateTransactions($stock_id, $current_item_id, 'move', $neg_move_quantity, $current_shelf_id, $current_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                                            // Transaction update - new row
                                            updateTransactions($stock_id, $new_item_id, 'move', $move_quantity, $new_shelf_id, $new_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 

                                            // check and delete old row if the quantity is now 0
                                            checkDeleteCurrentRow($current_item_id);


                                                            // send email - testing 
                                                            send_email($to, $toName, $fromName, ucwords($current_system_name).' - Stock Moved', createEmail("<p>Item ID: $stock_id stock moved - $move_quantity moved from </p>"));


                                            header("Location: $redirect_url&success=stockMoved&edited=$new_item_id"); // Final redirect - for success and stock is moved.
                                            exit();
                                        }
                                    }
                                } else {
                                    // New Row Found
                                    echo("new row found");

                                    $row_newRow = $result_newRow->fetch_assoc();

                                    $new_item_id = $row_newRow['id'];
                                    $new_quantity = $row_newRow['quantity'];
                                    $new_serial_number = $row_newRow['serial_number'];
                                    
                                    // REMOVE quantity from OLD row

                                    $current_new_quantity = (int)$current_quantity - (int)$move_quantity;
                                    echo ("<br> new quantity = $current_new_quantity <br>");

                                    if ($current_serial_number !== '' && !empty($current_serial_number)) {
                                        $sql = "UPDATE item SET quantity=?, serial_number='$new_serial_number_specified'
                                            WHERE id=?";
                                    } else {
                                        $sql = "UPDATE item SET quantity=?
                                            WHERE id=?";
                                    }
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        echo("<br>issue at line: ".__LINE__."<br>");
                                        header("Location: ../".$redirect_url."&error=itemTableSQLConnectionUpdateCurrent");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt, "ss", $current_new_quantity, $current_item_id);
                                        mysqli_stmt_execute($stmt);

                                        
                                        

                                        // Check if the old row is now 0, and remove it if it is
                                        checkDeleteCurrentRow($current_item_id);




                                        // ADD quantity to NEW row

                                        $new_new_quantity = (int)$new_quantity + (int)$move_quantity;
                                        echo ("<br> new new quantity = $new_new_quantity <br>");

                                        $sql = "UPDATE item SET quantity=?
                                                WHERE id=?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            echo("<br>issue at line: ".__LINE__."<br>");
                                            header("Location: ../".$redirect_url."&error=itemTableSQLConnectionUpdateCurrent");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "ss", $new_new_quantity, $new_item_id);
                                            mysqli_stmt_execute($stmt);

                                            // Transaction update - old row
                                            $neg_move_quantity = -1*(int)$move_quantity;
                                            updateTransactions($stock_id, $current_item_id, 'move', $neg_move_quantity, $current_shelf_id, $current_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                                            // Transaction update - new row
                                            updateTransactions($stock_id, $new_item_id, 'move', $move_quantity, $new_shelf_id, $new_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 


                                                            // send email - testing 
                                                            send_email($to, $toName, $fromName, ucwords($current_system_name).' - Stock Moved', createEmail("<p>Item ID: $stock_id stock moved - quantity: $move_quantity.</p>"));


                                            header("Location: $redirect_url&success=stockMoved&edited=$new_item_id"); // Final redirect - for success and stock is moved.
                                            exit();
                                        }
                                    }                        
                                }
                            }
                        }
                    }
                } else { // else for the username checker at top of page.
                    header("Location: $redirect_url&error=unauthorised");
                    exit();
                }
            } else { // else for the submit button checker at top of page.
                header("Location: $redirect_url&error=noSubmit&line=".__LINE__);
                exit();
            }
        } else {
            header("Location: ".$redirect_url.$query_char."error=unknownQuery");
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
            session_start();
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
                                $sql_delete_item = "DELETE FROM item WHERE stock_id=?";
                                $stmt_delete_item = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_item, $sql_delete_item)) {
                                    $errors[] = 'delete item table error - SQL connection';
                                    header("Location: $redirect_url&error=itemTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_item, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_item);
                                    $rows_delete_item = $conn->affected_rows;
                                    if ($rows_delete_item > 0) {
                                        // echo("<br>Item(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_item<br>");
                                    } else {
                                        // There wont always be items related to the stock object, ignore the error

                                        // echo("<br>No Items Deleted for stock_id: $stock_id... <br>");
                                        // header("Location: $redirect_url&error=deleteItemTable-NoRowsDeleted");
                                        // exit();
                                    }
                                    
                                }

                                // CLEAR STOCK_IMG TABEL
                                $sql_delete_stock_img = "DELETE FROM stock_img WHERE stock_id=?";
                                $stmt_delete_stock_img = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_stock_img, $sql_delete_stock_img)) {
                                    $errors[] = 'delete stock_img table error - SQL connection';
                                    header("Location: $redirect_url&error=stock_imgTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_stock_img, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_stock_img);
                                    $rows_delete_stock_img = $conn->affected_rows;
                                    if ($rows_delete_stock_img > 0) {
                                        // echo("<br>stock_img(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_stock_img<br>");
                                    } else {
                                        // There wont always be images linked, so ignore this 

                                        // echo("<br>No stock_imgs Deleted for stock_id: $stock_id... <br>");
                                        // header("Location: $redirect_url&error=deletestock_imgTable-NoRowsDeleted");
                                        // exit();
                                    }
                                    
                                }

                                // CLEAR STOCK_LABEL TABEL
                                $sql_delete_stock_label = "DELETE FROM stock_label WHERE stock_id=?";
                                $stmt_delete_stock_label = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_delete_stock_label, $sql_delete_stock_label)) {
                                    $errors[] = 'delete stock_label table error - SQL connection';
                                    header("Location: $redirect_url&error=stock_labelTableSQLConnection");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_delete_stock_label, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_delete_stock_label);
                                    $rows_delete_stock_label = $conn->affected_rows;
                                    if ($rows_delete_stock_label > 0) {
                                        // echo("<br>stock_label(s) Deleted for stock_id: $stock_id , Row count: $rows_delete_stock_label<br>");
                                    } else {
                                        // There wont always be labels to delete, so ignore for now.

                                        // echo("<br>No stock_labels Deleted for stock_id: $stock_id... <br>");
                                        // header("Location: $redirect_url&error=deleteStock_labelTable-NoRowsDeleted");
                                        // exit();
                                    }
                                    
                                }

                                // CLEAR STOCK TABLE
                                $sql_delete_stock = "DELETE FROM stock WHERE id=?";
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