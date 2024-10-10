<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// Logic for saving the audit information in audit.php

$redirect_url = 'audit.php';

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

if (isset($_POST['redirect_url'])) {
    $redirect_url = $_POST['redirect_url'];
}

if (str_contains($redirect_url, '&')) {
    $queryChar = '&';
} else {
    $queryChar = '?';
}

// csrf_token management
if (isset($_POST['csrf_token'])) {
    if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
        return 'error=csrfMissmatch';
    }
} else {
    return 'error=csrfMissmatch';
}

$comment = $_POST['comment'];
$user_id = $_POST['user_id'];
$checked = $_POST['checked'];
$stock_id = $_POST['stock_id'];

// foreach ($_POST as $var) {
//     $result[] = $var;
// }

function getAuditRow($input_stock_id, $type) {
    $return = [];

    if ($type == "lt") {
        $q = "<";
    } elseif ($type == "lte") {
        $q = "<=";
    } elseif ($type == "gte") {
        $q = ">=";
    } elseif ($type == "gt") {
        $q = ">";
    } else {
        $q = "<";
    }

    include 'dbh.inc.php';
    $sql = "SELECT id, stock_id, user_id, date, comment
            FROM stock_audit
            WHERE stock_id = '$input_stock_id' AND date $q DATE_SUB(NOW(), INTERVAL 6 MONTH) ORDER BY id DESC";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // fails to connect
        $return['error'] = "Connection Error";
        $return['empty'] = 0;
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            $return['empty'] = 1;
        } else {
            // rows found
            while ($row = $result->fetch_assoc()) {
                $return['empty'] = 0;
                $id = $row['id'];
                $stock_id = $row['stock_id'];
                $user_id = $row['user_id'];
                $date = $row['date'];
                $comment = $row['comment'];
                $return[] = array('id' => $id, 'stock_id' => $stock_id, 'user_id' => $user_id, 'date' => $date, 'comment' => $comment);
            }
        }
    }
    return $return;
}

include 'dbh.inc.php';
$date = date('Y-m-d');

if ($checked == "true") {
    // add to table with current date
    // check if a row exists (date greater than or equal to current-6m)
    $auditCheck = getAuditRow($stock_id, "gte");

    if (!array_key_exists('error', $auditCheck)) {

        if ($auditCheck['empty'] !== 1) {
            // Row exists already - update
            $matchID = $auditCheck[0]['id'];

            $sql_update = "UPDATE stock_audit SET date=?, comment=?, user_id=? WHERE id='$matchID';";
            $stmt_update = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                echo ('ISSUE AT LINE: '.__LINE__.'<br>');
            } else {
                mysqli_stmt_bind_param($stmt_update, "sss", $date, $comment, $user_id);
                mysqli_stmt_execute($stmt_update);
                $result['success'] = "Updated";
                // update changelog
                //addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "stock_audit", $matchID, "date", $auditCheck[0]['date'], $date);
            }
        } else {
            // Add Row
            $sql_insert = "INSERT INTO stock_audit (stock_id, user_id, date, comment) 
                                    VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
                echo ('ISSUE AT LINE: '.__LINE__.'<br>');
            } else {
                mysqli_stmt_bind_param($stmt_insert, "ssss", $stock_id, $user_id, $date, $comment);
                mysqli_stmt_execute($stmt_insert);
                // get new id
                $new_id = mysqli_insert_id($conn); // ID of the new row in the table.
                $result['success'] = "Added";
                // update changelog
                // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "shelf", $id, "name", null, $name);

            }
        }
    } else {
        $result['error'] = $auditCheck['error'];
    }
    
} elseif ($checked == "false") {
    // remove current row from table
    // this will be a matching row, where the ID matches, and the date is within the last 6 months.

    // check if a row exists (date greater than or equal to current-6m)
    $auditCheck = getAuditRow($stock_id, "gte");

    if (!array_key_exists('error', $auditCheck)) {

        if ($auditCheck['empty'] !== 1) {
            // Row exists already - update
            $matchID = $auditCheck[0]['id'];

            $sql_update = "DELETE FROM stock_audit WHERE id='$matchID';";
            $stmt_update = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                echo ('ISSUE AT LINE: '.__LINE__.'<br>');
            } else {
                mysqli_stmt_execute($stmt_update);
                $result['success'] = "Deleted";
                // update changelog
                //addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "stock_audit", $matchID, "date", $auditCheck[0]['date'], $date);
            }
        } else {
            $result['error'] = "no row found, nothing to do";
        }
    } else {
        $result['error'] = $auditCheck['error'];
    }
}
echo(json_encode($result));
// print_r('<pre>');
// print_r($ldap_userlist);
// print_r('</pre>');





?>