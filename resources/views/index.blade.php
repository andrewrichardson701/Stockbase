<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}}</title>
</head>
<body onload="getInventory(0)">
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="content">
        {!! $response_handling !!}

        <!-- Get Inventory -->
        <input id="hidden-row-count" type="hidden" value="{{$q_data['rows']}}" />
        <input id="hidden-page-number" type="hidden" value="{{$q_data['page']}}" />
        <input id="hidden-oos" type="hidden" value="{{$q_data['oos']}}" />
        <pre id="hidden-sql" hidden></pre>
        
        @if ($sites['count'] == 0 || $areas['count'] == 0 || $shelves['count'] == 0)
            <div class="container" style="margin-top:20px">
                <h2 style="padding-bottom:20px;padding-top:20px">Add First Locations</h2>
                <form id="addLocations" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <input type="hidden" name="index" value="1"/>
                    <table id="area-table">
                        <tbody>
                            <tr class="nav-row" id="area-headings" style="margin-bottom:20px">
                                <th style="width:250px;"><h3 style="font-size:22px">Add Site</h3></th>
                                <th style="width: 250px"></th>
                            </tr>
                            <tr class="nav-row" id="site-name-row">
                                <td id="site-name-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="site-name">Site Name:</p>
                                </td>
                                <td id="site-name-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="site-name" name="site-name"required>
                                </td>
                            </tr>
                            <tr class="nav-row" id="site-description-row">
                                <td id="site-description-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="site-description">Site Description:</p>
                                </td>
                                <td id="site-description-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="site-description" name="site-description"required>
                                </td>
                            </tr>
                            
                            <tr class="nav-row" id="area-headings" style="margin-top:50px;margin-bottom:20px">
                                <th style="width:250px;"><h3 style="font-size:22px">Add Area</h3></th>
                                <th style="width: 250px"></th>
                            </tr>
                            <tr class="nav-row" id="area-name-row">
                                <td id="area-name-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="area-name">Area Name:</p>
                                </td>
                                <td id="area-name-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="area-name" name="area-name"required>
                                </td>
                            </tr>
                            <tr class="nav-row" id="area-description-row">
                                <td id="area-description-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="area-description">Area Description:</p>
                                </td>
                                <td id="area-description-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="area-description" name="area-description"required>
                                </td>
                            </tr>

                            <tr class="nav-row" id="shelf-headings" style="margin-top:50px;margin-bottom:20px">
                                <th style="width:250px;"><h3 style="font-size:22px">Add Shelf</h3></th>
                                <th style="width: 250px"></th>
                            </tr>
                            <tr class="nav-row" id="shelf-name-row">
                                <td id="shelf-name-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="shelf-name">Shelf Name:</p>
                                </td>
                                <td id="shelf-name-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="shelf-name" name="shelf-name"required>
                                </td>
                            </tr>
                            
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:250px">
                                    <input id="location-submit" type="submit" name="location-submit" class="btn btn-success" style="margin-left:25px" value="Submit">
                                </td>
                                <td style="width:250px">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        @else
            <div class="container" id="search-fields" style="max-width:max-content;margin-bottom:10px; margin-top:20px">
                <div class="nav-row">
                    <form action="./" method="get" class="nav-row" style="max-width:max-content">
                        <input id="query-site" type="hidden" name="site" value="{{$q_data['site']}}" /> 
                        <input id="query-area" type="hidden" name="area" value="{{$q_data['area']}}" />
                        <input id="query-oos" type="hidden" name="oos" value="{{$q_data['oos']}}" /> 
                        <span id="search-input-site-span" style="margin-bottom:10px; margin-right:10px" class="index-dropdown">
                            <label for="search-input-site">Site</label><br>
                            <select id="site-dropdown" name="site" class="form-control nav-v-b theme-dropdown" oninput="getInventory(1)" >
                                <option value="0" @if ($q_data['site'] == 0) selected @endif >All</option>
                            @foreach ($sites['rows'] as $site) 
                                <option value="{{$site['id']}}" @if ($q_data['site'] == $site['id']) selected @endif >{{$site['name']}}</option>
                            @endforeach
                            </select>
                        </span>
                        <span id="search-input-area-span" style="margin-bottom:10px; margin-right:10px" class="index-dropdown">
                            <label for="area-dropdown">Area</label><br>
                            <select id="area-dropdown" name="area" class="form-control nav-v-b theme-dropdown" oninput="getInventory(1)" >
                                <option value="0" @if ($q_data['area'] == 0) selected @endif >All</option>
                            @foreach ($areas['rows'] as $area) 
                                <option value="{{$area['id']}}" @if ($q_data['area'] == $area['id']) selected @endif >{{$area['name']}}</option>
                            @endforeach
                            </select>
                        </span>
                        <span id="search-input-name-span" style="margin-right:0.5em;margin-bottom:10px;">
                            <label for="search-input-name">Name</label><br>
                            <input id="search-input-name" type="text" name="name" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Name" oninput="getInventory(1)" value="{{$q_data['name']}}" />
                        </span>
                        <span class="viewport-large-block" id="search-input-sku-span" style="margin-right:0.5em;margin-bottom:10px;">
                            <label for="search-input-sku">SKU</label><br>
                            <input id="search-input-sku" type="text" name="sku" class="form-control" style="width:160px;display:inline-block" placeholder="Search by SKU" oninput="getInventory(1)" value="{{$q_data['sku']}}" />
                        </span>
                        <span class="viewport-large-block" id="search-input-shelf-span" style="margin-right:0.5em;margin-bottom:10px;">
                            <label for="search-input-shelf">Shelf</label><br>
                            <input id="search-input-shelf" type="text" name="shelf" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Shelf" oninput="getInventory(1)" value="{{$q_data['shelf']}}" />
                        </span>
                        <span class="viewport-large-block" id="search-input-manufacturer-span" style="margin-right:0.5em;margin-bottom:10px;">
                            <label for="search-input-manufacturer">Manufacturer</label><br>
                            <select id="search-input-manufacturer" name="manufacturer" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Manufacturer" onchange="getInventory(1)">
                                <option value="" @if ($q_data['manufacturer'] == '' || !isset($q_data['manufacturer'])) selected @endif >All</option>
                            @foreach ($manufacturers['rows'] as $manufacturer) 
                                <option value="{{$manufacturer['name']}}" @if ($q_data['manufacturer'] == $manufacturer['name']) selected @endif >{{$manufacturer['name']}}</option>
                            @endforeach
                            </select>
                        </span>
                        <span class="viewport-large-block" id="search-input-label-span" style="margin-right:1em;margin-bottom:10px;">
                            <label for="search-input-label">Tag</label><br>
                            <select id="search-input-tag" name="tag" class="form-control" style="width:160px;display:inline-block" placeholder="Search by Tag" onchange="getInventory(1)">
                                <option value="" @if ($q_data['tag'] == '' || !isset($q_data['tag'])) selected @endif >All</option>
                            @foreach ($tags['rows'] as $tag) 
                                <option value="{{$tag['name']}}" @if ($q_data['tag'] == $tag['name']) selected @endif >{{$tag['name']}}</option>
                            @endforeach
                                <option value="tags" class="gold link theme-tableOuter">view tags</option>
                            </select>
                        </span>
                        <input type="submit" value="submit" hidden>
                    </form>
                    <div id="clear-div" class="nav-div viewport-large-block" style="margin-left:0px;margin-right:0px;margin-bottom:10px;">
                        <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage('/')">
                            <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                        </button>
                    </div>
                    <div id="zero-div" class="nav-div viewport-large-block" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                    @if ($q_data['oos'] == 0)
                        <button id="zerostock" class="btn btn-success nav-v-b" style="opacity:90%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter('', 'oos', '1'))">
                    @else
                        <button id="zerostock" class="btn btn-danger nav-v-b" style="opacity:80%;color:black;padding:0px 2px 0px 2px" onclick="navPage(updateQueryParameter('', 'oos', '0'))">
                    @endif
                            <span class="zeroStockFont">
                                <p style="margin:0px;padding:0px"> @if ($q_data['oos'] == 0) <i class="fa fa-plus"></i> Show @else <i class="fa fa-minus"></i> Hide @endif </p>
                                <p style="margin:0px;padding:0px">0 Stock</p>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- mobile layout section -->
            <div class="container viewport-small" style="margin-top:-10px;max-width:max-content;">
                <div class="nav-row">
                    <div id="clear-div" class="nav-div" style="margin-left:0px;margin-right:0px;margin-bottom:10px;">
                        <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage('/')">
                            <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                        </button>
                    </div>
                    <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
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
                    <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px;margin-bottom:10px;">
                        <button id="cable-stock" class="btn clickable btn-dark nav-v-b" style="opacity:90%;color:white;padding:6px 6px 6px 6px" onclick="navPage('cablestock')">
                            Cables
                        </button>
                    </div>
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
                <table class="table table-dark theme-table centertable" id="inventoryTable" style="margin-bottom:0px;">
                    <thead style="text-align: center; white-space: nowrap;">
                        <tr class="theme-tableOuter">
                            <th id="id" hidden>id</th>
                            <th id="img"></th>
                            <th class="clickable sorting sorting-asc" id="name" onclick="sortTable(2, this)">Name</th>
                            <th class="clickable sorting viewport-large-empty" id="sku" onclick="sortTable(3, this)">SKU</th>
                            <th class="clickable sorting" id="quantity" onclick="sortTable(4, this)">Quantity</th>
                            <th class="clickable sorting" id="site" onclick="sortTable(5, this)" @if ((int)$q_data['site'] !== 0) hidden @endif>Site</th>
                            <th id="areas">Area(s)</th>
                            <th id="tags" class="viewport-large-empty">Tags</th>
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
            </div>
            <!-- End of Table -->            
        @endif
    </div> 

    <!-- Add the JS for the file -->
    <script src="js/index.js"></script>

    @include('foot')
</body>