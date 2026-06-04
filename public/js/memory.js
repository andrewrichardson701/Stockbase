function toggleAddDiv() {
    var div = document.getElementById('add-memory-section');
    var addButton = document.getElementById('add-memory');
    var addButtonHide = document.getElementById('add-memory-hide');
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

function modalLoadDeleteMemory(id) {
    console.log(id);
    var modal = document.getElementById("modalDivDeleteMemory");
    var serial = document.getElementById('memory-serial-'+id).innerHTML;

    var deleteInputID = document.getElementById('delete-id');
    var deleteHeadingSerial = document.getElementById('delete-memory-serial');


    deleteHeadingSerial.innerText = serial+" (ID: "+id+")";
    deleteInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseDeleteMemory = function() { 
    var modal = document.getElementById("modalDivDeleteMemory");
    modal.style.display = "none";
}


// MODAL SCRIPT
// Get the modal
function modalLoadNewEccType() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewEccType");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewEccType = function() { 
    var modal = document.getElementById("modalDivNewEccType");
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
function modalLoadNewGeneration() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewGeneration");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewGeneration = function() { 
    var modal = document.getElementById("modalDivNewGeneration");
    modal.style.display = "none";
}

// Get the modal
function modalLoadNewFormFactor() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewFormFactor");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewFormFactor = function() { 
    var modal = document.getElementById("modalDivNewFormFactor");
    modal.style.display = "none";
}

function modalLoadEditMemory(id) {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    // get the memory info
    $.ajax({
        
        type: "POST",
        url: "/_ajax-getMemoryInfo",
        data: {
            id: id,
            _token: csrf,
            submit: '1'
        },
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (response['error'] !== undefined) {
                alert('Error loading memory info - try refreshing the page');
                return;
            } 
            if (!response['id']) {
                alert('Memory not found - try refreshing the page');
                return;
            }         

            var modal = document.getElementById("modalDivEditMemory");

            var model = response['model'];
            var serial = response['serial_number'];
            var type_id = response['type_id'];
            var vendor_id = response['vendor_id'];
            var speed_id = response['speed_id'];
            var generation_id = response['generation_id'];
            var capacity_id = response['capacity_id'];
            var form_factor_id = response['form_factor_id'];
            var site_id = response['site_id'];
            var area_id = response['area_id'];
            var shelf_id = response['shelf_id'];

            var editInputModel = document.getElementById('model_memory_edit');
            var editInputType = document.getElementById('type_memory_edit');
            var editInputSerial = document.getElementById('serial_memory_edit');
            var editInputVendor = document.getElementById('vendor_memory_edit');
            var editInputSpeed = document.getElementById('speed_memory_edit');
            var editInputGeneration = document.getElementById('generation_memory_edit');
            var editInputCapacity = document.getElementById('capacity_memory_edit');
            var editInputFormFactor = document.getElementById('form_factor_memory_edit');
            var editInputSite = document.getElementById('site_memory_edit');
            var editInputArea = document.getElementById('area_memory_edit');
            var editInputShelf = document.getElementById('shelf_memory_edit');

            editInputModel.value = model;
            editInputSerial.value = serial;
            setSelectValue(editInputVendor, vendor_id);
            setSelectValue(editInputType, type_id);
            setSelectValue(editInputSpeed, speed_id);
            setSelectValue(editInputGeneration, generation_id);
            setSelectValue(editInputCapacity, capacity_id);
            setSelectValue(editInputFormFactor, form_factor_id);
            setSelectValue(editInputSite, site_id);
            setSelectValue(editInputArea, area_id);
            setSelectValue(editInputShelf, shelf_id);

            var editInputID = document.getElementById('edit-id');
            var editHeadingSerial = document.getElementById('edit-memory-serial');


            editHeadingSerial.innerText = serial+" (ID: "+id+")";
            editInputID.value = id;
            modal.style.display = "block";
        },
        async: true
    });
}

