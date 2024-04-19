<?php 
// get IP Info of current browser 
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

// update the login_log table
function updateLoginLog($data, $type, $auth) {

    $ip_info = getIPInfo();
    $ip = $ip_info['ip'];
    $ipfield = $ip_info['type'];
    $ipconvert = $ip_info['convert'];
    $username = addslashes(trim($data["username"]));
    
    include 'dbh.inc.php';

    if (isset($data['user_id']) && is_numeric($data['user_id'])) {
        $user_id = $data['user_id'];
        $sql_insert = "INSERT INTO login_log (type, username, user_id, auth, timestamp, $ipfield) VALUES (?,?,$user_id,?,CURRENT_TIMESTAMP,$ipconvert)";
    } else {
        $sql_insert = "INSERT INTO login_log (type, username, auth, timestamp, $ipfield) VALUES (?,?,?,CURRENT_TIMESTAMP,$ipconvert)";
    }
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



// query the login_failure table for count and timeout to see if you are blocked.
function queryLoginBlocked($data, $auth) {

    $ip_info = getIPInfo();
    $ip = $ip_info['ip'];
    $ipfield = $ip_info['type'];
    $ipconvert = $ip_info['convert'];
    $username = addslashes(trim($data["username"]));

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $uid_type = "email";
    } else {
        $uid_type = "username";
    }

    include 'dbh.inc.php';

    $sql_failed = "SELECT id, username, auth, $ipfield, last_timestamp, count, CURRENT_TIMESTAMP
                    FROM login_failure
                    WHERE username=? 
                        AND auth=? 
                        AND $ipfield=$ipconvert";
    $stmt_failed = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_failed, $sql_failed)) {
        echo("Error getting users");
    } else {
        mysqli_stmt_bind_param($stmt_failed, "sss", $username, $auth, $ip);
        mysqli_stmt_execute($stmt_failed);
        $result_failed = mysqli_stmt_get_result($stmt_failed);
        $rowCount_failed = $result_failed->num_rows;
        if ($rowCount_failed > 0) {
            $row_failed = $result_failed->fetch_assoc();
            $r_last_timestamp = $row_failed['last_timestamp'];
            $r_current_timestamp = $row_failed['CURRENT_TIMESTAMP'];
            $r_count = $row_failed['count'];

            if ($r_count >= 5) {
                // if the time is within the retry period (600s (10mins)) - prompt for banned for 10.
                if (strtotime($r_current_timestamp) - strtotime($r_last_timestamp) < 600) {
                    $return = "blocked";
                } else {
                    $return = 'valid';
                }
            } else {
                $return = 'valid';
            }
        } else {
            // does not exist in table
            $return = 'valid';
        }
    }
    return $return;
}
// update the login_failure log
function insertLoginFail($data, $auth) {

    $ip_info = getIPInfo();
    $ip = $ip_info['ip'];
    $ipfield = $ip_info['type'];
    $ipconvert = $ip_info['convert'];
    $username = addslashes(trim($data["username"]));

    include 'dbh.inc.php';

    $sql_failed = "SELECT id, username, auth, $ipfield, last_timestamp, count, CURRENT_TIMESTAMP
                    FROM login_failure
                    WHERE username=? 
                        AND auth=? 
                        AND $ipfield=$ipconvert";
    $stmt_failed = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_failed, $sql_failed)) {
        echo("Error getting users");
    } else {
        mysqli_stmt_bind_param($stmt_failed, "sss", $username, $auth, $ip);
        mysqli_stmt_execute($stmt_failed);
        $result_failed = mysqli_stmt_get_result($stmt_failed);
        $rowCount_failed = $result_failed->num_rows;
        if ($rowCount_failed > 0) {
            // add 1
            $row = $result_failed->fetch_assoc();
            $id = $row['id'];
            $count = (int)$row['count'];
            $last_timestamp = $row['last_timestamp'];
            $current_timestamp = $row['CURRENT_TIMESTAMP'];

            if (strtotime($current_timestamp) - strtotime($last_timestamp) < 60) {
                $newCount = $count+1;
            } else {
                $newCount = 1;
            }

            $sql_update = "UPDATE login_failure
                            SET count=?, last_timestamp=CURRENT_TIMESTAMP
                            WHERE id=?";
            $stmt_update = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                echo('sql issue');
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_update, "ss", $newCount, $id);
                mysqli_stmt_execute($stmt_update);
                $return = $newCount;
            }
            
        } else {
            // insert
            $sql_insert = "INSERT INTO login_failure (username, auth, $ipfield, last_timestamp, count) 
                            VALUES (?,?,$ipconvert,CURRENT_TIMESTAMP,1)";
            $stmt_insert = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
                echo("Error getting users");
            } else {
                mysqli_stmt_bind_param($stmt_insert, "sss", $username, $auth, $ip);
                mysqli_stmt_execute($stmt_insert);
                // $log_id = mysqli_insert_id($conn);
                $return = 1;
            } 
        }
    }
    return $return;
}
// delete from login_failure
function deleteLoginFail($data, $auth) {
    $ip_info = getIPInfo();
    $ip = $ip_info['ip'];
    $ipfield = $ip_info['type'];
    $ipconvert = $ip_info['convert'];
    $username = addslashes(trim($data["username"]));

    include 'dbh.inc.php';

    $sql_failed = "SELECT id, username, auth, $ipfield, last_timestamp, count, CURRENT_TIMESTAMP
                    FROM login_failure
                    WHERE username=? 
                        AND auth=? 
                        AND $ipfield=$ipconvert";
    $stmt_failed = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_failed, $sql_failed)) {
        echo("Error getting users");
    } else {
        mysqli_stmt_bind_param($stmt_failed, "sss", $username, $auth, $ip);
        mysqli_stmt_execute($stmt_failed);
        $result_failed = mysqli_stmt_get_result($stmt_failed);
        $rowCount_failed = $result_failed->num_rows;
        if ($rowCount_failed > 0) {
            $row_failed = $result_failed->fetch_assoc();
            $id = $row_failed['id'];

            $sql_update = "DELETE FROM login_failure
                            WHERE id=?";
            $stmt_update = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                echo('sql issue');
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_update, "s", $id);
                mysqli_stmt_execute($stmt_update);
            }

        } 
    }
}


?>
