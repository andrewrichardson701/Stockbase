<?php
// LOGOUT FROM THE USER - UNSET AND DESTROY THE SESSION - NAVIGATE TO LOGIN PAGE
session_start();
session_unset();
session_destroy();
header("Location: ./login.php");
exit();
?>