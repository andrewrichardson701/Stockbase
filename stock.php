<?php 
// SHOWS THE INFORMATION FOR EACH PEICE OF STOCK AND ITS LOCATIONS ETC. 
// id QUERY STRING IS NEEDED FOR THIS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>Inventory - Stock</title>
</head>
<body>
    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <?php // dependency PHP
    $show_inventory = 0; // for nav.php to show the site and area on the banner
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $stock_id = $_GET['id'];
        } else {
            echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['id'].'</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>');
            exit();
        }
    } else {
        header("Location: ./?error=noStockSelected");
        exit();
    }
    if (!isset($_SERVER['HTTP_REFERER'])) {
        $_SERVER['HTTP_REFERER'] = './index.php';
    }
    ?>

    <!-- Get Inventory -->
    <?php
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
                    <button id="add-stock" class="btn btn-secondary cw nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(\''.$_SERVER['HTTP_REFERER'].'\');">
                        <i class="fa fa-arrow-left fa-2xs"></i> Back 
                    </button>
                    <div class="nav-row" style="margin-top:10px">
                        <h3 style="font-size:22px;margin-top:20px;margin-bottom:0;width:max-content" id="stock-name">'.$stock_name.' ('.$stock_sku.')</h3>
                        <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                            <button id="edit-stock" class="btn btn-info cw nav-v-b" style="width:110px">
                                <i class="fa fa-pencil"></i> Edit 
                            </button>
                        </div> 
                        <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                            <button id="add-stock" class="btn btn-success cw nav-v-b" style="width:110px">
                                <i class="fa fa-plus"></i> Add 
                            </button>
                        </div> 
                        <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                            <button id="remove-stock" class="btn btn-danger cw nav-v-b" style="width:110px">
                                <i class="fa fa-minus"></i> Remove 
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
                    shelf.id AS shelf_id, shelf.name AS shelf_name,
                    site.id AS site_id, site.name AS site_name, site.description AS site_description,
                    SUM(item.quantity) AS item_quantity,
                    manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name
                FROM stock
                INNER JOIN item ON stock.id=item.stock_id
                INNER JOIN shelf ON item.shelf_id=shelf.id 
                INNER JOIN area ON shelf.area_id=area.id 
                INNER JOIN site ON area.site_id=site.id
                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                WHERE stock.id=?
                GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                site_id, site_name, site_description, 
                area_id, area_name, 
                shelf_id, shelf_name,
                manufacturer_id, manufacturer_name
                ORDER BY area.name;";
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
                                        'manufacturer_name' => $stock_manufacturer_name);
            }
            
            
            // Inventory Rows
            echo ('
            <div class="container well-nopad bg-dark">
                <div class="row">
                    <div class="col-sm text-left" id="stock-info-left">
                        <p id="locations-head"><strong>Locations</strong></p>
                        <p id="locations">');
                            for ($l=0; $l < count($stock_inv_data); $l++) {
                                echo($stock_inv_data[$l]['area_name'].' <a class="btn btn-dark cw" style="padding: 0px 3px 0px 3px;cursor:auto">Stock: <or class="gold">'.$stock_inv_data[$l]['quantity'].'</or></a>, ');
                            }
                        echo('</p>
                        <p id="labels-head"><strong>Labels</strong></p>
                        <p id="labels">***label***</p>
                        <p id="sku-head"><strong>SKU</strong></p>
                        <p id="sku">'.$stock_sku.'</p>
                        <p id="shelf-head"><strong>Shelf</strong></p>
                        <p id="shelf">');
                            for ($l=0; $l < count($stock_inv_data); $l++) {
                                if ($l == 0 && $l < count($stock_inv_data)-1) { $divider = ', <br>'; } else { $divider = ''; }
                                echo($stock_inv_data[$l]['area_name'].': <or class="gold">'.$stock_inv_data[$l]['shelf_name'].'</or>'.$divider);
                            }
                        echo('</p>
                        <p id="manufacturer-head"><strong>Manufacturer</strong></p>
                        <p id="manufacturer" class="gold">'.$stock_manufacturer_name.'</p>
                    </div>
                    <div class="col-sm text-center" id="stock-info-middle">
                    </div>
                    <div class="col-sm text-right"  id="stock-info-right">');  
                    if (!empty($stock_img_data)) {
                        echo('<div class="well-nopad bg-dark nav-right" style="margin:20px;padding:0px;width:max-content">
                        <div class="nav-row">');
                        for ($i=0; $i < count($stock_img_data); $i++) {
                            $ii = $i+1;
                            if ($i == 0) {
                                echo('
                                <div class=" thumb bg-dark-m" style="width:235px;height:235px" onclick="modalLoad(this.children[0])">
                                    <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:235px" alt="'.$stock_name.' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" />
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
                            <button id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px">
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
                <h2 style="font-size:22px">Transactions (currently fake data)</h2>
                <table class="table table-dark centertable" id="transactions">
                    <thead>
                        <tr>
                            <th hidden>id</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>User</th>
                            <th>Quantity</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td hidden>1</td>
                            <td>Add</td>
                            <td>2023-04-07</td>
                            <td>DF3 Store</td>
                            <td>andrewr</td>
                            <td>6</td>
                            <td>Sold to customer</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            ');
        }
    }

    ?>


    <script> // MODAL SCRIPT
        // Get the modal
        function modalLoad(element) {
            var modal = document.getElementById("modalDiv");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementById(element);
            var modalImg = document.getElementById("modalImg");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = element.src;
            captionText.innerHTML = element.alt;

            
            
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalClose = function() { 
            var modal = document.getElementById("modalDiv");
            modal.style.display = "none";
        }
    </script>
    <script> // site selection <select> page navigation (area one below)
        function siteChange(element) {
            var selectElement = document.getElementById(element);
            var newSiteValue = selectElement.value;

            if (newSiteValue) {
                var updatedUrl = updateQueryParameter('', 'site', newSiteValue);
                updatedUrl = updateQueryParameter(updatedUrl, 'area', '0');
                window.location.href = updatedUrl;
            }
        }
        function areaChange(element) {
            var selectElement = document.getElementById(element);
            var newAreaValue = selectElement.value;

            if (newAreaValue) {
                var updatedUrl = updateQueryParameter('', 'area', newAreaValue);
                window.location.href = updatedUrl;
            }
        }
    </script>
    <script>
        function updateQueryParameter(url, query, newQueryValue) {
            // Get the current URL
            if (url === '') {
                var currentUrl = window.location.href;
            } else {
                var currentUrl = url;
            }
            
            // Get the index of the "?" character in the URL
            var queryStringIndex = currentUrl.indexOf('?');

            // If there is no "?" character in the URL, return the URL with the new $query query parameter value
            if (queryStringIndex === -1) {
                return currentUrl + '?' + query + '=' + newQueryValue;
            }

            // Get the query string portion of the URL
            var queryString = currentUrl.slice(queryStringIndex + 1);

            // Split the query string into an array of key-value pairs
            var queryParams = queryString.split('&');

            // Create a new array to hold the updated query parameters
            var updatedQueryParams = [];

            // Loop through the query parameters and update the query parameter if it exists
            for (var i = 0; i < queryParams.length; i++) {
                var keyValue = queryParams[i].split('=');
                if (keyValue[0] === query) {
                updatedQueryParams.push(query + '=' + newQueryValue);
                } else {
                updatedQueryParams.push(queryParams[i]);
                }
            }

            // If the query parameter does not exist, add it to the array of query parameters
            if (updatedQueryParams.indexOf(query + '=' + newQueryValue) === -1) {
                updatedQueryParams.push(query + '=' + newQueryValue);
            }

            // Join the updated query parameters into a string and append them to the original URL
            var updatedQueryString = updatedQueryParams.join('&');
            return currentUrl.slice(0, queryStringIndex + 1) + updatedQueryString;
        }
    </script>
    <script>
        function sortTable(n, header) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("inventoryTable");
        switching = true;
        //Set the sorting direction to ascending:
        dir = "asc";
        /*Make a loop that will continue until no switching has been done:*/
        while (switching) {
            //start by saying: no switching is done:
            switching = false;
            rows = table.rows;
            /*Loop through all table rows (except the first, which contains table headers):*/
            for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare, one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /*check if the two rows should switch place, based on the direction, asc or desc:*/
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                //if so, mark as a switch and break the loop:
                shouldSwitch = true;
                break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                //if so, mark as a switch and break the loop:
                shouldSwitch = true;
                break;
                }
            }
            }
            if (shouldSwitch) {
            /*If a switch has been marked, make the switch and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            //Each time a switch is done, increase this count by 1:
            switchcount++;
            } else {
            /*If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
            }
        }

        // update the header class to indicate sorting direction and show arrow
        var headers = document.getElementsByTagName("th");
        for (var i = 0; i < headers.length; i++) {
            headers[i].classList.remove("sorting-asc", "sorting-desc");
        }
        header.classList.add("sorting-" + dir);
        }
    </script>
    <script>
        function navPage(url) {
            window.location.href = url;
        }
    </script>
</body>