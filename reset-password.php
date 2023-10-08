<?php 
// DEFAULT LANDING PAGE IF NOT LOGGED IN. 
// ALLOWS USERS TO LOGIN TO THE SYSTEM TO VIEW AND MODIFY CONTENT
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
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

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
                    if (isset($_GET["error"])) {
                        if ($_GET["error"] == "resubmit") {
                            echo '<p class="red">Error occured, please re-submit.</p>';
                        } elseif ($_GET["error"] == "resubmitDate") {
                            echo '<p class="red">Error occured with the date, please re-submit.</p>';
                        } elseif ($_GET["error"] == "resubmitToken") {
                            echo '<p class="red">Error occured with the token, please re-submit.</p>';
                        } elseif ($_GET["error"] == "resubmitResults") {
                            echo '<p class="red">Error occured with the results, please re-submit.</p>';
                        } elseif ($_GET["error"] == "selectorMissing") {
                            echo '<p class="red">Error occured: selector missing.</p>';
                        } elseif ($_GET["error"] == "validatorMissing") {
                            echo '<p class="red">Error occured: validator missing.</p>';
                        }
                    }
                    if (isset($_GET["sqlerror"])) {
                        echo '<p class="red">SQL error. Check URL!</p>';
                    }
                ?>
            </div>
        </div>
	</div>
    
<?php include 'foot.php'; ?>

</body>
