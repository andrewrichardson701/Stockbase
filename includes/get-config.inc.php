<?php
// GET GLOBAL CONFIG OPTIONS TO BE APPLIED TO NAV AND OTHER OPTIONS ON THE PAGE
// CALLED FROM THE HEAD.PHP PAGE DIRECTLY

$predefined_system_name = "inventory System";
$predefined_config_banner_color = '#E1B12C';
$predefined_config_logo_image = 'default/default-logo.png';
$predefined_config_favicon_image = 'default/default-favicon.png';
$predefined_config_currency = '£';
$predefined_sku_prefix = 'ITEM-';

$config_admin_roles_array = ["Root", "Admin"];

function getWorB($hexCode) {
    // Convert the hex code to an RGB array.
    $hex = str_replace('#', '', $hexCode);
    $rgb = array_map('hexdec', str_split($hex, 2));
    // Calculate the lightness of the color.
    $lightness = ($rgb[0] + $rgb[1] + $rgb[2]) / 3;
  
    // Return black if the color is light, white if it's dark.
    return $lightness > 127 ? "#000000" : "#ffffff";
}

include 'dbh.inc.php';

$sql_config = "SELECT * FROM config ORDER BY id LIMIT 1";
$stmt_config = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_config);
    $result_config = mysqli_stmt_get_result($stmt_config);
    $rowCount_config = $result_config->num_rows;
    if ($rowCount_config < 1) {
        echo ("No custom config found");
        $config_system_name         = '';
        $config_banner_color        = '';
        $config_logo_image          = '';
        $config_favicon_image       = '';
        $config_currency            = '';
        $config_sku_prefix          = '';
        $config_ldap_enabled        = '';
        $config_ldap_username       = '';
        // $config_ldap_password    = '';
        $config_ldap_domain         = '';
        $config_ldap_host           = '';
        $config_ldap_host_secondary = '';
        $config_ldap_port           = '';
        $config_ldap_basedn         = '';
        $config_ldap_usergroup      = '';
        $config_ldap_userfilter     = '';
        $config_smtp_username       = '';
        // $config_smtp_password    = '';
        $config_smtp_encryption     = '';
        $config_smtp_host           = '';
        $config_smtp_port           = '';
        $config_smtp_from_email     = '';
        $config_smtp_from_name      = '';
        $config_smtp_to_email       = '';
    } else {
        while ( $config = $result_config->fetch_assoc() ) {
            $config_system_name         = isset($config['system_name']) ? $config['system_name'] : '';
            $config_banner_color        = isset($config['banner_color']) ? $config['banner_color'] : '';
            $config_logo_image          = isset($config['logo_image']) ? $config['logo_image'] : '';
            $config_favicon_image       = isset($config['favicon_image']) ? $config['favicon_image'] : '';
            $config_currency            = isset($config['currency']) ? $config['currency'] : '';
            $config_sku_prefix          = isset($config['sku_prefix']) ? $config['sku_prefix'] : '';

            $config_ldap_enabled        = isset($config['ldap_enabled']) ? $config['ldap_enabled'] : '';
            $config_ldap_username       = isset($config['ldap_username']) ? $config['ldap_username'] : '';
            // $config_ldap_password     = base64_decode($config['ldap_password']);
            $config_ldap_domain         = isset($config['ldap_domain']) ? $config['ldap_domain'] : '';
            $config_ldap_host           = isset($config['ldap_host']) ? $config['ldap_host'] : '';
            $config_ldap_host_secondary = isset($config['ldap_host_secondary']) ? $config['ldap_host_secondary'] : '';
            $config_ldap_port           = isset($config['ldap_port']) ? $config['ldap_port'] : '';
            $config_ldap_basedn         = isset($config['ldap_basedn']) ? $config['ldap_basedn'] : '';
            $config_ldap_usergroup      = isset($config['ldap_usergroup']) ? $config['ldap_usergroup'] : '';
            $config_ldap_userfilter     = isset($config['ldap_userfilter']) ? $config['ldap_userfilter'] : '';

            $config_smtp_username       = isset($config['smtp_username']) ? $config['smtp_username'] : '';
            // $config_smtp_password       = base64_decode($config['smtp_password']); 
            $config_smtp_encryption     = isset($config['smtp_encryption']) ? $config['smtp_encryption'] : '';
            $config_smtp_host           = isset($config['smtp_host']) ? $config['smtp_encryption'] : '';
            $config_smtp_port           = isset($config['smtp_port']) ? $config['smtp_host'] : '';       
            $config_smtp_from_email     = isset($config['smtp_from_email']) ? $config['smtp_from_email'] : ''; 
            $config_smtp_from_name      = isset($config['smtp_from_name']) ? $config['smtp_from_name'] : ''; 
            $config_smtp_to_email       = isset($config['smtp_to_email']) ? $config['smtp_to_email'] : '';   
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
            $config_d_system_name         = $config_d['system_name'];
            $config_d_banner_color        = $config_d['banner_color'];
            $config_d_logo_image          = $config_d['logo_image'];
            $config_d_favicon_image       = $config_d['favicon_image'];
            $config_d_currency            = $config_d['currency'];
            $config_d_sku_prefix          = $config_d['sku_prefix'];

            $config_d_ldap_enabled        = $config_d['ldap_enabled'];
            $config_d_ldap_username       = $config_d['ldap_username'];
            $config_d_ldap_password       = $config_d['ldap_password'];
            $config_d_ldap_domain         = $config_d['ldap_domain'];
            $config_d_ldap_host           = $config_d['ldap_host'];
            $config_d_ldap_host_secondary = $config_d['ldap_host_secondary'];
            $config_d_ldap_port           = $config_d['ldap_port'];
            $config_d_ldap_basedn         = $config_d['ldap_basedn'];
            $config_d_ldap_usergroup      = $config_d['ldap_usergroup'];
            $config_d_ldap_userfilter     = $config_d['ldap_userfilter'];

            $config_d_smtp_username       = $config_d['smtp_username'];
            $config_d_smtp_password       = $config_d['smtp_password']; 
            $config_d_smtp_encryption     = $config_d['smtp_encryption'];
            $config_d_smtp_host           = $config_d['smtp_host'];
            $config_d_smtp_port           = $config_d['smtp_port'];       
            $config_d_smtp_from_email     = $config_d['smtp_from_email']; 
            $config_d_smtp_from_name      = $config_d['smtp_from_name']; 
            $config_d_smtp_to_email       = $config_d['smtp_to_email']; 
        }
    }
}

