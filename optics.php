<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// OPTICS INVENTORY PAGE

include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Optics</title>
</head>
<body>
    <script>
        // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
        if (!<?php echo json_encode(in_array($_SESSION['role'], $config_optics_roles_array)); ?>) {
            window.location.href = './login.php';
        }
    </script>
    <!-- Header and Nav -->
    <?php 
        $navHighlight = 'optics'; // for colouring the nav bar link
        include 'nav.php'; 
    ?>
    <!-- End of Header and Nav -->
    <div class="container">
        <!-- <h2 class="header-small" style="padding-bottom:0px">Optics</h2> -->
        <?php 
        if (isset($_GET['error'])) { echo('<p class="red">Error: '.$_GET['error'].'</p>'); } 
        if (isset($_GET['success'])) { echo('<p class="green">Success: '.$_GET['success'].'</p>'); } 
        ?>
    </div>
    <div class="content" style="padding-top:20px">
        <div class="container" id="selection" style="margin-bottom:15px">
            <?php
            $site = isset($_GET['site']) ? $_GET['site'] : "0";
            $search = isset($_GET['search']) ? $_GET['search'] : "";
            $sort = isset($_GET['sort']) ? $_GET['sort'] : "";
            $deleted = isset($_GET['deleted']) ? $_GET['deleted'] : "";

            // get types from table to create buttons

            $optic_type = isset($_GET['type']) ? $_GET['type'] : 0;
            $optic_speed = isset($_GET['speed']) ? $_GET['speed'] : 0;
            $optic_mode = isset($_GET['mode']) ? $_GET['mode'] : 0;
            $optic_connector = isset($_GET['connector']) ? $_GET['connector'] : 0;
            $site = isset($_GET['site']) ? $_GET['site'] : 0;

            echo('<div class="row centertable" style="max-width:max-content">
                    <div class="col align-middle" style="max-width:max-content">
                        <label class="align-middle" style="padding-right:15px;padding-top:7px">Site:</label>
                        <select name="site" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'site\', this.value))">');
                        $sql_site = "SELECT id, name
                                    FROM site
                                    WHERE site.deleted != 1";
                        $stmt_site = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                            echo('<option value="0" selected>ERROR</option>');
                        } else {
                            mysqli_stmt_execute($stmt_site);
                            $result_site = mysqli_stmt_get_result($stmt_site);
                            $rowCount_site = $result_site->num_rows;
                            if ($rowCount_site < 1) {
                                // error
                                echo('<option value="0" selected>No Sites Found...</option>');
                            } else {
                                echo('<option value="0" '); if ($site == '' || $site == 0) { echo('selected'); } echo('>All</option>');
                                while ($row_site = $result_site->fetch_assoc()) {
                                    $id = $row_site['id'];
                                    $name = $row_site['name'];

                                    echo('<option value="'.$id.'"'); if ($site == $id) { echo('selected'); } echo('>'.$name.'</option>');
                                }
                            }
                        }
                    echo('</select>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <form action="" method="GET" style="display:inline">
                            <label class="align-middle" style="padding-top:7px;padding-right:15px;">Search:</label>
                            <input type="text" name="search" placeholder="Search" class="form-control" style="display:inline !important; width:200px;padding-right:0px"'); if (isset($_GET['search'])) { echo('value="'.$_GET['search'].'"');} echo('>
                            <button id="search-submit" class="btn btn-info" style="margin-top:-3px;vertical-align:middle;padding: 8 6 8 6;opacity:80%;color:black" type="submit">
                                <i class="fa fa-search" style="padding-top:4px"></i>
                            </button>
                        </form>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(\'optics.php\')">
                            <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                        </button>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="add-optic" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { echo ('hidden'); } else { } echo('>
                            <i class="fa fa-plus" style="padding-top:4px"></i> Add Optic
                        </button>
                        <button id="add-optic-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { } else { echo ('hidden'); } echo('>
                            Hide Add Optic
                        </button>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="show-deleted-optics" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 1))" '); if((isset($deleted) && $deleted == 1)) { echo('hidden'); } echo('>
                            View Deleted
                        </button>
                        <button id="hide-deleted-optics" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 0))" '); if(isset($deleted) && ($deleted == 0 || $deleted == '')) { echo('hidden'); } echo('>
                            Hide Deleted
                        </button>
                    </div>');
                echo('</div>
                    <div class="row centertable" style="max-width:max-content; margin-top:10px">');
                    $sql_type = "SELECT id, name FROM optic_type WHERE deleted=0";
                    $stmt_type = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_type, $sql_type)) {
                        echo("ERROR no optic types found");
                    } else {
                        mysqli_stmt_execute($stmt_type);
                        $result_type = mysqli_stmt_get_result($stmt_type);
                        $rowCount_type = $result_type->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Type:</label>');
                            echo ('<select name="type" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'type\', this.value))">');
                                echo ('<option value="0"'); if ($optic_type == 0 || $optic_type == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_type = $result_type->fetch_assoc()) {
                                $type_id = $row_type['id'];
                                $type_name = $row_type['name'];
                                echo ('<option value="'.$type_id.'"'); if ($type_id == $optic_type) { echo(' selected'); } echo('>'.$type_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }
            
                    $sql_speed = "SELECT id, name FROM optic_speed";
                    $stmt_speed = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_speed, $sql_speed)) {
                        echo("ERROR no optic speeds found");
                    } else {
                        mysqli_stmt_execute($stmt_speed);
                        $result_speed = mysqli_stmt_get_result($stmt_speed);
                        $rowCount_speed = $result_speed->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Speed:</label>');
                            echo ('<select name="speed" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'speed\', this.value))">');
                                echo ('<option value="0"'); if ($optic_speed == 0 || $optic_speed == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_speed = $result_speed->fetch_assoc()) {
                                $speed_id = $row_speed['id'];
                                $speed_name = $row_speed['name'];
                                echo ('<option value="'.$speed_id.'"'); if ($speed_id == $optic_speed) { echo(' selected'); } echo('>'.$speed_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }

                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Mode:</label>');
                            echo ('<select name="mode" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'mode\', this.value))">');
                                echo ('<option value="0"'); if ($optic_mode == 0 || $optic_mode == '') { echo(' selected'); } echo('>All</option>');
                                echo ('<option value="SM"'); if ($optic_mode == 'SM') { echo(' selected'); } echo('>SM</option>');
                                echo ('<option value="MM"'); if ($optic_mode == 'MM') { echo(' selected'); } echo('>MM</option>');
                                echo ('<option value="Copper"'); if ($optic_mode == 'Copper') { echo(' selected'); } echo('>Copper</option>');
                            echo('</select>');
                        echo('</div>');

                    $sql_connector = "SELECT id, name FROM optic_connector WHERE deleted=0";
                    $stmt_connector = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_connector, $sql_connector)) {
                        echo("ERROR no optic connectors found");
                    } else {
                        mysqli_stmt_execute($stmt_connector);
                        $result_connector = mysqli_stmt_get_result($stmt_connector);
                        $rowCount_connector = $result_connector->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Connector:</label>');
                            echo ('<select name="connector" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'connector\', this.value))">');
                                echo ('<option value="0"'); if ($optic_connector == 0 || $optic_connector == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_connector = $result_connector->fetch_assoc()) {
                                $connector_id = $row_connector['id'];
                                $connector_name = $row_connector['name'];
                                echo ('<option value="'.$connector_id.'"'); if ($connector_id == $optic_connector) { echo(' selected'); } echo('>'.$connector_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }
                
            echo('</div>');
            
            ?>
        </div>

        <!-- Add optic form section -->
        <?php 
        // url example for return info: https://inventory-dev.ajrich.co.uk/optics.php?add-form=1form-site=1&form-type=1&form-speed=4&form-connector=2&form-mode=SM&form-vendor=1&form-model=TTYTRED
        echo('
        <div class="container" id="add-optic-section" style="margin-bottom:20px" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { } else { echo ('hidden'); } echo('>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new optic</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="optic-add-response" hidden></p>
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                    <div class="row" style="margin-right:25px;margin-top:5px">
                        <div class="col">
                            <div>Serial Number</div>
                            <div><input class="form-control text-center" type="text" id="serial" name="serial" style="min-width:120px" placeholder="Serial" oninput="searchSerial(this.value)"required/></div>
                        </div>
                        <div class="col">
                            <div>Model</div>
                            <div>
                                <input class="form-control text-center" id="model" type="text" list="names" name="model" placeholder="Model" style="min-width:120px" '); if (isset($_GET['form-model'])) { echo ('value="'.$_GET['form-model'].'"'); } echo (' required/>
                                <datalist id="names">');
                                    $sql_model = "SELECT DISTINCT model from optic_item WHERE deleted=0 AND quantity=1 ORDER BY model";
                                    $stmt_model = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_model, $sql_model)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_model);
                                        $result_model = mysqli_stmt_get_result($stmt_model);
                                        $rowCount_model = $result_model->num_rows;
                                        if ($rowCount_model < 1) {
                                        } else {
                                            while( $row_model = $result_model->fetch_assoc() ) {
                                                echo("<option>".$row_model['model']."</option>");
                                            }
                                        }
                                    }
                                echo('
                                </datalist>
                            </div>
                        </div>
                        <div class="col">
                            <div>Vendor</div>
                            <div>
                                <select id="vendor" name="vendor" class="form-control text-center" style="border-color:black;" required>');

                                    $sql_vendor = "SELECT * FROM optic_vendor ORDER BY name";
                                    $stmt_vendor = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_vendor, $sql_vendor)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_vendor);
                                        $result_vendor = mysqli_stmt_get_result($stmt_vendor);
                                        $rowCount_vendor = $result_vendor->num_rows;
                                        if ($rowCount_vendor < 1) {
                                            echo ("<option selected disabled>No Sites Found</option> ");
                                        } else {
                                            echo ("<option selected disabled>Select Vendor</option>");
                                            while( $row_vendor = $result_vendor->fetch_assoc() ) {
                                                echo("<option value='".$row_vendor['id']."'"); if (isset($_GET['form-vendor']) && $_GET['form-vendor'] == $row_vendor['id']) { echo ('selected'); } echo (">".$row_vendor['name']."</option>");
                                            }
                                        }
                                    }   

                                echo('      
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14" onclick="modalLoadNewVendor()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Type</div>
                            <div>
                                <select id="type" name="type" class="form-control text-center" style="border-color:black;"  required>');
                                
                                    $sql_type = "SELECT * FROM optic_type WHERE deleted=0";
                                    $stmt_type = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_type, $sql_type)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_type);
                                        $result_type = mysqli_stmt_get_result($stmt_type);
                                        $rowCount_type = $result_type->num_rows;
                                        if ($rowCount_type < 1) {
                                            echo ("<option selected disabled>No Types Found</option> ");
                                        } else {
                                            echo ('<option value="" selected disabled hidden>Select Type</option>');
                                            while( $row_type = $result_type->fetch_assoc() ) {
                                                echo("<option value='".$row_type['id']."'"); if (isset($_GET['form-type']) && $_GET['form-type'] == $row_type['id']) { echo ('selected'); } echo (">".$row_type['name']."</option>");
                                            }
                                        }
                                    }   

                                echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14" onclick="modalLoadNewType()">Add New</a>
                            </div>
                        </div>
                    </div> 
                
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Speed</div>
                            <div>
                                <select id="speed" name="speed" class="form-control text-center" style="border-color:black;"  required>');
                                
                                    $sql_speed = "SELECT * FROM optic_speed";
                                    $stmt_speed = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_speed, $sql_speed)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_speed);
                                        $result_speed = mysqli_stmt_get_result($stmt_speed);
                                        $rowCount_speed = $result_speed->num_rows;
                                        if ($rowCount_speed < 1) {
                                            echo ("<option selected disabled>No Speeds Found</option> ");
                                        } else {
                                            echo ('<option value="" selected disabled hidden>Select Speed</option>');
                                            while( $row_speed = $result_speed->fetch_assoc() ) {
                                                echo("<option value='".$row_speed['id']."'"); if (isset($_GET['form-speed']) && $_GET['form-speed'] == $row_speed['id']) { echo ('selected'); } echo (">".$row_speed['name']."</option>");
                                            }
                                        }
                                    }   

                                echo(' 
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Connector</div>
                            <div>
                                <select id="connector" name="connector" class="form-control text-center" style="border-color:black;"  required>');
                                
                                $sql_connector = "SELECT * FROM optic_connector WHERE deleted=0";
                                $stmt_connector = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_connector, $sql_connector)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_execute($stmt_connector);
                                    $result_connector = mysqli_stmt_get_result($stmt_connector);
                                    $rowCount_connector = $result_connector->num_rows;
                                    if ($rowCount_connector < 1) {
                                        echo ("<option selected disabled>No Speeds Found</option> ");
                                    } else {
                                        echo ('<option value="" selected disabled hidden>Select Connector</option>');
                                        while( $row_connector = $result_connector->fetch_assoc() ) {
                                            echo("<option value='".$row_connector['id']."'"); if (isset($_GET['form-connector']) && $_GET['form-connector'] == $row_connector['id']) { echo ('selected'); } echo (">".$row_connector['name']."</option>");
                                        }
                                    }
                                }   

                            echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14" onclick="modalLoadNewConnector()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Mode</div>
                            <div>
                                <select id="mode" name="mode" class="form-control text-center" style="border-color:black;"  required>
                                    <option value="" selected disabled hidden>Select Mode</option>
                                    <option value="SM"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'SM') { echo ('selected'); } echo ('>Single-Mode (SM)</option>
                                    <option value="MM"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'MM') { echo ('selected'); } echo ('>Multi-Mode (MM)</option>
                                    <option value="Copper"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'Copper') { echo ('selected'); } echo ('>Copper</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="site" name="site" class="form-control text-center" style="border-color:black;" required>');

                                    $sql_site_cable = "SELECT * FROM site";
                                    $stmt_site_cable = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_site_cable, $sql_site_cable)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_site_cable);
                                        $result_site_cable = mysqli_stmt_get_result($stmt_site_cable);
                                        $rowCount_site_cable = $result_site_cable->num_rows;
                                        if ($rowCount_site_cable < 1) {
                                            echo ("<option selected disabled>No Sites Found</option> ");
                                        } else {
                                            echo ("<option selected disabled>Select Site</option>");
                                            while( $row_site_cable = $result_site_cable->fetch_assoc() ) {
                                                echo("<option value='".$row_site_cable['id']."'"); if (isset($_GET['form-site']) && $_GET['form-site'] == $row_site_cable['id']) { echo ('selected'); } echo (">".$row_site_cable['name']."</option>");
                                            }
                                        }
                                    }   

                                echo('      
                                </select>
                            </div>
                        </div> 
                    </div> 
                    <div class="row align-middle" style="margin-right:25px">
                        <div class="col" style="margin-top:10px">
                            <button id="optic-add-single" class="btn btn-success align-bottom" type="submit" name="add-optic-submit" style="" value="1">Add</button>
                            <button id="optic-add-multiple" class="btn btn-success align-bottom" type="submit" name="add-optic-submit" style="margin-left:20px" value="2">Add Multiple</button>
                        </div>
                    </div>        
                </form>
            </div>
        </div>
        ');
        ?>

        <div class="container">

            <!-- Get Inventory -->
            <?php
            $order = " ORDER BY T.id, V.name, C.id, I.model, I.serial_number";
            switch ($sort) {
                case 'type':
                    $order = " ORDER BY T.name, V.name, C.id, I.model, I.serial_number";
                    break;
                case 'connector':
                    $order = " ORDER BY C.name, T.name, V.name, I.model, I.serial_number";
                    break;
                case 'model':
                    $order = " ORDER BY I.model, T.name, V.name, C.id, I.serial_number";
                    break;
                case 'speed':
                    $order = " ORDER BY S.id, T.name, V.name, C.id, I.model, I.serial_number";
                    break;
                case 'mode':
                    $order = " ORDER BY I.mode, T.name, V.name, C.id, I.model, I.serial_number";
                    break;
                case 'serial':
                    $order = " ORDER BY I.serial_number, T.name, V.name, C.id, I.model";
                    break;
                case 'vendor':
                    $order = " ORDER BY V.name, T.name, C.id, I.model, I.serial_number";
                    break;
                default:
                    $order = " ORDER BY T.id, V.name, C.id, I.model, I.serial_number";
                    break;
            }

            if (isset($deleted)) { 
                if ($deleted == 0 || $deleted == '') {
                    $sql_where = "WHERE I.deleted=0";
                } else {
                    $sql_where = "WHERE I.deleted=1";
                }
            }

            include 'includes/dbh.inc.php';
            $s = 0;
            $sql_inv = "SELECT I.id AS i_id, I.model AS i_model, I.serial_number AS i_serial_number, I.mode AS i_mode, I.quantity AS i_quantity,
                            V.id AS v_id, V.name AS v_name, 
                            T.id AS t_id, T.name AS t_name, 
                            C.id AS c_id, C.name AS c_name,
                            S.id AS s_id, S.name AS s_name,
                            (SELECT count(id) AS count
                                FROM optic_comment
                                WHERE optic_comment.item_id = I.id AND optic_comment.deleted=0) AS comments,
                            site.id AS site_id, site.name AS site_name 
                        FROM optic_item AS I 
                        INNER JOIN optic_vendor AS V on I.vendor_id = V.id 
                        INNER JOIN optic_type AS T ON I.type_id=T.id 
                        INNER JOIN optic_connector AS C ON I.connector_id=C.id 
                        INNER JOIN optic_speed AS S ON I.speed_id=S.id 
                        INNER JOIN site ON I.site_id=site.id ";
            $sql_inv .= $sql_where;
            $sql_inv_add = '';
            if ((int)$site !== 0) { 
                $sql_inv_add  .= " AND site.id=$site"; 
            } 
            if ((int)$optic_type !== 0 ) { 
                if (is_numeric($optic_type)) {
                    $sql_inv_add  .= " AND T.id=$optic_type";
                }
            } 
            if ((int)$optic_speed !== 0 ) { 
                if (is_numeric($optic_speed)) {
                    $sql_inv_add  .= " AND S.id=$optic_speed";
                }
            } 
            if ($optic_mode !== 0 && $optic_mode !== '0') { 
                $sql_inv_add  .= " AND I.mode='$optic_mode'";
            } 
            if ((int)$optic_connector !== 0 ) { 
                if (is_numeric($optic_connector)) {
                    $sql_inv_add  .= " AND C.id=$optic_connector";
                }
            } 
            if ($search !== '') { 
                $name = mysqli_real_escape_string($conn, $search); // escape the special characters
                $sql_inv_add  .= " 
                            AND (I.serial_number LIKE '%$search%' 
                                OR I.model LIKE '%$search%' 
                                OR V.name LIKE '%$search%' 
                                OR T.name LIKE '%$search%' 
                                OR C.name LIKE '%$search%'
                                OR I.mode LIKE '%$search%' 
                                OR S.name LIKE '%$search%'
                                )
                        ";
            }
            $sql_inv .= $sql_inv_add;
            $sql_inv .= $order;
            echo '<pre hidden>'.$sql_inv.'</pre>';
            $stmt_inv = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_inv);
                $result_inv = mysqli_stmt_get_result($stmt_inv);
                $rowCount_inv = $result_inv->num_rows;

                $rowdump = []; // testing

                echo('
                    <div class="container">
                    <hr style="border-color:#9f9d9d; margin-left:10px">
                        <div class="row centertable">
                            <div class="col float-left">
                                Count: <or class="green">'.$rowCount_inv.'</or>
                            </div>
                            <div class="col align-middle" style="max-width:max-content;white-space: nowrap;padding-bottom:10px">
                                <table>
                                    <tr class="align-middle">
                                        <td class="align-middle" style="padding-right:10px">
                                            Sort By:
                                        </td>
                                        <td class="align-middle">
                                            <select name="sort" class="form-control row-dropdown" style="width:max-content;height:25px; padding:0px" onchange="navPage(updateQueryParameter(\'\', \'sort\', this.value))">
                                                <option value="type"'); if ($sort == "type" || $sort == '') { echo(' selected'); } echo('>Type</option>
                                                <option value="connector"'); if ($sort == "connector") { echo(' selected'); } echo('>Connector</option>
                                                <option value="model"'); if ($sort == "model") { echo(' selected'); } echo('>Model</option>
                                                <option value="speed"'); if ($sort == "speed") { echo(' selected'); } echo('>Speed</option>
                                                <option value="mode"'); if ($sort == "mode") { echo(' selected'); } echo('>Mode</option>
                                                <option value="serial"'); if ($sort == "serial") { echo(' selected'); } echo('>Serial</option>
                                                <option value="vendor"'); if ($sort == "vendor") { echo(' selected'); } echo('>Vendor</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <table class="table table-dark theme-table centertable">
                        <thead>
                            <tr class="align-middle text-center theme-tableOuter">
                                <th hidden>ID</th>
                                <th>Type</th>
                                <th>Connector</th>
                                <th>Model</th>
                                <th>Speed</th>
                                <th>Mode</th>
                                <th>Serial Number</th>
                                <th>Vendor</th>
                                <th'); if ((int)$site !== 0) { echo(' hidden'); } echo('>Site</th>
                                <th>Comments</th>
                                <th hidden>Quantity</th>
                                <th></th>
                            <tr>
                        </thead>
                        <tbody>
                ');
                if ($rowCount_inv == 0) {
                    echo('<tr><td colspan=100% class="text-center align-middle">No Optics Found.</td></td>');
                } else {

                    while ($row_inv = $result_inv->fetch_assoc()) {

                        $rowdump[] = $row_inv; // testing

                        $i_id = $row_inv['i_id'];
                        $i_model = $row_inv['i_model'];
                        $i_serial_number = $row_inv['i_serial_number'];
                        $i_model = $row_inv['i_model'];
                        $i_mode = $row_inv['i_mode'];
                        $i_quantity = $row_inv['i_quantity'];
                        $v_id = $row_inv['v_id'];
                        $v_name = $row_inv['v_name'];
                        $t_id = $row_inv['t_id'];
                        $t_name = $row_inv['t_name'];
                        $c_id = $row_inv['c_id'];
                        $c_name = $row_inv['c_name'];
                        $s_id = $row_inv['s_id'];
                        $s_name = $row_inv['s_name'];
                        $i_comments = mysqli_real_escape_string($conn, $row_inv['comments']);
                        $site_id = $row_inv['site_id'];
                        $site_name = $row_inv['site_name'];

                        echo('
                            <tr id="item-'.$i_id.'" class="row-show align-middle text-center'); if ($deleted == 1) { echo(' red'); } echo('">
                                <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                                    <input type="hidden" value="'.$i_id.'" name="id"/>
                                    <td class="align-middle" hidden>'.$i_id.'</td>
                                    <td class="align-middle">'.$t_name.'</td>
                                    <td class="align-middle">'.$c_name.'</td>
                                    <td class="align-middle">'.$i_model.'</td>
                                    <td class="align-middle">'.$s_name.'</td>
                                    <td class="align-middle">'.$i_mode.'</td>
                                    <td class="align-middle">'.$i_serial_number.'</td>
                                    <td class="align-middle">'.$v_name.'</td>
                                    <td class="align-middle link gold" style="white-space: nowrap !important;" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$site_id.'\'))"'); if ((int)$site !== 0) { echo(' hidden'); } echo('>'.$site_name.'</td>
                                    <td hidden class="align-middle text-right'); if ((int)$i_comments > 0) { echo(' clickable gold link" onclick="toggleAddComment(\''.$i_id.'\', 1)"'); } else { echo('" style="color:#8f8f8f"'); } echo('>'.$i_comments.'</or></td>
                                    <td hidden class="align-middle text-left"><button class="btn btn-success" type="button" style="padding: 2px 4px 2px 4px" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')"><i class="fa fa-plus"></i></button></td>
                                    <td class="align-middle">
                                        <div style="position: relative; display: inline-block;">
                                        <i class="'); if ((int)$i_comments > 0) { echo('fa-solid fa-message clickable gold" style="font-size:20; padding:5px"'); } else { echo('fa-regular fa-message clickable gold" style="font-size:18; padding:5px"'); } echo(' onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')"></i>');
                                            if ((int)$i_comments > 0) { 
                                                echo('<span class="uni theme-inv-textColor" style="pointer-events: none; font-size:10; position: absolute; top: 4px; right: 7px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')">'.$i_comments.'</span>');
                                            } else {
                                                echo('<span class="uni gold" style="pointer-events: none; font-size:12; position: absolute; top: 3px; right: 6px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')">+</span>');
                                            }
                                    echo('
                                        </div>
                                    </td>
                                    <td class="align-middle" hidden>'.$i_quantity.'</td>
                                    <td class="align-middle">');
                                    if ($deleted == 1) { 
                                        echo('<button class="btn btn-success" type="submit" name="optic-restore-submit" value="1" title="Restore?"><i class="fa fa-trash-restore"></i></button>');
                                    } else {
                                        echo('<button class="btn btn-danger" type="submit" name="optic-delete-submit" value="1" title="Delete?"><i class="fa fa-trash"></i></button>');
                                    }
                                echo('
                                    </td>
                                </form>
                            </tr>
                            <tr id="item-'.$i_id.'-add-comments" class="row-add-hide align-middle text-center" hidden>
                                <td colspan="100%">
                                    <div class="container">
                                        <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                                            <div class="row centertable" style="max-width:max-content">
                                                <div class="col" style="max-width:max-content">
                                                    <label class="nav-v-c">Comment:</label>
                                                </div>
                                                <div class="col" style="max-width:max-content">
                                                    <input type="hidden" name="id" value="'.$i_id.'" />
                                                    <input name="comment" class="form-control row-dropdown" type="text" style="padding: 2 7 2 7; width:250px" placeholder="Comment..."/>
                                                </div>
                                                <div class="col" style="max-width:max-content">
                                                    <button class="btn btn-success align-bottom" type="submit" name="optic-comment-add" style="margin-left:10px" value="1">Add</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        ');
                        if ((int)$i_comments > 0) {
                            echo('
                            <tr id="item-'.$i_id.'-comments" class="row-hide align-middle text-center" hidden>
                                <td colspan="100%">
                                    <div class="container">
                                        <table class="centertable" style="border: 1px solid #454d55;">
                                            <thead>
                                                <tr class="row-show align-middle text-center">
                                                    <th hidden>ID</th>
                                                    <th>Username</th>
                                                    <th>Comment</th>
                                                    <th>Timestamp</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>');
                                            $sql_comment = "SELECT oc.id AS id, oc.item_id AS item_id, oc.comment AS comment, oc.user_id AS user_id, oc.timestamp AS timestamp, u.username AS username
                                                        FROM optic_comment AS oc
                                                        INNER JOIN users AS u ON u.id=oc.user_id
                                                        WHERE oc.deleted != 1 AND oc.item_id = '$i_id'
                                                        ORDER BY timestamp";
                                            $stmt_comment = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_comment, $sql_comment)) {
                                                //error
                                                echo('<tr><td colspan=100%>Error getting comments...</td></tr>');
                                            } else {
                                                mysqli_stmt_execute($stmt_comment);
                                                $result_comment = mysqli_stmt_get_result($stmt_comment);
                                                $rowCount_comment = $result_comment->num_rows;
                                                if ($rowCount_comment < 1) {
                                                    // somehow no entries found
                                                    echo('<tr><td colspan=100%>No Entries Found...</td></tr>');
                                                } else {
                                                    while ($row_comment = $result_comment->fetch_assoc()) {
                                                        $com_id = $row_comment['id'];
                                                        $com_user_id = $row_comment['user_id'];
                                                        $com_username = $row_comment['username'];
                                                        $com_comment = $row_comment['comment'];
                                                        $com_timestamp = $row_comment['timestamp'];

                                                        echo ('
                                                            <tr id="comment-'.$com_id.'" class="row-show align-middle text-center">
                                                                <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                                                                    <input type="hidden" value="'.$com_id.'" name="id"/>
                                                                    <td class="align-middle" hidden>'.$com_id.'</td>
                                                                    <td class="align-middle">'.$com_username.'</td>
                                                                    <td class="align-middle">'.$com_comment.'</td>
                                                                    <td class="align-middle">'.$com_timestamp.'</td>
                                                                    <td class="align-middle"><button class="btn btn-danger" type="submit" name="optic-comment-delete" value="1"><i class="fa fa-trash"></i></button></td>
                                                                </form>
                                                            </tr>
                                                        ');
                                                    }
                                                }
                                            }
                                        echo('</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            ');
                        }

                        print_r('<div hidden>');
                        print_r($row_inv);
                        print_r('</div>');
                    }
                   
                } 
                echo('
                        </tbody>
                    </table>
                ');
                // testing
                // print_r('<pre style="margin-top:100px">');// testing
                // print_r($sql_inv);// testing
                // print_r('<br><br>');// testing
                // print_r($rowdump);// testing
                // print_r('</pre>');// testing
            }

            ?>
        </div>
    </div>    
    <!-- Modal NewType Div -->
    <div id="modalDivNewType" class="modal">
    <!-- <div id="modalDivNewType" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewType()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="type_name" class="nav-v-c align-middle">Type Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="type_name" name="type_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-type-add" value="Add Type" class="btn btn-success">Add Type</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewType Div -->
    <!-- Modal NewVendor Div -->
    <div id="modalDivNewVendor" class="modal">
    <!-- <div id="modalDivNewVendor" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewVendor()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="vendor_name" class="nav-v-c align-middle">Vendor Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="vendor_name" name="vendor_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-vendor-add" value="Add Vendor" class="btn btn-success">Add Vendor</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewVendor Div -->
    <!-- Modal NewConnector Div -->
    <div id="modalDivNewConnector" class="modal">
    <!-- <div id="modalDivNewConnector" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewConnector()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="connector_name" class="nav-v-c align-middle">Connector Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="connector_name" name="connector_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-connector-add" value="Add Connector" class="btn btn-success">Add Connector</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewConnector Div -->

    <?php include 'foot.php'; ?>

</body>

<script> // toggle hidden row below current
function toggleHidden(id) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-comments';
    var hiddenRow = document.getElementById(hiddenID);
    var allRows = document.getElementsByClassName('row-show');
    var allHiddenRows = document.getElementsByClassName('row-hide');
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else {
        for(var i = 0; i < allHiddenRows.length; i++) {
            allHiddenRows[i].hidden=true;
        } 
        for (var j = 0; j < allRows.length; j++) {
            allRows[j].classList.remove('theme-th-selected');
        }     
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
    }
}
function toggleAddComment(id, com) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-add-comments';
    var hiddenRow = document.getElementById(hiddenID);
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else { 
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
    }
    if (com > 0) {
        toggleHidden(id);
    }
}
</script>
<script>
    function toggleAddDiv() {
        var div = document.getElementById('add-optic-section');
        var addButton = document.getElementById('add-optic');
        var addButtonHide = document.getElementById('add-optic-hide');
        var serial = document.getElementById('serial');
        if (div.hidden === true) {
            div.hidden = false;
            addButton.hidden = true;
            addButtonHide.hidden = false;
            serial.focus(); // for James to use barcode reader - selects the serial number box immediately.
        } else {
            div.hidden = true;
            addButton.hidden = false;
            addButtonHide.hidden = true;
        }

    }
</script>
<script> // MODAL SCRIPT
    // Get the modal
    function modalLoadNewType(property) {
        //get the modal div with the property
        var modal = document.getElementById("modalDivNewType");
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseNewType = function() { 
        var modal = document.getElementById("modalDivNewType");
        modal.style.display = "none";
    }

    function modalLoadNewVendor(property) {
        //get the modal div with the property
        var modal = document.getElementById("modalDivNewVendor");
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseNewVendor = function() { 
        var modal = document.getElementById("modalDivNewVendor");
        modal.style.display = "none";
    }

    function modalLoadNewConnector(property) {
        //get the modal div with the property
        var modal = document.getElementById("modalDivNewConnector");
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseNewConnector = function() { 
        var modal = document.getElementById("modalDivNewConnector");
        modal.style.display = "none";
    }

</script>
<script>
        function searchSerial(search) {
            // Make an AJAX request to retrieve the corresponding sites
            var serialBox = document.getElementById('serial');
            var modelBox = document.getElementById('model');
            var vendorBox = document.getElementById('vendor');
            var typeBox = document.getElementById('type');
            var speedBox = document.getElementById('speed');
            var connectorBox = document.getElementById('connector');
            var modeBox = document.getElementById('mode');
            var siteBox = document.getElementById('site');
            var responseBox = document.getElementById('optic-add-response');
            var btnAddSingle = document.getElementById('optic-add-single');
            var btnAddMultiple = document.getElementById('optic-add-multiple');
            
            responseBox.hidden = true;
            btnAddSingle.disabled = false;
            btnAddMultiple.disabled = false;

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "includes/optics.inc.php?request-optic=1&serial="+search, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the response and populate the shelf select box
                    var data = JSON.parse(xhr.responseText);
                    console.log(data);
                    if (data["skip"] === undefined) {
                        // console.log('noskip');
                        if (data["error"] === undefined) {
                            // console.log('noerror');
                            if (data["success"] !== undefined) {
                                // console.log('success');
                                serialBox.value = data['serial_number'];
                                modelBox.value = data['model'];
                                vendorBox.value = data['vendor_id'];
                                typeBox.value = data['type_id'];
                                speedBox.value = data['speed_id'];
                                connectorBox.value = data['connector_id'];
                                modeBox.value = data['mode'];
                                siteBox.value = data['site_id'];
                                responseBox.hidden = false;
                                responseBox.innerHTML = "<or class='green'>"+data['success']+"</or>";
                                btnAddSingle.disabled = false;
                                btnAddMultiple.disabled = false;
                            }
                        } else {
                            // console.log("error");
                            responseBox.hidden = false;
                            responseBox.innerHTML = "<or class='red'>"+data['error']+"</or>";
                            serialBox.value = data['serial_number'];
                            modelBox.value = data['model'];
                            vendorBox.value = data['vendor_id'];
                            typeBox.value = data['type_id'];
                            speedBox.value = data['speed_id'];
                            connectorBox.value = data['connector_id'];
                            modeBox.value = data['mode'];
                            siteBox.value = data['site_id'];
                            btnAddSingle.disabled = true;
                            btnAddMultiple.disabled = true;
                            
                        }
                        // modelBox.value = '';
                        // vendorBox.value = '';
                        // typeBox.value = '';
                        // speedBox.value = '';
                        // connectorBox.value = '';
                        // modeBox.value = '';
                        // siteBox.value = '';
                        // responseBox.hidden = true;
                    }
                }
            };
            xhr.send();
        }


    </script>