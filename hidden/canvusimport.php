<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

if (isset($_POST['canvusimport-submit'])) {
    if (isset($_FILES['csv']) && $_FILES["csv"]["error"] == UPLOAD_ERR_OK) {
        // Get the uploaded file details
        $fileName = $_FILES["csv"]["name"];
        $tmpName = $_FILES["csv"]["tmp_name"];

        // Move the uploaded file to a desired directory
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
        // unlink($uploadPath);

        // Display the array with headings as keys
        // print_r('<pre>');
        // print_r($csvData);
        // print_r('</pre>');
    } else {
        echo "Error uploading the file.";
    }
} 


// Function to convert array to CSV and force download
function downloadCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($data[0])); // Output header

    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
}
?>





<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#ffffff">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/inv.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v6.4.0/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>

<body>
    <div class="container" style="margin-top:30px">
        <h1>Canvus Import</h1>
    </div>
    <div class="container" style="margin-top:30px">
        <form enctype="multipart/form-data" action="canvusimport.php" method="post">
            <input type="file" name="csv" accept=".csv" />
            <input class="btn btn-success" type="submit" name="canvusimport-submit" value="Submit" />
        </form>
    </div>
</body>


<?php
function newSKU() {
    // include '../includes/dbh.inc.php';
    include 'dbh.inc.php'; // testing on copy db

    // GET SKU LIST
    $skus = [];
                    
    $sql_sku = "SELECT DISTINCT sku FROM stock
                ORDER BY sku";
    $stmt_sku = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_sku, $sql_sku)) {

    } else {
        mysqli_stmt_execute($stmt_sku);
        $result_sku = mysqli_stmt_get_result($stmt_sku);
        $rowCount_sku = $result_sku->num_rows;
        if ($rowCount_sku < 1) {

        } else {
            while ($row_sku = $result_sku->fetch_assoc() ){
                array_push($skus, $row_sku['sku']);
            }
        }
    }

    $sql_d_config = "SELECT sku_prefix FROM config_default WHERE id=1";
    $stmt_d_config = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_d_config, $sql_d_config)) {

    } else {
        mysqli_stmt_execute($stmt_d_config);
        $result_d_config = mysqli_stmt_get_result($stmt_d_config);
        $rowCount_d_config = $result_d_config->num_rows;
        if ($rowCount_d_config < 1) {

        } else {
            while ($row_d_config = $result_d_config->fetch_assoc() ){
                $config_d_sku_prefix = isset($row_d_config['sku_prefix']) ? $row_d_config['sku_prefix'] : 'ITEM-';
            }
        }
    }

    // if default sku is not set, set it to ITEM- 
    $config_d_sku_prefix = isset($config_d_sku_prefix) ? $config_d_sku_prefix : 'ITEM-';

    $sql_config = "SELECT sku_prefix FROM config WHERE id=1";
    $stmt_config = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {

    } else {
        mysqli_stmt_execute($stmt_config);
        $result_config = mysqli_stmt_get_result($stmt_config);
        $rowCount_config = $result_config->num_rows;
        if ($rowCount_config < 1) {

        } else {
            while ($row_config = $result_config->fetch_assoc() ){
                $config_sku_prefix = isset($row_config['sku_prefix']) ? $row_config['sku_prefix'] : $config_d_sku_prefix;
            }
        }
    }
    
    $current_sku_prefix = isset($config_sku_prefix) ? $config_sku_prefix : $config_d_sku_prefix;

    $regex = '/^'.$current_sku_prefix.'\d{5}$/';
    $PRE_skus = preg_grep($regex, $skus);
    if (empty($PRE_skus) || count($PRE_skus) == 0) {
        $new_PRE_sku_number = 1;
    } else {
        usort($PRE_skus, function($a, $b) { // sort the array 
            return strnatcmp($a, $b);
        });
        preg_match_all('/\d+/', end($PRE_skus), $max_sku_number_temp);
        if (!empty($max_sku_number_temp)) {
            $max_sku_number = $max_sku_number_temp[0][0];
            $new_PRE_sku_number = $max_sku_number +1; 
        } else {
            $new_PRE_sku_number = 1;
        }
    }

    $new_PRE_skus = $current_sku_prefix . str_pad($new_PRE_sku_number, 5, '0', STR_PAD_LEFT);
    return $new_PRE_skus;
}

