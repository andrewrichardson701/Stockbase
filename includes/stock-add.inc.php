<?php
// INCLUDED IN THE STOCK PAGE FOR NEW STOCK OR INVENTORY TO CURRENT STOCK

// Query string bits
$stock_id = isset($_GET['stock_id']) ? $_GET['stock_id'] : '';

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

        echo('<form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">');
        if ($stock_id == 0 || $stock_id == '0') {
            //<input type="text" name="labels" placeholder="Labels - allow multiple" id="labels" class="form-control nav-v-c" style="width:300px" value="'.$input_labels.'"></input>
            echo('
            <!-- this is for the stock-modify.inc.php page -->
            <input type="hidden" name="stock-add" value="1" /> 
            <div class="container well-nopad theme-divBg" style="margin-bottom:5px">
                <h3 style="font-size:22px; margin-left:25px">Add New Stock</h3>
                <div class="row">
                    <div class="col-sm text-left" id="stock-info-left">
                        <div class="nav-row">
                            <div class="nav-row" id="name-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c" style="width:300px" value="'.$input_name.'" required></input></div>
                            </div>
                            <div class="nav-row" id="sku-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c" style="width:300px" value="'.$input_sku.'" pattern="^[A-Za-z\s\p{P}]+$"></input></div>
                            </div>
                            <div class="nav-row" id="description-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                <div><textarea class="form-control nav-v-c" id="description" name="description" rows="3" cols="32" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="'.$input_description.'" ></textarea></div>
                            </div>
                            <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c" style="width:300px" value="'.$input_min_stock.'"></input></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm"  id="stock-info-right"> 
                        <div id="image-preview" style="height:150px;margin:auto;text-align:center">
                            <img class="nav-v-c" id="upload-img-pre" style="max-width:150px;max-height:150px" />
                        </div>
                        <div class="nav-row"  id="labels-row" style="margin-top:25px">
                            <div class="nav-right" style="margin-right:25px"><label class="nav-v-c" style="width:100%" for="labels" id="labels-label">Image:</label></div>
                            <div><input class="nav-v-c text-center" type="file" accept="image/*" style="width: 350px" id="image" name="image" onchange="loadImage(event)"></div>
                        </div>
                        <script>
                        var loadImage = function(event) {
                            var preview = document.getElementById(\'upload-img-pre\');
                            preview.src = URL.createObjectURL(event.target.files[0]);
                            preview.onload = function() {
                            URL.revokeObjectURL(preview.src) // free memory
                            }
                        };
                        </script>
                    </div>
                    <div class="nav-row" id="labels-row" style="margin-top:25px;padding-left:15px;padding-right:15px">
                        <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="labels" id="labels-label">Labels</label></div>
                        <div>
                            <select class="form-control" id="labels-init" name="labels-init" style="width:300px"">
                                <option value="" selected disabled hidden>-- Select a label if needed --</option>');
                                include 'includes/dbh.inc.php';
                                $sql = "SELECT id, name
                                        FROM label
                                        ORDER BY id";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    // fails to connect
                                } else {
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $rowCount = $result->num_rows;
                                    if ($rowCount < 1) {
                                        echo('<option value="0">No Lables Found...</option>');
                                    } else {
                                        // rows found
                                        while ($row = $result->fetch_assoc()) {
                                            $label_id = $row['id'];
                                            $label_name = $row['name'];
                                            echo('<option value="'.$label_id.'">'.$label_name.'</option>');
                                        }
                                    }
                                }
                            echo('
                            </select>

                            <select id="labels" name="labels[]" multiple class="form-control" style="margin-top:2px;display: inline-block;width:300px;height:40px"></select>
                            <style>
                                #labels {
                                display: inline-block;
                                padding-top:2px;
                                padding-bottom:2px;
                                width: auto;
                                }
                                
                                #labels option {
                                display: inline-block;
                                padding: 3px;
                                margin-right: 10px;
                                background-color: #f1f1f1;
                                border: 1px solid #ccc;
                                border-radius: 5px;
                                }
                            </style>
                            <script>
                            var selectBox = document.getElementById("labels-init");
                            var selectedBox = document.getElementById("labels");

                            selectBox.addEventListener("change", function() {
                            var selectedOption = selectBox.options[selectBox.selectedIndex];
                            if (selectedOption.value !== "") {
                                selectedBox.add(selectedOption);
                            }
                            });

                            selectedBox.addEventListener("change", function() {
                            var removedOption = selectedBox.options[selectedBox.selectedIndex];
                            if (removedOption.value !== "") {
                                selectBox.add(removedOption);
                                selectedBox.remove(selectedBox.selectedIndex);
                            }
                            });
                            </script>
                        </div>
                        <div>
                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'label\')">Add New</label>
                        </div>
                    </div>
                </div>
            </div>
            ');
        } else {
            // Adding exisiting stock for item
            echo('
            <!-- this is for the stock-modify.inc.php page -->
            <input type="hidden" name="stock-add" value="1" /> 
            <input type="hidden" name="id" value="'.$_GET['stock_id'].'" />');
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
                        WHERE stock.id=? AND stock.deleted=0 AND item.deleted=0
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
                                    <a href="../stock.php?stock_id='.$stock_id.'"><h2>'.$stock_inv_data[0]['name'].'</h2></a>
                                    <p id="sku"><strong>SKU:</strong> <or class="blue">'.$stock_inv_data[0]['sku'].'</or></p>');

                                    echo('
                                    <p id="locations" style="margin-bottom:0"><strong>Locations:</strong><br>');
                                        if (!$stock_inv_data[0]['shelf_id']) {
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
                }
            }
        }
        // <input type="text" name="manufacturer" placeholder="Manufacturer - make drop down" id="manufacturer" class="form-control nav-v-c" style="width:300px" value="'.$input_manufacturer.'"></input>
        // <input type="text" name="site" placeholder="Site - make drop down" id="site" class="form-control nav-v-c" style="width:300px" value="'.$input_site.'"></input>
        // <input type="text" name="area" placeholder="Area - make drop down" id="area" class="form-control nav-v-c" style="width:300px" value="'.$input_area.'"></input>
        // <input type="text" name="shelf" placeholder="Shelf - make drop down" id="shelf" class="form-control nav-v-c" style="width:300px" value="'.$input_shelf.'" required></input>
        echo('
            <div class="container well-nopad theme-divBg">
                <div class="row">
                    <div class="text-left" id="stock-info-left" style="padding-left:15px">
                        <div class="nav-row" style="margin-bottom:25px">
                            <div class="nav-row" id="upc-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="upc" id="upc-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Universal Product Code for item">UPC</or></label></div>
                                <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c" style="width:300px" value="'.$input_upc.'"></input></div>
                            </div>
                            <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                <div>
                                    <select name="manufacturer" id="manufacturer" class="form-control" style="width:300px" required>
                                        <option value="" selected disabled hidden>Select Manufacturer</option>');
                                        include 'includes/dbh.inc.php';
                                            $sql = "SELECT id, name
                                                    FROM manufacturer
                                                    ORDER BY id";
                                            $stmt = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                // fails to connect
                                            } else {
                                                mysqli_stmt_execute($stmt);
                                                $result = mysqli_stmt_get_result($stmt);
                                                $rowCount = $result->num_rows;
                                                if ($rowCount < 1) {
                                                    echo('<option value="0">No Manufacturers Found...</option>');
                                                } else {
                                                    // rows found
                                                    while ($row = $result->fetch_assoc()) {
                                                        $manufacturers_id = $row['id'];
                                                        $manufacturers_name = $row['name'];
                                                        echo('<option value="'.$manufacturers_id.'">'.$manufacturers_name.'</option>');
                                                    }
                                                }
                                            }
                                    echo('
                                    </select>
                                </div>
                                <div>
                                    <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'manufacturer\')">Add New</label>
                                </div>
                            </div>
                            <div class="nav-row" id="site-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site</label></div>
                                <div>
                                    <select class="form-control" id="site" name="site" style="width:300px" required>
                                        <option value="" selected disabled hidden>Select Site</option>');
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
                                </div>');
                                if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                    echo('<div>
                                        <label class="text-right orangebrown clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'site\')">Add New (admin only)</label>
                                    </div>');
                                }
                            echo('
                            </div>
                            <div class="nav-row" id="area-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area</label></div>
                                <div>
                                    <select class="form-control" id="area" name="area" style="width:300px" disabled required>
                                        <option value="" selected disabled hidden>Select Area</option>
                                    </select>
                                </div>');
                                if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                    echo('<div>
                                    <label class="text-right orangebrown clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'area\')">Add New (admin only)</label>
                                </div>');
                                }
                            echo('</div>
                            <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf</label></div>
                                <div>
                                    <select class="form-control" id="shelf" name="shelf" style="width:300px" disabled required>
                                        <option value="" selected disabled hidden>Select Shelf</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'shelf\')">Add New</label>
                                </div>
                            </div>
                            <div class="nav-row" id="cost-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost (£)</label></div>
                                <div><input type="number" name="cost" placeholder="0" id="cost" class="form-control nav-v-c" style="width:300px" value="0" value="'.$input_cost.'" required></input></div>
                            </div>');
                            // <div class="nav-row" id="comments-row" style="margin-top:25px">
                            //     <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="comments" id="comments-label">Comments</label></div>
                            //     <div><textarea class="form-control nav-v-c" id="comments" name="comments" rows="2" cols="32" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Info about the stock, if relevant"></textarea></div>
                            // </div>
                            echo('
                        </div>
                        <hr style="border-color: gray; margin-right:15px">
                        <div class="nav-row" style="margin-bottom:25px">
                            <div class="nav-row" id="quantity-row" style="margin-top:10px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c" style="width:300px" value="1" value="'.$input_quantity.'" required></input></div>
                            </div>
                            <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c" style="width:300px" value="'.$input_serial_number.'"></input></div>
                            </div>
                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c" style="width:300px" value="New Stock" value="'.$input_reason.'"></input></div>
                            </div>
                            <div class="nav-row" id="submit-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"></div>
                                <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success" /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ');
        echo('</form>');
    } else {
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        echo('
            <form action="?modify=add" method="GET" style="margin-bottom:0">
                <div class="container" id="stock-info-left">
                    <div class="nav-row" id="search-stock-row">
                        <input type="hidden" name="modify" id="modify" value="add" />
                        <span class="nav-row">
                            <p class="nav-v-c" style="margin-right:20px">Search for item</p>
                            <input class="form-control" type="text" style="width: 250px" id="search" name="search" placeholder="Search for item" value="'.$search.'"/>
                            <p class="nav-v-c" style="margin-left:20px;margin-right:20px">or </p><a class="link btn btn-success cw nav-v-c" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">Add New Stock</a>
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
            $sql = "SELECT * from stock
                    WHERE name LIKE CONCAT('%', ?, '%') AND stock.deleted=0
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
                <table class="table table-dark theme-table" style="min-width:500px;max-width:max-content">
                    <thead>
                        <tr class="theme-tableOuter">
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
                    WHERE stock.is_cable=0 AND stock.deleted=0 
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
                                </td>
                            </td></tr>');
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
    <?php include 'includes/stock-new-properties.inc.php'; ?>
</div>

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
</script>







