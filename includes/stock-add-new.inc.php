<?php
// print_r($_POST);
// print_r($_FILES);
// exit();

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

if (isset($_POST['submit'])) {
    session_start();
    include 'smtp.inc.php';

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
        $quantity = $_POST['quantity']; 
        $serial_number = $_POST['serial-number']; 
        $reason = $_POST['reason'];

        $username = $_SESSION['username'];
        $redirect_url = $_SESSION['redirect_url'];

        $redirect_queries = "&manufacturer=$manufacturer&site=$site&area=$area&shelf=$shelf&quantity=$quantity&serial-number=$serial_number&reason=$reason";
        
        if (!isset($_POST['shelf']) || $_POST['shelf'] == '' || $_POST['shelf'] == 0 || $_POST['shelf'] == '0') {
            header("Location: ../$redirect_url.$redirect_queries&error=shelfRequired");
            exit();
        }



        include 'dbh.inc.php';
        if (!isset($_POST['id']) || $_POST['id'] == 0 || $_POST['id'] == '0') {
            // adding new stock
            $name = $_POST['name'];
            $sku = $_POST['sku'];
            $description = $_POST['description'];
            $min_stock = $_POST['min-stock'] == '' ? 0 : $_POST['min-stock'];
            $labels = $_POST['labels'];
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
                header("Location: ../$redirect_url.$redirect_queries&error=stockTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt_sku);
                $result_sku = mysqli_stmt_get_result($stmt_sku);
                $rowCount_sku = $result_sku->num_rows;
                if ($rowCount_sku < 1) {
                    // header("Location: ".$redirect_url.$reditect_queies."&error=noSkusInTable");
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
                header("Location: ../$redirect_url.$redirect_queries&error=stockTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt_d_config);
                $result_d_config = mysqli_stmt_get_result($stmt_d_config);
                $rowCount_d_config = $result_d_config->num_rows;
                if ($rowCount_d_config < 1) {
                    // header("Location: ".$redirect_url.$reditect_queies."&error=noSkusInTable");
                    // exit();
                } else {
                    while ($row_d_config = $result_d_config->fetch_assoc() ){
                        $config_d_sku_prefix = isset($row_d_config['sku_prefix']) ? $row_d_config['sku_prefix'] : 'ITEM-';
                    }
                }
            }

            $sql_config = "SELECT sku_prefix FROM config WHERE id=1";
            $stmt_config = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
                header("Location: ../$redirect_url.$redirect_queries&error=stockTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_execute($stmt_config);
                $result_config = mysqli_stmt_get_result($stmt_config);
                $rowCount_config = $result_config->num_rows;
                if ($rowCount_config < 1) {
                    // header("Location: ".$redirect_url.$reditect_queies."&error=noSkusInTable");
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
            if ($sku !== '') {
                // SKU is not blank
                if (in_array($sku, $skus)) {
                    // SKU already exists
                    header("Location: ../$redirect_url.$redirect_queries&error=SKUexists");
                    exit();
                }
            } else {
                usort($PRE_skus, function($a, $b) { // sort the array 
                    return strnatcmp($a, $b);
                });
                $new_PRE_sku_number = ((int)substr(end($PRE_skus), 3) +1);
                $new_PRE_skus = $current_sku_prefix . str_pad($new_PRE_sku_number, 5, '0', STR_PAD_LEFT);
                $sku = $new_PRE_skus;
            }
            
            // echo("To be added:<br>name = $name<br>description = $description<br>sku = $sku<br>min_stock = $min_stock");

            // ADD STOCK to stock table
            $sql = "INSERT INTO stock (name, description, sku, min_stock) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ".$redirect_url.$reditect_queies."&error=stockTableSQLConnection");
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
                            header("Location: ".$redirect_url.$reditect_queies."&error=stockTableSQLConnection");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "ss", $id, $label);
                            mysqli_stmt_execute($stmt);
                        }
                    }
                } else {
                    $sql = "INSERT INTO stock_label (stock_id, label_id) VALUES (?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ".$redirect_url.$reditect_queies."&error=stockTableSQLConnection");
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
            header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnection");
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
                    header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnection");
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
                        header("Location: ".$redirect_url.$reditect_queies."&error=transactionConnectionSQL");
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
                    header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnection");
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
                        header("Location: ".$redirect_url.$reditect_queies."&error=transactionConnectionSQL");
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
                header("Location: ".$redirect_url.$reditect_queies."&error=multipleItemsFound");
                exit();
            }
        }
        







        // ADD STOCK to item table
            // $sql = "INSERT INTO stock ('name', 'description', 'sku', 'min_stock') VALUES (?, ?, ?, ?)";
            // $stmt = mysqli_stmt_init($conn);
            // if (!mysqli_stmt_prepare($stmt, $sql)) {
            //     header("Location: ../admin.php?sqlerror=config_noUpdate#global-settings");
            //     exit();
            // } else {
            //     mysqli_stmt_bind_param($stmt, "ssss", $name, $description, $sku, $min_stock);
            //     mysqli_stmt_execute($stmt);
            //     $insert_id = mysqli_insert_id($conn); // ID of the new row in the table.
            //     header("Location: ../admin.php?restore=globalSuccess#global-settings");
            //     exit();
            // }





    } else {
        header("Location: ".$_SESSION['redirect_url']."&error=addStock");
        exit();
    }
} else {
    header("Location: ".$_SESSION['redirect_url']."&error=noSubmit");
    exit();
}




?>