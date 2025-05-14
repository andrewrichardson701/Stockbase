async function modalLoadContainerRemoveConfirmation() {
    var shelfSelect = document.getElementById('shelf');
    var shelfSelectedNum = shelfSelect.options.selectedIndex;
    var itemID = shelfSelect.options[shelfSelectedNum].title;
    console.log(itemID);

    var modal = document.getElementById("modalDivContainerRemoveConfirmation");
    modal.style.display = "block";
    // Do some AJAX here to get the contents of the container and add it to the table
    // containerRemoveContentsTableBody

    var countText = document.getElementById('removeContainerChildCount');
    var tableBody = document.getElementById('containerRemoveContentsTableBody');
    var containerRemoveItemID = document.getElementById('containerRemoveItemID');
    var containerRemoveShelf = document.getElementById('containerRemoveShelf');
    var containerRemoveQuantity = document.getElementById('containerRemoveQuantity');

    var shelfSelect = document.getElementById('shelf');
    var quantityInput = document.getElementById('quantity');

    var removeContainerStockName = document.getElementById('removeContainerStockName');
    var removeContainerItemID = document.getElementById('removeContainerItemID');

    containerRemoveItemID.value=itemID;
    containerRemoveShelf.value=shelfSelect.value;
    containerRemoveQuantity.value=quantityInput.value;

    removeContainerItemID.innerHTML = itemID;
    removeContainerStockName.innerHTML = document.getElementById('stock_name').innerHTML;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stockajax.php?request-container-children=1&container_id="+itemID+"&container_is_item=1", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var data = JSON.parse(xhr.responseText);
            var info = data['info'];
            // console.log(data);
            var bodyExtras = '';
            var count = data['count'];
            if (info == "No children found.") {
                count = 0;
            }
            countText.innerHTML = count;
            var trs = '';
            var tr = '';

            if (count > 0) {
                for (let i=0; i<count; i++) {
                    if (data[i]) {
                        tr =`<tr class='linkTableRow'> 
                                <td><img class='inv-img-main' src='assets/img/stock/`+data[i]['child_img_image']+`' alt='`+data[i]['child_stock_name']+`'></td> 
                                <td class='text-center align-middle'>`+data[i]['child_item_id']+`</td> 
                                <td class='text-center align-middle'>`+data[i]['child_stock_name']+`</td> 
                                <td class='text-center align-middle'><or class='title' title='`+data[i]['child_stock_description']+`'>`+data[i]['child_stock_description'].substring(0,30)+`</or></td> 
                                <td class='text-center align-middle'><button class='btn btn-danger' style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px"><i class="fa fa-unlink" ></i></button></td> 
                            </tr>`;
                        trs = trs+tr;
                    }
                }
            } else {
                trs = "<tr><td colspan=100%>No chilren found.</td></tr>";
            }
            // console.log(trs);
            tableBody.innerHTML=trs;
            // console.log(trs);
        }
    };
    xhr.send();
}

function modalCloseContainerRemoveConfirmation() { 
    var modal = document.getElementById("modalDivContainerRemoveConfirmation");
    modal.style.display = "none";
    // Empty the table here too.
    // containerRemoveContentsTableBody
}

function confirmAction(stock_name, stock_sku, url) {
    var confirmed = confirm('Are you sure you want to proceed? \nThis will remove ALL entries for '+stock_name+' ('+stock_sku+').');
    if (confirmed) {
        window.location.href = url;
    }
}

