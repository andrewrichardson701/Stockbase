<?php
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at >// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public L>// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USED FOR UPDATING A USER'S FAVOURITES

// USED BY: stock.php for favourite adding

// print_r($_POST);
//         exit();

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function getItemStockInfo($stock_id) {                                                                                                                                                                               global $redirect_url, $queryChar;

    include 'dbh.inc.php';

    $sql = "SELECT * FROM stock WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return '';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $stock_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return '';
        } elseif ($rowCount > 1) {
            return '';
        } else {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
}
function checkFavouriteStatus($stock_id, $user_id) {
    include 'dbh.inc.php';

    $sql = "SELECT * FROM favourites WHERE stock_id=? AND user_id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return '';
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $stock_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            return 'add';
        } else {
            return 'remove';
        }
    }
}
function addFavourite($stock_id, $user_id) {
    include 'dbh.inc.php';

    $sql = "INSERT INTO favourites (stock_id, user_id)
            VALUES (?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 'SQL error';
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $stock_id, $user_id);
        mysqli_stmt_execute($stmt);
        $favourite_id = mysqli_insert_id($conn);
        return $favourite_id;
    }
}
function removeFavourite($stock_id, $user_id) {
    include 'dbh.inc.php';

    $sql = "DELETE FROM favourites WHERE stock_id=? AND user_id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 'SQL error';
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $stock_id, $user_id);
        mysqli_stmt_execute($stmt);
        return 1;
    }
}

$return = [];
if (isset($_POST['stock_id'])) {
    if (isset($_SESSION['user_id'])) {
        $stock_id = $_POST['stock_id'];
	$user_id = $_SESSION['user_id'];
        if (getItemStockInfo($stock_id) != '') { // check if the item exists in the stock list - doesnt matter if it is deleted or not, we will allow favourites on them.
            $type = checkFavouriteStatus($stock_id, $user_id);
	    $return['type'] = $type;
	    if ($type == "add") {
		// add the favourite
		$fav_result = addFavourite($stock_id, $user_id);
	    } else {
		// remove the favourite
		$fav_result = removeFavourite($stock_id, $user_id);
            }

            if (is_numeric((int)$fav_result)) {
                $return['id'] = $fav_result;
                $return['status'] = 'true';
            } else {
                $return['id'] = '';
                $return['status'] = 'false';
            }
            // do the logic - check if the favourite is there or not and remove/add, also set the $return['type'] and $return['status'] to be correct.
        }
	echo(json_encode($return));
    } else {
    }
} else {
}

