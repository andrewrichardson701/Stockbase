<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// INCLUDED IN THE STOCK PAGE FOR REMOVING STOCK OR INVENTORY TO CURRENT STOCK

// Query string bits
$stock_id = isset($_GET['stock_id']) ? $_GET['stock_id'] : '';

// Redirect using javascript or if they have disabled it, using meta tag.
if ($stock_id == 0 || $stock_id == '0') {
    $url = "../stock.php?modify=remove&error=stock_id not set";
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

<!-- NEW STOCK -->
<div class="container well-nopad theme-divBg">
    
    <?php
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
            $sql = "SELECT id, name, description, sku, min_stock
                    FROM stock
                    WHERE id=? AND deleted=0
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
                    while ($row = $result->fetch_assoc()) {
                        $data_id = $row['id'];
                        $data_name = $row['name'];
                        $data_description = $row['description'];
                        $data_sku = $row['sku'];
                        $data_min_stock = $row['min_stock'];
                    }

                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, area.name AS area_name,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name,site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    (SELECT SUM(quantity) 
                                        FROM item 
                                        WHERE item.stock_id = stock.id AND item.shelf_id = shelf.id
                                    ) AS item_quantity,
                                    manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') 
                                        FROM stock_tag 
                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                        WHERE stock_tag.stock_id = stock.id
                                    ) AS tag_names,
                                    (SELECT GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') 
                                        FROM stock_tag
                                        WHERE stock_tag.stock_id = stock.id
                                    ) AS tag_ids
                                FROM stock
                                LEFT JOIN item ON stock.id=item.stock_id
                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                WHERE stock.id=? AND item.deleted=0 ANd stock.deleted=0
                                GROUP BY 
                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                    site_id, site_name, site_description, 
                                    area_id, area_name, 
                                    shelf_id, shelf_name,
                                    manufacturer_id, manufacturer_name
                                    ORDER BY site.name DESC, area.name ASC, shelf.name ASC;";
                    $stmt_stock = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                        mysqli_stmt_execute($stmt_stock);
                        $result_stock = mysqli_stmt_get_result($stmt_stock);
                        $rowCount_stock = $result_stock->num_rows;
                        if ($rowCount_stock < 1) {
                            $disabled = ' disabled';
                        } else {
                            $disabled = '';
                        }
                        $stock_inv_data = [];
                        $stock_inv_manu = [];
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
                            $stock_manufacturer_id = $row['manufacturer_id'];
                            $stock_manufacturer_name = $row['manufacturer_name'];
                            $stock_tag_ids = $row['tag_ids'];
                            $stock_tag_names = $row['tag_names'];
                            
                            $stock_tag_data = [];

                            if ($stock_tag_ids !== null) {
                                for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                                    $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                                        'name' => explode(", ", $stock_tag_names)[$n]);
                                }
                            } else {
                                $stock_tag_data = '';
                            }
                            

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
                                                    'manufacturer_id' => $stock_manufacturer_id,
                                                    'manufacturer_name' => $stock_manufacturer_name,
                                                    'tag' => $stock_tag_data);
                            
                            $stock_inv_manu[$stock_manufacturer_id] = array('id' => $stock_manufacturer_id, 'name' => $stock_manufacturer_name);
                        }
                        
                        $stock_id = $_GET['stock_id'];
                        echo('
                        
                        <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                            <!-- this is for the stock-modify.inc.php page -->
                            <input type="hidden" name="stock-remove" value="1" /> 
                            <div class="nav-row" style="margin-bottom:10px">
                                <div class="nav-row" id="heading-row" style="margin-top:10px">
                                    <div class="stock-inputLabelSize"></div>
                                    <div id="heading-heading">
                                        <a href="../stock.php?stock_id='.$stock_id.'"><h2>'.$data_name.'</h2></a>
                                        <p id="sku"><strong>SKU:</strong> <or class="blue">'.$data_sku.'</or></p>
                                        <p id="locations" style="margin-bottom:0"><strong>Locations:</strong><br>');
                                        if (empty($stock_inv_data)) {
                                            echo("No locations linked.");
                                        } else {
                                            echo('<table><tbody>');
                                            for ($l=0; $l < count($stock_inv_data); $l++) {
                                                // if ($l == 0 || $l < count($stock_inv_data)-1) { $divider = '<br>'; } else { $divider = ''; }
                                                echo('<tr><td>'.$stock_inv_data[$l]['area_name'].', '.$stock_inv_data[$l]['shelf_name'].'</td><td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">'.$stock_inv_data[$l]['quantity'].'</or></a></or></td></tr>');
                                            }
                                            echo('</tbody></table>');
                                        }
                                        echo('</p>
                                    </div>
                                </div>
                            </div>');
                            if ($rowCount_stock < 1) {
                                echo ('<div class="container red text-center" style="padding-bottom:10px" id="no-stock-found"><div class="row"><div class="stock-inputLabelSize"></div><div>No Stock Found</div></div></div>');
                            }
                            echo('
                            <div class="container well-nopad theme-divBg">
                                <div class="row">
                                    <div class="text-left" id="stock-info-left" style="padding-left:15px">
                                        <div class="nav-row" style="margin-bottom:25px">
                                            <input type="hidden" id="stock-id" value="'.$stock_id.'" name="stock_id" />
                                            <input type="hidden" value="'.$data_sku.'" name="stock_sku" />
                                            <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                                <div>
                                                    <select name="manufacturer" id="manufacturer" class="form-control stock-inputSize" onchange="populateRemoveShelves(this)" required'.$disabled.'>');
                                                        echo('<option value="" selected disabled hidden>Select Manufacturer</option>');
                                                        foreach ( $stock_inv_manu as $manu) {
                                                            echo('<option value='.$manu['id'].'>'.$manu['name'].'</option>');
                                                        }
                                                    echo('
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Location</label></div>
                                                <div>
                                                    <select class="form-control stock-inputSize" id="shelf" name="shelf" required onchange="populateSerials(this)" disabled>
                                                        <option value="" selected disabled hidden>Select Location</option>');
                                                        // $temp_site_id = '';
                                                        // foreach ($stock_inv_data as $temp_data) {
                                                        //     if ($temp_data['shelf_id'] !== $temp_site_id) {
                                                        //         echo('<option value='.$temp_data['shelf_id'].'>'.$temp_data['site_name'].' - '.$temp_data['area_name'].' - '.$temp_data['shelf_name'].'</option>');
                                                        //     }
                                                        //     $temp_site_id = $temp_data['shelf_id'];
                                                        // }
                                                    echo('
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="nav-row" id="price-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="price" id="price-label">Sale Price (£)</label></div>
                                                <div><input type="number" name="price" placeholder="0" id="price" class="form-control nav-v-c stock-inputSize" value="0" value="'.$input_cost.'" required'.$disabled.'></input></div>
                                            </div>
                                        </div>
                                        <hr style="border-color: gray; margin-right:15px">
                                        <div class="nav-row" style="margin-bottom:0">
                                            <div class="nav-row" id="date-row" style="margin-top:10px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="transaction_date" id="date-label">Transaction Date</label></div>
                                                <div><input type="date" value="'.date('Y-m-d').'" name="transaction_date" id="transaction_date" class="form-control" style="width:150px" required'.$disabled.'/></div>
                                            </div>
                                            <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Number to be tracked.">Serial Numbers</or></label></div>
                                                <div>
                                                    <select name="serial-number" id="serial-number" class="form-control stock-inputSize" value="'.$input_serial_number.'" '.$disabled.' onchange="getQuantity()">
                                                        <option value="" selected disabled hidden>Serial...</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="nav-row" id="quantity-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                                <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="'.$input_quantity.'" min="1" required'.$disabled.'></input></div>
                                            </div>
                                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                                <div><input type="text" name="reason" placeholder="Customer sale, ID: XXXXXX" id="reason" class="form-control nav-v-c stock-inputSize" value="'.$input_reason.'" required'.$disabled.'></input></div>
                                            </div>
                                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"></div>
                                                <div>');
                                                    
                                                    $stock_quantity_total = 0;
                                                    foreach ($stock_inv_data as $d) {
                                                        $stock_quantity_total += $d['quantity'];
                                                    }
                                                    if ($stock_quantity_total !== 0){
                                                        echo('<input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" />');
                                                    } else {
                                                        echo('<input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" disabled />');
                                                        echo('<a href="#" onclick="confirmAction(\''.$data_name.'\', \''.$data_sku.'\', \'includes/stock-modify.inc.php?stock_id='.$stock_id.'&type=delete\')" class="nav-v-c btn btn-danger cw" style="margin-left:300px"><strong><u>Delete Stock</u></strong></a>');
                                                    }
                                                echo('
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ');
                        echo('</form>');
                    }
                }
            }
        }
    } else {
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        echo('
            <form action="?modify=remove" method="GET" style="margin-bottom:0">
                <div class="container" id="stock-info-left">
                    <div class="nav-row" id="search-stock-row">
                        <input type="hidden" name="modify" id="modify" value="remove" />
                        <span class="nav-row">
                            <p class="nav-v-c" style="margin-right:20px">Search for item</p>
                            <input class="form-control stock-inputSize" type="text" id="search" name="search" placeholder="Search for item" value="'.$search.'"/>
                        </span>
                    </div>
                </div>
            </form>
        ');
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            echo('
            <div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">
                ');
            include 'includes/dbh.inc.php';
            $sql = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, 
                        (SELECT SUM(quantity) 
                            FROM item 
                            INNER JOIN shelf ON item.shelf_id=shelf.id
                            INNER JOIN area ON shelf.area_id=area.id
                            WHERE item.stock_id=stock.id
                        ) AS item_quantity,
                        stock_img_image.stock_img_image
                    FROM stock
                    LEFT JOIN item ON stock.id=item.stock_id
                    LEFT JOIN shelf ON item.shelf_id=shelf.id 
                    LEFT JOIN area ON shelf.area_id=area.id 
                    LEFT JOIN site ON area.site_id=site.id
                    LEFT JOIN (
                        SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                        FROM stock_img
                        GROUP BY stock_img.stock_id
                    ) AS stock_img_image
                        ON stock_img_image.stock_id = stock.id
                    WHERE stock.is_cable=0 AND stock.deleted=0 AND item.deleted=0 AND stock.name LIKE CONCAT('%', ?, '%')
                    GROUP BY 
                        stock.id, stock_name, stock_description, stock_sku, 
                        stock_img_image.stock_img_image
                    ORDER BY stock.name";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
            } else {
                mysqli_stmt_bind_param($stmt, "s", $search);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    echo('<p>No Stock Found</p>');
                } else {
                    echo('
                <table class="table table-dark theme-table" style="max-width:max-content">
                    <thead>
                        <tr class="theme-tableOuter">
                            <th class="viewport-mid-large" style="max-width:max-content">ID</th>
                            <th>Image</th>
                            <th>Stock Name</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody  class="align-middle" style="text-align: center; white-space: nowrap;">
                    ');
                    while ($row = $result->fetch_assoc() ) {
                        $id = $row['stock_id'];
                        $name = $row['stock_name'];
                        $sku = $row['stock_sku'];
                        $quantity = $row['item_quantity'];
                        echo('
                        <tr class="clickable vertical-align align-middle" onclick="window.location.href=\'stock.php?modify='.$_GET['modify'].'&stock_id='.$id.'\'">
                            <td class="viewport-mid-large" id="'.$id.'-id"  style="max-width:max-content">'.$id.'</td>
                            <td class="align-middle" id="'.$id.'-img-cell">');
                            if ($row['stock_img_image'] !== null && $row['stock_img_image'] !== '') {
                                echo ('<img id="'.$id.'-img" class="inv-img-main thumb" src="assets/img/stock/'.$row['stock_img_image'].'" alt="'.$row['stock_name'].'" title="'.$row['stock_name'].'" onclick="modalLoad(this)">');
                            } 
                        echo('
                            </td>
                            <td id="'.$id.'-name">'.$name.'</td>
                            <td id="'.$id.'-sku">'.$sku.'</td>
                            <td id="'.$id.'-quantity">'.$quantity.'</td>
                        </tr>
                        ');
                    }
                    echo('
                    </tbody>
                </table>'); 
                }
            }
            echo('
            </div>
            ');
        } else {
            $sql_total = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, 
                            (SELECT SUM(quantity) 
                                FROM item 
                                INNER JOIN shelf ON item.shelf_id=shelf.id
                                INNER JOIN area ON shelf.area_id=area.id
                                WHERE item.stock_id=stock.id
                            ) AS item_quantity,
                            stock_img_image.stock_img_image
                        FROM stock
                        LEFT JOIN item ON stock.id=item.stock_id
                        LEFT JOIN shelf ON item.shelf_id=shelf.id 
                        LEFT JOIN area ON shelf.area_id=area.id 
                        LEFT JOIN site ON area.site_id=site.id
                        LEFT JOIN (
                            SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                            FROM stock_img
                            GROUP BY stock_img.stock_id
                        ) AS stock_img_image
                            ON stock_img_image.stock_id = stock.id
                        WHERE stock.is_cable=0 AND stock.deleted=0 AND item.deleted=0
                        GROUP BY 
                            stock.id, stock_name, stock_description, stock_sku, 
                            stock_img_image.stock_img_image
                        ORDER BY stock.name;";
            $stmt_total = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_total, $sql_total)) {
                echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
            } else {
                mysqli_stmt_execute($stmt_total);
                $result_total = mysqli_stmt_get_result($stmt_total);
                $totalRowCount = $result_total->num_rows;
            }

            if ($totalRowCount > 0) {
                // Pagination settings
                $results_per_page = 10; // row count to show
                $total_pages = ceil($totalRowCount / $results_per_page);
    
                $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                if ($current_page < 1) {
                    $current_page = 1;
                } elseif ($current_page > $total_pages) {
                    $current_page = $total_pages;
                } 
                
                // Calculate the offset for the query
                $offset = ($current_page - 1) * $results_per_page;

                echo('
                <div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">');
                include 'includes/dbh.inc.php';
                $sql = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, 
                            (SELECT SUM(quantity) 
                                FROM item 
                                INNER JOIN shelf ON item.shelf_id=shelf.id
                                INNER JOIN area ON shelf.area_id=area.id
                                WHERE item.stock_id=stock.id
                            ) AS item_quantity,
                            stock_img_image.stock_img_image
                        FROM stock
                        LEFT JOIN item ON stock.id=item.stock_id
                        LEFT JOIN shelf ON item.shelf_id=shelf.id 
                        LEFT JOIN area ON shelf.area_id=area.id 
                        LEFT JOIN site ON area.site_id=site.id
                        LEFT JOIN (
                            SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                            FROM stock_img
                            GROUP BY stock_img.stock_id
                        ) AS stock_img_image
                            ON stock_img_image.stock_id = stock.id
                        WHERE stock.is_cable=0 AND stock.deleted=0 AND item.deleted=0
                        GROUP BY 
                            stock.id, stock_name, stock_description, stock_sku, 
                            stock_img_image.stock_img_image
                        ORDER BY stock.name
                        LIMIT $results_per_page OFFSET $offset;";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;

                    if ($rowCount < 1) {
                        echo('<p>No Stock Found</p>');
                    } else {
                        // rows found
                        echo('
                        <table class="table table-dark theme-table" id="inventoryTable" style="max-width:max-content">
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
                            <tbody class="align-middle" style="text-align: center; white-space: nowrap;">');
                            while($row = $result->fetch_assoc()){
                                if ($row['item_quantity'] == null || $row['item_quantity'] == 0 || $row['item_quantity'] == '') {
                                    $quantity = '<or class="red">0</or>';
                                } else {
                                    $quantity =  $row['item_quantity'];
                                }
                                echo('
                                <tr class="clickable vertical-align align-middle" id="'.$row['stock_id'].'" onclick="window.location.href=\'stock.php?modify='.$_GET['modify'].'&stock_id='.$row['stock_id'].'\'">
                                    <td class="align-middle viewport-mid-large" id="'.$row['stock_id'].'-id">'.$row['stock_id'].'</td>
                                    <td class="align-middle" id="'.$row['stock_id'].'-img-cell">');
                                    if ($row['stock_img_image'] !== null && $row['stock_img_image'] !== '') {
                                        echo ('<img id="'.$row['stock_id'].'-img" class="inv-img-main thumb" src="assets/img/stock/'.$row['stock_img_image'].'" alt="'.$row['stock_name'].'" title="'.$row['stock_name'].'" onclick="modalLoad(this)">');
                                    } 
                                echo('
                                    </td>
                                    <td class="align-middle" id="'.$row['stock_id'].'-name">'.$row['stock_name'].'</td>
                                    <td class="align-middle" id="'.$row['stock_id'].'-description" hidden>'.$row['stock_description'].'</td>
                                    <td class="align-middle" id="'.$row['stock_id'].'-sku">'.$row['stock_sku'].'</td>
                                    <td class="align-middle" id="'.$row['stock_id'].'-quantity">'.$quantity.'</td>
                                </tr>
                                ');
                            }
                            if ($total_pages > 1) {
                                echo('
                                <tr class="theme-tableOuter">
                                    <td colspan="100%">');
        
                                if ($current_page > 1) {
                                    echo('<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'#transactions\')"><</or>');
                                }

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    if ($i == $current_page) {
                                        echo('<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>');
                                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                    } else {
                                        echo('<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'#transactions\')">'.$i.'</or>');
                                    }
                                }

                                if ($current_page < $total_pages) {
                                    echo('<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'#transactions\')">></or>');
                                }  
                            } 
                        echo('    
                            </tbody>
                        </table>
                        ');
                    }
                }
                echo('
                </div>
                ');
            }
        }

    }
    
    ?>
    
</div>
<script>
    function confirmAction(stock_name, stock_sku, url) {
        var confirmed = confirm('Are you sure you want to proceed? \nThis will remove ALL entries for '+stock_name+' ('+stock_sku+').');
        if (confirmed) {
            window.location.href = url;
        }
    }

    // populate shelves from manuyfacturer
    function populateRemoveShelves(elem) {
        var stock = document.getElementById('stock-id').value;
        manufacturer_id = elem.value;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getremoveshelves=1&stock="+stock+"&manufacturer="+manufacturer_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var shelves = JSON.parse(xhr.responseText);
                var select = document.getElementById('shelf');
                select.options.length = 0;
                if (shelves.length === 0) {
                    select.options[0] = new Option("", "");
                }
                for (var i = 0; i < shelves.length; i++) {
                    if (i == 0) {
                        select.options[select.options.length] = new Option(shelves[i].location, shelves[i].id, true, true);
                    } else {
                        select.options[select.options.length] = new Option(shelves[i].location, shelves[i].id);
                    }
                    // select.options[select.options.length] = new Option(shelves[i].location, shelves[i].id);
                }
                select.disabled = (select.options.length === 0);
                // select.disabled = (select.options.length === 1);
                populateSerials(document.getElementById('shelf'));
            }
        };
        xhr.send();
    }

    // populate serials
    function populateSerials(elem) {
        var stock = document.getElementById('stock-id').value;
        shelf_id = elem.value;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getserials=1&stock="+stock+"&shelf="+shelf_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var serials = JSON.parse(xhr.responseText);
                var select = document.getElementById('serial-number');
                select.options.length = 0;
                if (serials.length === 0) {
                    select.options[0] = new Option("", "");
                }
                for (var i = 0; i < serials.length; i++) {
                    if (i == 0) {
                        select.options[select.options.length] = new Option(serials[i].serial_number, serials[i].serial_number, true, true);
                    } else {
                        select.options[select.options.length] = new Option(serials[i].serial_number, serials[i].serial_number);
                    }
                }
                // select.disabled = (select.options.length === 1);
                getQuantity();
            }
        };
        xhr.send();
       
    }

    function getQuantity() {
        var stock = document.getElementById('stock-id').value;
        var manufacturer = document.getElementById('manufacturer').value;
        var shelf = document.getElementById('shelf').value;
        var serial = document.getElementById('serial-number').value;
        
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getquantity=1&stock="+stock+"&shelf="+shelf+"&manufacturer="+manufacturer+"&serial="+serial, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var quantityArr = JSON.parse(xhr.responseText);
                var quantity = document.getElementById('quantity');
                quantity.value = 1;
                quantity.max = quantityArr[0]['quantity'];
                // console.log(quantity.max[0]);

                if (quantity.min === quantity.max) {
                    quantity.disabled = true;
                } else {
                    quantity.disabled = false;
                }
            }
        };
        xhr.send();
    }
</script>
<script>
    // Script to populare the remove fields from clicking the remove button in the stock table.
    document.onload=populateFields();
    async function populateFields() {
        const queryParams = new URLSearchParams(window.location.search);
        for (const key of queryParams.keys()) {
            console.log(key);
        }
        setTimeout(function () {
            if (queryParams.keys('manufacturer')) {
                // console.log(queryParams.get('manufacturer'));
                var manufacturerValue = queryParams.get('manufacturer');
                var manufacturerSelect = document.getElementById('manufacturer');
                for (let i = 0; i < manufacturerSelect.options.length; i++) {
                    const option = manufacturerSelect.options[i];
                    // Check if the option's value matches the 'manufacturer' query string parameter
                    if (option.value === manufacturerValue) {
                        // Set the 'selected' attribute if there is a match
                        option.selected = true;
                        break; // Exit the loop since we found the matching option
                    }
                }
                populateRemoveShelves(manufacturerSelect);
                setTimeout(function () {
                    if (queryParams.keys('shelf')) {
                        // console.log(queryParams.get('shelf'));
                        var shelfValue = queryParams.get('shelf');
                        var shelfSelect = document.getElementById('shelf');
                        for (let i = 0; i < shelfSelect.options.length; i++) {
                            const option = shelfSelect.options[i];
                            // Check if the option's value matches the 'shelf' query string parameter
                            if (option.value === shelfValue) {
                                // Set the 'selected' attribute if there is a match
                                option.selected = true;
                                break; // Exit the loop since we found the matching option
                            }
                        }
                        populateSerials(shelfSelect);
                        setTimeout(function () {
                            if (queryParams.keys('serial')) {
                                // console.log(queryParams.get('serial'));
                                var serialValue = queryParams.get('serial');
                                var serialSelect = document.getElementById('serial-number');
                                for (let i = 0; i < serialSelect.options.length; i++) {
                                    const option = serialSelect.options[i];
                                    // Check if the option's value matches the 'serial' query string parameter
                                    if (option.value === serialValue) {
                                        // Set the 'selected' attribute if there is a match
                                        option.selected = true;
                                        break; // Exit the loop since we found the matching option
                                    }
                                }
                                getQuantity();
                            }
                        }, 500);
                    }
                }, 500);
            }
        }, 300);
    }
</script>