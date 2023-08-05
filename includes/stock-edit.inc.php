<?php

if (isset($_GET['stock_id'])) {
    if (is_numeric($_GET['stock_id'])) {
        $stock_id = $_GET['stock_id'];

        include 'dbh.inc.php';

        $sql_stock = "SELECT * FROM stock WHERE id=?";
        $stmt_stock = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
            mysqli_stmt_execute($stmt_stock);
            $result_stock = mysqli_stmt_get_result($stmt_stock);
            $rowCount_stock = $result_stock->num_rows;
            if ($rowCount_stock < 1) {
                echo ('No Stock Found');
            } else {
                // STOCK FOUND
                $stock = $result_stock->fetch_assoc();
                $stock_img_data = [];

                $sql_img = "SELECT * FROM stock_img WHERE stock_id=?";
                $stmt_img = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_img, $sql_img)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_img, "s", $stock_id);
                    mysqli_stmt_execute($stmt_img);
                    $result_img = mysqli_stmt_get_result($stmt_img);
                    $rowCount_img = $result_img->num_rows;
                    if ($rowCount_img < 1) {
                        //no images
                    } else {
                        while ($row_img = $result_img->fetch_assoc()) {
                            $stock_img_data[] = array('id' => $row_img['id'], 'image' => $row_img['image'], 'stock_id' => $row_img['stock_id']);
                        }
                    }
                }

                $sql_label = "SELECT stock_label.id AS stock_label_id, stock_label.stock_id AS stock_label_stock_id, stock_label.label_id AS stock_label_label_id,
                                    label.id AS label_id, label.name AS label_name
                                FROM stock_label 
                                INNER JOIN label ON stock_label.label_id=label.id
                                WHERE stock_id=?";
                $stmt_label = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_label, $sql_label)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_label, "s", $stock_id);
                    mysqli_stmt_execute($stmt_label);
                    $result_label = mysqli_stmt_get_result($stmt_label);
                    $rowCount_label = $result_label->num_rows;
                    if ($rowCount_label < 1) {
                        //no images
                        $stock_label_data = '';
                    } else {
                        while ($row_label = $result_label->fetch_assoc()) {
                            $stock_label_data[] = array('id' => $row_label['label_id'], 'name' => $row_label['label_name'], 'stock_id' => $row_label['stock_label_stock_id']);
                        }
                    }
                }

                        

                // print_r($stock_img);

                echo ('
                
                <div class="container well-nopad bg-dark" style="margin-bottom:5px">
                    <h3 style="font-size:22px; margin-left:25pxq">Edit Existing Stock</h3>
                    <div class="row">
                        <div class="col-sm-7 text-left" id="stock-info-left">
                            <form id="edit-form" action="includes/stock-edit-action.inc.php" method="POST" enctype="multipart/form-data">
                                <div class="nav-row" style="margin-bottom:25px">
                                    <div class="nav-row" id="id-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="id" id="id-label">ID</label></div>
                                        <div><input type="text" name="id-visible" placeholder="X" id="id-visible" class="form-control nav-v-c" style="width:300px;color:black;background-color:#adadad !important" value="'.$stock['id'].'" disabled></input></div>
                                        <input type="hidden" name="id" id="id" value="'.$stock['id'].'" />
                                    </div>
                                    <div class="nav-row" id="name-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                        <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c" style="width:300px" value="'.$stock['name'].'" required></input></div>
                                    </div>
                                    <div class="nav-row" id="sku-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                        <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c" style="width:300px" value="'.$stock['sku'].'" ></input></div>
                                    </div>
                                    <div class="nav-row" id="description-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                        <div><textarea class="form-control nav-v-c" id="description" name="description" rows="3" cols="32" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="'.$stock['description'].'" >'.$stock['description'].'</textarea></div>
                                    </div>
                                    <div class="nav-row" id="labels-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="labels" id="labels-label">Labels</label></div>
                                        <div id="labels-group">
                                        <input id="labels-selected" name="labels-selected" type="hidden" />
                                        <select id="labels" name="labels[]" multiple class="form-control nav-trans" style="border: 1px solid grey;display: inline-block;width:300px;height:40px">');
                                            if (is_array($stock_label_data)) {
                                                for ($l=0; $l<count($stock_label_data); $l++) {
                                                    // echo('<option class="btn btn-dark btn-stock gold fafont" style="margin-top:4px;border:1px solid gray" value="'.$stock_label_data[$l]['id'].'">'.$stock_label_data[$l]['name'].' &#xf057;</option> ');
                                                    echo('<option class="btn-stock" style="margin-top:1px;border:1px solid gray" value="'.$stock_label_data[$l]['id'].'" selected>'.$stock_label_data[$l]['name'].'</option> ');
                                                }
                                            } else {
                                                //echo('None');
                                            }
                                        echo('
                                        </select>
                                        <select class="form-control" id="labels-init" name="labels-init" style="width:300px;margin-top:2px">
                                            <option value="" selected>-- Add Labels --</option>');
                                            include 'includes/dbh.inc.php';
                                            $sql = "SELECT id, name
                                                    FROM label
                                                    WHERE label.id NOT IN (SELECT label_id FROM stock_label WHERE stock_id = '$stock_id')
                                                    ORDER BY id";
                                            $stmt = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                // fails to connect
                                            } else {
                                                mysqli_stmt_execute($stmt);
                                                $result = mysqli_stmt_get_result($stmt);
                                                $rowCount = $result->num_rows;
                                                if ($rowCount < 1) {
                                                    echo('<option value="0" selected>No Manufacturers Found...</option>');
                                                } else {
                                                    // rows found
                                                    while ($row = $result->fetch_assoc()) {
                                                        $label_id = $row['id'];
                                                        $label_name = $row['name'];
                                                        echo('<option class="btn-stock" style="margin-top:1px;border:1px solid gray" value="'.$label_id.'">'.$label_name.'</option>');
                                                    }
                                                }
                                            }
                                        echo('
                                        </select>
                                        </div>
                                        <div>
                                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14" onclick="modalLoadProperties(\'label\')">Add New</label>
                                        </div>
                                    </div>');
                                    echo('
                                    <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                        <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c" style="width:300px" value="'.$stock['min_stock'].'"></input></div>
                                    </div>
                                    <div class="nav-row" id="submit-row" style="margin-top:25px">
                                        <div style="width:200px;margin-right:25px"></div>
                                        <div><input id="form-submit" type="submit" value="Save" name="submit" class="nav-v-c btn btn-success" /></div>
                                    </div>
                                    <style>
                                            #labels {
                                            display: inline-block;
                                            padding-top:2px;
                                            padding-bottom:2px;
                                            width: auto;
                                            }
                                            
                                            #labels option {
                                            display: inline-block;
                                            padding: 3px;
                                            margin-right: 10px;
                                            background-color: #f1f1f1;
                                            border: 1px solid #ccc;
                                            border-radius: 5px;
                                            }
                                        </style>
                                        <script>
                                        var selectBox = document.getElementById("labels-init");
                                        var selectedBox = document.getElementById("labels");
                                        var labelsSelected = document.getElementById("labels-selected");
                                        
                                        selectBox.addEventListener("change", function() {
                                            var selectedOption = selectBox.options[selectBox.selectedIndex];
                                            if (selectedOption.value !== "") {
                                                selectedBox.add(selectedOption);
                                            }
                                        });

                                        selectedBox.addEventListener("change", function() {
                                            var removedOption = selectedBox.options[selectedBox.selectedIndex];
                                            if (removedOption.value !== "") {
                                                selectBox.add(removedOption);
                                                selectedBox.remove(selectedBox.selectedIndex);
                                            }
                                        });
                                        
                                        selectedBox.addEventListener("change", function() {
                                            // Reset the selected values array
                                            selectedValues = [];

                                            // Iterate over the selected options and collect their values
                                            var selectedOptions = Array.from(selectedBox.options);
                                            selectedOptions.forEach(function(option) {
                                                selectedValues.push(option.value);
                                            });

                                            // Assign the selected values to the input field
                                            labelsSelected.value = selectedValues.join(", "); // Use desired separator if needed
                                        });

                                        selectBox.addEventListener("change", function() {
                                            // Reset the selected values array
                                            selectedValues = [];

                                            // Iterate over the selected options and collect their values
                                            var selectedOptions = Array.from(selectedBox.options);
                                            selectedOptions.forEach(function(option) {
                                                selectedValues.push(option.value);
                                            });

                                            // Assign the selected values to the input field
                                            labelsSelected.value = selectedValues.join(", "); // Use desired separator if needed
                                        });


                                        </script>
                                </div>
                            </form>
                        </div>
                        

                        <div class="col-sm text-right"  id="stock-info-right"> ');
                        if (!isset($_GET['images']) || ($_GET['images'] !== 'edit')) {
                            if (!empty($stock_img_data)) {
                                echo('<div class="well-nopad bg-dark nav-right" style="margin:20px;padding:0px;width:max-content;margin-left:70px">
                                <div class="nav-row" style="width:315px">');
                                for ($i=0; $i < count($stock_img_data); $i++) {
                                    $ii = $i+1;
                                    if ($i == 0) {
                                        if ($i+1 === count($stock_img_data)) {
                                            $imgWidth = "315px";
                                        } else {
                                            $imgWidth = "235px";
                                        }
                                        echo('
                                        <div class=" thumb bg-dark-m" style="width:'.$imgWidth.';height:235px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:'.$imgWidth.'" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" />
                                        </div>
                                        <span id="side-images" style="margin-left:5px">
                                        ');
                                    } 
                                    if ($i == 1 || $i == 2) {
                                        echo('
                                        <div class="thumb bg-dark-m" style="width:75px;height:75px;margin-bottom:5px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:75px" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                        </div>
                                        ');
                                    }
                                    if ($i == 3) {
                                        if ($i < (count($stock_img_data)-1)) {
                                            echo ('
                                            <div class="thumb bg-dark-m" style="width:75px;height:75px">
                                            <p class="nav-v-c text-center" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-more" style="width:75px">+'.(count($stock_img_data)-3).'</p>
                                            ');
                                        } else {
                                            echo('
                                            <div class="thumb bg-dark-m" style="width:75px;height:75px" onclick="modalLoad(this.children[0])">
                                            <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:75px" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" onclick="modalLoad(this)"/>
                                            ');
                                        }
                                        echo('</div>');
                                    }
                                    if ($i == (count($stock_img_data)-1)) {
                                        echo('<span>');
                                    }
                                }
                                echo('</div>
                                </div>');
                                echo('<div id="edit-images-div" class="nav-div-mid">
                                    <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter(\'\', \'images\', \'edit\'))">
                                        <i class="fa fa-pencil"></i> Edit images
                                    </a>
                                </div> ');
                            } else {
                                echo('<div id="edit-images-div" class="nav-div-mid nav-v-c">
                                    <a id="edit-images" class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter(\'\', \'images\', \'edit\'))">
                                        <i class="fa fa-plus"></i> Add images
                                    </a>
                                </div> ');
                            }
                        } else {
                            if (!empty($stock_img_data)) {
                                echo('<table><tbody>');
                                for ($i=0; $i < count($stock_img_data); $i++) {
                                    $ii = $i+1;
                                        echo('
                                        <tr>
                                            <form action="includes/stock-edit-action.inc.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to unlink this image?\nThe file will remain on the system.\');">
                                                <td class="bg-dark-m" style="padding-right:5px">
                                                    <input type="hidden" name="stock_id" value="'.$stock_img_data[$i]['stock_id'].'" />
                                                    <input type="hidden" name="img_id" value="'.$stock_img_data[$i]['id'].'" />
                                                    <input type="hidden" name="submit" value="image-delete" />
                                                    <div class="thumb bg-dark-m" style="width:75px;height:75px;margin-bottom:5px" onclick="modalLoad(this.children[0])">
                                                        <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="width:75px" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                                    
                                                    </div>
                                                </td>
                                                <td class="bg-dark-m uni" style="font-size:14">assets/img/stock/'.$stock_img_data[$i]['image'].'</td>
                                                <td class="bg-dark-m" style="padding-left:10px;padding-right:10px">
                                                    <button id="edit-images" class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>

                                        ');
                                }
                                echo('</tbody></table>');
                            } 
                            echo('<div id="edit-images-div" class="nav-div-mid" style="margin-top:10px">
                                    <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="modalLoadSelection()">
                                        <i class="fa fa-plus"></i> Add existing image
                                    </a>
                                    <a id="edit-images" class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="modalLoadUpload()">
                                        <i class="fa fa-plus"></i> Add new image
                                    </a>
                                </div> ');
                        }
                        echo('
                        </div>
                    </div>
                </div>
                
            ');
            }
        }

    }
}








?>


<!-- Modal Image Selection Div -->
<div id="modalDivSelection" class="modal">
<!-- <div id="modalDivSelection" style="display: block;"> -->
    <span class="close" onclick="modalCloseSelection()">&times;</span>
    <div class="container well-nopad bg-dark" style="padding:25px">
        <div  class="well-nopad bg-dark" style="overflow-y:auto; height:450px">
            <?php
                $filepath = 'assets/img/stock';
                $files = array_values(array_diff(scandir($filepath), array('..', '.')));
                // print_r($files);
                echo('<div class="nav-row">');
                for ($f=0; $f<count($files); $f++) {
                    echo('<div class="thumb bg-dark-m" id="add-image-'.$f.'-div" style="width:200px;height:200px;margin:2px"><img class="nav-v-c" id="add-image-'.$f.'" style="width:200px" alt="'.$files[$f].'" src="'.$filepath.'/'.$files[$f].'" onclick="modalImageInputFill(this);"/></div>');
                }
                echo('</div>');

            ?>
        </div>
        <form action="includes/stock-edit-action.inc.php" method="POST" enctype="multipart/form-data">
            <div class="nav-row well-nopad bg-dark">
                <div class="nav-row" style="padding:25px 50px 25px 50px;width:750px">
                    <div>
                        <input class="nav-v-c form-control" style="height:35px;width:500px;background-color:#adadad !important; color:black !important" name="img-file-name-visible" id="img-file-name-visible" type="text" placeholder="path/to/file.png" disabled />
                        <input type="hidden" id="img-file-name" name="img-file-name"/>
                        <input type="hidden" id="stock_id" name="stock_id" value="<?php echo(isset($stock_id)?$stock_id:''); ?>" />
                    </div>
                    <div style="padding-left:25px">
                        <input class="btn btn-success" type="submit" name="submit" value="Add Image" />
                    </div>
                </div>
                <div class="thumb bg-dark-m" style="width:85px;height:85px;margin:2px">
                    <img class="nav-v-c" id="img-selected-thumb" style="width:85px" />
                </div>
            </div>
        </form>
    </div> 
</div>
<!-- End of Modal Image Selection Div -->

<!-- Modal Image Upload Div -->
<div id="modalDivUpload" class="modal">
    <span class="close" onclick="modalCloseUpload()">&times;</span>
    <div class="container well-nopad bg-dark" style="padding:25px">
        <div style="width:250px;height:250px;margin:auto">
            <img class="nav-v-c" id="upload-img-pre" style="max-width:250px;max-height:250px" />
        </div>
        <div style="margin:auto;text-align:center;margin-top:10px">
            <form action="includes/stock-edit-action.inc.php" method="POST" enctype="multipart/form-data">
                <input type="file" accept="image/*" style="margin:auto;text-align:center" id="image" name="image" onchange="loadImage(event)"><br><br>
                <input type="hidden" id="upload_stock_id" name="stock_id" value="<?php echo(isset($_GET['stock_id'])?$_GET['stock_id']:''); ?>" />
                <input type="submit" name="submit" class="btn btn-success" value="Upload" />
                <script>
                var loadImage = function(event) {
                    var preview = document.getElementById('upload-img-pre');
                    preview.src = URL.createObjectURL(event.target.files[0]);
                    preview.onload = function() {
                    URL.revokeObjectURL(preview.src) // free memory
                    }
                };
                </script>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Image Upload Div -->

<!-- Modal Image Div -->
<div id="modalDiv" class="modal" onclick="modalClose()">
    <span class="close" onclick="modalClose()">&times;</span>
    <img class="modal-content bg-trans" id="modalImg">
    <div id="caption" class="modal-caption"></div>
</div>
<!-- End of Modal Image Div -->



<script> // MODAL SCRIPT bits
    // Get the modal
    function modalLoadSelection() {
        var modal = document.getElementById("modalDivSelection");
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseSelection = function() { 
        var modal = document.getElementById("modalDivSelection");
        modal.style.display = "none";
    }

    // Populate the input box value
    function modalImageInputFill(element) {
        var inputBoxVisible = document.getElementById('img-file-name-visible');
        var inputBox = document.getElementById('img-file-name');

        var imageThumb = document.getElementById('img-selected-thumb');

        inputBoxVisible.value = '/assets/img/stock/'+element.alt;
        inputBox.value = element.alt;
        imageThumb.src = '/assets/img/stock/'+element.alt;
        imageThumb.alt = element.alt;
    }

    function modalLoadUpload() {
        var modal = document.getElementById("modalDivUpload");
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseUpload = function() { 
        var modal = document.getElementById("modalDivUpload");
        modal.style.display = "none";
    }
</script>

<?php include 'includes/stock-new-properties.inc.php'; ?>