$default_system_name         = ($config_d_system_name         !== '' ? $config_d_system_name                 : $predefined_system_name);
$default_banner_color        = ($config_d_banner_color        !== '' ? $config_d_banner_color                : $predefined_config_banner_color);
$default_logo_image          = ($config_d_logo_image          !== '' ? $config_d_logo_image                  : $predefined_config_logo_image);
$default_favicon_image       = ($config_d_favicon_image       !== '' ? $config_d_favicon_image               : $predefined_config_favicon_image);
$default_currency            = ($config_d_currency            !== '' ? $config_d_currency                    : $predefined_config_currency);
$default_sku_prefix          = ($config_d_sku_prefix          !== '' ? $config_d_sku_prefix                  : $predefined_sku_prefix);
$default_banner_text_color   = getWorB($default_banner_color);

$current_system_name         = ($config_system_name           !== '' ? $config_system_name                   : $default_system_name);
$current_banner_color        = ($config_banner_color          !== '' ? $config_banner_color                  : $default_banner_color);
$current_logo_image          = ($config_logo_image            !== '' ? $config_logo_image                    : $default_logo_image);
$current_favicon_image       = ($config_favicon_image         !== '' ? $config_favicon_image                 : $default_favicon_image);
$current_currency            = ($config_currency              !== '' ? $config_currency                      : $default_currency );
$current_sku_prefix          = ($config_sku_prefix            !== '' ? $config_sku_prefix                    : $default_sku_prefix);
$current_banner_text_color   = getWorB($current_banner_color);
  
# ---

