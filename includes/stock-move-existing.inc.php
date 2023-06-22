<?php



// NEED TO DO THE LOGIC FOR SERIAL NUMBERS - 
// IF SERIAL NUMBER PRESENT, REMOVE SERIAL FROM CURRENT ROW
// ADD SERIAL TO NEW ROW (ADD NEW ROW BECAUSE ROW UNLIKELY TO EXIST)

// IF ROW BECOMES 0 AFTER MOVE, DELETE THE ROW?

// BASIC MOVING OF ITEMS WORKS SO FAR. JUST LOGIC FOR THE SERIALS LEFT



print_r($_POST);
session_start();

$current_date = date('Y-m-d'); // current date in YYY-MM-DD format
$current_time = date('H:i:s'); // current time in HH:MM:SS format

$stock_id = $_POST['current_stock'];

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

$redirect_url = "../stock.php?stock_id=$stock_id&modify=move";

$errors = 0;

// Transaction updates
function updateTransactions($stock_id, $item_id, $type, $quantity, $serial_number, $reason, $date, $time, $username) {
    include 'dbh.inc.php';
    $cost = 0;
    $sql_trans = "INSERT INTO transaction (stock_id, item_id, type, quantity, price, serial_number, reason,  date, time, username) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_trans = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
        header("Location: ".$redirect_url.$reditect_queies."&error=transactionConnectionSQL");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt_trans, "ssssssssss", $stock_id, $item_id, $type, $quantity, $cost, $serial_number, $reason, $date, $time, $username);
        mysqli_stmt_execute($stmt_trans);
        echo ("transaction added");
    }  
} 

// Check if the values all match up with current DB.

include 'dbh.inc.php';
if ($current_serial_number !== '' && !empty($current_serial_number)) {
    $sql_currentRow = "SELECT * FROM item WHERE id=? AND stock_id=? AND shelf_id=? AND upc=? AND quantity=? AND manufacturer_id=? AND serial_number LIKE '%$current_serial_number%'";
} else {
    $sql_currentRow = "SELECT * FROM item WHERE id=? AND stock_id=? AND shelf_id=? AND upc=? AND quantity=? AND manufacturer_id=?";
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
            // header("Location: ".$redirect_url."&error=noMatchInItemTableWithSerial");
            exit();
        } else {
            echo("<br>issue at line: ".__LINE__."<br>");
            // header("Location: ".$redirect_url."&error=noMatchInItemTable");
            exit();
        }
    } else {
        // Rows Found

        // Checks if a NEW row exists, and selects it if it does - add new if not

        $sql_newRow = "SELECT * FROM item WHERE stock_id=? AND shelf_id=? AND upc=? AND manufacturer_id=? AND (serial_number IS NULL OR serial_number = '') LIMIT 1";
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
                $errors = $errors+1;

                // Add New Row
                
                $row_currentRow = $result_currentRow->fetch_assoc();

                $sql = "INSERT INTO item (stock_id, upc, quantity, cost, serial_number, comments, manufacturer_id, shelf_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $errors = $errors+1;
                    echo("<br>issue at line: ".__LINE__."<br>");
                    header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnection");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ssssssss", $row_currentRow['stock_id'], $row_currentRow['upc'], $move_quantity, $row_currentRow['cost'], $new_serial_number, $row_currentRow['comments'], $row_currentRow['manufacturer_id'], $new_shelf_id);
                    mysqli_stmt_execute($stmt);
                    $item_id = mysqli_insert_id($conn); // ID of the new row in the table.

                    // Transaction update
                    updateTransactions($stock_id, $new_item_id, 'add', $move_quantity, $new_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                    
                    // REMOVE quantity from OLD row

                    $new_quantity = (int)$result_currnetRow['quantity'] - (int)$move_quantity;

                    $sql = "UPDATE item SET quantity=?
                            WHERE id=?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $errors = $errors+1;
                        echo("<br>issue at line: ".__LINE__."<br>");
                        header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnectionUpdateCurrent");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "ss", $new_quantity, $current_item_id);
                        mysqli_stmt_execute($stmt);

                        // Transaction update
                        $neg_move_quantity = -1*(int)$move_quantity;
                        updateTransactions($stock_id, $current_item_id, 'move', $neg_move_quantity, $serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                        $errors = $errors-1;
                    }
                }
                // header("Location: ".$redirect_url."&error=noMatchInItemTableNew");
                exit();
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

                $sql = "UPDATE item SET quantity=?
                        WHERE id=?";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $errors = $errors+1;
                    echo("<br>issue at line: ".__LINE__."<br>");
                    header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnectionUpdateCurrent");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $current_new_quantity, $current_item_id);
                    mysqli_stmt_execute($stmt);

                    // Transaction update
                    $neg_move_quantity = -1*(int)$move_quantity;
                    updateTransactions($stock_id, $current_item_id, 'move', $neg_move_quantity, $current_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                }

                // ADD quantity to NEW row

                $new_new_quantity = (int)$new_quantity + (int)$move_quantity;
                echo ("<br> new new quantity = $new_new_quantity <br>");

                $sql = "UPDATE item SET quantity=?
                        WHERE id=?";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo("<br>issue at line: ".__LINE__."<br>");
                    header("Location: ".$redirect_url.$reditect_queies."&error=itemTableSQLConnectionUpdateCurrent");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $new_new_quantity, $new_item_id);
                    mysqli_stmt_execute($stmt);

                    // Transaction update
                    updateTransactions($stock_id, $new_item_id, 'move', $move_quantity, $new_serial_number, 'Move Stock', $current_date, $current_time, $_SESSION['username']); 
                }
                
                
                // CHECK ONENOTE FOR NEXT STEPS

            }
        }
    }
}


// Check if the item / site / area / shelf combination matches




?>