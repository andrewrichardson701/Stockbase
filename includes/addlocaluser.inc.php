<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

if (isset($_POST['submit'])) {
    // csrf_token management
    if (isset($_POST['csrf_token'])) {
        if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
            header("Location: ../admin.php?error=csrfMissmatch&section=smtp-settings#smtp-settings");
            exit();
        }
    } else {
        header("Location: ../admin.php?error=csrfMissmatch&section=smtp-settings#smtp-settings");
        exit();
    }
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['password_confirm']) || !isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email']) || !isset($_POST['role'])) {
        header("Location: ../addlocaluser.php?error=emptyFields");
        exit();
    } else {
        if (isset($_POST['password']) === isset($_POST['password_confirm'])) {
            
            $new_username = $_POST["username"];
            $new_password = $_POST["password"];
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $new_password_confirm = $_POST['password_confirm'];
            $new_first_name = $_POST['first_name'];
            $new_last_name = $_POST['last_name'];
            $new_email = $_POST['email'];
            $new_role_id = $_POST['role'];
            $new_auth = 'local';
            $new_enabled = 1;
            $new_expired = 1;

            // Check if the user exists already in the users table
            include 'dbh.inc.php';

            $sql_users = "SELECT * FROM users WHERE username=? AND auth='local'";
            $stmt_users = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                header("Location: ../addlocaluser.php?error=sqlerror&table=users&line=".__LINE__."&file=".__FILE__);
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_users, "s", $new_username);
                mysqli_stmt_execute($stmt_users);
                $result = mysqli_stmt_get_result($stmt_users);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    $userFound = 0;

                    // ADD user to table
                    $new_first_name = mysqli_real_escape_string($conn, $new_first_name); // escape the special characters
                    $new_last_name = mysqli_real_escape_string($conn, $new_last_name); // escape the special characters
                    $sql_upload = "INSERT INTO users (username, first_name, last_name, email, role_id, auth, password, enabled, password_expired, theme_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $stmt_upload = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                        header("Location: ../addlocaluser.php?error=sqlerror&table=users&line=".__LINE__."&file=".__FILE__);
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_upload, "ssssssssss", $new_username, $new_first_name, $new_last_name, $new_email, $new_role_id, $new_auth, $new_password_hash, $new_enabled, $new_expired, $current_default_theme_id);
                        mysqli_stmt_execute($stmt_upload);
                        $new_id = mysqli_insert_id($conn);
                        include 'changelog.inc.php';
                        include 'smtp.inc.php';

                        // update changelog
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "users", $new_id, "username", null, $new_username);

                        $baseUrl = ((str_contains($_SERVER['HTTP_REFERER'], "https")) ? 'https' : 'http') . "://".$current_base_url;
                        $email_subject = ucwords($current_system_name)." - Account created";
                        $email_body = "<p>Your user account has been created.<br>Your username is: <strong>$new_username</strong><br>Your temporary password is:<br><strong>$new_password</strong><br>You will be prompted to change this on login.<br>Click <a href=\"$baseUrl\">here</a> to login.</p>";
                        send_email($new_email, ucwords($new_first_name).' '.ucwords($new_last_name), $config_smtp_from_name, $email_subject, createEmail($email_body), 0);

                    }
                    header("Location: ../addlocaluser.php?user=added&username=$new_username&userId=$new_id");
                    exit();

                } elseif ($rowCount == 1) {
                    header("Location: ../addlocaluser.php?error=userExists");
                    exit();
                } else { // if there are somehow too many rows matching the sql
                    header("Location: ../addlocaluser.php?sqlerror=multipleEntries");
                    exit();
                }
            }
        } else {
            header("Location: ../addlocaluser.php?error=passwordMismatch");
        }
    } 
} else {
    header("Location: ../addlocaluser.php?error=submitNotSet");
    exit();
}
?>