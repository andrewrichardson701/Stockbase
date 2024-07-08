<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// DEFAULT LANDING PAGE IF NOT LOGGED IN. 
// ALLOWS USERS TO LOGIN TO THE SYSTEM TO VIEW AND MODIFY CONTENT
session_start();

// if session not set, go to login page
if (session_status() !== PHP_SESSION_ACTIVE) {
    header("Location: ./");
    exit();
} else {
    if (!isset($_SESSION['username']) || !isset($_SESSION['first_name']) || !isset($_SESSION['last_name']) || !isset($_SESSION['email']) || !isset($_SESSION['session_id'])) {
        if (!isset($_SESSION['username']) && !isset($_SESSION['first_name']) && !isset($_SESSION['last_name']) && !isset($_SESSION['email']) && !isset($_SESSION['session_id'])) {
            
        } else {
            header("Location: ./logout.php");
            exit();
        }
    } else {
        header("Location: ./");
        exit();
    }
}
// create csrf token to be used in form submission
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a random token
}

// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Login</title>
</head>
<body id="body">
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->
    <?php 
    
    include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings. ?>
    
    <div class="container" style="margin-top:75px">
        <div class="row">
            <div class="col-md-6" style="margin-left:25px; margin-right:25px">
                <h3>Login</h3>
                <p style="margin-top:2vh;margin-bottom:3vh">Please input your credentials to login.</p>
                <form id="loginForm" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input id="csrf_token" type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-group">
                        <label>Username</label>
                        <input id="username" type="username" name="username" class="form-control" placeholder="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    
                        <div class="nav-row">
                            <div class="form-group">
                                <input id="submit" type="submit" name="submit" class="btn btn-primary" value="Login">
                            </div>
                            <?php 
                            if ($current_ldap_enabled == 1) {
                                echo('
                                    <div style="margin-left:25px">
                                        <label class="switch" style="margin-bottom: 0px">
                                            <input type="checkbox" name="local" id="local-toggle" value="on">
                                            <span class="sliderBlue round" id="local-span" style="transform: scale(0.6, 0.6)"></span>
                                        </label>
                                    </div>
                                    <label class="nav-div" style="margin-left:0px">Local Login<p></p></label>
                                ');
                            } else {
                                echo('<input id="local-toggle" type="hidden" name="local" value="on" />');
                            }
                            ?>
                        </div>
                        
                </form>
                <?php
                    showResponse();
                    if (isset($_GET["newpwd"])) {
                        if ($_GET["newpwd"] == "passwordupdated") {
                            echo '<p class="green">Your password has been changed!</p>';
                        }
                    }
                    if (isset($_GET['resetemail'])) {
                        if ($_GET['resetemail'] == "sent") {
                            echo '<p class="green">Password reset email sent. Please check your email.</p>';
                        }
                    }
                ?>
                <p id="js-info" style="display:none"></p>
                <p><a href="login.php?reset=true" id="password-reset">Forgot password?</a>
                <!-- <button class="btn btn-info viewport-small-block" onclick="modalLoadSwipe()">Swipe card login</button> -->
                <!-- <p><a href="https://todo.ajrich.co.uk/#/board/16" id="todo" class="link" target="_blank"> To do list for the ongoing project</a></p> -->
            </div>
        </div>
	</div>
    <?php
        if (isset($_GET['reset']) && $_GET['reset'] == "true") {
    ?>
    <div id="modalDiv" class="modal" style="display:block;padding:auto;background-color: rgba(0,0,0,0.7);">
        <span class="close" onclick="modalClose()">×</span>
            <div class="well-nopad theme-divBg" style="position:relative; margin:auto; min-width:200px;max-width:500px; height:300px; overflow-y:auto;display:flex;justify-content:center;align-items:center;">
            <form id="locationForm" enctype="application/x-www-form-urlencoded" action="./includes/changepassword.inc.php" method="POST">
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <table>
                    <tbody>
                        <tr>
                            <td class="align-middle">Username / Email:</td>
                            <td class="align-middle" style="padding-left:10px"><input class="form-control" type="text" name="uid" placeholder="username / email" /></td>
                        </tr>
                        <tr>
                            <td colspan=2 class="text-center" style="padding-top:20px">
                                <input class="btn btn-success" name="reset-request-submit" value="Reset Password" type="submit"/> 
                                <button type="button"  class="btn btn-warning" onclick="navPage('login.php')" style="margin-left:20px">Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <?php
            if (isset($_GET['reseterror'])) {
                if ($_GET['reseterror'] == "uidMissmatch") {
                    echo('<p class="red">Username/email not found.</p>');
                } elseif ($_GET['reseterror'] == "multipleUsersMatchUid") {
                    echo('<p class="red">Multiple users found for this username/email (somehow). Contact an administrator.</p>');
                }
            }
            ?>
            </div>
        </div>
    </div>
    
    <?php
        }
    ?>

<script src='assets/js/login.js'></script>

<?php include 'foot.php'; ?>

</body>
