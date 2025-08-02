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


function testLDAP() {
    var ldap_username = $('#auth-username').val();
    var ldap_password = $('#auth-password').val();
    var ldap_password_confirm = $('#auth-password-confirm').val();
    var ldap_domain = $('#auth-domain').val();
    var ldap_host = $('#auth-host').val();
    var ldap_host_secondary = $('#auth-host-secondary').val();
    var ldap_port = $('#auth-port').val();
    var ldap_basedn = $('#auth-basedn').val();
    var ldap_usergroup = $('#auth-usergroup').val();
    var ldap_userfilter = $('#auth-userfilter').val();

    var ldapForm = document.getElementById("ldapForm");
    var outputPre = document.getElementById("ldapTestOutput");
    if (outputPre !== null) {
        outputPre.parentNode.removeChild(outputPre)
    }
    var newOutputPre = document.createElement("pre");
    newOutputPre.setAttribute("class", "well-nopad theme-divBg");
    newOutputPre.setAttribute("id", "ldapTestOutput");
    newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
    ldapForm.parentNode.insertBefore(newOutputPre, ldapForm.nextSibling);

    $.ajax({
        type: "POST",
        url: "admin.ldapSettings",
        data: {ldap_username: ldap_username, 
            ldap_password: ldap_password, 
            ldap_password_confirm: ldap_password_confirm, 
            ldap_domain: ldap_domain,
            ldap_host: ldap_host,
            ldap_host_secondary: ldap_host_secondary,
            ldap_port: ldap_port,
            ldap_basedn: ldap_basedn,
            ldap_usergroup: ldap_usergroup,
            ldap_userfilter: ldap_userfilter
        },
        dataType: "json",
        success: function(response){
            var userlist = response;
            var div = document.getElementById('ldapTestOutput');
            // console.log(response);
            if (Array.isArray(userlist)) {
                for (var i = 0; i < userlist.length; i++) {
                var user = userlist[i];
                // console.log(user);
                div.textContent += user+"\n";
                }
            } else {
                div.textContent += userlist+"\n";
            } 
        },
        async: false // <- this turns it into synchronous
    });
}

function testSMTP() {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    var smtpLoading = document.getElementById("smtp-loading-icon");
    var smtpSuccess = document.getElementById("smtp-success-icon");
    var smtpFail = document.getElementById("smtp-fail-icon");
    smtpLoading.style.display = "inline-block";
    smtpSuccess.style.display = "none";
    smtpFail.style.display = "none";

    var smtp_host = $('#smtp-host').val();
    var smtp_port = $('#smtp-port').val();
    var smtp_encryption = $('#smtp-encryption').val();
    var smtp_username = $('#smtp-username').val();
    var smtp_password = $('#smtp-password').val();
    var smtp_from_email = $('#smtp-from-email').val();
    var smtp_from_name = $('#smtp-from-name').val();
    var smtp_to_email = $('#smtp-backup-to').val();

    var smtpForm = document.getElementById("smtpForm");
    var outputPre = document.getElementById("smtpTestOutput");
    if (outputPre !== null) {
        outputPre.parentNode.removeChild(outputPre)
    }
    var newOutputPre = document.createElement("pre");
    newOutputPre.setAttribute("class", "well-nopad theme-divBg");
    newOutputPre.setAttribute("id", "smtpTestOutput");
    newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
    smtpForm.parentNode.insertBefore(newOutputPre, smtpForm.nextSibling);

    $.ajax({
        type: "POST",
        url: "admin.smtpTest",
        data: {
            _token: csrf,
            smtp_host: smtp_host,
            smtp_port: smtp_port,
            smtp_encryption: smtp_encryption,
            smtp_username: smtp_username,
            smtp_password: smtp_password,
            smtp_from_email: smtp_from_email,
            smtp_from_name: smtp_from_name,
            smtp_to_email: smtp_to_email,
            smtp_to_name: smtp_to_email,
            notif_id: 1,
            debug: 1
        },
        dataType: "html",
        success: function(response) {
            var result = response;
            var div = document.getElementById('smtpTestOutput');

            div.textContent += result + "\n";

            // Continue with the rest of the code once the AJAX request is complete
            processLastLine();
            newOutputPre.scrollIntoView();
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error", status, error); // Logs "error", "Internal Server Error", etc.
            console.log("Status Code:", xhr.status);    // Logs 500
            console.log("Response Text:", xhr.responseText); // Laravel's error response

            var div = document.getElementById('smtpTestOutput');
            div.textContent += `Error (${xhr.status}): ${xhr.statusText}\n`;

            // Optional: show Laravel error message (usually HTML or JSON)
            if (xhr.responseText) {
                div.textContent += xhr.responseText + "\n";
            }

            smtpLoading.style.display = "none";
            smtpSuccess.style.display = "none";
            smtpFail.style.display = "inline";
            newOutputPre.scrollIntoView();
        },
        async: true
    });

    function processLastLine() {
        var div = document.getElementById('smtpTestOutput');

        // Get the content of the <pre> element
        var divContent = div.textContent || div.innerText;
        // Split the content into an array of lines
        var lines = divContent.trim().split('\n');
        // Get the last line
        var lastLine = lines[lines.length - 1];
        // Check if the last line starts with "221"
        if (lastLine.startsWith('221')) {
            // Show some text on the screen or perform any desired action
            console.log('SMTP success code 221 found');
            smtpLoading.style.display = "none";
            smtpSuccess.style.display = "inline";
            smtpFail.style.display = "none";
        } else {
            console.log('SMTP error');
            smtpLoading.style.display = "none";
            smtpSuccess.style.display = "none";
            smtpFail.style.display = "inline";
        }
    }
}
    
