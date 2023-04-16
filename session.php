<?php
// SETUP THE SESSION FOR ALL PAGES - THIS WILL CONFIRM IF THERE IS A LOGGED IN USER OR NOT.
session_start();
// set the redirect_url 
if (substr($_SERVER['REQUEST_URI'], 0, strlen('/inventory/')) == '/inventory/') {
    $_SESSION['redirect_url'] = substr($_SERVER['REQUEST_URI'], strlen('/inventory/'));
} else {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
}
// if session not set, go to login page
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

$loggedin_username = $_SESSION['username'];
$loggedin_firstname = $_SESSION['first_name'];
$loggedin_lastname = $_SESSION['last_name'];
$loggedin_email = $_SESSION['email'];
$loggedin_role = $_SESSION['role'];
$loggedin_auth = $_SESSION['auth'];
$profile_name = ucwords($loggedin_firstname);
?>
