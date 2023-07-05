<?php 
// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>Inventory</title>
</head>
<body>
    <?php // dependency PHP
    $show_inventory = 1; // for nav.php to show the site and area on the banner
    ?>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    
    <!-- Get Inventory -->
    <?php
    $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
    $site = isset($_GET['site']) ? $_GET['site'] : "0";
    $area = isset($_GET['area']) ? $_GET['area'] : "0";
    $name = isset($_GET['name']) ? $_GET['name'] : "";
    $sku = isset($_GET['sku']) ? $_GET['sku'] : "";
    $location = isset($_GET['location']) ? $_GET['location'] : "";
    $shelf = isset($_GET['shelf']) ? $_GET['shelf'] : "";
    $label = isset($_GET['label']) ? $_GET['label'] : "";
    $manufacturer = isset($_GET['manufacturer']) ? $_GET['manufacturer'] : "";
    $site_names_array = [];
    $area_names_array = [];

    include 'includes/dbh.inc.php';
    $s = 0;
    // $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
    //             shelf.id AS shelf_id, shelf.name AS shelf_name, area.id AS area_id, area.name AS area_name, area.description AS area_description, 
    //             area.parent_id as area_parent_id, site.id AS site_id, site.name AS site_name, site.description AS site_description 
    //             FROM stock
    //             INNER JOIN shelf ON stock.shelf_id=shelf.id 
    //             INNER JOIN area ON shelf.area_id=area.id 
    //             INNER JOIN site ON area.site_id=site.id 
    //             WHERE site.id=?";
    // $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
    //             GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
    //             site.id AS site_id, site.name AS site_name, site.description AS site_description,
    //             SUM(item.quantity) AS item_quantity
    //             FROM stock
    //             INNER JOIN item ON stock.id=item.stock_id
    //             INNER JOIN shelf ON item.shelf_id=shelf.id 
    //             INNER JOIN area ON shelf.area_id=area.id 
    //             INNER JOIN site ON area.site_id=site.id";
    $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                    GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
                    site.id AS site_id, site.name AS site_name, site.description AS site_description,
                    (SELECT SUM(quantity) 
                        FROM item 
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        WHERE item.stock_id=stock.id AND area.site_id=site.id
                    ) AS item_quantity,

                    label_names.label_names AS label_names,
                    label_ids.label_ids AS label_ids,
                    stock_img_image.stock_img_image
                FROM stock
                LEFT JOIN item ON stock.id=item.stock_id
                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                LEFT JOIN area ON shelf.area_id=area.id 
                LEFT JOIN site ON area.site_id=site.id
                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                LEFT JOIN (
                    SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                    FROM stock_img
                    GROUP BY stock_img.stock_id
                ) AS stock_img_image
                    ON stock_img_image.stock_id = stock.id
                LEFT JOIN (SELECT stock_label.stock_id, GROUP_CONCAT(DISTINCT label.name SEPARATOR ', ') AS label_names
                        FROM stock_label 
                        INNER JOIN label ON stock_label.label_id = label.id
                        GROUP BY stock_label.stock_id) AS label_names
                    ON label_names.stock_id = stock.id
                LEFT JOIN (SELECT stock_label.stock_id, GROUP_CONCAT(DISTINCT label_id SEPARATOR ', ') AS label_ids
                        FROM stock_label
                        GROUP BY stock_label.stock_id) AS label_ids
                    ON label_ids.stock_id = stock.id";
    $sql_inv_add = '';
    if ($site !== '0') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." site.id=?"; $s++; 
        if (!isset($value1)) {
            $value1 = $site;
        }
    } 
    if ($area !== '0') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." area.id=?"; $s++; 
        if (!isset($value1)) {
            $value1 = $area;
        } else {
            $value2 = $area;
        } 
    } 
    if ($name !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." stock.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $name;
        } elseif (!isset($value2)) {
            $value2 = $name;
        } else {
            $value3 = $name;
        }
    }
    if ($sku !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." stock.sku LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $sku;
        } elseif (!isset($value2)) {
            $value2 = $sku;
        } elseif (!isset($value3)) {
            $value3 = $sku;
        } else {
            $value4 = $sku;
        } 
    }
    if ($location !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." area.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $location;
        } elseif (!isset($value2)) {
            $value2 = $location;
        } elseif (!isset($value3)) {
            $value3 = $location;
        } elseif (!isset($value4)) {
            $value4 = $location;
        } else {
            $value5 = $location;
        } 
    }
    if ($shelf !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." shelf.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $shelf;
        } elseif (!isset($value2)) {
            $value2 = $shelf;
        } elseif (!isset($value3)) {
            $value3 = $shelf;
        } elseif (!isset($value4)) {
            $value4 = $shelf;
        } elseif (!isset($value5)) {
            $value5 = $shelf;
        } else {
            $value6 = $shelf;
        } 
    }
    if ($label !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." label_names LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $label;
        } elseif (!isset($value2)) {
            $value2 = $label;
        } elseif (!isset($value3)) {
            $value3 = $label;
        } elseif (!isset($value4)) {
            $value4 = $label;
        } elseif (!isset($value5)) {
            $value5 = $label;
        } elseif (!isset($value6)) {
            $value6 = $label;
        } else {
            $value7 = $label;
        } 
    }
    if ($manufacturer !== '') { $qType = ($s < 1) ? 'WHERE' : 'AND'; $sql_inv_add  .= " ".$qType." manufacturer.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $manufacturer;
        } elseif (!isset($value2)) {
            $value2 = $manufacturer;
        } elseif (!isset($value3)) {
            $value3 = $manufacturer;
        } elseif (!isset($value4)) {
            $value4 = $manufacturer;
        } elseif (!isset($value5)) {
            $value5 = $manufacturer;
        } elseif (!isset($value6)) {
            $value6 = $manufacturer;
        } elseif (!isset($value7)) {
            $value7 = $manufacturer;
        } else {
            $value8 = $manufacturer;
        } 
    }
    if ($showOOS == 0) { 
        $qType = ($s < 1) ? 'WHERE' : 'AND'; 
        $sql_inv_add  .= " ".$qType." 
            (SELECT SUM(quantity) 
                FROM item 
                INNER JOIN shelf ON item.shelf_id=shelf.id
                INNER JOIN area ON shelf.area_id=area.id
                WHERE item.stock_id=stock.id AND area.site_id=site.id
            )!='null'";
    } 
    $sql_inv .= $sql_inv_add;
    // $sql_inv .= " ORDER BY stock.name;";
    $sql_inv .= " GROUP BY 
                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                    site_id, site_name, site_description, stock_img_image.stock_img_image
                ORDER BY stock.name;";
    // echo '<pre>'.$sql_inv.'</pre>';
    $stmt_inv = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
        echo("ERROR getting entries");
    } else {
        if ($s == 1) { mysqli_stmt_bind_param($stmt_inv, "s", $value1); }
        elseif ($s == 2) { mysqli_stmt_bind_param($stmt_inv, "ss", $value1, $value2); }
        elseif ($s == 3) { mysqli_stmt_bind_param($stmt_inv, "sss", $value1, $value2, $value3); }
        elseif ($s == 4) { mysqli_stmt_bind_param($stmt_inv, "ssss", $value1, $value2, $value3, $value4); }
        elseif ($s == 5) { mysqli_stmt_bind_param($stmt_inv, "sssss", $value1, $value2, $value3, $value4, $value5); }
        elseif ($s == 6) { mysqli_stmt_bind_param($stmt_inv, "ssssss", $value1, $value2, $value3, $value4, $value5, $value6); }
        elseif ($s == 7) { mysqli_stmt_bind_param($stmt_inv, "sssssss", $value1, $value2, $value3, $value4, $value5, $value6, $value7); }
        elseif ($s == 8) { mysqli_stmt_bind_param($stmt_inv, "ssssssss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8); }
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
                exit();
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
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:10px">Inventory');
                if ($site !== '0') { $area_name = $area == 0 ? "All" : $area_names_array[$area]; echo(' - '.$area_name);}
            echo('</h2>
            <p>Welcome, <or class="green">'.$profile_name.'</or>.</p>
            </div>

            <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:20px">
                <div class="nav-row">
                    <form action="./" method="get" class="nav-row" style="max-width:max-content">
                        <input id="query-site" type="hidden" name="site" value="'.$site.'" /> 
                        <input id="query-area" type="hidden" name="area" value="'.$area.'" />');
                        echo ('
                        <span id="search-input-site-span" style="margin-right: 10px; padding-left:12px">
                            <label for="search-input-site">Site</label><br>
                            <select id="site-dropdown" name="site" class="form-control nav-v-b cw" style="background-color:484848;border-color:black;margin:0;padding-left:0" onchange="siteChange(\'site-dropdown\')">
                            <option style="color:white" value="0"'); if ($area == 0) { echo('selected'); } echo('>All</option>
                        ');
                        if (!empty($site_names_array)) {
                            foreach (array_keys($site_names_array) as $site_id) {
                                $site_name = $site_names_array[$site_id];
                                echo('<option style="color:white" value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                            }
                        }
                        
                        echo('
                            </select>
                        </span>
                        ');  
                        echo ('
                        <span id="search-input-area-span" style="margin-right: 10px; padding-left:12px">
                            <label for="search-input-manufacturer">Area</label><br>
                                <select id="area-dropdown" name="area" class="form-control nav-v-b cw" style="background-color:#484848;border-color:black;margin:0;padding-left:0" onchange="areaChange(\'area-dropdown\')">
                                <option style="color:white" value="0"'); if ($area == 0) { echo('selected'); } echo('>All</option>
                            ');
                            if (!empty($area_names_array)) {
                                foreach (array_keys($area_names_array) as $area_id) {
                                    $area_name = $area_names_array[$area_id];
                                    echo('<option style="color:white" value="'.$area_id.'"'); if ($area == $area_id) { echo('selected'); } echo('>'.$area_name.'</option>');
                                }
                            }
                            
                        
                        echo('
                            </select>
                        </span>
                        ');
                        echo('
                        <span id="search-input-name-span" style="margin-right: 10px;margin-left:10px">
                            <label for="search-input-name">Name</label><br>
                            <input id="search-input-name" type="text" name="name" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                        </span>
                        <span id="search-input-sku-span" style="margin-right: 10px">
                            <label for="search-input-sku">SKU</label><br>
                            <input id="search-input-sku" type="text" name="sku" class="form-control" style="width:180px;display:inline-block" placeholder="Search by SKU" value="'); echo(isset($_GET['sku']) ? $_GET['sku'] : ''); echo('" />
                        </span>
                        <span id="search-input-shelf-span" style="margin-right: 10px" hidden>
                            <label for="search-input-shelf">Shelf</label><br>
                            <input id="search-input-shelf" type="text" name="shelf" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Shelf" value="'); echo(isset($_GET['shelf']) ? $_GET['shelf'] : ''); echo('" />
                        </span>
                        <span id="search-input-manufacturer-span" style="margin-right: 10px">
                            <label for="search-input-manufacturer">Manufacturer</label><br>
                            <input id="search-input-manufacturer" type="text" name="manufacturer" class="form-control" style="width:180px;display:inline-block" placeholder="Manufacturer" value="'); echo(isset($_GET['manufacturer']) ? $_GET['manufacturer'] : ''); echo('" />
                        </span>
                        <span id="search-input-label-span" style="margin-right: 10px">
                            <label for="search-input-label">Label</label><br>
                            <input id="search-input-label" type="text" name="label" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Label" value="'); echo(isset($_GET['label']) ? $_GET['label'] : ''); echo('" />
                        </span>
                        <input type="submit" value="submit" hidden>
                    </form>');

                    // these are now moved to the nav bar

                    // echo('
                    // <div id="add-div" class="nav-div nav-right" style="margin-right:5px" hidden>
                    //     <button id="add-stock" class="btn btn-success cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'add\'))">
                    //         <i class="fa fa-plus"></i> Add 
                    //     </button>
                    // </div>
                    // <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:0" hidden>
                    //     <button id="remove-stock" class="btn btn-danger cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'remove\'))">
                    //         <i class="fa fa-minus"></i> Remove 
                    //     </button>
                    // </div>');

                    echo('
                    <div id="clear-div" class="nav-div" style="margin-left:5px;margin-right:0">
                        <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black;padding:6 6 6 6" onclick="navPage(\'/\')">
                            <i class="fa fa-rotate-right" style="height:24px;padding-top:4px"></i>
                        </button>
                    </div>
                    <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0">');
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
                    </div>');
                    
                    echo('
                </div>
            </div>

        ');
        if ($rowCount_inv < 1) {
            echo ('<div class="container" id="no-inv-found">No Inventory Found</div>');
        } else {
            
            echo('
            <!-- Modal Image Div -->
            <div id="modalDiv" class="modal" onclick="modalClose()">
                <span class="close" onclick="modalClose()">&times;</span>
                <img class="modal-content bg-trans" id="modalImg">
                <div id="caption" class="modal-caption"></div>
            </div>
            <!-- End of Modal Image Div -->

            <!-- Table -->
            <div class="container">
                <table class="table table-dark centertable" id="inventoryTable">
                    <thead style="text-align: center; white-space: nowrap;">
                        <tr>
                            <th id="id" hidden>id</th>
                            <th id="img"</th>
                            <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(2, this)">Name</th>
                            <th class="clickable sorting" id="sku" onclick="sortTable(3, this)">SKU</th>
                            <th class="clickable sorting" id="quantity" onclick="sortTable(4, this)">Quantity</th>');
            if ($site == 0) { echo('<th class="clickable sorting" id="site" onclick="sortTable(5, this)">Site</th>'); }
                        echo('<th id="lables">Labels</th>
                        <th id="location">Location(s)</th>
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
                $stock_quantity_total = $row['item_quantity'];
                $stock_locations = $row['area_names'];
                $stock_site_id = $row['site_id'];
                $stock_site_name = $row['site_name'];
                $stock_label_names = ($row['label_names'] !== null) ? explode(", ", $row['label_names']) : '---';
                

                // Echo each row (inside of SQL results)
                echo('
                            <tr class="vertical-align align-middle"id="'.$stock_id.'">
                                <td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>
                                <td class="align-middle" id="'.$stock_id.'-img-td">
                                ');
                                if (!is_null($stock_img_file_name)) {
                                    echo('<img id="'.$stock_id.'-img" class="inv-img thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />');
                                }
                                echo('</td>
                                <td class="align-middle link gold" id="'.$stock_id.'-name" onclick="navPage(\'./stock.php?stock_id='.$stock_id.'\')">'.$stock_name.'</td>
                                <td class="align-middle" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                                <td class="align-middle" id="'.$stock_id.'-quantity">'); 
                                if ($stock_quantity_total == 0) {
                                    echo('<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>');
                                } else {
                                     echo($stock_quantity_total);
                                }
                                echo('</td>');
                if ($site == 0) { echo ('<td class="align-middle link gold" id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'); }
                            echo('<td class="align-middle" id="'.$stock_id.'-label">');
                            if (is_array($stock_label_names)) {
                                for ($o=0; $o < count($stock_label_names); $o++) {
                                    $divider = $o < count($stock_label_names)-1 ? ', ' : '';
                                    echo('<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'label\', \''.$stock_label_names[$o].'\'))">'.$stock_label_names[$o].'</or>'.$divider);
                                }
                            } 
                            echo('</td>
                            <td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                            </tr>
                ');
            }

            // End table + body
            echo ('
                    </body>
                </table>
            </div>
            ');


        }
    }

    ?>

</body>