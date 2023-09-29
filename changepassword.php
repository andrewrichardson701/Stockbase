<?php 
// USER PROFILE PAGE
// SEE USER INFO FROM THE DATABASE BUT NOT MODIFY ANY (YET ATLEAST)

include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 

if ($_SESSION['auth'] == "ldap") {
    header("Location: profile.php");
    exit();
}
?> 

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Change Password</title>
</head>
<body>
    <?php // dependency PHP
    
    ?>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->
    
    <div class="content">
        <div class="container">
            <h2 class="header-small">Change Password</h2>
        <?php

        include 'includes/dbh.inc.php';

        $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role,
                        users.password_expired AS password_expired
                        FROM users 
                        INNER JOIN users_roles ON users.role_id = users_roles.id
                        WHERE username=?";
        $stmt_users = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
            header("Location: ../index.php?error=sqlerror_getUsersList");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt_users, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmt_users);
            $result = mysqli_stmt_get_result($stmt_users);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                $userFound = 0;

            } elseif ($rowCount == 1) {
                while ($row = $result->fetch_assoc()){
                    $profile_id = $row['users_id'];
                    $profile_username = $row['username'];
                    $profile_first_name = $row['first_name'];
                    $profile_last_name = $row['last_name'];
                    $profile_email = $row['email'];
                    $profile_role = ucwords($row['role']);
                    $profile_password_expired = $row['password_expired'];
                }  
            } else { // if there are somehow too many rows matching the sql
                header("Location: ../index.php?sqlerror=multipleentries");
                exit();
            }
        }
        ?>
            <p>
                <?php
                if ($profile_password_expired == 1) {
                    echo("Password Expired. Please change your password to continue.");
                }
                ?>
            </p>
        </div>
        <div class="container" style="margin-top:25px">
            
            <div style="padding-top: 20px;margin-left:25px">
                <form action="includes/changepassword.inc.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user-id" value="<?php echo($profile_id); ?>">
                    <input type="hidden" name="user-role" value="<?php echo($profile_role); ?>">
                    <table>
                        <tbody>
                            <tr class="nav-row">
                                <td style="width:200px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">New Password:</p>
                                </td>
                                <td>
                                    <input class="form-control" id="password" type="password" name="password" />
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:200px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Confirm Password:</p>
                                </td>
                                <td>
                                    <input class="form-control" id="confirm-password" type="password" name="confirm-password" />
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:200px"></td>
                                <td>
                                    <input id="password-submit" type="submit" name="password-submit" class="btn btn-success" value="Change">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>

        </div>
    </div>
    <?php include 'foot.php'; ?>

</body>