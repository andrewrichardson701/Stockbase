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

// ##########

    function toggleAddDiv() {
        var div = document.getElementById('add-cables-section');
        var addButton = document.getElementById('add-cables');
        var addButtonHide = document.getElementById('add-cables-hide');
        var addButtonSmall = document.getElementById('add-cables-small');
        var addButtonHideSmall = document.getElementById('add-cables-hide-small');

        if (div.hidden === true) {
            div.hidden = false;
            addButton.hidden = true;
            addButtonHide.hidden = false;
            addButtonSmall.hidden = true;
            addButtonHideSmall.hidden = false;
        } else {
            div.hidden = true;
            addButton.hidden = false;
            addButtonHide.hidden = true;
            addButtonSmall.hidden = false;
            addButtonHideSmall.hidden = true;
        }

    }

// ##########

function modalLoadNewType() {
    var modal = document.getElementById("modalDivNewType");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewType = function() { 
    var modal = document.getElementById("modalDivNewType");
    modal.style.display = "none";
}

// ########## 

// for the select boxes
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

function toggleHidden(id) {
    var Row = document.getElementById(id);
    var hiddenID = id+'-move-hidden';
    var hiddenRow = document.getElementById(hiddenID);
    var allRows = document.getElementsByClassName('row-show');
    var allHiddenRows = document.getElementsByClassName('move-hide');
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else {
        for(var i = 0; i < allHiddenRows.length; i++) {
            allHiddenRows[i].hidden=true;
            allHiddenRows[i].classList.remove('theme-th-selected');
        }  
        for (var j = 0; j < allRows.length; j++) {
            allRows[j].classList.remove('theme-th-selected');
        }   
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
    }
}

function populateAreasMove(id) {
    // console.log(id);
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
function populateShelvesMove(id) {
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

// ##########