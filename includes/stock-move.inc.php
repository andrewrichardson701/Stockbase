<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// INCLUDED IN THE STOCK PAGE FOR MOVING STOCK OR INVENTORY TO CURRENT STOCK

// Query string bits
$stock_id = isset($_GET['stock_id']) ? $_GET['stock_id'] : '';

// Redirect using javascript or if they have disabled it, using meta tag.
if ($stock_id == 0 || $stock_id == '0') {
    $url = "../stock.php?modify=move&error=stock_id not set";
    echo '<script type="text/javascript">';
    echo 'window.location.href="'.$url.'";';
    echo '</script>';
    echo '<noscript>';
    echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
    echo '</noscript>'; 
    exit();
}

// include 'head.php';
?>
<!-- <div style="margin-bottom:200px"></div> -->

<div class="container well-nopad theme-divBg">
    
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    echo('
        <input id="hidden-page-number" type="hidden" value="'.$page.'" />
        <pre id="hidden-sql" hidden></pre>
    ');
    if (is_numeric($stock_id)) {
        $input_name          = isset($_GET['name'])          ? $_GET['name'] : '';
        $input_sku           = isset($_GET['sku'])           ? $_GET['sku'] : '';
        $input_description   = isset($_GET['description'])   ? $_GET['description'] : '';
        $input_min_stock     = isset($_GET['min_stock'])     ? $_GET['min_stock'] : '';
        $input_tags          = isset($_GET['tags'])          ? $_GET['tags'] : '';
         
        $input_upc           = isset($_GET['upc'])           ? $_GET['upc'] : '';
        $input_manufacturer  = isset($_GET['manufacturer'])  ? $_GET['manufacturer'] : '';
        $input_site          = isset($_GET['site'])          ? $_GET['site'] : '';
        $input_area          = isset($_GET['area'])          ? $_GET['area'] : '';
        $input_shelf         = isset($_GET['shelf'])         ? $_GET['shelf'] : '';
        $input_cost          = isset($_GET['cost'])          ? $_GET['cost'] : '';
        
        $input_quantity      = isset($_GET['quantity'])      ? $_GET['quantity'] : '';
        $input_serial_number = isset($_GET['serial_number']) ? $_GET['serial_number'] : '';
        $input_reason        = isset($_GET['reason'])        ? $_GET['reason'] : '';

        
        if ($stock_id !== 0 || $stock_id !== '0') {
            include 'includes/dbh.inc.php';
            $sql = "SELECT id, name, description, sku, min_stock, is_cable
                    FROM stock
                    WHERE id=? AND stock.deleted=0
                    ORDER BY id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                // fails to connect
            } else {
                mysqli_stmt_bind_param($stmt, "s", $stock_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    echo('<p class="red">No Stock Info Found...</p>');
                } else {
                    // rows found
                    $row = $result->fetch_assoc(); 

                    $data_id = $row['id'];
                    $data_name = $row['name'];
                    $data_description = $row['description'];
                    $data_sku = $row['sku'];
                    $data_min_stock = $row['min_stock'];
                    $data_is_cable = $row['is_cable'];

                    if ($data_is_cable == 0) {
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                            area.id AS area_id, area.name AS area_name,
                                            shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                            item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, 
                                            ic.id AS ic_id, ic.container_id AS ic_container_id, ic.container_is_item AS ic_container_is_item,
                                            cs.name AS cs_name,
                                            c.name AS c_name,
                                            item.is_container AS item_is_container,
                                            ic2.container_id AS ic2_container_id,
                                            sum(item.quantity) as item_quantity,
                                            manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                                            (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                FROM stock_tag 
                                                INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                WHERE stock_tag.stock_id = stock.id
                                                ORDER BY tag.name
                                            ) AS tag_names,
                                            (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                FROM stock_tag
                                                INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                WHERE stock_tag.stock_id = stock.id
                                                ORDER BY tag.name
                                            ) AS tag_ids
                                        FROM stock
                                        LEFT JOIN item ON stock.id=item.stock_id
                                        LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                        LEFT JOIN area ON shelf.area_id=area.id 
                                        LEFT JOIN site ON area.site_id=site.id
                                        LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                        LEFT JOIN item_container AS ic ON item.id=ic.item_id
                                        LEFT JOIN item AS ci ON ci.id=ic.container_id AND ic.container_is_item = 1 /* used to check if the item is in a container */
                                        LEFT JOIN stock AS cs ON cs.id=ci.stock_id
                                        LEFT JOIN container AS c ON c.id = ic.container_id AND ic.container_is_item = 0
                                        LEFT JOIN item_container AS ic2 ON ic2.container_id=item.id AND ic2.container_is_item = 1 /* check if the item itself is a container */
                                        WHERE stock.id=? AND item.quantity!=0 AND stock.deleted=0
                                        GROUP BY 
                                            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                            site_id, site_name, site_description, 
                                            area_id, area_name, 
                                            shelf_id, shelf_name,
                                            manufacturer_name, manufacturer_id,
                                            item_serial_number, item_upc, item_comments, item_cost,
                                            ic_id, ic_container_id, ic_container_is_item,
                                            cs_name,
                                            c_name,
                                            ic2_container_id,
                                            item_is_container
                                        ORDER BY site.id, area.name, shelf.name;";
                    } else {
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                            area.id AS area_id, area.name AS area_name,
                                            shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                            cable_item.id AS item_id, cable_item.cost AS item_cost, cable_item.quantity AS item_quantity,
                                            (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                FROM stock_tag 
                                                INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                WHERE stock_tag.stock_id = stock.id
                                                ORDER BY tag.name
                                            ) AS tag_names,
                                            (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                FROM stock_tag
                                                INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                WHERE stock_tag.stock_id = stock.id
                                                ORDER BY tag.name
                                            ) AS tag_ids
                                        FROM stock
                                        LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                        LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                        LEFT JOIN area ON shelf.area_id=area.id 
                                        LEFT JOIN site ON area.site_id=site.id
                                        WHERE stock.id=? AND quantity!=0 AND stock.deleted=0 
                                        GROUP BY 
                                            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                            site_id, site_name, site_description, 
                                            area_id, area_name, 
                                            shelf_id, shelf_name,
                                            item_id, item_cost, item_quantity
                                        ORDER BY site.id, area.name, shelf.name;";
                    }
                    $stmt_stock = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                        mysqli_stmt_execute($stmt_stock);
                        $result_stock = mysqli_stmt_get_result($stmt_stock);
                        $rowCount_stock = $result_stock->num_rows;

                        if ($rowCount_stock < 1) {
                            echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                        } else {
                            $stock_inv_data = [];
                            while ( $row = $result_stock->fetch_assoc() ) {
                                $stock_id = $row['stock_id'];
                                $stock_name = $row['stock_name'];
                                $stock_sku = $row['stock_sku'];
                                $stock_quantity_total = $row['item_quantity'];
                                $stock_shelf_id = $row['shelf_id'];
                                $stock_shelf_name = $row['shelf_name'];
                                $stock_area_id = $row['area_id'];
                                $stock_area_name = $row['area_name'];
                                $stock_site_id = $row['site_id'];
                                $stock_site_name = $row['site_name'];
                                if ($data_is_cable == 0) {
                                    $stock_manufacturer_id = $row['manufacturer_id'];
                                    $stock_manufacturer_name = $row['manufacturer_name'];
                                    $item_upc = $row['item_upc'];
                                    $item_comments = $row['item_comments'];
                                    $item_serial_number = $row['item_serial_number'];
                                } else {
                                    $item_id = $row['item_id'];
                                }
                                $item_cost = $row['item_cost'];
                                $stock_tag_ids = $row['tag_ids'];
                                $stock_tag_names = $row['tag_names'];

                                $stock_ic_id = isset($row['ic_id']) ? isset($row['ic_id']) : '';
                                $stock_container_id = $stock_ic_container_id = isset($row['ic_container_id']) ? $row['ic_container_id'] : '';
                                $stock_item_is_container = isset($row['item_is_container']) ? $row['item_is_container'] : '';
                                $stock_container_name = isset($row['cs_name']) ? $row['cs_name'] : (isset($row['c_name']) ? $row['c_name'] : '');
                                $stock_item_container_id = isset($row['ic2_container_id']) ? $row['ic2_container_id'] : '';

                                $stock_tag_data = [];

                                if ($stock_tag_ids !== null) {
                                    for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                                        $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                                            'name' => explode(", ", $stock_tag_names)[$n]);
                                    }
                                } else {
                                    $stock_tag_data = '';
                                }
                                
                                if ($data_is_cable == 0) {
                                    $stock_inv_data[] = array('id' => $stock_id,
                                                            'name' => $stock_name,
                                                            'sku' => $stock_sku,
                                                            'quantity' => $stock_quantity_total,
                                                            'in_container' => $stock_ic_id,
                                                            'item_container_id' => $stock_item_container_id,
                                                            'container_is_item' => $stock_item_is_container,
                                                            'container_id' => $stock_container_id,
                                                            'container_name' => $stock_container_name,
                                                            'shelf_id' => $stock_shelf_id,
                                                            'shelf_name' => $stock_shelf_name,
                                                            'area_id' => $stock_area_id,
                                                            'area_name' => $stock_area_name,
                                                            'site_id' => $stock_site_id,
                                                            'site_name' => $stock_site_name,
                                                            'manufacturer_id' => $stock_manufacturer_id,
                                                            'manufacturer_name' => $stock_manufacturer_name,
                                                            'upc' => $item_upc,
                                                            'cost' => $item_cost,
                                                            'comments' => $item_comments,
                                                            'serial_number' => $item_serial_number,
                                                            'tag' => $stock_tag_data);
                                } else {
                                    $stock_inv_data[] = array('id' => $stock_id,
                                                            'name' => $stock_name,
                                                            'sku' => $stock_sku,
                                                            'quantity' => $stock_quantity_total,
                                                            'shelf_id' => $stock_shelf_id,
                                                            'shelf_name' => $stock_shelf_name,
                                                            'area_id' => $stock_area_id,
                                                            'area_name' => $stock_area_name,
                                                            'site_id' => $stock_site_id,
                                                            'site_name' => $stock_site_name,
                                                            'item_id' => $item_id,
                                                            'cost' => $item_cost,
                                                            'tag' => $stock_tag_data);
                                }
                            }
                            
                            $stock_id = $_GET['stock_id'];
                            echo('<div class="nav-row" style="margin-top: 2px; margin-bottom:5px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div id="heading-heading" style="margin-left:10vw;">
                                            <a href="../stock.php?stock_id='.$stock_id.'"><h2 id="stock_name">'.$data_name.'</h2></a>
                                            <p id="sku" style="margin-bottom:0px;padding-bottom:0px"><strong>SKU:</strong> <or class="blue">'.$data_sku.'</or></p>
                                            <p class="green"');
                                                if (isset($_GET['success']) && $_GET['success'] == 'stockMoved') {
                                                    echo (' style="margin-bottom:0">Stock Moved!');
                                                } else{
                                                    echo(' style="margin-bottom:24px">');
                                                }
                                        echo('</p>
                                        </div>
                                    </div>
                                </div>');
                                    echo('
                                <div style="width:100%">
                                    <table class="table table-dark theme-table centertable" style="max-width:max-content">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th hidden>ID</th>
                                                <th>Site</th>
                                                <th>Location</th>
                                                <th>Shelf</th>
                                                ');
                                                if ($data_is_cable == 0) {
                                                    echo('
                                                    <th>Container</th>
                                                    <th class="viewport-mid-large">Manufacturer</th>
                                                    <th class="viewport-small-only-empty">Manu.</th>
                                                    <th class="viewport-mid-large">UPC</th>
                                                    <th title="Serial Numbers">Serial</th>
                                                    <th'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('>Cost</th>
                                                    <th class="viewport-mid-large">Comments</th>
                                                    ');
                                                } else {
                                                    echo('
                                                    <th'); if($current_cost_enable_cable == 0) {echo(' hidden');} echo('>Cost</th>
                                                    ');
                                                }
                                                echo('
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>                           
                                    ');
                                    for ($i=0; $i<count($stock_inv_data); $i++) {
                                        echo('
                                            <tr id="item-'.$i.'" class="row-show clickable'); if (isset($_GET['edited']) && $_GET['edited'] == $i) { echo(' last-edit'); } echo('" onclick="toggleHidden(\''.$i.'\')">
                                                <td hidden>'.$i.'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'">'.$stock_inv_data[$i]['site_name'].'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'">'.$stock_inv_data[$i]['area_name'].'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'-'.$stock_inv_data[$i]['shelf_id'].'">'.$stock_inv_data[$i]['shelf_name'].'</td>
                                                ');
                                                if ($data_is_cable == 0) {
                                                    echo('
                                                    <td class="text-center" id="item-'.$i.'-container-'.$stock_inv_data[$i]['container_id'].'">');
                                                        if ($stock_inv_data[$i]['in_container'] != '')  echo('<a href="containers.php?container_id=3295&con_is_item='.$stock_inv_data[$i]['container_is_item'].'">'.$stock_inv_data[$i]['container_name'].'</a>'); 
                                                        if ($stock_inv_data[$i]['container_is_item'] !== 0) { echo('<i class="fa-solid fa-check" style="color:lime;padding-left:5px"></i>'); } 
                                                echo('</td>
                                                    <td id="item-'.$i.'-manu-'.$stock_inv_data[$i]['manufacturer_id'].'">'.$stock_inv_data[$i]['manufacturer_name'].'</td>
                                                    <td id="item-'.$i.'-upc" class="viewport-mid-large">'.$stock_inv_data[$i]['upc'].'</td>
                                                    <td id="item-'.$i.'-sn">'.$stock_inv_data[$i]['serial_number'].'</td>
                                                    <td id="item-'.$i.'-cost"'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('>'.$config_currency.$stock_inv_data[$i]['cost'].'</td>
                                                    <td id="item-'.$i.'-comments" class="viewport-mid-large">'.$stock_inv_data[$i]['comments'].'</td>
                                                    ');
                                                } else {
                                                    echo('
                                                    <td id="item-'.$i.'-cost"'); if($current_cost_enable_cable == 0) {echo(' hidden');} echo('>'.$config_currency.$stock_inv_data[$i]['cost'].'</td>
                                                    ');
                                                }
                                                echo('
                                                <td id="item-'.$i.'-stock">'.$stock_inv_data[$i]['quantity'].'</td>
                                            </tr>
                                            <tr class="row-hide" id="item-'.$i.'-edit" hidden>
                                                <td colspan=100%>
                                                    <div class="container">                                                       
                                                        <table class="centertable" style="border: 1px solid #454d55;">
                                                            <form class="" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                                                                <!-- Include CSRF token in the form -->
                                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                                <!-- below input used for the stock-modify.inc.php page to determine the type of change -->');
                                                                if ($data_is_cable == 0) {
                                                                    echo('<input type="hidden" name="stock-move" value="1" /> ');
                                                                } else {
                                                                    echo('<input type="hidden" name="cablestock-move" value="1" /> 
                                                                        <input type="hidden" name="redirect_url" value="stock.php?stock_id='.$stock_id.'&modify=move" />
                                                                        <input type="hidden" name="current_cable_item" value="'.$stock_inv_data[$i]['item_id'].'" />');
                                                                }
                                                                echo('
                                                                <input type="hidden" id="'.$i.'-c-i" name="current_i" value="'.$i.'" />
                                                                <input type="hidden" id="'.$i.'-c-stock" name="current_stock" value="'.$stock_id.'" />
                                                                <input type="hidden" id="'.$i.'-c-site" name="current_site" value="'.$stock_inv_data[$i]['site_id'].'" />
                                                                <input type="hidden" id="'.$i.'-c-area" name="current_area" value="'.$stock_inv_data[$i]['area_id'].'" />
                                                                <input type="hidden" id="'.$i.'-c-shelf" name="current_shelf" value="'.$stock_inv_data[$i]['shelf_id'].'" />
                                                                ');
                                                                if ($data_is_cable == 0) {
                                                                    if (isset($stock_inv_data[$i]['container_id']) && $stock_inv_data[$i]['container_id'] != '') {
                                                                        echo('<input type="hidden" name="in_container" value="1" />');
                                                                    }
                                                                    echo('
                                                                    <input type="hidden" id="'.$i.'-c-manufacturer" name="current_manufacturer" value="'.$stock_inv_data[$i]['manufacturer_id'].'" />
                                                                    <input type="hidden" id="'.$i.'-c-upc" name="current_upc" value="'.$stock_inv_data[$i]['upc'].'" />
                                                                    <input type="hidden" id="'.$i.'-c-serial" name="current_serial" value="'.$stock_inv_data[$i]['serial_number'].'" />
                                                                    <input type="hidden" id="'.$i.'-c-comments" name="current_comments" value="'.htmlspecialchars($stock_inv_data[$i]['comments'], ENT_QUOTES, 'UTF-8').'" />
                                                                    ');
                                                                }
                                                                echo('
                                                                <input type="hidden" id="'.$i.'-c-cost" name="current_cost" value="'.$stock_inv_data[$i]['cost'].'" />
                                                                <input type="hidden" id="'.$i.'-c-quantity" name="current_quantity" value="'.$stock_inv_data[$i]['quantity'].'" />
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="row">
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c">To:</label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-site" name="site" style="min-width:50px; padding:2px 0px 2px 0px;  width:max-content !important" required onchange="populateAreas(\''.$i.'\')">
                                                                                        <option value="" selected disabled hidden>Site</option>');
                                                                                            include 'includes/dbh.inc.php';
                                                                                            $sql = "SELECT id, name
                                                                                                    FROM site
                                                                                                    WHERE site.deleted=0
                                                                                                    ORDER BY id";
                                                                                            $stmt = mysqli_stmt_init($conn);
                                                                                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                                                                // fails to connect
                                                                                            } else {
                                                                                                mysqli_stmt_execute($stmt);
                                                                                                $result = mysqli_stmt_get_result($stmt);
                                                                                                $rowCount = $result->num_rows;
                                                                                                if ($rowCount < 1) {
                                                                                                    echo('<option value="0">No Sites Found...</option>');
                                                                                                } else {
                                                                                                    // rows found
                                                                                                    while ($row = $result->fetch_assoc()) {
                                                                                                        $sites_id = $row['id'];
                                                                                                        $sites_name = $row['name'];
                                                                                                        echo('<option value="'.$sites_id.'">'.$sites_name.'</option>');
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                    echo('
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-area" name="area" style="min-width:50px; padding:2px 0px 2px 0px; max-width:max-content !important" disabled required onchange="populateShelves(\''.$i.'\')">
                                                                                        <option value="" selected disabled hidden>Area</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-shelf" name="shelf" style="min-width:50px; padding:2px 0px 2px 0px; max-width:max-content !important" disabled required>
                                                                                        <option value="" selected disabled hidden>Shelf</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c" for="'.$i.'-n-quantity">Quantity: </label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="number" class="form-control nav-v-c row-dropdown" id="'.$i.'-n-quantity" name="quantity" style="min-width: 20px; padding: 2px 7px 2px 7px; max-width:50px;" placeholder="1" value="1" min="1" max="'.$stock_inv_data[$i]['quantity'].'" required />
                                                                                </div>
                                                                                ');
                                                                                if ($data_is_cable == 0) {
                                                                                    echo('
                                                                                    <div class="col" style="max-width:max-content !important">
                                                                                        <input type="number" class="form-control nav-v-c row-dropdown" id="'.$i.'-n-serial" name="serial" style="min-width: 80px; padding: 2px 7px 2px 7px; width:max-content; max-width:90px" placeholder="'); if (isset($stock_inv_data[$i]['serial_number']) && $stock_inv_data[$i]['serial_number'] !== '') { echo $stock_inv_data[$i]['serial_number']; } else { echo "No Serial Number"; } echo('" value="'.$stock_inv_data[$i]['serial_number'].'" disabled /> 
                                                                                    </div>
                                                                                    ');
                                                                                }
                                                                                echo('
                                                                                <div class="col" style="max-width:max-content !important">');
                                                                                if (isset($stock_inv_data[$i]['item_container_id']) && $stock_inv_data[$i]['item_container_id'] != '') {
                                                                                    echo('<input type="button" class="btn btn-warning nav-v-c btn-move" id="'.$i.'-n-submit" value="Move" style="opacity:80%;" name="submit" required onclick="modalLoadContainerMoveConfirmation('.$i.', '.$stock_inv_data[$i]['item_container_id'].')" />');
                                                                                } else {
                                                                                    echo('<input type="submit" class="btn btn-warning nav-v-c btn-move" id="'.$i.'-n-submit" value="Move" style="opacity:80%;" name="submit" required />');
                                                                                }
                                                                                echo('</div>
                                                                            </div>');
                                                                            if (isset($stock_inv_data[$i]['container_id']) && $stock_inv_data[$i]['container_id'] != '') {
                                                                                echo ('
                                                                                    <div class="row">
                                                                                        <div class="col text-center" style="width:100%">
                                                                                            <p class="red" style="margin:15px 0px 0px 0px">* Moving stock from within a container will remove the container link. *</p>
                                                                                        </div>
                                                                                    </div>
                                                                                ');
                                                                            }
                                                                            if (isset($stock_inv_data[$i]['container_is_item']) && $stock_inv_data[$i]['container_is_item'] == 1) {
                                                                                echo ('
                                                                                    <div class="row">
                                                                                        <div class="col text-center" style="width:100%">
                                                                                            <p style="margin:15px 0px 0px 0px"><or class="red">* This item is a container. Please consider its contents before moving. *</or><br>Check container: <a href="containers.php?container_id='.$stock_inv_data[$i]['item_container_id'].'&con_is_item=1">'.$stock_name.'</a></p>
                                                                                        </div>
                                                                                    </div>
                                                                                ');
                                                                            }
                                                                        echo('
                                                                        </td>
                                                                    </td>
                                                                </tbody>
                                                            </form>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        ');
                                    }
                                    echo('
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="container well-nopad theme-divBg" style="margin-top:5px">
                                <h2 style="font-size:22px">Transactions</h2>');
                                include 'includes/transaction.inc.php';
                        echo('</div>');
                        }
                    }
                }
            }
        }
    } else {
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        echo('
            <form action="?modify=move" method="GET" style="margin-bottom:0">
                <div class="container" id="stock-info-left">
                    <div class="nav-row" id="search-stock-row">
                        <input type="hidden" name="modify" id="modify" value="move" />
                        <span class="nav-row">
                            <p class="nav-v-c" style="margin-right:20px">Search for item</p>
                            <input class="form-control stock-inputSize" type="text" id="search" name="search" placeholder="Search for item" oninput="getInventory(1)" value="'.htmlspecialchars($search, ENT_QUOTES, 'UTF-8').'"/>
                        </span>
                    </div>
                </div>
            </form>
        ');
        
        // Put the table in place
        echo('
        <div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">
            <input type="hidden" id="inv-action-type" name="inv-action-type" value="move" />
            <table class="table table-dark theme-table" id="inventoryTable" style="padding-bottom:0;margin-bottom:0">
                <thead style="text-align: center; white-space: nowrap;">
                    <tr class="theme-tableOuter">
                        <th class="viewport-mid-large">ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th hidden>Descritpion</th>
                        <th>SKU</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="inv-body" class="align-middle" style="text-align: center; white-space: nowrap;">                       
                </tbody>
            </table>
            <table class="table table-dark theme-table centertable">
                <tbody>
                    <tr class="theme-tableOuter">
                        <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                        <div class="row">
                            <div class="col text-center"></div>
                            <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                            </div>
                            <div class="col text-center">
                            </div>
                        </div>
                    </tr>
                </tbody>
            </table>
        </div>
        ');
    }
    
    ?>
</div>

<!-- Container Move item Modal -->
<div id="modalDivContainerMoveConfirmation" class="modal">
    <span class="close" onclick="modalCloseContainerMoveConfirmation()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <form class="padding:0px;margin:0px" id="containerMoveForm" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="submit" value="1" />
            <input type="hidden" name="container-move" value="1" />
            <input type="hidden" id="containerMoveItemID" name="item_id" />
            <input type="hidden" id="containerMoveShelf" name="shelf_id" />
            <input type="hidden" id="containerMoveQuantity" name="quantity" />
        </form>
        <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
            <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Move Container Item</h4>
            <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="moveContainerItemID"></td><th style="padding-right:5px">Name:</th><td id="moveContainerItemName"></td></tr></tbody></table>
            <table class="centertable" style="margin-top:10px">
                <tbody style="border: none">
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will KEEP all child objects attached and move them with the current item. All child objects will be moved to the new location.">Move container and contents</or></td>
                        <td style="border: none"><input type="submit" form="containerMoveForm" class="btn btn-danger" name="container-move-all" value="Move All" /></td>
                    </tr>
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will UNLINK all child objects and ONLY move the selected item. All child objects will remain in the same location.">Move container only</or></td>
                        <td style="border: none"><input type="submit" form="containerMoveForm" class="btn btn-warning" name="container-move-single" value="Move Single Item" style="margin-top:5px;margin-bottom:5px"/></td>
                    </tr>
                </tbody>
            </table>
            <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                <p><strong>Contents</strong> - Items: <strong id="moveContainerChildCount" class="green"></strong></p>
                <table id="containerMoveContentsTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                    <thead>
                        <tr>
                            <th><!-- Image --></th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Name</th>
                            <th class="text-center align-middle">Description</th>
                            <th><!-- Unlink --></th>
                        </tr>
                    </thead>
                    <tbody id="containerMoveContentsTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
            <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseContainerMoveConfirmation()">Cancel</button>
        </span>
    </div>
</div>
<!-- End of Move Container Modal-->

<script>
    function modalLoadContainerMoveConfirmation(i, itemID) {
        var modal = document.getElementById("modalDivContainerMoveConfirmation");
        modal.style.display = "block";
        // Do some AJAX here to get the contents of the container and add it to the table
        // containerMoveContentsTableBody

        var countText = document.getElementById('moveContainerChildCount');
        var tableBody = document.getElementById('containerMoveContentsTableBody');
        var containerMoveItemID = document.getElementById('containerMoveItemID');
        var containerMoveShelf = document.getElementById('containerMoveShelf');
        var containerMoveQuantity = document.getElementById('containerMoveQuantity');

        var shelfSelect = document.getElementById(i+'-n-shelf');
        var quantityInput = document.getElementById(i+'-n-quantity');

        var moveContainerItemName = document.getElementById('moveContainerItemName');
        var moveContainerItemID = document.getElementById('moveContainerItemID');

        containerMoveItemID.value=itemID;
        containerMoveShelf.value=shelfSelect.value;
        containerMoveQuantity.value=quantityInput.value;

        moveContainerItemID.innerHTML = itemID;
        moveContainerItemName.innerHTML = document.getElementById('stock_name').innerHTML;

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stockajax.php?request-container-children=1&container_id="+itemID+"&container_is_item=1", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var data = JSON.parse(xhr.responseText);
                // console.log(data);
                var bodyExtras = '';
                var count = data['count'];
                countText.innerHTML = count;
                var trs = '';
                var tr = '';
                
                if (count > 0) {
                    for (let i=0; i<count; i++) {
                        if (data[i]) {
                            tr =`<tr class='linkTableRow'> 
                                    <td><img class='inv-img-main' src='assets/img/stock/`+data[i]['child_img_image']+`' alt='`+data[i]['child_stock_name']+`'></td> 
                                    <td class='text-center align-middle'>`+data[i]['child_item_id']+`</td> 
                                    <td class='text-center align-middle'>`+data[i]['child_stock_name']+`</td> 
                                    <td class='text-center align-middle'><or class='title' title='`+data[i]['child_stock_description']+`'>`+data[i]['child_stock_description'].substring(0,30)+`</or></td> 
                                    <td class='text-center align-middle'><button class='btn btn-danger' style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px"><i class="fa fa-unlink" ></i></button></td> 
                                </tr>`;
                            trs = trs+tr;
                        }
                    }
                }
                // console.log(trs);
                tableBody.innerHTML=trs;
                // console.log(trs);
            }
        };
        xhr.send();
    }

    function modalCloseContainerMoveConfirmation() { 
        var modal = document.getElementById("modalDivContainerMoveConfirmation");
        modal.style.display = "none";
        // Empty the table here too.
        // containerMoveContentsTableBody
    }
</script>
                                                                                    

<script> // for the select boxes
function populateAreas(id) {
    console.log(id);
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
function populateShelves(id) {
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
<script> // toggle hidden row below current
function toggleHidden(id) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-edit';
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
</script>
<script> // function to force the quantity input box to 1 with a max of 1 if a serial number is selected
function serialInputCheck(id) {
  var selectBox = document.getElementById(id+'-n-serial')
  var inputBox = document.getElementById(id+'-n-quantity');
  var currentValue = inputBox.value;
    // console.log(currentValue);
  var currentMaxQuantity = document.getElementById(id+'-c-quantity').value;

  if (selectBox.value !== '') {
    inputBox.value = '1';
    inputBox.setAttribute('max', '1');
  } else {
    inputBox.value = currentValue;
    inputBox.setAttribute('max', currentMaxQuantity);
  }
}
</script>
<script>
    function getInventory(search) {
        // Make an AJAX request to retrieve the corresponding sites
        var invBody = document.getElementById('inv-body');
        var pageNumberArea = document.getElementById('inv-page-numbers');
        var sql = document.getElementById('hidden-sql');
        // console.log(invBody);
        var name = document.getElementById('search').value;
        var page = document.getElementById('hidden-page-number').value;
        var type = document.getElementById('inv-action-type').value;

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stockajax.php?request-inventory-stock=1&name="+name+"&rows=10&page="+page+"&type="+type, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var inventory = JSON.parse(xhr.responseText);
                // console.log(inventory);
                var bodyExtras = '';
                var count = inventory[-1]['rows'];

                for (let i=0; i<count; i++) {
                    if (inventory[i]) {
                        var extras = bodyExtras+inventory[i];
                        bodyExtras = extras;
                    }
                }
                invBody.innerHTML = bodyExtras;
                pageNumberArea.innerHTML = inventory[-1]['page-number-area'];
                sql.innerText = inventory[-1]['sql'];
                
                if (search == 1) {
                    var newURL = inventory[-1]['url'];
                    window.history.pushState({ path: newURL }, '', newURL);
                }
            }
        };
        xhr.send();
    }

    if (document.getElementById('inventoryTable')) {
        document.onload=getInventory(0);
    }
</script>