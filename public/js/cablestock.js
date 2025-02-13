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
    var site = document.getElementById("site-dropdown").value;

    // Make an AJAX request to retrieve the corresponding areas
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "_ajax-selectBoxes?site=" + site, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
        // Parse the response and populate the area select box
        var areas = JSON.parse(xhr.responseText);
        var select = document.getElementById("area-dropdown");
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
    var area = document.getElementById("area-dropdown").value;

    // Make an AJAX request to retrieve the corresponding shelves
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "_ajax-selectBoxes?area=" + area, true);
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
document.getElementById("site-dropdown").addEventListener("change", populateAreas);
// document.getElementById("area-dropdown").addEventListener("change", populateShelves);

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
    xhr.open("GET", "_ajax-selectBoxes?site=" + site, true);
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
    xhr.open("GET", "_ajax-selectBoxes?area=" + area, true);
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

function getInventory(search) {
    // Make an AJAX request to retrieve the corresponding sites
    var invBody = document.getElementById('inv-body');
    var pageNumberArea = document.getElementById('inv-page-numbers');
    var sql = document.getElementById('hidden-sql');
    var oos = document.getElementById('hidden-oos').value;
    var site = document.getElementById('site-dropdown').value;
    var name = document.getElementById('search-input-name').value;
    var page = document.getElementById('hidden-page-number').value;
    var rows = document.getElementById('hidden-row-count').value;
    var type = document.getElementById('search-input-type').value;
    var cable = document.getElementById('hidden-cabletype').value;

    //console.log("_ajax-stockCables?request-cables=1&oos="+oos+"&site="+site+"&area="+area+"&name="+name+"&sku="+sku+"&shelf="+shelf+"&manufacturer="+manufacturer+"&tag="+tag+"&rows="+rows+"&page="+page);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "_ajax-stockCables?request-cables=1&oos="+oos+"&site="+site+"&name="+name+"&rows="+rows+"&page="+page+"&type="+type+"&cable="+cable, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var inventory = JSON.parse(xhr.responseText);
            // console.log(inventory);
            var bodyExtras = '';
            var count = inventory[-1]['rows'];
            var siteNeeded = inventory[-1]['siteNeeded'];
            var siteHeading = document.getElementById('site');
            // console.log(siteNeeded);
            if (siteNeeded == 0 || siteNeeded == '0') {
                siteHeading.hidden = true;
            } else {
                siteHeading.hidden = false;
            }

            for (let i=0; i<count; i++) {
                if (inventory[i]) {
                    var extras = bodyExtras+inventory[i];
                    bodyExtras = extras;
                }
            }
            invBody.innerHTML = bodyExtras;
            pageNumberArea.innerHTML = inventory[-1]['page-number-area'];
            sql.innerText = inventory[-1]['sql'];

            document.getElementById('rows-'+rows).selected=true; // show the row count as selected
            if (search == 1) {
                var newURL = inventory[-1]['url'];
                window.history.pushState({ path: newURL }, '', newURL);
            }
        }
    };
    xhr.send();
}