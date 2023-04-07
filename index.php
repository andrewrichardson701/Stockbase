<?php 
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>Inventory</title>
</head>
<body>
    <?php // dependency PHP
    $show_inventory = 1; // for nav.php to show the site and area on the banner
    ?>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    
    <!-- Get Inventory -->
    <?php
    include 'includes/dbh.inc.php';
    // $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
    //             shelf.id AS shelf_id, shelf.name AS shelf_name, area.id AS area_id, area.name AS area_name, area.description AS area_description, 
    //             area.parent_id as area_parent_id, site.id AS site_id, site.name AS site_name, site.description AS site_description 
    //             FROM stock
    //             INNER JOIN shelf ON stock.shelf_id=shelf.id 
    //             INNER JOIN area ON shelf.area_id=area.id 
    //             INNER JOIN site ON area.site_id=site.id 
    //             WHERE site.id=?";
    $sql_inv = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
                site.id AS site_id, site.name AS site_name, site.description AS site_description,
                SUM(item.quantity) AS item_quantity
                FROM stock
                INNER JOIN item ON stock.id=item.stock_id
                INNER JOIN shelf ON item.shelf_id=shelf.id 
                INNER JOIN area ON shelf.area_id=area.id 
                INNER JOIN site ON area.site_id=site.id";
    $s = 0;
    $sql_inv_add = '';
    if ($site !== '0') { $sql_inv_add  .= " AND site.id=?"; $s++; 
        $value1 = $site; 
    } 
    if ($area !== '0') { $sql_inv_add  .= " AND area.id=?"; $s++; 
        if (!isset($value1)) {
            $value1 = $area;
        } else {
            $value2 = $area;
        }
    } 
    if ($name !== '') { $sql_inv_add  .= " AND stock.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $name;
        } elseif (!isset($value2)) {
            $value2 = $name;
        } else {
            $value3 = $name;
        }
    }
    if ($sku !== '') { $sql_inv_add  .= " AND stock.sku LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $sku;
        } elseif (!isset($value2)) {
            $value2 = $sku;
        } elseif (!isset($value3)) {
            $value3 = $sku;
        } else {
            $value4 = $sku;
        }
    }
    if ($location !== '') { $sql_inv_add  .= " AND area.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $location;
        } elseif (!isset($value2)) {
            $value2 = $location;
        } elseif (!isset($value3)) {
            $value3 = $location;
        } elseif (!isset($value4)) {
            $value4 = $location;
        } else {
            $value5 = $location;
        }
    }
    if ($shelf !== '') { $sql_inv_add  .= " AND shelf.name LIKE CONCAT('%', ?, '%')"; $s++; 
        if (!isset($value1)) {
            $value1 = $shelf;
        } elseif (!isset($value2)) {
            $value2 = $shelf;
        } elseif (!isset($value3)) {
            $value3 = $shelf;
        } elseif (!isset($value4)) {
            $value4 = $shelf;
        } elseif (!isset($value5)) {
            $value5 = $shelf;
        } else {
            $value6 = $shelf;
        }
    }
    if ($s !== 0) { $sql_inv .= $sql_inv_add; }
    // $sql_inv .= " ORDER BY stock.name;";
    $sql_inv .= " GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                site_id, site_name, site_description
                ORDER BY stock.name;";
    $stmt_inv = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
        echo("ERROR getting entries");
    } else {
        if ($area != 0) {
            $area_name = $area_names_array[$area];
        } else {
            $area_name = "All";
        }
        if ($s == 1) { mysqli_stmt_bind_param($stmt_inv, "s", $value1); }
        elseif ($s == 2) { mysqli_stmt_bind_param($stmt_inv, "ss", $value1, $value2); }
        elseif ($s == 3) { mysqli_stmt_bind_param($stmt_inv, "sss", $value1, $value2, $value3); }
        elseif ($s == 4) { mysqli_stmt_bind_param($stmt_inv, "ssss", $value1, $value2, $value3, $value4); }
        elseif ($s == 5) { mysqli_stmt_bind_param($stmt_inv, "sssss", $value1, $value2, $value3, $value4, $value5); }
        elseif ($s == 6) { mysqli_stmt_bind_param($stmt_inv, "ssssss", $value1, $value2, $value3, $value4, $value5, $value6); }
        mysqli_stmt_execute($stmt_inv);
        $result_inv = mysqli_stmt_get_result($stmt_inv);
        $rowCount_inv = $result_inv->num_rows;
        echo('
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:10px">Inventory');
                if ($site !== '0') { echo(' - '.$area_name);}
            echo('</h2>
            <p>Welcome, <or class="green">'.$profile_name.'</or>.</p>
            </div>

            <div class="container" id="search-fields" style="margin-bottom:20px">
                <div class="nav-row">
                    <form action="./" method="get" class="nav-row" style="max-width:max-content">
                        <input id="query-site" type="hidden" name="site" value="'.$site.'" /> 
                        <input id="query-area" type="hidden" name="area" value="'.$area.'" />
                        <span id="search-input-name-span" style="margin-right: 10px">
                            <label for="search-input-name">Name</label><br>
                            <input id="search-input-name" type="text" name="name" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Name" value="'); echo(isset($_GET['name']) ? $_GET['name'] : ''); echo('" />
                        </span>
                        <span id="search-input-sku-span" style="margin-right: 10px">
                            <label for="search-input-sku">SKU</label><br>
                            <input id="search-input-sku" type="text" name="sku" class="form-control" style="width:180px;display:inline-block" placeholder="Search by SKU" value="'); echo(isset($_GET['sku']) ? $_GET['sku'] : ''); echo('" />
                        </span>
                        <span id="search-input-location-span" style="margin-right: 10px">
                            <label for="search-input-location">Location</label><br>
                            <input id="search-input-location" type="text" name="location" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Location" value="'); echo(isset($_GET['location']) ? $_GET['location'] : ''); echo('" />
                        </span>
                        <span id="search-input-shelf-span" style="margin-right: 10px">
                            <label for="search-input-shelf">Shelf</label><br>
                            <input id="search-input-shelf" type="text" name="shelf" class="form-control" style="width:180px;display:inline-block" placeholder="Search by Shelf" value="'); echo(isset($_GET['shelf']) ? $_GET['shelf'] : ''); echo('" />
                        </span>
                        <input type="submit" value="submit" hidden>
                    </form>
                    <div id="add-div" class="nav-div" style="margin-left:0px;margin-right:5px">
                        <button id="add-stock" class="btn btn-success cw nav-v-b" style="width:110px">
                            <i class="fa fa-plus"></i> add 
                        </button>
                    </div> 
                    <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                        <button id="add-stock" class="btn btn-danger cw nav-v-b" style="width:110px">
                            <i class="fa fa-minus"></i> remove 
                        </button>
                    </div> 
                </div>
            </div>

        ');
        if ($rowCount_inv < 1) {
            echo ('<div class="container" id="no-inv-found">No Inventory Found</div>');
        } else {
            
            echo('
            <!-- Modal Image Div -->
            <div id="modalDiv" class="modal" onclick="modalClose()">
                <span class="close" onclick="modalClose()">&times;</span>
                <img class="modal-content bg-trans" id="modalImg">
                <div id="caption" class="modal-caption"></div>
            </div>
            <!-- End of Modal Image Div -->

            <!-- Table -->
            <div class="container">
                <table class="table table-dark centertable" id="inventoryTable">
                    <thead style="text-align: center; white-space: nowrap;">
                        <tr>
                            <th id="id" hidden>id</th>
                            <th id="img"</th>
                            <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(2, this)">Name</th>
                            <th class="clickable sorting" id="sku" onclick="sortTable(3, this)">SKU</th>
                            <th class="clickable sorting" id="quantity" onclick="sortTable(4, this)">Quantity</th>');
            if ($site == 0) { echo('<th class="clickable sorting" id="site" onclick="sortTable(5, this)">Site</th>'); }
                        echo('<th id="location">Location(s)</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle" style="text-align: center; white-space: nowrap;">
            ');
            // Inventory Rows
            while ( $row = $result_inv->fetch_assoc() ) {
                //TEMP DATA
                $img_directory = "assets/img/"; //not correct.
                $stock_id = "1";
                $stock_img_file_name = "Logo";
                $stock_img_file_type = ".png";
                $stock_name = "temp stock name";
                $stock_sku = "TEMP-SKU-001";
                $stock_quantity_total = 69;
                $stock_locations = "DF3 Store, Store 50";

                $stock_id = $row['stock_id'];
                // $stock_img_file_name =
                // $stock_img_file_type
                $stock_name = $row['stock_name'];
                $stock_sku = $row['stock_sku'];
                $stock_quantity_total = $row['item_quantity'];
                $stock_locations = $row['area_names'];
                $stock_site_id = $row['site_id'];
                $stock_site_name = $row['site_name'];
                

                // Echo each row (inside of SQL results)
                echo('
                            <tr class="vertical-align align-middle"id="'.$stock_id.'">
                                <td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>
                                <td class="align-middle" id="'.$stock_id.'-img-td"><img id="'.$stock_id.'-img" class="inv-img thumb" src="'.$img_directory.$stock_img_file_name.$stock_img_file_type.'" alt="'.$stock_name.'" onclick="modalLoad(this)" /></td>
                                <td class="align-middle link gold" id="'.$stock_id.'-name" onclick="navPage(\'./stock.php?id='.$stock_id.'\')">'.$stock_name.'</td>
                                <td class="align-middle" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                                <td class="align-middle" id="'.$stock_id.'-quantity">'.$stock_quantity_total.'</td>');
                if ($site == 0) { echo ('<td class="align-middle link gold" id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'); }
                            echo('<td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                            </tr>
                ');
            }

            // End table + body
            echo ('
                    </body>
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