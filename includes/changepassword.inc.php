<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USED TO CHANGE PASSWORD

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

$redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
if (strpos($redirect_url, "?")) {
    $queryChar = "&";
} else {
    $queryChar = "?";
}

if (isset($_POST['password-submit'])) { // normal change password requests
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
} elseif (isset($_POST["reset-request-submit"])) { // for sending reset emails
    if (isset($_POST['uid'])) {
        
        require 'dbh.inc.php';

        $uid = strtolower($_POST["uid"]);
        
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);

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

                $override_email = $user_email;

                include 'smtp.inc.php';
                
                $baseUrl = ((str_contains($_SERVER['HTTP_REFERER'], "https")) ? 'https' : 'http') . "://".$current_base_url."/reset-password.php";
                $url = "$baseUrl?selector=" . $selector . "&validator=" . bin2hex($token);

                $email_subject = ucwords($current_system_name)." - Reset your password";
                $email_body = '<p>We recieved a password reset request for your account. <br>The link to reset your password is below. If you did not make this request, you can ignore this email.</p>
                                <p>Here is your password reset link: </br><a href="' . $url . '">' . $url . '</a></p>';
                send_email($user_email, $user_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 0);
                header("Location: ../login.php?resetemail=sent");
                exit();
            }
        }
    } else {
        header("Location: ../login.php?reset=true&error=noUid");
        exit();
    }
} elseif (isset($_POST['reset-password-submit'])) { // for actually resetting the password
    if (isset($_POST["selector"])) {
        if (isset($_POST["validator"])) {
            $selector = $_POST["selector"];
	        $validator = $_POST["validator"];

            $url = "../reset-password.php";
            $baseUrl = ((str_contains($_SERVER['HTTP_REFERER'], "https")) ? 'https' : 'http') . "://".$current_base_url;

            if (isset($_POST["password"]) && isset($_POST["password_confirm"])) {

                $password = $_POST["password"];
                $password_confirm = $_POST["password_confirm"];

                if (empty($password) || empty($password_confirm)) {
                    header("location: $url&newpwd=empty"); 
                    exit();
                } else if ($password != $password_confirm) {
                    header("location: $url&newpwd=pwdnotsame"); 
                    exit();
                } 

                $current_date = date("U");

                require 'dbh.inc.php';

                $sql = "SELECT * FROM password_reset WHERE reset_selector=? AND reset_expires >= ?";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("location: $url&sqlerror=password_reset&error=resubmit"); 
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $selector, $current_date); 
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    if (!$row = mysqli_fetch_assoc($result)) {
                        header("location: $url&error=resubmitDate"); 
                        exit();
                    } else {

                        $token_bin = hex2bin($validator);
                        $token_check = password_verify($token_bin, $row["reset_token"]);

                        if ($token_check === false) {
                            header("location: $url&error=resubmitToken"); 
                            exit();
                        } else if ($token_check === true) {

                            $reset_user_id = $row['reset_user_id'];

                            $sql = "SELECT * FROM users WHERE id=?;";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("location: $url&sqlerror=usersSelect&error=resubmit"); 
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $reset_user_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (!$row = mysqli_fetch_assoc($result)) {
                                    header("location: $url&error=resubmitResults"); 
                                    exit();
                                } else {
                                    $user_firstname = $row['first_name'];
                                    $user_lastname = $row['last_name'];
                                    $user_email = $row['email'];
                                    $user_fullname = ucwords($user_firstname).' '.ucwords($user_lastname);

                                    $sql = "UPDATE users SET password=? WHERE id=? AND auth='local'";
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        header("location: $url&sqlerror=usersUpdate&error=resubmit"); 
                                        exit();
                                    } else {
                                        $new_password_hash = password_hash($password, PASSWORD_DEFAULT);
                                        mysqli_stmt_bind_param($stmt, "ss", $new_password_hash, $reset_user_id);
                                        mysqli_stmt_execute($stmt);

                                        $sql = "DELETE FROM password_reset WHERE reset_user_id=?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("location: $url&sqlerror=password_resetDelete&error=resubmit"); 
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt, "s", $reset_user_id);
                                            mysqli_stmt_execute($stmt);

                                            include 'smtp.inc.php';

                                            $email_subject = ucwords($current_system_name)." - Password Reset!";
                                            $email_body = '<p>Your password has been reset.</p>
                                                            <p>Click <a href="' . $baseUrl . '">here</a> to login.</p>';
                                            send_email($user_email, $user_fullname, $config_smtp_from_name, $email_subject, createEmail($email_body), 0);
                                            header("Location: ../login.php?newpwd=passwordupdated");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                header("location: $url&newpwd=empty"); 
		        exit();
            }
        } else {
            header("location: ../reset-password.php?error=validatorMissing");
            exit();
        }
    } else {
        header("location: ../reset-password.php?error=selectorMissing");
        exit();
    }
} else {
    header("Location: ../".$redirect_url.$queryChar."error=noSubmit");
    exit();
}
?>
