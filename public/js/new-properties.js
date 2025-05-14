function hideProps() {
    properties = document.getElementsByClassName("property");
    for (i = 0; i < properties.length; i++) {
        properties[i].hidden=true;
    }
}
hideProps();

function modalLoadProperties(property) {
    hideProps();
    //get the modal div with the property
    var modal = document.getElementById("modalDivProperties");
    var div = document.getElementById("property-"+property);
    modal.style.display = "block";
    div.hidden=false;
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseProperties = function() { 
    var modal = document.getElementById("modalDivProperties");
    modal.style.display = "none";
    hideProps();
}

function addProperty(property) {
    if (property !== '') {
        var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';
        var description = document.getElementById(property+'_description') !== null ? document.getElementById(property+'_description').value : '';
        var site_id = document.getElementById(property+'_site_id') !== null ? document.getElementById(property+'_site_id').value : '';
        var area_id = document.getElementById(property+'_area_id') !== null  ? document.getElementById(property+'_area_id').value : '';
        if (property == 'shelf' && document.getElementById('area-properties') !== null) {
            var area_id = document.getElementById('area-properties').value;
        }
        
        $.ajax({
            type: "POST",
            url: "/_ajax-addProperty",
            data: {
                type: property,
                property_name: name,
                description: description,
                site_id: site_id,
                area_id: area_id,
                submit: '1'
            },
            dataType: "html",
            success: function(response) {
                console.log('added');
                console.log(response);
                modalCloseProperties();
                if (property == 'area') {
                    populateAreas();
                }  
                if (property == 'shelf') {
                    populateAreas();
                }  
                if (property !== 'area' && property !== 'shelf') {
                    if (typeof loadProperty === "function") {
                        loadProperty(property);
                    } else {
                        location.reload()
                    }
                }  
                
            },
            async: true
        });
    }
}

function populateAreasProperties() {
// Get the selected site
var site = document.getElementById("site-properties").value;

// Make an AJAX request to retrieve the corresponding areas
var xhr = new XMLHttpRequest();
xhr.open("GET", "_ajax-selectBoxes?site=" + site, true);
xhr.onload = function() {
if (xhr.status === 200) {
  // Parse the response and populate the area select box
  var areas = JSON.parse(xhr.responseText);
  var select = document.getElementById("area-properties");
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

document.getElementById("site-properties").addEventListener("change", populateAreasProperties);