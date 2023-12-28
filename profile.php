<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USER PROFILE PAGE
// SEE USER INFO FROM THE DATABASE. LOCAL USERS CAN ALSO RESET THEIR PASSWORDS HERE
include 'session.php'; // Session setup and redirect if the session is not active 
include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings.
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<!DOCTYPE html>
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
        <?php 
            $navHighlight = 'profile'; // for colouring the nav bar link
            include 'nav.php'; 
        ?>
        <!-- End of Header and Nav -->

        <div class="container">
            <h2 class="header-small">Profile</h2>
        </div>
        <?php

        include 'includes/dbh.inc.php';

        $sql_users = "SELECT users.id as users_id, users.username as username, users.first_name as first_name, 
                            users.last_name as last_name, users.email as email, users.auth as auth, 
                            users_roles.name as role, users.theme_id AS users_theme_id,
                            theme.name as theme_name, theme.file_name as theme_file_name
                        FROM users 
                        INNER JOIN users_roles ON users.role_id = users_roles.id
                        LEFT JOIN theme ON users.theme_id = theme.id
                        WHERE username=?";
        $stmt_users = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
            echo('<p class="red">ERROR: SQL Error. Table = users. Check '.__FILE__.' at line:'.__LINE__.'.');
            // header("Location: ../index.php?error=sqlerror&table=users");
            // exit();
        } else {
            mysqli_stmt_bind_param($stmt_users, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmt_users);
            $result = mysqli_stmt_get_result($stmt_users);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                $userFound = 0;
                echo('<p class="red">ERROR: No user found. Check '.__FILE__.' at line:'.__LINE__.'.');
                // header("Location: ../index.php?sqlerror=noEntries");
                // exit();
            } elseif ($rowCount == 1) {
                while ($row = $result->fetch_assoc()){
                    $profile_id = $row['users_id'];
                    $profile_username = $row['username'];
                    $profile_first_name = $row['first_name'];
                    $profile_last_name = $row['last_name'];
                    $profile_email = $row['email'];
                    $profile_role = ucwords($row['role']);
                    $profile_auth = $row['auth'];
                    $profile_theme_id = $row['users_theme_id'];
                    $profile_theme_name = $row['theme_name'];
                    $profile_theme_file_name = $row['theme_file_name'];
                }  
            } else { // if there are somehow too many rows matching the sql
                echo('<p class="red">ERROR: Multiple entries found. Check '.__FILE__.' at line:'.__LINE__.'.');
                // header("Location: ../index.php?sqlerror=multipleEntries");
                // exit();
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
                                <tr class="nav-row profile-table-row" id="firstname">
                                    <td id="firstname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                                    </td>
                                    <td id="firstname_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_first_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="lastname">
                                    <td id="lastname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                                    </td>
                                    <td id="lastname_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_last_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="email">
                                    <td id="email_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Email:</p>
                                    </td>
                                    <td id="email_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_email.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="role">
                                    <td id="role_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Role:</p>
                                    </td>
                                    <td id="role_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_role.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="role">
                                    <td id="auth_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Auth:</p>
                                    </td>
                                    <td id="auth_info">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row2">
                                    <td id="theme_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Theme:</p>
                                    </td>
                                    <td id="theme_info">
                                        <select class="form-control" name="theme" id="theme-select" onchange="changeTheme()">');
                                        $sql_theme = "SELECT * FROM theme";
                                        $stmt_theme = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
                                            echo("ERROR getting entries");
                                        } else {
                                            mysqli_stmt_execute($stmt_theme);
                                            $result_theme = mysqli_stmt_get_result($stmt_theme);
                                            $rowCount_theme = $result_theme->num_rows;
                                            if ($rowCount_theme < 1) {
                                                echo ("No themes found.");
                                            } else {
                                                while ( $row_theme = $result_theme->fetch_assoc() ) {
                                                    $theme_id = $row_theme['id'];
                                                    $theme_name = $row_theme['name'];
                                                    $theme_file_name = $row_theme['file_name'];
                                                    echo ('<option id="theme-select-option-'.$theme_id.'" title="'.$theme_file_name.'" alt="'.$theme_name.'" value="'.$theme_id.'" '); if ($profile_theme_id == $theme_id) { echo('selected'); } echo('>'.$theme_name); if ($current_default_theme_id == $theme_id) { echo(' (default)'); } echo('</option>');
                                                }
                                            }
                                        }

                                        echo('
                                        </select>
                                    </td>
                                    <td id="theme_header" style="width:200px;padding-left:20px">
                                        <p style="min-height:max-content;margin:0" class="nav-v-c align-middle viewport-large-block">
                                            <a class="link align-middle" href="theme-test.php">Theme testing</a>
                                        </p>
                                    </td>
                                </tr>
                                <tr class="nav-row  profile-table-row2" id="resync">
                                    <form enctype="multipart/form-data" action="includes/ldap-resync.inc.php" method="post">
                                        <td id="resync_button_td" style="width:200px">
                                            <input type="password" style="width:180px" class="form-control" name="password" id="ldap_password" placeholder="Password" />
                                        </td>
                                        <td><input type="submit" id="resync" name="submit" value="Re-sync" class="btn btn-warning" /></td>
                                    </form>
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
                            <tr class="nav-row profile-table-row" id="firstname">
                                <td id="firstname_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                                </td>
                                <td id="firstname_info">
                                <input type="text" class="nav-v-c align-middle form-control" name="first-name" value="'.htmlspecialchars($profile_first_name, ENT_QUOTES, 'UTF-8').'" placeholder="First Name" required />
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row" id="lastname">
                                <td id="lastname_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                                </td>
                                <td id="lastname_info">
                                    <input type="text" class="nav-v-c align-middle form-control" name="last-name" value="'.htmlspecialchars($profile_last_name, ENT_QUOTES, 'UTF-8').'" placeholder="Last Name" required />
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row" id="email">
                                <td id="email_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Email:</p>
                                </td>
                                <td id="email_info">
                                <input type="text" class="nav-v-c align-middle form-control" name="email" value="'.$profile_email.'" placeholder="email@domain.com" required />
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row" id="role">
                                <td id="role_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Role:</p>
                                </td>
                                <td id="role_info">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_role.'</p>
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row" id="auth">
                                <td id="auth_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Auth:</p>
                                </td>
                                <td id="auth_info">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row2">
                                <td id="theme_header" style="width:200px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Theme:</p>
                                </td>
                                <td id="theme_info">
                                <select class="form-control" name="theme" id="theme-select" onchange="changeTheme()">');
                                    $sql_theme = "SELECT * FROM theme";
                                    $stmt_theme = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_theme);
                                        $result_theme = mysqli_stmt_get_result($stmt_theme);
                                        $rowCount_theme = $result_theme->num_rows;
                                        if ($rowCount_theme < 1) {
                                            echo ("No themes found.");
                                        } else {
                                            while ( $row_theme = $result_theme->fetch_assoc() ) {
                                                $theme_id = $row_theme['id'];
                                                $theme_name = $row_theme['name'];
                                                $theme_file_name = $row_theme['file_name'];
                                                echo ('<option id="theme-select-option-'.$theme_id.'" title="'.$theme_file_name.'" alt="'.$theme_name.'" value="'.$theme_id.'" '); if ($profile_theme_id == $theme_id) { echo('selected'); } echo('>'.$theme_name); if ($current_default_theme_id == $theme_id) { echo(' (default)'); } echo('</option>');
                                            }
                                        }
                                    }

                                    echo('
                                    </select>
                                </td>
                                <td id="theme_header" style="width:200px;padding-left:20px">
                                    <p style="min-height:max-content;margin:0" class="nav-v-c align-middle viewport-large-block">
                                        <a class="link align-middle" href="theme-test.php">Theme testing</a>
                                    </p>
                                </td>
                            </tr>
                            <tr class="nav-row profile-table-row2">
                                <td id="profile-submit" style="width:200px">
                                    <button class="btn btn-success align-bottom" type="submit" name="profile-submit" style="margin-left:0px" value="1">Save</button>
                                </td>
                                <td class="align-middle"><a href="changepassword.php">Change password</a></td>
                            </tr>  
                    ');
                }
                $errorPprefix = '<tr class="nav-row" style="margin-top:30px"><td><p class="red">Error: ';
                $errorPsuffix = '</p></td></tr>';
                $successPprefix = '<tr class="nav-row" style="margin-top:30px"><td><p class="green">';
                $successPsuffix = '</p></td></tr>';

                showResponse();

                $sql_card = "SELECT card_primary, card_secondary FROM users WHERE id=$profile_id";
                $stmt_card = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_card, $sql_card)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_card);
                    $result_card = mysqli_stmt_get_result($stmt_card);
                    $rowCount_card = $result_card->num_rows;
                    $row_card = $result_card->fetch_assoc(); 
                    $card_primary = isset($row_card['card_primary']) ? $row_card['card_primary'] : '';
                    $card_secondary = isset($row_card['card_secondary']) ? $row_card['card_secondary'] : '';
                }
                // echo('<tr class="nav-row"><th class="text-center" style="width:180px;margin-top:20px">Swipe card 1</th><th class="text-center" style="width:185px;margin-top:20px">Swipe card 2</th></tr>');
                echo('<tr class="nav-row">');
                if ($card_primary == '' || $card_primary == null) {
                    echo('<td style="width:200px"><button class="btn btn-success" style="width:180px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'assign\', 1)">Assign swipe card 1</button></td>');
                } else {
                    echo('<td style="width:200px"><button class="btn btn-warning" style="width:180px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'re-assign\', 1)">Re-assign swipe card 1</button></td>');
                }
                if ($card_secondary == '' || $card_secondary == null) {
                    echo('<td><button class="btn btn-success" style="width:185px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'assign\', 2)">Assign swipe card 2</button></td>');
                } else {
                    echo('<td><button class="btn btn-warning" style="width:185px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'re-assign\', 2)">Re-assign swipe card 2</button></td>');
                }
                echo('</tr>');
                if ($card_primary !== '' || $card_secondary !== '') {
                    echo ('<tr class="nav-row">
                    <td style="width:200px">');
                    if ($card_primary !== '') {
                        echo('
                        <form id="cardRemoveForm-1" action="includes/admin.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                            <input type="hidden" name="card-remove" value="1" />
                            <input type="hidden" id="removeCard" name="card" value="1" />
                            <button class="btn btn-danger" style="width:180px;margin-top:20px" type="submit">De-assign swipe card 1</button>
                        </form>');
                    }
                    echo('</td>
                    <td>');
                    if ($card_secondary !== '') {
                        echo('
                        <form id="cardRemoveForm-2" action="includes/admin.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                            <input type="hidden" name="card-remove" value="1" />
                            <input type="hidden" id="removeCard" name="card" value="2" />
                            <button class="btn btn-danger" style="width:185px;margin-top:20px" type="submit">De-assign swipe card 2</button>
                        </form>');
                    }
                    echo('</td>
                    </tr>');
                }
                ?>          
                        </tbody>
                    </table>
                <?php if($_SESSION['auth'] == "ldap") { echo("</form>"); } ?>
            </div>

        </div>
    </div>
    <div id="modalDivSwipe" class="modal">
    <!-- <div id="modalDivSwipe" class="modal" style="display: block !important;">  -->
        <span class="close" onclick="modalCloseSwipe()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <form id='cardModifyForm' action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="card-modify" value="1" />
                <input type="hidden" id="cardType" name="type" value="" />
                <input type="hidden" id="cardCard" name="card" value="" />
                <input type="hidden" name="cardData" id="cardData" value=""/>
                <h3 class="text-center" id="cardHead"></h3>
                <p class="text-center" style="margin-top:50px">Scan swipe card...</p>
                <button class="btn btn-danger" onclick="document.getElementById('cardData').value='17322435'">Temp</button>
            </form>
        </div>
        <script>
            $(document).ready(function() {
                $(document).keypress(function(event) {
                    // Assuming the card input triggers a keypress event
                    var cardData = String.fromCharCode(event.which);
                    var cardData_input = document.getElementById('cardData');
                    var cardModifyForm = document.getElementById('cardModifyForm');
                    cardData_input.value = cardData;
                    cardModifyForm.submit();
                });
            });
        </script>
    </div>
        
<?php include 'foot.php'; ?>

<script>
    function changeTheme() {
        var select = document.getElementById('theme-select');
        var value = select.value;
        var css = document.getElementById('theme-css');
        var profile_id = document.getElementById('profile-id').value;
        var theme = document.getElementById('theme-select-option-'+value).title;
        var theme_name = document.getElementById('theme-select-option-'+value).alt;
        // css.href = "./assets/css/theme-"+theme+".css";


        var xhr = new XMLHttpRequest();
            xhr.open("GET", "includes/change-theme.inc.php?change=1&theme_file_name="+theme+"&value="+value+"&theme_name="+theme_name+"&user-id="+profile_id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the response and populate the shelf select box
                    var re = JSON.parse(xhr.responseText);
                    if (re == 'success') {
                        css.href = './assets/css/'+theme;
                    } 
                }
            };
            xhr.send();
    }
</script>
<script>
    function modalLoadSwipe(type, card) {
        var modal = document.getElementById("modalDivSwipe");
        var cardTypeInput = document.getElementById('cardType');
        var cardCardInput = document.getElementById('cardCard');
        var cardHead = document.getElementById('cardHead');
        modal.style.display = "block";
        cardTypeInput.value = type;
        cardCardInput.value = card;
        if (type == 'assign') {
            cardHead.innerText = 'Assign Swipe Card '+card;
        } else {
            cardHead.innerText = 'Re-assign Swipe Card '+card;
        }
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseSwipe = function() { 
        var modal = document.getElementById("modalDivSwipe");
        var cardTypeInput = document.getElementById('cardType');
        var cardCardInput = document.getElementById('cardCard');
        var cardHead = document.getElementById('cardHead');
        modal.style.display = "none";
        cardTypeInput.value = '';
        cardCardInput.value = '';
        cardHead.innerText = '';
    }
</script>

</body>