// populate shelves from manufacturer
async function populateRemoveShelves(elem) {
    var stock = document.getElementById('stock-id').value;
    var contButton = document.getElementById('removeContButton');
    var normalButton = document.getElementById('removeButton');

    manufacturer_id = elem.value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?getremoveshelves=1&stock="+stock+"&manufacturer="+manufacturer_id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var shelves = JSON.parse(xhr.responseText);
            // console.log(shelves);
            var select = document.getElementById('shelf');
            select.options.length = 0;
            if (shelves.length === 0) {
                select.options[0] = new Option("", "");
            }
            for (var i = 0; i < shelves.length; i++) {
                if (i == 0) {
                    var option = new Option(shelves[i].location, shelves[i].id, true, true);
                    if (shelves[i].item_id !== '') {
                        option.setAttribute('title', shelves[i].item_id);
                    }
                    select.options[select.options.length] = option;
                } else {
                    var option = new Option(shelves[i].location, shelves[i].id);
                    if (shelves[i].item_id !== '') {
                        option.setAttribute('title', shelves[i].item_id);
                    }
                    select.options[select.options.length] = option;
                }
                // select.options[select.options.length] = new Option(shelves[i].location, shelves[i].id);
            }
            select.disabled = (select.options.length === 0);
            var shelf = document.getElementById('shelf');
            // select.disabled = (select.options.length === 1);
            if (select.value < 0) {
                contButton.disabled=false;
                contButton.hidden=false;
                normalButton.hidden=true;
                normalButton.disabled=true;
            } else {
                contButton.disabled=true;
                contButton.hidden=true;
                normalButton.hidden=false;
                normalButton.disabled=false;
            }
            populateContainers(shelf);
        }
    };
    xhr.send();
}

// populate containers
async function populateContainers(elem) {
    var contButton = document.getElementById('removeContButton');
    var normalButton = document.getElementById('removeButton');
    var select = document.getElementById('shelf');
    if (select.value < 0) {
        contButton.disabled=false;
        contButton.hidden=false;
        normalButton.hidden=true;
        normalButton.disabled=true;
    } else {
        contButton.disabled=true;
        contButton.hidden=true;
        normalButton.hidden=false;
        normalButton.disabled=false;
    }
    var stock = document.getElementById('stock-id').value;
    var shelf_id = elem.value;
    var manu_id = document.getElementById('manufacturer').value;
    var xhr = new XMLHttpRequest();
    // console.log("_ajax-selectBoxes?getcontainers=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id);
    // console.log("includes/stock-selectboxes.inc.php?getcontainers=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id);
    xhr.open("GET", "/_ajax-selectBoxes?getcontainers=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var containers = JSON.parse(xhr.responseText);
            // console.log(containers);
            var select = document.getElementById('container');
            select.options.length = 0;
            if (containers.length === 0) {
                select.options[0] = new Option("", "0");
            }
            for (var i = 0; i < containers.length; i++) {
                if (i == 0) {
                    if (containers[i].ic_container_is_item == 0) {
                        select.options[select.options.length] = new Option(containers[i].c_name, containers[i].c_id, true, true);
                    } else {
                        select.options[select.options.length] = new Option(containers[i].s_name, containers[i].ic_container_id*-1, true, true);
                    }
                } else {
                    if (containers[i].ic_container_is_item == 0) {
                        select.options[select.options.length] = new Option(containers[i].c_name, containers[i].c_id);
                    } else {
                        select.options[select.options.length] = new Option(containers[i].s_name, containers[i].ic_container_id*-1);
                    }
                }
            }
            select.disabled = (select.options.length === 0);
            // select.disabled = (select.options.length === 1);
            var container = document.getElementById('container');
            populateSerials(container);
        }
    };
    xhr.send();
    
}

// populate serials
async function populateSerials(elem) {
    // console.log(elem);
    if (elem.value == null || elem.value == '' || elem.value == undefined) {
        elem.value = 0;
    }        
    var stock = document.getElementById('stock-id').value;
    var container = elem.value;
    var shelf_id = document.getElementById('shelf').value;
    var manu_id = document.getElementById('manufacturer').value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?getserials=1&stock="+stock+"&shelf="+shelf_id+"&manufacturer="+manu_id+"&container="+container, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var serials = JSON.parse(xhr.responseText);
            var select = document.getElementById('serial-number');
            select.options.length = 0;
            if (serials.length === 0) {
                select.options[0] = new Option("", "");
            }
            for (var i = 0; i < serials.length; i++) {
                if (i == 0) {
                    select.options[select.options.length] = new Option(serials[i].serial_number, serials[i].serial_number, true, true);
                } else {
                    select.options[select.options.length] = new Option(serials[i].serial_number, serials[i].serial_number);
                }
            }
            // select.disabled = (select.options.length === 1);
            getQuantity();
        }
    };
    xhr.send();
    
}