// color-picker box json - for Admin.php
$("input.color").each(function() {
    var that = this;
    $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
        that.type = (that.type == "color") ? "text" : "color";
    }));
}).change(function() {
    $(this).attr("data-value", this.value);
    this.type = "text";
});


// Function to extract the anchor and split it before the first hyphen
function extractParamsFromAnchor(anchor) {
    const params = anchor.split('-');
    return {
        param1: anchor,
        param2: params[0],
    };
}

// Check for anchors in the URL and call toggleSection function if present
window.onload = function () {
    const anchor = window.location.hash.substring(1); // Remove the leading '#'
    if (anchor) {
        const { param1, param2 } = extractParamsFromAnchor(anchor);
        // console.log(param1);
        // console.log(param2);
        toggleSection(document.getElementById(param1), param2);

        // Scroll to the anchor ID after the toggleSection function is done
        const anchorElement = document.getElementById(anchor);
        if (anchorElement) {
            anchorElement.scrollIntoView({ behavior: 'smooth' });
        }
    } else {
        toggleSection(document.getElementById("global-settings"), "global");

    }
};

// ##############

// LDAP TOGGLE ENABLE STUFF

// Get the initial state of the LDAP enable toggle checkbox
let isLdapCheckboxChecked = document.getElementById("ldap-enabled-toggle").checked;

// Add an event listener to the checkbox
document.getElementById("ldap-enabled-toggle").addEventListener("change", function (event) {
    // Check if the checkbox is being unchecked
    const isUncheck = !this.checked;

    // If the checkbox is being unchecked, display the confirmation popup
    if (isUncheck) {
        const confirmed = confirm(
            'Disabling LDAP will force local user login.\nMake sure you have a local user available.\nAre you sure you want to do this?'
        );

        // If the user cancels, revert the checkbox back to its previous state
        if (!confirmed) {
            this.checked = true; // Revert the checkbox back to checked state
            return;
        }
    }

    // Update the initial state of the checkbox for the next change event
    isLdapCheckboxChecked = this.checked;

    // If the checkbox is not being unchecked or the user confirmed, submit the form
    document.getElementById("ldapToggleForm").submit();
});

// ############

// SMTP TOGGLE ENABLE STUFF

// Get the initial state of the SMTP enable toggle checkbox
let isSmtpCheckboxChecked = document.getElementById("smtp-enabled-toggle").checked;

// Add an event listener to the checkbox
document.getElementById("smtp-enabled-toggle").addEventListener("change", function (event) {
    // Check if the checkbox is being unchecked
    const isUncheck = !this.checked;

    // If the checkbox is being unchecked, display the confirmation popup
    if (isUncheck) {
        const confirmed = confirm(
            'Disabling SMTP will stop ALL email notifications.\nAre you sure you want to do this?'
        );

        // If the user cancels, revert the checkbox back to its previous state
        if (!confirmed) {
            this.checked = true; // Revert the checkbox back to checked state
            return;
        }
    }

    // Update the initial state of the checkbox for the next change event
    isSmtpCheckboxChecked = this.checked;

    // If the checkbox is not being unchecked or the user confirmed, submit the form
    document.getElementById("smtpToggleForm").submit();
});

