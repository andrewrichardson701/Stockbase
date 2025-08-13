function modalLoadContainerMoveConfirmation(i, itemID) {
    var modal = document.getElementById("modalDivContainerMoveConfirmation");
    modal.style.display = "block";
    // Do some AJAX here to get the contents of the container and add it to the table
    // containerMoveContentsTableBody

    var countText = document.getElementById('moveContainerChildCount');
    var tableBody = document.getElementById('containerMoveContentsTableBody');
    var containerMoveItemID = document.getElementById('containerMoveItemID');
    var containerMoveShelf = document.getElementById('containerMoveShelf');
    var containerMoveQuantity = document.getElementById('containerMoveQuantity');
    var containerMoveCurrentStock = document.getElementById('containerMoveCurrentStock');
    var containerMoveCurrentShelf = document.getElementById('containerMoveCurrentShelf');
    var containerMoveCurrentSerial = document.getElementById('containerMoveCurrentSerial');
    var containerMoveCurrentManufacturer = document.getElementById('containerMoveCurrentManufacturer');
    var containerMoveCurrentComments = document.getElementById('containerMoveCurrentComments');
    var containerMoveCurrentCost = document.getElementById('containerMoveCurrentCost');
    var containerMoveCurrentUPC = document.getElementById('containerMoveCurrentUPC');

    var shelfSelect = document.getElementById(i+'-n-shelf');
    var quantityInput = document.getElementById(i+'-n-quantity');

    var moveContainerItemName = document.getElementById('moveContainerItemName');
    var moveContainerItemID = document.getElementById('moveContainerItemID');

    containerMoveItemID.value=itemID;
    containerMoveShelf.value=shelfSelect.value;
    containerMoveQuantity.value=quantityInput.value;

    moveContainerItemID.innerHTML = itemID;
    moveContainerItemName.innerHTML = document.getElementById('stock_name').innerHTML;
    containerMoveCurrentStock.value = document.getElementById(i+'-c-stock').value;
    containerMoveCurrentShelf.value = document.getElementById(i+'-c-shelf').value;
    containerMoveCurrentSerial.value = document.getElementById(i+'-c-serial').value;
    containerMoveCurrentManufacturer.value = document.getElementById(i+'-c-manufacturer').value;
    containerMoveCurrentComments.value = document.getElementById(i+'-c-comments').value;
    containerMoveCurrentCost.value = document.getElementById(i+'-c-cost').value;
    containerMoveCurrentUPC.value = document.getElementById(i+'-c-upc').value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stockajax.php?request-container-children=1&container_id="+itemID+"&container_is_item=1", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var data = JSON.parse(xhr.responseText);
            // console.log(data);
            var bodyExtras = '';
            var count = data['count'];
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
            }
            // console.log(trs);
            tableBody.innerHTML=trs;
            // console.log(trs);
        }
    };
    xhr.send();
}

function modalCloseContainerMoveConfirmation() { 
    var modal = document.getElementById("modalDivContainerMoveConfirmation");
    modal.style.display = "none";
    // Empty the table here too.
    // containerMoveContentsTableBody
}

// for the select boxes
function populateAreas(id) {
    console.log(id);
    // Get the selected site
    var site = document.getElementById(id+"-n-site").value;

    // Make an AJAX request to retrieve the corresponding areas
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?site=" + site, true);
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

function populateShelves(id) {
    // Get the selected area
    var area = document.getElementById(id+"-n-area").value;

    // Make an AJAX request to retrieve the corresponding shelves
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?area=" + area, true);
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

// toggle hidden row below current
    function toggleHidden(id) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-edit';
    var hiddenRow = document.getElementById(hiddenID);
    var allRows = document.getElementsByClassName('row-show');
    var allHiddenRows = document.getElementsByClassName('row-hide');
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else {
        for(var i = 0; i < allHiddenRows.length; i++) {
            allHiddenRows[i].hidden=true;
        } 
        for (var j = 0; j < allRows.length; j++) {
            allRows[j].classList.remove('theme-th-selected');
        }     
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
    }
}

// function to force the quantity input box to 1 with a max of 1 if a serial number is selected
function serialInputCheck(id) {
    var selectBox = document.getElementById(id+'-n-serial')
    var inputBox = document.getElementById(id+'-n-quantity');
    var currentValue = inputBox.value;
    // console.log(currentValue);
    var currentMaxQuantity = document.getElementById(id+'-c-quantity').value;

    if (selectBox.value !== '') {
        inputBox.value = '1';
        inputBox.setAttribute('max', '1');
    } else {
        inputBox.value = currentValue;
        inputBox.setAttribute('max', currentMaxQuantity);
    }
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
