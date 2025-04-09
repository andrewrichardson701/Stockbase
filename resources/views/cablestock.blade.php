<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Cables</title>
</head>
<body onload="getInventory(0)">
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->
    <div class="content">
        
        <!-- Get Inventory -->
        <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:10px; margin-top:20px">
            <div class="nav-row">
                <form action="cablestock" method="get" class="nav-row" style="max-width:max-content">
                    <input id="query-site" type="hidden" name="site" value="{{$q_data['site']}}" />
                    <input id="hidden-cabletype" type="hidden" name="cable" value="{{$q_data['cable_type']}}" />
                    <input id="hidden-oos" type="hidden" name="oos" value="{{$q_data['oos']}}" />
                    <input id="hidden-row-count" type="hidden" value="{{$q_data['rows']}}" />
                    <input id="hidden-page-number" type="hidden" value="{{$q_data['page']}}" />

                    <span id="search-input-site-span" style="margin-bottom:10px;" class="index-dropdown">
                        <label for="search-input-site">Site</label><br>
                        <select id="site-dropdown" name="site" class="form-control nav-v-b theme-dropdown" oninput="getInventory(1)" >
                            <option value="0" @if ($q_data['site'] == 0) selected @endif >All</option>
                        @foreach ($sites['rows'] as $site) 
                            <option value="{{$site['id']}}" @if ($q_data['site'] == $site['id']) selected @endif >{{$site['name']}}</option>
                        @endforeach
                        </select>
                    </span>
                    <span id="search-input-name-span" style="margin-right:0.5em;margin-bottom:10px;">
                        <label for="search-input-name">Name</label><br>
                        <input id="search-input-name" type="text" name="name" class="form-control" style="display:inline-block" placeholder="Search by Name" oninput="getInventory(1)" value="{{$q_data['name']}}" />
                    </span>
                    <span id="search-input-type-span" style="margin-right:0.5em;margin-bottom:10px;">
                        <label for="search-input-type">Type</label><br>
                        <select id="search-input-type" name="type" class="form-control" style="display:inline-block" placeholder="Search by Type" onchange="getInventory(1)" >
                            <option value="" @if ($q_data['type'] == 0) selected @endif >All</option>
                        @foreach ($cable_types['rows'] as $type) 
                            <option value="{{$type['id']}}" @if ($q_data['type'] == $type['id']) selected @endif >{{$type['name']}}</option>
                        @endforeach
                        </select>
                    </span>
                    <input type="submit" value="submit" hidden>
                </form>

                <div id="clear-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:5px;margin-right:0px">
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="navPage('cablestock')">
                        <i class="fa fa-ban fa-rotate-90" style="height:24px;padding-top:4px"></i>
                    </button>
                </div>
                <div id="zero-div" class="nav-div viewport-large-block" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                @if ($q_data['oos'] == 0)
                    <button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter('', 'oos', '1'))">
                @else
                    <button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter('', 'oos', '0'))">
                @endif
                        <span class="zeroStockFont">
                            <p style="margin:0px;padding:0px;font-size:12px"> @if ($q_data['oos'] == 0) <i class="fa fa-plus"></i> Show @else <i class="fa fa-minus"></i> Hide @endif </p>
                            <p style="margin:0px;padding:0px;font-size:12px">0 Stock</p>
                        </span>
                    </button>
                </div>
                <div id="add-cables-div" class="nav-div viewport-large-block" style="margin-bottom:10px;margin-left:15px;margin-right:0px">
                    <button id="add-cables" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button">
                        <i class="fa fa-plus" style="height:24px;padding-top:4px"></i> Add Cables
                    </button>
                    <button id="add-cables-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button" hidden>
                        Hide Add Cables
                    </button>
                </div>
            </div>
        </div>
        <!-- mobile layout section -->
        <div class="container viewport-small" style="margin-top:-10px;max-width:max-content">
            <div class="nav-row">
                <div id="clear-div" class="nav-div" style="margin-left:0px;margin-right:0px;margin-bottom:10px;">
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage('/')">
                        <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                    </button>
                </div>
                <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">');
                @if ($q_data['oos'] == 0)
                    <button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0px 2px 1px 2px" onclick="navPage(updateQueryParameter('', 'oos', '1'))">
                @else
                    <button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0px 2px 1px 2px" onclick="navPage(updateQueryParameter('', 'oos', '0'))">
                @endif
                       <span class="zeroStockFont">
                            <p style="margin:0px;padding:0px"> @if ($q_data['oos'] == 0) <i class="fa fa-plus"></i> Show @else <i class="fa fa-minus"></i> Hide @endif </p>
                            <p style="margin:0px;padding:0px">0 Stock</p>
                         </span>
                    </button>
                </div>
                <div id="add-cables-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                    <button id="add-cables-small" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button">
                        <i class="fa fa-plus" style="padding-top:0px"></i> Add Cables
                    </button>
                    <button id="add-cables-hide-small" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:6px 6px 6px 6px" onclick="toggleAddDiv()" type="button" hidden>
                        Hide Add Cables
                    </button>
                </div>
                <div id="stockBtn-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                    <button id="stockBtn" class="btn btn-dark nav-v-b" style="opacity:90%;color:white;padding:6px 6px 6px 6px" onclick="navPage('cablestock')" type="button">
                        Item Stock
                    </button>
                </div>
            </div>
        </div>
                    
        <!-- Add Cables form section -->
        <div class="container" id="add-cables-section" style="margin-bottom:10px" hidden>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new cables</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <form id="add-cables-form" action="cablestock.addCables" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <table class="centertable">
                        <thead>
                            <th style="padding-left:5px">Site</th>
                            <th style="padding-left:5px">Area</th>
                            <th style="padding-left:5px">Shelf</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select id="site-dropdown-add" name="site" class="form-control" style="border-color:black;margin:0px;padding-left:0px" required>
                                        <option value="0" @if ($q_data['site'] == 0) selected @endif >All</option>
                                    @foreach ($sites['rows'] as $site) 
                                        <option value="{{$site['id']}}" @if ($q_data['site'] == $site['id']) selected @endif >{{$site['name']}}</option>
                                    @endforeach
                                    </select>
                                    <label style="margin-top:5px;font-size:14px">&nbsp;</label>
                                </td>
                                <td>
                                    <select id="area" name="area" class="form-control" style="border-color:black;margin:0px;padding-left:0px" disabled required>
                                        <option value="" selected disabled hidden>Select Area</option>
                                    </select>
                                    <label style="margin-top:5px;font-size:14px">&nbsp;</label>
                                </td>
                                <td>
                                    <select id="shelf" name="shelf" class="form-control" style="border-color:black;margin:0px;padding-left:0px" disabled required>
                                        <option value="" selected disabled hidden>Select Shelf</option>
                                    </select>
                                    <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadProperties('shelf')">Add New</a>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                        <tbody>
                            <tr>
                                <td colspan=100%>
                                    <div class="row" style="margin-right:25px">
                                        <div class="col">
                                            <div>Name</div>
                                            <div>
                                                <input class="form-control" type="text" list="names" name="stock-name" placeholder="Cable Name" style="min-width:120px" required/>
                                                <datalist id="names">');
                                                @foreach ($cables['rows'] as $cable) 
                                                    <option>{{$cable['name']}}</option>
                                                @endforeach
                                                echo('
                                                </datalist>
                                            </div>
                                        </div>
                                        <div class="col"><div>Description</div><div><input class="form-control" type="text" name="stock-description" style="min-width:120px" placeholder="Description"/></div></div>
                                        <div class="col">
                                            <div>Type</div>
                                            <div>
                                                <select class="form-control" name="cable-type" style="min-width:100px" required>
                                                <option selected disabled>Select Type</option>');
                                                @if ($cable_types['count'] > 0)
                                                    @foreach ($cable_types['rows'] as $type) 
                                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                    @endforeach
                                                @else
                                                    <option seleced disabled>No Types Found</option>
                                                @endif
                                            

                                                echo('
                                                </select>
                                            </div>
                                            <div class="text-center">
                                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewType()">Add New</a>
                                            </div>
                                        </div>
                                        <div class="col" style="max-width:max-content"><div>Min.Stock</div><div><input class="form-control" type="number" name="stock-min-stock" placeholder="Minimum Stock Count" style="width:70px" value="10" required/></div></div>
                                        <div class="col" style="max-width:max-content"><div>Quantity</div><div><input class="form-control" type="number" name="item-quantity" placeholder="Quantity" style="width:70px" value="1" required/></div></div>
                                        <div class="col" style="max-width:max-content" @if ($head_data['config_compare']['cost_enable_cable'] == 0) hidden @endif ><div>Cost</div><div><input class="form-control" type="number" step=".01" name="item-cost" placeholder="Cost" style="width:70px" value="0" required/></div></div>
                                        <div class="col" style="max-width:max-content""><div>&nbsp;</div><div><button class="btn btn-success align-bottom" type="submit" name="add-cables-submit" style="margin-left:10px" value="1">Add</button></div></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=100% class="text-center">
                                    <input type="file" style="width: 250px;margin-top:10px" id="stock-img" name="stock-img">
                                </td>
                            </tr>
                        </tbody>
                    </table>                      
                </form>
            </div>
        </div>

        <!-- Modal Image Div -->
        <div id="modalDiv" class="modal" onclick="modalClose()">
            <span class="close" onclick="modalClose()">&times;</span>
            <img class="modal-content bg-trans" id="modalImg">
            <div id="caption" class="modal-caption"></div>
        </div>
        <!-- End of Modal Image Div -->

        <!-- Table -->
        <div class="container">
            <pre id="hidden-sql" hidden></pre>
            <table class="table table-dark theme-table centertable" id="cableSelection" style="border:0px !important">
                <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border:0px !important">
                    <tr style="border:0px !important">
                        <th class="clickable @if ($q_data['cable_type'] == 'copper' || $q_data['cable_type'] == '') theme-th-selected @else th-noBorder @endif " onclick="navPage(updateQueryParameter('', 'cable', 'copper'))">Copper</th>
                        <th class="clickable @if ($q_data['cable_type'] == 'fibre') theme-th-selected @else th-noBorder @endif " onclick="navPage(updateQueryParameter('', 'cable', 'fibre'))">Fibre</th>
                        <th class="clickable @if ($q_data['cable_type'] == 'power') theme-th-selected @else th-noBorder @endif " onclick="navPage(updateQueryParameter('', 'cable', 'power'))">Power</th>
                        <th class="clickable @if ($q_data['cable_type'] == 'other') theme-th-selected @else th-noBorder @endif " onclick="navPage(updateQueryParameter('', 'cable', 'other'))">Other</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan=4 class="theme-th-selected">
                            <table class="table table-dark theme-table centertable" id="inventoryTable" style="padding-bottom:0px;margin-bottom:0px;">
                                <thead style="text-align: center; white-space: nowrap;">
                                    <tr>
                                        <th id="stock-id" hidden>Stock ID</th>
                                        <th id="item-id" hidden>Item ID</th>
                                        <th id="image"></th>
                                        <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(3, this)">Name</th>
                                        <th id="type-id" hidden>Type ID</th>
                                        <th class="clickable sorting viewport-large-empty" id="type" onclick="sortTable(5, this)">Type</th>
                                        <th class="clickable sorting" id="site" onclick="sortTable(6, this)">Site</th>
                                        <th class="clickable sorting" id="quantity" onclick="sortTable(7, this)">Quantity</th>
                                        <th id="min-stock" class="viewport-large-empty" style="color:#8f8f8f">Min. stock</th>
                                        <th class="viewport-small-empty" style="color:#8f8f8f">Min.</th>
                                        <th class="btn-cableStock"></th>
                                        <th class="btn-cableStock"></th>
                                        <th class="btn-cableStock"></th>
                                    </tr>
                                </thead>
                                <tbody id="inv-body" class="align-middle" style="text-align: center; white-space: nowrap;">
                                </tbody>
                            </table>
                            <!-- pagination bits -->
                            <table class="table table-dark theme-table centertable">
                                <tbody>
                                    <tr class="theme-tableOuter">
                                        <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                                        <div class="row">
                                            <div class="col text-center"></div>
                                            <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                                            </div>
                                            <div class="col text-center">
                                                <table style="margin-left:auto; margin-right:20px">
                                                    <tbody>
                                                        <tr>
                                                            <td class="theme-textColor align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                                Rows: 
                                                            </td>
                                                            <td class="align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                                <select id="tableRowCount" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" name="rows" onchange="navPage(updateQueryParameter('', 'rows', this.value))">
                                                                    <option id="rows-10"  value="10" @if ($q_data['rows'] == 10) selected @endif >10</option>
                                                                    <option id="rows-50"  value="50" @if ($q_data['rows'] == 50) selected @endif >50</option>
                                                                    <option id="rows-100" value="100" @if ($q_data['rows'] == 100) selected @endif >100</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 

    @include('includes.stock.new-properties')

    <div id="modalDivNewType" class="modal">
        <!-- <div id="modalDivProperties" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewType()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form action="includes/cablestock.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <table class="centertable">
                        <tbody>
                        <tr class="align-middle">
                                <td style="width:150px">Parent:</td>
                                <td>
                                    <select class="form-control" name="type-parent" style="min-width:150px;max-width:300px" required>
                                            <option value="" selected disabled hidden>Select Parent</option>
                                            <option value="Copper">Copper</option>
                                            <option value="Fibre">Fibre</option>
                                            <option value="Power">Power</option>
                                            <option value="Other">Other</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px">New Type:</td>
                                <td>
                                    <input class="form-control" type="text" style="min-width:150px;max-width:300px" placeholder="New Type" name="type-name" required/>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px">Description:</td>
                                <td>
                                    <input class="form-control" type="text" style="min-width:150px;max-width:300px" placeholder="Description" name="type-description" required/>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <td style="width:150px"></td>     
                                <td><input type="submit" name="submit" value="Add Type" class="btn btn-success"></td>
                                <td hidden=""><input type="hidden" name="new-type" value="1"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>  
        </div>
    </div>
    
    <!-- Add the JS for the file -->
    <script src="js/cablestock.js"></script>
        
    @include('foot')

</body>