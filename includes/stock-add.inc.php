<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

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
        $input_tags           = isset($_GET['tags'])         ? $_GET['tags'] : '';
         
        $input_upc           = isset($_GET['upc'])           ? $_GET['upc'] : '';
        $input_manufacturer  = isset($_GET['manufacturer'])  ? $_GET['manufacturer'] : '';
        $input_site          = isset($_GET['site'])          ? $_GET['site'] : '';
        $input_area          = isset($_GET['area'])          ? $_GET['area'] : '';
        $input_shelf         = isset($_GET['shelf'])         ? $_GET['shelf'] : '';
        $input_cost          = isset($_GET['cost'])          ? $_GET['cost'] : '';
        
        $input_quantity      = isset($_GET['quantity'])      ? $_GET['quantity'] : '';
        $input_serial_number = isset($_GET['serial_number']) ? $_GET['serial_number'] : '';
        $input_reason        = isset($_GET['reason'])        ? $_GET['reason'] : '';

        echo('');
        if ($stock_id == 0 || $stock_id == '0') {
            //<input type="text" name="tags" placeholder="Labels - allow multiple" id="tags" class="form-control nav-v-c" style="width:300px" value="'.$input_tags.'"></input>
            echo('
            <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                <!-- this is for the stock-modify.inc.php page -->
                <input type="hidden" name="stock-add" value="1" /> 
                <div class="container well-nopad theme-divBg" style="margin-bottom:5px">
                    <h3 style="font-size:22px; margin-left:25px">Add New Stock</h3>
                    <div class="row">
                        <div class="col-sm text-left" id="stock-info-left">
                            <div class="nav-row">
                                <div class="nav-row" id="name-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                    <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c stock-inputSize" value="'.htmlspecialchars($input_name, ENT_QUOTES, 'UTF-8').'" required></input></div>
                                </div>
                                <div class="nav-row" id="sku-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                    <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c stock-inputSize" value="'.$input_sku.'" pattern="^[A-Za-z\s\p{P}]+$"></input></div>
                                </div>
                                <div class="nav-row" id="description-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                    <div><textarea class="form-control nav-v-c stock-inputSize" id="description" name="description" rows="3" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="'.htmlspecialchars($input_description, ENT_QUOTES, 'UTF-8').'" ></textarea></div>
                                </div>
                                <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                    <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c stock-inputSize" value="'.$input_min_stock.'"></input></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4" id="stock-info-right" style="margin-left:0px !important"> 
                            <div id="image-preview" style="height:150px;margin:auto;text-align:center">
                                <img class="nav-v-c" id="upload-img-pre" style="max-width:150px;max-height:150px" />
                            </div>
                            <div class="nav-row"  id="images-row" style="margin-top:25px">
                                <table class="centertable">
                                    <tbody>
                                        <tr>
                                            <td style="padding-right:25px" class="text-center viewport-small-empty">Image</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right:25px" class="viewport-large-empty">Image:</td>
                                            <td><input class=" text-center" type="file" accept="image/*" style="width: 15vw" id="image" name="image" onchange="loadImage(event)"></td>
                                        </tr>
                                    </tbody>
                                </table>
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
                        <div class="nav-row" id="tags-row" style="margin-top:25px;padding-left:15px;padding-right:15px">
                            <div class="stock-inputLabelSize"><label class="text-right" style="padding-top:5px;width:100%" for="tags" id="labels-tag">Tags</label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="tag-select" name="tags-init">
                                    <option value="" selected disabled hidden>-- Select a tag if needed --</option>
                                </select>

                                <select id="tags" name="tags[]" multiple class="form-control stock-inputSize" style="margin-top:2px;display: inline-block;height:40px"></select>
                                <style>
                                    #tags {
                                    display: inline-block;
                                    padding-top:2px;
                                    padding-bottom:2px;
                                    width: auto;
                                    }
                                    
                                    #tags option {
                                    display: inline-block;
                                    padding: 3px;
                                    margin-right: 10px;
                                    background-color: #f1f1f1;
                                    border: 1px solid #ccc;
                                    border-radius: 5px;
                                    }
                                </style>
                                <script>
                                var selectBox = document.getElementById("tag-select");
                                var selectedBox = document.getElementById("tags");

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
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'tag\')">Add New</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container well-nopad theme-divBg">
                    <div class="row">
                        <div class="text-left" id="stock-info-left" style="padding-left:15px">
                            <div class="nav-row" style="margin-bottom:25px">
                                ');
                                if ($data_is_cable == 0) {
                                    echo('
                                    <div class="nav-row" id="upc-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="upc" id="upc-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Universal Product Code for item">UPC</or></label></div>
                                        <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c stock-inputSize" value="'.$input_upc.'"></input></div>
                                    </div>
                                    <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                        <div  class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                        <div>
                                            <select name="manufacturer" id="manufacturer-select" class="form-control stock-inputSize" required>
                                                <option value="" selected disabled hidden>Select Manufacturer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'manufacturer\')">Add New</label>
                                        </div>
                                    </div>
                                    ');
                                }
                                echo('
                                <div class="nav-row" id="site-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site</label></div>
                                    <div>
                                        <select class="form-control stock-inputSize" id="site" name="site" required>
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
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area</label></div>
                                    <div>
                                        <select class="form-control stock-inputSize" id="area" name="area" disabled required>
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
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf</label></div>
                                    <div>
                                        <select class="form-control stock-inputSize" id="shelf" name="shelf" disabled required>
                                            <option value="" selected disabled hidden>Select Shelf</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'shelf\')">Add New</label>
                                    </div>
                                </div>
                                ');
                                if ($data_is_cable == 0) {
                                    echo('
                                    <div class="nav-row" id="cost-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost ('.$config_currency.')</label></div>
                                        <div><input type="number" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize" value="0" value="'.$input_cost.'" required></input></div>
                                    </div>
                                    ');
                                }
                                echo('
                            </div>
                            <hr style="border-color: gray; margin-right:15px">
                            <div class="nav-row" style="margin-bottom:25px">
                                <div class="nav-row" id="quantity-row" style="margin-top:10px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                    <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="'.$input_quantity.'" required></input></div>
                                </div>
                                ');
                                if ($data_is_cable == 0) {
                                    echo('
                                    <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                        <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize" value="'.$input_serial_number.'"></input></div>
                                    </div>
                                    ');
                                }
                                echo('
                                <div class="nav-row" id="reason-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                    <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c stock-inputSize" value="New Stock" value="'.htmlspecialchars($input_reason, ENT_QUOTES, 'UTF-8').'"></input></div>
                                </div>
                                <div class="nav-row" id="submit-row" style="margin-top:25px">
                                    <div class="stock-inputLabelSize"></div>
                                    <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success" /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
            ');
        } else {
            // Check if stock exists
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

                    // Adding exisiting stock for item

                    if ($data_is_cable == 0) {
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
                                    WHERE stock.id=? AND stock.deleted=0 AND item.deleted=0
                                    GROUP BY 
                                        stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                        site_id, site_name, site_description, 
                                        area_id, area_name, 
                                        shelf_id, shelf_name,
                                        manufacturer_id, manufacturer_name
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
                                                        'tag' => $stock_tag_data);
                                }
                            }
                            
                            $stock_id = $_GET['stock_id'];
                            echo('<form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">');
                                if ($data_is_cable == 0) {
                                    echo('<input type="hidden" name="stock-add" value="1" />  ');
                                } else {
                                    echo('<input type="hidden" name="cablestock-add" value="1" />  ');
                                }
                                echo('
                                <input type="hidden" name="id" value="'.$_GET['stock_id'].'" />
                                <div class="nav-row" style="margin-bottom:10px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div class="stock-inputLabelSize"></div>
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
                                                        echo('<tr><td>'.$stock_inv_data[$l]['site_name'].', '.$stock_inv_data[$l]['area_name'].', '.$stock_inv_data[$l]['shelf_name'].'</td><td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">'.$stock_inv_data[$l]['quantity'].'</or></a></or></td></tr>');
                                                    }
                                                    echo('</tbody></table>');
                                                }
                                                
                                            echo('</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="container well-nopad theme-divBg">
                                    <div class="row">
                                        <div class="text-left" id="stock-info-left" style="padding-left:15px">
                                            <div class="nav-row" style="margin-bottom:25px">
                                                ');
                                                if ($data_is_cable == 0) {
                                                    echo('
                                                    <div class="nav-row" id="upc-row" style="margin-top:25px">
                                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="upc" id="upc-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Universal Product Code for item">UPC</or></label></div>
                                                        <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c stock-inputSize" value="'.$input_upc.'"></input></div>
                                                    </div>
                                                    <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                                        <div  class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                                        <div>
                                                            <select name="manufacturer" id="manufacturer-select" class="form-control stock-inputSize" required>
                                                                <option value="" selected disabled hidden>Select Manufacturer</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'manufacturer\')">Add New</label>
                                                        </div>
                                                    </div>
                                                    ');
                                                }
                                                echo('
                                                <div class="nav-row" id="site-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site</label></div>
                                                    <div>
                                                        <select class="form-control stock-inputSize" id="site" name="site" required>
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
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area</label></div>
                                                    <div>
                                                        <select class="form-control stock-inputSize" id="area" name="area" disabled required>
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
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf</label></div>
                                                    <div>
                                                        <select class="form-control stock-inputSize" id="shelf" name="shelf" disabled required>
                                                            <option value="" selected disabled hidden>Select Shelf</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'shelf\')">Add New</label>
                                                    </div>
                                                </div>
                                                ');
                                                if ($data_is_cable == 0) {
                                                    echo('
                                                    <div class="nav-row" id="cost-row" style="margin-top:25px">
                                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost ('.$config_currency.')</label></div>
                                                        <div><input type="number" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize" value="0" value="'.$input_cost.'" required></input></div>
                                                    </div>
                                                    ');
                                                }
                                                echo('
                                            </div>
                                            <hr style="border-color: gray; margin-right:15px">
                                            <div class="nav-row" style="margin-bottom:25px">
                                                <div class="nav-row" id="quantity-row" style="margin-top:10px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label></div>
                                                    <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="'.$input_quantity.'" required></input></div>
                                                </div>
                                                ');
                                                if ($data_is_cable == 0) {
                                                    echo('
                                                    <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                                        <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize" value="'.$input_serial_number.'"></input></div>
                                                    </div>
                                                    ');
                                                }
                                                echo('
                                                <div class="nav-row" id="reason-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                                    <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c stock-inputSize" value="New Stock" value="'.htmlspecialchars($input_reason, ENT_QUOTES, 'UTF-8').'"></input></div>
                                                </div>
                                                <div class="nav-row" id="submit-row" style="margin-top:25px">
                                                    <div class="stock-inputLabelSize"></div>
                                                    <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success" /></div>
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
            <form action="?modify=add" method="GET" style="margin-bottom:0">
                <div class="container" id="stock-info-left">
                    <div class="nav-row" id="search-stock-row">
                        <input type="hidden" name="modify" id="modify" value="add" />
                        
                        <table>
                            <tbody>
                                <tr>
                                    <td style="padding-right:20px">Search for item</td>
                                    <td><input class="form-control stock-inputSize" type="text" id="search" name="search" placeholder="Search for item" value="'.htmlspecialchars($search, ENT_QUOTES, 'UTF-8').'"/></td>
                                    <td class="text-right viewport-mid-large" style="padding-left:20px;padding-right:20px">or</td>
                                    <td class="viewport-mid-large"><a class="link btn btn-success cw" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">Add New Stock</a></td>
                                </tr>
                                <tr class="viewport-small-only-empty">
                                    <td class="text-right" style="padding-right:20px">or</td>
                                    <td><a class="link btn btn-success cw" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">Add New Stock</a></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </form>
        ');
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            echo('
            <div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">
                ');
            include 'includes/dbh.inc.php';
            $search = mysqli_real_escape_string($conn, $search); // escape special characters
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
    }
    
    ?>
    <?php include 'includes/stock-new-properties.inc.php'; ?>
</div>
<script>
    function loadProperty(property) {
        var select = document.getElementById(property+'-select');
        var upperProperty = property[0].toUpperCase() + property.substring(1);
        $.ajax({
            type: "POST",
            url: "./includes/stock-new-properties.inc.php",
            data: {
                load_property: '1',
                type: property,
                submit: '1'
            },
            dataType: "json",
            success: function(response) {
                var rows = response;
                if (Array.isArray(rows)) {
                    select.options.length = 0;
                    select.options[0] = new Option('Select '+upperProperty, '');
                    for (var j = 0; j < rows.length; j++) {
                        select.options[j+1] = new Option(rows[j].name, rows[j].id);
                    }
                    select.options[0].disaled = true;
                    select.options[0].selected = true;
                } else {
                    console.log('error - check loadProperty function');
                }
            },
            async: true
        });
    }
    if (document.getElementById('manufacturer-select')) {
        document.onload = loadProperty('manufacturer');
    }
    if (document.getElementById('tag-select')) {
        document.onload = loadProperty('tag');
    }
    
</script>
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
if (document.getElementById("site")) {
    document.getElementById("site").addEventListener("change", populateAreas);
}
if (document.getElementById("area")) {
    document.getElementById("area").addEventListener("change", populateShelves);
}
</script>







