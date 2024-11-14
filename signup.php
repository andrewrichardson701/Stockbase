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

include 'includes/dbh.inc.php';
$sql = "SELECT signup_allowed FROM config WHERE id = 1";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo('sql error - getting config');
} else {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rowCount = $result->num_rows;
    $row = $result->fetch_Assoc();
    if ($row['signup_allowed'] == 0) {
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
    <title><?php echo ucwords($current_system_name);?> - Sign up</title>
</head>
<body id="body">
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->
    <?php 
    
    include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings. ?>
    
    <div class="container" style="padding-top:135px">
        <div class="row">
            <div class="col-md-6" style="margin-left:25px; margin-right:25px">
                <h3>Sign up</h3>
                <p style="margin-top:2vh;margin-bottom:3vh">Please fill out the below.</p>
                <form id="signupForm" style="margin-bottom:0px" enctype="multipart/form-data" action="./includes/signup.inc.php" method="POST">
                    <!-- Include CSRF token in the form -->
                    <input id="csrf_token" type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="signup_check" value="1">
                    <div class="form-group">
                        <label>Username</label>
                        <div id="username-div" style="position:relative">
                            <input id="username" type="text" name="username" class="form-control" placeholder="username" oninput="checkCredentials(this, 'username')" required>
                            <i class="fa fa-check" id="username-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                            <p class="red" id="username-error" style="margin-left:10px; padding-top:5px" hidden>Username already in use.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <div id="email-div" style="position:relative">
                            <input id="email" type="email" name="email" class="form-control" placeholder="email@example.com" oninput="checkCredentials(this, 'email')" required>
                            <i class="fa fa-check" id="email-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                            <p class="red" id="email-error" style="margin-left:10px; padding-top:5px" hidden>Email address already in use.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>First name</label>
                        <div id="firstname-div" style="position:relative">
                            <input id="firstname" type="firstname" name="firstname" class="form-control" placeholder="John" oninput="checkCredentials(this, 'firstname')" required>
                            <i class="fa fa-check" id="firstname-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Last name</label>
                        <div id="lastname-div" style="position:relative">
                            <input id="lastname" type="lastname" name="lastname" class="form-control" placeholder="Smith" oninput="checkCredentials(this, 'lastname')" required>
                            <i class="fa fa-check" id="lastname-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div id="password-div" style="position:relative">
                            <input id="password" type="password" name="password" class="form-control" placeholder="Password" oninput="checkCredentials(this, 'password')" data-strength required>
                            <meter min="0" max="10" low="6" optimum="10" high="9" id="password-strength-meter" value="0" style="width:100%"></meter>
                            <i class="fa fa-eye" id="password-eye" style="color: black; position:absolute; right:20px;top:12px; cursor: pointer" onclick="togglePassword(this, 'password')" title="Toggle password visibility."></i>
                            <i class="fa fa-check" id="password-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div id="password-div-confirm" style="position:relative">
                            <input id="password-confirm" type="password" name="password_confirm" class="form-control" placeholder="Password" oninput="checkCredentials(this, 'password-confirm')" required>
                            <i class="fa fa-eye" id="password-eye-confirm" style="color: black; position:absolute; right:20px;top:12px; cursor: pointer" onclick="togglePassword(this, 'password-confirm')" title="Toggle password visibility."></i>
                            <i class="fa fa-check" id="password-confirm-check" style="color: green; position:absolute; right:-20px;top:12px;" hidden></i>
                            <p class="red" id="password-confirm-error" style="margin-left:10px; padding-top:5px" hidden>Password does not match.</p>
                            <p class="green" id="password-confirm-success" style="margin-left:10px; padding-top:5px" hidden>Password matches.</p>
                        </div>
                    </div>
                    
                        <div class="nav-row" style="padding-bottom: 30px">
                            <div class="form-group">
                                <input id="submit" type="submit" name="submit" class="btn btn-primary" value="Sign up" disabled>
                            </div>
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
            </div>
        </div>
	</div>
    
<!-- Add the JS for the file -->
<script src='assets/js/signup.js'></script>
<script src='assets/js/credentials.js'></script>

<?php include 'foot.php'; ?>

</body>
