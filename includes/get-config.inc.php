<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// GET GLOBAL CONFIG OPTIONS TO BE APPLIED TO NAV AND OTHER OPTIONS ON THE PAGE
// CALLED FROM THE HEAD.PHP PAGE DIRECTLY

$predefined_system_name = "inventory System";
$predefined_config_banner_color = '#E1B12C';
$predefined_config_logo_image = 'default/default-logo.png';
$predefined_config_favicon_image = 'default/default-favicon.png';
$predefined_config_currency = '£';
$predefined_sku_prefix = 'ITEM-';
$predefined_base_url = 'inventory.ajrich.co.uk';
$predefined_default_theme_id = 1;

function getWorB($hexCode) {
    // Convert the hex code to an RGB array.
    $hex = str_replace('#', '', $hexCode);
    $rgb = array_map('hexdec', str_split($hex, 2));
    // Calculate the lightness of the color.
    $lightness = ($rgb[0] + $rgb[1] + $rgb[2]) / 3;
  
    // Return black if the color is light, white if it's dark.
    return $lightness > 127 ? "#000000" : "#ffffff";
}
// script to get complement colours.
function getComplement($hex) { // get inverted colour
    $hex = str_replace('#', '', $hex);
    $rgb = array_map('hexdec', str_split($hex, 2));
    $complement = array(255 - $rgb[0], 255 - $rgb[1], 255 - $rgb[2]);
    $complementHex = sprintf("%02x%02x%02x", $complement[0], $complement[1], $complement[2]);
    return '#' . $complementHex;
}
// adjust brightness of a hex colour
function adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');
    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }
    $hexCode = array_map('hexdec', str_split($hexCode, 2));
    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }
    return '#' . implode($hexCode);
}


include 'dbh.inc.php';

// get admin capable user roles
$config_admin_roles_array = [];

$sql_roles = "SELECT * FROM users_roles ORDER BY id";
$stmt_roles = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_roles);
    $result_roles = mysqli_stmt_get_result($stmt_roles);
    $rowCount_roles = $result_roles->num_rows;
    if ($rowCount_roles < 1) {
        echo ("No User Roles in the users_roles table. Something is wrong here...");
    } else {
        while ( $role = $result_roles->fetch_assoc() ) {
            if ($role['is_admin'] == 1 || $role['is_root'] == 1) {
                $config_admin_roles_array[] = $role['name'];
            }
        }
    }
}

// get config
$sql_config = "SELECT * FROM config ORDER BY id LIMIT 1";
$stmt_config = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_config);
    $result_config = mysqli_stmt_get_result($stmt_config);
    $rowCount_config = $result_config->num_rows;
    if ($rowCount_config < 1) {
        // echo ("No custom config found");
        $config_system_name         = '';
        $config_banner_color        = '';
        $config_logo_image          = '';
        $config_favicon_image       = '';
        $config_currency            = '';
        $config_sku_prefix          = '';
        $config_base_url            = '';
        $config_default_theme_id    = '';

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

        $config_smtp_enabled        = '';
        $config_smtp_username       = '';
        // $config_smtp_password    = '';
        $config_smtp_encryption     = '';
        $config_smtp_host           = '';
        $config_smtp_port           = '';
        $config_smtp_from_email     = '';
        $config_smtp_from_name      = '';
        $config_smtp_to_email       = '';

        $config_cost_enable_normal  = '';
        $config_cost_enable_cable   = '';

        $config_footer_enable       = '';
        $config_footer_left_enable  = '';
        $config_footer_right_enable = '';
        
    } else {
        while ( $config = $result_config->fetch_assoc() ) {
            $config_system_name         = isset($config['system_name']) ? $config['system_name'] : '';
            $config_banner_color        = isset($config['banner_color']) ? $config['banner_color'] : '';
            $config_logo_image          = isset($config['logo_image']) ? $config['logo_image'] : '';
            $config_favicon_image       = isset($config['favicon_image']) ? $config['favicon_image'] : '';
            $config_currency            = isset($config['currency']) ? $config['currency'] : '';
            $config_sku_prefix          = isset($config['sku_prefix']) ? $config['sku_prefix'] : '';
            $config_base_url            = isset($config['base_url']) ? $config['base_url'] : '';
            $config_default_theme_id    = isset($config['default_theme_id']) ? $config['default_theme_id'] : '';

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

            $config_smtp_enabled        = isset($config['smtp_enabled']) ? $config['smtp_enabled'] : '';
            $config_smtp_username       = isset($config['smtp_username']) ? $config['smtp_username'] : '';
            // $config_smtp_password       = base64_decode($config['smtp_password']); 
            $config_smtp_encryption     = isset($config['smtp_encryption']) ? $config['smtp_encryption'] : '';
            $config_smtp_host           = isset($config['smtp_host']) ? $config['smtp_host'] : '';
            $config_smtp_port           = isset($config['smtp_port']) ? $config['smtp_port'] : '';       
            $config_smtp_from_email     = isset($config['smtp_from_email']) ? $config['smtp_from_email'] : ''; 
            $config_smtp_from_name      = isset($config['smtp_from_name']) ? $config['smtp_from_name'] : ''; 
            $config_smtp_to_email       = isset($config['smtp_to_email']) ? $config['smtp_to_email'] : '';

            $config_cost_enable_normal  = isset($config['cost_enable_normal']) ? $config['cost_enable_normal'] : '';
            $config_cost_enable_cable   = isset($config['cost_enable_cable']) ? $config['cost_enable_cable'] : '';

            $config_footer_enable       = isset($config['footer_enable']) ? $config['footer_enable'] : '';
            $config_footer_left_enable  = isset($config['footer_left_enable']) ? $config['footer_left_enable'] : '';
            $config_footer_right_enable = isset($config['footer_right_enable']) ? $config['footer_right_enable'] : '';
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
            $config_d_base_url            = $config_d['base_url'];
            $config_d_default_theme_id       = $config_d['default_theme_id'];

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

            $config_d_smtp_enabled        = $config_d['smtp_enabled'];
            $config_d_smtp_username       = $config_d['smtp_username'];
            $config_d_smtp_password       = $config_d['smtp_password']; 
            $config_d_smtp_encryption     = $config_d['smtp_encryption'];
            $config_d_smtp_host           = $config_d['smtp_host'];
            $config_d_smtp_port           = $config_d['smtp_port'];       
            $config_d_smtp_from_email     = $config_d['smtp_from_email']; 
            $config_d_smtp_from_name      = $config_d['smtp_from_name']; 
            $config_d_smtp_to_email       = $config_d['smtp_to_email']; 

            $config_d_cost_enable_normal  = $config_d['cost_enable_normal']; 
            $config_d_cost_enable_cable   = $config_d['cost_enable_cable']; 

            $config_d_footer_enable       = $config_d['footer_enable']; 
            $config_d_footer_left_enable  = $config_d['footer_left_enable']; 
            $config_d_footer_right_enable = $config_d['footer_right_enable']; 
        }
    }
}