// ##########

function toggleFooter(checkbox, id) {
    var type = id;
    var value = checkbox.checked ? 1 : 0;
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    $.ajax({
        type: "POST",
        url: "/admin.toggleFooter",
        data: {
            _token: csrf,
            "footer-toggle": 1,
            type: type,
            value: value
        },
        dataType: "json",
        success: function(response) {
            var outputBox = document.getElementById('footer-output');
            outputBox.hidden = false;
            outputBox.classList = "last-edit-T";
            outputBox.innerHTML = response[0];
        },
        async: true
    });
}


// Cost toggle checkboxes
function toggleCost(checkbox, id) {
    var type = id;
    var value = checkbox.checked ? 1 : 0;
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    $.ajax({
        type: "POST",
        url: "/admin.stockManagementSettings",
        data: {
            "cost-toggle": 1,
            type: type,
            value: value,
            _token: csrf
        },
        dataType: "json",
        success: function(response) {
            var outputBox = document.getElementById('cost-output');
            outputBox.hidden = false;
            outputBox.classList = "last-edit-T";
            outputBox.innerHTML = response[0];
        },
        async: true
    });
}

// Mail notifications checkboxes
function mailNotification(checkbox, id) {
    var notification = id;
    var value = checkbox.checked ? 1 : 0;
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    $.ajax({
        type: "POST",
        url: "/admin.toggleNotification",
        data: {
            "mail-notification": 1,
            id: notification,
            value: value,
            _token: csrf
        },
        dataType: "json",
        success: function(response) {
            var outputBox = document.getElementById('notification-output');
            outputBox.hidden = false;
            outputBox.classList = "last-edit-T";
            outputBox.innerHTML = response[0];
        },
        async: true
    });
}

// auth setting checkboxes
function authSettings(checkbox, id) {
    var outputBox = document.getElementById('authentication-output');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    if (checkbox.checked) {
        // enable the auth setting
        var value = 1;
    } else {
        // disable the auth setting
        var value = 0;
    }
    $.ajax({
        type: "POST",
        url: "/admin.toggleAuth",
        data: {
            _token: csrf,
            auth_setting: 1,
            id: id,
            value: value
        },
        dataType: "json",
        success: function(response){
            // do something with redirect_url to put it on the page.
            if (response['status'] == 'true') {
                outputBox.hidden = false;
                outputBox.classList="last-edit-T";
                outputBox.innerHTML = response[0];
            }
        },
        error: function(response) {
            console.log(response);
        },
        async: true // <- this turns it into synchronous
    });
}

// ################

