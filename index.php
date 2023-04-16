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
                        WHERE item.stock_id = stock.id
                    ) AS item_quantity,
                    manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                    label_names.label_names AS label_names,
                    label_ids.label_ids AS label_ids,
                    stock_img_image.stock_img_image
                FROM stock
                INNER JOIN item ON stock.id=item.stock_id
                INNER JOIN shelf ON item.shelf_id=shelf.id 
                INNER JOIN area ON shelf.area_id=area.id 
                INNER JOIN site ON area.site_id=site.id
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
    if ($s !== 0) { $sql_inv .= $sql_inv_add; }
    // $sql_inv .= " ORDER BY stock.name;";
    $sql_inv .= " GROUP BY 
                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                    site_id, site_name, site_description, stock_img_image.stock_img_image,
                    manufacturer_id, manufacturer_name
                ORDER BY stock.name;";
    // echo '<pre>'.$sql_inv.'</pre>';
    $stmt_inv = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
        echo("ERROR getting entries");
    } else {
        if ($area != 0) {
            $area_name = $area_names_array[$area];
        } else {
            $area_name = "All";
        }
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
        echo('
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:10px">Inventory');
                if ($site !== '0') { echo(' - '.$area_name);}
            echo('</h2>
            <p>Welcome, <or class="green">'.$profile_name.'</or>.</p>
            </div>

            <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:20px">
                <div class="nav-row">
                    <form action="./" method="get" class="nav-row" style="max-width:max-content">
                        <input id="query-site" type="hidden" name="site" value="'.$site.'" /> 
                        <input id="query-area" type="hidden" name="area" value="'.$area.'" />
                        <span id="search-input-name-span" style="margin-right: 10px">
                            <label for="search-input-name">Name</label><br>
                            <input id="search-input-name" type="text" name="name" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                        </span>
                        <span id="search-input-sku-span" style="margin-right: 10px">
                            <label for="search-input-sku">SKU</label><br>
                            <input id="search-input-sku" type="text" name="sku" class="form-control" style="width:180px;display:inline-block" placeholder="Search by SKU" value="'); echo(isset($_GET['sku']) ? $_GET['sku'] : ''); echo('" />
                        </span>
                        <span id="search-input-shelf-span" style="margin-right: 10px">
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
                    </form>
                    <div id="add-div" class="nav-div nav-right" style="margin-right:5px">
                        <button id="add-stock" class="btn btn-success cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'add\'))">
                            <i class="fa fa-plus"></i> Add 
                        </button>
                    </div> 
                    <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:0">
                        <button id="remove-stock" class="btn btn-danger cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'remove\'))">
                            <i class="fa fa-minus"></i> Remove 
                        </button>
                    </div> 
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
                                <td class="align-middle" id="'.$stock_id.'-quantity">'.$stock_quantity_total.'</td>');
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