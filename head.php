<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// PAGE HEADER SETUP - SETS UP CSS, BOOTSTRAP AND OTHER STYLES AND SCRIPTS
$versionNumber = 'v1.0.1';

include './includes/get-config.inc.php'; // get config options

// anti clickjacking defense
header("X-Frame-Options: DENY");
// Set a cookie with the Secure flag for defense against cookie attacks
setcookie("stockbase_cookie", bin2hex(random_bytes(32)), [ 'expires' => time() + 3600, 'path' => "/", 'domain' => $current_base_url, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' ]);
?>
<!-- CSP headers -->
<meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src 'self' https://ajax.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline';
    style-src 'self' https://stackpath.bootstrapcdn.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com 'unsafe-inline';
    font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com;
    img-src 'self' https://api.qrserver.com data:;
">
<meta charset="utf-8">
<meta name="theme-color" content="#ffffff">
<link rel="icon" type="image/png" href="./assets/img/config/<?php echo($current_favicon_image); ?>">

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
<link rel="stylesheet" href="./assets/css/main.css">
<link rel="stylesheet" href="./assets/css/inv.css">
<?php
if (isset($loggedin_theme_file_name) && $loggedin_theme_file_name !== '') {
    echo('<link id="theme-css" rel="stylesheet" href="./assets/css/'.$loggedin_theme_file_name.'">');
} elseif (isset($current_default_theme_file_name) && $current_default_theme_file_name !== ''){
    echo('<link id="theme-css" rel="stylesheet" href="./assets/css/'.$current_default_theme_file_name.'">');
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">

<style>
.inv-nav {
    background-color: <?php echo($current_banner_color);?> ;
    z-index:0px;
}
.inv-nav-secondary {
    background-color: <?php echo(adjustBrightness($current_banner_color, -0.2));?> ;
    z-index:0px;
}

</style>

<?
// HTTP Headers, from httpe-headers.php, now truncated to here.

// HEADERS FOR PROXY AND REQUESTED URL INFO
$requestedUrl = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? (isset(explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1]) ? explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1] : '') : '';
$requestedHttp = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ''; // IP of host server
$requestedPort = isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : '';
$requestedHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : '';
$requestedServer = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : '';
$remoteIP = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : ''; // IP of connector

$requestedUri = isset($_SERVER['HTTP_X_REQUEST_URI']) ? $_SERVER['HTTP_X_REQUEST_URI'] : '';

$serverendhttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryStringUrl = '?'.$queryString;
} else {
    $queryString = '';
    $queryStringUrl = '';
}

$fullRequestedURL = $requestedHttp.'://'.$requestedUrl.$requestedUri.$queryStringUrl;
$platform = $_SERVER["HTTP_USER_AGENT"];
?>

<script> // color-picker box json - for Admin.php
        $("input.color").each(function() {
            var that = this;
            $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
                that.type = (that.type == "color") ? "text" : "color";
            }));
        }).change(function() {
            $(this).attr("data-value", this.value);
            this.type = "text";
        });
</script>
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
