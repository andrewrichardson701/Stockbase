<?php 
// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Fixed Cable Stock</title>
</head>
<body>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->
    <div class="content">
        <?php
        // functions
        function containsColorName($inputString) {
            // List of color names (you can add more colors as needed)
            $colorNames = array(
                'red', 'green', 'blue', 'yellow', 'orange', 'purple', 'pink',
                'brown', 'white', 'black', 'gray', 'cyan', 'magenta', 'gold', 'aqua'
            );
        
            // Convert the input string to lowercase
            $lowercaseInput = strtolower($inputString);
        
            // Loop through the color names and check if any of them are present in the input string
            foreach ($colorNames as $color) {
                if (strpos($lowercaseInput, $color) !== false) {
                    return $color; // Found a color name in the input string
                }
            }
        
            return false; // No color name found in the input string
        }

        function getColorHexFromName($colorName) {
            // Convert the color name to lowercase for case-insensitive matching
            $colorNameLower = strtolower($colorName);
        
            // Associative array with HTML color names and their corresponding HEX values
            $htmlColors = array(
                'aqua' => '#00ffff',
                'black' => '#000000',
                'blue' => '#0000ff',
                'fuchsia' => '#ff00ff',
                'gray' => '#808080',
                'green' => '#008000',
                'lime' => '#00ff00',
                'maroon' => '#800000',
                'navy' => '#000080',
                'olive' => '#808000',
                'purple' => '#800080',
                'red' => '#ff0000',
                'silver' => '#c0c0c0',
                'teal' => '#008080',
                'white' => '#ffffff',
                'yellow' => '#ffff00'
                // Add more color mappings as needed
            );
        
            // Check if the color name exists in the color map
            if (isset($htmlColors[$colorNameLower])) {
                return $htmlColors[$colorNameLower];
            } else {
                return false; // Color name not found in the color map
            }
        }
        ?>
        
        <!-- Get Inventory -->
        <?php
        $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
        $site = isset($_GET['site']) ? $_GET['site'] : "0";
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $cableType = isset($_GET['cable']) ? $_GET['cable'] : 'copper';
        $site_names_array = [];
        $area_names_array = [];

        include 'includes/dbh.inc.php';
        $s = 0;
        $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, 
                        stock.sku as stock_sku, stock.min_stock as stock_min_stock, stock.is_cable as stock_is_cable,
                        cable_item.id as cable_item_id, cable_item.stock_id as cable_item_stock_id, cable_item.quantity as cable_item_quantity,
                        cable_item.cost AS cable_item_cost, cable_item.site_id AS cable_item_site_id, cable_item.type_id as cable_item_type_id,
                        cable_types.id AS cable_types_id, cable_types.name AS cable_types_name, cable_types.description AS cable_types_description,
                        cable_types.parent AS cable_types_parent,
                        site.id AS site_id, site.name AS site_name,
                        stock_img_image.stock_img_image
                    FROM cable_item
                    LEFT JOIN stock ON cable_item.stock_id=stock.id 
                    LEFT JOIN site ON cable_item.site_id=site.id
                    LEFT JOIN cable_types ON cable_item.type_id=cable_types.id
                    LEFT JOIN (
                            SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                            FROM stock_img
                            GROUP BY stock_img.stock_id
                        ) AS stock_img_image
                        ON stock_img_image.stock_id = stock.id
                    WHERE stock.is_cable=1";
        $sql_inv_add = '';
        if ($site !== '0') { 
            $sql_inv_add  .= " AND site.id=$site"; $s++; 
        } 
        if ($showOOS == 0) { 
            $sql_inv_add  .= " AND cable_item.quantity!=0";
        } 
        if ($name !== '') { 
            $sql_inv_add  .= " AND stock.name LIKE '%$name%'";
        }
        if (isset($cableType)) {
            $type = ucwords($cableType);
            $sql_inv_add .= " AND cable_types.parent = '$type'";
        }    
        $sql_inv .= $sql_inv_add;
        $sql_inv .= " GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
                        cable_item_id, 
                        site_id, site_name, stock_img_image.stock_img_image";
        $sql_inv .= " ORDER BY stock.name;";
        echo '<pre hidden>'.$sql_inv.'</pre>';
        $stmt_inv = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_inv);
            $result_inv = mysqli_stmt_get_result($stmt_inv);
            $rowCount_inv = $result_inv->num_rows;

            // GET SITE AND AREA VALUES
            //site
            include 'includes/dbh.inc.php';

            $sql_site = "SELECT DISTINCT site.id, site.name, site.description
                        FROM site 
                        ORDER BY site.id";
            $stmt_site = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_site);
                $result_site = mysqli_stmt_get_result($stmt_site);
                $rowCount_site = $result_site->num_rows;
                if ($rowCount_site < 1) {
                    echo ("No sites found");
                    exit();
                } else {
                    
                    while( $row = $result_site->fetch_assoc() ) {
                        $site_id = $row['id'];
                        $site_name = $row['name'];
                        $site_description = $row['description'];
                        $site_names_array[$site_id] = $site_name;
                        // echo('<option style="color:black" value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                    }          
                }
            }

            //area
            if (isset($_GET['site']) && $_GET['site'] !==0) {
                $sql_area = "SELECT DISTINCT area.id, area.name, area.description, area.site_id
                            FROM area 
                            INNER JOIN site ON site.id=area.site_id
                            WHERE site.id=?
                            ORDER BY area.id";
                $stmt_area = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_area, "s", $site);
                    mysqli_stmt_execute($stmt_area);
                    $result_area = mysqli_stmt_get_result($stmt_area);
                    $rowCount_area = $result_area->num_rows;
                    if ($rowCount_area < 1) {
                        // echo ("No areas found");
                        // exit();
                    } else {
                        while( $row = $result_area->fetch_assoc() ) {
                            $area_id = $row['id'];
                            $area_name = $row['name'];
                            $area_description = $row['description'];
                            $area_names_array[$area_id] = $area_name;
                            // echo('<option style="color:black" value="'.$area_id.'"'); if ($area == $area_id) { echo('selected'); } echo('>'.$area_name.'</option>');
                        }
                        // echo($area);
                    }
                }
            }

            echo('
                <div class="container" style="padding-bottom:25px">
                    <h2 class="header-small" style="padding-bottom:10px">'.ucwords($current_system_name).' - Fixed Cables');
                echo('</h2>
                <p>Stock for any fixed cables (cables which always need to be in stock and have known locations).</p>
                </div>

                <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:20px">
                    <div class="nav-row">
                        <form action="./cablestock.php" method="get" class="nav-row" style="max-width:max-content">
                            <input id="query-site" type="hidden" name="site" value="'.$site.'" />');
                            echo ('
                            <span id="search-input-site-span" style="margin-right: 10px; padding-left:12px">
                                <label for="search-input-site">Site</label><br>
                                <select id="site-dropdown" name="site" class="form-control nav-v-b cw" style="background-color:484848;border-color:black;margin:0;padding-left:0" onchange="siteChange(\'site-dropdown\')">
                                <option style="color:white" value="0"'); if ($site == 0) { echo('selected'); } echo('>All</option>
                            ');
                            if (!empty($site_names_array)) {
                                foreach (array_keys($site_names_array) as $site_id) {
                                    $site_name = $site_names_array[$site_id];
                                    echo('<option style="color:white" value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                                }
                            }
                            
                            echo('
                                </select>
                            </span>
                            ');  
                            
                            echo('
                            <span id="search-input-name-span" style="margin-right: 10px;margin-left:10px">
                                <label for="search-input-name">Name</label><br>
                                <input id="search-input-name" type="text" name="name" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                            </span>
                            <input type="submit" value="submit" hidden>
                        </form>');

                        echo('
                        <div id="clear-div" class="nav-div" style="margin-left:5px;margin-right:0">
                            <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black;padding:6 6 6 6" onclick="navPage(\'./cablestock.php\')">
                                <i class="fa fa-rotate-right" style="height:24px;padding-top:4px"></i>
                            </button>
                        </div>
                        <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0">');
                        if ($showOOS == 0) {
                            echo('<button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0 2 0 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'1\'))">');
                        } else {
                            echo('<button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0 2 0 2" onclick="navPage(updateQueryParameter(\'\', \'oos\', \'0\'))">');
                        }
                                echo('
                                <span>
                                    <p style="margin:0;padding:0;font-size:12">'); if ($showOOS == 0) { echo('<i class="fa fa-plus"></i> Show'); } else { echo('<i class="fa fa-minus"></i> Hide'); } echo('</p>
                                    <p style="margin:0;padding:0;font-size:12">0 Stock</p>
                            </button>
                        </div>
                        <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0">
                            <button id="cable-stock" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6 6 6 6" onclick="navPage(\'/\')">
                                Item Stock
                            </button>
                        </div>');
                        
                        echo('
                    </div>
                </div>

            ');
            
                
            echo('
            <!-- Modal Image Div -->
            <div id="modalDiv" class="modal" onclick="modalClose()">
                <span class="close" onclick="modalClose()">&times;</span>
                <img class="modal-content bg-trans" id="modalImg">
                <div id="caption" class="modal-caption"></div>
            </div>
            <!-- End of Modal Image Div -->
            <style>
            .th-selected {
                background-color: #202328;
                border: 0px !important;
            }
            .th-noBorder {
                border: 0px !important;
            }
            </style>
            <!-- Table -->
            <div class="container">
                <table class="table table-dark centertable" id="cableSelection" style="border:0px !important">
                    <thead style="text-align: center; white-space: nowrap; border:0px !important">
                        <tr style="border:0px !important">');
                            echo('<th class="clickable '); if ($cableType == "copper" || $cableType == '') { echo('th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'copper\'))">Copper</th>');
                            echo('<th class="clickable '); if ($cableType == "fibre") { echo('th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'fibre\'))">Fibre</th>');
                            echo('<th class="clickable '); if ($cableType == "power") { echo('th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'power\'))">Power</th>');
                            echo('<th class="clickable '); if ($cableType == "other") { echo('th-selected'); } else { echo('th-noBorder'); } echo('" onclick="navPage(updateQueryParameter(\'\', \'cable\', \'other\'))">Other</th>');
                            echo('
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan=4 class="th-selected">');
                            if ($rowCount_inv < 1) {
                                echo ('<div class="container" id="no-inv-found">No Inventory Found</div>');
                            } else {
                                echo('
                                <table class="table table-dark centertable" id="inventoryTable">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr>
                                            <th id="stock-id" hidden>Stock ID</th>
                                            <th id="item-id" hidden>Item ID</th>
                                            <th id="image"></th>
                                            <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(3, this)">Name</th>
                                            <th id="type-id" hidden>Type ID</th>
                                            <th class="clickable sorting" id="type" onclick="sortTable(5, this)">Type</th>
                                            <th class="clickable sorting" id="site-name" onclick="sortTable(6, this)">Site</th>
                                            <th class="clickable sorting" id="quantity" onclick="sortTable(7, this)">Quantity</th>
                                            <th id="min-stock" style="color:#8f8f8f">Min. stock</th>
                                            <th style="width:50px"></th>
                                            <th style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle" style="text-align: center; white-space: nowrap;">
                                ');
                                // Inventory Rows
                                while ( $row = $result_inv->fetch_assoc() ) {
                                    // print_r('<pre>'); print_r($row); print_r('</pre>');
                                    $img_directory = "assets/img/stock/"; 

                                    $stock_id = $row['stock_id'];
                                    $stock_img_file_name = $row['stock_img_image'];
                                    $stock_name = $row['stock_name'];
                                    $stock_sku = $row['stock_sku'];
                                    $stock_quantity_total = $row['cable_item_quantity'];
                                    $stock_site_id = $row['site_id'];
                                    $stock_site_name = $row['site_name'];
                                    $stock_min_stock = $row['stock_min_stock'];
                                    $cable_item_id = $row['cable_item_id'];
                                    $cable_types_id = $row['cable_types_id'];
                                    $cable_types_name = $row['cable_types_name'];
                                    $cable_types_description = $row['cable_types_description']; 
                                    $cable_types_parent = $row['cable_types_parent'];         

                                    // Echo each row (inside of SQL results)
                                    if (isset($_GET['cableItemID']) && $_GET['cableItemID'] == $cable_item_id) { 
                                        $last_edited = ' last-edit'; 
                                    } else {
                                        $last_edited = '';
                                    }

                                    echo('
                                        <tr class="vertical-align align-middle'.$last_edited.'" id="'.$cable_item_id.'">
                                            <form id="modify-cable-item-'.$cable_item_id.'" action="includes/cablestock-edit.inc.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="stock-id" value="'.$stock_id.'" />
                                                <input type="hidden" name="cable-item-id" value="'.$cable_item_id.'" />
                                                <td class="align-middle" id="'.$cable_item_id.'-stock-id" hidden>'.$stock_id.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-item-id" hidden>'.$cable_item_id.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-img-td">
                                                ');
                                                if (!is_null($stock_img_file_name)) {
                                                    echo('<img id="'.$cable_item_id.'-img" class="inv-img thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />');
                                                }
                                                echo('</td>');
                                                
                                                if (strpos($stock_name, "SM") !== false && $cable_types_parent == "Fibre") {
                                                    $nameColor = "yellow";
                                                } elseif (strpos($stock_name, "MM") !== false && $cable_types_parent == "Fibre") {
                                                    $nameColor = "aqua";
                                                } else {
                                                    $nameColor = containsColorName($stock_name);
                                                }
                                                
                                                $name_prefix = '';
                                                $name_suffix = '';
                                                if ($nameColor !== false && $nameColor !== null && $nameColor !== '') {
                                                    $nameColorHex = getColorHexFromName($nameColor);
                                                    $complement_nameColor = getComplement($nameColorHex);
                                                    $name_prefix = "<or style='background-color: $nameColorHex; color: $complement_nameColor'>";
                                                    $name_suffix = "</or>";
                                                }
                                                echo('
                                                <td class="align-middle" id="'.$cable_item_id.'-name">'.$name_prefix.$stock_name.$name_suffix.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-type-id" hidden>'.$cable_types_id.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-type"><or title="'.$cable_types_description.'">'.$cable_types_name.'</or></td> 
                                                <td class="align-middle clickable link gold" id="'.$cable_item_id.'-site-name" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-quantity">'); 
                                                if ($stock_quantity_total == 0) {
                                                    echo("<or class='red' title='Out of Stock'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Out of stock. Order more if necessary.'>0 <i class='fa fa-warning' /></u></or>");
                                                } elseif ($stock_quantity_total < $stock_min_stock) {
                                                    echo("<or class='red'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Below minimum stock count. Order more!'>$stock_quantity_total</u></or>");
                                                } else {
                                                    echo($stock_quantity_total);
                                                }
                                                echo('</td>');
                                            echo('
                                                <td class="align-middle" id="'.$cable_item_id.'-min-stock"  style="color:#8f8f8f">'.$stock_min_stock.'</td>
                                                <td class="align-middle" id="'.$cable_item_id.'-add"><button id="'.$stock_id.'-add-btn" class="btn btn-success cw nav-v-b" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                                                <td class="align-middle" id="'.$cable_item_id.'-remove"><button id="'.$stock_id.'-remove-btn" class="btn btn-danger cw nav-v-b" type="submit" name="action" value="remove" '); if ($stock_quantity_total == 0) { echo "disabled"; } echo('><i class="fa fa-minus"></i></button></td>
                                            </form>
                                        </tr>
                                    ');
                                }

                                // End table + body
                                echo ('
                                        </body>
                                    </table>');
                            }
                            echo('
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            ');    

        }

        ?>
    </div>

    <script>
        // Function to get the value of a query parameter from the URL
        function getQueryParameter(parameterName) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(parameterName);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Get the value of the "cableItemID" query parameter from the URL
            const cableItemID = getQueryParameter("cableItemID");

            // Check if the "cableItemID" parameter is set and not empty
            if (cableItemID && cableItemID.trim() !== "") {
                // Scroll to the element with the ID equal to "cableItemID"
                const elementToScroll = document.getElementById(cableItemID);
                if (elementToScroll) {
                    elementToScroll.scrollIntoView({ behavior: "smooth" });
                }
            }
        });
    </script>
        
    <?php include 'foot.php'; ?>

</body>