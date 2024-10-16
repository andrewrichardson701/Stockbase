// Lazy preloading
document.addEventListener("DOMContentLoaded", function() {
    var fonts = [
        "https://use.fontawesome.com/releases/v6.4.0/webfonts/fa-regular-400.woff2",
        "https://use.fontawesome.com/releases/v6.4.0/webfonts/fa-regular-400.ttf",
        "https://use.fontawesome.com/releases/v6.4.0/webfonts/fa-solid-900.woff2",
        "https://use.fontawesome.com/releases/v6.4.0/webfonts/fa-brands-400.woff2",
        "https://fonts.gstatic.com/s/poppins/v21/pxiEyp8kv8JHgFVrJJfecg.woff2"
    ];

    fonts.forEach(function(font) {
        var link = document.createElement("link");
        link.rel = "preload";
        link.href = font;
        link.as = "font";
        link.crossOrigin = "anonymous";
        document.head.appendChild(link);
    });
});

// color-picker box json - for Admin.php
$("input.color").each(function() {
    var that = this;
    $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
        that.type = (that.type == "color") ? "text" : "color";
    }));
}).change(function() {
    $(this).attr("data-value", this.value);
    this.type = "text";
});

// MODAL SCRIPT
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

// site selection <select> page navigation (area one below)
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

// update query string
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

// table sorting - mostly unused
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

// page navigation function to make life easier
function navPage(url) {
    window.location.href = url;
}

var shiftWindow = function() { scrollBy(0, -100) };
if (location.hash) shiftWindow();
window.addEventListener("hashchange", shiftWindow);