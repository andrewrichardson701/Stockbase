<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// RESET PASSWORD 
// ALLOWS USERS TO RESET THEIR PASSWORD IF LOGGED IN
include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings.

session_start();

// if session not set, go to login page
if (session_status() !== PHP_SESSION_ACTIVE) {
    header("Location: ./index.php");
    exit();
} else {
    if (!isset($_SESSION['username']) || !isset($_SESSION['first_name']) || !isset($_SESSION['last_name']) || !isset($_SESSION['email'])) {
        if (!isset($_SESSION['username']) && !isset($_SESSION['first_name']) && !isset($_SESSION['last_name']) && !isset($_SESSION['email'])) {
            
        } else {
            header("Location: ./logout.php");
            exit();
        }
    } else {
        header("Location: ./");
        exit();
    }
}
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Reset Password</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small"><?php echo ucwords($current_system_name);?></h2>
    </div>
    <div class="container" style="margin-top:20px">
        <div class="row">
            <div class="col-md-6" style="margin-left:25px">
                <h2>Create new password</h2>
                <p>Please enter a new password below.</p>
                <form enctype="multipart/form-data" action="includes/changepassword.inc.php" method="post">
                    <input type="hidden" name="selector" value="<?php echo $_GET['selector']; ?>" />
                    <input type="hidden" name="validator" value="<?php echo $_GET['validator']; ?>" />
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-control" placeholder="Password" required>
                    </div>
                    
                        <div class="nav-row">
                            <div class="form-group">
                                <input type="submit" name="reset-password-submit" class="btn btn-primary" value="Reset password">
                            </div>
                        </div>
                        
                </form>
                <?php
                    if (isset($_GET["newpwd"])) {
                        if ($_GET["newpwd"] == "passwordupdated") {
                            echo '<p class="green">Your password has been changed!</p>';
                            echo '<p>Click <a href="login.php">here</a> to login.</p>';
                        } elseif ($_GET['newpwd'] == "empty") {
                            echo '<p class="red">Password is missing.</p>';
                        } elseif ($_GET['newpwd'] == "pwdnotsame") {
                            echo '<p class="red">Password confirm doesn\'t match.</p>';
                        }
                    }
                    showResponse();
                ?>
            </div>
        </div>
	</div>
    
<?php include 'foot.php'; ?>

</body>
