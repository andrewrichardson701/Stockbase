<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.


if (!empty($_POST)) {
    if (isset($_POST['submit'])) {
        if (isset($_POST['property_name'])) {
            if(session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            } 
            include 'changelog.inc.php';
            include 'dbh.inc.php';
            $redirect_url = str_replace('includes/', '', $_SESSION['redirect_url']);
            // print_r($_POST);        
            $name = isset($_POST['property_name']) ? $_POST['property_name'] : header("Location ../$redirect_url&error=nameEmpty"); // all
            $description = isset($_POST['description']) ? $_POST['description'] : ''; // site/area
            $site_id = isset($_POST['site_id']) ? $_POST['site_id'] : ''; // area
            $area_id = isset($_POST['area_id']) ? $_POST['area_id'] : ''; // shelf
            $type=$_POST['type'];

            $name = mysqli_real_escape_string($conn, $name); // escape the special characters
            $description = mysqli_real_escape_string($conn, $description); // escape the special characters

            switch ($type) {
                case 'tag':
                    $sqlCheck = "SELECT * FROM tag WHERE name='$name'";
                    $sql = "INSERT INTO tag (name, description) VALUES ('$name', '$description')";
                    break;
                case 'manufacturer':
                    $sqlCheck = "SELECT * FROM manufacturer WHERE name='$name'";
                    $sql = "INSERT INTO manufacturer (name) VALUES ('$name')";
                    break;
                case 'site':
                    $sqlCheck = "SELECT * FROM site WHERE name='$name'";
                    $sql = "INSERT INTO site (name, description) VALUES ('$name', '$description')";
                    break;
                case 'area':
                    $sqlCheck = "SELECT * FROM area WHERE name='$name'";
                    $sql = "INSERT INTO area (name, description, site_id) VALUES ('$name', '$description', '$site_id')";
                    break;
                case 'shelf':
                    $sqlCheck = "SELECT * FROM site WHERE name='$name'";
                    $sql = "INSERT INTO site (name, area_id) VALUES ('$name', '$area_id')";
                    break;
                default:
                    return 'invalidProperty';

            }
            include 'dbh.inc.php';
            $stmtCheck = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmtCheck, $sqlCheck)) {
                return 'Error: SQL Issue';
            } else {
                mysqli_stmt_execute($stmtCheck);
                $resultCheck = mysqli_stmt_get_result($stmtCheck);
                $rowCountCheck = $resultCheck->num_rows;
                if ($rowCountCheck < 1) {
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        return 'Error: SQL Issue';
                    } else {
                        mysqli_stmt_execute($stmt);
                        $new_id = mysqli_insert_id($conn);
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "$type", $new_id, "name", null, "$name");
                        return 'Property Added';
                    } 
                } else {
                    // exists
                    return 'Error: Property Already Exists.';
                }
            }
        } elseif (isset($_POST['load_property'])) {
            include 'dbh.inc.php';
            $type = $_POST['type'];

            $sql = "SELECT id, name
                    FROM $type
                    WHERE $type.deleted=0
                    ORDER BY id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                // fails to connect
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                $property_array = [];
                if ($rowCount < 1) {

                } else {
                    // rows found
                    while ($row = $result->fetch_assoc()) {
                        $property_array[] = array('id' => $row['id'], 'name' => $row['name']);
                    }
                }
                echo(json_encode($property_array));
                exit();
            } 
        }
    }
}

?>



<script> // MODAL SCRIPT
    // Get the modal
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

</script>






