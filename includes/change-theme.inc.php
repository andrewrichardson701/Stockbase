<?php

if (isset($_GET['change']) && isset($_GET['theme']) && isset($_GET['value']) && isset($_GET['user-id'])) {

    session_start();
    $_SESSION['theme'] = $_GET['theme'];

    $theme = $_GET['value'];

    include 'dbh.inc.php';
    include 'changelog.inc.php';

    // get current info
    $sql = "SELECT id, username, theme FROM users
                    WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $errors[] = 'checkID stock table error - SQL connection';
        echo('error');
    } else {
        mysqli_stmt_bind_param($stmt, "s", $_GET['user-id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        
        $row = $result->fetch_assoc();

        $username = $row['username'];
        $old_theme = $row['theme'];

        //update info
        $sql_update = "UPDATE users SET theme='$theme' WHERE id=?";
        $stmt_update = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
            echo('error');
        } else {
            mysqli_stmt_bind_param($stmt_update, "s", $_GET['user-id']);
            mysqli_stmt_execute($stmt_update);
            
            // update changelog
            addChangelog($_GET['user-id'], $username, "Theme Change", "users", $_GET['user-id'], "theme", $old_theme, $_GET['value']);
            
            echo ('success');
        }
    } 
} 