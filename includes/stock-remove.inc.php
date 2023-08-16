<?php
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
<div class="container well-nopad bg-dark">
    
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
                    WHERE id=?
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
                                    (SELECT GROUP_CONCAT(DISTINCT label.name SEPARATOR ', ') 
                                        FROM stock_label 
                                        INNER JOIN label ON stock_label.label_id = label.id 
                                        WHERE stock_label.stock_id = stock.id
                                    ) AS label_names,
                                    (SELECT GROUP_CONCAT(DISTINCT label_id SEPARATOR ', ') 
                                        FROM stock_label
                                        WHERE stock_label.stock_id = stock.id
                                    ) AS label_ids
                                FROM stock
                                LEFT JOIN item ON stock.id=item.stock_id
                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                WHERE stock.id=?
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
                                                        'label' => $stock_label_data);
                            }
                            
                            $stock_id = $_GET['stock_id'];
                            echo('<form action="includes/stock-remove-existing.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                                <div class="nav-row" style="margin-bottom:10px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div style="width:200px;margin-right:25px"></div>
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
                                                    echo('<tr><td>'.$stock_inv_data[$l]['area_name'].', '.$stock_inv_data[$l]['shelf_name'].'</td><td style="padding-left:5px"><a class="btn btn-dark btn-stock cw">Stock: <or class="gold">'.$stock_inv_data[$l]['quantity'].'</or></a></or></td></tr>');
                                                }
                                                echo('</tbody></table>');
                                            }
                                            echo('</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="container well-nopad bg-dark">
                                    <div class="row">
                                        <div class="text-left" id="stock-info-left" style="padding-left:15px">
                                            <div class="nav-row" style="margin-bottom:25px">
                                                <input type="hidden" value="'.$stock_id.'" name="stock_id" />
                                                <input type="hidden" value="'.$stock_sku.'" name="stock_sku" />
                                                <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                                    <div>
                                                        <select name="manufacturer" id="manufacturer" class="form-control" style="width:300px" required>
                                                            <option value="" selected disabled hidden>Select Manufacturer</option>');
                                                            $temp_manu_id = '';
                                                            foreach ($stock_inv_data as $temp_data) {
                                                                if ($temp_data['manufacturer_id'] !== $temp_manu_id) {
                                                                    echo('<option value='.$temp_data['manufacturer_id'].'>'.$temp_data['manufacturer_name'].'</option>');
                                                                }
                                                                $temp_manu_id = $temp_data['manufacturer_id'];
                                                            }
                                                        echo('
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Location</label></div>
                                                    <div>
                                                        <select class="form-control" id="shelf" name="shelf" style="width:300px" required>
                                                            <option value="" selected disabled hidden>Select Location</option>');
                                                            $temp_site_id = '';
                                                            foreach ($stock_inv_data as $temp_data) {
                                                                if ($temp_data['shelf_id'] !== $temp_site_id) {
                                                                    echo('<option value='.$temp_data['shelf_id'].'>'.$temp_data['site_name'].' - '.$temp_data['area_name'].' - '.$temp_data['shelf_name'].'</option>');
                                                                }
                                                                $temp_site_id = $temp_data['shelf_id'];
                                                            }
                                                        echo('
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="nav-row" id="price-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="price" id="price-label">Sale Price (£)</label></div>
                                                    <div><input type="number" name="price" placeholder="0" id="price" class="form-control nav-v-c" style="width:300px" value="0" value="'.$input_cost.'" required></input></div>
                                                </div>
                                            </div>
                                            <hr style="border-color: gray; margin-right:15px">
                                            <div class="nav-row" style="margin-bottom:0">
                                                <div class="nav-row" id="date-row" style="margin-top:10px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="transaction_date" id="date-label">Transaction Date</label></div>
                                                    <div><input type="date" value="'.date('Y-m-d').'" name="transaction_date" id="transaction_date" class="form-control" style="width:150px"/></div>
                                                </div>
                                                <div class="nav-row" id="quantity-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                                    <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c" style="width:300px" value="1" value="'.$input_quantity.'" required></input></div>
                                                </div>
                                                <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                                    <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c" style="width:300px" value="'.$input_serial_number.'"></input></div>
                                                </div>
                                                <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                                    <div><input type="text" name="reason" placeholder="Customer sale [CDC-ID: #XXXXXX]" id="reason" class="form-control nav-v-c" style="width:300px" value="'.$input_reason.'" required></input></div>
                                                </div>
                                                <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"></div>
                                                    <div>');
                                                        
                                                        $stock_quantity_total = 0;
                                                        foreach ($stock_inv_data as $d) {
                                                            $stock_quantity_total += $d['quantity'];
                                                        }
                                                        if ($stock_quantity_total !== 0){
                                                            echo('<input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" />');
                                                        } else {
                                                            echo('<input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" disabled />');
                                                            echo('<a href="#" onclick="confirmAction(\''.$stock_name.'\', \''.$stock_sku.'\', \'includes/stock-remove-existing.inc.php?stock_id='.$stock_id.'&type=delete\')" class="nav-v-c btn btn-danger cw" style="margin-left:300px"><strong><u>Delete Stock</u></strong></a>');
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
                            <input class="form-control" type="text" style="width: 250px" id="search" name="search" placeholder="Search for item" value="'.$search.'"/>
                        </span>
                    </div>
                </div>
            </form>
        ');
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            echo('
            <div class="container well-nopad bg-dark" style="margin-top:20px;padding-left:20px">
                ');
            include 'includes/dbh.inc.php';
            $sql = "SELECT * from stock
                    WHERE name LIKE CONCAT('%', ?, '%')
                    ORDER BY name;";
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
                <table class="table table-dark" style="min-width:500px;max-width:max-content">
                    <thead>
                        <tr>
                            <th style="max-width:max-content">ID</th>
                            <th>Stock Name</th>
                            <th>SKU</th>
                        </tr>
                    </thead>
                    <tbody>
                    ');
                    while ($row = $result->fetch_assoc() ) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $sku = $row['sku'];
                        echo('
                        <tr class="clickable" onclick="window.location.href=\'stock.php?modify='.$_GET['modify'].'&stock_id='.$id.'\'">
                            <td id="'.$id.'-id"  style="max-width:max-content">'.$id.'</td>
                            <td id="'.$id.'-name">'.$name.'</td>
                            <td id="'.$id.'-sku">'.$sku.'</td>
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
            // Pagination settings
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            if ($page < 1) {
                $page = 1;
            }
            $pageSize = 10; // Number of rows per page

            // Calculate the offset for the query
            $offset = ($page - 1) * $pageSize;

            echo('
            <div class="container well-nopad bg-dark" style="margin-top:20px;padding-left:20px">');
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
                    GROUP BY 
                        stock.id, stock_name, stock_description, stock_sku, 
                        stock_img_image.stock_img_image
                    ORDER BY stock.name
                    LIMIT $pageSize OFFSET $offset;";
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
                    <table class="table table-dark" id="inventoryTable" style="max-width:max-content">
                        <thead style="text-align: center; white-space: nowrap;">
                            <tr>
                                <th>ID</th>
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
                            <tr class="clickable" style="vertical-align align-middle" id="'.$row['stock_id'].'" onclick="window.location.href=\'stock.php?modify='.$_GET['modify'].'&stock_id='.$row['stock_id'].'\'">
                                <td class="align-middle" id="'.$row['stock_id'].'-id">'.$row['stock_id'].'</td>
                                <td class="align-middle" id="'.$row['stock_id'].'-img-cell">');
                                if ($row['stock_img_image'] !== null && $row['stock_img_image'] !== '') {
                                    echo ('<img id="'.$row['stock_id'].'-img" class="inv-img thumb" src="assets/img/stock/'.$row['stock_img_image'].'" alt="'.$row['stock_name'].'" title="'.$row['stock_name'].'" onclick="modalLoad(this)">');
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
    
    ?>
    
</div>
<script>
    function confirmAction(stock_name, stock_sku, url) {
        var confirmed = confirm('Are you sure you want to proceed? \nThis will remove ALL entries for '+stock_name+' ('+stock_sku+').');
        if (confirmed) {
            window.location.href = url;
        }
    }
</script>