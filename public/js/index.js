// get the invetory list using AJAX
function getInventory(search) {
    // Make an AJAX request to retrieve the corresponding sites
    var invBody = document.getElementById('inv-body');
    var pageNumberArea = document.getElementById('inv-page-numbers');
    var sql = document.getElementById('hidden-sql');
    // console.log(invBody);
    var oos = document.getElementById('hidden-oos').value;
    var site = document.getElementById('site-dropdown').value;
    var area = document.getElementById('area-dropdown').value;
    var name = document.getElementById('search-input-name').value;
    var sku = document.getElementById('search-input-sku').value;
    var shelf = document.getElementById('search-input-shelf').value;
    var manufacturer = document.getElementById('search-input-manufacturer').value;
    var tagSelect = document.getElementById('search-input-tag');
    var tag = document.getElementById('search-input-tag').value;
    var page = document.getElementById('hidden-page-number').value;
    var rows = document.getElementById('hidden-row-count').value;

    var areaSelect = document.getElementById('area-dropdown');

    if (tag == "tags") {
        url = window.location.pathname + window.location.search
        tagSelect.options[0].selected=true;
        getInventory(1); // run again to reset the filter if tags is stored.
        // console.log(url);
        window.location.href = './tags';
    }

    //console.log("_ajax-stock?request-inventory=1&oos="+oos+"&site="+site+"&area="+area+"&name="+name+"&sku="+sku+"&shelf="+shelf+"&manufacturer="+manufacturer+"&tag="+tag+"&rows="+rows+"&page="+page);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "_ajax-stock?request-inventory=1&oos="+oos+"&site="+site+"&area="+area+"&name="+name+"&sku="+sku+"&shelf="+shelf+"&manufacturer="+manufacturer+"&tag="+tag+"&rows="+rows+"&page="+page, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the response and populate the shelf select box
            var inventory = JSON.parse(xhr.responseText);
            // console.log(inventory);
            var bodyExtras = '';
            var count = inventory[-1]['rows'];
            var siteNeeded = inventory[-1]['siteNeeded'];
            var siteHeading = document.getElementById('site');
            // console.log(siteNeeded);
            if (siteNeeded == 0 || siteNeeded == '0') {
                siteHeading.hidden = true;
            } else {
                siteHeading.hidden = false;
            }

            for (let i=0; i<count; i++) {
                if (inventory[i]) {
                    var extras = bodyExtras+inventory[i];
                    bodyExtras = extras;
                }
            }
            invBody.innerHTML = bodyExtras;
            pageNumberArea.innerHTML = inventory[-1]['page-number-area'];
            sql.innerText = inventory[-1]['sql'];
            
            var areas = inventory[-1]['areas'];
            // console.log(areas);

            areaSelect.options.length = 0;
            areaSelect.options[0] = new Option('All', 0); 
            areaSelect.options[0].selected = true;
            for (var j = 0; j < areas.length; j++) {
                areaSelect.options[j+1] = new Option(areas[j].name, areas[j].id);
               
                if (areas[j].id == inventory[-1]['area']) {
                    areaSelect.options[j+1].selected = true;
                }
            }
            document.getElementById('rows-'+rows).selected=true; // show the row count as selected
            if (search == 1) {
                var newURL = inventory[-1]['url'];
                window.history.pushState({ path: newURL }, '', newURL);
            }
        }
    };
    xhr.send();
}