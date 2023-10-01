<?php



// NEED TO CHECK IF A USER IS LOGGED IN 
// CHECK IF SUBMIT IS PUSHED

// MAYBE MOVE THE SERIAL NUMBERS TO SEPERATE ROWS IN THE ADD PROCESS?


// IF ROW BECOMES 0 AFTER MOVE, DELETE THE ROW?
$stock_id = isset($_POST['current_stock']) ? $_POST['current_stock'] : '';
$redirect_url = "../stock.php?stock_id=$stock_id&modify=move";

if (isset($_POST['submit'])) {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    } 

    if ($_POST['submit'] == 'Move' && isset($_SESSION['username']) && $_SESSION['username'] != '' && $_SESSION['username'] != null) {
        // print_r($_POST);

        include 'smtp.inc.php';
        
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
                header("Location: ".$redirect_url."&error=transactionConnectionSQL");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_trans, "sssssssssss", $stock_id, $item_id, $type, $shelf_id, $quantity, $cost, $serial_number, $reason, $date, $time, $username);
                mysqli_stmt_execute($stmt_trans);
                echo ("transaction added");
            }  
        } 

        // function to check the current row and if 0 quantity, remove it.
        function checkDeleteCurrentRow($item_id) {
            global $redirect_url;
            include 'dbh.inc.php';
        
            $sql = "SELECT * FROM item WHERE id=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ".$redirect_url."&error=itemTableSQLConnectionCurrentRowCheck");
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
                        }
                    } else {
                        // no need to do anything, there is still quantity.
                    }
                }
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
            header("Location: ".$redirect_url."&error=stockTableSQLConnectionCurrentRow");
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
                    header("Location: ".$redirect_url."&error=noMatchInItemTableWithSerial");
                    exit();
                } else {
                    echo("<br>issue at line: ".__LINE__."<br>");
                    header("Location: ".$redirect_url."&error=noMatchInItemTable");
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
                    header("Location: ".$redirect_url."&error=stockTableSQLConnectionNewRow");
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
                            header("Location: ".$redirect_url."&error=itemTableSQLConnectionUpdateCurrent");
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
                                header("Location: ".$redirect_url."&error=itemTableSQLConnectionUpdateCurrent");
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
    header("Location: $redirect_url&error=noSubmit");
    exit();
}





?>