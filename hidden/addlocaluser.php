<?php 
session_start();
// if session not set, go to login page
if ((session_status() !== PHP_SESSION_ACTIVE) || (!isset($_SESSION['username'])) || ($_SESSION['username'] === '')) {
    if (substr($_SERVER['REQUEST_URI'], 0, strlen('/inventory/')) == '/inventory/') {
        $_SESSION['redirect_url'] = substr($_SERVER['REQUEST_URI'], strlen('/inventory/'));
    } else {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    }
    // redirect to login page
    if (isset($_SESSION['redirect_url']) && $_SESSION['redirect_url'] !== '') {
        header('Location: ../login.php?url=' . urlencode($_SESSION['redirect_url']));
        exit;
    } else {
        header('Location: ../login.php');
        exit;
    }
    
} 
include '../http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include '../head.php'; // Sets up bootstrap and other dependencies ?>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/inv.css">
    <title>Inventory</title>
    <link rel="stylesheet" href="assets/css/inv.css">
</head>
<body>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <div class="nav inv-nav">
        <div id="nav-row" class="nav-row">
            <div class="logo-div">
                <a href="../">
                    <img class="logo" src="../assets/img/Logo.png" />
                </a>
            </div>
            <div id="profile-div" class="nav-right nav-div">
                <button id="profile" class="nav-v-c nav-trans cw" onclick="window.location='./profile.php';"><?php echo($_SESSION['first_name']); ?></button>
            </div>
            <?php
            if ($_SESSION['role'] == "admin") {
                echo('
                <div id="admin-div" class="nav-div">
                    <button id="admin" class="nav-v-c nav-trans cw" onclick="window.location=\'./admin.php\';">Admin</button>
                </div> 
                ');
            }
            ?>
            <div id="logout-div" class="nav-div">
                <button id="logout" class="nav-v-c nav-trans cw" onclick="window.location='../logout.php';">Logout</button>
            </div> 
        </div>
    </div>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Inventory</h2>
    </div>
    <div class="container" style="margin-top:20px">
        <div class="row">
            <div class="col-md-6" style="margin-left:25px">
                <h3>Add Local User</h3>
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
                        <input type="text" name="role" class="form-control" placeholder="user / admin" required>
                    </div>
                    <div class="nav-row">
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary" value="Add user">
                        </div>
                    </div>
                </form>
                <?php
                    if (isset($_GET["user"])) {
                        if ($_GET["user"] == "added") {
                            echo '<p class="green">Local user added!</p>';
                        }
                    }
                    if (isset($_GET["sqlerror"])) {
                        echo '<p class="red">SQL error, check URL...</p>';
                    }
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
                        } else {
                            echo '<p class="red">Error, check URL...</p>';
                        }
                    }
                ?>
            </div>
        </div>
	</div>

    <script>
        function navPage(url) {
            window.location.href = url;
        }
    </script>

</body>