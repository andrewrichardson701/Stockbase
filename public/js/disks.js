function toggleAddDiv() {
    var div = document.getElementById('add-disk-section');
    var addButton = document.getElementById('add-disk');
    var addButtonHide = document.getElementById('add-disk-hide');
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

function modalLoadDeleteDisk(id) {
    console.log(id);
    var modal = document.getElementById("modalDivDeleteDisk");
    var serial = document.getElementById('disk-serial-'+id).innerHTML;

    var deleteInputID = document.getElementById('delete-id');
    var deleteHeadingSerial = document.getElementById('delete-disk-serial');


    deleteHeadingSerial.innerText = serial+" (ID: "+id+")";
    deleteInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseDeleteDisk = function() { 
    var modal = document.getElementById("modalDivDeleteDisk");
    modal.style.display = "none";
}

function modalLoadMoveDisk(id) {
    console.log(id);
    var modal = document.getElementById("modalDivMoveDisk");
    var serial = document.getElementById('disk-serial-'+id).innerHTML;

    var moveInputID = document.getElementById('move-id');
    var moveHeadingSerial = document.getElementById('move-disk-serial');


    moveHeadingSerial.innerText = serial+" (ID: "+id+")";
    moveInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseMoveDisk = function() { 
    var modal = document.getElementById("modalDivMoveDisk");
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

// Get the modal
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

// Get the modal
function modalLoadNewCapacity() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewCapacity");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewCapacity = function() { 
    var modal = document.getElementById("modalDivNewCapacity");
    modal.style.display = "none";
}

// Get the modal
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

// Get the modal
function modalLoadNewCaddy() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewCaddy");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewCaddy = function() { 
    var modal = document.getElementById("modalDivNewCaddy");
    modal.style.display = "none";
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








function addOpticProperty(property) {
    if (property !== '') {
        var optic_property = 'optic_'+property;
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';
        
        $.ajax({
            type: "POST",
            url: "/_ajax-addProperty",
            data: {
                _token: csrf,
                type: optic_property,
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
    var optic_property = 'optic_'+property;
    var select = document.getElementById(optic_property+'-select');
    var upperProperty = property[0].toUpperCase() + property.substring(1);
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    $.ajax({
        type: "POST",
        url: "/_ajax-loadProperty",
        data: {
            load_property: '1',
            type: optic_property,
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

    var responseBox = document.getElementById('disk-add-response');
    var btnAddSingle = document.getElementById('disk-add-single');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    responseBox.hidden = true;
    btnAddSingle.disabled = false;

    if (search !== null && search !== '') {
        $.ajax({
            type: "POST",
            url: "/assets/disks.serialSearch",
            data: {
                "request-disk": 1,
                "serial": search,
                _token: csrf
            },
            dataType: "json",
            success: function(data) {
                // console.log(data);
                if (data["skip"] === undefined) {
                    if (data["error"] === undefined) {
                        if (data["success"] !== undefined) {
                            responseBox.hidden = false;
                            responseBox.innerHTML = "<or class='green'>" + data['success'] + "</or>";
                            btnAddSingle.disabled = false;
                        }
                    } else {
                        responseBox.hidden = false;
                        responseBox.innerHTML = "<or class='red'>" + data['error'] + "</or>";
                        btnAddSingle.disabled = true;
                    }
                }
            },
            async: true
        });
    }
}