function getQuantity() {
    var stock = document.getElementById('stock-id').value;
    var manufacturer = document.getElementById('manufacturer').value;
    var shelf = document.getElementById('shelf').value;
    var serial = document.getElementById('serial-number').value;
    var container = document.getElementById('container').value;
    if (container == '') {
        container = 0;
        
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?getquantity=1&stock="+stock+"&shelf="+shelf+"&manufacturer="+manufacturer+"&serial="+serial+"&container="+container, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var quantityArr = JSON.parse(xhr.responseText);
            var quantity = document.getElementById('quantity');

            quantity.max = quantityArr[0]['quantity'];
            if (quantity.max > 0) {
                quantity.value = 1;
            } else {
                quantity.value = 0;
            }
            // console.log(quantity.max[0]);

            if (quantity.min === quantity.max) {
                quantity.disabled = true;
            } else {
                quantity.disabled = false;
            }
        }
    };
    xhr.send();
}
function getQuantityCable() {
    var stock = document.getElementById('stock-id').value;
    var shelf = document.getElementById('shelf').value;
    
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stock-selectboxes.inc.php?getquantitycable=1&stock="+stock+"&shelf="+shelf, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var quantityArr = JSON.parse(xhr.responseText);
            var quantity = document.getElementById('quantity');
            quantity.value = 1;
            quantity.max = quantityArr[0]['quantity'];
            // console.log(quantity.max[0]);

            if (quantity.min === quantity.max) {
                quantity.disabled = true;
            } else {
                quantity.disabled = false;
            }
        }
    };
    xhr.send();
}

// Script to populare the remove fields from clicking the remove button in the stock table.
document.onload=populateFields();
async function populateFields() {
    const queryParams = new URLSearchParams(window.location.search);
    for (const key of queryParams.keys()) {
        // console.log(key);
    }
    setTimeout(function () {
        if (queryParams.get('manufacturer')) {
            // console.log(queryParams.get('manufacturer'));
            var manufacturerValue = queryParams.get('manufacturer');
            var manufacturerSelect = document.getElementById('manufacturer');
            for (let i = 0; i < manufacturerSelect.options.length; i++) {
                const option = manufacturerSelect.options[i];
                // Check if the option's value matches the 'manufacturer' query string parameter
                if (option.value === manufacturerValue) {
                    // Set the 'selected' attribute if there is a match
                    option.selected = true;
                    break; // Exit the loop since we found the matching option
                }
            }
            populateRemoveShelves(manufacturerSelect);
            setTimeout(function () {
                if (queryParams.get('shelf') !== null) {
                    // console.log(queryParams.get('shelf'));
                    var shelfValue = queryParams.get('shelf');
                    var shelfSelect = document.getElementById('shelf');
                    for (let i = 0; i < shelfSelect.options.length; i++) {
                        const option = shelfSelect.options[i];
                        // Check if the option's value matches the 'shelf' query string parameter
                        if (option.value === shelfValue) {
                            // Set the 'selected' attribute if there is a match
                            option.selected = true;
                            break; // Exit the loop since we found the matching option
                        }
                    }
                    populateSerials(shelfSelect);
                    setTimeout(function () {
                        if (queryParams.get('serial') !== null) {
                            // console.log(queryParams.get('serial'));
                            var serialValue = queryParams.get('serial');
                            var serialSelect = document.getElementById('serial-number');
                            for (let i = 0; i < serialSelect.options.length; i++) {
                                const option = serialSelect.options[i];
                                // Check if the option's value matches the 'serial' query string parameter
                                if (option.value === serialValue) {
                                    // Set the 'selected' attribute if there is a match
                                    option.selected = true;
                                    break; // Exit the loop since we found the matching option
                                }
                            }
                            getQuantity();
                        }
                    }, 500);
                }
            }, 500);
        }
    }, 300);
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
    xhr.open("GET", "/_ajax-stock?request-inventory=1&name="+name+"&rows=10&page="+page+"&type="+type+"&oos=0", true);
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