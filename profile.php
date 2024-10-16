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
    <!-- Header and Nav -->
    <?php
        $navHighlight = 'profile'; // for colouring the nav bar link
        include 'nav.php';
    ?>
    <!-- End of Header and Nav -->
    <div class="content">
        <?php // dependency PHP

        ?>
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
                        WHERE users.id=?";
        $stmt_users = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
            echo('<p class="red">ERROR: SQL Error. Table = users. Check '.__FILE__.' at line:'.__LINE__.'.');
            // header("Location: ../index.php?error=sqlerror&table=users");
            // exit();
        } else {
            mysqli_stmt_bind_param($stmt_users, "s", $_SESSION['user_id']);
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
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Username:</p>
                                    </td>
                                    <td id="username_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_username.'</p>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="firstname">
                                    <td id="firstname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">First Name:</p>
                                    </td>
                                    <td id="firstname_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_first_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="lastname">
                                    <td id="lastname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Last Name:</p>
                                    </td>
                                    <td id="lastname_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_last_name.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="email">
                                    <td id="email_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle"" for="admin-banner-color"">Email:</p>
                                    </td>
                                    <td id="email_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_email.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="role">
                                    <td id="role_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle"" for="admin-banner-color"">Role:</p>
                                    </td>
                                    <td id="role_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_role.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="auth">
                                    <td id="auth_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Auth:</p>
                                    </td>
                                    <td id="auth_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                    </td>
                                </tr>');
                                if ($_SESSION['user_id'] != 0) {
                                    $sql_2fa = "SELECT 2fa_enabled FROM users WHERE id=?";
                                    $stmt_2fa = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_2fa, $sql_2fa)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_bind_param($stmt_2fa, "s", $profile_id);
                                        mysqli_stmt_execute($stmt_2fa);
                                        $result_2fa = mysqli_stmt_get_result($stmt_2fa);
                                        $rowCount_2fa = $result_2fa->num_rows;
                                        $row_2fa = $result_2fa->fetch_assoc();
                                        $data_2fa_enabled = $row_2fa['2fa_enabled'];
                                        if ($data_2fa_enabled == 1) {
                                            $checked_2fa_enabled = 'checked';
                                        } else {
                                            $checked_2fa_enabled = '';
                                        }
                                        
                                    }

                                    $sql_2faGlobal = "SELECT 2fa_enabled, 2fa_enforced FROM config WHERE id=1";
                                    $stmt_2faGlobal = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_2faGlobal, $sql_2faGlobal)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_2faGlobal);
                                        $result_2faGlobal = mysqli_stmt_get_result($stmt_2faGlobal);
                                        $rowCount_2faGlobal = $result_2faGlobal->num_rows;
                                        $row_2faGlobal = $result_2faGlobal->fetch_assoc();
                                        $data_2faGlobal_enabled = $row_2faGlobal['2fa_enabled'];
                                        $data_2faGlobal_enforced = $row_2faGlobal['2fa_enforced'];
                                        
                                        if ($data_2faGlobal_enforced == 1) {
                                            $disabled_2fa_class = ' title';
                                            $disabled_2fa_props = ' title="2FA is enforced Globally." disabled';
                                        } elseif ($data_2faGlobal_enforced == 0 && $data_2faGlobal_enabled == 1) {
                                            $disabled_2fa_class = '';
                                            $disabled_2fa_props = '';
                                        } elseif ($data_2faGlobal_enforced == 0 && $data_2faGlobal_enabled == 0) {
                                            $disabled_2fa_class = ' title';
                                            $disabled_2fa_props = ' title="2FA is disabled Globally." disabled';
                                        }
                                        
                                    }

                                    echo('
                                    <tr class="nav-row profile-table-row" id="2fa_enable">
                                        <td id="2fa_enable_header" style="width:200px">
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle'.$disabled_2fa_class.'"'.$disabled_2fa_props.'>Enable 2FA:</p>
                                        </td>
                                        <td id="2fa_enable_selection">
                                            <label class="switch align-middle'.$checked_2fa_enabled.' class="'.$disabled_2fa_class.'"'.$disabled_2fa_props.' style="margin-bottom:0px;margin-top:3px" >
                                                <input type="checkbox" name="enable_2fa" '.$checked_2fa_enabled.' class="'.$disabled_2fa_class.'"'.$disabled_2fa_props.'>
                                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" id="reset2fa-button" type="button" onclick="modalLoadReset2FA('.$profile_id.')">Reset 2FA</button>
                                        </td>
                                    </tr>');
                                }
                                echo('
                                <tr class="nav-row profile-table-row2">
                                    <td id="theme_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Theme:</p>
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
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle viewport-large-block">
                                            <a class="link align-middle" href="theme-test.php">Theme testing</a>
                                        </p>
                                    </td>
                                </tr>
                                <tr class="nav-row  profile-table-row2" id="resync">
                                    <form enctype="multipart/form-data" action="includes/ldap-resync.inc.php" method="post">
                                        <!-- Include CSRF token in the form -->
                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
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
                        <!-- Include CSRF token in the form -->
                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                        <input id="profile-id" type="hidden" value="'.$profile_id.'" name="id"/>
                        <table>
                            <tbody>
                                <tr class="nav-row" id="username">
                                    <td id="username_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Username:</p>
                                    </td>
                                    <td id="username_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_username.'</p>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="firstname">
                                    <td id="firstname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">First Name:</p>
                                    </td>
                                    <td id="firstname_info">
                                    <input type="text" class="nav-v-c align-middle form-control" name="first-name" value="'.htmlspecialchars($profile_first_name, ENT_QUOTES, 'UTF-8').'" placeholder="First Name" required />
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="lastname">
                                    <td id="lastname_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Last Name:</p>
                                    </td>
                                    <td id="lastname_info">
                                        <input type="text" class="nav-v-c align-middle form-control" name="last-name" value="'.htmlspecialchars($profile_last_name, ENT_QUOTES, 'UTF-8').'" placeholder="Last Name" required />
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="email">
                                    <td id="email_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Email:</p>
                                    </td>
                                    <td id="email_info">
                                    <input type="text" class="nav-v-c align-middle form-control" name="email" value="'.$profile_email.'" placeholder="email@domain.com" required />
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="role">
                                    <td id="role_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Role:</p>
                                    </td>
                                    <td id="role_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_role.'</p>
                                    </td>
                                </tr>
                                <tr class="nav-row profile-table-row" id="auth">
                                    <td id="auth_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Auth:</p>
                                    </td>
                                    <td id="auth_info">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">'.$profile_auth.'</p>
                                    </td>
                                </tr>');
                                if ($_SESSION['user_id'] != 0) {
                                    $sql_2fa = "SELECT 2fa_enabled FROM users WHERE id=?";
                                    $stmt_2fa = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_2fa, $sql_2fa)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_bind_param($stmt_2fa, "s", $profile_id);
                                        mysqli_stmt_execute($stmt_2fa);
                                        $result_2fa = mysqli_stmt_get_result($stmt_2fa);
                                        $rowCount_2fa = $result_2fa->num_rows;
                                        $row_2fa = $result_2fa->fetch_assoc();
                                        $data_2fa_enabled = $row_2fa['2fa_enabled'];
                                        if ($data_2fa_enabled == 1) {
                                            $checked_2fa_enabled = 'checked';
                                        } else {
                                            $checked_2fa_enabled = '';
                                        }
                                        
                                    }

                                    $sql_2faGlobal = "SELECT 2fa_enabled, 2fa_enforced FROM config WHERE id=1";
                                    $stmt_2faGlobal = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_2faGlobal, $sql_2faGlobal)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_2faGlobal);
                                        $result_2faGlobal = mysqli_stmt_get_result($stmt_2faGlobal);
                                        $rowCount_2faGlobal = $result_2faGlobal->num_rows;
                                        $row_2faGlobal = $result_2faGlobal->fetch_assoc();
                                        $data_2faGlobal_enabled = $row_2faGlobal['2fa_enabled'];
                                        $data_2faGlobal_enforced = $row_2faGlobal['2fa_enforced'];
                                        
                                        if ($data_2faGlobal_enforced == 1) {
                                            $disabled_2fa_class = ' title';
                                            $disabled_2fa_props = ' title="2FA is enforced Globally." disabled';
                                        } elseif ($data_2faGlobal_enforced == 0 && $data_2faGlobal_enabled == 1) {
                                            $disabled_2fa_class = '';
                                            $disabled_2fa_props = '';
                                        } elseif ($data_2faGlobal_enforced == 0 && $data_2faGlobal_enabled == 0) {
                                            $disabled_2fa_class = ' title';
                                            $disabled_2fa_props = ' title="2FA is disabled Globally." disabled';
                                        }
                                        
                                    }

                                    echo('
                                    <tr class="nav-row profile-table-row" id="2fa_enable">
                                        <td id="2fa_enable_header" style="width:200px">
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle'.$disabled_2fa_class.'"'.$disabled_2fa_props.'>Enable 2FA:</p>
                                        </td>
                                        <td id="2fa_enable_selection"style="margin-right:125px">
                                            <label class="switch align-middle'.$checked_2fa_enabled.' class="'.$disabled_2fa_class.'"'.$disabled_2fa_props.' style="margin-bottom:0px;margin-top:3px" >
                                                <input type="checkbox" name="enable_2fa" '.$checked_2fa_enabled.' class="'.$disabled_2fa_class.'"'.$disabled_2fa_props.'>
                                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" id="reset2fa-button" type="button" onclick="modalLoadReset2FA('.$profile_id.')">Reset 2FA</button>
                                        </td>
                                    </tr>');
                                }
                                echo('
                                <tr class="nav-row profile-table-row2">
                                    <td id="theme_header" style="width:200px">
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Theme:</p>
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
                                        <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle viewport-large-block">
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

                // below commented out due to card reader stuff probably not going to be happening
                // $sql_card = "SELECT card_primary, card_secondary FROM users WHERE id=$profile_id";
                // $stmt_card = mysqli_stmt_init($conn);
                // if (!mysqli_stmt_prepare($stmt_card, $sql_card)) {
                //     echo("ERROR getting entries");
                // } else {
                //     mysqli_stmt_execute($stmt_card);
                //     $result_card = mysqli_stmt_get_result($stmt_card);
                //     $rowCount_card = $result_card->num_rows;
                //     $row_card = $result_card->fetch_assoc(); 
                //     $card_primary = isset($row_card['card_primary']) ? $row_card['card_primary'] : '';
                //     $card_secondary = isset($row_card['card_secondary']) ? $row_card['card_secondary'] : '';
                // }
                // // echo('<tr class="nav-row"><th class="text-center" style="width:180px;margin-top:20px">Swipe card 1</th><th class="text-center" style="width:185px;margin-top:20px">Swipe card 2</th></tr>');
                // echo('<tr class="nav-row" hidden>');
                // if ($card_primary == '' || $card_primary == null) {
                //     echo('<td style="width:200px"><button class="btn btn-success" style="width:180px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'assign\', 1)">Assign swipe card 1</button></td>');
                // } else {
                //     echo('<td style="width:200px"><button class="btn btn-warning" style="width:180px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'re-assign\', 1)">Re-assign swipe card 1</button></td>');
                // }
                // if ($card_secondary == '' || $card_secondary == null) {
                //     echo('<td><button class="btn btn-success" style="width:185px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'assign\', 2)">Assign swipe card 2</button></td>');
                // } else {
                //     echo('<td><button class="btn btn-warning" style="width:185px;margin-top:20px" type="button" onclick="modalLoadSwipe(\'re-assign\', 2)">Re-assign swipe card 2</button></td>');
                // }
                // echo('</tr>');
                // if ($card_primary !== '' || $card_secondary !== '') {
                //     echo ('<tr class="nav-row">
                //     <td style="width:200px">');
                //     if ($card_primary !== '') {
                //         echo('
                //         <form id="cardRemoveForm-1" action="includes/admin.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                //             <!-- Include CSRF token in the form -->
                //             <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                //             <input type="hidden" name="card-remove" value="1" />
                //             <input type="hidden" id="removeCard" name="card" value="1" />
                //             <button class="btn btn-danger" style="width:180px;margin-top:20px" type="submit">De-assign swipe card 1</button>
                //         </form>');
                //     }
                //     echo('</td>
                //     <td>');
                //     if ($card_secondary !== '') {
                //         echo('
                //         <form id="cardRemoveForm-2" action="includes/admin.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                //             <!-- Include CSRF token in the form -->
                //             <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                //             <input type="hidden" name="card-remove" value="1" />
                //             <input type="hidden" id="removeCard" name="card" value="2" />
                //             <button class="btn btn-danger" style="width:185px;margin-top:20px" type="submit">De-assign swipe card 2</button>
                //         </form>');
                //     }
                //     echo('</td>
                //     </tr>');
                // }
                // ?> 
                            <tr id="login_history">
                                <td colspan=100%><p class="gold link" style="margin-top:20px" onclick="modalLoadLoginHistory()">View login history</p></td>
                            </tr>         
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
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="card-modify" value="1" />
                <input type="hidden" id="cardType" name="type" value="" />
                <input type="hidden" id="cardCard" name="card" value="" />
                <input type="hidden" name="cardData" id="cardData" value=""/>
                <h3 class="text-center" id="cardHead"></h3>
                <p class="text-center" style="margin-top:50px">Scan swipe card...</p>
                <!-- <button class="btn btn-danger" onclick="document.getElementById('cardData').value='17322435'">Temp</button> -->
            </form>
        </div>
    </div>
    <div id="modalDivReset2FA" class="modal" style="display: none;">
        <span class="close" onclick="modalCloseReset2FA()">×</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div style="margin:auto;text-align:center;margin-top:10px">
                <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="2fareset_submit" value="set" />
                    <input type="hidden" name="2fa_user_id" id="2fareset_user_id" value=""/>
                    <p>Are you sure you want to reset your 2FA?<br>
                    This will prompt a reset on your next login.</p>
                    <span>
                        <button class="btn btn-danger" type="submit" name="submit" value="1">Reset</button>
                        <button class="btn btn-warning" type="button" onclick="modalCloseReset2FA()">Cancel</button>
                    </span>
                </form>
            </div>
        </div>
    </div>
    <div id="modalDivLoginHistory" class="modal">
        <span class="close" onclick="modalCloseLoginHistory()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <h2 style="margin-left:20px">Login History</h2>
            <div class="well-nopad theme-divBg" style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-top:50px">
                <table class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead class="text-center align-middle theme-tableOuter">
                        <th class="text-center align-middle">id</th>
                        <th class="text-center align-middle">type</th>
                        <th class="text-center align-middle">username</th>
                        <th class="text-center align-middle">user_id</th>
                        <th class="text-center align-middle">ipv4</th>
                        <th class="text-center align-middle">ipv6</th>
                        <th class="text-center align-middle">timestamp</th>
                        <th class="text-center align-middle">auth</th>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_log = "SELECT id, type, username, user_id, INET_NTOA(ipv4) AS ipv4, INET6_NTOA(ipv6) AS ipv6, timestamp, auth
                                    FROM login_log 
                                    WHERE username=?
                                        AND (user_id=? OR user_id IS NULL) 
                                        AND auth=?
                                    ORDER BY id DESC;";
                        $stmt_log = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_log, $sql_log)) {
                            echo("ERROR getting entries");
                        } else {
                            mysqli_stmt_bind_param($stmt_log, "sss", $_SESSION['username'], $_SESSION['user_id'], $_SESSION['auth']);
                            mysqli_stmt_execute($stmt_log);
                            $result_log = mysqli_stmt_get_result($stmt_log);
                            $rowCount_log = $result_log->num_rows;
                            while($row_log = $result_log->fetch_assoc()) {
                                $color = '';
                                if ($row_log['type'] == 'failed') {
                                    $color = 'transactionRemove';
                                } elseif ($row_log['type'] == 'login') {
                                    $color = 'transactionAdd';
                                } elseif ($row_log['type'] == 'logout') {
                                    $color = 'transactionDelete';
                                }
                                echo('<tr class="text-center align-middle '.$color.'">
                                        <td class="text-center align-middle">'.$row_log['id'].'</td>
                                        <td class="text-center align-middle">'.$row_log['type'].'</td>
                                        <td class="text-center align-middle">'.$row_log['username'].'</td>
                                        <td class="text-center align-middle">'.$row_log['user_id'].'</td>
                                        <td class="text-center align-middle">'.$row_log['ipv4'].'</td>
                                        <td class="text-center align-middle">'.$row_log['ipv6'].'</td>
                                        <td class="text-center align-middle">'.$row_log['timestamp'].'</td>
                                        <td class="text-center align-middle">'.$row_log['auth'].'</td>
                                    </tr>');
                            }
                        }
                            
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<!-- Add the JS for the file -->
<script src="assets/js/profile.js"></script>    

<?php include 'foot.php'; ?>

</body>
