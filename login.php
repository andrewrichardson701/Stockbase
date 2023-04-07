<?php 
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
    <title>Inventory</title>
</head>
<body>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Inventory</h2>
    </div>
    <div class="container" style="margin-top:20px">
        <div class="row">
            <div class="col-md-6" style="margin-left:25px">
                <h3>Login</h3>
                <p>Please input your credentials to login.</p>
                <p class="red">Demo LDAP username: <or class="blue">inventory</or> password: <or class="blue">DemoPass1!</or></p>
                <form enctype="multipart/form-data" action="includes/login.inc.php" method="post">
                    <div class="form-group">
                        <label>Username / Email Address</label>
                        <input type="username" name="username" class="form-control" placeholder="username@domain.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="nav-row">
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary" value="Login">
                        </div>
                        <div style="margin-left:25px">
                            <label class="switch" style="margin-bottom: 0px">
                                <input type="checkbox" name="local" id="local-toggle" value="on">
                                <span class="sliderBlue round" style="transform: scale(0.6, 0.6)"></span>
                            </label>
                        </div>
                        <label class="nav-div" style="margin-left:0">Local Login</p>
                    </div>
                </form>
                <?php
                    if (isset($_GET["newpwd"])) {
                        if ($_GET["newpwd"] == "passwordupdated") {
                            echo '<p class="green">Your password has been changed!</p>';
                        }
                    }
                    if (isset($_GET["error"])) {
                        if ($_GET["error"] == "invalidCredentials") {
                            echo '<p class="red">Invalid Username / Password...</p>';
                        } elseif ($_GET["error"] == "submitNotSet") {
                            echo '<p class="red">Form submission required.</p>';
                        } elseif ($_GET["error"] == "sqlerror") {
                            echo '<p class="red">SQL error.</p>';
                        } elseif ($_GET["error"] == "emptyfields") {
                            echo '<p class="red">Missing fields...</p>';
                        }
                    }
                    if (isset($_GET["sqlerror"])) {
                        echo '<p class="red">SQL error. Check URL!</p>';
                    }
                ?>
                <p><a href="reset-password.php">Forgot password?</a>
            </div>
        </div>
	</div>

    <script>
        function navPage(url) {
            window.location.href = url;
        }
    </script>

</body>