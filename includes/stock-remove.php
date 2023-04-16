<?php
// INCLUDED IN THE STOCK PAGE FOR NEW STOCK OR INVENTORY TO CURRENT STOCK

// Query string bits
$stock_id = isset($_GET['stock_id']) ? $_GET['stock_id'] : '';

// include 'head.php';
?>
<!-- <div style="margin-bottom:200px"></div> -->

<!-- NEW STOCK -->
<h2 class="red">IN PROGRESS - COPY OF ADD FOR TESTING </h2>
<div class="container well-nopad bg-dark">
    <form action="includes/new-stock.inc.php" method="POST" style="max-width:max-content;margin-bottom:0">
    <?php
    if (is_numeric($stock_id)) {
        if ($stock_id == 0 || $stock_id == '0') {
            echo('
            <div class="container well-nopad bg-dark" style="margin-bottom:5px">
                <h3 style="font-size:22px; margin-left:25px">Add New Stock</h3>
                <div class="row">
                    <div class="col-sm text-left" id="stock-info-left">
                        <div class="nav-row" style="margin-bottom:25px">
                            <div class="nav-row" id="name-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                                <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c" style="width:300px" required></input></div>
                            </div>
                            <div class="nav-row" id="sku-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                                <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c" style="width:300px"></input></div>
                            </div>
                            <div class="nav-row" id="description-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                                <div><textarea class="form-control nav-v-c" id="description" name="description" rows="3" cols="32" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary"></textarea></div>
                            </div>
                            <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                                <div><input type="text" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c" style="width:300px"></input></div>
                            </div>
                            <div class="nav-row" id="labels-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="labels" id="labels-label">Labels</label></div>
                                <div><input type="text" name="labels" placeholder="Labels - allow multiple" id="labels" class="form-control nav-v-c" style="width:300px"></input></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm text-right"  id="stock-info-right"> 
                        <div id="image-preview" style="height:150px"></div>
                        <div class="nav-row"  id="labels-row" style="margin-top:25px">
                            <div class="nav-right" style="margin-right:25px"><label class="nav-v-c" style="width:100%" for="labels" id="labels-label">Image:</label></div>
                            <div><input class="nav-v-c" type="file" style="width: 350px" id="image" name="image"></div>
                        </div>
                    </div>
                </div>
            </div>
            ');
        }
        echo('
            <div class="container well-nopad bg-dark">
                <div class="row">
                    <div class="col-sm text-left" id="stock-info-left">
                        <div class="nav-row" style="margin-bottom:25px">
                            <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                <div><input type="text" name="manufacturer" placeholder="Manufacturer - make drop down" id="manufacturer" class="form-control nav-v-c" style="width:300px"></input></div>
                            </div>
                            <div class="nav-row" id="site-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site</label></div>
                                <div><input type="text" name="site" placeholder="Site - make drop down" id="site" class="form-control nav-v-c" style="width:300px" required></input></div>
                            </div>
                            <div class="nav-row" id="area-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area</label></div>
                                <div><input type="text" name="area" placeholder="Area - make drop down" id="area" class="form-control nav-v-c" style="width:300px" required></input></div>
                            </div>
                            <div class="nav-row" id="shelf-row" style="margin-top:25px">
                                <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf</label></div>
                                <div><input type="text" name="shelf" placeholder="Shelf - make drop down" id="shelf" class="form-control nav-v-c" style="width:300px" required></input></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm text-right"  id="stock-info-right"> 
                        Right section
                    </div>
                </div>
            </div>
        ');
    } else {
        echo('<p> ADD <a class="link" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">NEW</a> OR <a class="link" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 1))">EXISTING (id=1)</a> </p>');
    }
    
    ?>
    </form>
</div>









