<?php 
// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?></title>
</head>
<body>
    <?php // dependency PHP
    // $show_inventory = 1; // for nav.php to show the site and area on the banner - no longer used.
    ?>
    
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="content">
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

        if (isset($_GET['rows'])) {
            if ($_GET['rows'] == 50 || $_GET['rows'] == 100) {
                $rowSelectValue = $_GET['rows'];
            } else {
                $rowSelectValue = 10;
            }
        } else {
            $rowSelectValue = 10;
        }

        
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
        $sql_inv_count = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, 
                            stock.min_stock AS stock_min_stock, stock.is_cable AS stock_is_cable,
                        GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
                        site.id AS site_id, site.name AS site_name, site.description AS site_description,";
        if ($area != 0) {
            $sql_inv_count .=" area.id as area_id_global,";
        }
        $sql_inv_count .=   " (SELECT SUM(quantity) 
                            FROM item 
                            INNER JOIN shelf ON item.shelf_id=shelf.id
                            INNER JOIN area ON shelf.area_id=area.id
                            WHERE item.stock_id=stock.id AND area.site_id=site.id";
        if ($area != 0) {
            $sql_inv_count .=       " AND shelf.area_id=area_id_global";
        }
        $sql_inv_count .=   " ) AS item_quantity,
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
                        ON label_ids.stock_id = stock.id
                        WHERE stock.is_cable=0";
        $sql_inv_add = '';
        if ($site !== '0') { $sql_inv_add  .= " AND site.id=$site"; $s++; } 
        if ($area !== '0') { $sql_inv_add  .= " AND area.id=$area"; $s++; } 
        if ($name !== '') { $sql_inv_add  .= " AND stock.name LIKE CONCAT('%', '$name', '%')"; $s++; }
        if ($sku !== '') { $sql_inv_add  .= " AND stock.sku LIKE CONCAT('%', '$sku', '%')"; $s++; }
        if ($location !== '') { $sql_inv_add  .= " AND area.name LIKE CONCAT('%', '$location', '%')"; $s++; }
        if ($shelf !== '') { $sql_inv_add  .= " AND shelf.name LIKE CONCAT('%', '$shelf', '%')"; $s++; }
        if ($label !== '') { $sql_inv_add  .= " AND label_names LIKE CONCAT('%', '$label', '%')"; $s++; }
        if ($manufacturer !== '') { $sql_inv_add  .= " AND manufacturer.name LIKE CONCAT('%', '$manufacturer', '%')"; $s++; }
        if ($showOOS == 0) { 
            $sql_inv_add  .= " AND 
                (SELECT SUM(quantity) 
                    FROM item 
                    INNER JOIN shelf ON item.shelf_id=shelf.id
                    INNER JOIN area ON shelf.area_id=area.id
                    WHERE item.stock_id=stock.id AND area.site_id=site.id
                )!='null'";
        } 
        $sql_inv_count .= $sql_inv_add;
        // $sql_inv .= " ORDER BY stock.name;";
        $sql_inv_count .= " GROUP BY 
                        stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable, 
                        site_id, site_name, site_description, stock_img_image.stock_img_image";
        if ($area != 0) { $sql_inv .= ", area.id"; }
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
                        // echo ("No sites found");
                        // exit();
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

                // Check for site/area/shelf count
                $siteCount = $rowCount_site;

                $sql_areaCheck = "SELECT DISTINCT area.id, area.name, area.description
                            FROM area 
                            ORDER BY area.id";
                $stmt_areaCheck = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_areaCheck, $sql_areaCheck)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_areaCheck);
                    $result_areaCheck = mysqli_stmt_get_result($stmt_areaCheck);
                    $rowCount_areaCheck = $result_areaCheck->num_rows;
                    $areaCount = $rowCount_areaCheck;
                }

                $sql_shelfCheck = "SELECT DISTINCT site.id, site.name    
                            FROM site 
                            ORDER BY site.id";
                $stmt_shelfCheck = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_shelfCheck, $sql_shelfCheck)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_shelfCheck);
                    $result_shelfCheck = mysqli_stmt_get_result($stmt_shelfCheck);
                    $rowCount_shelfCheck = $result_shelfCheck->num_rows;
                    $shelfCount = $rowCount_shelfCheck;
                }

                if (!$siteCount > 0 || !$areaCount > 0 || !$shelfCount > 0) {
                    // missing sites or areas
                    echo('
                        <div class="container" style="padding-bottom:25px">
                            <h2 class="header-small" style="padding-bottom:10px">'.ucwords($current_system_name).'</h2>
                            <p>Welcome, <or class="green">'.$profile_name.'</or>.</p>
                            <p>There are no Sites, Areas or Shelves in the database. To continue, we need to add atleast one.<br> 
                            More can be added from the admin page.</p>
                        </div>
                        <div class="container">
                            
                            <form id="addLocations" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                <input type="hidden" name="index" value="1"/>
                                <table id="area-table">
                                    <tbody>
                                        <tr class="nav-row" id="area-headings" style="margin-bottom:20px">
                                            <th style="width:250px;"><h3 style="font-size:22px">Add Site</h3></th>
                                            <th style="width: 250px"></th>
                                        </tr>
                                        <tr class="nav-row" id="site-name-row">
                                            <td id="site-name-label" style="width:250px;margin-left:25px">
                                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="site-name">Site Name:</p>
                                            </td>
                                            <td id="site-name-input">
                                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="site-name" name="site-name"required>
                                            </td>
                                        </tr>
                                        <tr class="nav-row" id="site-description-row">
                                            <td id="site-description-label" style="width:250px;margin-left:25px">
                                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="site-description">Site Description:</p>
                                            </td>
                                            <td id="site-description-input">
                                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="site-description" name="site-description"required>
                                            </td>
                                        </tr>
                                        
                                        <tr class="nav-row" id="area-headings" style="margin-top:50px;margin-bottom:20px">
                                            <th style="width:250px;"><h3 style="font-size:22px">Add Area</h3></th>
                                            <th style="width: 250px"></th>
                                        </tr>
                                        <tr class="nav-row" id="area-name-row">
                                            <td id="area-name-label" style="width:250px;margin-left:25px">
                                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="area-name">Area Name:</p>
                                            </td>
                                            <td id="area-name-input">
                                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="area-name" name="area-name"required>
                                            </td>
                                        </tr>
                                        <tr class="nav-row" id="area-description-row">
                                            <td id="area-description-label" style="width:250px;margin-left:25px">
                                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="area-description">Area Description:</p>
                                            </td>
                                            <td id="area-description-input">
                                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="area-description" name="area-description"required>
                                            </td>
                                        </tr>

                                        <tr class="nav-row" id="shelf-headings" style="margin-top:50px;margin-bottom:20px">
                                            <th style="width:250px;"><h3 style="font-size:22px">Add Shelf</h3></th>
                                            <th style="width: 250px"></th>
                                        </tr>
                                        <tr class="nav-row" id="shelf-name-row">
                                            <td id="shelf-name-label" style="width:250px;margin-left:25px">
                                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="shelf-name">Shelf Name:</p>
                                            </td>
                                            <td id="shelf-name-input">
                                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="shelf-name" name="shelf-name"required>
                                            </td>
                                        </tr>
                                        
                                        <tr class="nav-row" style="margin-top:20px">
                                            <td style="width:250px">
                                                <input id="location-submit" type="submit" name="location-submit" class="btn btn-success" style="margin-left:25px" value="Submit">
                                            </td>
                                            <td style="width:250px">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    ');
                } else {
                    // all is as expected. we have sites and areas
                    echo('
                        <div class="container" style="padding-bottom:20px">
                            <h2 class="header-small" style="padding-bottom:10px">'.ucwords($current_system_name));
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
                                        <input id="search-input-name" type="text" name="name" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                                    </span>
                                    <span id="search-input-sku-span" style="margin-right: 10px">
                                        <label for="search-input-sku">SKU</label><br>
                                        <input id="search-input-sku" type="text" name="sku" class="form-control" style="width:160px;display:inline-block" placeholder="Search by SKU" value="'); echo(isset($_GET['sku']) ? $_GET['sku'] : ''); echo('" />
                                    </span>
                                    <span id="search-input-shelf-span" style="margin-right: 10px" hidden>
                                        <label for="search-input-shelf">Shelf</label><br>
                                        <input id="search-input-shelf" type="text" name="shelf" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Shelf" value="'); echo(isset($_GET['shelf']) ? $_GET['shelf'] : ''); echo('" />
                                    </span>
                                    <span id="search-input-manufacturer-span" style="margin-right: 10px">
                                        <label for="search-input-manufacturer">Manufacturer</label><br>
                                        <input id="search-input-manufacturer" type="text" name="manufacturer" class="form-control" style="width:160px;display:inline-block" placeholder="Manufacturer" value="'); echo(isset($_GET['manufacturer']) ? $_GET['manufacturer'] : ''); echo('" />
                                    </span>
                                    <span id="search-input-label-span" style="margin-right: 10px">
                                        <label for="search-input-label">Label</label><br>
                                        <input id="search-input-label" type="text" name="label" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Label" value="'); echo(isset($_GET['label']) ? $_GET['label'] : ''); echo('" />
                                    </span>
                                    <input type="submit" value="submit" hidden>
                                </form>');

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
                                </div>
                                <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0">
                                    <button id="cable-stock" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6 6 6 6" onclick="navPage(\'cablestock.php\')">
                                        Fixed Cables
                                    </button>
                                </div>
                                ');
                                
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
                            <table class="table table-dark centertable" id="inventoryTable" style="margin-bottom:0px">
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
                                                echo('<img id="'.$stock_id.'-img" class="inv-img-25h thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />');
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

                        // PAGE COUNT
                        
                        echo('
                        <tr style="background-color:#21272b">
                            <td colspan="100%" style="padding:0;margin:0">
                            <div class="row">
                                <div class="col text-center"></div>
                                <div class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">');
                    
                        if ($total_pages > 1) {
                            if ($current_page > 1) {
                                echo('&nbsp;<or class="gold clickable" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>');
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo('&nbsp;<span class="current-page blue">' . $i . '</span>');
                                    // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                } else {
                                    echo('&nbsp;<or class="gold clickable" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>');
                                }
                            }

                            if ($current_page < $total_pages) {
                                echo('&nbsp;<or class="gold clickable" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>');
                            }  
                        }

                        echo('</div>
                            <div class="col text-center">
                                <table style="margin-left:auto; margin-right:20px">
                                    <tbody>
                                        <tr>
                                            <td class="cw align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                Rows: 
                                            </td>
                                            <td class="align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                <select id="tableRowCount" class="form-control" style="width:50px;height:25px; padding:0px" name="rows" onchange="navPage(updateQueryParameter(\'\', \'rows\', this.value))">
                                                    <option value="10"');  if($rowSelectValue == 10)  { echo('selected'); } echo('>10</option>
                                                    <option value="50"');  if($rowSelectValue == 50)  { echo('selected'); } echo('>50</option>
                                                    <option value="100"'); if($rowSelectValue == 100) { echo('selected'); } echo('>100</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        ');

                        // End table + body
                        echo ('
                                </body>
                            </table>
                        </div>
                        ');
                    }
                }
            }
        }

        ?>
    </div> 
    
    <?php include 'foot.php'; ?>

</body>