$default_system_name         = ($config_d_system_name         !== '' ? $config_d_system_name                 : $predefined_system_name);
$default_banner_color        = ($config_d_banner_color        !== '' ? $config_d_banner_color                : $predefined_config_banner_color);
$default_logo_image          = ($config_d_logo_image          !== '' ? $config_d_logo_image                  : $predefined_config_logo_image);
$default_favicon_image       = ($config_d_favicon_image       !== '' ? $config_d_favicon_image               : $predefined_config_favicon_image);
$default_currency            = ($config_d_currency            !== '' ? $config_d_currency                    : $predefined_config_currency);
$default_sku_prefix          = ($config_d_sku_prefix          !== '' ? $config_d_sku_prefix                  : $predefined_sku_prefix);
$default_base_url            = ($config_d_base_url            !== '' ? $config_d_base_url                    : $predefined_base_url);
$default_banner_text_color   = getWorB($default_banner_color);

$current_system_name         = ($config_system_name           !== '' ? $config_system_name                   : $default_system_name);
$current_banner_color        = ($config_banner_color          !== '' ? $config_banner_color                  : $default_banner_color);
$current_logo_image          = ($config_logo_image            !== '' ? $config_logo_image                    : $default_logo_image);
$current_favicon_image       = ($config_favicon_image         !== '' ? $config_favicon_image                 : $default_favicon_image);
$current_currency            = ($config_currency              !== '' ? $config_currency                      : $default_currency );
$current_sku_prefix          = ($config_sku_prefix            !== '' ? $config_sku_prefix                    : $default_sku_prefix);
$current_base_url            = ($config_base_url              !== '' ? $config_base_url                      : $default_base_url);
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

$default_smtp_enabled        = ($config_d_smtp_enabled        !== '' ? $config_d_smtp_enabled                : 'MISSING - PLEASE FIX');
$default_smtp_username       = ($config_d_smtp_username       !== '' ? $config_d_smtp_username               : 'MISSING - PLEASE FIX');
$default_smtp_password       = ($config_d_smtp_password       !== '' ? '<or class="green">Password Set</or>' : 'MISSING - PLEASE FIX');
$default_smtp_encryption     = ($config_d_smtp_encryption     !== '' ? $config_d_smtp_encryption             : 'MISSING - PLEASE FIX');
$default_smtp_host           = ($config_d_smtp_host           !== '' ? $config_d_smtp_host                   : 'MISSING - PLEASE FIX');
$default_smtp_port           = ($config_d_smtp_port           !== '' ? $config_d_smtp_port                   : 'MISSING - PLEASE FIX');
$default_smtp_from_email     = ($config_d_smtp_from_email     !== '' ? $config_d_smtp_from_email             : 'MISSING - PLEASE FIX');    
$default_smtp_from_name      = ($config_d_smtp_from_name      !== '' ? $config_d_smtp_from_name              : 'MISSING - PLEASE FIX');    
$default_smtp_to_email       = ($config_d_smtp_to_email       !== '' ? $config_d_smtp_to_email               : 'MISSING - PLEASE FIX');

