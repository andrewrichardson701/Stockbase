<?php 
// SHOWS THE INFORMATION FOR EACH PEICE OF STOCK AND ITS LOCATIONS ETC. 
// id QUERY STRING IS NEEDED FOR THIS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="content">
        <?php // dependency PHP
        $show_inventory = 0; // for nav.php to show the site and area on the banner
        if (isset($_GET['stock_id'])) {
            if (is_numeric($_GET['stock_id'])) {
                $stock_id = $_GET['stock_id'];
            } else {
                if (isset($_GET['modify'])) {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">add new stock item</a>.</p></div>');
                    exit();
                } else {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>');
                    exit();
                }
                
            }
        } elseif (isset($_GET['modify'])) {
            if ($_GET['modify'] == "edit") {
                echo("No Stock ID Selected.");
            }
        } else {
            echo("No Stock ID or Modification Selected.");
        }
        if (isset($_GET['modify'])) {
            $stock_modify = $_GET['modify'];
        }
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = './index.php';
        }
        ?>

        <!-- Get Inventory -->
        <?php
        $stock_modify_values = ['add', 'remove', 'edit', 'move'];
        if (isset($stock_modify) && in_array(strtolower($stock_modify), $stock_modify_values)) {
            echo('<div class="container" style="padding-bottom:25px">
            <h2 class="header-small" style="padding-bottom:10px">Stock - '.ucwords($stock_modify).'</h2>');
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($_GET['error'] == 'SKUexists') { $error = 'SKU "<or class="blue">'.$_GET['sku'].'</or>" already exists. Please pick another, or leave empty to generate a new one'; }
                if ($_GET['error'] == 'multipleItemsFound') { $error = 'Multiple item rows found in the items table. Something needs corecting in the database. <br>To continue, change one of:<br>&nbsp;<or class="blue">UPC</or>,<br>&nbsp;<or class="blue">Serial Numbers</or>,<br>&nbsp;<or class="blue">Item Cost</or>,<br>&nbsp;<or class="blue">Shelf/Location</or>'; }
                echo('<p class="red" style="margin-bottom:0">ERROR: '.$error.'.</p>');
            }
            echo('</div>');
            include 'includes/stock-'.$stock_modify.'.inc.php';

        } else {
            include 'includes/dbh.inc.php';
            $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock,
                                stock_img.id AS stock_img_id, stock_img.stock_id AS stock_img_stock_id, stock_img.image AS stock_img_image
                        FROM stock
                        LEFT JOIN stock_img ON stock.id=stock_img.stock_id
                        WHERE stock.id=?;";
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
                    $stock_img_data = [];
                    while ( $row = $result_stock->fetch_assoc() ) {
                        $stock_id                  = $row['stock_id']          ; 
                        $stock_name                = $row['stock_name']        ;
                        $stock_description         = $row['stock_description'] ;
                        $stock_sku                 = $row['stock_sku']         ;
                        $stock_min_stock           = $row['stock_min_stock']   ;
                        $stock_stock_img_id        = $row['stock_img_id']      ;
                        $stock_stock_img_stock_id  = $row['stock_img_stock_id'];
                        $stock_stock_img_image     = $row['stock_img_image']   ;

                        if ($stock_stock_img_id !== null) {
                        $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                        }
                    }
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // print_r('<pre class="bg-dark">');
                    // print_r($stock_img_data);
                    // print_r('</pre>');

                    echo('
                        <div class="container" style="padding-bottom:25px">
                            <h2 class="header-small" style="padding-bottom:10px">Stock</h2>
                            <div class="nav-row" style="margin-top:10px">
                                <h3 style="font-size:22px;margin-top:20px;margin-bottom:0;width:max-content" id="stock-name">'.$stock_name.' ('.$stock_sku.')</h3>
                                <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                    <button id="edit-stock" class="btn btn-info cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'edit\'))">
                                        <i class="fa fa-pencil"></i> Edit 
                                    </button>
                                </div> 
                                <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="add-stock" class="btn btn-success cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'add\'))">
                                        <i class="fa fa-plus"></i> Add 
                                    </button>
                                </div> 
                                <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="remove-stock" class="btn btn-danger cw nav-v-b" style="width:110px" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'remove\'))">
                                        <i class="fa fa-minus"></i> Remove 
                                    </button>
                                </div> 
                                <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                    <button id="transfer-stock" class="btn btn-warning nav-v-b" style="width:110px;color:black" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'move\'))">
                                        <i class="fa fa-arrows-h"></i> Move 
                                    </button>
                                </div>
                            </div>
                            <p id=stock-description style="margin-bottom:0px">'.$stock_description.'</p>
                        </div>

                        <!-- Modal Image Div -->
                        <div id="modalDiv" class="modal" onclick="modalClose()">
                            <span class="close" onclick="modalClose()">&times;</span>
                            <img class="modal-content bg-trans" id="modalImg">
                            <div id="caption" class="modal-caption"></div>
                        </div>
                        <!-- End of Modal Image Div -->

                    ');
                }
            }


            $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                            area.id AS area_id, area.name AS area_name,
                            shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                            (SELECT SUM(quantity) 
                                FROM item 
                                WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id
                            ) AS item_quantity,
                            (SELECT GROUP_CONCAT(DISTINCT manufacturer.name ORDER BY manufacturer.name SEPARATOR ', ') 
                                FROM item 
                                INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                WHERE item.stock_id = stock.id
                            ) AS manufacturer_names,
                            (SELECT GROUP_CONCAT(DISTINCT manufacturer.id ORDER BY manufacturer.name SEPARATOR ', ') 
                                FROM item 
                                INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                WHERE item.stock_id = stock.id
                            ) AS manufacturer_ids,
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
                        WHERE stock.id=?
                        GROUP BY 
                            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                            site_id, site_name, site_description, 
                            area_id, area_name, 
                            shelf_id, shelf_name
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
                        $stock_min_stock = $row['stock_min_stock'];
                        $stock_quantity_total = $row['item_quantity'];
                        $stock_shelf_id = $row['shelf_id'];
                        $stock_shelf_name = $row['shelf_name'];
                        $stock_area_id = $row['area_id'];
                        $stock_area_name = $row['area_name'];
                        $stock_site_id = $row['site_id'];
                        $stock_site_name = $row['site_name'];
                        $stock_manufacturer_ids = $row['manufacturer_ids'];
                        $stock_manufacturer_names = $row['manufacturer_names'];
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

                        $stock_manufacturer_data = [];
                        if ($stock_manufacturer_ids !== null) {
                            for ($n=0; $n < count(explode(", ", $stock_manufacturer_ids)); $n++) {
                                $stock_manufacturer_data[$n] = array('id' => explode(", ", $stock_manufacturer_ids)[$n],
                                                                    'name' => explode(", ", $stock_manufacturer_names)[$n]);
                            }
                        } else {
                            $stock_manufacturer_data = '';
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
                                                'manufacturer' => $stock_manufacturer_data,
                                                'label' => $stock_label_data);
                    }
                    
                    // Inventory Rows
                    echo ('
                    <div class="container well-nopad bg-dark">
                        <div class="row">
                            <div class="col-sm-7 text-left" id="stock-info-left">
                                ');
                                $totalStock = 0;
                                for ($st=0; $st<count($stock_inv_data); $st++) {
                                    $totalStock = $totalStock + (int)$stock_inv_data[$st]['quantity'];
                                }
                                echo('<table class="" id="stock-info-table" style="max-width:max-content">
                                        <thead>
                                            <tr>
                                                <th hidden>id</th>
                                                <th hidden>Site</th>
                                                <th>Location</th>
                                                <th style="padding-left: 5px">Shelf</th>
                                                <th style="padding-left: 5px">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>');
                                    for ($st=0; $st<count($stock_inv_data); $st++) {
                                        if ($stock_inv_data[$st]['quantity'] !== 0 && $stock_inv_data[$st]['quantity'] !== '0' && $stock_inv_data[$st]['quantity'] !== null && $stock_inv_data[$st]['quantity'] !== 'null') {
                                            echo('
                                            <tr id="stock-row-'.$stock_inv_data[$st]['id'].'">
                                                <td hidden>'.$stock_inv_data[$st]['id'].'</td>
                                                <td hidden id="site-'.$stock_inv_data[$st]['site_id'].'"><or onclick="window.location.href=\'./?site='.$stock_inv_data[$st]['site_id'].'\'">'.$stock_inv_data[$st]['site_name'].'</or></td>
                                                <td id="area-'.$stock_inv_data[$st]['area_id'].'"><or onclick="window.location.href=\'./?site='.$stock_inv_data[$st]['site_id'].'&area='.$stock_inv_data[$st]['area_id'].'\'">'.$stock_inv_data[$st]['area_name'].':</or></td>
                                                <td id="shelf-'.$stock_inv_data[$st]['shelf_id'].'" style="padding-left: 5px"><button class="btn btn-dark btn-stock-click gold" onclick="window.location.href=\'./?shelf='.str_replace(' ', '+', $stock_inv_data[$st]['shelf_name']).'\'">'.$stock_inv_data[$st]['shelf_name'].'</button></td>
                                                <td style="padding-left: 5px"><a class="btn btn-dark btn-stock">'.$stock_inv_data[$st]['quantity'].'</a></td>
                                            </tr>
                                            ');
                                        }
                                    }
                                    echo('</tbody>
                                    </table>
                                ');

                                echo('</p>');

                                // echo('
                                // <p id="sku-head"><strong>SKU</strong></p>
                                // <p id="sku">'.$stock_sku.'</p>');
                                echo('
                                    <p id="min-stock"><strong>Minimum Stock Count:</strong> <or class="gold">'.$stock_min_stock.'</or></p>
                                ');

                                echo('
                                <p id="labels-head"><strong>Labels</strong></p>
                                <p id="labels">');
                                if (is_array($stock_inv_data[0]['label'])) {
                                    for ($l=0; $l < count($stock_inv_data[0]['label']); $l++) {
                                        echo('<button class="btn btn-dark btn-stock-click gold" id="label-'.$stock_inv_data[0]['label'][$l]['id'].'" onclick="window.location.href=\'./?label='.$stock_inv_data[0]['label'][$l]['name'].'\'">'.$stock_inv_data[0]['label'][$l]['name'].'</button> ');
                                    }
                                } else {
                                    echo('None');
                                }
                                
                                echo('
                                <p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers">');
                                if ( is_array($stock_inv_data[0]['manufacturer'])) {
                                    for ($m=0; $m < count($stock_inv_data[0]['manufacturer']); $m++) {
                                        echo('<button class="btn btn-dark btn-stock-click gold" id="manufacturer-'.$stock_inv_data[0]['manufacturer'][$m]['id'].'" onclick="window.location.href=\'./?manufacturer='.$stock_inv_data[0]['manufacturer'][$m]['name'].'\'">'.$stock_inv_data[0]['manufacturer'][$m]['name'].'</button> ');
                                    }
                                } else {
                                    echo('None');
                                }
                                echo('</p>');
                                
                                $sql_serials = "SELECT DISTINCT serial_number, id FROM item WHERE stock_id=? AND serial_number != '' AND quantity != 0 ORDER BY id";
                                $stmt_serials = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_serials, $sql_serials)) {
                                    // fails to connect (do nothing this time)
                                } else {
                                    mysqli_stmt_bind_param($stmt_serials, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_serials);
                                    $result_serials = mysqli_stmt_get_result($stmt_serials);
                                    $rowCount_serials = $result_serials->num_rows;
                                    if ($rowCount_serials > 0) {
                                        // rows found
                                        echo('<p id="serial-numbers-head"><strong>Serial Numbers</strong></p><p>');
                                        $sn = 0;
                                        while ($row_serials = $result_serials->fetch_assoc()) {
                                            $sn++;
                                            echo('<a class="btn btn-dark btn-stock" style="color:#cfcfcf !important" id="serialNumber'.$sn.'">'.$row_serials['serial_number'].'</a>');
                                        }
                                        echo('</p>');
                                    }
                                }  



                            echo('   
                            </div>
                            
                            <div class="col-sm text-right" style="margin-left:70px" id="stock-info-right">');  
                            if (!empty($stock_img_data)) {
                                echo('<div class="well-nopad bg-dark nav-right" style="margin:20px;padding:0px;width:max-content">
                                <div class="nav-row" style="width:315px">');
                                for ($i=0; $i < count($stock_img_data); $i++) {
                                    $ii = $i+1;
                                    if ($i == 0) {
                                        if ($i+1 === count($stock_img_data)) {
                                            $imgWidth = "315px";
                                        } else {
                                            $imgWidth = "235px";
                                        }
                                        echo('
                                        <div class=" thumb bg-dark-m" style="width:'.$imgWidth.';height:235px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:'.$imgWidth.'" alt="'.$stock_name.' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" />
                                        </div>
                                        <span id="side-images" style="margin-left:5px">
                                        ');
                                    } 
                                    if ($i == 1 || $i == 2) {
                                        echo('
                                        <div class="thumb bg-dark-m" style="width:75px;height:75px;margin-bottom:5px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:75px" alt="'.$stock_name.' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                        </div>
                                        ');
                                    }
                                    if ($i == 3) {
                                        if ($i < (count($stock_img_data)-1)) {
                                            echo ('
                                            <div class="thumb bg-dark-m" style="width:75px;height:75px">
                                            <p class="nav-v-c text-center" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-more" style="width:75px">+'.(count($stock_img_data)-3).'</p>
                                            ');
                                        } else {
                                            echo('
                                            <div class="thumb bg-dark-m" style="width:75px;height:75px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:75px" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" onclick="modalLoad(this)"/>
                                            ');
                                        }
                                        echo('</div>');
                                    }
                                    if ($i == (count($stock_img_data)-1)) {
                                        echo('<span>');
                                    }
                                }
                                echo('</div>
                                </div>');
                                echo('<div id="edit-images-div" class="nav-div-mid">
                                    <button id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'modify\', \'edit\'), \'images\', \'edit\'))">
                                        <i class="fa fa-pencil"></i> Edit images
                                    </button>
                                </div> ');
                            } else {
                                echo('<div id="edit-images-div" class="nav-div-mid nav-v-c">
                                    <button id="edit-images" class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px">
                                        <i class="fa fa-plus"></i> Add images
                                    </button>
                                </div> ');
                            }
                            echo('
                            </div>
                        </div>
                    </div>
                    <div class="container well-nopad bg-dark" style="margin-top:5px">
                        <h2 style="font-size:22px">Transactions</h2>');
                        include 'includes/transaction.inc.php';
                    echo('</div>');
                }
            }
        }

        ?>
    </div>
    
    <?php include 'foot.php'; ?>

</body>