// MODAL SCRIPT
// Get the modal
function modalLoadAdd(site_id) {
    //get the modal div with the property
    var modal = document.getElementById("modalDivAdd");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseAdd = function() { 
    var modal = document.getElementById("modalDivAdd");
    modal.style.display = "none";
}


// Get the modal
function modalLoadEdit(id, type) {
    //get the modal div with the property
    var modal = document.getElementById("modalDivEdit");
    modal.style.display = "block";

    var input_parent_site = document.getElementById('location-parent-site-input');
    var input_parent_area = document.getElementById('location-parent-area-input');
    var input_parent_site_tr = document.getElementById('location-parent-site-tr');
    var input_parent_area_tr = document.getElementById('location-parent-area-tr');
    var input_parent_site_th = document.getElementById('location-parent-site-th');
    var input_parent_area_th = document.getElementById('location-parent-area-th');

    var input_type = document.getElementById('location-type-input');
    var text_type = document.getElementById('location-type-text');

    var input_id = document.getElementById('location-id-input');
    var text_id = document.getElementById('location-id-text');

    var input_name = document.getElementById('location-name-input');

    var input_description_tr = document.getElementById('location-description-tr');
    var input_description = document.getElementById('location-description-input');

    // input_parent.value = '';
    // input_parent_site.value = '';
    input_parent_area.options.length = 0;
    input_parent_site.options.length = 0;
    input_parent_area_tr.hidden=true;
    input_parent_site_tr.hidden=true;
    input_description_tr.hidden=true;
    
    if (type !== "site") {
        if (type == "area") {
            input_parent_site_tr.hidden=false;
            populateSites(input_parent_site, document.getElementById(type+'-'+id+'-parent').value);

        } 
        if (type == "shelf") {
            input_parent_area_tr.hidden=false;
            input_parent_site_tr.hidden=false;
            populateSites(input_parent_site, document.getElementById(type+'-'+id+'-site').value);
            populateAreas(input_parent_area, document.getElementById(type+'-'+id+'-site').value, document.getElementById(type+'-'+id+'-parent').value);
        }
    } 

    input_type.value = type;
    if (type.length > 0) {
        type_cap = type.charAt(0).toUpperCase() + type.slice(1);
    }
    text_type.textContent = type_cap;

    input_id.value = document.getElementById(type+'-'+id+'-id').value;
    text_id.textContent = document.getElementById(type+'-'+id+'-id').value;

    input_name.value = document.getElementById(type+'-'+id+'-name').value;

    if (type !== "shelf") {
        input_description_tr.hidden=false;
        input_description.value = document.getElementById(type+'-'+id+'-description').value;
    }
    
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseEdit = function() { 
    var modal = document.getElementById("modalDivEdit");
    modal.style.display = "none";

    var input_parent_site = document.getElementById('location-parent-site-input');
    var input_parent_area = document.getElementById('location-parent-area-input');
    var input_parent_site_tr = document.getElementById('location-parent-site-tr');
    var input_parent_area_tr = document.getElementById('location-parent-area-tr');
    var input_parent_site_th = document.getElementById('location-parent-site-th');
    var input_parent_area_th = document.getElementById('location-parent-area-th');

    var input_type = document.getElementById('location-type-input');
    var text_type = document.getElementById('location-type-text');

    var input_id = document.getElementById('location-id-input');
    var text_id = document.getElementById('location-id-text');

    var input_name = document.getElementById('location-name-input');

    var input_description_tr = document.getElementById('location-description-tr');
    var input_description = document.getElementById('location-description-input');

    input_parent_area.value = '';
    input_parent_site.value = '';
    input_parent_area.options.length = 0;
    input_parent_site.options.length = 0;
    input_parent_area_tr.hidden=true;
    input_parent_site_tr.hidden=true;

    input_type.value = '';
    text_type.textContent = '';

    input_id.value = '';
    text_id.textContent = '';

    input_name.value = '';

    input_description_tr.hidden=true;
    input_description.value = '';
}

function modalLoadAddPermPreset() {
    //get the modal div with the property
    var modal = document.getElementById("modalAddPermPreset");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseAddPermPreset = function() { 
    var modal = document.getElementById("modalAddPermPreset");
    modal.style.display = "none";
}

// Get the modal
function resetPassword(user_id) {
    var modal = document.getElementById("modalDivResetPW");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
    var user_id_element = document.getElementById('modal-user-id');
    user_id_element.value = user_id;
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseResetPW = function() { 
    var modal = document.getElementById("modalDivResetPW");
    modal.style.display = "none";
}

function modalLoadReset2FA(id) {
    var modal = document.getElementById("modalDivReset2FA");

    modal.style.display = "block";
    var user_id_element = document.getElementById('2fareset_user_id');
    var username_text_element = document.getElementById('2fareset_username');
    var username = document.getElementById('user_'+id+'_username').innerHTML;
    user_id_element.value = id;
    username_text_element.innerHTML = username;
}
function modalCloseReset2FA() { 
    var modal = document.getElementById("modalDivReset2FA");
    modal.style.display = "none";
    var user_id_element = document.getElementById('2fareset_user_id');
    var username_text_element = document.getElementById('2fareset_username');
    user_id_element.value = '';
    username_text_element.innerHTML = '';
}

// ###########

function toggleDeletedAttributes(type, show) {
    var identifier = type+'-deleted';
    var attributes = document.getElementsByClassName(identifier);
    var showbutton = document.getElementById('show-deleted-'+type);
    var hidebutton = document.getElementById('hide-deleted-'+type);
    console.log(attributes);
    for (i=0; i<attributes.length; i++) {
        var attribute = attributes[i]
        if (show == 1) {
            attribute.hidden = false
        } else {
            attribute.hidden = true;
        }
    }
    if (show == 1) {
        showbutton.hidden = true;
        hidebutton.hidden = false;
    } else {
        showbutton.hidden = false;
        hidebutton.hidden = true;
    }
}


// ################

// POPULATE Selects

function populateSites(field, current_site) {
    // Make an AJAX request to retrieve the corresponding sites

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?getsites=1&_=" + new Date().getTime(), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var sites = JSON.parse(xhr.responseText);
            var select = field;
            select.options.length = 0;
            select.options[0] = new Option("Select Site", "");
            select.options[0].disabled = true;
            for (var i = 0; i < sites.length; i++) {
                select.options[select.options.length] = new Option(sites[i].name, sites[i].id);
            }
            select.disabled = (select.options.length === 1);
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value === current_site) {
                    select.options[i].selected = true;
                }
            }
        }
    };
    xhr.send();
}
function populateAreas(field, current_site, current_area) {
    // Make an AJAX request to retrieve the corresponding areas

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/_ajax-selectBoxes?site=" + current_site + "&_=" + new Date().getTime(), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var areas = JSON.parse(xhr.responseText);
            var select = field;
            select.options.length = 0;
            select.options[0] = new Option("Select Area", "");
            select.options[0].disabled = true;
            for (var i = 0; i < areas.length; i++) {
                select.options[select.options.length] = new Option(areas[i].name, areas[i].id);
            }
            select.disabled = (select.options.length === 1);
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value === current_area) {
                    select.options[i].selected = true;
                }
            }
        }
    };
    xhr.send();
}
function populateAreasUpdate() {
    // Get the selected site
    var site = document.getElementById("location-parent-site-input").value;
    var type = document.getElementById("location-type-input").value;
    if (type === "shelf") {
        // Make an AJAX request to retrieve the corresponding areas
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/_ajax-selectBoxes?site=" + site + "&_=" + new Date().getTime(), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the area select box
                var areas = JSON.parse(xhr.responseText);
                var select = document.getElementById("location-parent-area-input");
                select.options.length = 0;
                select.options[0] = new Option("Select Area", "");
                select.options[0].disabled = true;
                for (var i = 0; i < areas.length; i++) {
                    select.options[select.options.length] = new Option(areas[i].name, areas[i].id);
                }
                select.disabled = (select.options.length === 1);
            }
        };
        xhr.send();
    }
}
document.getElementById("location-parent-site-input").addEventListener("change", populateAreasUpdate);

