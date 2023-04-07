<?php
if (isset($_POST['submit'])) {
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['password_confirm']) || !isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email']) || !isset($_POST['role'])) {
        header("Location: ../login.php?error=emptyfields");
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
            $new_role = $_POST['role'];
            $new_auth = 'local';
            // Check if the user exists already in the users table
            include '../../includes/dbh.inc.php';

            $sql_users = "SELECT * FROM users WHERE username=? AND auth='local'";
            $stmt_users = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                header("Location: ../login.php?error=sqlerror_getUsersList");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_users, "s", $new_username);
                mysqli_stmt_execute($stmt_users);
                $result = mysqli_stmt_get_result($stmt_users);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    $userFound = 0;

                    // ADD user to table

                    $sql_upload = "INSERT INTO users (username, first_name, last_name, email, role, auth, password) VALUES (?,?,?,?,?,?,?)";
                    $stmt_upload = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_upload, $sql_upload)) {
                        header("Location: ../addlocaluser.php?error=sqlerror");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt_upload, "sssssss", $new_username, $new_first_name, $new_last_name, $new_email, $new_role, $new_auth, $new_password_hash);
                        mysqli_stmt_execute($stmt_upload);
                    }
                    header("Location: ../addlocaluser.php?user=added");
                    exit();

                } elseif ($rowCount == 1) {
                    header("Location: ../addlocaluser.php?error=userExists");
                    exit();
                } else { // if there are somehow too many rows matching the sql
                    header("Location: ../login.php?sqlerror=multipleentries");
                    exit();
                }
            }
        } else {
            header("Location: ../login.php?error=passwordMismatch");
        }
    } 
} else {
    header("Location: ../login.php?error=submitNotSet");
    exit();
}
?>