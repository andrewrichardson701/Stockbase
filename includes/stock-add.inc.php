<?php
// INCLUDED IN THE STOCK PAGE FOR NEW STOCK OR INVENTORY TO CURRENT STOCK

// Query string bits
$stock_id = isset($_GET['stock_id']) ? $_GET['stock_id'] : '';

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

        echo('<form action="includes/stock-add-new.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">');
        if ($stock_id == 0 || $stock_id == '0') {
            //<input type="text" name="labels" placeholder="Labels - allow multiple" id="labels" class="form-control nav-v-c" style="width:300px" value="'.$input_labels.'"></input>
            echo('
            <div class="container well-nopad bg-dark" style="margin-bottom:5px">
                <h3 style="font-size:22px; margin-left:25pxq">Add New Stock</h3>
                <div class="row">
                    <div class="col-sm text-left" id="stock-info-left">
                        <div class="nav-row" style="margin-bottom:25px">
                            <div class="nav-row" id="name-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c" style="width:300px" value="'.$input_name.'" required></input></div>
                            </div>
                            <div class="nav-row" id="sku-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c" style="width:300px" value="'.$input_sku.'" ></input></div>
                            </div>
                            <div class="nav-row" id="description-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                <div><textarea class="form-control nav-v-c" id="description" name="description" rows="3" cols="32" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="'.$input_description.'" ></textarea></div>
                            </div>
                            <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                <div><input type="text" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c" style="width:300px" value="'.$input_min_stock.'"></input></div>
                            </div>
                            <div class="nav-row" id="labels-row" style="margin-top:25px">
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
                                                echo('<option value="0">No Manufacturers Found...</option>');
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
                            </div>
                        </div>
                    </div>

                    <div class="col-sm text-right"  id="stock-info-right"> 
                        <div id="image-preview" style="height:150px"></div>
                        <div class="nav-row"  id="labels-row" style="margin-top:25px">
                            <div class="nav-right" style="margin-right:25px"><label class="nav-v-c" style="width:100%" for="labels" id="labels-label">Image:</label></div>
                            <div><input class="nav-v-c" type="file" style="width: 350px" id="image" name="image"></div>
                        </div>
                    </div>
                </div>
            </div>
            ');
        } else {
            // Adding exisiting stock for item
            echo('<input type="hidden" name="id" value="'.$_GET['stock_id'].'" />');
        }
        // <input type="text" name="manufacturer" placeholder="Manufacturer - make drop down" id="manufacturer" class="form-control nav-v-c" style="width:300px" value="'.$input_manufacturer.'"></input>
        // <input type="text" name="site" placeholder="Site - make drop down" id="site" class="form-control nav-v-c" style="width:300px" value="'.$input_site.'"></input>
        // <input type="text" name="area" placeholder="Area - make drop down" id="area" class="form-control nav-v-c" style="width:300px" value="'.$input_area.'"></input>
        // <input type="text" name="shelf" placeholder="Shelf - make drop down" id="shelf" class="form-control nav-v-c" style="width:300px" value="'.$input_shelf.'" required></input>
        echo('
            <div class="container well-nopad bg-dark">
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
                                    <select name="manufacturer" id="manufacturer" class="form-control" style="width:300px">
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
                            </div>
                            <div class="nav-row" id="site-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site</label></div>
                                <div>
                                    <select class="form-control" id="site" name="site" style="width:300px" required>
                                        <option value="" selected disabled hidden>Select Site</option>');
                                            include 'includes/dbh.inc.php';
                                            $sql = "SELECT id, name
                                                    FROM site
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
                            </div>
                            <div class="nav-row" id="area-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area</label></div>
                                <div>
                                    <select class="form-control" id="area" name="area" style="width:300px" disabled required>
                                        <option value="" selected disabled hidden>Select Area</option>
                                    </select>
                               </div>
                            </div>
                            <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf</label></div>
                                <div>
                                    <select class="form-control" id="shelf" name="shelf" style="width:300px" disabled required>
                                        <option value="" selected disabled hidden>Select Shelf</option>
                                    </select>
                                </div>
                            </div>
                            <div class="nav-row" id="cost-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost (£)</label></div>
                                <div><input type="text" name="cost" placeholder="0" id="cost" class="form-control nav-v-c" style="width:300px" value="0" value="'.$input_cost.'" required></input></div>
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
                                <div><input type="text" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c" style="width:300px" value="1" value="'.$input_quantity.'" required></input></div>
                            </div>
                            <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c" style="width:300px" value="'.$input_serial_number.'"></input></div>
                            </div>
                            <div class="nav-row" id="reason-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                                <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c" style="width:300px" value="New Stock" value="'.$input_reason.'"></input></div>
                            </div>
                            <div class="nav-row" id="reason-row" style="margin-top:25px">
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
        if (isset($_GET['search'])) {
            echo('
            <div class="container well-nopad bg-dark" style="margin-top:20px;padding-left:20px">
                ');
            include 'includes/dbh.inc.php';
            $sql = "SELECT * from stock
                    WHERE name LIKE CONCAT('%', ?, '%')
                    ORDER BY name;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo('SQL Failure at '.__LINE__.' in includes/stock-add.php');
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
                        <tr class="clickable" onclick="window.location.href=\'stock.php?modify=add&stock_id='.$id.'\'">
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
        }

    }
    
    ?>
    
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