// ############


// show input for the ShowADD section
function showInput() {
    var type = document.getElementById("addLocation-type");
    var selectedType = type.options[type.selectedIndex].value;

    var inputContainers = document.getElementsByClassName("specialInput");
    for (var i = 0; i < inputContainers.length; i++) {
        inputContainers[i].hidden = true;
        inputContainers[i].value = '';
    }

    var modifyContainers = document.getElementsByClassName(selectedType);
    for (var i = 0; i < modifyContainers.length; i++) {
        modifyContainers[i].hidden = false;
    }
}

function populateParent() {
    // Get the selected type
    var type = document.getElementById("addLocation-type").value;
    if (type == 'shelf') {
        var search_type = 'area';
    } else if (type == 'area') {
        var search_type = 'site';
    } else {
        var search_type = null;
    }
    
    if (search_type !== null) {
        // Make an AJAX request to retrieve the corresponding parents
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/_ajax-selectBoxes?location_type=" + search_type + "&_=" + new Date().getTime(), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the area select box
                var select = document.getElementById("addLocation-parent");
                    select.options.length = 0;
                    select.options[0] = new Option("Select Parent", "");
                if (xhr.responseText !== '') {
                    var parents = JSON.parse(xhr.responseText);
                    for (var i = 0; i < parents.length; i++) {
                        select.options[select.options.length] = new Option(parents[i].name, parents[i].id);
                    }
                    
                } 
                select.disabled = (select.options.length === 1);
            }
            
        };
        xhr.send();
    }
}
document.getElementById("addLocation-type").addEventListener("change", populateParent);

function showLinks(type, num) {
    var button = document.getElementById(type+'-'+num+'-links');
    var linksRow = document.getElementById(type+'-row-'+num+'-links');

    if (linksRow.hidden === true) {
        button.className = "btn btn-dark";
        button.innerText = "Hide Links";
        linksRow.hidden = false;
    } else {
        button.className = "btn btn-warning";
        button.innerText = "Show Links";
        linksRow.hidden = true;
    }
}

// script to load the template email into page 
// function emailTemplate() {
//     var body = document.getElementById('email-template-body').value;
//     var emailDiv = document.getElementById('email-template');

