function toggleAddDiv() {
    var div = document.getElementById('add-cpu-section');
    var addButton = document.getElementById('add-cpu');
    var addButtonHide = document.getElementById('add-cpu-hide');
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

function modalLoadDeleteCpu(id) {
    console.log(id);
    var modal = document.getElementById("modalDivDeleteCpu");
    var serial = document.getElementById('cpu-serial-'+id).innerHTML;

    var deleteInputID = document.getElementById('delete-id');
    var deleteHeadingSerial = document.getElementById('delete-cpu-serial');


    deleteHeadingSerial.innerText = serial+" (ID: "+id+")";
    deleteInputID.value = id;
    modal.style.display = "block";


}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseDeleteCpu = function() { 
    var modal = document.getElementById("modalDivDeleteCpu");
    modal.style.display = "none";
}


// MODAL SCRIPT
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
function modalLoadNewModel() {
    //get the modal div with the property
    var modal = document.getElementById("modalDivNewModel");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseNewModel = function() { 
    var modal = document.getElementById("modalDivNewModel");
    modal.style.display = "none";
}


function modalLoadEditCpu(id) {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    // get the cpu info
    $.ajax({
        
        type: "POST",
        url: "/_ajax-getCpuInfo",
        data: {
            id: id,
            _token: csrf,
            submit: '1'
        },
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (response['error'] !== undefined) {
                alert('Error loading cpu info - try refreshing the page');
                return;
            } 
            if (!response['id']) {
                alert('Cpu not found - try refreshing the page');
                return;
            }         

            var modal = document.getElementById("modalDivEditCpu");

            var model_id = response['model_id'];
            var serial = response['serial_number'];
            var vendor_id = response['vendor_id'];
            var site_id = response['site_id'];
            var area_id = response['area_id'];
            var shelf_id = response['shelf_id'];

            var editInputModel = document.getElementById('model_cpu_edit');
            var editInputSerial = document.getElementById('serial_cpu_edit');
            var editInputVendor = document.getElementById('vendor_cpu_edit');
            var editInputSite = document.getElementById('site_cpu_edit');
            var editInputArea = document.getElementById('area_cpu_edit');
            var editInputShelf = document.getElementById('shelf_cpu_edit');

            editInputSerial.value = serial;
            setSelectValue(editInputVendor, vendor_id);
            setSelectValue(editInputModel, model_id);
            setSelectValue(editInputSite, site_id);
            populateAreasEdit(); // populate areas based on site selection before setting area value
            setSelectValue(editInputArea, area_id);
            populateShelvesEdit(); // populate shelves based on area selection before setting shelf value
            setSelectValue(editInputShelf, shelf_id);

            var editInputID = document.getElementById('edit-id');
            var editHeadingSerial = document.getElementById('edit-cpu-serial');


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
modalCloseEditCpu = function() { 
    var modal = document.getElementById("modalDivEditCpu");
    modal.style.display = "none";
}


function addCpuProperty(property) {
    if (property !== '') {
        var cpu_property = property;
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';

        if (property === 'cpu_model') {
            var socket = document.getElementById('socket').value;
            var cpu_family = document.getElementById('cpu_family').value;
            var core_count = document.getElementById('core_count').value;
            var clock_speed = document.getElementById('clock_speed').value;
        } else {
            var socket = null;
            var cpu_family = null;
            var core_count = null;
            var clock_speed = null;
        }
        
        $.ajax({
            type: "POST",
            url: "/_ajax-addProperty",
            data: {
                _token: csrf,
                type: cpu_property,
                property_name: name,
                socket: socket,
                cpu_family: cpu_family,
                core_count: core_count,
                clock_speed: clock_speed,
                submit: '1'
            },
            dataType: "html",
            success: function(response) {
                console.log(response);
                modalCloseNewVendor();
                modalCloseNewModel();
                modalCloseDeleteCpu();
                // modalCloseMoveCpu();

                location.reload()
                
            },
            async: true
        });
    }
}

function searchSerial(search) {

    var responseBox = document.getElementById('cpu-add-response');
    var btnAddSingle = document.getElementById('cpu-add-single');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    responseBox.hidden = true;
    btnAddSingle.disabled = false;

    if (search !== null && search !== '') {
        $.ajax({
            type: "POST",
            url: "/assets/cpu.serialSearch",
            data: {
                "request-cpu": 1,
                "serial": search,
                _token: csrf
            },
            dataType: "json",
            success: function(data) {
                console.log(data);
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
  var site = document.getElementById("site-add_cpu").value;
  
  // Make an AJAX request to retrieve the corresponding areas
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?site=" + site, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the area select box
      var areas = JSON.parse(xhr.responseText);
      var select = document.getElementById("area-add_cpu");
      var selectValue = select.value; // Store the current value of the select box
      select.options.length = 0;
      select.options[0] = new Option("Select Area", "");
      select.options[0].hidden = true;
      select.options[0].disabled = true;
      for (var i = 0; i < areas.length; i++) {
        var isSelected = (areas[i].id == selectValue);
        select.options[select.options.length] = new Option(areas[i].name, areas[i].id, false, isSelected);
      }
      select.disabled = (select.options.length === 1);
    }
  };
  xhr.send();
}
function populateShelves() {
  // Get the selected area
  var area = document.getElementById("area-add_cpu").value;

  // Make an AJAX request to retrieve the corresponding shelves
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?area=" + area, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the shelf select box
      var shelves = JSON.parse(xhr.responseText);
      var select = document.getElementById("shelf-add_cpu");
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
  var site = document.getElementById("site_cpu_edit").value;
  
  // Make an AJAX request to retrieve the corresponding areas
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?site=" + site, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the area select box
      var areas = JSON.parse(xhr.responseText);
      var select = document.getElementById("area_cpu_edit");
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
  var area = document.getElementById("area_cpu_edit").value;

  // Make an AJAX request to retrieve the corresponding shelves
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "/_ajax-selectBoxes?area=" + area, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      // Parse the response and populate the shelf select box
      var shelves = JSON.parse(xhr.responseText);
      var select = document.getElementById("shelf_cpu_edit");
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


if (document.getElementById("site-add_cpu")) {
    document.getElementById("site-add_cpu").addEventListener("change", populateAreas);
}
if (document.getElementById("area-add_cpu")) {
    document.getElementById("area-add_cpu").addEventListener("change", populateShelves);
}

if (document.getElementById("site_cpu_edit")) {
    document.getElementById("site_cpu_edit").addEventListener("change", populateAreasEdit);
}
if (document.getElementById("area_cpu_edit")) {
    document.getElementById("area_cpu_edit").addEventListener("change", populateShelvesEdit);
}