$default_ldap_enabled        = ($config_d_ldap_enabled        !== '' ? $config_d_ldap_enabled                : 'MISSING - PLEASE FIX');  
$default_ldap_username       = ($config_d_ldap_username       !== '' ? $config_d_ldap_username               : 'MISSING - PLEASE FIX');
$default_ldap_password       = ($config_d_ldap_password       !== '' ? '<or class="green">Password Set</or>' : 'MISSING - PLEASE FIX');
$default_ldap_domain         = ($config_d_ldap_domain         !== '' ? $config_d_ldap_domain                 : 'MISSING - PLEASE FIX');
$default_ldap_host           = ($config_d_ldap_host           !== '' ? $config_d_ldap_host                   : 'MISSING - PLEASE FIX');
$default_ldap_host_secondary = ($config_d_ldap_host_secondary !== '' ? $config_d_ldap_host_secondary         : 'MISSING - PLEASE FIX');
$default_ldap_port           = ($config_d_ldap_port           !== '' ? $config_d_ldap_port                   : 'MISSING - PLEASE FIX');
$default_ldap_basedn         = ($config_d_ldap_basedn         !== '' ? $config_d_ldap_basedn                 : 'MISSING - PLEASE FIX');    
$default_ldap_usergroup      = ($config_d_ldap_usergroup      !== '' ? $config_d_ldap_usergroup              : 'MISSING - PLEASE FIX');    
$default_ldap_userfilter     = ($config_d_ldap_userfilter     !== '' ? $config_d_ldap_userfilter             : 'MISSING - PLEASE FIX');  

$current_ldap_enabled        = ($config_ldap_enabled          !== '' ? $config_ldap_enabled                  : $default_ldap_enabled);
$current_ldap_username       = ($config_ldap_username         !== '' ? $config_ldap_username                 : $default_ldap_username);
// $current_ldap_password      = ($config_ldap_password         !== '' ? $config_ldap_password                 : $default_ldap_password);
$current_ldap_domain         = ($config_ldap_domain           !== '' ? $config_ldap_domain                   : $default_ldap_domain);
$current_ldap_host           = ($config_ldap_host             !== '' ? $config_ldap_host                     : $default_ldap_host);
$current_ldap_host_secondary = ($config_ldap_host_secondary   !== '' ? $config_ldap_host_secondary           : $default_ldap_host_secondary);
$current_ldap_port           = ($config_ldap_port             !== '' ? $config_ldap_port                     : $default_ldap_port);
$current_ldap_basedn         = ($config_ldap_basedn           !== '' ? $config_ldap_basedn                   : $default_ldap_basedn);    
$current_ldap_usergroup      = ($config_ldap_usergroup        !== '' ? $config_ldap_usergroup                : $default_ldap_usergroup);    
$current_ldap_userfilter     = ($config_ldap_userfilter       !== '' ? $config_ldap_userfilter               : $default_ldap_userfilter);    

# ---

$default_smtp_username       = ($config_d_smtp_username       !== '' ? $config_d_smtp_username               : 'MISSING - PLEASE FIX');
$default_smtp_password       = ($config_d_smtp_password       !== '' ? '<or class="green">Password Set</or>' : 'MISSING - PLEASE FIX');
$default_smtp_encryption     = ($config_d_smtp_encryption     !== '' ? $config_d_smtp_encryption             : 'MISSING - PLEASE FIX');
$default_smtp_host           = ($config_d_smtp_host           !== '' ? $config_d_smtp_host                   : 'MISSING - PLEASE FIX');
$default_smtp_port           = ($config_d_smtp_port           !== '' ? $config_d_smtp_port                   : 'MISSING - PLEASE FIX');
$default_smtp_from_email     = ($config_d_smtp_from_email     !== '' ? $config_d_smtp_from_email             : 'MISSING - PLEASE FIX');    
$default_smtp_from_name      = ($config_d_smtp_from_name      !== '' ? $config_d_smtp_from_name              : 'MISSING - PLEASE FIX');    
$default_smtp_to_email       = ($config_d_smtp_to_email       !== '' ? $config_d_smtp_to_email               : 'MISSING - PLEASE FIX');  

$current_smtp_username       = ($config_smtp_username         !== '' ? $config_smtp_username                 : $default_smtp_username);
// $current_smtp_password     = ($config_smtp_password         !== '' ? $config_smtp_password                 : $default_smtp_password);
$current_smtp_encryption     = ($config_smtp_encryption       !== '' ? $config_smtp_encryption               : $default_smtp_encryption);
$current_smtp_host           = ($config_smtp_host             !== '' ? $config_smtp_host                     : $default_smtp_host);
$current_smtp_port           = ($config_smtp_port             !== '' ? $config_smtp_port                     : $default_smtp_port);
$current_smtp_from_email     = ($config_smtp_from_email       !== '' ? $config_smtp_from_email               : $default_smtp_from_email);    
$current_smtp_from_name      = ($config_smtp_from_name        !== '' ? $config_smtp_from_name                : $default_smtp_from_name);    
$current_smtp_to_email       = ($config_smtp_to_email         !== '' ? $config_smtp_to_email                 : $default_smtp_to_email);    




?>