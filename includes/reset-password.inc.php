<?php

if (isset($_POST["reset-request-submit"])) {
    if (isset($_POST['uid'])) {
        
        require 'dbh.inc.php';
        include 'smtp.inc.php';

        $uid = strtolower($_POST["uid"]);
        
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);

        $baseUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://".$current_base_url."/reset-password.php";
        $url = "$baseUrl?selector=" . $selector . "&validator=" . bin2hex($token);

        $expires = date("U") + 3600;

        if (filter_var($uid, FILTER_VALIDATE_EMAIL)) {
            $uidType = "email";
        } else {
            $uidType = "username";
        }

        $sql_users = "SELECT id, email, username, first_name, last_name, enabled, auth
                        FROM users 
                        WHERE $uidType='$uid' AND auth='local'";
        $stmt_users = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
            header("Location: ../login.php?reset=true&sqlerror=unableToGetUsers&uid=$uid");
            exit();
        } else {
            mysqli_stmt_execute($stmt_users);
            $result_users = mysqli_stmt_get_result($stmt_users);
            $rowCount_users = $result_users->num_rows;
            if ($rowCount_users < 1) {
                header("Location: ../login.php?reseterror=uidMissmatch&uid=$uid");
                exit();
            } elseif ($rowCount_users > 1) {
                // This should not be possible, the email and usernames should be unique
                header("Location: ../login.php?reset=true&reseterror=multipleUsersMatchUid&uid=$uid");
                exit();
            } else {
                // only one user found. this is expected.
                $row_users = $result_users->fetch_assoc();
                
                $user_id = $row_users['id'];
                $user_email = $row_users['email'];
                $user_firstname = $row_users['first_name'];
                $user_lastname = $row_users['last_name'];
                $user_fullname = ucwords($user_firstname).' '.ucwords($user_lastname);

                
                $sql = "DELETE FROM password_reset WHERE reset_user_id=?;";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../login.php?reset=true&sqlerror=password_reset".__LINE__."&uid=$uid");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $user_id);
                    mysqli_stmt_execute($stmt);
                }

                $sql = "INSERT INTO password_reset (reset_user_id, reset_selector, reset_token, reset_expires) VALUES (?, ?, ?, ?);";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../login.php?reset=true&sqlerror=password_reset".__LINE__."&uid=$uid");
                    exit();
                } else {
                    $hashed_token = password_hash($token, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "ssss", $user_id, $selector, $hashed_token, $expires);
                    mysqli_stmt_execute($stmt);
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                $email_subject = ucwords($current_system_name)." - Reset your password";
                $email_body = '<p>We recieved a password reset request for your account. <br>The link to reset your password is below. If you did not make this request, you can ignore this email.</p>
                                <p>Here is your password reset link: </br><a href="' . $url . '">' . $url . '</a></p>';
                if (send_email($user_email, $user_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body))){
                    header("Location: ../login.php?resetemail=sent");
                    exit();
                } else {
                    header("Location: ../login.php?reset=true&resetemail=failed&uid=$uid");
                    exit();
                }
            }
        }
    } else {
        header("Location: ../login.php?reset=true&error=noUid");
        exit();
    }
    
} else {
    header("Location ../index.php");
}
?>