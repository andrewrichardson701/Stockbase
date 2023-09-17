<?php
// SETUP THE SESSION FOR ALL PAGES - THIS WILL CONFIRM IF THERE IS A LOGGED IN USER OR NOT.
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); 
}

// set the redirect_url 

$redirect_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$queryString = '?'.parse_url($_SERVER['QUERY_STRING'], PHP_URL_PATH);

$_SESSION['redirect_url'] = str_contains(basename($redirect_path), '.php') ? basename($redirect_path).$queryString : "";

// if session not set, go to login page
if (!str_contains($_SERVER['REQUEST_URI'], "changepassword.inc.php")) {
    if ((session_status() !== PHP_SESSION_ACTIVE) || (!isset($_SESSION['username'])) || ($_SESSION['username'] === '')) {
        // redirect to login page
        if (isset($_SESSION['redirect_url']) && $_SESSION['redirect_url'] !== '') {
            header('Location: ./login.php?url=' . urlencode($_SESSION['redirect_url']));
            exit;
        } else {
            header('Location: ./login.php');
            exit;
        }
        
    }  
}


if (isset($_SESSION['password_expired']) && $_SESSION['password_expired'] == 1) {
    if (!strpos($_SERVER['REQUEST_URI'], "changepassword.php")) {
        header("Location: ./changepassword.php?expired=true");
        exit();
    }
}
$loggedin_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$loggedin_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$loggedin_firstname = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$loggedin_lastname = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '';
$loggedin_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$loggedin_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$loggedin_auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : '';
$loggedin_theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : '';
$loggedin_password_expired = isset($_SESSION['password_expired']) ? $_SESSION['password_expired'] : '';

$loggedin_fullname = ucwords($loggedin_firstname).' '.ucwords($loggedin_lastname);
$profile_name = ucwords($loggedin_firstname);

?>
