<?php
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
                                        shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                        item.serial_number AS item_serial_number, item.upc AS item_upc, item.id AS item_id, item.cost AS item_cost, item.comments AS item_comments, 
                                        (SELECT SUM(quantity) 
                                            FROM item 
                                            WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id AND item.manufacturer_id=manufacturer.id 
                                                AND item.serial_number=item_serial_number AND item.upc=item_upc AND item.id=item_id
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
                                    WHERE stock.id=? AND quantity!=0
                                    GROUP BY 
                                        stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                        site_id, site_name, site_description, 
                                        area_id, area_name, 
                                        shelf_id, shelf_name,
                                        manufacturer_name, manufacturer_id,
                                        item_serial_number, item_upc, item_id, item_comments, item_cost
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
                                $item_id = $row['item_id'];
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
                                                        'item_id' => $item_id,
                                                        'upc' => $item_upc,
                                                        'cost' => $item_cost,
                                                        'comments' => $item_comments,
                                                        'serial_number' => $item_serial_number,
                                                        'label' => $stock_label_data);
                            }
                            
                            $stock_id = $_GET['stock_id'];
                            echo('<div class="nav-row" style="margin-top: 2px; margin-bottom:5px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div id="heading-heading" style="margin-left:225px;">
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
                                    <table class="table table-dark centertable" style="max-width:max-content">
                                        <thead>
                                            <tr>
                                                <th hidden>ID</th>
                                                <th>Site</th>
                                                <th>Location</th>
                                                <th>Shelf</th>
                                                <th>Manufacturer</th>
                                                <th>UPC</th>
                                                <th title="Serial Numbers">SNs</th>
                                                <th>Cost</th>
                                                <th>Comments</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>                           
                                    ');
                                    for ($i=0; $i<count($stock_inv_data); $i++) {
                                        echo('
                                            <tr id="item-'.$stock_inv_data[$i]['item_id'].'" class="clickable'); if (isset($_GET['edited']) && $_GET['edited'] == $stock_inv_data[$i]['item_id']) { echo(' last-edit'); } echo('" onclick="toggleHidden(\''.$stock_inv_data[$i]['item_id'].'\')">
                                                <td hidden>'.$stock_inv_data[$i]['item_id'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-'.$stock_inv_data[$i]['site_id'].'">'.$stock_inv_data[$i]['site_name'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'">'.$stock_inv_data[$i]['area_name'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'-'.$stock_inv_data[$i]['shelf_id'].'">'.$stock_inv_data[$i]['shelf_name'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-manu-'.$stock_inv_data[$i]['manufacturer_id'].'">'.$stock_inv_data[$i]['manufacturer_name'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-manu">'.$stock_inv_data[$i]['upc'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-sn">'.$stock_inv_data[$i]['serial_number'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-cost">'.$currency_symbol.$stock_inv_data[$i]['cost'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-comments">'.$stock_inv_data[$i]['comments'].'</td>
                                                <td id="item-'.$stock_inv_data[$i]['item_id'].'-stock">'.$stock_inv_data[$i]['quantity'].'</td>
                                            </tr>
                                            <tr class="move-hide" id="item-'.$stock_inv_data[$i]['item_id'].'-edit" hidden>
                                                <td colspan=7>
                                                    <div class="container">
                                                        <form action="includes/stock-move-existing.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-stock" name="current_stock" value="'.$stock_id.'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-item" name="current_item" value="'.$stock_inv_data[$i]['item_id'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-site" name="current_site" value="'.$stock_inv_data[$i]['site_id'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-area" name="current_area" value="'.$stock_inv_data[$i]['area_id'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-shelf" name="current_shelf" value="'.$stock_inv_data[$i]['shelf_id'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-manufacturer" name="current_manufacturer" value="'.$stock_inv_data[$i]['manufacturer_id'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-upc" name="current_upc" value="'.$stock_inv_data[$i]['upc'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-serial" name="current_serial" value="'.$stock_inv_data[$i]['serial_number'].'" />
                                                            <input type="hidden" id="'.$stock_inv_data[$i]['item_id'].'-c-quantity" name="current_quantity" value="'.$stock_inv_data[$i]['quantity'].'" />
                                                            <table style="border: 1px solid #454d55;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <label style="padding-top:5px">To:</label>
                                                                        </td>
                                                                        <td>
                                                                            <select class="form-control" id="'.$stock_inv_data[$i]['item_id'].'-n-site" name="site" style="min-width:100px" required onchange="populateAreas(\''.$stock_inv_data[$i]['item_id'].'\')">
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
                                                                        </td>
                                                                        <td>
                                                                            <select class="form-control" id="'.$stock_inv_data[$i]['item_id'].'-n-area" name="area" style="min-width:100px" disabled required onchange="populateShelves(\''.$stock_inv_data[$i]['item_id'].'\')">
                                                                                <option value="" selected disabled hidden>Select Area</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select class="form-control" id="'.$stock_inv_data[$i]['item_id'].'-n-shelf" name="shelf" style="min-width:100px" disabled required>
                                                                                <option value="" selected disabled hidden>Select Shelf</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <label style="padding-top:5px" for="'.$stock_inv_data[$i]['item_id'].'-n-quantity">Quantity: </label>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" class="form-control" id="'.$stock_inv_data[$i]['item_id'].'-n-quantity" name="quantity" style="min-width: 50px; max-width:70px;" placeholder="1" value="1" min="1" max="'.$stock_inv_data[$i]['quantity'].'" required />
                                                                        </td>
                                                                        ');
                                                                        if ($stock_inv_data[$i]['serial_number'] != '') {
                                                                            $serials = explode(', ', $stock_inv_data[$i]['serial_number']);
                                                                            echo('<td>
                                                                                <select class="form-control" id="'.$stock_inv_data[$i]['item_id'].'-n-serial" name="serial" style="min-width:100px" onchange="serialInputCheck(\''.$stock_inv_data[$i]['item_id'].'\')" required>
                                                                                    <option value="" selected disabled hidden>Serial #</option>
                                                                                    <option value=""></option>
                                                                                    ');
                                                                                    foreach ($serials as $serial) {
                                                                                        echo('<option value="'.$serial.'">'.$serial.'</option>');
                                                                                    }
                                                                                    echo('
                                                                                </select>
                                                                            </td>');
                                                                        }
                                                                        echo('
                                                                        <td>
                                                                            <input type="submit" class="btn btn-warning" id="'.$stock_inv_data[$i]['item_id'].'-n-submit" value="Move" style="opacity:80%" name="submit" required />
                                                                        </td>
                                                                    </tr>
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
                            
                            <div class="container well-nopad bg-dark" style="margin-top:5px">
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
                            <input class="form-control" type="text" style="width: 250px" id="search" name="search" placeholder="Search for item" value="'.$search.'"/>
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
                echo('SQL Failure at '.__LINE__.' in includes/stock-remove.inc.php');
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
                        <tr class="clickable" onclick="window.location.href=\'stock.php?modify=move&stock_id='.$id.'\'">
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