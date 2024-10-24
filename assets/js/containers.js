function toggleEditcontainer(id) {
    var row = document.getElementById('container-'+id);
    var rowEdit = document.getElementById('container-'+id+'-edit');
    if (rowEdit.hidden == true) {
        row.hidden=true;
        rowEdit.hidden=false;
    } else {
        row.hidden=false;
        rowEdit.hidden=true;
    }
}
function toggleHiddencontainer(id) {
    var Row = document.getElementById('container-'+id);
    var RowEdit = document.getElementById('container-'+id+'-edit');
    var button = document.getElementById('container-'+id+'-toggle');
    var buttonEdit = document.getElementById('container-'+id+'-edit-toggle');
    var hiddenID = 'container-'+id+'-objects';
    var hiddenRow = document.getElementById(hiddenID);
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
        button.innerText='+';
        if (RowEdit) {
            RowEdit.classList.remove('theme-th-selected');
        }
        if (buttonEdit) {
            buttonEdit.innerText='+';
        }
    } else {
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
        button.innerText='-';
        if (RowEdit) {
            RowEdit.classList.add('theme-th-selected');
        }
        if (buttonEdit) { 
            buttonEdit.innerText='-';
        }
    }
}

function modalLoadAddContainer() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivAddContainer");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
function modalCloseAddContainer() { 
    var modal = document.getElementById("modalDivAddContainer");
    modal.style.display = "none";
}

function populateAreasContainers() {
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

function populateShelvesContainers() {
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

if (document.getElementById("site")) {
    document.getElementById("site").addEventListener("change", populateAreasContainers);
}
if (document.getElementById("area")) {
    document.getElementById("area").addEventListener("change", populateShelvesContainers);
}


function modalLoadAddChildren(container_id, is_item) {
    var modal = document.getElementById("modalDivAddChildren");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
    var contID = document.getElementById('contID');
    var contName = document.getElementById('contName');
    var formContId = document.getElementById('addChildrenContID');
    var formIsItem = document.getElementById('addChildrenIsItem');
    formIsItem.value = is_item;
    formContId.value = container_id;
    contID.innerHTML = container_id;
    contName.innerHTML = document.getElementById('container-'+container_id+'-name').innerHTML;
    var tableBody = document.getElementById('addChildrenTableBody');
    // do the ajax to fill the table
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stockajax.php?request-nearby-stock=1&item_id="+container_id+"&is_item="+is_item, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var data = JSON.parse(xhr.responseText);
            // console.log(inventory);
            var bodyExtras = '';
            var totalCount = data['count'];
            var trs = '';
            var tr = '';
            if (totalCount > 0) {
                for (let i=0; i<totalCount; i++) {
                    if (data['data'][i]) {
                        if (data['data'][i]['item_id'] == null) {
                            var id = '--';
                        } else {
                            var id = data['data'][i]['item_id'];
                        }
                        tr = "<tr class='clickable' onclick=\"addChildrenClicked(this, '"+data['data'][i]['stock_id']+"', '"+id+"')\"> \
                                <td class='text-center align-middle'>"+data['data'][i]['stock_id']+"</td> \
                                <td class='text-center align-middle'>"+data['data'][i]['stock_name']+"</td> \
                                <td class='text-center align-middle'>"+data['data'][i]['item_serial_number']+"</td> \
                                <td class='text-center align-middle'>"+data['data'][i]['quantity']+"</td> \
                                <td class='text-center align-middle'>"+id+"</td> \
                            </tr>";
                        trs = trs+tr;
                    }
                }
            }
            tableBody.innerHTML=trs;
            // console.log(trs);
        }
    };
    xhr.send();
}
function addChildrenClicked(row, stockID, itemID) {
    var linkButton = document.getElementById('submit-button-addChildren');
    var formStockID = document.getElementById('addChildrenStockID');
    var formItemID = document.getElementById('addChildrenItemID');
    var tableBody = document.getElementById('addChildrenTableBody');
    if (tableBody) {
        var tableRows = tableBody.querySelectorAll('tr');
        // Iterate over each <tr> element and remove the class 'theme-th-selected'
        tableRows.forEach(row => {
            row.classList.remove('theme-th-selected');
        });
    }
    row.classList.add("theme-th-selected");
    linkButton.disabled = false;
    formStockID.value = stockID;
    if (itemID !== '--') {
        formItemID.value = itemID;
    } else {
        formItemID.value = '';
    }
}
function modalCloseAddChildren(itemID) { 
    var modal = document.getElementById("modalDivAddChildren");
    modal.style.display = "none";
}

function modalLoadUnlinkContainer(containerID, itemID, is_item) {

    if (is_item == 1) {
        var item = 'item';
    } else {
        var item = '';
    }
    var modal = document.getElementById("modalDivUnlinkContainer");

    var text_containerID = document.getElementById("unlink-container-id");
    var text_containerName = document.getElementById("unlink-container-name");
    var text_itemID = document.getElementById("unlink-container-item-id");
    var text_itemName = document.getElementById("unlink-container-item-name");

    var form_itemID = document.getElementById("form-unlink-container-item-id");

    modal.style.display = "block";


    var containerName = document.getElementById("container"+item+"-"+containerID+"-name").innerHTML;
    var itemName = document.getElementById("container"+item+"-"+containerID+"-item-"+itemID+"-name").innerHTML;

    form_itemID.value = itemID;
    text_containerID.innerText = containerID;
    text_containerName.innerText = containerName;
    text_itemID.innerText = itemID;
    text_itemName.innerHTML = itemName;
        
}

function modalCloseUnlinkContainer() { 
    var modal = document.getElementById("modalDivUnlinkContainer");
    modal.style.display = "none";
}