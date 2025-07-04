 // Toggle hide/show section
function toggleSection(element, section) {
    var div = document.getElementById(section);
    var icon = element.children[0];
    if (div.hidden == false) {
        div.hidden=true;
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
    } else {
        div.hidden=false;
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
    }
}

// #########

// Carousel
function modalCloseUnlinkContainer() { 
    var modal = document.getElementById("modalDivUnlinkContainer");
    modal.style.display = "none";
}


function modalLoadCarousel() {
    var modal = document.getElementById("modalDivCarousel");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
}

// #############

// for the modal unlinking contanier div
function modalLoadUnlinkContainer(containerID, itemID, inverse) {
    var modal = document.getElementById("modalDivUnlinkContainer");

    var text_containerID = document.getElementById("unlink-container-id");
    var text_containerName = document.getElementById("unlink-container-name");
    var text_itemID = document.getElementById("unlink-container-item-id");
    var text_itemName = document.getElementById("unlink-container-item-name");

    var form_itemID = document.getElementById("form-unlink-container-item-id");

    modal.style.display = "block";

    if (inverse == 1) {
        var itemName = document.getElementById("hiddenStockName").value;
        var containerName = document.getElementById("modalUnlinkContainerItemName-"+containerID).innerHTML;
        
        form_itemID.value = containerID;
        text_containerID.innerText = itemID;
        text_containerName.innerText = containerName;
        text_itemID.innerText = containerID;
        text_itemName.innerHTML = itemName;
    } else {
        var containerName = document.getElementById("modalUnlinkContainerName").value;
        var itemName = document.getElementById("modalUnlinkContainerItemName-"+itemID).innerHTML;

        form_itemID.value = itemID;
        text_containerID.innerText = containerID;
        text_containerName.innerText = containerName;
        text_itemID.innerText = itemID;
        text_itemName.innerHTML = itemName;
    }
        
}

// When the user clicks on <span> (x), close the modal or if they click the image.
function modalCloseCarousel() { 
    var modal = document.getElementById("modalDivCarousel");
    modal.style.display = "none";
}


// Container Link
function modalLoadContainerLink(itemID) {
    var modal = document.getElementById("modalDivContainerLink");
    modal.style.display = "block";
}

function modalCloseContainerLink(itemID) { 
    var modal = document.getElementById("modalDivContainerLink");
    modal.style.display = "none";
}

