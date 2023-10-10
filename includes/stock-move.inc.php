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
$currency_symbol = '£';

// include 'head.php';
?>
<!-- <div style="margin-bottom:200px"></div> -->

<div class="container well-nopad theme-divBg">
    
    <?php
    if (is_numeric($stock_id)) {
        $input_name          = isset($_GET['name'])          ? $_GET['name'] : '';
        $input_sku           = isset($_GET['sku'])           ? $_GET['sku'] : '';
        $input_description   = isset($_GET['description'])   ? $_GET['description'] : '';
        $input_min_stock     = isset($_GET['min_stock'])     ? $_GET['min_stock'] : '';
        $input_labels        = isset($_GET['labels'])        ? $_GET['labels'] : '';
         
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
                    while ($row = $result->fetch_assoc()) {
                        $data_id = $row['id'];
                        $data_name = $row['name'];
                        $data_description = $row['description'];
                        $data_sku = $row['sku'];
                        $data_min_stock = $row['min_stock'];
                    }

                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                        area.id AS area_id, area.name AS area_name,
                                        shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                        item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, 
                                        (SELECT SUM(quantity) 
                                            FROM item 
                                            WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id AND item.manufacturer_id=manufacturer.id 
                                                AND item.serial_number=item_serial_number AND item.upc=item_upc AND item.comments=item_comments AND item.deleted=0
                                        ) AS item_quantity,
                                        manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                                        (SELECT GROUP_CONCAT(DISTINCT label.name ORDER BY label.name SEPARATOR ', ') 
                                            FROM stock_label 
                                            INNER JOIN label ON stock_label.label_id = label.id 
                                            WHERE stock_label.stock_id = stock.id
                                            ORDER BY label.name
                                        ) AS label_names,
                                        (SELECT GROUP_CONCAT(DISTINCT label.id ORDER BY label.name SEPARATOR ', ') 
                                            FROM stock_label
                                            INNER JOIN label ON stock_label.label_id = label.id
                                            WHERE stock_label.stock_id = stock.id
                                            ORDER BY label.name
                                        ) AS label_ids
                                    FROM stock
                                    LEFT JOIN item ON stock.id=item.stock_id
                                    LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                    LEFT JOIN area ON shelf.area_id=area.id 
                                    LEFT JOIN site ON area.site_id=site.id
                                    LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                    WHERE stock.id=? AND quantity!=0 AND stock.deleted=0 AND item.deleted=0
                                    GROUP BY 
                                        stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                        site_id, site_name, site_description, 
                                        area_id, area_name, 
                                        shelf_id, shelf_name,
                                        manufacturer_name, manufacturer_id,
                                        item_serial_number, item_upc, item_comments, item_cost
                                    ORDER BY site.id, area.name, shelf.name;";
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
                                $stock_manufacturer_id = $row['manufacturer_id'];
                                $stock_manufacturer_name = $row['manufacturer_name'];
                                $item_upc = $row['item_upc'];
                                $item_cost = $row['item_cost'];
                                $item_comments = $row['item_comments'];
                                $item_serial_number = $row['item_serial_number'];
                                $stock_label_ids = $row['label_ids'];
                                $stock_label_names = $row['label_names'];
                                
                                $stock_label_data = [];

                                if ($stock_label_ids !== null) {
                                    for ($n=0; $n < count(explode(", ", $stock_label_ids)); $n++) {
                                        $stock_label_data[$n] = array('id' => explode(", ", $stock_label_ids)[$n],
                                                                            'name' => explode(", ", $stock_label_names)[$n]);
                                    }
                                } else {
                                    $stock_label_data = '';
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
                                                        'upc' => $item_upc,
                                                        'cost' => $item_cost,
                                                        'comments' => $item_comments,
                                                        'serial_number' => $item_serial_number,
                                                        'label' => $stock_label_data);
                            }
                            
                            $stock_id = $_GET['stock_id'];
                            echo('<div class="nav-row" style="margin-top: 2px; margin-bottom:5px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div id="heading-heading" style="margin-left:15vw;">
                                            <a href="../stock.php?stock_id='.$stock_id.'"><h2>'.$data_name.'</h2></a>
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
                                                <th class="viewport-mid-large">Manufacturer</th>
                                                <th class="viewport-small-only-empty">Manu.</th>
                                                <th class="viewport-mid-large">UPC</th>
                                                <th title="Serial Numbers">Serial</th>
                                                <th>Cost</th>
                                                <th class="viewport-mid-large">Comments</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>                           
                                    ');
                                    for ($i=0; $i<count($stock_inv_data); $i++) {
                                        echo('
                                            <tr id="item-'.$i.'" class="clickable'); if (isset($_GET['edited']) && $_GET['edited'] == $i) { echo(' last-edit'); } echo('" onclick="toggleHidden(\''.$i.'\')">
                                                <td hidden>'.$i.'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'">'.$stock_inv_data[$i]['site_name'].'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'">'.$stock_inv_data[$i]['area_name'].'</td>
                                                <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'-'.$stock_inv_data[$i]['shelf_id'].'">'.$stock_inv_data[$i]['shelf_name'].'</td>
                                                <td id="item-'.$i.'-manu-'.$stock_inv_data[$i]['manufacturer_id'].'">'.$stock_inv_data[$i]['manufacturer_name'].'</td>
                                                <td id="item-'.$i.'-upc" class="viewport-mid-large">'.$stock_inv_data[$i]['upc'].'</td>
                                                <td id="item-'.$i.'-sn">'.$stock_inv_data[$i]['serial_number'].'</td>
                                                <td id="item-'.$i.'-cost">'.$currency_symbol.$stock_inv_data[$i]['cost'].'</td>
                                                <td id="item-'.$i.'-comments" class="viewport-mid-large">'.$stock_inv_data[$i]['comments'].'</td>
                                                <td id="item-'.$i.'-stock">'.$stock_inv_data[$i]['quantity'].'</td>
                                            </tr>
                                            <tr class="move-hide" id="item-'.$i.'-edit" hidden>
                                                <td colspan=100%>
                                                    <div class="container">                                                       
                                                        <form class="" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                                                            <!-- below input used for the stock-modify.inc.php page to determine the type of change -->
                                                            <input type="hidden" name="stock-move" value="1" />
                                                            <input type="hidden" id="'.$i.'-c-i" name="current_i" value="'.$i.'" />
                                                            <input type="hidden" id="'.$i.'-c-stock" name="current_stock" value="'.$stock_id.'" />
                                                            <input type="hidden" id="'.$i.'-c-site" name="current_site" value="'.$stock_inv_data[$i]['site_id'].'" />
                                                            <input type="hidden" id="'.$i.'-c-area" name="current_area" value="'.$stock_inv_data[$i]['area_id'].'" />
                                                            <input type="hidden" id="'.$i.'-c-shelf" name="current_shelf" value="'.$stock_inv_data[$i]['shelf_id'].'" />
                                                            <input type="hidden" id="'.$i.'-c-manufacturer" name="current_manufacturer" value="'.$stock_inv_data[$i]['manufacturer_id'].'" />
                                                            <input type="hidden" id="'.$i.'-c-upc" name="current_upc" value="'.$stock_inv_data[$i]['upc'].'" />
                                                            <input type="hidden" id="'.$i.'-c-serial" name="current_serial" value="'.$stock_inv_data[$i]['serial_number'].'" />
                                                            <input type="hidden" id="'.$i.'-c-cost" name="current_cost" value="'.$stock_inv_data[$i]['cost'].'" />
                                                            <input type="hidden" id="'.$i.'-c-comments" name="current_comments" value="'.$stock_inv_data[$i]['comments'].'" />
                                                            <input type="hidden" id="'.$i.'-c-quantity" name="current_quantity" value="'.$stock_inv_data[$i]['quantity'].'" />
                                                            <table style="border: 1px solid #454d55;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="row">
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c">To:</label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-site" name="site" style="min-width:50px; padding:2 0 2 0;  width:max-content !important" required onchange="populateAreas(\''.$i.'\')">
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
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-area" name="area" style="min-width:50px; padding: 2 0 2 0; max-width:max-content !important" disabled required onchange="populateShelves(\''.$i.'\')">
                                                                                        <option value="" selected disabled hidden>Area</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <select class="form-control nav-v-c row-dropdown" id="'.$i.'-n-shelf" name="shelf" style="min-width:50px; padding: 2 0 2 0; max-width:max-content !important" disabled required>
                                                                                        <option value="" selected disabled hidden>Shelf</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <label class="nav-v-c" for="'.$i.'-n-quantity">Quantity: </label>
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="number" class="form-control nav-v-c row-dropdown" id="'.$i.'-n-quantity" name="quantity" style="min-width: 20px; padding: 2 7 2 7; max-width:50px;" placeholder="1" value="1" min="1" max="'.$stock_inv_data[$i]['quantity'].'" required />
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="number" class="form-control nav-v-c row-dropdown" id="'.$i.'-n-serial" name="serial" style="min-width: 80px; padding: 2 7 2 7; width:max-content; max-width:90px" placeholder="'); if (isset($stock_inv_data[$i]['serial_number']) && $stock_inv_data[$i]['serial_number'] !== '') { echo $stock_inv_data[$i]['serial_number']; } else { echo "No Serial Number"; } echo('" value="'.$stock_inv_data[$i]['serial_number'].'" disabled /> 
                                                                                </div>
                                                                                <div class="col" style="max-width:max-content !important">
                                                                                    <input type="submit" class="btn btn-warning nav-v-c btn-move" id="'.$i.'-n-submit" value="Move" style="opacity:80%; name="submit" required />
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </td>
                                                                </tbody>
                                                            </table>
                                                        </form>
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
            // Get total row count
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
                    WHERE stock.is_cable=0
                        AND (SELECT SUM(quantity) 
                                FROM item 
                                INNER JOIN shelf ON item.shelf_id=shelf.id
                                INNER JOIN area ON shelf.area_id=area.id
                                WHERE item.stock_id=stock.id AND area.site_id=site.id
                            )!='null'
                        AND stock.deleted=0 AND item.deleted=0
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
                        WHERE stock.is_cable=0
                            AND (SELECT SUM(quantity) 
                                    FROM item 
                                    INNER JOIN shelf ON item.shelf_id=shelf.id
                                    INNER JOIN area ON shelf.area_id=area.id
                                    WHERE item.stock_id=stock.id AND area.site_id=site.id
                                )!='null'
                            AND stock.deleted=0 AND item.deleted=0
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
    var hiddenID = 'item-'+id+'-edit';
    var hiddenRow = document.getElementById(hiddenID);
    var allHiddenRows = document.getElementsByClassName('move-hide');
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
    } else {
        for(var i = 0; i < allHiddenRows.length; i++) {
        allHiddenRows[i].hidden=true;
        }   
        hiddenRow.hidden=false;
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