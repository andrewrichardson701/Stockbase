 // toggle hidden row below current
function toggleHidden(id) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-comments';
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
function toggleAddComment(id, com) {
    var Row = document.getElementById('item-'+id);
    var hiddenID = 'item-'+id+'-add-comments';
    var hiddenRow = document.getElementById(hiddenID);
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
    } else { 
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
    }
    if (com > 0) {
        toggleHidden(id);
    }
}


function toggleAddDiv() {
    var div = document.getElementById('add-optic-section');
    var addButton = document.getElementById('add-optic');
    var addButtonHide = document.getElementById('add-optic-hide');
    var serial = document.getElementById('serial');
    if (div.hidden === true) {
        div.hidden = false;
        addButton.hidden = true;
        addButtonHide.hidden = false;
        serial.focus(); // for James to use barcode reader - selects the serial number box immediately.
    } else {
        div.hidden = true;
        addButton.hidden = false;
        addButtonHide.hidden = true;
    }

}
function toggleAddDivSmall() {
    var div = document.getElementById('add-optic-section');
    var addButton = document.getElementById('add-optic-small');
    var addButtonHide = document.getElementById('add-optic-hide-small');
    var serial = document.getElementById('serial');
    if (div.hidden === true) {
        div.hidden = false;
        addButton.hidden = true;
        addButtonHide.hidden = false;
        serial.focus(); // for barcode readers - selects the serial number box immediately.
    } else {
        div.hidden = true;
        addButton.hidden = false;
        addButtonHide.hidden = true;
    }

}


function modalLoadDeleteOptic(id) {
    console.log(id);
    var modal = document.getElementById("modalDivDeleteOptic");
    var serial = document.getElementById('optic-serial-'+id).innerHTML;

    var deleteInputID = document.getElementById('delete-id');
    var deleteHeadingSerial = document.getElementById('delete-optic-serial');


    deleteHeadingSerial.innerText = serial+" (ID: "+id+")";
    deleteInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseDeleteOptic = function() { 
    var modal = document.getElementById("modalDivDeleteOptic");
    modal.style.display = "none";
}

function modalLoadMoveOptic(id) {
    console.log(id);
    var modal = document.getElementById("modalDivMoveOptic");
    var serial = document.getElementById('optic-serial-'+id).innerHTML;

    var moveInputID = document.getElementById('move-id');
    var moveHeadingSerial = document.getElementById('move-optic-serial');


    moveHeadingSerial.innerText = serial+" (ID: "+id+")";
    moveInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseMoveOptic = function() { 
    var modal = document.getElementById("modalDivMoveOptic");
    modal.style.display = "none";
}

// MODAL SCRIPT
// Get the modal
function modalLoadNewType() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewType");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewType = function() { 
    var modal = document.getElementById("modalDivNewType");
    modal.style.display = "none";
}

function modalLoadNewVendor() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewVendor");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewVendor = function() { 
    var modal = document.getElementById("modalDivNewVendor");
    modal.style.display = "none";
}

function modalLoadNewSpeed() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewSpeed");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewSpeed = function() { 
    var modal = document.getElementById("modalDivNewSpeed");
    modal.style.display = "none";
}

function modalLoadNewConnector() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewConnector");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewConnector = function() { 
    var modal = document.getElementById("modalDivNewConnector");
    modal.style.display = "none";
}

function modalLoadNewDistance() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewDistance");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewDistance = function() { 
    var modal = document.getElementById("modalDivNewDistance");
    modal.style.display = "none";
}

function addOpticProperty(property) {
    if (property !== '') {
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';
        
        $.ajax({
            type: "POST",
            url: "/_ajax-addProperty",
            data: {
                _token: csrf,
                type: property,
                property_name: name,
                submit: '1'
            },
            dataType: "html",
            success: function(response) {
                console.log(response);
                modalCloseNewType();
                modalCloseNewVendor();
                modalCloseNewSpeed();
                modalCloseNewConnector();
                modalCloseNewDistance();
                if (typeof loadOpticProperty === "function") {
                    loadOpticProperty(property);
                } else {
                    location.reload()
                }

                
            },
            async: true
        });
    }
}

function loadOpticProperty(property) {
    var select = document.getElementById(property+'-select');
    var upperProperty = property[0].toUpperCase() + property.substring(1);
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    $.ajax({
        type: "POST",
        url: "/_ajax-loadProperty",
        data: {
            load_property: '1',
            type: property,
            submit: '1',
            _token: csrf
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

function searchSerial(search) {
    // Make an AJAX request to retrieve the corresponding sites
    var serialBox = document.getElementById('serial');
    var modelBox = document.getElementById('model');
    var vendorBox = document.getElementById('vendor');
    var typeBox = document.getElementById('type');
    var speedBox = document.getElementById('speed');
    var connectorBox = document.getElementById('connector');
    var distanceBox = document.getElementById('distance');
    var modeBox = document.getElementById('mode');
    var siteBox = document.getElementById('site');
    var responseBox = document.getElementById('optic-add-response');
    var btnAddSingle = document.getElementById('optic-add-single');
    var btnAddMultiple = document.getElementById('optic-add-multiple');
    
    responseBox.hidden = true;
    btnAddSingle.disabled = false;
    btnAddMultiple.disabled = false;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/optics.inc.php?request-optic=1&serial="+search, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var data = JSON.parse(xhr.responseText);
            console.log(data);
            if (data["skip"] === undefined) {
                // console.log('noskip');
                if (data["error"] === undefined) {
                    // console.log('noerror');
                    if (data["success"] !== undefined) {
                        // console.log('success');
                        serialBox.value = data['serial_number'];
                        modelBox.value = data['model'];
                        vendorBox.value = data['vendor_id'];
                        typeBox.value = data['type_id'];
                        speedBox.value = data['speed_id'];
                        connectorBox.value = data['connector_id'];
                        distanceBox.value = data['distance_id'];
                        modeBox.value = data['mode'];
                        siteBox.value = data['site_id'];
                        responseBox.hidden = false;
                        responseBox.innerHTML = "<or class='green'>"+data['success']+"</or>";
                        btnAddSingle.disabled = false;
                        btnAddMultiple.disabled = false;
                    }
                } else {
                    // console.log("error");
                    responseBox.hidden = false;
                    responseBox.innerHTML = "<or class='red'>"+data['error']+"</or>";
                    serialBox.value = data['serial_number'];
                    modelBox.value = data['model'];
                    vendorBox.value = data['vendor_id'];
                    typeBox.value = data['type_id'];
                    speedBox.value = data['speed_id'];
                    connectorBox.value = data['connector_id'];
                    distanceBox.value = data['distance_id'];
                    modeBox.value = data['mode'];
                    siteBox.value = data['site_id'];
                    btnAddSingle.disabled = true;
                    btnAddMultiple.disabled = true;
                    
                }
            }
        }
    };
    xhr.send();
}
