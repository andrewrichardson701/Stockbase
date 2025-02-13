{{ dd($params) }}
<?php   
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// SHOWS THE INFORMATION FOR EACH PEICE OF STOCK AND ITS LOCATIONS ETC. 
// id QUERY STRING IS NEEDED FOR THIS
include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="content">
        @include('includes.stock.new-properties')

        @if(!is_numeric($params['stock_id']) 
            @if(!empty($params['modify_type']))
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter('', 'stock_id', 0))">add new stock item</a>.</p></div>
            @else
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>
            @endif
        @endif

        <!-- Get Inventory -->
        @if(!empty($params['modify_type']))
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:5px">Stock - {{ ucwords($params['modify_type']) }}</h2>
                {!! $response_handling !!}
            </div>
            @include('includes.stock.stock-' . $stock_modify)
        @else
            @if (($stock_data['count'] ?? 0) < 1)
                <div class="container" id="no-stock-found">No Stock Found</div>
            @else
                <script src="assets/js/favourites.js"></script>
                        <div id="favouriteButton" class="" style="width: max-content">
                            <button onclick="favouriteStock({{ $params['stock_id'] }})" class="favouriteBtn" id="favouriteBtn" title="Favourite Stock">
                                <i id="favouriteIcon" class=" @if (($favourited ?? 0) == 1) fa-solid @else fa-regular @endif fa-star"></i>
                            </button>
                        </div>
                            <div class="container stock-heading">
                                <div class="row">
                                    <div class="col">
                                        <h2 class="header-small" style="padding-bottom:0px">Stock</h2>
                                    </div>
                                    <div class="col nav-div nav-right" style="margin-bottom: 5px;max-width:max-content; width:max-content;margin-right:0px !important">
                                        <div class="nav-row">
                                            <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                                <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/edit')">
                                                    <i class="fa fa-pencil"></i><or class="viewport-large-empty"> Edit</or>
                                                </button>
                                            </div> 
                                            <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                                <button id="add-stock" class="btn btn-success theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/add')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                                    <i class="fa fa-plus"></i><or class="viewport-large-empty"> Add</or>
                                                </button>
                                            </div> 
                                            <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                                <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/remove')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                                    <i class="fa fa-minus"></i><or class="viewport-large-empty"> Remove</or>
                                                </button>
                                            </div> 
                                            <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                                <button id="transfer-stock" class="btn btn-warning nav-v-b stock-modifyBtn" style="color:black" onclick="navPage('stock/{{ $stock_id }}/move')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                                    <i class="fa fa-arrows-h"></i><or class="viewport-large-empty"> Move</or>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! $response_handling !!}
                                <div class='row ' style='margin-top:5px;margin-top:10px;'>
                                    <div class='col' style='margin-top:auto;margin-bottom:auto;'>
                                        <h3 style='font-size:22px;margin-bottom:0px;' id='stock-name'>{{ $stock_data['name'] }}({{ $stock_data['sku'] }})</h3>
                                        <input type='hidden' id='hiddenStockName' value='".$stock_name."'>
                                    </div>
                                    
                                </div>
                                <p id='stock-description' style='color:#898989;margin-bottom:0px;margin-top:10px'>{{ str_replace(array("\r\n","\\r\\n"), "<br/>", $stock_data['description']) }}</p>
                                @if ($stock_data['deleted'] == 1)
                                    <p class="red" style="margin-top:20px;font-size:20">Stock Deleted. <a class="link" style="font-size:20" href="admin.php#stockmanagement-settings">Restore?</a></p>
                                @endif
                            </div>

                            <!-- Modal Image Div -->
                            <div id="modalDiv" class="modal" onclick="modalClose()">
                                <span class="close" onclick="modalClose()">&times;</span>
                                <img class="modal-content bg-trans modal-imgWidth" id="modalImg">
                                <div id="caption" class="modal-caption"></div>
                            </div>
                            <!-- End of Modal Image Div -->

                            <div class="container well-nopad theme-divBg">
                                <div class="row">
                                    <div class="col-sm-7 text-left" id="stock-info-left">
                                        <table class="" id="stock-info-table" style="max-width:max-content">
                                            <thead>
                                                <tr>
                                                    <th hidden>id</th>
                                                    <th>Site</th>
                                                    <th style="padding-left: 10px">Location</th>
                                                    <th style="padding-left: 5px">Shelf</th>
                                                    <th style="padding-left: 5px">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>');
                                        @foreach ($stock_inv_data['rows'] as $key => $row)
                                            @if (($row['quantity'] ?? 0) !== 0)
                                                <tr id="stock-row-{{ $key }}">
                                                    <td hidden>{{ $row['id'] }}</td>
                                                    <td id="stock-row-{{ $key }}-site-{{ $row['site_id'] }}"><or class="clickable" onclick="window.location.href='/?site={{ $row['site_id'] }}">{{ $row['site_name'] }}</or></td>
                                                    <td id="stock-row-{{ $key }}-area-{{ $row['area_id'] }}" style="padding-left: 10px"><or class="clickable" onclick="window.location.href='/?site={{ $row['site_id'] }}&area={{ $row['area_id'] }}'">{{ $row['area_name'] }}</or>:</td>
                                                    <td id="stock-row-{{ $key }}-shelf-{{ $row['shelf_id'] }}" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold clickable" onclick="window.location.href='/?shelf={{ str_replace(' ', '+', $row['shelf_name']) }}'">{{ $row['shelf_name'] }}</button></td>
                                                    <td style="padding-left: 5px" class="text-center theme-textColor">{{ $row['quantity'] }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if (($stock_inv_data['total_quantity'] ?? 0) == 0)
                                            <tr id="stock-row-na-0">
                                                <td colspan=100% style="padding-left: 5px" class="text-center">N/A</td>
                                            </tr>
                                        @endif

                                        
                                            </tbody>
                                        </table>
                                        <p id="min-stock"><strong>Minimum Stock Count:</strong> <or class="specialColor">$stock_data['min_stock']</or></p>

                                        @if ($stock_data['is_cable'] == 0)
                                            <p class="clickable gold" id="extra-info-dropdown" onclick="toggleSection(this, 'extra-info')">More Info <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></p> 
                                            <div id="extra-info" hidden>
                                                <p id="tags-head"><strong>Tags</strong></p>
                                                <p id="tags">
                                                @if (is_array($stock_inv_data['tags'])) 
                                                    @foreach($stock_inv_data['tags'] as $key => $tag)
                                                        <button class="btn theme-btn btn-stock-click gold clickable" id="tag-{{ $tag['id'] }}" onclick="window.location.href='/?tag={ $tag['name'] }}'">{{ $tag['name'] }}</button> 
                                                    @endforeach
                                                @else
                                                    None
                                                @endif
                                                </p>
                                                <p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers">
                                                @if (is_array($stock_inv_data['manufacturers'])) 
                                                    @foreach($stock_inv_data['manufacturers'] as $key => $manufacturer)
                                                        <button class="btn theme-btn btn-stock-click gold clickable" id="manufacturer-{{ $manufacturer['id'] }}" onclick="window.location.href='/?manufacturer={ $manufacturer['name'] }}'">{{ $manufacturer['name'] }}</button> 
                                                    @endforeach
                                                @else
                                                    None
                                                @endif
                                                </p>
                                                <p id="serial-numbers-head"><strong>Serial Numbers</strong></p>
                                                <p>
                                                @foreach ($serial_numbers as $key => $row)
                                                    <a class="serial-bg" id="serialNumber-{{ $key }}">$row['serial_number']</a>
                                                @endforeach
                                                </p>
                                    
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col text-right" id="stock-info-right">
                                    @if (!empty($stock_data['img_data']))  
                                        <div class="well-nopad theme-divBg nav-right stock-imageBox">
                                            <div class="nav-row stock-imageMainSolo">
                                            @foreach ($stock_data['img_data']['rows'] as $key => $row)
                                                @if ($loop->iteration == 1)
                                                    
                                                    @if ($stock_data['img_data']['count'] <= 1)
                                                        <div class="thumb theme-divBg-m text-center stock-imageMainSolo" onclick="modalLoadCarousel()">
                                                            <img class="nav-v-c stock-imageMainSolo" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                                        </div>
                                                        <span id="side-images" style="margin-left:5px">
                                                    @else 
                                                        <div class="thumb theme-divBg-m text-center stock-imageMain" onclick="modalLoadCarousel()">
                                                            <img class="nav-v-c stock-imageMain" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                                        </div>
                                                        <span id="side-images" style="margin-left:5px">
                                                    @endif
                                                @endif
                                                
                                                @if ($loop->iteration == 2 || $loop->iteration == 3)
                                                    <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                                        <img class="nav-v-c stock-imageOther" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                                    </div>
                                                @endif
                                                @if ($loop->iteration == 4)
                                                    @if ($loop->iteration < $stock_data['img_data']['count'])
                                                    <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                                        <p class="nav-v-c text-center stock-imageOther" id="stock-{{ $stock_data['id'] }-img-more">+{{ $stock_data['img_data']['count']-3 }}</p>
                                                    @else
                                                    <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                                        <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }-img-{{  $row['id'] }}" src="img/stock/{{ $row['image'] }}" onclick="modalLoad(this)"/>
                                                    @endif
                                                    </div>
                                                @endif
                                                @if ($loop->last || $loop->iteration == 4)
                                                    </span>
                                                    @break
                                                @endif
                                            @endforeach
                                            </div>
                                        </div>

                                        @if ($stock_data['img_data']['count'] == 1)
                                        <!-- Modal Image Div -->
                                        <div id="modalDivCarousel" class="modal" onclick="modalCloseCarousel()">
                                            <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                            @foreach($stock_data['img_data']['rows'] as $key => $row)
                                                <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" src="img/stock/{{  $row['image'] }}"/>
                                            @endforeach
                                            <img class="modal-content bg-trans" id="modalImg">
                                            <div id="caption" class="modal-caption"></div>
                                        </div>
                                        <!-- End of Modal Image Div -->
                                        @else 
                                        <link rel="stylesheet" href="css/carousel.css">
                                        <script src="js/carousel.js"></script>
                                        <!-- Modal Image Div -->
                                        <div id="modalDivCarousel" class="modal">
                                            <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                            <img class="modal-content bg-trans" id="modalImg">
                                                <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center" style="margin-left:10vw; margin-right:10vw">
                                                    <!-- Indicators -->
                                                    <ol class="carousel-indicators">
                                                    @for ($a=0; $a < $stock_data['img_data']['count']; $a++)
                                                        @if ($a == 0)
                                                        <li data-target="#myCarousel" data-slide-to="{{ $a }}"></li>
                                                        @else
                                                        <li data-target="#myCarousel" data-slide-to="{{ $a }}" class="active"></li>
                                                        @endif
                                                        
                                                    @endfor
                                                    </ol>

                                                    <!-- Wrapper for slides -->
                                                    <div class="carousel-inner" align="centre">');
                                                    @foreach ($stock_data['img_data']['rows'] as $key => $row)
                                                        <div class="item @if ($loop->iteration == 1) active @endif " align="centre">
                                                        <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id']}}-img-{{ $row['id'] }}" src="img/stock/{{ $row['image'] }}"/>
                                                            <div class="carousel-caption">
                                                                <h3></h3>
                                                                <p></p>
                                                            </div>
                                                        </div>
                                                    @endforeach
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
                                        @endif
                                    @else
                                        <div id="edit-images-div" class="nav-div-mid nav-v-c">
                                            <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter('stock/{{ $stock_data['id'] }}/edit', \'images\', \'edit\'))">
                                                <i class="fa fa-plus"></i> Add images
                                            </button>
                                        </div> 
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="container well-nopad theme-divBg" style="margin-top:5px">
                                <h2 style="font-size:22px">Stock</h2>








                                
            @endif
        @endif

        
        <?php
        $stock_modify_values = ['add', 'remove', 'edit', 'move'];
        if (isset($stock_modify) && in_array(strtolower($stock_modify), $stock_modify_values)) {
            echo('<div class="container" style="padding-bottom:25px">
            <h2 class="header-small" style="padding-bottom:5px">Stock - '.ucwords($stock_modify).'</h2>');
            showResponse(); 
            echo('</div>');
            include 'includes/stock-'.$stock_modify.'.inc.php';

        } else {
            include 'includes/dbh.inc.php';
            $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, stock.is_cable AS stock_is_cable,
                                stock_img.id AS stock_img_id, stock_img.stock_id AS stock_img_stock_id, stock_img.image AS stock_img_image, stock.deleted AS stock_deleted
                        FROM stock
                        LEFT JOIN stock_img ON stock.id=stock_img.stock_id
                        WHERE stock.id=?";
            $stmt_stock = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                mysqli_stmt_execute($stmt_stock);
                $result_stock = mysqli_stmt_get_result($stmt_stock);
                $rowCount_stock = $result_stock->num_rows;

                if ($rowCount_stock < 1) {
                    echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                } else {
                    $stock_img_data = [];
                    while ( $row = $result_stock->fetch_assoc() ) {
                        $stock_id                  = $row['stock_id']          ; 
                        $stock_name                = $row['stock_name']        ;
                        $stock_description         = $row['stock_description'] ;
                        $stock_sku                 = $row['stock_sku']         ;
                        $stock_min_stock           = $row['stock_min_stock']   ;
                        $stock_is_cable            = $row['stock_is_cable']   ;
                        $stock_stock_img_id        = $row['stock_img_id']      ;
                        $stock_stock_img_stock_id  = $row['stock_img_stock_id'];
                        $stock_stock_img_image     = $row['stock_img_image']   ;
                        $stock_stock_deleted       = $row['stock_deleted'];

                        if ($stock_stock_img_id !== null) {
                        $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                        }
                    }
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // print_r('<pre class="theme-divBg">');
                    // print_r($stock_img_data);
                    // print_r('</pre>');
                    if ($stock_stock_deleted == 1) {
                        $delete_disable = ' disabled';
                    } else {
                        $delete_disable = '';
                    }
                    if ($stock_is_cable == 1) {
                        $cable_disable = ' disabled';
                    } else {
                        $cable_disable = '';
                    }

                    // check if current stock is favourited
		            function checkFavouriteStatus($stock_id, $user_id) {
                       include 'includes/dbh.inc.php';

                       $sql = "SELECT * FROM favourites WHERE stock_id=? AND user_id=?";
                       $stmt = mysqli_stmt_init($conn);
                       if (!mysqli_stmt_prepare($stmt, $sql)) {
                            return '';
                       } else {
                            mysqli_stmt_bind_param($stmt, "ss", $stock_id, $user_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            if ($rowCount < 1) {
                                return 'add';
                            } else {
                                return 'remove';
                            }
                        }
                    }

		            $favourite_check = checkFavouriteStatus($stock_id, $_SESSION['user_id']);

                    if ($favourite_check == 'add') {
                        $fa_type = 'fa-regular';
                    } else {
                        $fa_type = 'fa-solid';
                    }
                    echo('
                        <script src="assets/js/favourites.js"></script>
                        <div id="favouriteButton" class="" style="width: max-content">
                            <button onclick="favouriteStock('.$stock_id.')" class="favouriteBtn" id="favouriteBtn" title="Favourite Stock">
                                <i id="favouriteIcon" class="'.$fa_type.' fa-star"></i>
                            </button>
                        </div>
                            <div class="container stock-heading">
                                <div class="row">
                                    <div class="col">
                                        <h2 class="header-small" style="padding-bottom:0px">Stock</h2>
                                    </div>
                                    <div class="col nav-div nav-right" style="margin-bottom: 5px;max-width:max-content; width:max-content;margin-right:0px !important">
                                        <div class="nav-row">
                                            <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                                <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'edit\'))">
                                                    <i class="fa fa-pencil"></i><or class="viewport-large-empty"> Edit</or>
                                                </button>
                                            </div> 
                                            <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                                <button id="add-stock" class="btn btn-success theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'add\'))"'.$delete_disable.'>
                                                    <i class="fa fa-plus"></i><or class="viewport-large-empty"> Add</or>
                                                </button>
                                            </div> 
                                            <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                                <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'remove\'))"'.$delete_disable.'>
                                                    <i class="fa fa-minus"></i><or class="viewport-large-empty"> Remove</or>
                                                </button>
                                            </div> 
                                            <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                                <button id="transfer-stock" class="btn btn-warning nav-v-b stock-modifyBtn" style="color:black" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'move\'))"'.$delete_disable.'>
                                                    <i class="fa fa-arrows-h"></i><or class="viewport-large-empty"> Move</or>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>');                            
                                showResponse(); 
                                echo("
                                <div class='row ' style='margin-top:5px;margin-top:10px;'>
                                    <div class='col' style='margin-top:auto;margin-bottom:auto;'>
                                        <h3 style='font-size:22px;margin-bottom:0px;' id='stock-name'>".$stock_name." (".$stock_sku.")</h3>
                                        <input type='hidden' id='hiddenStockName' value='".$stock_name."'>
                                    </div>
                                    
                                </div>
                                <p id='stock-description' style='color:#898989;margin-bottom:0px;margin-top:10px'>".str_replace(array("\r\n","\\r\\n"), "<br/>", $stock_description)."</p>
                                "); 
                                if ($stock_stock_deleted == 1) { echo('<p class="red" style="margin-top:20px;font-size:20">Stock Deleted. <a class="link" style="font-size:20" href="admin.php#stockmanagement-settings">Restore?</a></p>');} 
                                echo('
                            </div>

                            <!-- Modal Image Div -->
                            <div id="modalDiv" class="modal" onclick="modalClose()">
                                <span class="close" onclick="modalClose()">&times;</span>
                                <img class="modal-content bg-trans modal-imgWidth" id="modalImg">
                                <div id="caption" class="modal-caption"></div>
                            </div>
                            <!-- End of Modal Image Div -->

                    ');

                    if ($stock_is_cable == 0) { // not a cable
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, area.name AS area_name,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    (SELECT SUM(quantity) 
                                        FROM item 
                                        WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id
                                    ) AS item_quantity,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.name ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_names,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.id ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_ids,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                        FROM stock_tag 
                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                        WHERE stock_tag.stock_id = stock.id
                                        ORDER BY tag.name
                                    ) AS tag_names,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                        FROM stock_tag
                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                        WHERE stock_tag.stock_id = stock.id
                                        ORDER BY tag.name
                                    ) AS tag_ids
                                FROM stock
                                LEFT JOIN item ON stock.id=item.stock_id
                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                WHERE stock.id=?
                                GROUP BY 
                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                    site_id, site_name, site_description, 
                                    area_id, area_name, 
                                    shelf_id, shelf_name
                                ORDER BY site.id, area.name, shelf.name;";
                    } else { // is a cable
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, area.name AS area_name,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    (SELECT SUM(quantity) 
                                        FROM cable_item 
                                        WHERE cable_item.stock_id = stock.id AND cable_item.shelf_id=shelf.id
                                    ) AS item_quantity
                                FROM stock
                                LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                WHERE stock.id=?
                                GROUP BY 
                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                    site_id, site_name, site_description, 
                                    area_id, area_name, 
                                    shelf_id, shelf_name
                                ORDER BY site.id, area.name, shelf.name;";
                    }
                    $stmt_stock = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                        mysqli_stmt_execute($stmt_stock);
                        $result_stock = mysqli_stmt_get_result($stmt_stock);
                        $rowCount_stock = $result_stock->num_rows;

                        if ($rowCount_stock < 1) {
                            echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                        } else {
                            

                                    
                                    
                                    
                                
                                        
                                 









                            /////////////////////////////////////////////

                                if ($stock_is_cable == 0) {
                                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                    area.id AS area_id, area.name AS area_name,
                                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                    item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, 
                                                    (SELECT SUM(quantity) 
                                                        FROM item 
                                                        WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id AND item.manufacturer_id=manufacturer.id 
                                                            AND item.serial_number=item_serial_number AND item.upc=item_upc AND item.comments=item_comments AND item.cost=item_cost
                                                    ) AS item_quantity,
                                                    manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag 
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_names,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_ids
                                                FROM stock
                                                LEFT JOIN item ON stock.id=item.stock_id
                                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                                LEFT JOIN area ON shelf.area_id=area.id 
                                                LEFT JOIN site ON area.site_id=site.id
                                                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                                WHERE stock.id=? AND quantity!=0
                                                GROUP BY 
                                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                                    site_id, site_name, site_description, 
                                                    area_id, area_name, 
                                                    shelf_id, shelf_name,
                                                    manufacturer_name, manufacturer_id,
                                                    item_serial_number, item_upc, item_comments, item_cost
                                                ORDER BY site.id, area.name, shelf.name;";
                                } else {
                                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                    area.id AS area_id, area.name AS area_name,
                                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                    cable_item.cost AS item_cost,
                                                    (SELECT SUM(quantity) 
                                                        FROM cable_item 
                                                        WHERE cable_item.stock_id = stock.id AND cable_item.shelf_id=shelf.id
                                                    ) AS item_quantity,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag 
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_names,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_ids
                                                FROM stock
                                                LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                                LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                                LEFT JOIN area ON shelf.area_id=area.id 
                                                LEFT JOIN site ON area.site_id=site.id
                                                WHERE stock.id=? AND quantity!=0
                                                GROUP BY 
                                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                                    site_id, site_name, site_description, 
                                                    area_id, area_name, 
                                                    shelf_id, shelf_name,
                                                    item_cost
                                                ORDER BY site.id, area.name, shelf.name;";
                                }
                                $stmt_stock = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_stock);
                                    $result_stock = mysqli_stmt_get_result($stmt_stock);
                                    $rowCount_stock = $result_stock->num_rows;

                                    if ($rowCount_stock < 1) {
                                        echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                                    } else {
                                        $stock_inv_data = [];
                                        while ( $row = $result_stock->fetch_assoc() ) {
                                            $stock_id = $row['stock_id'];
                                            $stock_name = $row['stock_name'];
                                            $stock_sku = $row['stock_sku'];
                                            $stock_quantity_total = $row['item_quantity'];
                                            $stock_shelf_id = $row['shelf_id'];
                                            $stock_shelf_name = $row['shelf_name'];
                                            $stock_area_id = $row['area_id'];
                                            $stock_area_name = $row['area_name'];
                                            $stock_site_id = $row['site_id'];
                                            $stock_site_name = $row['site_name'];
                                            if ($stock_is_cable == 0) {
                                                $stock_manufacturer_id = $row['manufacturer_id'];
                                                $stock_manufacturer_name = $row['manufacturer_name'];
                                                $item_upc = $row['item_upc'];
                                                $item_comments = $row['item_comments'];
                                                $item_serial_number = $row['item_serial_number'];
                                            }
                                            $item_cost = $row['item_cost'];
                                            $stock_tag_ids = $row['tag_ids'];
                                            $stock_tag_names = $row['tag_names'];
                                            
                                            $stock_tag_data = [];

                                            if ($stock_tag_ids !== null) {
                                                for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                                                    $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                                                        'name' => explode(", ", $stock_tag_names)[$n]);
                                                }
                                            } else {
                                                $stock_tag_data = '';
                                            }
                                            
                                            if ($stock_is_cable == 0) {
                                                $stock_inv_data[] = array('id' => $stock_id,
                                                                    'name' => $stock_name,
                                                                    'sku' => $stock_sku,
                                                                    'quantity' => $stock_quantity_total,
                                                                    'shelf_id' => $stock_shelf_id,
                                                                    'shelf_name' => $stock_shelf_name,
                                                                    'area_id' => $stock_area_id,
                                                                    'area_name' => $stock_area_name,
                                                                    'site_id' => $stock_site_id,
                                                                    'site_name' => $stock_site_name,
                                                                    'manufacturer_id' => $stock_manufacturer_id,
                                                                    'manufacturer_name' => $stock_manufacturer_name,
                                                                    'upc' => $item_upc,
                                                                    'cost' => $item_cost,
                                                                    'comments' => $item_comments,
                                                                    'serial_number' => $item_serial_number,
                                                                    'tag_names' => $stock_tag_names,
                                                                    'tag' => $stock_tag_data);
                                            } else {
                                                $stock_inv_data[] = array('id' => $stock_id,
                                                                    'name' => $stock_name,
                                                                    'sku' => $stock_sku,
                                                                    'quantity' => $stock_quantity_total,
                                                                    'shelf_id' => $stock_shelf_id,
                                                                    'shelf_name' => $stock_shelf_name,
                                                                    'area_id' => $stock_area_id,
                                                                    'area_name' => $stock_area_name,
                                                                    'site_id' => $stock_site_id,
                                                                    'site_name' => $stock_site_name,
                                                                    'cost' => $item_cost);
                                            }
                                            
                                        }
                                        
                                              
                                            <table class="table table-dark theme-table centertable">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th class="align-middle text-center" hidden>ID</th>
                                                        <th class="align-middle text-center">Site</th>
                                                        <th class="align-middle text-center">Location</th>
                                                        <th class="align-middle text-center">Shelf</th>
                                                        @if ($stock_data['is_cable'] == 0) 
                                                            
                                                            <th class="align-middle text-center viewport-large-empty">Manufacturer</th>
                                                            <th class="align-middle text-center viewport-small-empty">Manu.</th>
                                                            <th class="align-middle text-center viewport-large-empty">UPC</th>
                                                            <th title="Serial Numbers" class="align-middle text-center">Serial</th>
                                                            <th class="align-middle text-center" hidden>Tags</th>
                                                            <th class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden @endif >Cost</th>
                                                            <th class="viewport-large-empty align-middle text-center">Comments</th>
                                                        @else 
                                                            <th class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_cable'] == 0) hidden @endif >Cost</th>
                                                        @endif
                                                        
                                                        <th class="align-middle text-center">Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                           
                                            @foreach($stock_inv_data['rows'] as $row)
                                                <tr id="item-{{ $loop->iteration }}" @if ($stock_data['is_cable'] == 0) 'class="clickable row-show" onclick="toggleHiddenStock({{ $loop->iteration }})">
                                                    <td hidden>{{ $loop->iteration }}</td>
                                                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}" class="align-middle text-center">{{ $row['site_name'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}" class="align-middle text-center">{{ $row['area_name'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}-{{ $row['shelf_id'] }}" class="align-middle text-center">{{ $row['shelf_name'] }}</td>
                                                    @if ($stock_data['is_cable'] == 0)
                                                    <td id="item-{{ $loop->iteration }}-manu-{{ $row['manufacturer_id'] }}" class="align-middle text-center">{{  $row['manufacturer_name'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-upc" class="viewport-large-empty align-middle text-center">{{ $row['upc'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-sn" class="align-middle text-center">{{ $row['serial_number'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-tags" class="align-middle text-center" hidden>{{ $row['tag_names'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-comments" class="viewport-large-empty align-middle text-center">{{ $row['comments'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-stock" class="align-middle text-center">{{ $row['quantity'] }}</td>
                                                    @else
                                                    <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_cable'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                                                    <td id="item-{{ $loop->iteration }}-stock" @if($row['quantity'] < $stock_data['min_stock']) class="red align-middle text-center" title="Below minimum stock count. Please re-order." @else class="align-middle text-center" @endif >{{ $row['quantity'] }}</td>
                                                    @endif
                                                </tr>
                                                @if ($stock_data['is_cable'] == 0)

                                                /////////////////////////////

                                                
                                                @else
                                                @endif
                                            @endforeach

                                            for ($i=0; $i<count($stock_inv_data); $i++) {




                                                //////////////////////////////////////
                                                if ($stock_is_cable == 0) { 
                                                    echo ('
                                                    <tr id="item-'.$i.'-hidden" class="row-hide" hidden>
                                                        <td colspan=100%>
                                                            <div style="max-height:75vh;overflow-x: hidden;overflow-y: auto;">
                                                                <table class="table table-dark theme-table centertable" style="border-left: 1px solid #454d55;border-right: 1px solid #454d55;border-bottom: 1px solid #454d55">
                                                                    <thead>
                                                                        <tr class="theme-tableOuter">
                                                                            <th class="align-middle text-center">ID</th>
                                                                            <th class="align-middle text-center" hidden>Site</th>
                                                                            <th class="align-middle text-center" hidden>Location</th>
                                                                            <th class="align-middle text-center" hidden>Shelf</th>
                                                                            <th class="align-middle text-center">Manufacturer</th>
                                                                            <th class="align-middle text-center">UPC</th>
                                                                            <th class="align-middle text-center">Serial</th>
                                                                            <th class="align-middle text-center"'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('>Cost ('.$current_currency.')</th>
                                                                            <th class="align-middle text-center">Comments</th>
                                                                            <th class="align-middle text-center" colspan=2>Container</th>
                                                                            <th class="align-middle text-center">Stock</th>
                                                                            <th class="align-middle text-center"></th>
                                                                            <th class="align-middle text-center"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    ');
                                                                    $hidden_shelf_id = $stock_inv_data[$i]['shelf_id'];
                                                                    $hidden_serial = $stock_inv_data[$i]['serial_number']; 
                                                                    $hidden_cost = $stock_inv_data[$i]['cost'];
                                                                    $hidden_manufacturer = $stock_inv_data[$i]['manufacturer_id'];
                                                                    $hidden_comments = $stock_inv_data[$i]['comments'];
                                                                    $hidden_comments = mysqli_real_escape_string($conn, $hidden_comments); // escape the special characters
                            
                                                                    $sql_hidden = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                                                area.id AS area_id, area.name AS area_name,
                                                                                shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                                                item.id AS item_id, item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, item.quantity AS item_quantity, item.is_container AS item_is_container,
                                                                                manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name
                                                                            FROM stock
                                                                            LEFT JOIN item ON stock.id=item.stock_id
                                                                            LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                                                            LEFT JOIN area ON shelf.area_id=area.id 
                                                                            LEFT JOIN site ON area.site_id=site.id
                                                                            LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                                                            LEFT JOIN item_container AS ic ON item.id=ic.item_id OR item.id=ic.container_id
                                                                            LEFT JOIN container AS c ON ic.container_id=c.id
                                                                            WHERE stock.id=? AND shelf.id=$hidden_shelf_id AND quantity!=0 AND item.deleted=0 AND item.serial_number='$hidden_serial' AND item.cost='$hidden_cost' AND item.comments='$hidden_comments' AND item.manufacturer_id='$hidden_manufacturer'
                                                                            GROUP BY stock_id, stock_name, stock_description, stock_sku, stock_min_stock, area_id, area_name, shelf_id, shelf_name, site_id, site_name, site_description, item.id, item_serial_number, item_upc, item_cost, item_comments, item_quantity, manufacturer_id, manufacturer_name 
                                                                            ORDER BY item.serial_number, item.upc, item.comments DESC";
                                                                    $stmt_hidden = mysqli_stmt_init($conn);
                                                                    if (!mysqli_stmt_prepare($stmt_hidden, $sql_hidden)) {
                                                                        echo("ERROR getting entries");
                                                                    } else {
                                                                        mysqli_stmt_bind_param($stmt_hidden, "s", $stock_id);
                                                                        mysqli_stmt_execute($stmt_hidden);
                                                                        $result_hidden = mysqli_stmt_get_result($stmt_hidden);
                                                                        $rowCount_hidden = $result_hidden->num_rows;

                                                                        if ($rowCount_hidden < 1) {
                                                                            echo ('<tr><td colpan=100%>No Stock Found</td></tr>');
                                                                        } else {
                                                                            $stock_img_data = [];
                                                                            while ( $row_hidden = $result_hidden->fetch_assoc() ) {
                                                                                $is_container = 0;
                                                                                if (isset($rowCount_container)) { unset($rowCount_container); }
                                                                                if ((int)$row_hidden['item_is_container'] == 1) {
                                                                                    $is_container = 1;
                                                                                    $container_item_id = $row_hidden['item_id'];
                                                                                    $sql_container = "SELECT s.name AS s_name, s.id AS s_id, 
                                                                                                        i.id AS i_id, i.upc AS i_upc, i.serial_number AS i_serial_number, i.comments AS i_comments,
                                                                                                        ic.id AS ic_id
                                                                                                        FROM item_container AS ic
                                                                                                        INNER JOIN item AS i ON i.id=ic.item_id AND ic.container_is_item=1
                                                                                                        INNER JOIN stock AS s ON s.id=i.stock_id
                                                                                                        WHERE ic.container_id='$container_item_id' AND ic.container_is_item=1";
                                                                                    $stmt_container = mysqli_stmt_init($conn);
                                                                                    if (!mysqli_stmt_prepare($stmt_container, $sql_container)) {
                                                                                        echo("ERROR getting entries");
                                                                                    } else {
                                                                                        mysqli_stmt_execute($stmt_container);
                                                                                        $result_container = mysqli_stmt_get_result($stmt_container);
                                                                                        $rowCount_container = $result_container->num_rows;
                                                                                    }
                                                                                }
                                                                                echo('
                                                                                <tr class="align-middle">
                                                                                    <form action="includes/stock-modify.inc.php" method="POST" id="form-item-'.$row_hidden['item_id'].'" enctype="multipart/form-data"></form>
                                                                                    <!-- Include CSRF token in the form -->
                                                                                    <input type="hidden" form="form-item-'.$row_hidden['item_id'].'" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                                                    <input type="hidden" form="form-item-'.$row_hidden['item_id'].'" name="submit" value="row"/>
                                                                                    <td class="align-middle text-center"><input type="hidden" form="form-item-'.$row_hidden['item_id'].'" name="item-id" value="'.$row_hidden['item_id'].'" />'.$row_hidden['item_id'].'</td>
                                                                                    <td hidden>'.$row_hidden['site_name'].'</td>
                                                                                    <td hidden>'.$row_hidden['area_name'].'</td>
                                                                                    <td hidden>'.$row_hidden['shelf_name'].'</td>
                                                                                    <td class="align-middle text-center">
                                                                                        <select class="form-control manufacturer-select" form="form-item-'.$row_hidden['item_id'].'" name="manufacturer_id" style="max-width:max-content">');
                                                                                        $sql_manufacturer = "SELECT * FROM manufacturer ORDER BY name;";
                                                                                        $stmt_manufacturer = mysqli_stmt_init($conn);
                                                                                        if (!mysqli_stmt_prepare($stmt_manufacturer, $sql_manufacturer)) {
                                                                                            echo("ERROR getting entries");
                                                                                        } else {
                                                                                            mysqli_stmt_execute($stmt_manufacturer);
                                                                                            $result_manufacturer = mysqli_stmt_get_result($stmt_manufacturer);
                                                                                            $rowCount_manufacturer = $result_manufacturer->num_rows;
                                                                                            echo('<option value="-1" class="gold link theme-tableOuter">Add New</option>');
                                                                                            if ($rowCount_manufacturer == 0){
                                                                                                echo('<option value="" selected disabled>No Manufacturers Found...</option>');
                                                                                            } else {
                                                                                                while ($row_manufacturer = $result_manufacturer->fetch_assoc()) {
                                                                                                    echo('<option value="'.$row_manufacturer['id'].'"'); if ($row_hidden['manufacturer_id'] == $row_manufacturer['id']) { echo(' selected'); } echo('>'.$row_manufacturer['name'].'</option>');
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    echo('
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="align-middle text-center"><input type="text" form="form-item-'.$row_hidden['item_id'].'" class="form-control" style="" value="'.$row_hidden['item_upc'].'" name="upc" /></td>
                                                                                    <td class="align-middle text-center"><input type="text" form="form-item-'.$row_hidden['item_id'].'" class="form-control" style="" value="'.$row_hidden['item_serial_number'].'" name="serial_number" /></td>
                                                                                    <td class="align-middle text-center"'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('><input type="number" step=".01" form="form-item-'.$row_hidden['item_id'].'" class="form-control" style="width:75px" value="'.$row_hidden['item_cost'].'" name="cost" min=0 /></td>
                                                                                    <td class="align-middle text-center"><input type="text" form="form-item-'.$row_hidden['item_id'].'" class="form-control" style="" value="'.htmlspecialchars($row_hidden['item_comments'], ENT_QUOTES, 'UTF-8').'" name="comments" /></td>
                                                                                    ');
                                                                                        $p_item_id = $row_hidden['item_id'];
                                                                                        $sql_parent = "SELECT ic.item_id AS ic_item_id, ic.container_id AS ic_container_id, ic.container_is_item AS ic_container_is_item,
                                                                                                                c.id AS c_id, c.name AS c_name,
                                                                                                                i.id AS i_id, 
                                                                                                                s.id AS s_id, s.name AS s_name
                                                                                                        FROM item_container AS ic
                                                                                                        LEFT JOIN container AS c ON ic.container_id=c.id AND ic.container_is_item=0
                                                                                                        LEFT JOIN item AS i ON ic.container_id=i.id AND ic.container_is_item=1
                                                                                                        LEFT JOIN stock AS s ON i.stock_id=s.id
                                                                                                        WHERE item_id=$p_item_id LIMIT 1;";
                                                                                        $stmt_parent = mysqli_stmt_init($conn);
                                                                                        if (!mysqli_stmt_prepare($stmt_parent, $sql_parent)) {
                                                                                            echo("ERROR getting entries");
                                                                                        } else {
                                                                                            mysqli_stmt_execute($stmt_parent);
                                                                                            $result_parent = mysqli_stmt_get_result($stmt_parent);
                                                                                            $rowCount_parent = $result_parent->num_rows;
                                                                                            if ($rowCount_parent == 0){
                                                                                                echo('<td class="align-middle text-center" style="padding-right:2px"'); if ($is_container == 1) { echo('colspan=2'); } echo('>');
                                                                                                if ($is_container == 1 && isset($rowCount_container) && $rowCount_container > 0) {
                                                                                                    echo('<input type="checkbox" form="form-item-'.$row_hidden['item_id'].'" name="container-toggle" checked hidden>');
                                                                                                } 
                                                                                                echo('
                                                                                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:0px" >
                                                                                                        <input type="checkbox" form="form-item-'.$row_hidden['item_id'].'" '); if ($is_container == 1) {echo 'checked name="container-toggle'; if (isset($rowCount_container) && $rowCount_container > 0) { echo'-disabled" disabled'; } } else { echo 'name="container-toggle';} echo('">
                                                                                                        <span class="slider round align-middle" style="transform: scale(0.8, 0.8)'); if ($is_container == 1 && isset($rowCount_container) && $rowCount_container > 0) { echo'; opacity: 0.5; cursor: no-drop;" title="Please un-assign the children first'; } echo('"></span>
                                                                                                    </label>');
                                                                                                echo('
                                                                                                </td>');
                                                                                                if ($is_container == 0) {
                                                                                                    echo('<td class="align-middle text-center" style="padding-left:2px">
                                                                                                            <button class="btn btn-warning" type="button" style="opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Link to container" onclick="modalLoadLinkToContainer(\''.$row_hidden['item_id'].'\')">
                                                                                                                <i class="fa fa-link"></i>
                                                                                                            </button>
                                                                                                    </td>');
                                                                                                }
                                                                                                
                                                                                            } else {
                                                                                                $row_parent = $result_parent->fetch_assoc();
                                                                                                $parent_c_is_item = $row_parent['ic_container_is_item'];
                                                                                                $parent_c_id = $row_parent['c_id'];
                                                                                                $parent_c_name = $row_parent['c_name'];
                                                                                                $parent_i_id = $row_parent['i_id'];
                                                                                                $parent_s_id = $row_parent['s_id'];
                                                                                                $parent_s_name = $row_parent['s_name'];
                                                                                                if ((int)$parent_c_is_item == 1) {
                                                                                                    $col_id = $parent_i_id;
                                                                                                    $col_name = $parent_s_name;
                                                                                                    $col_item = 1;
                                                                                                } else {
                                                                                                    $col_id = $parent_c_id;
                                                                                                    $col_name = $parent_c_name;
                                                                                                    $col_item = 0;
                                                                                                }

                                                                                                echo("<td class='align-middle text-center' style='padding-right:2px'>
                                                                                                    <a class='link' id='modalUnlinkContainerItemName-".$row_hidden['item_id']."' href='containers.php?container_id=$col_id&con_is_item=$col_item'>$col_name</a>
                                                                                                </td>
                                                                                                <td class='align-middle text-center' style='padding-left:2px'>
                                                                                                    <form action='includes/stock-modify.inc.php' method='POST' id='form-item-".$row_hidden['item_id']."-container-unlink' enctype='multipart/form-data'>
                                                                                                        <!-- Include CSRF token in the form -->
                                                                                                        <input type='hidden' name='csrf_token' value='".htmlspecialchars($_SESSION['csrf_token'])."'>
                                                                                                        <input type='hidden' name='item_id' value='".$row_hidden['item_id']."' form='form-item-".$row_hidden['item_id']."-container-unlink' />
                                                                                                        <input type='hidden' name='container-unlink' value='1' form='form-item-".$row_hidden['item_id']."-container-unlink' />
                                                                                                        <button class='btn btn-danger' type='button' name='submit' onclick=\"modalLoadUnlinkContainer('".$row_hidden['item_id']."', '$col_id', 1)\" form='form-item-".$row_hidden['item_id']."-container-unlink' style='color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px' title='Unlink from container'>
                                                                                                            <i class='fa fa-unlink'></i>
                                                                                                        </button>
                                                                                                    </form>
                                                                                                </td>");
                                                                                            }
                                                                                        }
                                                                                        
                                                                                    echo('
                                                                                    <td class="align-middle text-center">'.$row_hidden['item_quantity'].'</td>
                                                                                    <td style="padding-right:3px"><input type="submit" form="form-item-'.$row_hidden['item_id'].'" class="btn btn-success" name="stock-row-submit" value="Update" /></td>
                                                                                    <td style="padding-left:3px"><button type="button" class="btn btn-danger" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'&manufacturer='.$stock_inv_data[$i]['manufacturer_id'].'&shelf='.$stock_inv_data[$i]['shelf_id'].'&serial='.$stock_inv_data[$i]['serial_number'].'\', \'modify\', \'remove\'))" '); if ($is_container == 1 && isset($rowCount_container) && $rowCount_container > 0) { echo' disabled'; } echo('><i class="fa fa-trash"></i></button></td>
                                                                                </tr>
                                                                                ');
                                                                                if ($is_container == 1 && isset($rowCount_container)) {
                                                                                    echo('
                                                                                        <tr class="theme-th-selected">
                                                                                            <td colspan=100%>
                                                                                                <div style="max-height:50vh;overflow-x: hidden;overflow-y: auto;">
                                                                                                    <p class="centertable" style="width:85%; margin-bottom:5px">Contents</p>
                                                                                                    <table class="table table-dark theme-table centertable" style="border-left: 1px solid #454d55;border-right: 1px solid #454d55;border-bottom: 1px solid #454d55; width:85%">
                                                                                                        <thead>
                                                                                                            <th class="align-middle text-center">Item ID</th>
                                                                                                            <th class="align-middle text-center">Name</th>
                                                                                                            <th class="align-middle text-center">UPC</th>
                                                                                                            <th class="align-middle text-center">Serial</th>
                                                                                                            <th class="align-middle text-center">Comments</th>
                                                                                                            <th class="align-middle text-center">
                                                                                                                <button class="btn btn-success" type="submit" name="button" onclick="modalLoadAddChildren('.$row_hidden['item_id'].')" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Add more children">
                                                                                                                    + <i class="fa fa-link"></i>
                                                                                                                </button>
                                                                                                            </th>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                        ');
                                                                                    if ($rowCount_container == 0){
                                                                                        echo('
                                                                                                            <tr><td class="align-middle text-center" colspan=100%>No contents found.</td><tr>');
                                                                                    } else {
                                                                                        while ($row_container = $result_container->fetch_assoc()) {
                                                                                            echo('
                                                                                                            <tr class="align-middle">

                                                                                                                <td class="align-middle text-center">'.$row_container['i_id'].'</td>
                                                                                                                <td class="align-middle text-center" style="white-space:wrap;"><a class="link" href="stock.php?stock_id='.$row_container['s_id'].'" id="modalUnlinkContainerItemName-'.$row_container['i_id'].'">'.$row_container['s_name'].'</a></td>
                                                                                                                <td class="align-middle text-center">'.$row_container['i_upc'].'</td>
                                                                                                                <td class="align-middle text-center">'.$row_container['i_serial_number'].'</td>
                                                                                                                <td class="align-middle text-center">'.$row_container['i_comments'].'</td>
                                                                                                                <td class="align-middle text-center">
                                                                                                                    <input type="hidden" id="modalUnlinkContainerName" value="'.$row_hidden['stock_name'].'" />
                                                                                                                    <button class="btn btn-danger" type="submit" name="button" onclick="modalLoadUnlinkContainer(\''.$row_hidden['item_id'].'\', \''.$row_container['i_id'].'\', 0)" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                                                                                        <i class="fa fa-unlink"></i>
                                                                                                                    </button>
                                                                                                                </td>
                                                                                                            </tr>');
                                                                                        }
                                                                                    }
                                                                                    echo('
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                        ');
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    echo('
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    ');
                                                }
                                            }
                                            echo('
                                            </tbody>
                                        </table>
                                    
                                    
                                    
                                ');
                                }
                                echo('</div>');
                            }
                            echo('<div class="container well-nopad theme-divBg viewport-large-empty" style="margin-top:5px">
                                <h2 style="font-size:22px">Transactions</h2>');
                                include 'includes/transaction.inc.php';
                            echo('</div>
                                <div class="container well-nopad theme-divBg viewport-small-empty text-center" style="margin-top:5px">
                                    <or class="specialColor clickable" style="font-size:12px" onclick="navPage(\'transactions.php?stock_id='.$stock_id.'\ \')">View Transactions</or>
                                </div>');
                        }
                        
                    }
                }
            }

            
        }

        
    ?>
    </div>
    <!-- Start Modal for uninking from container -->
    <div id="modalDivUnlinkContainer" class="modal">
        <span class="close" onclick="modalCloseUnlinkContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" id="form-unlink-container-item-id" name="item_id" value=""  />
                    <input type="hidden" name="container-unlink" value="1"/>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <th colspan=100%>Container:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row" style="padding-top:20px">
                                <th colspan=100%>Item to unlink:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row text-center align-middle" style="padding-top:10px">
                                <td class="text-center align-middle" colspan=100% style="width:100%">
                                    <span style="white-space:nowrap; width:100%">
                                        <button class="btn btn-danger" type="submit" name="submit" style="color:black !important; margin-right:10px">Unlink <i style="margin-left:5px" class="fa fa-unlink"></i></button>
                                        <button class="btn btn-warning" type="button" onclick="modalCloseUnlinkContainer()" style="margin-left:10px">Cancel</button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal for uninking from container -->

    <!-- Link to Container Modal -->
    <div id="modalDivAddChildren" class="modal">
        <span class="close" onclick="modalCloseAddChildren()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
                <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add item to selected container</h4>
                <table class="centertable"><tbody><tr><th style="padding-right:5px">Container ID:</th><td style="padding-right:20px" id="contID"></td><th style="padding-right:5px">Container Name:</th><td id="contName"></td></tr></tbody></table>
                <div class="row" id="TheRow" style="min-width: 100%; max-width:1920px; flex-wrap:nowrap !important; padding-left:10px;padding-right:10px; max-width:max-content">
                    <div class="col well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px;">
                        <p><strong>Stock</strong></p>
                        <input type="text" name="search" class="form-control" style="width:300px; margin-bottom:5px" placeholder="Search" oninput="addChildrenSearch(document.getElementById('contID').innerHTML, this.value)"/>
                        <div style=" overflow-y:auto; overflow-x: hidden; height:300px; ">
                            <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th class='text-center align-middle'>Stock ID</th>
                                        <th class='text-center align-middle'>Name</th>
                                        <th class='text-center align-middle'>Serial Number</th>
                                        <th class='text-center align-middle'>Quantity</th>
                                        <th class='text-center align-middle'>Item ID</th>
                                    </tr>
                                </thead>
                                <tbody id="addChildrenTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <form enctype="multipart/form-data" action="./includes/stock-modify.inc.php" method="POST" style="padding: 0px; margin:0px">
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="container-link-fromstock" value="1" />
                <input type="hidden" id="addChildrenContID" name="container_id" value="" />
                <input type="hidden" id="addChildrenStockID" name="stock_id" value="" />
                <input type="hidden" id="addChildrenItemID" name="item_id" value="" />
                <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                    <input id="submit-button-addChildren" type="submit" name="submit" value="Link" class="btn btn-success" style="margin:10px 10px 0px 10px" disabled></input>
                    <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseAddChildren()">Cancel</button>
                </span>
            </form>
        </div>
    </div>
    <!-- End of Container Add item Modal -->

    <!-- Container Add item Modal -->
    <div id="modalDivLinkToContainer" class="modal">
        <span class="close" onclick="modalCloseLinkToContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
                <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add to container</h4>
                <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="linkToContainerItemID"></td><th style="padding-right:5px">Item Name:</th><td id="linkToContainerItemName"></td></tr></tbody></table>
                <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                    <p><strong>Containers</strong></p>
                    <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">ID</th>
                                <th class="text-center align-middle">Name</th>
                                <th class="text-center align-middle">Description</th>
                            </tr>
                        </thead>
                        <tbody id="containerSelectTableBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <form class="padding:0px;margin:0px" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                    <input type="hidden" name="container-link" value="1" />
                    <input type="hidden" id="linkToContainerTableItemID" name="item_id" />
                    <input type="hidden" id="linkToContainerTableID" name="container_id" />
                    <input type="hidden" id="linkToContainerTableItem" name="item" />
                    <input type="submit" id="containerLink-submit-button" name="submit" class="btn btn-success" style="margin:10px 10px 0px 10px" value="Link" disabled>
                    <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseLinkToContainer()">Cancel</button>
                </span>
            </form>
        </div>
    </div>
    <!-- End of Link to Container-->
     
    <!-- Add the JS for the file -->
    <script src="assets/js/stock.js"></script>
    <?php include 'foot.php'; ?>
</body>
