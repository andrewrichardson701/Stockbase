<?php
// Include your database connection code here (e.g., dbh.inc.php)

// Step 1: Get all rows with quantity more than 1
include 'dbh.inc.php';
$sql = "SELECT * FROM item WHERE quantity > 1";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    // Handle the error if the SQL statement preparation fails
    echo "SQL statement preparation failed!";
} else {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $original_id = $row['id'];
        $original_quantity = $row['quantity'];
        $original_stock_id = $row['stock_id'];
        $original_upc = $row['upc'];
        $original_cost = $row['cost'];
        $original_serial_number = '';
        $original_comments = $row['comments'];
        $original_manufacturer_id = $row['manufacturer_id'];
        $original_shelf_id = $row['shelf_id'];
        

        // Step 2: Update the original row's quantity to 1
        $update_sql = "UPDATE item SET quantity = 1 WHERE id = ?";
        $update_stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($update_stmt, $update_sql)) {
            // Handle the error if the SQL statement preparation fails
            echo "Update SQL statement preparation failed!";
        } else {
            mysqli_stmt_bind_param($update_stmt, "i", $original_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }

        // Step 3: Insert (quantity - 1) new rows with quantity = 1
        for ($i = 1; $i < $original_quantity; $i++) {
            $insert_sql = "INSERT INTO item (quantity, stock_id, upc, cost, serial_number, comments, manufacturer_id, shelf_id, deleted) VALUES (1, ?, ?, ?, ?, ?, ?, ?, 0)";
            $insert_stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($insert_stmt, $insert_sql)) {
                // Handle the error if the SQL statement preparation fails
                echo "Insert SQL statement preparation failed!";
            } else {

                mysqli_stmt_bind_param($insert_stmt, "sssssss", $original_stock_id, $original_upc, $original_cost, $original_serial_number, $original_comments, $original_manufacturer_id, $original_shelf_id);
                mysqli_stmt_execute($insert_stmt);
                mysqli_stmt_close($insert_stmt);
            }
        }
    }

    mysqli_stmt_close($stmt);
}

// Close the database connection here
?>