// Add children to container
function modalLoadAddChildren(itemID) {
    var modal = document.getElementById("modalDivAddChildren");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
    var contID = document.getElementById('contID');
    var contName = document.getElementById('contName');
    var formContId = document.getElementById('addChildrenContID');
    formContId.value = itemID;
    contID.innerHTML = itemID;
    contName.innerHTML = document.getElementById('stock-name').innerHTML;
    var tableBody = document.getElementById('addChildrenTableBody');
    // do the ajax to fill the table
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stockajax.php?request-nearby-stock=1&item_id="+itemID+"&is_item=1", true);
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

function addChildrenSearch(itemID, search) {
    var tableBody = document.getElementById('addChildrenTableBody');
    // do the ajax to fill the table
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/stockajax.php?request-nearby-stock=1&item_id="+itemID+"&is_item=1&name="+search, true);
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
                        tr = "<tr class='clickable'> \
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

// Container Link
function modalLoadLinkToContainer(itemID) {
    var modal = document.getElementById("modalDivLinkToContainer");
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    modal.style.display = "block";

    var tableBody = document.getElementById('containerSelectTableBody');
    var IDbox = document.getElementById('linkToContainerItemID');
    var NAMEbox = document.getElementById('linkToContainerItemName');
    var formItemID = document.getElementById('linkToContainerTableItemID');
    var stockName = document.getElementById('stock-name').innerHTML;

    NAMEbox.innerHTML = stockName;
    IDbox.innerHTML = itemID;
    formItemID.value = itemID;

    $.ajax({
        type: "POST",
        url: "/_ajax-nearbyContainers",
        data: {
            "request-nearby-containers": 1,
            "item_id": itemID,
            "_token": csrf
        },
        dataType: "json",
        success: function(data) {
            // console.log(data);
            var trs = '';

            var containerCount = data['containers']['count'];
            var item_containerCount = data['container_items']['count'];

            if (containerCount > 0) {
                for (let i = 0; i < containerCount; i++) {
                    var row = data['containers'][i];
                    if (row) {
                        trs += "<tr class='clickable linkTableRow' onclick='linkToContainerTableClick(this, " + row['id'] + ", 0)'> \
                                    <td class='text-center align-middle'>" + row['id'] + "</td> \
                                    <td class='text-center align-middle'>" + row['name'] + "</td> \
                                    <td class='text-center align-middle'>" + row['description'] + "</td> \
                                    <td class='text-center algin-middle red'>No</td>\
                                </tr>";
                    }
                }
            }

            if (item_containerCount > 0) {
                for (let i = 0; i < item_containerCount; i++) {
                    var row = data['container_items'][i];
                    if (row) {
                        trs += "<tr class='clickable linkTableRow' onclick='linkToContainerTableClick(this, " + row['id'] + ", 1)'> \
                                    <td class='text-center align-middle'>" + row['id'] + "</td> \
                                    <td class='text-center align-middle'>" + row['name'] + "</td> \
                                    <td class='text-center align-middle'>" + row['description'] + "</td> \
                                    <td class='text-center algin-middle green'>Yes</td>\
                                </tr>";
                    }
                }
            }

            if (data['count'] == 0) {
                trs += "<tr class='red'> \
                            <td colspan=100%>No containers found on this shelf.</td>\
                        </tr>";
            }
            // console.log(trs);
            tableBody.innerHTML = trs;
        },
        async: true
    });
}


// Container Link Table function
function linkToContainerTableClick(clicked, id, item) {
    var idInput = document.getElementById('linkToContainerTableID');
    var itemInput = document.getElementById('linkToContainerTableItem');
    var allRows = document.getElementsByClassName('linkTableRow');
    var button = document.getElementById('containerLink-submit-button');
    // check if id is number
    if (isNaN(id) == false && isNaN(item) == false) {
        idInput.value = id;
        itemInput.value = item;
    } else {
        // item is not a number
        console.log('linkToContainerTableClick Item or ID checker NaN');
    }
    for (var j = 0; j < allRows.length; j++) {
        allRows[j].classList.remove('theme-th-selected');
    }   
    clicked.classList.add('theme-th-selected');
    button.disabled=false;
}

function modalCloseLinkToContainer() { 
    var modal = document.getElementById("modalDivLinkToContainer");
    modal.style.display = "none";
}


// ##########

function toggleHiddenStock(id) {
    var Row = document.getElementById('item-'+id);
    var hiddenRow = document.getElementById('item-'+id+'-hidden');
    var allRows = document.getElementsByClassName('row-show');
    var allHiddenRows = document.getElementsByClassName('row-hide');
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else {
        for (var i = 0; i < allHiddenRows.length; i++) {
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

// ##########

// script to make the add new button in the manufacturer select box work
window.onload = function() { // set the data-prev value for checking the Add New option in the manufacturerid selects.
    const selectElements = document.querySelectorAll('.manufacturer-select');
    selectElements.forEach(selectElement => {
        selectElement.setAttribute('data-prev', selectElement.value);
        selectElement.addEventListener('change', checkAddNew);
    });
};
function checkAddNew(event) {
    const element = event.target;
    const prevValue = element.getAttribute('data-prev'); // This is set on page load 

    if (element.value == -1) {
        element.value = prevValue;
        modalLoadProperties('manufacturer');
        element.setAttribute('data-prev', element.value); // reset the data-prev value
    }
}