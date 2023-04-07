<?php

$predfined_config_banner_color = '#E1B12C';
$predfined_config_logo_image = 'default/default-logo.png';
$predfined_config_favicon_image = 'default/default-favicon.png';

include 'dbh.inc.php';

$sql_config = "SELECT banner_color, logo_image, favicon_image FROM config ORDER BY id LIMIT 1";
$stmt_config = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_config);
    $result_config = mysqli_stmt_get_result($stmt_config);
    $rowCount_config = $result_config->num_rows;
    if ($rowCount_config < 1) {
        // DO NOTHING
    } else {
        while ( $config = $result_config->fetch_assoc() ) {
            $config_banner_color = $config['banner_color'];
            $config_logo_image = $config['logo_image'];
            $config_favicon_image = $config['favicon_image'];
        }
    }
}

$sql_config_d = "SELECT banner_color, logo_image, favicon_image FROM config_default ORDER BY id LIMIT 1";
$stmt_config_d = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt_config_d, $sql_config_d)) {
    echo("ERROR getting entries");
} else {
    mysqli_stmt_execute($stmt_config_d);
    $result_config_d = mysqli_stmt_get_result($stmt_config_d);
    $rowCount_config_d = $result_config_d->num_rows;
    if ($rowCount_config_d < 1) {
        // DO NOTHING
    } else {
        while ( $config_d = $result_config_d->fetch_assoc() ) {
            $config_d_banner_color = $config_d['banner_color'];
            $config_d_logo_image = $config_d['logo_image'];
            $config_d_favicon_image = $config_d['favicon_image'];
        }
    }
}

if ($config_d_banner_color  === '') { $config_d_banner_color  = $predfined_config_banner_color;  }
if ($config_d_logo_image    === '') { $config_d_logo_image    = $predfined_config_logo_image;    }
if ($config_d_favicon_image === '') { $config_d_favicon_image = $predfined_config_favicon_image; }

if ($config_banner_color  === '') { $config_banner_color  = $config_d_banner_color;  }
if ($config_logo_image    === '') { $config_logo_image    = $config_d_logo_image;    }
if ($config_favicon_image === '') { $config_favicon_image = $config_d_favicon_image; }

?>