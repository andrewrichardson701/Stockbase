<?php
print_r($_POST);

session_start();
$redirect_url = $_SESSION['redirect_url'];
if (strpos($redirect_url, "?")) {
    $queryChar = "&";
} else {
    $queryChar = "?";
}

if (isset($_POST['password-submit'])) {
    if (isset($_POST['user-id'])) {
        $user_id = $_POST['user-id'];

        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];
        if ($new_password === $confirm_password) {

            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            include 'dbh.inc.php';

            $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, 
                                users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, 
                                users.enabled as enabled, users.password AS users_password
                            FROM users 
                            INNER JOIN users_roles ON users.role_id = users_roles.id
                            WHERE users.id=?";
            $stmt_users = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                header("Location: ../".$redirect_url.$queryChar."error=usersTableIssue");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_users, "s", $user_id);
                mysqli_stmt_execute($stmt_users);
                $result = mysqli_stmt_get_result($stmt_users);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    header("Location: ../".$redirect_url.$queryChar."error=noUserFound");
                    exit();
                } elseif ($rowCount > 1) {
                    header("Location: ../".$redirect_url.$queryChar."error=tooManyUserFound");
                    exit();
                } else {
                    // 1 user found - continue

                    $row = $result->fetch_assoc();
                    $current_password_hash = $row['users_password'];

                    if ($current_password_hash === $new_password_hash) {
                        header("Location: ../".$redirect_url.$queryChar."error=passwordMatchesCurrent");
                        exit();
                    } else {
                        $sql_upload = "UPDATE users SET password='$new_password_hash', password_expired=0 WHERE id=?";
                        $stmt_upload = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                            header("Location: ../".$redirect_url.$queryChar."error=passwordResetSQLError");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt_upload, "s", $user_id);
                            mysqli_stmt_execute($stmt_upload);
                            $_SESSION['password_expired'] = 0;
                            header("Location: ../profile.php?success=PasswordChanged");
                            exit();
                        }
                    }
                    
                }
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar."error=passwordsNotMatching");
            exit();
        }
    } else {
        header("Location: ../".$redirect_url.$queryChar."error=noUserID");
        exit(); 
    }
} else {
    header("Location: ../".$redirect_url.$queryChar."error=noSubmit");
    exit();
}
?>