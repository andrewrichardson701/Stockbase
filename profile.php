<?php 
// USER PROFILE PAGE
// SEE USER INFO FROM THE DATABASE. LOCAL USERS CAN ALSO RESET THEIR PASSWORDS HERE
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Profile</title>
</head>
<body>
    <div class="content">
        <?php // dependency PHP

        ?>

        <!-- Header and Nav -->
        <?php include 'nav.php'; ?>
        <!-- End of Header and Nav -->

        <div class="container">
            <h2 class="header-small">Profile</h2>
        </div>
        <?php

        include 'includes/dbh.inc.php';

        $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.theme AS users_theme
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
                    $profile_auth = $row['auth'];
                    $profile_theme = $row['users_theme'];
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
                <?php 
                if ($_SESSION['auth'] == "ldap") {
                    echo('<table class="theme-profileTextColor">
                            <tbody>
                                <input id="profile-id" type="hidden" value="'.$profile_id.'" name="id"/>
                                <tr class="nav-row" id="username">
                                    <td id="username_header" style="width:200px">
                                        <!-- Custodian Colour: #72BE2A -->
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Username:</p>
                                    </td>
                                    <td id="username_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_username.'</p>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:20px" id="firstname">
                                    <td id="firstname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                                    </td>
                                    <td id="firstname_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_first_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:20px" id="lastname">
                                    <td id="lastname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                                    </td>
                                    <td id="lastname_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_last_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:20px" id="email">
                                    <td id="email_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Email:</p>
                                    </td>
                                    <td id="email_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_email.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:20px" id="role">
                                    <td id="role_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Role:</p>
                                    </td>
                                    <td id="role_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_role.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:20px" id="role">
                                    <td id="auth_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Auth:</p>
                                    </td>
                                    <td id="auth_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:30px">
                                    <td id="theme_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Theme:</p>
                                    </td>
                                    <td id="theme_info">
                                        <select class="form-control" name="theme" id="theme-select" onchange="changeTheme()">
                                            <option value="0" '); if ($profile_theme == 0) { echo('selected'); } echo('>Dark (default)</option>
                                            <option value="1" '); if ($profile_theme == 1) { echo('selected'); } echo('>Light</option>
                                            <option value="2" '); if ($profile_theme == 2) { echo('selected'); } echo('>Light Blue</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="nav-row" style="margin-top:30px" id="resync">
                                    <td id="resync_button_td" style="width:200px">
                                        <form enctype="multipart/form-data" action="includes/ldap-resync.inc.php" method="post">
                                            <input type="password" class="form-control" name="password" id="ldap_password" placeholder="Password" />
                                            <input type="submit" style="margin-top:10px" id="resync" name="submit" value="Re-sync" class="btn btn-warning" />
                                        </form>
                                    </td>
                                </tr>
                            ');
                } else {
                    echo('
                    <form id="profileForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    <input id="profile-id" type="hidden" value="'.$profile_id.'" name="id"/>
                    <table>
                        <tbody>
                            <tr class="nav-row" id="username">
                                <td id="username_header" style="width:200px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Username:</p>
                                </td>
                                <td id="username_info">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_username.'</p>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="firstname">
                                <td id="firstname_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                                </td>
                                <td id="firstname_info">
                                <input type="text" class="nav-v-c align-middle form-control" name="first-name" value="'.$profile_first_name.'" placeholder="First Name" required />
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="lastname">
                                <td id="lastname_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                                </td>
                                <td id="lastname_info">
                                    <input type="text" class="nav-v-c align-middle form-control" name="last-name" value="'.$profile_last_name.'" placeholder="Last Name" required />
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="email">
                                <td id="email_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Email:</p>
                                </td>
                                <td id="email_info">
                                <input type="text" class="nav-v-c align-middle form-control" name="email" value="'.$profile_email.'" placeholder="email@domain.com" required />
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="role">
                                <td id="role_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Role:</p>
                                </td>
                                <td id="role_info">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_role.'</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="role">
                                <td id="auth_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Auth:</p>
                                </td>
                                <td id="auth_info">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:30px">
                                <td id="theme_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Theme:</p>
                                </td>
                                <td id="theme_info">
                                    <select class="form-control" name="theme" id="theme-select" onchange="changeTheme()">
                                        <option value="0" '); if ($profile_theme == 0) { echo('selected'); } echo('>Dark (default)</option>
                                        <option value="1" '); if ($profile_theme == 1) { echo('selected'); } echo('>Light</option>
                                        <option value="2" '); if ($profile_theme == 2) { echo('selected'); } echo('>Light Blue</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:30px">
                                <td id="profile-submit" style="width:200px">
                                    <button class="btn btn-success align-bottom" type="submit" name="profile-submit" style="margin-left:0px" value="1">Save</button>
                                </td>
                                <td class="align-middle"><a href="changepassword.php">Change password</a></td>
                            </tr>  
                    ');
                }
                if (isset($_GET['success'])) {
                    if ($_GET['success'] == "PasswordChanged") {
                        echo('<tr class="nav-row" style="margin-top:30px"><td><p class="green">Password Changed Successfully.</p></td></tr>');
                    } elseif ($_GET['success'] == "profileUpdated") {
                        echo('<tr class="nav-row" style="margin-top:30px"><td><p class="green">Profile Updated Successfully.</p></td></tr>');
                    }
                } elseif (isset($_GET['error'])) {
                    echo('<tr class="nav-row" style="margin-top:30px"><td><p class="red">'.$_GET['error'].'</p></td></tr>');
                }
                ?>
                        </tbody>
                    </table>
                <?php if($_SESSION['auth'] == "ldap") { echo("</form>"); } ?>
            </div>

        </div>
    </div>
        
<?php include 'foot.php'; ?>

<script>
    function changeTheme() {
        var select = document.getElementById('theme-select');
        var value = select.value;
        var css = document.getElementById('theme-css');
        var profile_id = document.getElementById('profile-id').value;

        switch(value) {
            case '0':
                var theme = 'dark';
                break;
            case '1': 
                var theme = 'light';
                break;
            case '2': 
                var theme = 'light-blue';
                break;
            default:
                var theme = 'dark';
        }

        // css.href = "./assets/css/theme-"+theme+".css";


        var xhr = new XMLHttpRequest();
            xhr.open("GET", "includes/change-theme.inc.php?change=1&theme="+theme+"&value="+value+"&user-id="+profile_id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the response and populate the shelf select box
                    var re = JSON.parse(xhr.responseText);
                    console.log (re);
                    if (re == 'success') {
                        css.href = './assets/css/theme-'+theme+'.css';
                    } 
                }
            };
            xhr.send();
    }
</script>

</body>