//     // Make an AJAX request to retrieve cotnent
//     var xhr = new XMLHttpRequest();
//     xhr.open("GET", "/admin.smtpTemplate?template=echo&body="+body + "&_=" + new Date().getTime());
//     xhr.onload = function() {
//         if (xhr.status === 200) {
//             // Parse the response and populate the field
//             if (xhr.responseText !== '') {
//                 var email = xhr.responseText;
//                 emailDiv.innerHTML = email;
//             } else {
//                 emailDiv.innerHTML = '<or class="red">AJAX Results Empty...</or>';
//             }
//         } else {
//             emailDiv.innerHTML = '<or class="red">XMLHttpRequest Status = '+xhr.status+'. Expected: 200</or>';
//         }
//     };
//     xhr.send();
// }

document.onload = emailTemplate();


function viewLoaderDiv(type) {
    document.getElementById('loaderDiv').style.display = type;
}

function loadAdminImages(currentPage, pageNum) {
    var tbody = document.getElementById('image-management-tbody');
    var data = 1;
    var loaderRow = document.getElementById('loader-tr');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    viewLoaderDiv('block');
    
    $.ajax({
        type: "POST",
        url: "/admin.imageManagementSettings",
        data: {
            _token: csrf,
            request_stock_images: data,
            current_page: currentPage,
            page: pageNum
        },
        dataType: "json",
        success: function(response){
            var rows = response;
            //console.log(response);
            if (Array.isArray(rows)) {
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (i == 0) {
                        loaderRow.remove();
                    }
                    if (row == "ERROR" && i == 0) {
                        tbody.innerHTML += "<tr><td colspan=100%>Error getting images</td></tr>";
                        continue;
                    } else {
                        tbody.innerHTML += row;
                    }
                }
                // viewLoaderDiv('none');
            } else {
                tbody.innerHTML += "<tr><td colspan=100%>Error getting images</td></tr>";
            } 
        },
        error: function(response) {
            tbody.innerHTML += "<tr><td colspan=100%>Error getting images</td></tr>";
        },
        async: true // <- this turns it into synchronous
    });
}

function toggleUserPermissions(id, state) {
    var show_button = document.getElementById('user_'+id+'_permissions-show');
    var hide_button = document.getElementById('user_'+id+'_permissions-hide');
    var row = document.getElementById('user_'+id+'_row');
    var permissions_row = document.getElementById('user_'+id+'_permissions_row');

    if (state == 1) {
        row.classList.add('theme-th-selected');
        permissions_row.classList.add('theme-th-selected');
        show_button.hidden = true;
        hide_button.hidden = false;
        permissions_row.hidden = false;
    } else {
        row.classList.remove('theme-th-selected');
        permissions_row.classList.remove('theme-th-selected');
        show_button.hidden = false;
        hide_button.hidden = true;
        permissions_row.hidden = true;
    }

}

function permissionsPreset(id) {
    var select = document.getElementById('permission-preset_'+id);
    var preset_id = select.value;
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    $.ajax({
        type: "POST",
        url: "/admin.userSettings",
        data: {
            id: preset_id,
            user_permissions_preset_ajax: 1,
            _token: csrf
        },
        dataType: "json",
        success: function(response) {
            if (Array.isArray(response)) {
                response.forEach(function(item) {
                    var key = item.key;
                    var value = item.value;

                    var checkbox = document.getElementById('users_permissions-' + id + '-' + key + '-checkbox');
                    if (checkbox) {
                        checkbox.checked = value == 1;
                    }
                });
            } else {
                console.log("Unexpected response format:", response);
            }
        },
        async: true
    });

}

function usersEnabledChange(id) {
    var checkbox = document.getElementById("user_"+id+"_enabled_checkbox");
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    if (checkbox.checked == true) {
        var checkboxValue = 1;
    } else {
        var checkboxValue = 0;
    }

    $.ajax({
        type: "POST",
        url: "/admin.userSettings",
        data: {
            user_id: id,
            user_new_enabled: checkboxValue,
            user_enabled_submit: 'yes',
            _token: csrf
        },
        dataType: "html",
        success: function(response) {
            var tr = document.getElementById('users_table_info_tr');
            var td = document.getElementById('users_table_info_td');
            tr.hidden = false;
            var result = response;
            if (result.startsWith("Error:")) {
                td.classList.add("red");
            } else {
                td.classList.add("green");
            }
            td.textContent = result;
        },
        async: true
    });
}