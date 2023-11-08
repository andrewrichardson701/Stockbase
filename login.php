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

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Login</title>
</head>
<body>
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
                <p class="red">Demo LDAP username: <or class="blue">inventory</or> password: <or class="blue">DemoPass1!</or></p>
                <form enctype="multipart/form-data" action="includes/login.inc.php" method="post" style="margin-bottom:0px">
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
                            <?php 
                            if ($current_ldap_enabled == 1) {
                                echo('
                                    <div style="margin-left:25px">
                                        <label class="switch" style="margin-bottom: 0px">
                                            <input type="checkbox" name="local" id="local-toggle" value="on">
                                            <span class="sliderBlue round" id="local-span" style="transform: scale(0.6, 0.6)"></span>
                                        </label>
                                    </div>
                                    <label class="nav-div" style="margin-left:0">Local Login<p></p></label>
                                ');
                            } else {
                                echo('<input type="hidden" name="local" value="on" />');
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
                <p><a href="login.php?reset=true" id="password-reset">Forgot password?</a>
                <button class="btn btn-info viewport-small-block" onclick="modalLoadSwipe()">Swipe card login</button>
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
            <form id="locationForm" enctype="multipart/form-data" action="./includes/changepassword.inc.php" method="POST">
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

    <div id="modalDivSwipe" class="modal viewport-small-block"<?php if (isset($_GET['reset']) && $_GET['reset'] == 'true') { echo(' hidden');}?> >
    <!-- <div id="modalDivSwipe" class="modal" style="display: block !important;">  -->
        <span class="close" onclick="modalCloseSwipe()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <form id='cardLoginForm' action="includes/login-card.inc.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="submitHidden" value="1" />
                <input type="hidden" name="cardData" id="cardData" />
                <h3 class="text-center">Present swipe card to login</h3>
                <p class="text-center" style="margin-top:20px">or <or class="link gold" onclick="modalCloseSwipe()">click here</or> to login manually.</p>
                <button class="btn btn-danger" onclick="document.getElementById('cardData').value='17322435'">Temp</button>
            </form>
        </div>
        <script>
            $(document).ready(function() {
                $(document).keypress(function(event) {
                    // Assuming the card input triggers a keypress event
                    var modalDiv = document.getElementById('modalDivSwipe');
                    var computedStyle = window.getComputedStyle(modalDiv);
                    var displayValue = computedStyle.getPropertyValue('display');
                    // console.log(displayValue);
                    if (displayValue !== 'none') {
                        var cardData = String.fromCharCode(event.which);
                        var cardData_input = document.getElementById('cardData');
                        var cardLoginForm = document.getElementById('cardLoginForm');
                        
                        cardData_input.textContent = cardData;
                        cardLoginForm.submit();
                    }
                });
            });
        </script>
    </div>

<script>
var toggle = document.getElementById("local-toggle");
var reset = document.getElementById("password-reset");
if (toggle.checked) {
    reset.hidden=false;
} else {
    reset.hidden=true;
}
toggle.addEventListener('change', (event) => {
    var reset = document.getElementById("password-reset");
    if (event.currentTarget.checked) {
        reset.hidden=false;
    } else {
        reset.hidden=true;
    }
})
</script>
<script>
    function modalLoadSwipe() {
        var modal = document.getElementById("modalDivSwipe");
        modal.style.display = "block";
        modal.hidden = false;
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseSwipe = function() { 
        var modal = document.getElementById("modalDivSwipe");
        modal.style.display = "none";
    }
</script>
<?php include 'foot.php'; ?>

</body>
