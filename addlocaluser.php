<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// ADMIN PAGE - SHOWS CONFIGURATION OPTIONS AND ONLY VISIBLE TO ADMIN USERS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings.
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 


?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Add Local User</title>
</head>
<body>
    <?php // dependency PHP    
        // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
        if (!in_array($_SESSION['role'], $config_admin_roles_array)) {
            header("Location: ./login.php");
            exit();
        }
    ?>

    <div class="content">
        <!-- Header and Nav -->
        <?php include 'nav.php'; ?>
        <!-- End of Header and Nav -->

        <div class="container">
            <h2 class="header-small">Add Local User</h2>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6" style="margin-left:25px">
                    <p>Please fill in the below to add a local user.</p>
                    <form enctype="multipart/form-data" action="./includes/addlocaluser.inc.php" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" placeholder="username@domain.com" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" style="width:250px" class="form-control" required>
                                <?php
                                    include 'includes/dbh.inc.php';

                                    $sql_users = "SELECT * FROM users_roles;";
                                    $stmt_users = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                                        header("Location: ../admin.php?error=sqlerror&table=users_roles");
                                        exit();
                                    } else {
                                        mysqli_stmt_execute($stmt_users);
                                        $result = mysqli_stmt_get_result($stmt_users);
                                        $rowCount = $result->num_rows;
                                        if ($rowCount < 1) {
                                            echo ("<option value='' selected disabled>No User Roles Found.</option>");
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                $role_id = $row['id'];
                                                $role_name = $row['name'];
                                                echo ("<option value='$role_id'"); if ($role_name == "User") { echo (" selected"); } elseif ($role_name == "Root") { echo (" disabled"); } echo (">$role_name</option>");
                                            }
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="nav-row">
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-primary" value="Add user">
                            </div>
                            <div class="form-group" style="margin-left:20px">
                                <input type="button" name="cancel" class="btn btn-warning" value="Cancel" onclick="window.location.href='admin.php#users-settings'">
                            </div>
                        </div>
                    </form>
                    <?php
                        showResponse();
                        if (isset($_GET["user"])) {
                            if ($_GET["user"] == "added") {
                                if (isset($_GET['username'])) {
                                    if (isset($_GET['userId']) && $_GET['userId'] !== '') {
                                        echo '<p class="green">Local user: <or class="blue">'.$_GET['username'].'</or> (id: <or class="blue">'.$_GET['userId'].'</or>) added!</p>';
                                    } else {
                                        echo '<p class="green">Local user: <or class="blue">'.$_GET['username'].'</or> added!</p>';
                                    }
                                } else {
                                    echo '<p class="green">Local user added!</p>';
                                }
                            }
                        }
                        if (isset($_GET["newpwd"])) {
                            if ($_GET["newpwd"] == "passwordupdated") {
                                echo '<p class="green">Your password has been changed!</p>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>