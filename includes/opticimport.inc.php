<?php
// This will be used as an include file inline within the optic-import.php file, to make everything clean.

// include 'changelog.inc.php';
// include 'dbh.inc.php';

function getOpticProperties($property) {
    global $conn;

    $table = 'optic_'.$property;
    $return = []; // return array

    $sql = "SELECT id, name, deleted FROM $table";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 'error';
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        while ($row = $result->fetch_assoc()) {
            $return['name'][$row['name']] = array('id' => $row['id'], 'name' => $row['name'], 'deleted' => $row['deleted']);
            $return['id'][$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'deleted' => $row['deleted']);
        }
    }
    return $return;
}
function checkPropertyExists($value, $array) {
    $name_array = $array['name'];
    $id_array = $array['id'];

    if (!is_numeric($value)) {
        $check_array = $name_array;
    } else  {
        $check_array = $id_array;
    }

    if (in_array($value, array_keys($check_array))) {
        if ($check_array[$value]['deleted'] == 0) {
            return $check_array[$value]['id']*-1;
        } else {
            return $check_array[$value]['id'];
        }
    } else {
        return 0;
    }
}
function addPropery($property, $value) {
    global $conn;

    $table = 'optic_'.$property;
    $sql = "INSERT INTO $table (name) 
            VALUES (?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 0;
    } else {
        mysqli_stmt_bind_param($stmt, "s", $value);
        mysqli_stmt_execute($stmt);
        // get new id
        $property_id = mysqli_insert_id($conn); // ID of the new row in the table.
        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", $table, $property_id, "name", null, $value);
        return 1;
    }
}
function addSite($property, $value) {
    global $conn;

    $sql = "INSERT INTO site (name, description) 
            VALUES (?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 0;
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $value, $value);
        mysqli_stmt_execute($stmt);
        // get new id
        $property_id = mysqli_insert_id($conn); // ID of the new row in the table.
        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", 'site', $property_id, "name", null, $value);
        return 1;
    }
}
function getSites() {
    global $conn;

    $table = 'site';
    $return = []; // return array

    $sql = "SELECT id, name, deleted FROM $table";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 'error';
    } else {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        while ($row = $result->fetch_assoc()) {
            $return['name'][$row['name']] = array('id' => $row['id'], 'name' => $row['name'], 'deleted' => $row['deleted']);
            $return['id'][$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'deleted' => $row['deleted']);
        }
    }
    return $return;
}
function undeleteProperty($table, $property, $value) {
    global $conn;

    $value = $value*-1;
    $return = []; // return array

    $sql = "UPDATE $table SET deleted = 0 WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return 'error';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $value);
        mysqli_stmt_execute($stmt);
        return $value;
    }
}
function getPropertyIdAfterCheck($property, $value, $array) {
    $id = checkPropertyExists($value, $array);
    if ($id == 0) {
        // add the property
        $id = addPropery($property, $value);
    }
    if ($id < 0) {
        // un-delete the property
        $table = 'optic_'.$property;
        $id = undeleteProperty($table, $property, $id);
    }
    return $id;
}
function getSiteIdAfterCheck($property, $value, $array) {
    $id = checkPropertyExists($value, $array);
    if ($id == 0) {
        // add the property
        $id = addSite($property, $value);
    }
    if ($id < 0) {
        // un-delete the property
        $id = undeleteProperty('site', $property, $id);
    }
    return $id;
}