function setSelectValue(selectEl, value) {
    if (!selectEl) return;

    value = String(value); // ensure type match

    let found = false;

    for (let i = 0; i < selectEl.options.length; i++) {
        if (String(selectEl.options[i].value) === value) {
            selectEl.selectedIndex = i;
            found = true;
            break;
        }
    }

    if (!found) {
        console.warn("Value not found in select:", value, selectEl);
    }

    // trigger change (important for UI frameworks)
    $(selectEl).trigger('change');
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseEditMemory = function() { 
    var modal = document.getElementById("modalDivEditMemory");
    modal.style.display = "none";
}






function addMemoryProperty(property) {
    if (property !== '') {
        var memory_property = 'memory_'+property;
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';
        
        $.ajax({
            type: "POST",
            url: "/_ajax-addProperty",
            data: {
                _token: csrf,
                type: memory_property,
                property_name: name,
                submit: '1'
            },
            dataType: "html",
            success: function(response) {
                console.log(response);
                modalCloseNewEccType();
                modalCloseNewVendor();
                modalCloseNewSpeed();
                modalCloseNewCapacity();
                modalCloseNewGeneration();
                modalCloseDeleteMemory();
                modalCloseMoveMemory();
                modalCloseNewFormFactor();
                if (typeof loadMemoryProperty === "function") {
                    loadMemoryProperty(property);
                } else {
                    location.reload()
                }

                
            },
            async: true
        });
    }
}

function loadMemoryProperty(property) {
    var memory_property = 'memory_'+property;
    var select = document.getElementById(memory_property+'-select');
    var upperProperty = property[0].toUpperCase() + property.substring(1);
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    $.ajax({
        type: "POST",
        url: "/_ajax-loadProperty",
        data: {
            load_property: '1',
            type: memory_property,
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

    var responseBox = document.getElementById('memory-add-response');
    var btnAddSingle = document.getElementById('memory-add-single');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    responseBox.hidden = true;
    btnAddSingle.disabled = false;

    if (search !== null && search !== '') {
        $.ajax({
            type: "POST",
            url: "/assets/memory.serialSearch",
            data: {
                "request-memory": 1,
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



// for the select boxes
function populateAreas() {
  // Get the selected site
  var site = document.getElementById("site-add_memory").value;
  
  // Make an AJAX request to retrieve the corresponding areas
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?site=" + site, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the area select box
      var areas = JSON.parse(xhr.responseText);
      var select = document.getElementById("area-add_memory");
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
  var area = document.getElementById("area-add_memory").value;

  // Make an AJAX request to retrieve the corresponding shelves
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?area=" + area, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the shelf select box
      var shelves = JSON.parse(xhr.responseText);
      var select = document.getElementById("shelf-add_memory");
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


// for the select boxes
function populateAreasEdit() {
  // Get the selected site
  var site = document.getElementById("site_memory_edit").value;
  
  // Make an AJAX request to retrieve the corresponding areas
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?site=" + site, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the area select box
      var areas = JSON.parse(xhr.responseText);
      var select = document.getElementById("area_memory_edit");
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
function populateShelvesEdit() {
  // Get the selected area
  var area = document.getElementById("area_memory_edit").value;

  // Make an AJAX request to retrieve the corresponding shelves
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?area=" + area, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the shelf select box
      var shelves = JSON.parse(xhr.responseText);
      var select = document.getElementById("shelf_memory_edit");
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


if (document.getElementById("site-add_memory")) {
    document.getElementById("site-add_memory").addEventListener("change", populateAreas);
}
if (document.getElementById("area-add_memory")) {
    document.getElementById("area-add_memory").addEventListener("change", populateShelves);
}

if (document.getElementById("site_memory_edit")) {
    document.getElementById("site_memory_edit").addEventListener("change", populateAreasEdit);
}
if (document.getElementById("area_memory_edit")) {
    document.getElementById("area_memory_edit").addEventListener("change", populateShelvesEdit);
}
