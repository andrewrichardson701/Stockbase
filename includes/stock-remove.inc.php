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
                        $data_is_cable = $row['is_cable'];
                    }

                    if ($data_is_cable == 0) {
                        $sql_stock = "SELECT i.id AS i_id, stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                            area.id AS area_id, area.name AS area_name,
                                            shelf.id AS shelf_id, shelf.name AS shelf_name,site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                            item.is_container AS item_is_container,
                                            (SELECT SUM(quantity) 
                                                FROM item 
                                                WHERE item.stock_id = stock.id AND item.shelf_id = shelf.id AND item.is_container=item_is_container AND (CASE WHEN item.is_container = 1 THEN item.id = i.id ELSE 1 END)
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
                                        LEFT JOIN item_container AS ic ON item.id=ic.container_id AND ic.container_is_item=1
                                        LEFT JOIN item AS i ON item.id=i.id AND item.is_container=1
                                        WHERE stock.id=? AND stock.deleted=0 AND item.deleted=0
                                        GROUP BY 
                                            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                            site_id, site_name, site_description, 
                                            area_id, area_name, 
                                            shelf_id, shelf_name,
                                            manufacturer_id, manufacturer_name,
                                            item_is_container,
                                            i.id
                                        ORDER BY site.name DESC, area.name ASC, shelf.name ASC;";
                    } else {
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                        area.id AS area_id, area.name AS area_name,
                                        shelf.id AS shelf_id, shelf.name AS shelf_name,site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                        cable_item.quantity AS item_quantity,
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
                                    LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                    LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                    LEFT JOIN area ON shelf.area_id=area.id 
                                    LEFT JOIN site ON area.site_id=site.id
                                    WHERE stock.id=? AND stock.deleted=0 AND cable_item.deleted=0
                                    GROUP BY 
                                        stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                        site_id, site_name, site_description, 
                                        area_id, area_name, 
                                        shelf_id, shelf_name,
                                        item_quantity
                                    ORDER BY site.name DESC, area.name ASC, shelf.name ASC;";
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
                            $disabled = ' disabled';
                        } else {
                            $disabled = '';
                        }
                        $stock_inv_data = [];
                        $stock_inv_manu = [];
                        $stock_inv_location = [];
                        while ( $row = $result_stock->fetch_assoc() ) {
                            $stock_id = $row['stock_id'];
                            $stock_name = $row['stock_name'];
                            $stock_sku = $row['stock_sku'];
                            $stock_item_id = isset($row['i_id']) ? $row['i_id'] : '';
                            $stock_quantity_total = $row['item_quantity'];
                            $stock_shelf_id = (isset($row['item_is_container']) && $row['item_is_container'] == 1) ? $row['shelf_id']*-1 : $row['shelf_id'];
                            $stock_shelf_name = (isset($row['item_is_container']) && $row['item_is_container'] == 1) ? $row['shelf_name'].' (container, ID: '.$stock_item_id.')' : $row['shelf_name'];
                            $stock_area_id = $row['area_id'];
                            $stock_area_name = $row['area_name'];
                            $stock_site_id = $row['site_id'];
                            $stock_site_name = $row['site_name'];
                            if ($data_is_cable == 0) {
                                
                                $stock_manufacturer_id = $row['manufacturer_id'];
                                $stock_manufacturer_name = $row['manufacturer_name'];
                                $stock_is_container = $row['item_is_container'];
                            }
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
                            
                            if ($data_is_cable == 0) {
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
                                                        'is_container' => $stock_is_container,
                                                        'item_id' => $stock_item_id,
                                                        'tag' => $stock_tag_data);
                        
                                $stock_inv_manu[$stock_manufacturer_id] = array('id' => $stock_manufacturer_id, 'name' => $stock_manufacturer_name);
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
                                                        'tag' => $stock_tag_data);

                                $stock_inv_location[$stock_shelf_id] = array('shelf_id' => $stock_shelf_id, 'shelf_name' => $stock_shelf_name,
                                                                                'area_id' => $stock_area_id, 'area_name' => $stock_area_name,
                                                                                'site_id' => $stock_site_id, 'site_name' => $stock_site_name);
                            }
                        }
                        
                        $stock_id = $_GET['stock_id'];
                        echo('
                        <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
                            <!-- Include CSRF token in the form -->
                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                           ');
                            if ($data_is_cable == 0) {
                                echo('<input type="hidden" name="stock-remove" value="1" /> ');
                            } else {
                                echo('<input type="hidden" name="cablestock-remove" value="1" />  ');
                            }
                            echo('
                            <div class="nav-row" style="margin-bottom:10px">
                                <div class="nav-row" id="heading-row" style="margin-top:10px">
                                    <div class="stock-inputLabelSize"></div>
                                    <div id="heading-heading">
                                        <a href="../stock.php?stock_id='.$stock_id.'"><h2 id="stock_name">'.$data_name.'</h2></a>
                                        <p id="sku"><strong>SKU:</strong> <or class="blue">'.$data_sku.'</or></p>
                                        <p id="locations" style="margin-bottom:0px"><strong>Locations:</strong><br>');
                                        if (empty($stock_inv_data)) {
                                            echo("No locations linked.");
                                        } else {
                                            echo('<table><tbody>');
                                            for ($l=0; $l < count($stock_inv_data); $l++) {
                                                // if ($l == 0 || $l < count($stock_inv_data)-1) { $divider = '<br>'; } else { $divider = ''; }
                                                echo('<tr><td>'.$stock_inv_data[$l]['site_name'].', '.$stock_inv_data[$l]['area_name'].', '.$stock_inv_data[$l]['shelf_name'].'</td><td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">'.$stock_inv_data[$l]['quantity'].'</or></a></or></td></tr>');
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
                                            ');
                                            if ($data_is_cable == 0) { 
                                                echo('
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
                                                        <select class="form-control stock-inputSize" id="shelf" name="shelf" required onchange="populateContainers(this)" disabled>
                                                            <option value="" selected disabled hidden>Select Location</option>');

                                                        echo('
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="nav-row" id="container-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="container" id="container-label">Container</label></div>
                                                    <div>
                                                        <select class="form-control stock-inputSize" id="container" name="container" required onchange="populateSerials(this)" disabled>
                                                            <option value="" selected disabled hidden>Select Location</option>');

                                                        echo('
                                                        </select>
                                                    </div>
                                                </div>
                                                ');
                                            } else {
                                                echo('
                                                <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Location</label></div>
                                                    <div>
                                                        <select class="form-control stock-inputSize" id="shelf" name="shelf" required onchange="getQuantityCable()" required'.$disabled.'>');
                                                            echo('<option value="" selected disabled hidden>Select Location</option>');
                                                            foreach ( $stock_inv_location as $location) {
                                                                echo('<option value='.$location['shelf_id'].'>'.$location['site_name'].', '.$location['area_name'].', '.$location['shelf_name'].'</option>');
                                                            }
                                                        echo('
                                                        </select>
                                                    </div>
                                                </div>
                                                ');
                                            }
                                            echo('
                                            <div class="nav-row" id="price-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="price" id="price-label">Sale Price (£)</label></div>
                                                <div><input type="number" name="price" placeholder="0" id="price" class="form-control nav-v-c stock-inputSize" value="0" value="'.$input_cost.'" required'.$disabled.'></input></div>
                                            </div>
                                        </div>
                                        <hr style="border-color: gray; margin-right:15px">
                                        <div class="nav-row" style="margin-bottom:0px">
                                            <div class="nav-row" id="date-row" style="margin-top:10px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="transaction_date" id="date-label">Transaction Date</label></div>
                                                <div><input type="date" value="'.date('Y-m-d').'" name="transaction_date" id="transaction_date" class="form-control" style="width:150px" required'.$disabled.'/></div>
                                            </div>');
                                            if ($data_is_cable == 0) { 
                                                echo('
                                                <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Number to be tracked.">Serial Numbers</or></label></div>
                                                    <div>
                                                        <select name="serial-number" id="serial-number" class="form-control stock-inputSize" value="'.$input_serial_number.'" '.$disabled.' onchange="getQuantity()">
                                                            <option value="" selected disabled hidden>Serial...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                ');
                                            }
                                            echo('
                                            <div class="nav-row" id="quantity-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                                <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="'.$input_quantity.'" min="1" required'.$disabled.'></input></div>
                                            </div>
                                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                                <div><input type="text" name="reason" placeholder="Customer sale, ID: XXXXXX" id="reason" class="form-control nav-v-c stock-inputSize" value="'.htmlspecialchars($input_reason, ENT_QUOTES, 'UTF-8').'" required'.$disabled.'></input></div>
                                            </div>
                                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                <div class="stock-inputLabelSize"></div>
                                                <div>');
                                                    
                                                    $stock_quantity_total = 0;
                                                    foreach ($stock_inv_data as $d) {
                                                        $stock_quantity_total += $d['quantity'];
                                                    }
                                                    if ($stock_quantity_total !== 0){
                                                        echo('<input type="submit" id="removeButton" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" />');
                                                        echo('<button type="button" id="removeContButton" name="submit" value="Remove Stock" class="nav-v-c btn btn-danger"onclick="modalLoadContainerRemoveConfirmation()"  hidden disabled>Remove Stock</button>');
                                                    } else {
                                                        echo('<input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" disabled />');
                                                        echo('<a href="#" onclick="confirmAction(\''.addslashes(htmlspecialchars($data_name)).'\', \''.$data_sku.'\', \'includes/stock-modify.inc.php?stock_id='.$stock_id.'&type=delete\')" class="nav-v-c btn btn-danger cw" style="margin-left:300px"><strong><u>Delete Stock</u></strong></a>');
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
            <form action="?modify=remove" method="GET" style="margin-bottom:0px">
                <div class="container" id="stock-info-left">
                    <div class="nav-row" id="search-stock-row">
                        <input type="hidden" name="modify" id="modify" value="remove" />
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
            <input type="hidden" id="inv-action-type" name="inv-action-type" value="remove" />
            <table class="table table-dark theme-table" id="inventoryTable" style="padding-bottom:0px;margin-bottom:0px">
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

<!-- Container Remove item Modal -->
<div id="modalDivContainerRemoveConfirmation" class="modal">
    <span class="close" onclick="modalCloseContainerRemoveConfirmation()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <form class="padding:0px;margin:0px" id="containerRemoveForm" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="submit" value="1" />
            <input type="hidden" name="container-remove" value="1" />
            <input type="hidden" id="containerRemoveItemID" name="item_id" />
            <input type="hidden" id="containerRemoveShelf" name="shelf_id" />
            <input type="hidden" id="containerRemoveQuantity" name="quantity" />
        </form>
        <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
            <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Remove Container Stock</h4>
            <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="removeContainerItemID"></td><th style="padding-right:5px">Name:</th><td id="removeContainerStockName"></td></tr></tbody></table>
            <table class="centertable" style="margin-top:10px">
                <tbody style="border: none">
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will KEEP all child objects attached and remove them with the current item. All child objects will be removed to the new location.">Remove container and contents</or></td>
                        <td style="border: none"><input type="submit" form="containerRemoveForm" class="btn btn-danger" name="container-remove-all" value="Remove All" /></td>
                    </tr>
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will UNLINK all child objects and ONLY remove the selected item. All child objects will remain in the same location.">Remove container only</or></td>
                        <td style="border: none"><input type="submit" form="containerRemoveForm" class="btn btn-warning" name="container-remove-single" value="Remove Single Item" style="margin-top:5px;margin-bottom:5px"/></td>
                    </tr>
                </tbody>
            </table>
            <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                <p><strong>Contents</strong> - Items: <strong id="removeContainerChildCount" class="green"></strong></p>
                <table id="containerRemoveContentsTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                    <thead>
                        <tr>
                            <th><!-- Image --></th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Name</th>
                            <th class="text-center align-middle">Description</th>
                            <th><!-- Unlink --></th>
                        </tr>
                    </thead>
                    <tbody id="containerRemoveContentsTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
            <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseContainerRemoveConfirmation()">Cancel</button>
        </span>
    </div>
</div>
<!-- End of Remove Container Modal-->

<script>
    async function modalLoadContainerRemoveConfirmation() {
        var shelfSelect = document.getElementById('shelf');
        var shelfSelectedNum = shelfSelect.options.selectedIndex;
        var itemID = shelfSelect.options[shelfSelectedNum].title;
        console.log(itemID);
    
        var modal = document.getElementById("modalDivContainerRemoveConfirmation");
        modal.style.display = "block";
        // Do some AJAX here to get the contents of the container and add it to the table
        // containerRemoveContentsTableBody

        var countText = document.getElementById('removeContainerChildCount');
        var tableBody = document.getElementById('containerRemoveContentsTableBody');
        var containerRemoveItemID = document.getElementById('containerRemoveItemID');
        var containerRemoveShelf = document.getElementById('containerRemoveShelf');
        var containerRemoveQuantity = document.getElementById('containerRemoveQuantity');

        var shelfSelect = document.getElementById('shelf');
        var quantityInput = document.getElementById('quantity');

        var removeContainerStockName = document.getElementById('removeContainerStockName');
        var removeContainerItemID = document.getElementById('removeContainerItemID');

        containerRemoveItemID.value=itemID;
        containerRemoveShelf.value=shelfSelect.value;
        containerRemoveQuantity.value=quantityInput.value;

        removeContainerItemID.innerHTML = itemID;
        removeContainerStockName.innerHTML = document.getElementById('stock_name').innerHTML;

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stockajax.php?request-container-children=1&container_id="+itemID+"&container_is_item=1", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var data = JSON.parse(xhr.responseText);
                var info = data['info'];
                // console.log(data);
                var bodyExtras = '';
                var count = data['count'];
                if (info == "No children found.") {
                    count = 0;
                }
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
                } else {
                    trs = "<tr><td colspan=100%>No chilren found.</td></tr>";
                }
                // console.log(trs);
                tableBody.innerHTML=trs;
                // console.log(trs);
            }
        };
        xhr.send();
    }

    function modalCloseContainerRemoveConfirmation() { 
        var modal = document.getElementById("modalDivContainerRemoveConfirmation");
        modal.style.display = "none";
        // Empty the table here too.
        // containerRemoveContentsTableBody
    }
</script>
<script>
    function confirmAction(stock_name, stock_sku, url) {
        var confirmed = confirm('Are you sure you want to proceed? \nThis will remove ALL entries for '+stock_name+' ('+stock_sku+').');
        if (confirmed) {
            window.location.href = url;
        }
    }

    // populate shelves from manuyfacturer
    async function populateRemoveShelves(elem) {
        var stock = document.getElementById('stock-id').value;
        var contButton = document.getElementById('removeContButton');
        var normalButton = document.getElementById('removeButton');

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
                        var option = new Option(shelves[i].location, shelves[i].id, true, true);
                        if (shelves[i].item_id !== '') {
                            option.setAttribute('title', shelves[i].item_id);
                        }
                        select.options[select.options.length] = option;
                    } else {
                        var option = new Option(shelves[i].location, shelves[i].id);
                        if (shelves[i].item_id !== '') {
                            option.setAttribute('title', shelves[i].item_id);
                        }
                        select.options[select.options.length] = option;
                    }
                    // select.options[select.options.length] = new Option(shelves[i].location, shelves[i].id);
                }
                select.disabled = (select.options.length === 0);
                var shelf = document.getElementById('shelf');
                // select.disabled = (select.options.length === 1);
                if (select.value < 0) {
                    contButton.disabled=false;
                    contButton.hidden=false;
                    normalButton.hidden=true;
                    normalButton.disabled=true;
                } else {
                    contButton.disabled=true;
                    contButton.hidden=true;
                    normalButton.hidden=false;
                    normalButton.disabled=false;
                }
                populateContainers(shelf);
            }
        };
        xhr.send();
    }

    // populate containers
    async function populateContainers(elem) {
        var contButton = document.getElementById('removeContButton');
        var normalButton = document.getElementById('removeButton');
        var select = document.getElementById('shelf');
        if (select.value < 0) {
            contButton.disabled=false;
            contButton.hidden=false;
            normalButton.hidden=true;
            normalButton.disabled=true;
        } else {
            contButton.disabled=true;
            contButton.hidden=true;
            normalButton.hidden=false;
            normalButton.disabled=false;
        }
        var stock = document.getElementById('stock-id').value;
        var shelf_id = elem.value;
        var manu_id = document.getElementById('manufacturer').value;
        var xhr = new XMLHttpRequest();
        // console.log("includes/stock-selectboxes.inc.php?getcontainers=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id);
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getcontainers=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var containers = JSON.parse(xhr.responseText);
                var select = document.getElementById('container');
                select.options.length = 0;
                if (containers.length === 0) {
                    select.options[0] = new Option("", "0");
                }
                for (var i = 0; i < containers.length; i++) {
                    if (i == 0) {
                        select.options[select.options.length] = new Option(containers[i].container_name, containers[i].container_id, true, true);
                    } else {
                        select.options[select.options.length] = new Option(containers[i].container_name, containers[i].container_id);
                    }
                }
                select.disabled = (select.options.length === 0);
                // select.disabled = (select.options.length === 1);
                var container = document.getElementById('container');
                populateSerials(container);
            }
        };
        xhr.send();
       
    }

    // populate serials
    async function populateSerials(elem) {
        // console.log(elem);
        if (elem.value == null || elem.value == '' || elem.value == undefined) {
            elem.value = 0;
        }        
        var stock = document.getElementById('stock-id').value;
        var container = elem.value;
        var shelf_id = document.getElementById('shelf').value;
        var manu_id = document.getElementById('manufacturer').value;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getserials=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id+"&container="+container, true);
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
        var container = document.getElementById('container').value;
        if (container == '') {
            container = 0;
            
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getquantity=1&stock="+stock+"&shelf="+shelf+"&manufacturer="+manufacturer+"&serial="+serial+"&container="+container, true);
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
    function getQuantityCable() {
        var stock = document.getElementById('stock-id').value;
        var shelf = document.getElementById('shelf').value;
        
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?getquantitycable=1&stock="+stock+"&shelf="+shelf, true);
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
            // console.log(key);
        }
        setTimeout(function () {
            if (queryParams.get('manufacturer')) {
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
                    if (queryParams.get('shelf') !== null) {
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
                            if (queryParams.get('serial') !== null) {
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