if (isset($csvData)){

    // include '../includes/dbh.inc.php';
    include 'dbh.inc.php'; // testing on copy db
    $manualreview_lines = [];
    $action_lines = [];
    foreach ($csvData as $line) {
        $item = $line['Item'];
        $sku = $line['SKU'];
        $area = $line['Area'];
        $manufacturer = $line['Manufacturer'];
        $DA2_MMRA = $line['MMRA Store'];
        $DA2_NOC = $line['DA2 NOC Shelves'];
        $DF3_STORE = $line['DF3 Store'];
        $ENERGY_CENTRE = $line['Energy Centre Store'];
        $FIBRE_STORE = $line['Fibre Store'];
        $JC_OFFICE = $line["JCs Office"];
        $STORE_50 = $line['Store 50'];
        $ALL_COUNT = $line['All Locations'];
        $AVERAGE_COST = $line['Average Cost'];
        $TOTAL_VALUE = $line['Total Value'];

        $area_fields = ['MMRA Store', 'DA2 NOC Shelves', 'DF3 Store', 'Energy Centre Store', 'Fibre Store', "JCs Office", 'Store 50'];
        $multiple_field_count = false;
        $skip = false;

        $row = array('Item' => $item, 'SKU' => $sku, 'Area' => $area, 'Manufacturer' => $manufacturer, 'MMRA Store' => $DA2_MMRA, 'DA2 NOC Shelves' => $DA2_NOC, 
                    'DF3 Store' => $DF3_STORE, 'Energy Centre Store' => $ENERGY_CENTRE, 'Fibre Store' => $FIBRE_STORE, 'JCs Office' => $JC_OFFICE, 'Store 50' => $STORE_50,
                    'All Locations' => $ALL_COUNT, 'Average Cost' => $AVERAGE_COST, 'Total Value' => $TOTAL_VALUE);

        foreach ($area_fields as $field) {
            // Assuming $line[$field] contains the value of the current field
            $value = $line[$field];

            // Check if the value is a number
            if (is_numeric($value) && $value > 0) {
                if ($multiple_field_count == true) {
                    // If already found a duplicate number, add the entire row to the "manualreview" array
                    $manualreview_lines[] = $row;
                    $skip = true;
                    break;  // No need to continue checking
                } else {
                    $multiple_field_count = true;
                }
                
            }
        }
        if ($skip == false) {
            $action_lines[] = $row;
        }




    }
    if (isset($manualreview_lines) && count($manualreview_lines) > 0 ){
        $headings = array_keys($manualreview_lines[0]);

        $fp = fopen('manualreviewlines.csv', 'w');

        fputcsv($fp,$headings,"\t");
        foreach($manualreview_lines as $row) {
            fputcsv($fp,$row,"\t");
        }

        fclose($fp);
    }
    

    // download the csv
    //downloadCSV($manualreview_lines, 'manual-review.csv');

    // check for an image in the folder
    // get all images into an array.
    $directory = 'images/';
    $files = scandir($directory);

    $fileList = array();

    foreach ($files as $file) {
        // Skip current and parent directory entries
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directory . $file;

        if (is_file($filePath)) {
            $fileInfo = pathinfo($filePath);
            $fileList[] = array('name' => $fileInfo['filename'], 'ext' => $fileInfo['extension']);
            //echo ("'".$fileInfo['filename']."', ");
        }
    }
    // Now $fileList contains the desired array of file names and extensions
    print_r('<pre hidden>');
    print_r($fileList);
    print_r('</pre><br><br>');
    if (isset($manualreview_lines) && count($manualreview_lines) > 0 ) {
        echo('<h3>Manual Review</h3><a class="btn btn-info" style="color:black !important" href="manualreviewlines.csv" download="manualreview.csv">Download CSV</a><br>');
        print_r('<pre>');
        print_r($manualreview_lines);
        print_r('</pre><br>');
    }
    

    
    
    echo('<h3>Actioned</h3>');
    print_r('<pre>');
    print_r($action_lines);
    print_r('</pre><br>');

    print_r('<pre hidden>');
    print_r($csvData);
    print_r('</pre>');
    if (isset($action_lines) && count($action_lines) > 0 ) {
        // print_r($action_lines);
        // foreach ($action_lines as $line) {
        //     print_r($line['Item']);
        //     print_r('<br>');
        // }
        foreach ($action_lines as $line) {
            $item = mysqli_real_escape_string($conn, $line['Item']);
            $sku = mysqli_real_escape_string($conn, $line['SKU']);
            $area = mysqli_real_escape_string($conn, $line['Area']);
            $manufacturer = mysqli_real_escape_string($conn, $line['Manufacturer']);
            $DA2_MMRA = mysqli_real_escape_string($conn, $line['MMRA Store']);
            $DA2_NOC = mysqli_real_escape_string($conn, $line['DA2 NOC Shelves']);
            $DF3_STORE = mysqli_real_escape_string($conn, $line['DF3 Store']);
            $ENERGY_CENTRE = mysqli_real_escape_string($conn, $line['Energy Centre Store']);
            $FIBRE_STORE = mysqli_real_escape_string($conn, $line['Fibre Store']);
            $JC_OFFICE = mysqli_real_escape_string($conn, $line["JCs Office"]);
            $STORE_50 = mysqli_real_escape_string($conn, $line['Store 50']);
            $ALL_COUNT = mysqli_real_escape_string($conn, $line['All Locations']);
            $AVERAGE_COST = mysqli_real_escape_string($conn, $line['Average Cost']);
            $TOTAL_VALUE = mysqli_real_escape_string($conn, $line['Total Value']);
            $area_fields = ['MMRA Store', 'DA2 NOC Shelves', 'DF3 Store', 'Energy Centre Store', 'Fibre Store', "JCs Office", 'Store 50'];

            // check if the stock name exists already
            $sql_stock = "SELECT id, name, deleted FROM stock WHERE name='$item' LIMIT 1";
            $stmt_stock = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                echo('ERROR AT LINE: '.__LINE__.'<br>');
            } else {
                mysqli_stmt_execute($stmt_stock);
                $result_stock = mysqli_stmt_get_result($stmt_stock);
                $rowCount_stock = $result_stock->num_rows;
                if ($rowCount_stock < 1) {
                    // none found - add
                    $sql_stock_insert = "INSERT INTO stock (name, description, sku, min_stock, is_cable) 
                                VALUES (?, ?, ?, 0, 0)";
                    $stmt_stock_insert = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock_insert, $sql_stock_insert)) {
                        echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                    } else {
                        $new_sku = newSKU();
                        $description = $item.' - Imported from cavnus, sku: '.$sku;
                        mysqli_stmt_bind_param($stmt_stock_insert, "sss", $item, $description, $new_sku);
                        mysqli_stmt_execute($stmt_stock_insert);
                        // get new id
                        $stock_id = mysqli_insert_id($conn); // ID of the new row in the table.
                        // update changelog
                        // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "shelf", $shelf_id, "name", null, $shelf_name);

                    }
                } else {
                    $row_stock = $result_stock->fetch_assoc();
                    $stock_id = $row_stock['id'];

                    // check if deleted, if deleted, undeleted.
                    if ($row_stock['deleted'] == 1) {
                        $sql_stock_update = "UPDATE stock SET deleted=0 WHERE id=$stock_id;";
                        $stmt_stock_update = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_stock_update, $sql_stock_update)) {
                            echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                        } else {
                            mysqli_stmt_execute($stmt_stock_update);
                            // update changelog
                            //addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "site", $site_id, "deleted", 0, 1);
                        }
                    }                
                }
            }

            //if manufacturer is blank, ignore it
            if (isset($manufacturer) && $manufacturer !== '') {
                // check if the manufacturer name exists already
                $sql_manufacturer = "SELECT id, name, deleted FROM manufacturer WHERE name='$manufacturer' LIMIT 1";
                $stmt_manufacturer = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_manufacturer, $sql_manufacturer)) {
                    echo('ERROR AT LINE: '.__LINE__.'<br>');
                } else {
                    mysqli_stmt_execute($stmt_manufacturer);
                    $result_manufacturer = mysqli_stmt_get_result($stmt_manufacturer);
                    $rowCount_manufacturer = $result_manufacturer->num_rows;
                    if ($rowCount_manufacturer < 1) {
                        // none found - add
                        $sql_manufacturer_insert = "INSERT INTO manufacturer (name) 
                                    VALUES (?)";
                        $stmt_manufacturer_insert = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_manufacturer_insert, $sql_manufacturer_insert)) {
                            echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                        } else {
                            mysqli_stmt_bind_param($stmt_manufacturer_insert, "s", $manufacturer);
                            mysqli_stmt_execute($stmt_manufacturer_insert);
                            // get new id
                            $manufacturer_id = mysqli_insert_id($conn); // ID of the new row in the table.
                            // update changelog
                            // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "shelf", $shelf_id, "name", null, $shelf_name);

                        }
                    } else {
                        $row_manufacturer = $result_manufacturer->fetch_assoc();
                        $manufacturer_id = $row_manufacturer['id'];

                        // check if deleted, if deleted, undeleted.
                        if ($row_manufacturer['deleted'] == 1) {
                            $sql_manufacturer_update = "UPDATE manufacturer SET deleted=0 WHERE id=$manufacturer_id;";
                            $stmt_manufacturer_update = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_manufacturer_update, $sql_manufacturer_update)) {
                                echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                            } else {
                                mysqli_stmt_execute($stmt_manufacturer_update);
                                // update changelog
                                //addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "site", $site_id, "deleted", 0, 1);
                            }
                        }
                    }
                }
            } else {
                $manufacurer_id = '';
            }
            
            // check the 'AREA' from the csv matches a shelf in the DB based on the locations (area in the db)
            // get the area name from the field
            foreach ($area_fields as $field) {
                if ($line[$field] !== '0') {
                    $db_area = $field;
                }
            }

            if ($db_area) {
                // check if the area exists in the db
                $sql_area = "SELECT id, name FROM area WHERE name='$db_area' LIMIT 1";
                $stmt_area = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                    echo('ERROR AT LINE: '.__LINE__.'<br>');
                } else {
                    mysqli_stmt_execute($stmt_area);
                    $result_area = mysqli_stmt_get_result($stmt_area);
                    $rowCount_area = $result_area->num_rows;
                    if ($rowCount_area < 1) {
                        // none found 
                        echo ('ISSUE AT LINE: '.__LINE__.' - no area matching "'.$db_area.'"<br>');
                    } else {
                        // check if deleted, if deleted, undeleted.
                        $row_area = $result_area->fetch_assoc();
                        $area_id = $row_area['id'];

                        // check if the shelf exists in the db
                        $sql_shelf = "SELECT id, name, deleted FROM shelf WHERE name='$area' AND area_id='$area_id' LIMIT 1";
                        $stmt_shelf = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_shelf, $sql_shelf)) {
                            echo('ERROR AT LINE: '.__LINE__.'<br>');
                        } else {
                            mysqli_stmt_execute($stmt_shelf);
                            $result_shelf = mysqli_stmt_get_result($stmt_shelf);
                            $rowCount_shelf = $result_shelf->num_rows;
                            if ($rowCount_shelf < 1) {
                                // none found - add
                                $sql_shelf_insert = "INSERT INTO shelf (name, area_id) 
                                            VALUES (?, ?)";
                                $stmt_shelf_insert = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_shelf_insert, $sql_shelf_insert)) {
                                    echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                                } else {
                                    $new_sku = newSKU();
                                    $description = $item.' - Imported from cavnus, sku: '.$sku;
                                    mysqli_stmt_bind_param($stmt_shelf_insert, "ss", $area, $area_id);
                                    mysqli_stmt_execute($stmt_shelf_insert);
                                    // get new id
                                    $shelf_id = mysqli_insert_id($conn); // ID of the new row in the table.
                                    // update changelog
                                    // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "shelf", $shelf_id, "name", null, $shelf_name);

                                }
                            } else {
                                $row_shelf = $result_shelf->fetch_assoc();
                                $shelf_id = $row_shelf['id'];

                                // check if deleted, if deleted, undeleted.
                                if ($row_shelf['deleted'] == 1) {
                                    $sql_shelf_update = "UPDATE shelf SET deleted=0 WHERE id=$shelf_id;";
                                    $stmt_shelf_update = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_shelf_update, $sql_shelf_update)) {
                                        echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                                    } else {
                                        mysqli_stmt_execute($stmt_shelf_update);
                                        // update changelog
                                        //addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "site", $site_id, "deleted", 0, 1);
                                    }
                                }


                                // insert items to item table
                                $item_ids = []; // all ids for the items

                                for ($i=1; $i <= $line[$db_area]; $i++) {
                                    // for loop adding 1 item row for the total count of stock for this area and shelf - the db_area = the field heading matching the area.

                                    if (isset($manufacturer_id) && $manufacturer_id !== '') {
                                        $sql_item_insert = "INSERT INTO item (stock_id, quantity, cost, manufacturer_id, shelf_id, serial_number, comments, upc) VALUES ('$stock_id', 1, 0, '$manufacturer_id', '$shelf_id', '', '', '')"; 
                                    } else {
                                        $sql_item_insert = "INSERT INTO item (stock_id, quantity, cost, shelf_id, serial_number, comments, upc) VALUES ('$stock_id', 1, 0,'$shelf_id', '', '', '')";
                                    }      
                                    $stmt_item_insert = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_item_insert, $sql_item_insert)) {
                                        echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                                    } else {
                                        mysqli_stmt_execute($stmt_item_insert);
                                        // get new id
                                        $item_id = mysqli_insert_id($conn); // ID of the new row in the table.
                                        // update changelog
                                        // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "item", $item_id, "name", null, $item_name);
                                        $item_ids[] = $item_id;
                                    }
                                }  
                                
                                // check if image exists, if not, check the images folder for a match to sku and add.

                                // check for a match in the DB
                                $sql_img = "SELECT id, image, stock_id FROM stock_img WHERE stock_id='$stock_id' LIMIT 1";
                                $stmt_img = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_img, $sql_img)) {
                                    echo('ERROR AT LINE: '.__LINE__.'<br>');
                                } else {
                                    mysqli_stmt_execute($stmt_img);
                                    $result_img = mysqli_stmt_get_result($stmt_img);
                                    $rowCount_img = $result_img->num_rows;
                                    if ($rowCount_img < 1) {
                                        // no image exists - see if we can add one!

                                        // check for an image match 
                                        $searchString = $sku;
                                        $matchingFile = null;

                                        foreach ($fileList as $fileInfo) {
                                            if ($fileInfo['name'] === $searchString) {
                                                $matchingFile = $fileInfo;
                                                break;
                                            }
                                        }

                                        if ($matchingFile !== null) {
                                            // Add Image

                                            echo "Match found for $searchString: ";
                                            print_r($matchingFile);
                                            echo "<br>";
                                            $current_image_file = $matchingFile['name'].'.'.$matchingFile['ext'];
                                            $timedate = date("YmdHis");
                                            $new_image_name = "stock-$stock_id-img-$timedate.".$matchingFile['ext'];

                                            //exec("cp images/$current_image_file ../assets/img/stock/$new_image_name");
                                            exec("cp images/$current_image_file images/stock/$new_image_name");

                                            $sql_img_insert = "INSERT INTO stock_img (stock_id, image) 
                                                        VALUES (?, ?)";
                                            $stmt_img_insert = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_img_insert, $sql_img_insert)) {
                                                echo ('ISSUE AT LINE: '.__LINE__.'<br>');
                                            } else {
                                                mysqli_stmt_bind_param($stmt_img_insert, "ss", $stock_id, $new_image_name);
                                                mysqli_stmt_execute($stmt_img_insert);
                                                // get new id
                                                $img_id = mysqli_insert_id($conn); // ID of the new row in the table.
                                                // update changelog
                                                // addChangelog($_SESSION['user_id'], $_SESSION['username'], "New record", "stock_img", $img_id, "image", null, $new_image_name);

                                            }
                                        } else {
                                            echo "No match found for $searchString <br>";
                                            // do nothing.
                                            $img_id = 'N/A';
                                        }
                                    } else {
                                        $row_img = $result_img->fetch_assoc();
                                        $img_id = $row_img['id'];
                                    }
                                }

                                
                            }
                        }
                    }
                }

            } else {
                echo('ISSUE AT LINE: '.__LINE__.'<br>');
            }
            if (isset($item_ids) && is_array($item_ids) && count($item_ids) > 0) {
                $item_ids_string = implode(', ', $item_ids);
            } else {
                $item_ids_string = '';
            }
            echo("<ul>");
            echo('<li>Stock ID: '.$stock_id.'</li>');
            echo('<li>Manufacturer ID: '.$manufacturer_id.'</li>');
            echo('<li>Count: '.$line[$db_area].'</li>');
            echo('<li>Item IDs: '.$item_ids_string.'</li>');
            echo('<li>Img ID: '.$img_id.'</li>');
            echo("</ul>");
            echo ('Stock ID: '.$stock_id.' | Manufacturer ID: '.$manufacturer_id.' | Shelf ID: '.$shelf_id.' | Count: '.$line[$db_area].' | Item IDs: '.$item_ids_string.' | Img ID: '.$img_id.' <br>');
        }
    }

}


?>