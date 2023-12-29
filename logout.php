<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// LOGOUT FROM THE USER - UNSET AND DESTROY THE SESSION - NAVIGATE TO LOGIN PAGE
include 'includes/session.inc.php';
function getIPInfo() {  // returns array of [ip, type, convert] 
    global $_SERVER;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  
        $ip = $_SERVER['HTTP_CLIENT_IP'];  
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    } else {  
        $ip = $_SERVER['REMOTE_ADDR']; 
    }  
    if (str_contains($ip, ',')) {
        $ip = strtok($ip, ',');
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $type = 'ipv4';
        $convert = 'INET_ATON(?)';
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $type = 'ipv6';
        $convert = 'INET6_ATON(?)';
    } 
    $return = array('ip' => $ip, 'type' => $type, 'convert' => $convert);

    return $return;  
} 

function updateLoginLog($type, $auth) {
    global $_SESSION;

    $ip_info = getIPInfo();
    $ip = $ip_info['ip'];
    $ipfield = $ip_info['type'];
    $ipconvert = $ip_info['convert'];
    $username = addslashes(trim($_SESSION["username"]));

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $uid_type = "email";
    } else {
        $uid_type = "username";
    }
    
    include 'includes/dbh.inc.php';

    $sql_users = "SELECT id FROM users WHERE $uid_type=? AND auth=?";
    $stmt_users = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
        echo("Error getting users");
    } else {
        mysqli_stmt_bind_param($stmt_users, "ss", $username, $auth);
        mysqli_stmt_execute($stmt_users);
        $result_users = mysqli_stmt_get_result($stmt_users);
        $rowCount_users = $result_users->num_rows;
        if ($rowCount_users !== 1) {
            $insert_field_extra = "";
            $insert_value_extra = "";
        } elseif ($rowCount_users == 1) {
            $row_users = $result_users->fetch_assoc();
            $user_id = $row_users['id'];
            $insert_field_extra = ", user_id";
            $insert_value_extra = ", $user_id";
        }
    }

    $sql_insert = "INSERT INTO login_log (type, username, auth, timestamp, $ipfield$insert_field_extra) VALUES (?,?,?,CURRENT_TIMESTAMP,$ipconvert$insert_value_extra)";
    $stmt_insert = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
        echo("Error getting users");
    } else {
        mysqli_stmt_bind_param($stmt_insert, "ssss", $type, $username, $auth, $ip);
        mysqli_stmt_execute($stmt_insert);
        $log_id = mysqli_insert_id($conn);
    } 
    return $log_id;
}

session_start();

updateLoginLog('logout', $_SESSION['auth']);

sessionLogout(); // session.inc.php

session_unset();
session_destroy();
header("Location: ./login.php");
exit();
?>