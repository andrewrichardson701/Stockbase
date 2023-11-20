<?php 
$session_timeout = (30*60); // mins * seconds
function getIPAddress() {  
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
    return $ip;  
}  
function getBrowser() {
    global $_SERVER;
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown";
    
    if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident/') !== false) {
        $browser = 'Internet Explorer';
    } elseif (strpos($user_agent, 'Edge') !== false) {
        $browser = 'Microsoft Edge';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        $browser = 'Mozilla Firefox';
    } elseif (strpos($user_agent, 'Chrome') !== false) {
        $browser = 'Google Chrome';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $browser = 'Safari';
    } elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) {
        $browser = 'Opera';
    }

    return $browser;
}
function getOS() {
    global $_SERVER;

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os = "Unknown";

    if (strpos($user_agent, 'Windows') !== false) {
        $os = 'Windows';
    } elseif (strpos($user_agent, 'Mac') !== false) {
        $os = 'Mac';
    } elseif (strpos($user_agent, 'Linux') !== false) {
        $os = 'Linux';
    } elseif (strpos($user_agent, 'Android') !== false) {
        $os = 'Android';
    } elseif (strpos($user_agent, 'iOS') !== false) {
        $os = 'iOS';
    }

    return $os;
}

function sessionCloseExpired() {
    global $_SESSION, $session_timeout;

    if (isset($_SESSION['user_id'])) {
        
        $user_id = $_SESSION['user_id'];
        $logout_time = time();
        $expire_time = time()-$session_timeout;

        include 'dbh.inc.php';

        $sql = "SELECT * 
                FROM sessionlog
                WHERE last_activity<? AND status='active'";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo('sql issue');
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $expire_time);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {

            } else {
                while ($row = $result->fetch_assoc()) {
                    $session_id = $row['id'];
                    $status = 'expired';

                    $sql_update = "UPDATE sessionlog 
                            SET logout_time=?, status=?
                            WHERE id=? AND status='active'";
                    $stmt_update = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                        echo('sql issue');
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_update, "sss", $logout_time, $status, $session_id);
                        mysqli_stmt_execute($stmt_update);
                    }
                }
            }
        }
    } 
}
function sessionLastActivity() {
    global $_SESSION;

    if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
        $user_id = $_SESSION['user_id'];
        $session_id = $_SESSION['session_id'];
        $last_activity = time();

        include 'dbh.inc.php';

        // check if the session exists 
        $sql = "SELECT * 
                FROM sessionlog
                WHERE id=? AND user_id=? AND status='active'";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo('sql issue');
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $session_id, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {

            } else {
                //session found
                $sql = "UPDATE sessionlog 
                        SET last_activity=?
                        WHERE id=? AND user_id=? AND status='active'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo('sql issue');
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "sss", $last_activity, $session_id, $user_id);
                    mysqli_stmt_execute($stmt);
                    $_SESSION['last_activity'] = $last_activity;
                }
            }
        }
    }
}
function sessionTimeout() {
    // used for when someone is timedout of the session
    if (isset($_SESSION['session_id']) && isset($_SESSION['user_id'])) {
        $session_id = $_SESSION['session_id'];
        $user_id = $_SESSION['user_id'];
        $logout_time = time();
        $status = 'timeout';

        include 'dbh.inc.php';

        // check if the session exists 
        $sql = "SELECT * 
                FROM sessionlog
                WHERE id=? AND user_id=? AND status='active'";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo('sql issue');
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $session_id, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {

            } else {
                //session found
                $sql = "UPDATE sessionlog 
                        SET logout_time=?, status=?
                        WHERE id=? AND user_id=? AND status='active'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo('sql issue');
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ssss", $logout_time, $status, $session_id, $user_id);
                    mysqli_stmt_execute($stmt);
                    unset($_SESSION['session_id']);
                    unset($_SESSION['last_activity']);
                }
            }
        }
    }
}
function checkTimeout() {
    global $_SESSION, $session_timeout;

    if (isset($_SESSION['last_activity'])) {
        $last_activity = $_SESSION['last_activity'];
        $activity_check_time = time()-$session_timeout;

        if ($last_activity < $activity_check_time) {

            sessionTimeout();
        }
    }
}

// to be called directly
function sessionLogin() {
    // used when someone logs in to the system
    global $_SESSION;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $login_time = time();
        $ip = getIPAddress();
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip_field = 'ipv4';
            $ip_insert = 'INET_ATON(?)';
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip_field = 'ipv6';
            $ip_insert = 'INET6_ATON(?)';
        } else {
            $ip_field = 'ipv4';
            $ip_insert = '?';
            $ip = null;
        }
        $browser = getBrowser();
        $os = getOS();
        $status = 'active';

        include 'dbh.inc.php';
        $sql = "INSERT INTO sessionlog (user_id, login_time, last_activity, $ip_field, browser, os, status) 
                                    VALUES (?, ?, ?, $ip_insert, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo('mysql issue');
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "sssssss", $user_id, $login_time, $login_time, $ip, $browser, $os, $status);
            mysqli_stmt_execute($stmt);
            $session_id = mysqli_insert_id($conn);
            $_SESSION['session_id'] = $session_id;
            $_SESSION['last_activity'] = time();
        }
    }  else {
        echo('user_id not set');
        error_log('user_id not set');
    }
    
}
// to be called directly
function sessionLogout() {
    // used when someone logs out of the system
    global $_SESSION;

    if (isset($_SESSION['session_id']) && isset($_SESSION['user_id'])) {
        $session_id = $_SESSION['session_id'];
        $user_id = $_SESSION['user_id'];
        $logout_time = time();
        $status = 'inactive';

        include 'dbh.inc.php';

        // check if the session exists 
        $sql = "SELECT * 
                FROM sessionlog
                WHERE id=? AND user_id=? AND status='active'";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo('sql issue');
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $session_id, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                echo('session not found');
            } else {
                //session found
                $sql = "UPDATE sessionlog 
                        SET logout_time=?, status=?
                        WHERE id=? AND user_id=? AND status='active'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo('sql issue');
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ssss", $logout_time, $status, $session_id, $user_id);
                    mysqli_stmt_execute($stmt);
                    unset($_SESSION['session_id']);
                    unset($_SESSION['last_activity']);
                }
            }
        }
    }
}

// $_SESSION['user_id'] = 1;
// $_SESSION['session_id'] = 7;

// print_r($_SESSION);
// print_r($_POST);

// // check for timeout
// checktimeout();
// // expire any old sessions
// sessionCloseExpired();
// // set the session last_activity
// sessionLastActivity();

?>