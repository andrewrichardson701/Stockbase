<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Cable Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php 
        $navHighlight = 'cables'; // for colouring the nav bar link
        $navBtnDim = 1;
        include 'nav.php'; 
    ?>
    <!-- End of Header and Nav -->
    <div class="content">
        
        <!-- Get Inventory -->
        <?php
        $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
        $site = isset($_GET['site']) ? $_GET['site'] : "0";
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $cableType = isset($_GET['cable']) ? $_GET['cable'] : 'copper';
        $cable_typesType = isset($_GET['type']) ? $_GET['type'] : '';
        if (isset($_GET['rows'])) {
            if ($_GET['rows'] == 50 || $_GET['rows'] == 100) {
                $rowSelectValue = htmlspecialchars($_GET['rows']);
            } else {
                $rowSelectValue = 10;
            }
        } else {
            $rowSelectValue = 10;
        }

        $site_names_array = [];
        $area_names_array = [];

        include 'includes/dbh.inc.php';
        $s = 0;
        $sql_inv_count = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, 
                            stock.sku as stock_sku, stock.min_stock as stock_min_stock, stock.is_cable as stock_is_cable,
                            cable_item.id as cable_item_id, cable_item.stock_id as cable_item_stock_id, cable_item.quantity as cable_item_quantity,
                            cable_item.cost AS cable_item_cost, cable_item.shelf_id AS cable_item_shelf_id, cable_item.type_id as cable_item_type_id,
                            cable_types.id AS cable_types_id, cable_types.name AS cable_types_name, cable_types.description AS cable_types_description,
                            cable_types.parent AS cable_types_parent,
                            site.id AS site_id, site.name AS site_name,
                            area.id AS area_id,
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
            $name = mysqli_real_escape_string($conn, $name); // escape the special characters
            $sql_inv_add  .= " AND stock.name LIKE '%$name%'";
        }
        if (is_numeric($cable_typesType) && $cable_typesType !== '') { // cable_types table 
            $sql_inv_add .= " AND cable_types.id = $cable_typesType";
        }
        if (isset($cableType)) {
            if (is_numeric($cable_typesType) && $cable_typesType !== '') {
                $sql_types = "SELECT parent
                            FROM cable_types 
                            WHERE id=?
                            LIMIT 1";
                $stmt_types = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_types, $sql_types)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_types, "s", $cable_typesType);
                    mysqli_stmt_execute($stmt_types);
                    $result_types = mysqli_stmt_get_result($stmt_types);
                    $rowCount_types = $result_types->num_rows;
                    if ($rowCount_types < 1) {
                        if ($name == '' || $name == null) {
                            $cable_type = ucwords($cableType);
                            $sql_inv_add .= " AND cable_types.parent = '$cable_type'";
                        }
                    } else {
                        $row = $result_types->fetch_assoc();
                        $query_parent = $row['parent'];
                        if ($name == '' || $name == null) {
                            $cableType = strtolower($query_parent);
                            $cable_type = ucwords($cableType);
                            $sql_inv_add .= " AND cable_types.parent = '".ucwords($cable_type)."'";
                            $_GET['cable'] = $query_parent;
                        }
                    }
                }
            } else {
                if ($name == '' || $name == null) {
                    $cable_type = ucwords($cableType);
                    $sql_inv_add .= " AND cable_types.parent = '$cable_type'";
                }
            }
        }    
        $sql_inv_count .= $sql_inv_add;
        $sql_inv_count .= " GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
                        cable_item_id, 
                        site_id, site_name, stock_img_image.stock_img_image";
        $sql_inv_count .= " ORDER BY stock.name";

        $stmt_inv_count = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_inv_count, $sql_inv_count)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_inv_count);
            $result_inv_count = mysqli_stmt_get_result($stmt_inv_count);
            $totalRowCount = $result_inv_count->num_rows;
            
            // Pagination settings
            $results_per_page = $rowSelectValue; // Number of rows per page - based no the querystring (or 10 by default)
            $total_pages = ceil($totalRowCount / $results_per_page);

            $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            if ($current_page < 1) {
                $current_page = 1;
            } elseif ($current_page > $total_pages) {
                $current_page = $total_pages;
            } 

            // Calculate the offset for the query
            $offset = ($current_page - 1) * $results_per_page;
            if ($offset < 0) {
                $offset = $results_per_page;
            }

            $sql_inv_pagination = " LIMIT $results_per_page OFFSET $offset;";

            $sql_inv = $sql_inv_count .= $sql_inv_pagination;


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
                                ');
                                // GET the count of the cable_types table
                                $types = array();
                                $sql_types = "SELECT DISTINCT id, name, description, parent
                                            FROM cable_types 
                                            ORDER BY parent";
                                $stmt_types = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_types, $sql_types)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_execute($stmt_types);
                                    $result_types = mysqli_stmt_get_result($stmt_types);
                                    $rowCount_types = $result_types->num_rows;
                                    if ($rowCount_types < 1) {
                                        // echo ("No Types found");
                                        // exit();
                                    } else {
                                        while( $row = $result_types->fetch_assoc() ) {
                                            $types[] = array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description'], 'parent' => $row['parent']);
                                        }
                                    }
                                }

                                echo('
                                <span id="search-input-type-span" style="margin-right:0.5em;margin-bottom:10px;">
                                    <label for="search-input-type">Type</label><br>
                                    <select id="search-input-type" name="type" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Type" onchange="this.form.submit()">
                                        <option value="" '); echo ((isset($_GET['type']) && !is_numeric($_GET['type'])) ? 'selected' : ''); echo('>All</option>');
                                    foreach ($types as $type) {
                                        echo('<option value="'.$type['id'].'" title="'.$type['description'].' ('.$type['parent'].')" '); if(isset($_GET['type']) && $_GET['type'] == $type['id']) { echo ('selected'); } echo('>'.$type['name'].'</option>');
                                    }
                                echo('
                                    </select>
                                </span>
                                <input type="submit" value="submit" hidden>
                            </form>');

                            echo('
                            <div id="clear-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:5px;margin-right:0px">
                                <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="navPage(\'./cablestock.php\')">
                                    <i class="fa fa-ban fa-rotate-90" style="height:24px;padding-top:4px"></i>
                                </button>
                            </div>
                            <div id="zero-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0px">');
                            if ($showOOS == 0) {
                                echo('<button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'1\'))">');
                            } else {
                                echo('<button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'0\'))">');
                            }
                                    echo('
                                    <span>
                                        <p style="margin:0px;padding:0px;font-size:12px">'); if ($showOOS == 0) { echo('<i class="fa fa-plus"></i> Show'); } else { echo('<i class="fa fa-minus"></i> Hide'); } echo('</p>
                                        <p style="margin:0px;padding:0px;font-size:12px">0 Stock</p>
                                </button>
                            </div>
                            <div id="add-cables-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0px">
                                <button id="add-cables" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button">
                                    <i class="fa fa-plus" style="height:24px;padding-top:4px"></i> Add Cables
                                </button>
                                <button id="add-cables-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button" hidden>
                                    Hide Add Cables
                                </button>
                            </div>
                            <!-- <div id="stockBtn-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0px">
                                <button id="stockBtn" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6px 6px 6px 6px" onclick="navPage(\'./\')" type="button">
                                    Item Stock
                                </button>
                            </div> -->
                        </div>
                    </div>
                    <!-- mobile layout section -->
                    <div class="container viewport-small" style="margin-top:-10px;max-width:max-content">
                        <div class="nav-row">
                            <div id="clear-div" class="nav-div" style="margin-left:0px;margin-right:0px;margin-bottom:10px;">
                                <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(\'/\')">
                                    <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                                </button>
                            </div>
                            <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">');
                            if ($showOOS == 0) {
                                echo('<button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0px 2px 1px 2px" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'1\'))">');
                            } else {
                                echo('<button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0px 2px 1px 2px" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'0\'))">');
                            }
                                    echo('
                                    <span class="zeroStockFont">
                                        <p style="margin:0px;padding:0px">'); if ($showOOS == 0) { echo('<i class="fa fa-plus"></i> Show'); } else { echo('<i class="fa fa-minus"></i> Hide'); } echo('</p>
                                        <p style="margin:0px;padding:0px">0 Stock</p>
                                </button>
                            </div>
                            <div id="add-cables-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                                <button id="add-cables-small" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button">
                                    <i class="fa fa-plus" style="padding-top:0px"></i> Add Cables
                                </button>
                                <button id="add-cables-hide-small" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button" hidden>
                                    Hide Add Cables
                                </button>
                            </div>
                            <div id="stockBtn-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                                <button id="stockBtn" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6px 6px 6px 6px" onclick="navPage(\'./\')" type="button">
                                    Item Stock
                                </button>
                            </div>
                        </div>
                    </div>



                <!-- Add Cables form section -->
                <div class="container" id="add-cables-section" style="margin-bottom:10px" hidden>
                    <div class="well-nopad theme-divBg text-center">
                        <h3 style="font-size:22px">Add new cables</h3>
                        <hr style="border-color:#9f9d9d; margin-left:10px">
                        <form id="add-cables-form" action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                            <!-- Include CSRF token in the form -->
                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                            <table class="centertable">
                                <thead>
                                    <th style="padding-left:5px">Site</th>
                                    <th style="padding-left:5px">Area</th>
                                    <th style="padding-left:5px">Shelf</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select id="site" name="site" class="form-control" style="border-color:black;margin:0px;padding-left:0px" required>');

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
                                            <label style="margin-top:5px;font-size:14px">&nbsp;</label>
                                        </td>
                                        <td>
                                            <select id="area" name="area" class="form-control" style="border-color:black;margin:0px;padding-left:0px" disabled required>
                                                <option value="" selected disabled hidden>Select Area</option>
                                            </select>
                                            <label style="margin-top:5px;font-size:14px">&nbsp;</label>
                                        </td>
                                        <td>
                                            <select id="shelf" name="shelf" class="form-control" style="border-color:black;margin:0px;padding-left:0px" disabled required>
                                                <option value="" selected disabled hidden>Select Shelf</option>
                                            </select>
                                            <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadProperties(\'shelf\')">Add New</a>
                                        </td>
                                    </tr>
                                </tbody>
                                <thead>
                                <tbody>
                                    <tr>
                                        <td colspan=100%>
                                            <div class="row" style="margin-right:25px">
                                                <div class="col">
                                                    <div>Name</div>
                                                    <div>
                                                        <input class="form-control" type="text" list="names" name="stock-name" placeholder="Cable Name" style="min-width:120px" required/>
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
                                                    </div>
                                                </div>
                                                <div class="col"><div>Description</div><div><input class="form-control" type="text" name="stock-description" style="min-width:120px" placeholder="Description"/></div></div>
                                                <div class="col">
                                                    <div>Type</div>
                                                    <div>
                                                        <select class="form-control" name="cable-type" style="min-width:100px" required>');
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
                                                    </div>
                                                    <div class="text-center">
                                                        <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewType()">Add New</a>
                                                    </div>
                                                </div>
                                                <div class="col" style="max-width:max-content"><div>Min.Stock</div><div><input class="form-control" type="number" name="stock-min-stock" placeholder="Minimum Stock Count" style="width:70px" value="10" required/></div></div>
                                                <div class="col" style="max-width:max-content"><div>Quantity</div><div><input class="form-control" type="number" name="item-quantity" placeholder="Quantity" style="width:70px" value="1" required/></div></div>
                                                <div class="col" style="max-width:max-content"'); if ($current_cost_enable_cable == 0) { echo(' hidden'); } echo('><div>Cost</div><div><input class="form-control" type="number" name="item-cost" placeholder="Cost" style="width:70px" value="0" required/></div></div>
                                                <div class="col" style="max-width:max-content""><div>&nbsp;</div><div><button class="btn btn-success align-bottom" type="submit" name="add-cables-submit" style="margin-left:10px" value="1">Add</button></div></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=100% class="text-center">
                                            <input type="file" style="width: 250px;margin-top:10px" id="stock-img" name="stock-img">
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
                                    <table class="table table-dark theme-table centertable" id="inventoryTable" style="padding-bottom:0px;margin-bottom:0px;">
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
                                        $stock_area_id = $row['area_id'];
                                        $stock_shelf_id = $row['cable_item_shelf_id'];
                                        $stock_site_name = $row['site_name'];
                                        $stock_min_stock = $row['stock_min_stock'];
                                        $cable_item_id = $row['cable_item_id'];
                                        $cable_item_cost = $row['cable_item_cost'];
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
                                            <tr class="vertical-align align-middle'.$last_edited.' row-show highlight" id="'.$cable_item_id.'">
                                                <td hidden>
                                                    <form id="modify-cable-item-'.$cable_item_id.'" action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data">
                                                        <!-- Include CSRF token in the form -->
                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                        <input type="hidden" name="stock-id" value="'.$stock_id.'" />
                                                        <input type="hidden" name="cable-item-id" value="'.$cable_item_id.'" />
                                                    </form>
                                                </td>
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
                                                <td class="align-middle" id="'.$cable_item_id.'-add"><button id="'.$stock_id.'-add-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-success cw nav-v-b btn-cableStock" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                                                <td class="align-middle" id="'.$cable_item_id.'-remove"><button id="'.$stock_id.'-remove-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-danger cw nav-v-b btn-cableStock" type="submit" name="action" value="remove" '); if ($stock_quantity_total == 0) { echo "disabled"; } echo('><i class="fa fa-minus"></i></button></td>
                                                <td class="align-middle" id="'.$cable_item_id.'-move"><button id="'.$stock_id.'-move-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-warning cw nav-v-b btn-cableStock" type="button" value="move" onclick="toggleHidden(\''.$cable_item_id.'\')" '); if ($stock_quantity_total == 0) { echo "disabled"; } echo('><i class="fa fa-arrows-h" style="color:black"></i></button></td>
                                            </tr>
                                            <tr class="vertical-align align-middle'.$last_edited.' move-hide" id="'.$cable_item_id.'-move-hidden" hidden>
                                                <td colspan=100%>
                                                    <form class="centertable" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
                                                        <!-- Include CSRF token in the form -->
                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                        <table class="centertable" style="border: 1px solid #454d55; width:100%"> 
                                                            <!-- below input used for the stock-modify.inc.php page to determine the type of change -->
                                                            <input type="hidden" name="cablestock-move" value="1">
                                                            <input type="hidden" id="'.$stock_id.'-c-stock" name="current_cable_item" value="'.$cable_item_id.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-stock" name="current_stock" value="'.$stock_id.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-site" name="current_site" value="'.$stock_site_id.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-area" name="current_area" value="'.$stock_area_id.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-shelf" name="current_shelf" value="'.$stock_shelf_id.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-cost" name="current_cost" value="'.$cable_item_cost.'">
                                                            <input type="hidden" id="'.$stock_id.'-c-quantity" name="current_quantity" value="'.$stock_quantity_total.'">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="container">
                                                                            <div class="row centertable" style="max-width:max-content">
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c">To:</label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$stock_id.'-n-site" name="site" style="min-width:50px; padding:2px 0px 2px 0px;  width:max-content !important" required onchange="populateAreasMove(\''.$stock_id.'\')">
                                                                                        <option value="" selected="" disabled="" hidden="">Site</option><option value="1">CDC ME14</option><option value="2">CDC DA2</option><option value="4">TestSite</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$stock_id.'-n-area" name="area" style="min-width:50px; padding: 2px 0px 2px 0px; max-width:max-content !important" disabled="" required onchange="populateShelvesMove(\''.$stock_id.'\')">
                                                                                        <option value="" selected="" disabled="" hidden="">Area</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$stock_id.'-n-shelf" name="shelf" style="min-width:50px; padding: 2px 0px 2px 0px; max-width:max-content !important" disabled="" required>
                                                                                        <option value="" selected="" disabled="" hidden="">Shelf</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c" for="0-n-quantity">Quantity: </label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="number" class="form-control nav-v-c row-dropdown" id="'.$stock_id.'-n-quantity" name="quantity" style="min-width: 20px; padding: 2px 7px 2px 7px; max-width:50px;" placeholder="1" value="1" min="1" max="'.$stock_quantity_total.'" required>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="submit" class="btn btn-warning nav-v-c btn-move" id="'.$stock_id.'-n-submit" value="Move" style="opacity:80%;" name="submit" required="">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </form>
                                                </td>
                                            </tr>
                                        ');
                                    }

                                    // End table + body
                                    echo ('
                                            </body>
                                        </table>
                                        <table class="table table-dark theme-table centertable">
                                            <tbody>
                                                <tr class="theme-tableOuter">
                                                    <td colspan="100%" style="padding:0px;margin:0px" class="invTablePagination">
                                                    <div class="row">
                                                        <div class="col text-center"></div>
                                                        <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                                                        ');
                                                        if ($total_pages > 1) {
                                                            if ($current_page > 1) {
                                                                echo '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
                                                            }
                                                            if ($total_pages > 5) {
                                                                for ($i = 1; $i <= $total_pages; $i++) {
                                                                    if ($i == $current_page) {
                                                                        echo '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                                                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                                                    } elseif ($i == 1 && $current_page > 5) {
                                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                                                                    } elseif ($i < $current_page && $i >= $current_page-2) {
                                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                                    } elseif ($i > $current_page && $i <= $current_page+2) {
                                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                                    } elseif ($i == $total_pages) {
                                                                        echo '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                                                                    }
                                                                }
                                                            } else {
                                                                for ($i = 1; $i <= $total_pages; $i++) {
                                                                    if ($i == $current_page) {
                                                                        echo '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                                                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                                                    } else {
                                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                                    }
                                                                }
                                                            }
                                        
                                                            if ($current_page < $total_pages) {
                                                                echo '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
                                                            }  
                                                        }
                                                        echo('
                                                        </div>
                                                        <div class="col text-center">
                                                            <table style="margin-left:auto; margin-right:20px">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="theme-textColor align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                                            Rows: 
                                                                        </td>
                                                                        <td class="align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                                            <select id="tableRowCount" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" name="rows" onchange="navPage(updateQueryParameter(\'\', \'rows\', this.value))">
                                                                                <option id="rows-10"  value="10"');  if($rowSelectValue == 10)  { echo('selected'); } echo('>10</option>
                                                                                <option id="rows-50"  value="50"');  if($rowSelectValue == 50)  { echo('selected'); } echo('>50</option>
                                                                                <option id="rows-100" value="100"'); if($rowSelectValue == 100) { echo('selected'); } echo('>100</option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </tr>
                                            </tbody>
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
        }

        ?>
    </div> 

    <?php include 'includes/stock-new-properties.inc.php'; ?>

    <div id="modalDivNewType" class="modal">
        <!-- <div id="modalDivProperties" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewType()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <table class="centertable">
                        <tbody>
                        <tr class="align-middle">
                                <td style="width:150px">Parent:</td>
                                <td>
                                    <select class="form-control" name="type-parent" style="min-width:150px;max-width:300px" required>
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
                                    <input class="form-control" type="text" style="min-width:150px;max-width:300px" placeholder="New Type" name="type-name" required/>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px">Description:</td>
                                <td>
                                    <input class="form-control" type="text" style="min-width:150px;max-width:300px" placeholder="Description" name="type-description" required/>
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

    function toggleHidden(id) {
        var Row = document.getElementById(id);
        var hiddenID = id+'-move-hidden';
        var hiddenRow = document.getElementById(hiddenID);
        var allRows = document.getElementsByClassName('row-show');
        var allHiddenRows = document.getElementsByClassName('move-hide');
        if (hiddenRow.hidden == false) {
            hiddenRow.hidden=true;
            hiddenRow.classList.remove('theme-th-selected');
            Row.classList.remove('theme-th-selected');
        } else {
            for(var i = 0; i < allHiddenRows.length; i++) {
                allHiddenRows[i].hidden=true;
                allHiddenRows[i].classList.remove('theme-th-selected');
            }  
            for (var j = 0; j < allRows.length; j++) {
                allRows[j].classList.remove('theme-th-selected');
            }   
            hiddenRow.hidden=false;
            hiddenRow.classList.add('theme-th-selected');
            Row.classList.add('theme-th-selected');
        }
    }

    function populateAreasMove(id) {
        // console.log(id);
        // Get the selected site
        var site = document.getElementById(id+"-n-site").value;
        
        // Make an AJAX request to retrieve the corresponding areas
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?site=" + site, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
            // Parse the response and populate the area select box
            var areas = JSON.parse(xhr.responseText);
            var select = document.getElementById(id+"-n-area");
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
    function populateShelvesMove(id) {
        // Get the selected area
        var area = document.getElementById(id+"-n-area").value;
        
        // Make an AJAX request to retrieve the corresponding shelves
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?area=" + area, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var shelves = JSON.parse(xhr.responseText);
            var select = document.getElementById(id+"-n-shelf");
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
    </script>
        
    <?php include 'foot.php'; ?>

</body>