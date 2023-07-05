<?php 
// ADMIN PAGE - SHOWS CONFIGURATION OPTIONS AND ONLY VISIBLE TO ADMIN USERS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 

if ($_SESSION['role'] !== "admin") {
    header("Location: ./login.php");
    exit();
}
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v6.4.0/css/all.css">
    <title>Inventory - Admin</title>
</head>
<body>
    <?php // dependency PHP
    function getComplement($hex) { // get inverted colour
        $hex = str_replace('#', '', $hex);
        $rgb = array_map('hexdec', str_split($hex, 2));
        $complement = array(255 - $rgb[0], 255 - $rgb[1], 255 - $rgb[2]);
        $complementHex = sprintf("%02x%02x%02x", $complement[0], $complement[1], $complement[2]);
        return '#' . $complementHex;
    }
    ?>
    
    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Admin</h2>
    </div>
    <?php
    include 'includes/dbh.inc.php';

    $sql_config = "SELECT * FROM config ORDER BY id LIMIT 1";
    $stmt_config = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
        echo("ERROR getting entries");
    } else {
        mysqli_stmt_execute($stmt_config);
        $result_config = mysqli_stmt_get_result($stmt_config);
        $rowCount_config = $result_config->num_rows;
        if ($rowCount_config < 1) {
            echo ("No cutstom config found");
        } else {
            while ( $config = $result_config->fetch_assoc() ) {
                $config_banner_color = $config['banner_color'];
                $config_logo_image = $config['logo_image'];
                $config_favicon_image = $config['favicon_image'];
                $config_currency = $config['currency'];
                $config_sku_prefix = $config['sku_prefix'];
                $config_ldap_username  = $config['ldap_username'];
                // $config_ldap_password = base64_decode($config['ldap_password']);
                $config_ldap_domain = $config['ldap_domain'];
                $config_ldap_host = $config['ldap_host'];
                $config_ldap_port = $config['ldap_port'];
                $config_ldap_basedn = $config['ldap_basedn'];
                $config_ldap_usergroup = $config['ldap_usergroup'];
                $config_ldap_userfilter = $config['ldap_userfilter'];
            }
        }
    }
    $sql_config_d = "SELECT * FROM config_default ORDER BY id LIMIT 1";
    $stmt_config_d = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_config_d, $sql_config_d)) {
        echo("ERROR getting entries");
    } else {
        mysqli_stmt_execute($stmt_config_d);
        $result_config_d = mysqli_stmt_get_result($stmt_config_d);
        $rowCount_config_d = $result_config_d->num_rows;
        if ($rowCount_config_d < 1) {
            echo ("No default config found");
        } else {
            while ( $config_d = $result_config_d->fetch_assoc() ) {
                $config_d_banner_color = $config_d['banner_color'];
                $config_d_logo_image = $config_d['logo_image'];
                $config_d_favicon_image = $config_d['favicon_image'];
                $config_d_currency = $config_d['currency'];
                $config_d_sku_prefix = $config_d['sku_prefix'];
                $config_d_ldap_username  = $config_d['ldap_username'];
                $config_d_ldap_password = $config_d['ldap_password'];
                $config_d_ldap_domain = $config_d['ldap_domain'];
                $config_d_ldap_host = $config_d['ldap_host'];
                $config_d_ldap_port = $config_d['ldap_port'];
                $config_d_ldap_basedn = $config_d['ldap_basedn'];
                $config_d_ldap_usergroup = $config_d['ldap_usergroup'];
                $config_d_ldap_userfilter = $config_d['ldap_userfilter'];
            }
        }
    }
    $current_banner_color    = ($config_banner_color      !== '' ? $config_banner_color                  : $config_d_banner_color);
    $current_logo_image      = ($config_logo_image        !== '' ? $config_logo_image                    : $config_d_logo_image);
    $current_favicon_image   = ($config_favicon_image     !== '' ? $config_favicon_image                 : $config_d_favicon_image);
    $current_currency        = ($config_currency          !== '' ? $config_currency                      : $config_d_currency );
    $current_sku_prefix      = ($config_sku_prefix        !== '' ? $config_sku_prefix                    : $config_d_sku_prefix);

    $default_banner_color    = ($config_d_banner_color    !== '' ? $config_d_banner_color                : 'MISSING - PLEASE FIX');
    $default_logo_image      = ($config_d_logo_image      !== '' ? $config_d_logo_image                  : 'MISSING - PLEASE FIX');
    $default_favicon_image   = ($config_d_favicon_image   !== '' ? $config_d_favicon_image               : 'MISSING - PLEASE FIX');
    $default_currency        = ($config_d_currency        !== '' ? $config_d_currency                    : 'MISSING - PLEASE FIX');
    $default_sku_prefix      = ($config_d_sku_prefix      !== '' ? $config_d_sku_prefix                    : 'MISSING - PLEASE FIX');

    $current_ldap_username   = ($config_ldap_username     !== '' ? $config_ldap_username                 : $config_d_ldap_username);
    // $current_ldap_password   = ($config_ldap_password     !== '' ? $config_ldap_password                 : $config_d_ldap_password);
    $current_ldap_domain     = ($config_ldap_domain       !== '' ? $config_ldap_domain                   : $config_d_ldap_domain);
    $current_ldap_host       = ($config_ldap_host         !== '' ? $config_ldap_host                     : $config_d_ldap_host);
    $current_ldap_port       = ($config_ldap_port         !== '' ? $config_ldap_port                     : $config_d_ldap_port);
    $current_ldap_basedn     = ($config_ldap_basedn       !== '' ? $config_ldap_basedn                   : $config_d_ldap_basedn);    
    $current_ldap_usergroup  = ($config_ldap_usergroup    !== '' ? $config_ldap_usergroup                : $config_d_ldap_usergroup);    
    $current_ldap_userfilter = ($config_ldap_userfilter   !== '' ? $config_ldap_userfilter               : $config_d_ldap_userfilter);    
    
    $default_ldap_username   = ($config_d_ldap_username   !== '' ? $config_d_ldap_username               : 'MISSING - PLEASE FIX');
    $default_ldap_password   = ($config_d_ldap_password   !== '' ? '<or class="green">Password Set</or>' : 'MISSING - PLEASE FIX');
    $default_ldap_domain     = ($config_d_ldap_domain     !== '' ? $config_d_ldap_domain                 : 'MISSING - PLEASE FIX');
    $default_ldap_host       = ($config_d_ldap_host       !== '' ? $config_d_ldap_host                   : 'MISSING - PLEASE FIX');
    $default_ldap_port       = ($config_d_ldap_port       !== '' ? $config_d_ldap_port                   : 'MISSING - PLEASE FIX');
    $default_ldap_basedn     = ($config_d_ldap_basedn     !== '' ? $config_d_ldap_basedn                 : 'MISSING - PLEASE FIX');    
    $default_ldap_usergroup  = ($config_d_ldap_usergroup  !== '' ? $config_d_ldap_usergroup              : 'MISSING - PLEASE FIX');    
    $default_ldap_userfilter = ($config_d_ldap_userfilter !== '' ? $config_d_ldap_userfilter             : 'MISSING - PLEASE FIX');  

    ?>
    <script> // Toggle hide/show section
        function toggleSection(element, section) {
            var div = document.getElementById(section);
            var icon = element.children[0];
            if (div.hidden == false) {
                div.hidden=true;
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            } else {
                div.hidden=false;
                icon.classList.remove("fa-chevron-down");
                icon.classList.add("fa-chevron-up");
            }
        }
    </script>
    <div class="container">
        <h3 class="clickable" style="font-size:22px" id="global-settings" onclick="toggleSection(this, 'global')">Global Settings <i class="fa-solid fa-chevron-up fa-2xs" style="margin-left:10px"></i></h3>
        <!-- Global Settings -->
        <div style="padding-top: 20px" id="global">
            <form id="globalForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <table id="globalTable">
                    <tbody>
                        <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Change</th>
                            <th style="width:170px;margin-left:25px">Custom</th>
                            <th style="width:120px;margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row" id="banner-color">
                            <td id="banner-color-label" style="width:250px;margin-left:25px">
                                <!-- Custodian Colour: #72BE2A -->
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="banner_color">Banner Colour:</p>
                            </td>
                            <td id="banner-color-picker" style="width:250px">
                                <label class="label-color">
                                    <input class="form-control input-color color" id="banner_color" name="banner_color" placeholder="#XXXXXX" data-value="#xxxxxx" value="<?php echo($current_banner_color); ?>"/>
                                </label>
                            </td>
                            <td style="width:170px;margin-left:25px">
                                <label class="nav-v-c"><span class="uni" style="color:<?php echo(getComplement($current_banner_color)); ?>;background-color:<?php echo($current_banner_color); ?>"><?php echo($current_banner_color); ?></span></label>
                            </td>
                            <td style="width:200px;margin-left:25px">
                                <label class="nav-v-c"><span class="uni" style="color:<?php echo(getComplement($default_banner_color)); ?>;background-color:<?php echo($default_banner_color); ?>"><?php echo($default_banner_color); ?></span></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="banner-logo">
                            <td id="banner-logo-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="logo_image">Banner Logo:</p>
                            </td>
                            <td id="banner-logo-file">
                                <input class="nav-v-c" type="file" style="width: 250px" id="logo_image" name="logo_image">
                            </td>
                            <td style="width:170px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($current_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                            </td>
                            <td style="width:200px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($default_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="favicon-image">
                            <td id="favicon-image-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="favicon_image">Favicon Image:</p>
                            </td>
                            <td id="favicon-image-file">
                                <input class="nav-v-c" type="file" style="width: 250px" id="favicon_image" name="favicon_image">
                            </td>
                            <td style="width:170px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($current_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                            </td>
                            <td style="width:200px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($default_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px">
                            <td id="currency-selector-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="currency_selection">Currency:</p>
                            </td>
                            <td id="currency-selector" style="width:250px">
                                <select id="currency_selection" name="currency_selection" placeholder="£" class="form-control" style="width:150px">
                                    <option alt="Pounds Sterling" value="£" <?php if ($current_currency == "£") { echo("selected"); } ?>>£ (Pound)</option>
                                    <option alt="Dollar"          value="$" <?php if ($current_currency == "$") { echo("selected"); } ?>>$ (Dollar)</option>
                                    <option alt="Euro"            value="€" <?php if ($current_currency == "€") { echo("selected"); } ?>>€ (Euro)</option>
                                    <option alt="Yen"             value="¥" <?php if ($current_currency == "¥") { echo("selected"); } ?>>¥ (Yen)</option>
                                    <option alt="Franc"           value="₣" <?php if ($current_currency == "₣") { echo("selected"); } ?>>₣ (Franc)</option>
                                    <option alt="Rupee"           value="₹" <?php if ($current_currency == "₹") { echo("selected"); } ?>>₹ (Rupee)</option>
                                    <option alt="Mark"            value="₻" <?php if ($current_currency == "₻") { echo("selected"); } ?>>₻ (Mark)</option>
                                    <option alt="Ruouble"         value="₽" <?php if ($current_currency == "₽") { echo("selected"); } ?>>₽ (Ruouble)</option>
                                    <option alt="Lira"            value="₺" <?php if ($current_currency == "₺") { echo("selected"); } ?>>₺ (Lira)</option>
                                </select>
                            </td>
                            <td style="width:170px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_currency); ?></span></label>
                            </td>
                            <td style="width:200px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_currency); ?></span></label>
                            </td>
                        </tr>


                        <tr class="nav-row" style="margin-top:20px">
                            <td id="sku-prefix-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="sku_prefix">SKU Prefix:</p>
                            </td>
                            <td id="sku-prefix-set" style="width:250px">
                                <input class="form-control nav-v-c" type="text" style="width: 150px" id="sku_prefix" name="sku_prefix">
                            </td>
                            <td style="width:170px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_sku_prefix); ?></span></label>
                            </td>
                            <td style="width:200px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_sku_prefix); ?></span></label>
                            </td>
                        </tr>


                        <tr class="nav-row" style="margin-top:20px">
                            <td style="width:250px">
                                <input id="global-submit" type="submit" name="global-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                            </td>
                            <td style="width:250px">
                            </td>
                            <td style="width:170px;margin-left:25px">
                            </td>
                            <td style="width:170px;margin-left:25px">
                                <input id="global-restore-defaults" type="submit" name="global-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'ldap')">LDAP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

        <!-- LDAP Settings -->
        <div style="padding-top: 20px" id="ldap" hidden>
            <?php 
            if (isset($_GET['error'])) {
                echo ('<p id="error-output" class="red" style="margin-left:25px">');
                if ($_GET['error'] == 'emptyFields') { echo('Empty fields in config.'); }
                echo('</p>');
            }
            if (isset($_GET['ldapUpload'])) {
                echo ('<p id="success-output" class="green" style="margin-left:25px">');
                if ($_GET['ldapUpload'] == 'success') { echo('LDAP config uploaded!'); }
                if ($_GET['ldapUpload'] == 'configRestored') { echo('LDAP config restored to defaults!'); }
                echo('</p>');
            }
            ?>
            <form id="ldapForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <table id="ldapTable">
                    <tbody>
                        <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Custom</th>
                            <th style="margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row" id="ldap-auth-username">
                            <td id="ldap-auth-username-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-username">Authentication Username:</p>
                            </td>
                            <td id="ldap-auth-username-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-username" name="auth-username" value="<?php echo(isset($_GET['auth-username']) ? $_GET['auth-username'] : $current_ldap_username); ?>" required>
                            </td>
                            <td id="ldap-auth-username-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-username-default"><?php echo($default_ldap_username); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password">
                            <td id="ldap-auth-password-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-password">Authentication Password:</p>
                            </td>
                            <td id="ldap-auth-password-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password" name="auth-password" value="password" required>
                            </td>
                            <td id="ldap-auth-password-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-password-default" <?php echo(($config_d_ldap_password !== '') ? 'type="password"' : ''); ?>><?php echo($default_ldap_password); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password-confirm">
                            <td id="ldap-auth-password-confirm-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-password-confirm">Confirm Password:</p>
                            </td>
                            <td id="ldap-auth-passowrd-confirm-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password-confirm" name="auth-password-confirm" value="password" required>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-domain">
                            <td id="ldap-auth-domain-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-domain">Domain:</p>
                            </td>
                            <td id="ldap-auth-domain-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-domain" name="auth-domain" value="<?php echo(isset($_GET['auth-domain']) ? $_GET['auth-domain'] : $current_ldap_domain); ?>" required>
                            </td>
                            <td id="ldap-auth-domain-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-domain-default"><?php echo($default_ldap_domain); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                            <td id="ldap-auth-host-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-host">Host:</p>
                            </td>
                            <td id="ldap-auth-host-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host" name="auth-host" value="<?php echo(isset($_GET['auth-host']) ? $_GET['auth-host'] : $current_ldap_host); ?>" required>
                            </td>
                            <td id="ldap-auth-host-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-host-default"><?php echo($default_ldap_host); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-port">
                            <td id="ldap-auth-port-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-port">Port:</p>
                            </td>
                            <td id="ldap-auth-port-input">
                                <input class="form-control nav-v-c" type="number" style="width: 250px" id="auth-port" name="auth-port" value="<?php echo(isset($_GET['auth-port']) ? $_GET['auth-port'] : $current_ldap_port); ?>" required>
                            </td>
                            <td id="ldap-auth-port-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-port-default"><?php echo($default_ldap_port); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-basedn">
                            <td id="ldap-auth-basedn-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-basedn">Base DN:</p>
                            </td>
                            <td id="ldap-auth-basedn-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-basedn" name="auth-basedn" value="<?php echo(isset($_GET['auth-basedn']) ? $_GET['auth-basedn'] : $current_ldap_basedn); ?>">
                            </td>
                            <td id="ldap-auth-basedn-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-basedn-default"><?php echo($default_ldap_basedn); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-usergroup">
                            <td id="ldap-auth-usergroup-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-usergroup">User Group:</p>
                            </td>
                            <td id="ldap-auth-usergroup-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-usergroup" name="auth-usergroup" value="<?php echo(isset($_GET['auth-usergroup']) ? $_GET['auth-usergroup'] : $current_ldap_usergroup); ?>">
                            </td>
                            <td id="ldap-auth-usergroup-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-usergroup-default"><?php echo($default_ldap_usergroup); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-userfilter">
                            <td id="ldap-auth-userfilter-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-userfilter">User Filter:</p>
                            </td>
                            <td id="ldap-auth-userfilter-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-userfilter" name="auth-userfilter" value="<?php echo(isset($_GET['auth-userfilter']) ? $_GET['auth-userfilter'] : $current_ldap_userfilter); ?>">
                            </td>
                            <td id="ldap-auth-userfilter-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-userfilter-default"><?php echo($default_ldap_userfilter); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px">
                            <td style="width:250px">
                                <input id="ldap-submit" type="submit" name="ldap-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                            </td>
                            <td style="width:250px">
                                <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testLDAP()">Test config</a>
                            </td>
                            <td style="margin-left:25px">
                                <input id="ldap-restore-defaults" type="submit" name="ldap-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'smtp')">SMTP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

        <!-- SMTP Settings -->
        <div style="padding-top: 20px" id="smtp" hidden>
            <form id="smtpForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <table id="smtpTable">
                    <tbody>
                        <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Custom</th>
                            <th style="margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row" id="smtp-host-row">
                            <td id="smtp-host-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-host">SMTP Host:</p>
                            </td>
                            <td id="smtp-host-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-host" name="smtp-host" value="mail.ajrich.co.uk" required>
                            </td>
                            <td id="smtp-host-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-host-default">mail.ajrich.co.uk</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-port-row" style="margin-top:20px">
                            <td id="smtp-port-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-port">SMTP Port:</p>
                            </td>
                            <td id="smtp-port-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-port" name="smtp-port" value="587" required>
                            </td>
                            <td id="smtp-port-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-port-default">587</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-encryption-row" style="margin-top:20px">
                            <td id="smtp-encryption-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-encryption">SMTP Encryption:</p>
                            </td>
                            <td id="smtp-encryption-input">
                                <select id="smtp-encryption" name="smtp-encryption" style="width:250px" class="form-control nav-v-c" required>
                                    <option value="none">None</option>
                                    <option value="starttls" selected>STARTTLS</option>
                                    <option value="tls">Transport Layer Security (TLS)</option>
                                    <option value="ssl">Secure Sockets Layer (SSL)</option>
                                </select>
                            </td>
                            <td id="smtp-encryption-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-encryption-default">STARTTLS</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-username-row" style="margin-top:20px">
                            <td id="smtp-username-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-username">SMTP Username:</p>
                            </td>
                            <td id="smtp-username-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-username" name="smtp-username" value="inventory@ajrich.co.uk" required>
                            </td>
                            <td id="smtp-username-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-username-default">inventory@ajrich.co.uk</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-password-row" style="margin-top:20px">
                            <td id="smtp-password-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-password">SMTP Password:</p>
                            </td>
                            <td id="smtp-password-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="smtp-password" name="smtp-password" value="DemoPass1!" required>
                            </td>
                            <td id="smtp-password-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-password-default"><or class="green">Password Set</or></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-from-email-row" style="margin-top:20px">
                            <td id="smtp-from-email-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-from-email">SMTP From Email:</p>
                            </td>
                            <td id="smtp-from-email-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-email" name="smtp-from-email" value="inventory@ajrich.co.uk" required>
                            </td>
                            <td id="smtp-from-email-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-from-email-default">inventory@ajrich.co.uk</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-from-name-row" style="margin-top:20px">
                            <td id="smtp-from-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-from-name">SMTP From Name:</p>
                            </td>
                            <td id="smtp-from-name-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-name" name="smtp-from-name" value="Inventory System" required>
                            </td>
                            <td id="smtp-from-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-from-name-default">Inventory System</p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-backup-to-row" style="margin-top:20px">
                            <td id="smtp-backup-to-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-backup-to">SMTP To Email (Backup):</p>
                            </td>
                            <td id="smtp-backup-to-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-backup-to" name="smtp-backup-to" value="inventory@ajrich.co.uk" required>
                            </td>
                            <td id="smtp-backup-to-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-backup-to-default">inventory@ajrich.co.uk</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px">
                            <td style="width:250px">
                                <input id="smtp-submit" type="submit" name="smtp-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                            </td>
                            <td style="width:250px">
                                <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testSMTP()">Test config</a>
                                <i id="smtp-success-icon" class="fa-solid fa-check fa-lg" style="color: lime; margin-left:10px; display: none;" ></i>
                                <i id="smtp-fail-icon" class="fa-solid fa-xmark fa-lg" style="color: red; margin-left:10px; display: none;" ></i>
                                <i id="smtp-loading-icon" class="fa-solid fa-spinner fa-spin fa-lg" style="color: cyan; margin-left:10px; display: none;" ></i>
                            </td>
                            <td style="margin-left:25px">
                                <input id="smtp-restore-defaults" type="submit" name="smtp-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->
    
    <script>
    function testLDAP() {
        var ldap_username = $('#auth-username').val();
        var ldap_password = $('#auth-password').val();
        var ldap_password_confirm = $('#auth-password-confirm').val();
        var ldap_domain = $('#auth-domain').val();
        var ldap_host = $('#auth-host').val();
        var ldap_port = $('#auth-port').val();
        var ldap_basedn = $('#auth-basedn').val();
        var ldap_usergroup = $('#auth-usergroup').val();
        var ldap_userfilter = $('#auth-userfilter').val();

        var ldapForm = document.getElementById("ldapForm");
        var outputPre = document.getElementById("ldapTestOutput");
        if (outputPre !== null) {
            outputPre.parentNode.removeChild(outputPre)
        }
        var newOutputPre = document.createElement("pre");
        newOutputPre.setAttribute("class", "well-nopad bg-dark");
        newOutputPre.setAttribute("id", "ldapTestOutput");
        newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
        ldapForm.parentNode.insertBefore(newOutputPre, ldapForm.nextSibling);

        $.ajax({
        type: "POST",
        url: "./includes/ldap-test.inc.php",
        data: {ldap_username: ldap_username, 
            ldap_password: ldap_password, 
            ldap_password_confirm: ldap_password_confirm, 
            ldap_domain: ldap_domain,
            ldap_host: ldap_host,
            ldap_port: ldap_port,
            ldap_basedn: ldap_basedn,
            ldap_usergroup: ldap_usergroup,
            ldap_userfilter: ldap_userfilter
        },
        dataType: "json",
        success: function(response){
            var userlist = response;
            var div = document.getElementById('ldapTestOutput');
            // console.log(response);
            if (Array.isArray(userlist)) {
                for (var i = 0; i < userlist.length; i++) {
                var user = userlist[i];
                // console.log(user);
                div.textContent += user+"\n";
                }
            } else {
                div.textContent += userlist+"\n";
            } 
        },
        async: false // <- this turns it into synchronous
        });
    }

    function testSMTP() {
        var smtpLoading = document.getElementById("smtp-loading-icon");
        var smtpSuccess = document.getElementById("smtp-success-icon");
        var smtpFail = document.getElementById("smtp-fail-icon");
        smtpLoading.style.display = "inline-block";
        smtpSuccess.style.display = "none";
        smtpFail.style.display = "none";

        var smtp_host = $('#smtp-host').val();
        var smtp_port = $('#smtp-port').val();
        var smtp_encryption = $('#smtp-encryption').val();
        var smtp_username = $('#smtp-username').val();
        var smtp_password = $('#smtp-password').val();
        var smtp_from_email = $('#smtp-from-email').val();
        var smtp_from_name = $('#smtp-from-name').val();
        var smtp_to_email = $('#smtp-backup-to').val();

        var smtpForm = document.getElementById("smtpForm");
        var outputPre = document.getElementById("smtpTestOutput");
        if (outputPre !== null) {
            outputPre.parentNode.removeChild(outputPre)
        }
        var newOutputPre = document.createElement("pre");
        newOutputPre.setAttribute("class", "well-nopad bg-dark");
        newOutputPre.setAttribute("id", "smtpTestOutput");
        newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
        smtpForm.parentNode.insertBefore(newOutputPre, smtpForm.nextSibling);

        $.ajax({
            type: "POST",
            url: "./includes/smtp-test.inc.php",
            data: {
                smtp_host: smtp_host,
                smtp_port: smtp_port,
                smtp_encryption: smtp_encryption,
                smtp_username: smtp_username,
                smtp_password: smtp_password,
                smtp_from_email: smtp_from_email,
                smtp_from_name: smtp_from_name,
                smtp_to_email: smtp_to_email
            },
            dataType: "html",
            success: function(response) {
                var result = response;
                var div = document.getElementById('smtpTestOutput');

                div.textContent += result + "\n";

                // Continue with the rest of the code once the AJAX request is complete
                processLastLine();
            },
            async: true
        });

        function processLastLine() {
            var div = document.getElementById('smtpTestOutput');

            // Get the content of the <pre> element
            var divContent = div.textContent || div.innerText;
            // Split the content into an array of lines
            var lines = divContent.trim().split('\n');
            // Get the last line
            var lastLine = lines[lines.length - 1];
            // Check if the last line starts with "221"
            if (lastLine.startsWith('221')) {
                // Show some text on the screen or perform any desired action
                console.log('SMTP success code 221 found');
                smtpLoading.style.display = "none";
                smtpSuccess.style.display = "inline";
                smtpFail.style.display = "none";
            } else {
                console.log('SMTP error');
                smtpLoading.style.display = "none";
                smtpSuccess.style.display = "none";
                smtpFail.style.display = "inline";
            }
        }
    }

    </script>
    <script> // color-picker box json - for Admin.php
        $("input.color").each(function() {
            var that = this;
            $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
                that.type = (that.type == "color") ? "text" : "color";
            }));
        }).change(function() {
            $(this).attr("data-value", this.value);
            this.type = "text";
        });
</script>

</body>