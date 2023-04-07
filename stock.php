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
    $show_inventory = 0; // for nav.php to show the site and area on the banner
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $stock_id = $_GET['id'];
        }
    }
    ?>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    
    <!-- Get Inventory -->
    <?php
    include 'includes/dbh.inc.php';
    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock
                FROM stock
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
            while ( $row = $result_stock->fetch_assoc() ) {
                $stock_name = $row['stock_name'];
                $stock_description = $row['stock_description'];
                $stock_sku = $row['stock_sku'];
                $stock_min_stock = $row['stock_min_stock'];
            }
            
            echo('
                <div class="container" style="padding-bottom:25px">
                    <h2 class="header-small" style="padding-bottom:10px">Stock</h2>
                    <button id="add-stock" class="btn btn-info cw nav-v-b" onclick="navPage(\''.$_SERVER['HTTP_REFERER'].'\');">
                        <i class="fa fa-arrow-left"></i> back 
                    </button>
                    <h3 style="font-size:22px;margin-top:20px" id="stock-name">'.$stock_name.'</h3>
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
                    area.name AS area_name,
                    shelf.id AS shelf_id, shelf.name AS shelf_name,
                    site.id AS site_id, site.name AS site_name, site.description AS site_description,
                    SUM(item.quantity) AS item_quantity
                FROM stock
                INNER JOIN item ON stock.id=item.stock_id
                INNER JOIN shelf ON item.shelf_id=shelf.id 
                INNER JOIN area ON shelf.area_id=area.id 
                INNER JOIN site ON area.site_id=site.id
                WHERE stock.id=?
                GROUP BY stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                site_id, site_name, site_description, area_name, shelf_id, shelf_name
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

            
            
            // Inventory Rows
            echo ('
            <div class="container well-nopad bg-dark">
                <div class="row">
                    <div class="col-sm text-left">
                        <p id="locations-head"><strong>Locations</strong></p>
                        <p id="locations">***location*** : ***count***</p>
                        <p id="labels-head"><strong>Labels</strong></p>
                        <p id="labels">***label***</p>
                        <p id="sku-head"><strong>SKU</strong></p>
                        <p id="sku">***SKU***</p>
                    </div>
                    <div class="col-sm text-center">
                        MIDDLE
                    </div>
                    <div class="col-sm text-right">
                        <img class="thumb" style="width:250px" src="assets/img/Logo.png" alt="Logo.png" onclick="modalLoad(this)">
                        RIGHT
                    </div>
                </div>

            ');
            while ( $row = $result_stock->fetch_assoc() ) {
                //TEMP DATA
                $img_directory = "assets/img/"; //not correct.
                $stock_id = "1";
                $stock_img_file_name = "Logo";
                $stock_img_file_type = ".png";
                $stock_name = "temp stock name";
                $stock_sku = "TEMP-SKU-001";
                $stock_quantity_total = 69;
                $stock_location = "DF3 Store, Store 50";

                echo $stock_id = $row['stock_id'];
                echo' -- ';
                // $stock_img_file_name =
                // $stock_img_file_type
                echo $stock_name = $row['stock_name'];
                echo' -- ';
                echo $stock_sku = $row['stock_sku'];
                echo' -- ';
                echo $stock_quantity_total = $row['item_quantity'];
                echo' -- ';
                echo $stock_location = $row['area_name'];
                echo' -- ';
                echo $stock_site_id = $row['site_id'];
                echo' -- ';
                echo $stock_site_name = $row['site_name'];
                echo'<br>';
                

                // Echo each row (inside of SQL results)

            }
            echo('
            </div>
            <div class="container well-nopad bg-dark" style="margin-top:5px">
                <h2 style="font-size:22px">Transactions</h2>
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