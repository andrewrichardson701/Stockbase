<?php
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.


if (isset($_GET['stock_id'])) {
    if (is_numeric($_GET['stock_id'])) {
        $stock_id = htmlspecialchars($_GET['stock_id']);

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

                $sql_tag = "SELECT stock_tag.id AS stock_tag_id, stock_tag.stock_id AS stock_tag_stock_id, stock_tag.tag_id AS stock_tag_tag_id,
                                    tag.id AS tag_id, tag.name AS tag_name
                                FROM stock_tag 
                                INNER JOIN tag ON stock_tag.tag_id=tag.id
                                WHERE stock_id=?
                                ORDER BY tag.name";
                $stmt_tag = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_tag, $sql_tag)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_tag, "s", $stock_id);
                    mysqli_stmt_execute($stmt_tag);
                    $result_tag = mysqli_stmt_get_result($stmt_tag);
                    $rowCount_tag = $result_tag->num_rows;
                    if ($rowCount_tag < 1) {
                        //no images
                        $stock_tag_data = '';
                    } else {
                        while ($row_tag = $result_tag->fetch_assoc()) {
                            $stock_tag_data[] = array('id' => $row_tag['tag_id'], 'name' => $row_tag['tag_name'], 'stock_id' => $row_tag['stock_tag_stock_id']);
                        }
                    }
                }

                        

                // print_r($stock_img);
                echo ('
                
                <div class="container well-nopad theme-divBg" style="margin-bottom:5px">
                    <div class="row">
                        <div class="col-sm-7 text-left" id="stock-info-left">
                            <form id="edit-form" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                                <!-- below input is used for the stock-modify.inc.php page -->
                                <!-- Include CSRF token in the form -->
                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                <input type="hidden" name="stock-edit" value="1" />
                                <div class="nav-row" style="margin-bottom:25px">
                                    <div class="nav-row" id="id-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="id" id="id-label">ID</label></div>
                                        <div><input type="text" name="id-visible" placeholder="X" id="id-visible" class="form-control nav-v-c stock-inputSize" style="color:black;background-color:#adadad !important" value="'.$stock['id'].'" disabled></input></div>
                                        <input type="hidden" name="id" id="id" value="'.$stock['id'].'" />
                                    </div>
                                    <div class="nav-row" id="name-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                        <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c stock-inputSize" value="'.htmlspecialchars($stock['name'], ENT_QUOTES, 'UTF-8').'" required></input></div>
                                    </div>
                                    <div class="nav-row" id="sku-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                        <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c stock-inputSize" value="'.$stock['sku'].'" pattern="^[A-Za-z0-9\p{P}]+$"></div>
                                    </div>
                                    <div class="nav-row" id="description-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                        <div><textarea class="form-control nav-v-c stock-inputSize" id="description" name="description" rows="3" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="'.htmlspecialchars($stock['description'], ENT_QUOTES, 'UTF-8').'" >'.str_replace(array("\r\n","\\r\\n"), "&#010;", $stock['description']).'</textarea></div>
                                    </div>');
                                    if ($stock['is_cable'] == 0) {
                                        echo('
                                        <div class="nav-row" id="tags-row" style="margin-top:25px">
                                            <div class="stock-inputLabelSize" style="max-width:200px"><label class="text-right" style="padding-top:5px;width:100%" for="tags" id="tags-label">Tags</label></div>
                                            <div id="tags-group">
                                            <input id="tags-selected" name="tags-selected" type="hidden" />
                                            <select id="tags" name="tags[]" multiple class="form-control nav-trans stock-inputSize" style="border: 1px solid grey;display: inline-block; height:90px; white-space:wrap">');
                                                if (is_array($stock_tag_data)) {
                                                    for ($l=0; $l<count($stock_tag_data); $l++) {
                                                        // echo('<option class="btn btn-dark btn-stock gold fafont" style="margin-top:4px;border:1px solid gray" value="'.$stock_tag_data[$l]['id'].'">'.$stock_tag_data[$l]['name'].' &#xf057;</option> ');
                                                        echo('<option class="btn-stock clickable" style="margin-top:1px;border:1px solid gray" value="'.$stock_tag_data[$l]['id'].'" selected>'.$stock_tag_data[$l]['name'].' ✕</option> ');
                                                    }
                                                } else {
                                                    //echo('None');
                                                }
                                            echo('
                                            </select>
                                            <select class="form-control stock-inputSize" id="tags-init" name="tags-init" style="margin-top:2px">
                                                <option value="" selected>-- Add Tags --</option>');
                                                include 'includes/dbh.inc.php';
                                                $sql = "SELECT id, name
                                                        FROM tag
                                                        WHERE tag.id NOT IN (SELECT tag_id FROM stock_tag WHERE stock_id = '$stock_id')
                                                        ORDER BY name";
                                                $stmt = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                    // fails to connect
                                                } else {
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    $rowCount = $result->num_rows;
                                                    if ($rowCount < 1) {
                                                        echo('<option value="0" selected disabled>No Tags Remaining</option>');
                                                    } else {
                                                        // rows found
                                                        while ($row = $result->fetch_assoc()) {
                                                            $tag_id = $row['id'];
                                                            $tag_name = $row['name'];
                                                            echo('<option class="btn-stock" style="margin-top:1px;border:1px solid gray" value="'.$tag_id.'">'.$tag_name.'</option>');
                                                        }
                                                    }
                                                }
                                            echo('
                                            </select>
                                            </div>
                                            <div class="viewport-large">
                                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties(\'tag\')">Add New</label>
                                            </div>
                                        </div>
                                        <div class="nav-row viewport-small" id="tags-row-add">
                                            <div class="stock-inputLabelSize" style="max-width:200px">
                                            </div>
                                            <div style="width:max-content">
                                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties(\'tag\')">Add New</label>
                                            </div>
                                        </div>');
                                    }
                                    echo('
                                    <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                        <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c stock-inputSize" value="'.$stock['min_stock'].'"></input></div>
                                    </div>
                                    <div class="nav-row" id="submit-row" style="margin-top:25px">
                                        <div class="stock-inputLabelSize" style="max-width:200px"></div>
                                        <div class="stock-inputSize"><input id="form-submit" type="submit" value="Save" name="submit" class="nav-v-c btn btn-success" /></div>
                                        <div>');
                                        if (isset($_GET['images']) && ($_GET['images'] == 'edit')) {
                                            // echo('<button type="button" style="margin-left:150px" class="nav-v-c btn btn-warning" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'images\', \'\'), \'modify\', \'\'))">Cancel</button>');
                                        } else {
                                            echo('<button type="button" class="nav-v-c btn btn-warning" onclick="navPage(updateQueryParameter(\'\', \'modify\', \'\'))">Cancel</button>');
                                        }
                                        echo('</div>
                                    </div>
                                    <style>
                                            #tags {
                                            display: inline-block;
                                            padding-top:2px;
                                            padding-bottom:2px;
                                            width: auto;
                                            }
                                            
                                            #tags option {
                                            display: inline-block;
                                            padding: 3px;
                                            margin-right: 10px;
                                            background-color: #f1f1f1;
                                            border: 1px solid #ccc;
                                            border-radius: 5px;
                                            }
                                        </style>
                                        <script>
                                        var selectBox = document.getElementById("tags-init");
                                        var selectedBox = document.getElementById("tags");
                                        var tagsSelected = document.getElementById("tags-selected");
                                        
                                        selectBox.addEventListener("change", function() {
                                            var selectedOption = selectBox.options[selectBox.selectedIndex];
                                            selectedOption.innerHTML += " ✕";
                                            selectedOption.classList.add("clickable");
                                            if (selectedOption.value !== "") {
                                                selectedBox.add(selectedOption);
                                            }
                                        });

                                        selectedBox.addEventListener("change", function() {
                                            var removedOption = selectedBox.options[selectedBox.selectedIndex];
                                            var tempHTML = removedOption.innerHTML;
                                            var newHTML = tempHTML.replace(" ✕", "");
                                            removedOption.innerHTML = newHTML;
                                            removedOption.classList.remove("clickable");
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
                                            tagsSelected.value = selectedValues.join(", "); // Use desired separator if needed
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
                                            tagsSelected.value = selectedValues.join(", "); // Use desired separator if needed
                                        });


                                        </script>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col text-right" id="stock-info-right">');  
                        if (!isset($_GET['images']) || ($_GET['images'] !== 'edit')) {
                            if (!empty($stock_img_data) && $stock_img_data !== null && $stock_img_data !== '') {
                                echo('<div class="well-nopad theme-divBg nav-right stock-imageBox">
                                <div class="nav-row stock-imageMainSolo">');
                                for ($i=0; $i < count($stock_img_data); $i++) {
                                    $ii = $i+1;
                                    if ($i == 0) {
                                        if ($i+1 === count($stock_img_data)) {
                                            $imgClass = "stock-imageMainSolo";
                                        } else {
                                            $imgClass = "stock-imageMain";
                                        }
                                        echo('
                                        <div class="thumb theme-divBg-m text-center '.$imgClass.'" onclick="modalLoadCarousel()">
                                            <img class="nav-v-c '.$imgClass.'" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" />
                                        </div>
                                        <span id="side-images" style="margin-left:5px">
                                        ');
                                    } 
                                    if ($i == 1 || $i == 2) {
                                        echo('
                                        <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                            <img class="nav-v-c stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                        </div>
                                        ');
                                    }
                                    if ($i == 3) {
                                        if ($i < (count($stock_img_data)-1)) {
                                            echo ('
                                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                            <p class="nav-v-c text-center stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-more">+'.(count($stock_img_data)-3).'</p>
                                            ');
                                        } else {
                                            echo('
                                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                            <img class="nav-v-c stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" onclick="modalLoad(this)"/>
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
                                // echo('<div id="edit-images-div" class="nav-div-mid">
                                //     <button id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'modify\', \'edit\'), \'images\', \'edit\'))">
                                //         <i class="fa fa-pencil"></i> Edit images
                                //     </button>
                                // </div> ');
                                echo('<div id="edit-images-div" class="nav-right text-center stock-imageMainSolo" style="margin-right:20px; height:max-content !important">
                                    <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter(\'\', \'images\', \'edit\'))">
                                        <i class="fa fa-pencil"></i> Edit images
                                    </a>
                                </div> ');
                                if (count($stock_img_data) == 1) {
                                    echo('
                                    <!-- Modal Image Div -->
                                    <div id="modalDivCarousel" class="modal" onclick="modalCloseCarousel()">
                                        <span class="close" onclick="modalCloseCarousel()">&times;</span>');
                                        for ($b=0; $b < count($stock_img_data); $b++) {
                                            echo('
                                            <img class="modal-content bg-trans modal-imgWidth" id="stock-'.$stock_img_data[$b]['stock_id'].'-img-'.$stock_img_data[$b]['id'].'" src="assets/img/stock/'.$stock_img_data[$b]['image'].'"/>
                                            ');
                                        }
                                        echo('
                                        <img class="modal-content bg-trans" id="modalImg">
                                        <div id="caption" class="modal-caption"></div>
                                    </div>
                                    <!-- End of Modal Image Div -->
                                    ');
                                } else {
                                    echo('
                                    <link rel="stylesheet" href="./assets/css/carousel.css">
                                    <script src="assets/js/carousel.js"></script>
                                    <!-- Modal Image Div -->
                                    <div id="modalDivCarousel" class="modal">
                                        <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                        <img class="modal-content bg-trans" id="modalImg">
                                            <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center" style="margin-left:10vw; margin-right:10vw">
                                                <!-- Indicators -->
                                                <ol class="carousel-indicators">');
                                                for ($a=0; $a < count($stock_img_data); $a++) {
                                                    if ($a == 0) { $active = ' class="active"'; } else { $active = ''; }
                                                    echo('<li data-target="#myCarousel" data-slide-to="'.$a.'"'.$active.'></li>');
                                                }
                                                echo('
                                                </ol>

                                                <!-- Wrapper for slides -->
                                                <div class="carousel-inner" align="centre">');
                                                for ($b=0; $b < count($stock_img_data); $b++) {
                                                    if ($b == 0) { $active = " active"; } else { $active = ''; }
                                                    $bb = $b+1;
                                                    echo('
                                                    <div class="item'.$active.'" align="centre">
                                                    <img class="modal-content bg-trans modal-imgWidth" id="stock-'.$stock_img_data[$b]['stock_id'].'-img-'.$stock_img_data[$b]['id'].'" src="assets/img/stock/'.$stock_img_data[$b]['image'].'"/>
                                                        <div class="carousel-caption">
                                                            <h3></h3>
                                                            <p></p>
                                                        </div>
                                                    </div>
                                                    ');
                                                }
                                                    echo('
                                                </div>

                                                <!-- Left and right controls -->
                                                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                    <i class="fa fa-chevron-left" style="position:absolute; top:50%; margin-top:-5px"></i>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                    <i class="fa fa-chevron-right" style="position:absolute; top:50%; margin-top:-5px"></i>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        <div id="caption" class="modal-caption"></div>
                                    </div>
                                    <!-- End of Modal Image Div -->
                                    ');
                                }
                            } else {
                                echo('<div id="edit-images-div" class="nav-div-mid nav-v-c">
                                    <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'modify\', \'edit\'), \'images\', \'edit\'))">
                                        <i class="fa fa-plus"></i> Add images
                                    </button>
                                </div> ');
                            }
                        } else {
                            if (!empty($stock_img_data)) {
                                echo('<table style="width:100%"><tbody>');
                                for ($i=0; $i < count($stock_img_data); $i++) {
                                    $ii = $i+1;
                                        echo('
                                        <tr>
                                            <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to unlink this image?\nThe file will remain on the system.\');">
                                                <!-- Include CSRF token in the form -->
                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                <input type="hidden" name="stock-edit" value="1" />
                                                <td class="theme-divBg-m" style="padding-right:5px">
                                                    <input type="hidden" name="stock_id" value="'.$stock_img_data[$i]['stock_id'].'" />
                                                    <input type="hidden" name="img_id" value="'.$stock_img_data[$i]['id'].'" />
                                                    <input type="hidden" name="submit" value="image-delete" />
                                                    <div class="thumb theme-divBg-m text-center" style="width:75px;height:75px;margin:5px" onclick="modalLoad(this.children[0])">
                                                        <img class="nav-v-c" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" style="max-height:80px; max-width:75px" alt="'.$stock['name'].' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                                    
                                                    </div>
                                                </td>
                                                <td class="theme-divBg-m uni" style="font-size:14px">assets/img/stock/'.$stock_img_data[$i]['image'].'</td>
                                                <td class="theme-divBg-m" style="padding-left:10px;padding-right:10px">
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
                                    </a><br>
                                    <button type="button" style="margin-top:15px" class="nav-v-b btn btn-warning" onclick="navPage(updateQueryParameter(\'\', \'images\', \'\'))">Cancel</button>
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
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div  class="well-nopad theme-divBg" style="overflow-y:auto; height:450px">
            <?php
                $filepath = 'assets/img/stock';
                $files = array_values(array_diff(scandir($filepath), array('..', '.')));
                // print_r($files);
                echo('<div class="nav-row">');
                for ($f=0; $f<count($files); $f++) {
                    echo('<div class="thumb theme-divBg-m" id="add-image-'.$f.'-div" style="width:200px;height:200px;margin:2px"><img class="nav-v-c" id="add-image-'.$f.'" style="width:200px" alt="'.$files[$f].'" src="'.$filepath.'/'.$files[$f].'" onclick="modalImageInputFill(this);"/></div>');
                }
                echo('</div>');

            ?>
        </div>
        <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="stock-edit" value="1" />
            <div class="nav-row well-nopad theme-divBg">
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
                <div class="thumb theme-divBg-m" style="width:85px;height:85px;margin:2px">
                    <img class="nav-v-c" id="img-selected-thumb" style="width:85px" />
                </div>
                <div style="padding-left:100px" class="">
                    <button class="btn btn-warning nav-v-c" type="button" onclick="modalCloseSelection()">Cancel</button>
                </div>
            </div>
        </form>
    </div> 
</div>
<!-- End of Modal Image Selection Div -->

<!-- Modal Image Upload Div -->
<div id="modalDivUpload" class="modal">
    <span class="close" onclick="modalCloseUpload()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div style="width:250px;height:250px;margin:auto">
            <img class="nav-v-c" id="upload-img-pre" style="max-width:250px;max-height:250px" />
        </div>
        <div style="margin:auto;text-align:center;margin-top:10px">
            <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="stock-edit" value="1" />
                <input type="file" accept="image/*" style="margin:auto;text-align:center" id="image" name="image" onchange="loadImage(event)"><br><br>
                <input type="hidden" id="upload_stock_id" name="stock_id" value="<?php echo(isset($_GET['stock_id'])?$_GET['stock_id']:''); ?>" />
                <input type="submit" name="submit" class="btn btn-success" value="Upload" style="margin-right:25px"/><button class="btn btn-warning" type="button"  onclick="modalCloseUpload()">Cancel</button>
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

    function modalLoadCarousel() {
        var modal = document.getElementById("modalDivCarousel");
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal or if they click the image.
    modalCloseCarousel = function() { 
        var modal = document.getElementById("modalDivCarousel");
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