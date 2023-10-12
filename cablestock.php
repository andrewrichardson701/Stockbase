<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Fixed Cable Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->
    <div class="content">
        <?php
        // functions
        function containsColorName($inputString) {
            // List of color names (you can add more colors as needed)
            $colorNames = array(
                'red', 'green', 'blue', 'yellow', 'orange', 'purple', 'pink',
                'brown', 'white', 'black', 'gray', 'cyan', 'magenta', 'gold', 'aqua'
            );
        
            // Convert the input string to lowercase
            $lowercaseInput = strtolower($inputString);
        
            // Loop through the color names and check if any of them are present in the input string
            foreach ($colorNames as $color) {
                if (strpos($lowercaseInput, $color) !== false) {
                    return $color; // Found a color name in the input string
                }
            }
        
            return false; // No color name found in the input string
        }

        function getColorHexFromName($colorName) {
            // Convert the color name to lowercase for case-insensitive matching
            $colorNameLower = strtolower($colorName);
        
            // Associative array with HTML color names and their corresponding HEX values
            $htmlColors = array(
                'aqua' => '#00ffff',
                'black' => '#000000',
                'blue' => '#0000ff',
                'fuchsia' => '#ff00ff',
                'gray' => '#808080',
                'green' => '#008000',
                'lime' => '#00ff00',
                'maroon' => '#800000',
                'navy' => '#000080',
                'olive' => '#808000',
                'purple' => '#800080',
                'red' => '#ff0000',
                'silver' => '#c0c0c0',
                'teal' => '#008080',
                'white' => '#ffffff',
                'yellow' => '#ffff00'
                // Add more color mappings as needed
            );
        
            // Check if the color name exists in the color map
            if (isset($htmlColors[$colorNameLower])) {
                return $htmlColors[$colorNameLower];
            } else {
                return false; // Color name not found in the color map
            }
        }
        ?>
        
        <!-- Get Inventory -->
        <?php
        $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
        $site = isset($_GET['site']) ? $_GET['site'] : "0";
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $cableType = isset($_GET['cable']) ? $_GET['cable'] : 'copper';

        $site_names_array = [];
        $area_names_array = [];

        include 'includes/dbh.inc.php';
        $s = 0;
        $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, 
                        stock.sku as stock_sku, stock.min_stock as stock_min_stock, stock.is_cable as stock_is_cable,
                        cable_item.id as cable_item_id, cable_item.stock_id as cable_item_stock_id, cable_item.quantity as cable_item_quantity,
                        cable_item.cost AS cable_item_cost, cable_item.shelf_id AS cable_item_site_id, cable_item.type_id as cable_item_type_id,
                        cable_types.id AS cable_types_id, cable_types.name AS cable_types_name, cable_types.description AS cable_types_description,
                        cable_types.parent AS cable_types_parent,
                        site.id AS site_id, site.name AS site_name,
                        stock_img_image.stock_img_image
                    FROM cable_item
                    LEFT JOIN stock ON cable_item.stock_id=stock.id 
                    LEFT JOIN shelf ON cable_item.shelf_id=shelf.id
                    LEFT JOIN area ON shelf.area_id=area.id
                    LEFT JOIN site ON area.site_id=site.id
                    LEFT JOIN cable_types ON cable_item.type_id=cable_types.id
                    LEFT JOIN (
                            SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                            FROM stock_img
                            GROUP BY stock_img.stock_id
                        ) AS stock_img_image
                        ON stock_img_image.stock_id = stock.id
                    WHERE stock.is_cable=1";
        $sql_inv_add = '';
        if ($site !== '0') { 
            $sql_inv_add  .= " AND site.id=$site"; $s++; 
        } 
        if ($showOOS == 0) { 
            $sql_inv_add  .= " AND cable_item.quantity!=0";
        } 
        if ($name !== '') { 
            $sql_inv_add  .= " AND stock.name LIKE '%$name%'";
        }
        if (isset($cableType)) {
            if ($name == '' || $name == null) {
                $type = ucwords($cableType);
                $sql_inv_add .= " AND cable_types.parent = '$type'";
            }
        }    
        $sql_inv .= $sql_inv_add;
        $sql_inv .= " GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
                        cable_item_id, 
                        site_id, site_name, stock_img_image.stock_img_image";
        $sql_inv .= " ORDER BY stock.name;";
        echo '<pre hidden>'.$sql_inv.'</pre>';
        $stmt_inv = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_inv);
            $result_inv = mysqli_stmt_get_result($stmt_inv);
            $rowCount_inv = $result_inv->num_rows;

            // GET SITE AND AREA VALUES
            //site
            include 'includes/dbh.inc.php';

            $sql_site = "SELECT DISTINCT site.id, site.name, site.description
                        FROM site 
                        ORDER BY site.id";
            $stmt_site = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_site);
                $result_site = mysqli_stmt_get_result($stmt_site);
                $rowCount_site = $result_site->num_rows;
                if ($rowCount_site < 1) {
                    echo ("No sites found");
                } else {
                    
                    while( $row = $result_site->fetch_assoc() ) {
                        $site_id = $row['id'];
                        $site_name = $row['name'];
                        $site_description = $row['description'];
                        $site_names_array[$site_id] = $site_name;
                        // echo('<option style="color:black" value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                    }          
                }
            }

            //area
            if (isset($_GET['site']) && $_GET['site'] !==0) {
                $sql_area = "SELECT DISTINCT area.id, area.name, area.description, area.site_id
                            FROM area 
                            INNER JOIN site ON site.id=area.site_id
                            WHERE site.id=?
                            ORDER BY area.id";
                $stmt_area = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_area, "s", $site);
                    mysqli_stmt_execute($stmt_area);
                    $result_area = mysqli_stmt_get_result($stmt_area);
                    $rowCount_area = $result_area->num_rows;
                    if ($rowCount_area < 1) {
                        // echo ("No areas found");
                        // exit();
                    } else {
                        while( $row = $result_area->fetch_assoc() ) {
                            $area_id = $row['id'];
                            $area_name = $row['name'];
                            $area_description = $row['description'];
                            $area_names_array[$area_id] = $area_name;
                            // echo('<option style="color:black" value="'.$area_id.'"'); if ($area == $area_id) { echo('selected'); } echo('>'.$area_name.'</option>');
                        }
                        // echo($area);
                    }
                }
            }

            echo('
                <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:10px; margin-top:20px">
                    <div class="nav-row">
                        <form action="./cablestock.php" method="get" class="nav-row" style="max-width:max-content">
                            <input id="query-site" type="hidden" name="site" value="'.$site.'" />
                            <input type="hidden" name="cable" value="'.$cableType.'" />
                            <input type="hidden" name="oos" value="'.$showOOS.'" />

                            <span id="search-input-site-span" style="margin-bottom:10px;" class="index-dropdown">
                                <label for="search-input-site">Site</label><br>
                                <select id="site-dropdown" name="site" class="form-control nav-v-b cw theme-dropdown"  onchange="siteChange(\'site-dropdown\')">
                                <option value="0"'); if ($site == 0) { echo('selected'); } echo('>All</option>
                            ');
                            if (!empty($site_names_array)) {
                                foreach (array_keys($site_names_array) as $site_id) {
                                    $site_name = $site_names_array[$site_id];
                                    echo('<option value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                                }
                            }
                            
                            echo('
                                </select>
                            </span>
                            ');  
                            
                            echo('
                            <span id="search-input-name-span" style="margin-right:0.5em;margin-bottom:10px;">
                                <label for="search-input-name">Name</label><br>
                                <input id="search-input-name" type="text" name="name" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                            </span>
                            <input type="submit" value="submit" hidden>
                        </form>');

                        echo('
                        <div id="clear-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:5px;margin-right:0">
                            <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black;padding:6 6 6 6" onclick="navPage(\'./cablestock.php\')">
                                <i class="fa fa-ban fa-rotate-90" style="height:24px;padding-top:4px"></i>
                            </button>
                        </div>
                        <div id="zero-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0">');
                        if ($showOOS == 0) {
                            echo('<button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0 2 0 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'1\'))">');
                        } else {
                            echo('<button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0 2 0 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'0\'))">');
                        }
                                echo('
                                <span>
                                    <p style="margin:0;padding:0;font-size:12">'); if ($showOOS == 0) { echo('<i class="fa fa-plus"></i> Show'); } else { echo('<i class="fa fa-minus"></i> Hide'); } echo('</p>
                                    <p style="margin:0;padding:0;font-size:12">0 Stock</p>
                            </button>
                        </div>
                        <div id="add-cables-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0">
                            <button id="add-cables" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6 6 6 6" onclick="toggleAddDiv()" type="button">
                                <i class="fa fa-plus" style="height:24px;padding-top:4px"></i> Add Cables
                            </button>
                            <button id="add-cables-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6 6 6 6" onclick="toggleAddDiv()" type="button" hidden>
                                Hide Add Cables
                            </button>
                        </div>
                        <div id="stockBtn-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0">
                            <button id="stockBtn" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6 6 6 6" onclick="navPage(\'./\')" type="button">
                                Item Stock
                            </button>
                        </div>
                    </div>
                </div>
                <!-- mobile layout section -->
                <div class="container viewport-small" style="margin-top:-10px;max-width:max-content">
                    <div class="nav-row">
                        <div id="clear-div" class="nav-div" style="margin-left:0;margin-right:0;margin-bottom:10px;">
                            <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(\'/\')">
                                <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                            </button>
                        </div>
                        <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0;margin-bottom:10px;">');
                        if ($showOOS == 0) {
                            echo('<button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0 2 1 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'1\'))">');
                        } else {
                            echo('<button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0 2 1 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'0\'))">');
                        }
                                echo('
                                <span class="zeroStockFont">
                                    <p style="margin:0;padding:0">'); if ($showOOS == 0) { echo('<i class="fa fa-plus"></i> Show'); } else { echo('<i class="fa fa-minus"></i> Hide'); } echo('</p>
                                    <p style="margin:0;padding:0">0 Stock</p>
                            </button>
                        </div>
                        <div id="add-cables-div" class="nav-div" style="margin-left:15px;margin-right:0;margin-bottom:10px;">
                            <button id="add-cables-small" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6 6 6 6" onclick="toggleAddDiv()" type="button">
                                <i class="fa fa-plus" style="padding-top:0px"></i> Add Cables
                            </button>
                            <button id="add-cables-hide-small" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6 6 6 6" onclick="toggleAddDiv()" type="button" hidden>
                                Hide Add Cables
                            </button>
                        </div>
                        <div id="stockBtn-div" class="nav-div" style="margin-left:15px;margin-right:0;margin-bottom:10px;">
                            <button id="stockBtn" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6 6 6 6" onclick="navPage(\'./\')" type="button">
                                Item Stock
                            </button>
                        </div>
                    </div>
                </div>

                <div class="container text-center"> 
                    <p>Stock for any fixed cables (cables that always need to be in stock).</p>
                </div>

            <!-- Add Cables form section -->
            <div class="container" id="add-cables-section" style="margin-bottom:10px" hidden>
                <div class="well-nopad theme-divBg text-center">
                    <h3 style="font-size:22px">Add new cables</h3>
                    <hr style="border-color:#9f9d9d; margin-left:10px">
                    <form id="add-cables-form" action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0">
                        <table class="centertable">
                            <thead>
                                <th style="padding-left:5px">Site</th>
                                <th style="padding-left:5px">Area</th>
                                <th style="padding-left:5px">Shelf</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select id="site" name="site" class="form-control" style="border-color:black;margin:0;padding-left:0" required>');

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
                                                        echo("<option value='".$row_site_cable['id']."'>".$row_site_cable['name']."</option>");
                                                    }
                                                }
                                            }   

                                    echo('      
                                        </select>
                                    </td>
                                    <td>
                                        <select id="area" name="area" class="form-control" style="border-color:black;margin:0;padding-left:0" disabled required>
                                            <option value="" selected disabled hidden>Select Area</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="shelf" name="shelf" class="form-control" style="border-color:black;margin:0;padding-left:0" disabled required>
                                            <option value="" selected disabled hidden>Select Shelf</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                            <thead>

                            <!-- Large scale -->
                            <thead class="viewport-large-empty">
                                <tr>
                                    <th style="padding-left:5px">Name</th>
                                    <th style="padding-left:5px">Description</th>
                                    <th style="padding-left:5px">Type</th>
                                    <th style="padding-left:5px">Min. Stock</th>
                                    <th style="padding-left:5px">Quantity</th>
                                    <th style="padding-left:5px">Cost</th>
                                    <th style="padding-left:5px"></th>
                                </tr>
                            </thead>
                            <tbody class="viewport-large-empty">
                                <tr>
                                    <td>
                                        <input class="form-control" type="text" list="names" name="stock-name" placeholder="Cable Name" required/>
                                        <datalist id="names">');
                                            $sql_stock_name = "SELECT * from stock WHERE is_cable=1 ORDER BY name";
                                            $stmt_stock_name = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_stock_name, $sql_stock_name)) {
                                                echo("ERROR getting entries");
                                            } else {
                                                mysqli_stmt_execute($stmt_stock_name);
                                                $result_stock_name = mysqli_stmt_get_result($stmt_stock_name);
                                                $rowCount_stock_name = $result_stock_name->num_rows;
                                                if ($rowCount_stock_name < 1) {
                                                } else {
                                                    while( $row_stock_name = $result_stock_name->fetch_assoc() ) {
                                                        echo("<option>".$row_stock_name['name']."</option>");
                                                    }
                                                }
                                            }
                                        echo('
                                        </datalist>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="stock-description" placeholder="Description" required/>
                                    </td>
                                    <td>
                                        <select class="form-control" name="cable-type" required>');

                                            $sql_types = "SELECT * from cable_types
                                                            ORDER BY parent";
                                            $stmt_types = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_types, $sql_types)) {
                                                echo("ERROR getting entries");
                                            } else {
                                                mysqli_stmt_execute($stmt_types);
                                                $result_types = mysqli_stmt_get_result($stmt_types);
                                                $rowCount_types = $result_types->num_rows;
                                                if ($rowCount_types < 1) {
                                                    echo ("<option selected disabled>No Types Found</option> ");
                                                } else {
                                                    echo ("<option selected disabled>Select Type</option>");
                                                    while( $row_types = $result_types->fetch_assoc() ) {
                                                        echo("<option value='".$row_types['id']."'>".$row_types['name']."</option>");
                                                    }
                                                }
                                            }

                                        echo('
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" name="stock-min-stock" placeholder="Minimum Stock Count" style="width:90px" value="10" required/>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" name="item-quantity" placeholder="Quantity" style="width:90px" value="1" required/>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" name="item-cost" placeholder="Cost" style="width:90px" value="0" required/>
                                    </td>
                                    <td>
                                        <button class="btn btn-success align-bottom" type="submit" name="add-cables-submit" style="margin-left:10px" value="1">Add</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=2></td>
                                    <td class="text-center"><label class="gold clickable" style="margin-top:5px;font-size:14" onclick="modalLoadNewType()">Add New</a></td>
                                    <td colspan=4</td>
                                </tr>
                                <tr>
                                    <td colspan=8 class="text-center">
                                        <input type="file" style="width: 250px;margin-top:5px" id="stock-img" name="stock-img">
                                    </td>
                                </tr>
                            </tbody>
                            <!-- Large scale end -->

                            <!-- Small scale -->
                            <thead class="viewport-small-empty">
                                <tr>
                                    <th style="padding-left:5px">Name</th>
                                    <th style="padding-left:5px">Description</th>
                                    <th style="padding-left:5px">Type</th>
                                </tr>
                            </thead>
                            <tbody class="viewport-small-empty">
                                <tr>
                                    <td>
                                        <input class="form-control" type="text" list="names" name="stock-name" placeholder="Cable Name" required/>
                                        <datalist id="names">');
                                            $sql_stock_name = "SELECT * from stock WHERE is_cable=1 ORDER BY name";
                                            $stmt_stock_name = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_stock_name, $sql_stock_name)) {
                                                echo("ERROR getting entries");
                                            } else {
                                                mysqli_stmt_execute($stmt_stock_name);
                                                $result_stock_name = mysqli_stmt_get_result($stmt_stock_name);
                                                $rowCount_stock_name = $result_stock_name->num_rows;
                                                if ($rowCount_stock_name < 1) {
                                                } else {
                                                    while( $row_stock_name = $result_stock_name->fetch_assoc() ) {
                                                        echo("<option>".$row_stock_name['name']."</option>");
                                                    }
                                                }
                                            }
                                        echo('
                                        </datalist>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="stock-description" placeholder="Description" required/>
                                    </td>
                                    <td>
                                        <select class="form-control" name="cable-type" required>');

                                            $sql_types = "SELECT * from cable_types
                                                            ORDER BY parent";
                                            $stmt_types = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_types, $sql_types)) {
                                                echo("ERROR getting entries");
                                            } else {
                                                mysqli_stmt_execute($stmt_types);
                                                $result_types = mysqli_stmt_get_result($stmt_types);
                                                $rowCount_types = $result_types->num_rows;
                                                if ($rowCount_types < 1) {
                                                    echo ("<option selected disabled>No Types Found</option> ");
                                                } else {
                                                    echo ("<option selected disabled>Select Type</option>");
                                                    while( $row_types = $result_types->fetch_assoc() ) {
                                                        echo("<option value='".$row_types['id']."'>".$row_types['name']."</option>");
                                                    }
                                                }
                                            }

                                        echo('
                                        </select>
                                    </td>
                                    <td class="text-center"><label class="gold clickable" style="margin-top:5px;font-size:14" onclick="modalLoadNewType()">Add New</a></td>
                                </tr>

                            </tbody>
                            <thead class="viewport-small-empty">
                                <tr>
                                    <th style="padding-left:5px">Min. Stock</th>
                                    <th style="padding-left:5px">Quantity</th>
                                    <th style="padding-left:5px">Cost</th>
                                    <th style="padding-left:5px"></th>
                                </tr>
                            </thead>
                            <tbody class="viewport-small-empty">
                                <tr>
                                    <td>
                                        <input class="form-control" type="number" name="stock-min-stock" placeholder="Minimum Stock Count" style="width:90px" value="10" required/>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" name="item-quantity" placeholder="Quantity" style="width:90px" value="1" required/>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" name="item-cost" placeholder="Cost" style="width:90px" value="0" required/>
                                    </td>
                                    <td>
                                        <button class="btn btn-success align-bottom" type="submit" name="add-cables-submit" style="margin-left:10px" value="1">Add</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=100% class="text-center">
                                        <input type="file" style="width: 250px;margin-top:5px" id="stock-img" name="stock-img">
                                    </td>
                                </tr>
                            </tbody>
                            <!-- Small scale end -->
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
            <style>
            .th-selected {
                background-color: #202328;
                border: 0px !important;
            }
            .th-noBorder {
                border: 0px !important;
            }
            </style>
            <!-- Table -->
            <div class="container">
                <table class="table table-dark theme-table centertable" id="cableSelection" style="border:0px !important">
                    <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border:0px !important">
                        <tr style="border:0px !important">');
                        if (!isset($_GET['name']) || $name == '') {
                            echo('<th class="clickable '); if ($cableType == "copper" || $cableType == '') { echo('theme-th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'copper\'))">Copper</th>');
                            echo('<th class="clickable '); if ($cableType == "fibre") { echo('theme-th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'fibre\'))">Fibre</th>');
                            echo('<th class="clickable '); if ($cableType == "power") { echo('theme-th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'power\'))">Power</th>');
                            echo('<th class="clickable '); if ($cableType == "other") { echo('theme-th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'other\'))">Other</th>');
                        }
                        echo('
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan=4 class="theme-th-selected">');
                            if ($rowCount_inv < 1) {
                                echo ('<div class="container" id="no-inv-found">No Inventory Found</div>');
                            } else {
                                echo('
                                <table class="table table-dark theme-table centertable" id="inventoryTable">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr>
                                            <th id="stock-id" hidden>Stock ID</th>
                                            <th id="item-id" hidden>Item ID</th>
                                            <th id="image"></th>
                                            <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(3, this)">Name</th>
                                            <th id="type-id" hidden>Type ID</th>
                                            <th class="clickable sorting viewport-large-empty" id="type" onclick="sortTable(5, this)">Type</th>
                                            <th class="clickable sorting" id="site-name" onclick="sortTable(6, this)">Site</th>
                                            <th class="clickable sorting" id="quantity" onclick="sortTable(7, this)">Quantity</th>
                                            <th id="min-stock" class="viewport-large-empty" style="color:#8f8f8f">Min. stock</th>
                                            <th class="viewport-small-empty" style="color:#8f8f8f">Min.</th>
                                            <th class="btn-cableStock"></th>
                                            <th class="btn-cableStock"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle" style="text-align: center; white-space: nowrap;">
                                ');
                                // Inventory Rows
                                while ( $row = $result_inv->fetch_assoc() ) {
                                    // print_r('<pre>'); print_r($row); print_r('</pre>');
                                    $img_directory = "assets/img/stock/"; 

                                    $stock_id = $row['stock_id'];
                                    $stock_img_file_name = $row['stock_img_image'];
                                    $stock_name = $row['stock_name'];
                                    $stock_sku = $row['stock_sku'];
                                    $stock_quantity_total = $row['cable_item_quantity'];
                                    $stock_site_id = $row['site_id'];
                                    $stock_site_name = $row['site_name'];
                                    $stock_min_stock = $row['stock_min_stock'];
                                    $cable_item_id = $row['cable_item_id'];
                                    $cable_types_id = $row['cable_types_id'];
                                    $cable_types_name = $row['cable_types_name'];
                                    $cable_types_description = $row['cable_types_description']; 
                                    $cable_types_parent = $row['cable_types_parent'];         

                                    // Echo each row (inside of SQL results)
                                    if (isset($_GET['cableItemID']) && $_GET['cableItemID'] == $cable_item_id) { 
                                        $last_edited = ' last-edit'; 
                                    } else {
                                        $last_edited = '';
                                    }

                                    echo('
                                        <tr class="vertical-align align-middle'.$last_edited.'" id="'.$cable_item_id.'">
                                            <form id="modify-cable-item-'.$cable_item_id.'" action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="stock-id" value="'.$stock_id.'" />
                                                <input type="hidden" name="cable-item-id" value="'.$cable_item_id.'" />
                                                <td class="align-middle" id="'.$cable_item_id.'-stock-id" hidden>'.$stock_id.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-item-id" hidden>'.$cable_item_id.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-img-td">
                                                ');
                                                if (!is_null($stock_img_file_name)) {
                                                    echo('<img id="'.$cable_item_id.'-img" class="inv-img-50h thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />');
                                                }
                                                echo('</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-name"><a class="link" href="stock.php?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                                                <td class="align-middle" id="'.$cable_item_id.'-type-id" hidden>'.$cable_types_id.'</td>
                                                <td class="align-middle viewport-large-empty" id="'.$cable_item_id.'-type"><or title="'.$cable_types_description.'">'.$cable_types_name.'</or></td> 
                                                <td class="align-middle link gold" id="'.$cable_item_id.'-site-name" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-quantity">'); 
                                                if ($stock_quantity_total == 0) {
                                                    echo("<or class='red' title='Out of Stock'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Out of stock. Order more if necessary.'>0 <i class='fa fa-warning' /></u></or>");
                                                } elseif ($stock_quantity_total < $stock_min_stock) {
                                                    echo("<or class='red'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Below minimum stock count. Order more!'>$stock_quantity_total</u></or>");
                                                } else {
                                                    echo($stock_quantity_total);
                                                }
                                                echo('</td>');
                                            echo('
                                                <td class="align-middle" id="'.$cable_item_id.'-min-stock"  style="color:#8f8f8f">'.$stock_min_stock.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-add"><button id="'.$stock_id.'-add-btn" class="btn btn-success cw nav-v-b btn-cableStock" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                                                <td class="align-middle" id="'.$cable_item_id.'-remove"><button id="'.$stock_id.'-remove-btn" class="btn btn-danger cw nav-v-b btn-cableStock" type="submit" name="action" value="remove" '); if ($stock_quantity_total == 0) { echo "disabled"; } echo('><i class="fa fa-minus"></i></button></td>
                                            </form>
                                        </tr>
                                    ');
                                }

                                // End table + body
                                echo ('
                                        </body>
                                    </table>');
                            }
                            echo('
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            ');    

        }

        ?>
    </div>    
    <div id="modalDivNewType" class="modal">
        <!-- <div id="modalDivProperties" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewType()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data">
                    <table class="centertable">
                        <tbody>
                        <tr class="align-middle">
                                <td style="width:150px">Parent:</td>
                                <td>
                                    <select class="form-control" name="type-parent" style="width:300px" required>
                                            <option value="" selected disabled hidden>Select Parent</option>
                                            <option value="Copper">Copper</option>
                                            <option value="Fibre">Fibre</option>
                                            <option value="Power">Power</option>
                                            <option value="Other">Other</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px">New Type:</td>
                                <td>
                                    <input class="form-control" type="text" placeholder="New Type" name="type-name" required/>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px">Description:</td>
                                <td>
                                    <input class="form-control" type="text" placeholder="Description" name="type-description" required/>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px"></td>     
                                <td><input type="submit" name="submit" value="Add Type" class="btn btn-success"></td>
                                <td hidden=""><input type="hidden" name="new-type" value="1"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>  
        </div>
    </div>

    <script>
        // Function to get the value of a query parameter from the URL
        function getQueryParameter(parameterName) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(parameterName);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Get the value of the "cableItemID" query parameter from the URL
            const cableItemID = getQueryParameter("cableItemID");

            // Check if the "cableItemID" parameter is set and not empty
            if (cableItemID && cableItemID.trim() !== "") {
                // Scroll to the element with the ID equal to "cableItemID"
                const elementToScroll = document.getElementById(cableItemID);
                if (elementToScroll) {
                    elementToScroll.scrollIntoView({ behavior: "smooth" });
                }
            }
        });
    </script>

    <script>
        function toggleAddDiv() {
            var div = document.getElementById('add-cables-section');
            var addButton = document.getElementById('add-cables');
            var addButtonHide = document.getElementById('add-cables-hide');
            var addButtonSmall = document.getElementById('add-cables-small');
            var addButtonHideSmall = document.getElementById('add-cables-hide-small');

            if (div.hidden === true) {
                div.hidden = false;
                addButton.hidden = true;
                addButtonHide.hidden = false;
                addButtonSmall.hidden = true;
                addButtonHideSmall.hidden = false;
            } else {
                div.hidden = true;
                addButton.hidden = false;
                addButtonHide.hidden = true;
                addButtonSmall.hidden = false;
                addButtonHideSmall.hidden = true;
            }

        }
    </script>
    <script>
        function modalLoadNewType() {
            var modal = document.getElementById("modalDivNewType");
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalCloseNewType = function() { 
            var modal = document.getElementById("modalDivNewType");
            modal.style.display = "none";
        }
    </script>
    <script> // for the select boxes
    function populateAreas() {
    // Get the selected site
    var site = document.getElementById("site").value;
    
    // Make an AJAX request to retrieve the corresponding areas
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stock-selectboxes.inc.php?site=" + site, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
        // Parse the response and populate the area select box
        var areas = JSON.parse(xhr.responseText);
        var select = document.getElementById("area");
        select.options.length = 0;
        select.options[0] = new Option("Select Area", "");
        for (var i = 0; i < areas.length; i++) {
            select.options[select.options.length] = new Option(areas[i].name, areas[i].id);
        }
        select.disabled = (select.options.length === 1);
        }
    };
    xhr.send();
    }
    function populateShelves() {
    // Get the selected area
    var area = document.getElementById("area").value;
    
    // Make an AJAX request to retrieve the corresponding shelves
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stock-selectboxes.inc.php?area=" + area, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
        // Parse the response and populate the shelf select box
        var shelves = JSON.parse(xhr.responseText);
        var select = document.getElementById("shelf");
        select.options.length = 0;
        select.options[0] = new Option("Select Shelf", "");
        for (var i = 0; i < shelves.length; i++) {
            select.options[select.options.length] = new Option(shelves[i].name, shelves[i].id);
        }
        select.disabled = (select.options.length === 1);
        }
    };
    xhr.send();
    }
    document.getElementById("site").addEventListener("change", populateAreas);
    document.getElementById("area").addEventListener("change", populateShelves);
    </script>
        
    <?php include 'foot.php'; ?>

</body>