$current_smtp_enabled        = ($config_smtp_enabled          !== '' ? $config_smtp_enabled                  : $default_smtp_enabled);
$current_smtp_username       = ($config_smtp_username         !== '' ? $config_smtp_username                 : $default_smtp_username);
// $current_smtp_password     = ($config_smtp_password         !== '' ? $config_smtp_password                 : $default_smtp_password);
$current_smtp_encryption     = ($config_smtp_encryption       !== '' ? $config_smtp_encryption               : $default_smtp_encryption);
$current_smtp_host           = ($config_smtp_host             !== '' ? $config_smtp_host                     : $default_smtp_host);
$current_smtp_port           = ($config_smtp_port             !== '' ? $config_smtp_port                     : $default_smtp_port);
$current_smtp_from_email     = ($config_smtp_from_email       !== '' ? $config_smtp_from_email               : $default_smtp_from_email);    
$current_smtp_from_name      = ($config_smtp_from_name        !== '' ? $config_smtp_from_name                : $default_smtp_from_name);    
$current_smtp_to_email       = ($config_smtp_to_email         !== '' ? $config_smtp_to_email                 : $default_smtp_to_email);    

# ---

$default_default_theme_id    = ($config_d_default_theme_id    !== '' ? $config_d_default_theme_id            : $predefined_default_theme_id); 

$current_default_theme_id    = ($config_default_theme_id      !== '' ? $config_default_theme_id              : $default_default_theme_id);

# ---

$default_cost_enable_normal  = ($config_d_cost_enable_normal  !== '' ? $config_d_cost_enable_normal          : 1); 
$default_cost_enable_cable   = ($config_d_cost_enable_cable   !== '' ? $config_d_cost_enable_cable           : 1); 

$current_cost_enable_normal  = ($config_cost_enable_normal    !== '' ? $config_cost_enable_normal            : $default_cost_enable_normal);
$current_cost_enable_cable   = ($config_cost_enable_cable     !== '' ? $config_cost_enable_cable             : $default_cost_enable_cable);

# ---

$default_footer_enable        = ($config_d_footer_enable       !== '' ? $config_d_footer_enable               : 1); 
$default_footer_left_enable   = ($config_d_footer_left_enable  !== '' ? $config_d_footer_left_enable          : 1); 
$default_footer_right_enable  = ($config_d_footer_right_enable !== '' ? $config_d_footer_right_enable         : 1); 

$current_footer_enable        = ($config_footer_enable         !== '' ? $config_footer_enable                 : $default_footer_enable );
$current_footer_left_enable   = ($config_footer_left_enable    !== '' ? $config_footer_left_enable            : $default_footer_left_enable);
$current_footer_right_enable  = ($config_footer_right_enable   !== '' ? $config_footer_right_enable           : $default_footer_right_enable);


// get theme info for defaults
$sql_theme = "SELECT * FROM theme WHERE id=$current_default_theme_id";
$stmt_theme = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_theme);
    $result_theme = mysqli_stmt_get_result($stmt_theme);
    $rowCount_theme = $result_theme->num_rows;
    if ($rowCount_theme < 1) {
        echo ("No themes found for id: $current_default_theme_id");
    } else {
        while ( $row_theme = $result_theme->fetch_assoc() ) {
            $c_theme_id = $row_theme['id'];
            $c_theme_name = $row_theme['name'];
            $c_theme_file_name = $row_theme['file_name'];
        }
    }
}

$sql_theme_d = "SELECT * FROM theme WHERE id=$default_default_theme_id";
$stmt_theme_d = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_theme_d, $sql_theme_d)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_theme_d);
    $result_theme_d = mysqli_stmt_get_result($stmt_theme_d);
    $rowCount_theme_d = $result_theme_d->num_rows;
    if ($rowCount_theme_d < 1) {
        echo ("No themes found for id: $default_default_theme_id");
    } else {
        while ( $row_theme_d = $result_theme_d->fetch_assoc() ) {
            $c_d_theme_id = $row_theme_d['id'];
            $c_d_theme_name = $row_theme_d['name'];
            $c_d_theme_file_name = $row_theme_d['file_name'];
        }
    }
}


$current_default_theme_name = $c_theme_name;
$current_default_theme_file_name = $c_theme_file_name;


$default_default_theme_name = $c_d_theme_name;
$default_default_theme_file_name = $c_d_theme_file_name;


?>