<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

if (isset($_POST['signup_check'])) {
    // csrf_token management
    if (isset($_POST['csrf_token'])) {
        if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
            header("Location: ../signup.php?error=csrfMissmatch");
            exit();
        }
    } else {
        header("Location: ../signup.php?error=csrfMissing");
        exit();
    }
    
    if (isset($_POST['username'])) {
        if (isset($_POST['password'])) {
            if (isset($_POST['password_confirm'])) {
                if(isset($_POST['email'])) {
                    if (isset($_POST['firstname'])) {
                        if (isset($_POST['lastname'])) {
                            // all found - do the logic
                            $username = strtolower($_POST['username']);
                            $password = $_POST['password'];
                            $password_hash = password_hash($password, PASSWORD_DEFAULT);
                            $password_confirm = $_POST['password_confirm'];
                            $email = strtolower($_POST['email']);
                            $firstname = ucfirst(strtolower($_POST['firstname']));
                            $lastname = ucfirst(strtolower($_POST['lastname']));

                            if ($password !== $password_confirm) {
                                header("Location: ../signup.php?error=passwordMissmatch");
                                exit();
                            }

                            // check if username is unique
                            include 'dbh.inc.php';

                            $sql = "SELECT * FROM users WHERE username = ?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../signup.php?error=sqlError");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $username);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $rowCount = $result->num_rows;
                
                                if ($rowCount > 0) {
                                    header("Location: ../signup.php?error=usernameTaken");
                                    exit();
                                } 
                            }

                            // check if email is unique

                            $sql = "SELECT * FROM users WHERE email = ?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../signup.php?error=sqlError");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $enail);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $rowCount = $result->num_rows;
                                $return['success'] = 1;
                                if ($rowCount > 0) {
                                    header("Location: ../signup.php?error=emailUsed");
                                    exit();
                                } 
                            }

                            // insert into the table
                            $sql_insert = "INSERT INTO users (username, first_name, last_name, email, auth, password, role_id, enabled, password_expired, theme_id)
                                            VALUES (?, ?, ?, ?, 'local', ?, 1, 1, 0, 0)";
                            $stmt_insert = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
                                header("Location: ../signup.php?error=sqlError");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_insert, "sssss", $username, $firstname, $lastname, $email, $password_hash);
                                mysqli_stmt_execute($stmt_insert);
                                $user_id = mysqli_insert_id($conn);
                                header("Location: ../login.php?success=userAdded&id=$user_id");
                                exit();
                            } 
                            
                        } else {
                            header("Location: ../signup.php?error=missingLastName");
                            exit();
                        }
                    } else {
                        header("Location: ../signup.php?error=missingFirstName");
                        exit();
                    }
                } else {
                    header("Location: ../signup.php?error=missingEmail");
                    exit();
                }
            } else {
                header("Location: ../signup.php?error=missingPasswordConfirm");
                exit();
            }
        } else {
            header("Location: ../signup.php?error=missingPassword");
            exit();
        }
    } else {
        header("Location: ../signup.php?error=missingUsername");
        exit();
    }
} else {
    header("Location: ../signup.php?error=noSubmit");
    exit();
}
?>



