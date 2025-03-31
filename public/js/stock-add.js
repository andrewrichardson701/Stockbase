
function loadProperty(property) {
    var select = document.getElementById(property+'-select');
    var upperProperty = property[0].toUpperCase() + property.substring(1);
    $.ajax({
        type: "POST",
        url: "./includes/stock-new-properties.inc.php",
        data: {
            load_property: '1',
            type: property,
            submit: '1'
        },
        dataType: "json",
        success: function(response) {
            var rows = response;
            if (Array.isArray(rows)) {
                select.options.length = 0;
                select.options[0] = new Option('Select '+upperProperty, '');
                for (var j = 0; j < rows.length; j++) {
                    select.options[j+1] = new Option(rows[j].name, rows[j].id);
                }
                select.options[0].disaled = true;
                select.options[0].selected = true;
            } else {
                console.log('error - check loadProperty function');
            }
        },
        async: true
    });
}
if (document.getElementById('manufacturer-select')) {
    document.onload = loadProperty('manufacturer');
}
if (document.getElementById('tag-select')) {
    document.onload = loadProperty('tag');
}
    
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
      select.options[0].hidden = true;
      select.options[0].disabled = true;
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
      select.options[0].hidden = true;
      select.options[0].disabled = true;
      for (var i = 0; i < shelves.length; i++) {
        select.options[select.options.length] = new Option(shelves[i].name, shelves[i].id);
      }
      select.disabled = (select.options.length === 1);
    }
  };
  xhr.send();
}
function populateContainers() {
  // Get the selected area
  var shelf = document.getElementById("shelf").value;
  
  // Make an AJAX request to retrieve the corresponding constiners
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "includes/stock-selectboxes.inc.php?container-shelf=" + shelf, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the container select box
      var containers = JSON.parse(xhr.responseText);
      console.log(containers);
      console.log(containers['container']);
      var select = document.getElementById("container");
      select.options.length = 0;
      select.options[0] = new Option("Select Container", "");
      select.options[0].hidden = true;
      select.options[0].disabled = true;
      containersOnly = containers['container'];
      itemContainers = containers['item_container'];
      for (var i = 0; i < containersOnly.length; i++) {
        select.options[select.options.length] = new Option(containersOnly[i].name, containersOnly[i].id);
      }
      for (var i = 0; i < itemContainers.length; i++) {
        contID = itemContainers[i].id * -1;
        select.options[select.options.length] = new Option(itemContainers[i].name, contID);
      }
      select.disabled = (select.options.length === 1);
    }
  };
  xhr.send();
}
if (document.getElementById("site")) {
    document.getElementById("site").addEventListener("change", populateAreas);
}
if (document.getElementById("area")) {
    document.getElementById("area").addEventListener("change", populateShelves);
}
if (document.getElementById("shelf")) {
    document.getElementById("shelf").addEventListener("change", populateContainers);
}

function getInventory(search) {
    // Make an AJAX request to retrieve the corresponding sites
    var invBody = document.getElementById('inv-body');
    var pageNumberArea = document.getElementById('inv-page-numbers');
    var sql = document.getElementById('hidden-sql');
    // console.log(invBody);
    var name = document.getElementById('search').value;
    var page = document.getElementById('hidden-page-number').value;
    var type = document.getElementById('inv-action-type').value;

    var xhr = new XMLHttpRequest();
    // xhr.open("GET", "includes/stockajax.php?request-inventory-stock=1&name="+name+"&rows=10&page="+page+"&type="+type, true);
    xhr.open("GET", "/_ajax-stock?request-inventory=1&name="+name+"&rows=10&page="+page+"&type="+type+"&oos=-1", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var inventory = JSON.parse(xhr.responseText);
            // console.log(inventory);
            var bodyExtras = '';
            var count = inventory[-1]['rows'];

            for (let i=0; i<count; i++) {
                if (inventory[i]) {
                    var extras = bodyExtras+inventory[i];
                    bodyExtras = extras;
                }
            }
            
            invBody.innerHTML = bodyExtras;
            pageNumberArea.innerHTML = inventory[-1]['page-number-area'];
            sql.innerText = inventory[-1]['sql'];

            if (search == 1) {
                var newURL = inventory[-1]['url'];
                window.history.pushState({ path: newURL }, '', newURL);
            }
        }
    };
    xhr.send();
}

if (document.getElementById('inventoryTable')) {
    document.onload=getInventory(0);
}
