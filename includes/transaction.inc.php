<?php
if (isset($_GET['stock_id'])) {
    if (is_numeric($_GET['stock_id'])) {
        if ($_GET['stock_id'] !== '') {
            $stock_id = $_GET['stock_id'];
            $currency_symbol = $config_currency;
            
            include 'dbh.inc.php';

            // Pagination variables
            $results_per_page = 5;
            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
            $start_index = ($current_page - 1) * $results_per_page;

            // $sql_tran = "SELECT t.id AS t_id, t.stock_id AS t_stock_id, t.item_id AS t_item_id, t.type AS t_type, t.quantity AS t_quantity,
            //                 t.price AS t_price, t.serial_number AS t_serial_number, t.reason AS t_reason, t.comments AS t_comments,
            //                 t.date AS t_date, t.time AS t_time, t.username as t_username, t.shelf_id AS t_shelf_id,
            //                 s.name AS s_name, a.name AS a_name
            //             FROM transaction AS t
            //             LEFT JOIN shelf AS s ON t.shelf_id=s.id
            //             LEFT JOIN area AS a ON s.area_id=a.id
            //             WHERE t.stock_id=?
            //             ORDER BY t.id DESC
            //             LIMIT ?, ?";

            // Grouping
            $sql_tran = "SELECT
                            t.stock_id AS t_stock_id,
                            t.type AS t_type,
                            SUM(t.quantity) AS t_quantity,
                            t.price AS t_price,
                            t.serial_number AS t_serial_number,
                            t.reason AS t_reason,
                            t.comments AS t_comments,
                            t.date AS t_date,
                            t.time AS t_time,
                            t.username AS t_username,
                            t.shelf_id AS t_shelf_id,
                            s.name AS s_name,
                            a.name AS a_name
                        FROM transaction AS t
                        LEFT JOIN shelf AS s ON t.shelf_id = s.id
                        LEFT JOIN area AS a ON s.area_id = a.id
                        WHERE t.stock_id = ?
                        GROUP BY
                            t.stock_id,
                            t.type,
                            t.price,
                            t.serial_number,
                            t.reason,
                            t.comments,
                            t.date,
                            t.time,
                            t.username,
                            t.shelf_id,
                            s.name,
                            a.name
                        ORDER BY t.date desc, t.time desc
                        LIMIT ?, ?";
            
            $stmt_tran = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_tran, $sql_tran)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_bind_param($stmt_tran, "sii", $stock_id, $start_index, $results_per_page);
                mysqli_stmt_execute($stmt_tran);
                $result_tran = mysqli_stmt_get_result($stmt_tran);
                $rowCount_tran = $result_tran->num_rows;
                if ($rowCount_tran < 1) {
                    echo ("No Transactions.");
                } else {
                    echo('
                    <table class="table table-dark theme-table centertable" id="transactions">
                        <thead>
                            <tr class="theme-tableOuter">
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
                                <th>Price</th>
                                <th>Serial Number</th>
                                <th hidden>Comments</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
        
                    ');

                    while ($row_tran = $result_tran->fetch_assoc()) {
                        $t_id = isset($row_tran['t_id']) ? $row_tran['t_id'] : '';
                        $t_stock_id = $row_tran['t_stock_id'];
                        $t_item_id = isset($row_tran['t_item_id']) ? $row_tran['t_item_id'] : '' ;
                        $t_type = $row_tran['t_type'];
                        $t_shelf_id = $row_tran['t_shelf_id'];
                        $t_quantity = $row_tran['t_quantity'];
                        $t_price = $row_tran['t_price'];
                        if ($t_price == '' || $t_price == null) { $t_price = 0;}
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
                                $t_type_color = 'transactionAdd';
                                break;
                            case 'remove':
                                $t_type_color = 'transactionRemove';
                                break;
                            case 'delete':
                                $t_type_color = 'transactionDelete';
                                break;
                            case 'move':
                                $t_type_color = 'transactionMove';
                                break;
                            default:
                                $t_type_color = '';
                        }
                        echo('
                            <tr class="' . $t_type_color . '">
                                <td id="t_id" hidden>' . $t_id . '</td>
                                <td hidden>' . $t_stock_id . '</td>
                                <td hidden>' . $t_item_id . '</td>
                                <td id="t_type">' . ucwords($t_type) . '</td>
                                <td id="t_date">' . $t_date . '</td>
                                <td id="t_time">' . $t_time . '</td>
                                <td hidden>' . $s_name . '</td>
                                <td id="a_name">' . $a_name . '</td>
                                <td id="t_username">' . $t_username . '</td>
                                <td id="t_quantity">' . $t_quantity . '</td>
                                <td>' . $currency_symbol . $t_price . '</td>
                                <td>' . $t_serial_number . '</td>
                                <td hidden>' . $t_comments . '</td>
                                <td id="t_reason">' . $t_reason . '</td>
                            </tr>
                        ');
                    }
                    
                    echo('</tbody>
                    </table>');

                    // Pagination links
                    echo('<div class="container" style="text-align: center;">');
                    $sql_count = "SELECT
                                    t.stock_id AS t_stock_id,
                                    t.type AS t_type,
                                    SUM(t.quantity) AS t_quantity,
                                    t.price AS t_price,
                                    t.serial_number AS t_serial_number,
                                    t.reason AS t_reason,
                                    t.comments AS t_comments,
                                    t.date AS t_date,
                                    t.time AS t_time,
                                    t.username AS t_username,
                                    t.shelf_id AS t_shelf_id,
                                    s.name AS s_name,
                                    a.name AS a_name
                                FROM transaction AS t
                                LEFT JOIN shelf AS s ON t.shelf_id = s.id
                                LEFT JOIN area AS a ON s.area_id = a.id
                                WHERE t.stock_id = ?
                                GROUP BY
                                    t.stock_id,
                                    t.type,
                                    t.price,
                                    t.serial_number,
                                    t.reason,
                                    t.comments,
                                    t.date,
                                    t.time,
                                    t.username,
                                    t.shelf_id,
                                    s.name,
                                    a.name
                                ORDER BY t.date desc, t.time desc";
                    $stmt_count = mysqli_stmt_init($conn);
                    if (mysqli_stmt_prepare($stmt_count, $sql_count)) {
                        mysqli_stmt_bind_param($stmt_count, "s", $stock_id);
                        mysqli_stmt_execute($stmt_count);
                        $result_count = mysqli_stmt_get_result($stmt_count);
                        $row_count = $result_count->num_rows;
                        $total_pages = ceil($row_count / $results_per_page);
                        
                        if ( $total_pages > 1){
                            if ($current_page > 1) {
                                echo('<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'#transactions\')"><</or>');
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo('<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>');
                                    // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                } else {
                                    echo('<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'#transactions\')">'.$i.'</or>');
                                }
                            }

                            if ($current_page < $total_pages) {
                                echo('<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'#transactions\')">></or>');
                            }  
                            echo('&nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage(\'transactions.php?stock_id='.$stock_id.'\ \')">view all</or>');
                        }
                    }
                    echo('</div>');
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
