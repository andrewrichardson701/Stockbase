<?php
//
// STILL  NEED TO WORK THE MINIMUM STOCK INTO THE CHECKER FOR REMOVING STOCK
//
include 'smtp.inc.php';
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
                            header("Location: ./?success=stockRemoved&stock_id=$stock_id");
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
}






if (isset($_POST['submit'])) {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $redirect_url = "../stock.php?modify=remove&stock_id=".$_POST['stock_id'];
    if ($_POST['submit'] == 'Remove Stock' && isset($_SESSION['username']) && $_SESSION['username'] != '' && $_SESSION['username'] != null) {
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
        function checkDeleteCurrentRow($item_id) {
            global $redirect_url;
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






}



if (isset($errors)) {
    echo("Errors:<br>");
    for ($e=0; $e<count($errors); $e++) {
        echo("&nbsp;".$e+1 .") ".$errors[$e]."<br>");
    }
}