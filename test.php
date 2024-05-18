<?php 
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 
$_SESSION['csrf_token'] = '123456789';
?>
<body>
<form action="includes/admin.inc.php" method="POST" enctype="application/x-www-form-urlencoded">
    <input name="user_id" value="5">
    <input name="user_new_role" value="3">
    <input name="user_role_submit" value="yes">
    <input name="csrf_token" value="123456789">
    <input type="submit" name="submit" value="submit">
</form>
</body>