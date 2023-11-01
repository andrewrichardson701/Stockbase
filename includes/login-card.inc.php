<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// SWIPE CARD LOGIN BACKEND - gets yuser info from the swipe card scanned and logs in.

if (isset($_POST['submitHidden'])) {
    if (isset($_POST['cardData'])) {
        $cardData = $_POST['cardData'];

        include 'dbh.inc.php';
        include 'get-config.inc.php';

        $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.enabled as enabled, users.password_expired AS password_expired, users.theme_id AS users_theme_id,
                            theme.name as theme_name, theme.file_name as theme_file_name
                        FROM users 
                        INNER JOIN users_roles ON users.role_id = users_roles.id
                        LEFT JOIN theme ON users.theme_id = theme.id
                        WHERE card_primary=? OR card_secondary=?";
        $stmt_users = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
            header("Location: ../login.php?error=sqlerror_getUsersList");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt_users, "ss", $cardData, $cardData);
            mysqli_stmt_execute($stmt_users);
            $result = mysqli_stmt_get_result($stmt_users);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                $userFound = 0;
                header("Location: ../login.php?error=invalidCredentials1");
                exit();
            } elseif ($rowCount == 1) {
                if ($row = mysqli_fetch_assoc($result)) {
                    if ($row['enabled'] != 1) {
                        header("Location: ../login.php?error=userDisabled");
                        exit();
                    }
                    session_start();
                    $userFound = 1; // not needed, but useful for debugging
                    $_SESSION['user_id'] = $row['users_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['auth'] = $row['auth'];
                    $_SESSION['theme_id'] = $row['users_theme_id'];
                    $_SESSION['theme_name'] = $row['theme_name'];
                    $_SESSION['theme_file_name'] = $row['theme_file_name'];
                    $_SESSION['password_expired'] = $row['password_expired'];
                    $_SESSION['impersonate'] = 0;
                    if (isset($_SESSION['redirect_url'])) {
                        if (str_contains($_SESSION['redirect_url'], "?")) {
                            header("Location: ../".$_SESSION['redirect_url']."&login=success");
                        } else {
                            header("Location: ../".$_SESSION['redirect_url']."?login=success");
                        }
                        exit();
                    } else {
                        header("Location: ../?login=success");
                        exit();
                    }
                } else {
                    header("Location: ../login.php?error=sqlResultsIssue");
                    exit();
                } 
            } else { // if there are somehow too many rows matching the sql
                $userFound = 0;
                header("Location: ../login.php?sqlerror=multipleEntries");
                exit();
            }
        }
    } else {
        header("Location: ../login.php?error=noCardData");
        exit();
    }
}  else {
    header("Location: ../login.php?error=noSubmit");
    exit();
}




?>