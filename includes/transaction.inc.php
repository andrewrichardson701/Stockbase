<?php
if (isset($_GET['stock_id'])) {
    if (is_numeric($_GET['stock_id'])) {
        if ($_GET['stock_id'] !== '') {
            $stock_id = $_GET['stock_id'];
            include 'dbh.inc.php';
            $sql_tran = "SELECT t.id AS t_id, t.stock_id AS t_stock_id, t.item_id AS t_item_id, t.type AS t_type, t.quantity AS t_quantity,
                            t.price AS t_price, t.serial_number AS t_serial_number, t.reason AS t_reason, t.comments AS t_comments,
                            t.date AS t_date, t.time AS t_time, t.username as t_username,
                            s.name AS s_name, a.name AS a_name
                        FROM transaction AS t
                        INNER JOIN item AS i ON t.item_id=i.id
                        INNER JOIN shelf AS s ON i.shelf_id=s.id
                        INNER JOIN area AS a ON s.area_id=a.id
                        WHERE t.stock_id=?
                        ORDER BY t_date DESC
                        LIMIT 5";
            $stmt_tran = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_tran, $sql_tran)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_bind_param($stmt_tran, "s", $stock_id);
                mysqli_stmt_execute($stmt_tran);
                $result_tran = mysqli_stmt_get_result($stmt_tran);
                $rowCount_tran = $result_tran->num_rows;
                if ($rowCount_tran < 1) {
                    echo ("No Transactions.");
                    exit();
                } else {
                    echo('
                    <table class="table table-dark centertable" id="transactions">
                        <thead>
                            <tr>
                                <th hidden>ID</th>
                                <th hidden>Stock ID</th>
                                <th hidden>Item ID</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th hidden>Shelf</th>
                                <th>Location</th>
                                <th>Username</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th hidden>Price</th>
                                <th hidden>Serial Number</th>
                                <th hidden>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
        
                    ');
                    while( $row_tran = $result_tran->fetch_assoc() ) {
                        $t_id = $row_tran['t_id'];
                        $t_stock_id = $row_tran['t_stock_id'];
                        $t_item_id = $row_tran['t_item_id'];
                        $t_type = $row_tran['t_type'];
                        $t_quantity = $row_tran['t_quantity'];
                        $t_price = $row_tran['t_price'];
                        $t_serial_number = $row_tran['t_serial_number'];
                        $t_reason = $row_tran['t_reason'];
                        $t_comments = $row_tran['t_comments'];
                        $t_date = $row_tran['t_date'];
                        $t_time = $row_tran['t_time'];
                        $t_username = $row_tran['t_username'];
                        $s_name = $row_tran['s_name'];
                        $a_name = $row_tran['a_name'];
                        switch ($t_type) {
                            case 'add':
                                $t_type_color = 'lime';
                                break;
                            case 'remove':
                                $t_type_color = 'red';
                                break;
                            case 'delete':
                                $t_type_color = 'brown';
                                break;
                            default:
                                $t_type_color = '';
                        }
                            echo('
                            <tr style="color:'.$t_type_color.'">
                                <td id="t_id" hidden>'.$t_id.'</td>
                                <td hidden>'.$t_stock_id.'</td>
                                <td hidden>'.$t_item_id.'</td>
                                <td id="t_type">'.$t_type.'</td>
                                <td id="t_date">'.$t_date.'</td>
                                <td id="t_time">'.$t_time.'</td>
                                <td hidden>'.$s_name.'</td>
                                <td id="a_name">'.$a_name.'</td>
                                <td id="t_username">'.$t_username.'</td>
                                <td id="t_quantity">'.$t_quantity.'</td>
                                <td id="t_reason">'.$t_reason.'</td>
                                <td hidden>'.$t_price.'</td>
                                <td hidden>'.$t_serial_number.'</td>
                                <td hidden>'.$t_comments.'</td>
                            </tr>
                            ');

                    }
                    echo('</tbody>
                    </table>');
                }
            }
        } else {
            echo ("error = id empty");
        }
    } else {
        echo ("error = non-numeric id");
    }
}






?>