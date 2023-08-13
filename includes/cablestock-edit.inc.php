<?php
// SAVING INFO FOR THE CABLESTOCK PAGE. THIS IS FOR REMOVING AND ADDING STOCK.

// print_r($_POST);
session_start();
$redirect_url = $_SESSION['redirect_url'];
$queryChar = strpos($redirect_url, "?") !== false ? '&' : '?';

function updateCableTransactions($stock_id, $item_id, $type, $quantity, $reason, $date, $time, $username) {
    global $redirect_url, $queryChar;
    include 'dbh.inc.php';
    $cost = 0;
    $sql_trans = "INSERT INTO cable_transaction (stock_id, item_id, type, quantity, reason, date, time, username) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_trans = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_trans, $sql_trans)) {
        header("Location: ../".$redirect_url.$queryChar."error=cable_transactionConnectionSQL");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt_trans, "ssssssss", $stock_id, $item_id, $type, $quantity, $reason, $date, $time, $username);
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

function addQuantity($stock_id, $cable_item_id) {
    global $redirect_url, $queryChar, $_SESSION;

    include 'smtp.inc.php';

    $type = "add";
    $reason = "Added via Fixed Cable page";
    $date = date('Y-m-d'); // current date in YYY-MM-DD format
    $time = date('H:i:s'); // current time in HH:MM:SS format
    $username = $_SESSION['username'];

    $row = getCableItemRow($cable_item_id);
    $quantity = $row['quantity'];
    $new_quantity = $quantity +1;

    if ($cable_item_id == $row['id']) {
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
            
            updateCableTransactions($stock_id, $cable_item_id, $type, $new_quantity, $reason, $date, $time, $username);

            $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Added";
            $email_body = "<p>Fixed cable stock added, for item ID: <strong>$cable_item_id</strong>!</p>";
            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));

            header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&success=quantityAdded");
            exit();
        }  
    } else {
        header("Location: ../".$redirect_url.$queryChar."cableItemID=$cable_item_id&error=cable_item_id-missmatch");
        exit();
    }
}

function removeQuantity($stock_id, $cable_item_id) {
    global $redirect_url, $queryChar, $_SESSION;

    include 'smtp.inc.php';

    $type = "remove";
    $reason = "Removed via Fixed Cable page";
    $date = date('Y-m-d'); // current date in YYY-MM-DD format
    $time = date('H:i:s'); // current time in HH:MM:SS format
    $username = $_SESSION['username'];

    $row = getCableItemRow($cable_item_id);
    $quantity = $row['quantity'];
    if ($quantity > 0) {
        $new_quantity = $quantity -1;

        if ($cable_item_id == $row['id']) { 
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
                
                updateCableTransactions($stock_id, $cable_item_id, $type, $new_quantity, $reason, $date, $time, $username);

                $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Below Minimum Stock Count. Please Order More!";
                $email_body = "<p>Fixed cable stock below minimum stock count, for item ID: <strong>$cable_item_id</strong>. Please order more!</p>";
                send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));

                // Check if the quantity is below minimum
                $sql = "SELECT * FROM stock WHERE id=$stock_id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../".$redirect_url.$queryChar."stockId=$stock_id&error=stockTableSQLConnection");
                    exit();
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        header("Location: ../".$redirect_url.$queryChar."stockId=$stock_id&error=noRowsFound");
                        exit();
                    } elseif ($rowCount > 1) {
                        header("Location: ../".$redirect_url.$queryChar."stockId=$stock_id&error=tooManyRowsFound");
                        exit();
                    } else {
                        $row = $result->fetch_assoc();
                        $min_quantity = $row['min_stock'];

                        if ($quantity <= $min_quantity) {
                            $email_subject = ucwords($current_system_name)." - Fixed Cable Stock Below Minimum Stock Count. Please Order More!";
                            $email_body = "<p>Fixed cable stock below minimum stock count, for item ID: <strong>$cable_item_id</strong>. Please order more!</p>";
                   
                            send_email($loggedin_email, $loggedin_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body));
                        }
                    }
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
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if (isset($_POST['cable-item-id'])) {
            if ($_POST['cable-item-id'] !== '' && $_POST['cable-item-id'] !== 0) {
                $cable_item_id = $_POST['cable-item-id'];
                $stock_id = $_POST['stock-id'];

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
    } else { // action not set
        header("Location: ../".$redirect_url.$queryChar."error=noAction");
        exit();
    }
} else { // not POST
    header("Location: ../".$redirect_url.$queryChar."error=notPOST");
    exit();
}





?>