if (isset($_POST['opticsimport-submit'])) {
    if (isset($_FILES['csv']) && $_FILES["csv"]["error"] == UPLOAD_ERR_OK) {
        // Get the uploaded file details
        $fileName = $_FILES["csv"]["name"];
        $tmpName = $_FILES["csv"]["tmp_name"];

        // Move the uploaded file to a desired directory
        if (!file_exists('uploads/')) {
            mkdir('uploads');
        }
        $uploadDir = 'uploads/';
        $uploadPath = $uploadDir . $fileName;
        move_uploaded_file($tmpName, $uploadPath);

        // Open and read the CSV file
        $csvFile = fopen($uploadPath, 'r');

        // Read the header row to get the column headings
        $headers = fgetcsv($csvFile, 1000, ',');

        // Initialize an empty array to store CSV data
        $csvData = [];
        
        // Read and iterate over the remaining rows
        while (($data = fgetcsv($csvFile, 1000, ',')) !== FALSE) {
            // Combine headers with current row data to create an associative array
            $rowData = array_combine($headers, $data);

            // Add the associative array to the main data array
            $csvData[] = $rowData;
        }

        // Close the CSV file
        fclose($csvFile);
        // Optional: Remove the uploaded file after processing
        unlink($uploadPath);

        // Display the array with headings as keys
        // print_r('<pre>');
        // print_r($csvData);
        // print_r('</pre>');



        if (isset($csvData)){
            $quanrantined_lines = []; // for rows that cant be added
            $actioned_lines = []; // for rows that have been added

            //get each row
            foreach ($csvData as $line) {
                $quarantined_reasons = array('serial_number' => 0,
                                            'vendor' => 0,
                                            'connector' => 0,
                                            'type' => 0,
                                            'speed' => 0,
                                            'distance' => 0,
                                            'deleted' => 0
                                            ); // array to hold the reason(s) for quarantine

                // get the db info for the propertiew - do this every time incase something changes.
                $db_speeds = getOpticProperties('speed');
                $db_connectors = getOpticProperties('connector');
                $db_distances = getOpticProperties('distance');
                $db_types = getOpticProperties('type');
                $db_vendors = getOpticProperties('vendor');

                $db_sites = getSites();

                $site = $line['Site'];
                $vendor = $line['Vendor'];
                $mode = $line['Mode'];
                $connector = $line['Connector'];
                $type = $line['Type'];
                $spectrum = $line['Spectrum'];
                $speed = $line['Speed'];
                $distance = $line['Distance'];
                $model = $line['Model'];
                $serial_number = $line['Serial_number'];
        
                $skip = false;
        
                $row = array('Site' => $site, 'Vendor' => $vendor, 'Connector' => $connector, 'Type' => $type, 'Spectrum' => $spectrum, 'Speed' => $speed, 
                            'Distance' => $distance, 'Model' => $model, 'Serial_number' => $serial_number);
        
                // check if a match already exists in serial number
                $sql = "SELECT * FROM optic_item WHERE UCASE(serial_number)=UCASE(?) LIMIT 1";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo('ERROR AT LINE: '.__LINE__.'<br>');
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $serial_number);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount > 0) {
                        $row = $result->fetch_assoc();

                        $optic_id = $row['id'];
                        $optic_site_id = $row['site_id'];
                        $optic_model = $row['model'];
                        $optic_vendor_id = $row['vendor_id'];
                        $optic_serial_number = $row['serial_number'];
                        $optic_type_id = $row['type_id'];
                        $optic_connector_id = $row['connector_id'];
                        $optic_mode = $row['mode'];
                        $optic_spectrum = $row['spectrum'];
                        $optic_speed_id = $row['speed_id'];
                        $optic_distance_id = $row['distance_id'];
                        $optic_quantity = $row['quantity'];
                        $optic_deleted = $row['deleted'];

                        $quarantined_reasons['serial_number'] = 1;

                        // site check
                        if (in_array($site, array_keys($db_sites['name']))) {
                            if ($db_sites['name'][$site]['id'] !== $optic_site_id) {
                                $quarantined_reasons['site'] = 1;
                            }
                            if ($db_sites['name'][$site]['deleted'] == 1) {
                                $quarantined_reasons['site'] = 1;
                            }
                        } else {
                            $quarantined_reasons['site'] = 1;
                        }
                        // vendor check
                        if (in_array($vendor, array_keys($db_vendors['name']))) {
                            if ($db_vendors['name'][$vendor]['id'] !== $optic_vendor_id) {
                                $quarantined_reasons['vendor'] = 1;
                            }
                            if ($db_vendors['name'][$vendor]['deleted'] == 1) {
                                $quarantined_reasons['vendor'] = 1;
                            }
                        } else {
                            $quarantined_reasons['vendor'] = 1;
                        }
                        // type check
                        if (in_array($type, array_keys($db_types['name']))) {
                            if ($db_types['name'][$type]['id'] !== $optic_type_id) {
                                $quarantined_reasons['type'] = 1;
                            }
                            if ($db_types['name'][$type]['deleted'] == 1) {
                                $quarantined_reasons['type'] = 1;
                            }
                        } else {
                            $quarantined_reasons['type'] = 1;
                        }
                        // connector check
                        if (in_array($connector, array_keys($db_connectors['name']))) {
                            if ($db_connectors['name'][$connector]['id'] !== $optic_connector_id) {
                                $quarantined_reasons['connector'] = 1;
                            }
                            if ($db_connectors['name'][$connector]['deleted'] == 1) {
                                $quarantined_reasons['connector'] = 1;
                            }
                        } else {
                            $quarantined_reasons['connector'] = 1;
                        }
                        // speed check
                        if (in_array($speed, array_keys($db_speeds['name']))) {
                            if ($db_speeds['name'][$speed]['id'] !== $optic_speed_id) {
                                $quarantined_reasons['speed'] = 1;
                            }
                            if ($db_speeds['name'][$speed]['deleted'] == 1) {
                                $quarantined_reasons['speed'] = 1;
                            }
                        } else {
                            $quarantined_reasons['speed'] = 1;
                        }
                        // distance check
                        if (in_array($distance, array_keys($db_distances['name']))) {
                            if ($db_distances['name'][$distance]['id'] !== $optic_distance_id) {
                                $quarantined_reasons['distance'] = 1;
                            }
                            if ($db_distances['name'][$distance]['deleted'] == 1) {
                                $quarantined_reasons['distance'] = 1;
                            }
                        } else {
                            $quarantined_reasons['distance'] = 1;
                        }
                        // deleted check
                        if ($optic_deleted == 1) {
                            $quarantined_reasons['deleted'] = 1;
                        }
                        $skip = true;
                        $quanrantined_lines[] = array('line' => $line, 'reasons' => $quarantined_reasons, 'matching_row' => $row);
                    }
                }

                if ($skip == false) {
                    // check if the properties exists
                    // site
                    $site_id = getSiteIdAfterCheck('site', $site, $db_sites);
                    // vendor
                    $vendor_id = getPropertyIdAfterCheck('vendor', $vendor, $db_vendors);
                    // connector
                    $connector_id = getPropertyIdAfterCheck('connector', $connector, $db_connectors);
                    // type
                    $type_id = getPropertyIdAfterCheck('type', $type, $db_types);
                    // speed
                    $speed_id = getPropertyIdAfterCheck('speed', $speed, $db_speeds);
                    // distance
                    $distance_id = getPropertyIdAfterCheck('distance', $distance, $db_distances);
                    
                    
                    // insert the data
                    $sql = "INSERT INTO optic_item (model, vendor_id, serial_number, type_id, connector_id, mode, spectrum, speed_id, distance_id, site_id, quantity) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        return 0;
                    } else {
                        mysqli_stmt_bind_param($stmt, "ssssssssss", $model, $vendor_id, $serial_number, $type_id, $connector_id, $mode, $spectrum, $speed_id, $distance_id, $site_id);
                        mysqli_stmt_execute($stmt);
                        // get new id
                        $optic_item_id = mysqli_insert_id($conn); // ID of the new row in the table.
                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", 'optic_item', $optic_item_id, "serial_number", null, $serial_number);

                         // get row data
                        $sql = "SELECT * FROM optic_item WHERE id = ?";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo('ERROR AT LINE: '.__LINE__.'<br>');
                        } else {
                            mysqli_stmt_bind_param($stmt, "s", $optic_item_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            $row = $result->fetch_assoc();
                            $actioned_lines[] = array('line' => $line, 'new_row' => $row);
                        }
                    }
                } 
            }







            if (isset($quanrantined_lines) && count($quanrantined_lines) > 0 ){
                $line0 = $quanrantined_lines[0]['line'];
                $headings = array_keys($line0);
                $fp = fopen('quarantined.csv', 'w');
        
                fputcsv($fp,$headings,",");
                foreach($quanrantined_lines as $row) {
                    $line = $row['line'];
                    fputcsv($fp,$line,",");
                }
        
                fclose($fp);
            }
            
            ?>

            <script>
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
            </script>

            <?php
            // show the quarantined items
            if (isset($quanrantined_lines) && count($quanrantined_lines) > 0 ) {
                echo('
                <div class="container">
                    <h3 class="clickable" onclick="toggleSection(this, \'quarantine\')">Quarantined<i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3>
                    <a class="btn btn-info" style="color:black !important" href="quarantined.csv" download="quanratined.csv">Download CSV</a>
                    <br>
                    <div class="" id="quarantine" hidden>');
                print_r('<pre>');
                print_r($quanrantined_lines);
                print_r('</pre>
                        <br>
                    </div>');
                echo('</div>');
            }
            
            
        
            
            
            echo('<h3 class="container clickable" onclick="toggleSection(this, \'actioned\')">Actioned<i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px; margin-top:40px"></i></h3>
            <div class="" id="actioned" hidden>');
            // print_r('<pre>');
            // print_r($actioned_lines);
            // print_r('</pre></div>');
        
            print_r('<pre hidden id="actioned-pre">');
            print_r($csvData);
            print_r('</pre>');

            if (isset($actioned_lines) && count($actioned_lines) > 0 ) {
                // print_r($actioned_lines);
                // create table
                echo('
                    <table class="table table-dark theme-table centertable" id="actioned-table">
                        <thead style="text-align: center; white-space: nowrap;">
                            <tr class="theme-tableOuter">
                                <th>id</th>
                                <th>model</th>
                                <th>vendor_id</th>
                                <th>serial_number</th>
                                <th>type_id</th>
                                <th>connector_id</th>
                                <th>mode</th>
                                <th>spectrum</th>
                                <th>speed_id</th>
                                <th>distance_id</th>
                                <th>site_id</th>
                                <th>quantity</th>
                                <th>deleted</th>
                            </tr>
                        </thead>
                ');
                echo('
                    <tbody>
                ');
                foreach ($actioned_lines as $line) {
                    $row = $line['new_row'];
                    echo('
                        <tr class="align-middle text-center">
                            <td>'.$row['id'].'</td>
                            <td>'.$row['model'].'</td>
                            <td>'.$row['vendor_id'].'</td>
                            <td>'.$row['serial_number'].'</td>
                            <td>'.$row['type_id'].'</td>
                            <td>'.$row['connector_id'].'</td>
                            <td>'.$row['mode'].'</td>
                            <td>'.$row['spectrum'].'</td>
                            <td>'.$row['speed_id'].'</td>
                            <td>'.$row['distance_id'].'</td>
                            <td>'.$row['site_id'].'</td>
                            <td>'.$row['quantity'].'</td>
                            <td>'.$row['deleted'].'</td>
                        </tr>
                    ');
                }
                echo('
                        </tbody>
                    </table>
                ');
                
                foreach ($actioned_lines as $row) {
                    $line = $row['line'];

                    // print the completed list on the page in a table
                    
                }
            } else {
                echo('<p>No rows actioned</p>');
            }
            echo('</div>');
        
        }




    } else {
        echo "<div class='container'>
            <p class='bg-dark red' style='width:100%' id='error-p'>Error uploading the file.</p>
        </div>";
    }
    echo('<div class="container"><a href="optics.php" class="btn btn-dark" style="color:black !important; margin-top:40px"><i class="fa fa-chevron-left"></i>&nbsp; Return to optics</a></div>');
}