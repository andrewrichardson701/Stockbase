<?php 
// USER PROFILE PAGE
// SEE USER INFO FROM THE DATABASE BUT NOT MODIFY ANY (YET ATLEAST)
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>Inventory - Profile</title>
</head>
<body>
    <?php // dependency PHP
    

    ?>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Profile</h2>
    </div>
    <?php

    include 'includes/dbh.inc.php';

    $sql_users = "SELECT * FROM users WHERE username=?";
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
                $profile_username = $row['username'];
                $profile_first_name = $row['first_name'];
                $profile_last_name = $row['last_name'];
                $profile_email = $row['email'];
                $profile_role = ucwords($row['role']);
            }  
        } else { // if there are somehow too many rows matching the sql
            header("Location: ../index.php?sqlerror=multipleentries");
            exit();
        }
    }
    ?>

    <div class="container" style="margin-top:25px">
        <h3 style="font-size:22px">User Information</h3>
        <div style="padding-top: 20px;margin-left:25px">
            <form action="/action_page.php">
                <table>
                    <tbody>
                        <tr class="nav-row" id="username">
                            <td id="username_header" style="width:200px">
                                <!-- Custodian Colour: #72BE2A -->
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Username:</p>
                            </td>
                            <td id="username_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_username); ?></p>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="firstname">
                            <td id="firstname_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                            </td>
                            <td id="firstname_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_first_name); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="lastname">
                            <td id="lastname_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                            </td>
                            <td id="lastname_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_last_name); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="email">
                            <td id="email_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Email:</p>
                            </td>
                            <td id="email_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_email); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="role">
                            <td id="role_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Role:</p>
                            </td>
                            <td id="role_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_role); ?></p>
                            </td>
                        </tr>
                        <!-- <tr class="nav-row" style="margin-top:20px" id="banner-color">
                            <td id="banner-color-label" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="admin-banner-color">Banner Colour:</p>
                            </td>
                            <td id="banner-color-picker">
                                <label class="label-color">
                                    <input class="form-control" name="banner-color" type="text" value=""/>
                                </label>
                            </td>
                        </tr> -->
                        <!-- <tr class="nav-row" style="margin-top:20px">
                            <td colspan=3>
                                <input id="form-submit" type="submit" class="btn btn-secondary" style="margin-left:25px" value="Save" />
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </form>
        </div>

    </div>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->
</body>