<!-- Modal Image Properties Div -->
<div id="modalDivProperties" class="modal">
<!-- <div id="modalDivProperties" style="display: block;"> -->
    <span class="close" onclick="modalCloseProperties()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <!-- Tag -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-tag" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="tag_name" class="nav-v-c align-middle">Tag Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="tag_name" name="property_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="tag_description" class="nav-v-c align-middle">Tag Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="tag_description" name="description" /></td>              
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Tag" class="btn btn-success"/></td> -->
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="submit" value="Add Tag" class="btn btn-success" onclick="addProperty('tag')">Add Tag</button></td>
                            <td hidden><input id="tag_type" type="hidden" name="type" value="tag" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Manufacturer -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-manufacturer" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td><label for="manufacturer_name" class="nav-v-c align-middle">New Manufacturer:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="manufacturer_name" name="property_name" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Manufacturer" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Manufacturer" class="btn btn-success" onclick="addProperty('manufacturer')">Add Manufacturer</button></td>
                            <td hidden><input id="manufacturer_type" type="hidden" name="type" value="manufacturer" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Site -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-site" hidden>
            <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data">
                <table class="centertable" style="border-collapse: collapse;table-layout:fixed;">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 130px"><label for="site_name" class="nav-v-c align-middle">New Site Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="site_name" name="property_name" /></td>           
                        </tr>
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width: 130px"><label for="site_description" class="nav-v-c align-middle">Site Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="site_description" name="description" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Site" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Site" class="btn btn-success" onclick="addProperty('site')">Add Site</button></td>
                            <td hidden><input type="hidden" name="type" value="site" /></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!-- Area -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-area" hidden>
            <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data">
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width:100px"><label for="area_name" class="nav-v-c align-middle">Site:</label></td>
                            <td style="margin-left:10px">
                                <select class="form-control" name="site_id">
                                    <?php 
                                        $sql = "SELECT name, id FROM site";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            header("Location: ../stock.php?modify=add&stock_id=&error=selectBoxSQLConnection");
                                            exit();
                                        } else {
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            $rowCount = $result->num_rows;
                                            if ($rowCount > 0) {
                                                while ($row = $result->fetch_assoc()){
                                                    echo('<option value="'.$row['id'].'">'.$row['name'].'</option>');
                                                }
                                            } else {
                                                //do nothing
                                                echo("0 rows");
                                            }
                                        }
                                    ?>
                                </select>    
                            </td>           
                        </tr>
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width:100px"><label for="area_name" class="nav-v-c align-middle">New Area:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="area_name" name="property_name" /></td>           
                            <td hidden><input type="hidden" name="type" value="area" /></td>
                        </tr>
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width: 100px"><label for="area_description" class="nav-v-c align-middle">Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="area_description" name="description" /></td>   
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Area" class="btn btn-success" onclick="addProperty('area')">Add Area</button></td>        
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Area" class="btn btn-success"/></td> -->
                            <td hidden><input type="hidden" name="type" value="area" /></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!-- Shelf -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-shelf" hidden>
            <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data">
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row" >
                            <td style="width:150px">Site: </td>
                            <td>
                                <select class="form-control" id="site-properties" name="site_site_id" style="width:300px" required>
                                    <option value="" selected disabled hidden>Select Site</option>
                                    <?php

                                        $sql = "SELECT id, name
                                                FROM site
                                                ORDER BY id";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                                            // fails to connect
                                        } else {
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            $rowCount = $result->num_rows;
                                            if ($rowCount < 1) {
                                                echo('<option value="0">No Sites Found...</option>');
                                            } else {
                                                // rows found
                                                while ($row = $result->fetch_assoc()) {
                                                    $sites_id = $row['id'];
                                                    $sites_name = $row['name'];
                                                    echo('<option value="'.$sites_id.'">'.$sites_name.'</option>');
                                                }
                                            }
                                        }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="nav-row" >
                            <td style="width:150px">Area: </td>
                            <td>
                                <select class="form-control" id="area-properties" name="area_area_id" style="width:300px" disabled required>
                                    <option value="" selected disabled hidden>Select Area</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="nav-row" >
                            <td style="width:150px"><label for="manufacturer_name" class="nav-v-c align-middle">New Shelf Name:</label></td>
                            <td><input type="text" class="form-control nav-v-c align-middle" id="manufacturer_name" name="property_name" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Shelf" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Shelf" class="btn btn-success" onclick="addProperty('shelf')">Add Shelf</button></td>
                            <td hidden><input type="hidden" name="type" value="manufacturer" /></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div> 
</div>
<!-- End of Modal Image Properties Div -->

<script>
    function addProperty(property) {
        if (property !== '') {
            var name = document.getElementById(property+'_name') !== null ? document.getElementById(property+'_name').value : '';
            var description = document.getElementById(property+'_description') !== null ? document.getElementById(property+'_description').value : '';
            var site_id = document.getElementById(property+'_site_id') !== null ? document.getElementById(property+'_site_id').value : '';
            var area_id = document.getElementById(property+'_area_id') !== null  ? document.getElementById(property+'_area_id').value : '';
            $.ajax({
                type: "POST",
                url: "./includes/stock-new-properties.inc.php",
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
</script>
<script>
function populateAreasProperties() {
  // Get the selected site
  var site = document.getElementById("site-properties").value;
  
  // Make an AJAX request to retrieve the corresponding areas
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "includes/stock-selectboxes.inc.php?site=" + site, true);
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
</script>