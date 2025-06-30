{{-- {{ dd($head_data, $stock_move_data, $stock_data, $stock_inv_data, $stock_item_data, $stock_move_data, $favourited, $serial_numbers, $container_data, $manufacturers, $sites, $transactions, $tag_data) }} --}}
<div class="nav-row" style="margin-bottom:10px">
    <div class="nav-row" id="heading-row" style="margin-top:10px">
        <div class="stock-inputLabelSize"></div>
        <div id="heading-heading">
            <a href="{{ url('stock/'.$stock_data['id']) }}"><h2 id="stock_name">{{ $stock_data['name'] }}</h2></a>
            <p id="sku"><strong>SKU:</strong> <or class="blue">{{ $stock_data['sku'] }}</or></p>
        </div>
    </div>
</div>
{{-- {{ dd($stock_move_data) }} --}}
<div style="width:100%">
    <table class="table table-dark theme-table centertable" style="max-width:max-content">
        <thead>
            <tr class="theme-tableOuter">
                <th hidden>ID</th>
                <th>Site</th>
                <th>Location</th>
                <th>Shelf</th>
                @if ($stock_data['is_cable'] == 0) 
                    <th>Container</th>
                    <th class="viewport-mid-large">Manufacturer</th>
                    <th class="viewport-small-only-empty">Manu.</th>
                    <th class="viewport-mid-large">UPC</th>
                    <th title="Serial Numbers">Serial</th>
                    <th @if($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif>Cost</th>
                    <th class="viewport-mid-large">Comments</th>
                @else
                    <th @if($head_data['config_compare']['cost_enable_cable'] == 0) hidden @endif>Cost</th>
                @endif
                <th>Stock</th>
            </tr>
        </thead>
        <tbody> 

        @if ($stock_move_data['count'] > 0 && !empty($stock_move_data['rows']))
            @foreach($stock_move_data['rows'] as $key => $row)
            <tr id="item-{{ $key }}" class="row-show clickable @if (isset($params['edited']) && $params['edited'] == $key) last-edit @endif" onclick="toggleHidden({{ $key }})">
                <td hidden>{{ $key }}</td>
                <td id="item-{{ $key }}-{{ $row['site_id'] }}">{{ $row['site_name'] }}</td>
                <td id="item-{{ $key }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}">{{ $row['area_name'] }}</td>
                <td id="item-{{ $key }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}-{{ $row['shelf_id'] }}">{{ $row['shelf_name'] }}</td>
                @if ($stock_data['is_cable'] == 0) 
                    <td class="text-center" id="item-{{ $key }}-container-{{ $row['container_id'] }}">
                        @if ($row['container_id'] !== null) 
                            <a href="{{ route('containers') }}?container_id={{ $row['container_id'] }}&con_is_item={{ $row['container_is_item'] }}">{{ $row['container_data']['container_name'] }}</a> 
                        @endif 
                        @if ($row['is_container'] !== 0) 
                            <i class="fa-solid fa-check" style="color:lime;padding-left:5px"></i> 
                        @endif 
                    </td>
                    <td id="item-{{ $key }}-manu-{{ $row['manufacturer_id'] }}">{{ $row['manufacturer_name'] }}</td>
                    <td id="item-{{ $key }}-upc" class="viewport-mid-large">{{ $row['upc'] }}</td>
                    <td id="item-{{ $key }}-sn">{{ $row['serial_number'] }}</td>
                    <td id="item-{{ $key }}-cost" @if($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif>{{ $head_data['config_compare']['currency'] }}{{ $row['cost'] }}</td>
                    <td id="item-{{ $key }}-comments" class="viewport-mid-large">{{ $row['comments'] }}</td>
                @else
                    <td id="item-{{ $key }}-cost" @if($head_data['config_compare']['cost_enable_cable'] == 0) hidden @endif>{{ $head_data['config_compare']['currency'] }}{{ $row['cost'] }}</td>
                @endif
                <td id="item-{{ $key }}-stock">{{ $row['quantity'] }}</td>
            </tr>
            <tr class="row-hide" id="item-{{ $key }}-edit" hidden>
                <td colspan=100%>
                    <div class="container">                                                       
                        <table class="centertable" style="border: 1px solid #454d55;">
                            <form class="" action="@if($stock_data['is_cable'] == 0 ){{ route('stock.move') }}@else{{ route('stock.move.cable') }}@endif" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0">
                                <!-- Include CSRF token in the form -->
                                @csrf
                                @if ($stock_data['is_cable'] == 0) 
                                    <input type="hidden" name="stock-move" value="1" />
                                @else
                                    <input type="hidden" name="cablestock-move" value="1" /> 
                                        <input type="hidden" name="redirect_url" value="stock.php?stock_id={{ $params['stock_id'] }}&modify=move" />
                                        <input type="hidden" name="current_cable_item" value="{{ $params['stock_id'] }}" />
                                @endif
                                <input type="hidden" id="{{ $key }}-c-i" name="current_i" value="{{ $key }}" />
                                <input type="hidden" id="{{ $key }}-c-stock" name="current_stock" value="{{ $params['stock_id'] }}" />
                                <input type="hidden" id="{{ $key }}-c-site" name="current_site" value="{{ $row['site_id'] }}" />
                                <input type="hidden" id="{{ $key }}-c-area" name="current_area" value="{{ $row['area_id'] }}" />
                                <input type="hidden" id="{{ $key }}-c-shelf" name="current_shelf" value="{{ $row['shelf_id'] }}" />
                                @if ($stock_data['is_cable'] == 0) 
                                    @if (isset($row['container_id']) && $row['container_id'] !== null) 
                                        <input type="hidden" name="in_container" value="1" />
                                    @endif
                                    <input type="hidden" id="{{ $key }}-c-manufacturer" name="current_manufacturer" value="{{ $row['manufacturer_id'] }}" />
                                    <input type="hidden" id="{{ $key }}-c-upc" name="current_upc" value="{{ $row['upc'] }}" />
                                    <input type="hidden" id="{{ $key }}-c-serial" name="current_serial" value="{{ $row['serial_number'] }}" />
                                    <input type="hidden" id="{{ $key }}-c-comments" name="current_comments" value="{{ $row['comments'] }}" />
                                @endif
                                <input type="hidden" id="{{ $key }}-c-cost" name="current_cost" value="{{ $row['cost'] }}" />
                                <input type="hidden" id="{{ $key }}-c-quantity" name="current_quantity" value="{{ $row['quantity'] }}" />
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col" style="max-width:max-content !important">
                                                    <label class="nav-v-c">To:</label>
                                                </div>
                                                <div class="col" style="max-width:max-content !important">
                                                    <select class="form-control nav-v-c row-dropdown theme-dropdown" id="{{ $key }}-n-site" name="site" style="min-width:50px; padding:2px 0px 2px 0px;  width:max-content !important" required onchange="populateAreas({{ $key }})">
                                                        <option value="" selected disabled hidden>Site</option>
                                                        @if ($sites['count'] > 0 && !empty($sites['rows']))
                                                            @foreach($sites['rows'] as $site)
                                                            <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="" selected disabled>No Sites Found</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col" style="max-width:max-content !important">
                                                    <select class="form-control nav-v-c row-dropdown theme-dropdown" id="{{ $key }}-n-area" name="area" style="min-width:50px; padding:2px 0px 2px 0px; max-width:max-content !important" disabled required onchange="populateShelves({{ $key }})">
                                                        <option value="" selected disabled hidden>Area</option>
                                                    </select>
                                                </div>
                                                <div class="col" style="max-width:max-content !important">
                                                    <select class="form-control nav-v-c row-dropdown theme-dropdown" id="{{ $key }}-n-shelf" name="shelf" style="min-width:50px; padding:2px 0px 2px 0px; max-width:max-content !important" disabled required>
                                                        <option value="" selected disabled hidden>Shelf</option>
                                                    </select>
                                                </div>
                                                <div class="col" style="max-width:max-content !important">
                                                    <label class="nav-v-c" for="{{ $key }}-n-quantity">Quantity: </label>
                                                </div>
                                                <div class="col" style="max-width:max-content !important">
                                                    <input type="number" class="form-control nav-v-c row-dropdown theme-input" id="{{ $key }}-n-quantity" name="quantity" style="min-width: 20px; padding: 2px 7px 2px 7px; max-width:50px;" placeholder="1" value="1" min="1" max="{{ $row['quantity'] }}" required />
                                                </div>
                                                @if ($stock_data['is_cable'] == 0) 
                                                    <div class="col" style="max-width:max-content !important">
                                                        <input type="text" class="form-control nav-v-c row-dropdown theme-input" id="{{ $key }}-n-serial" name="serial" style="min-width: 80px; padding: 2px 7px 2px 7px; width:max-content; max-width:90px" placeholder="@if (isset($row['serial_number']) && $row['serial_number'] !== '') {{ $row['serial_number'] }} @else No Serial Number @endif" value="{{ $row['serial_number'] }}" disabled /> 
                                                    </div>
                                                @endif
                                                <div class="col" style="max-width:max-content !important">
                                                @if (isset($row['is_container']) && $row['is_container'] == 1) 
                                                    <input type="button" class="btn btn-warning nav-v-c btn-move" id="{{ $key }}-n-submit" value="Move" style="opacity:80%;" name="submit" required onclick="modalLoadContainerMoveConfirmation({{ $key }}, {{ $row['container_item_data']['id'] }})" />
                                                @else
                                                    <input type="submit" class="btn btn-warning nav-v-c btn-move" id="{{ $key }}-n-submit" value="Move" style="opacity:80%;" name="submit" required />
                                                @endif
                                                </div>
                                            </div>
                                            @if (isset($row['container_id']) && $row['container_id'] !== null) 
                                                <div class="row">
                                                    <div class="col text-center" style="width:100%">
                                                        <p class="red" style="margin:15px 0px 0px 0px">* Moving stock from within a container will remove the container link. *</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($row['is_container']) && $row['is_container'] == 1) 
                                                <div class="row">
                                                    <div class="col text-center" style="width:100%">
                                                        <p style="margin:15px 0px 0px 0px"><or class="red">* This item is a container. Please consider its contents before moving. *</or><br>Check container: <a href="{{ route('containers') }}?container_id={{ $row['container_item_data']['id'] }}&con_is_item=1">{{ $row['stock_name'] }}</a></p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </td>
                                </tbody>
                            </form>
                        </table>
                    </div>
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colpsan=100% class="text-center">No data found</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
<!-- Container Move item Modal -->
<div id="modalDivContainerMoveConfirmation" class="modal">
    <span class="close" onclick="modalCloseContainerMoveConfirmation()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <form class="padding:0px;margin:0px" id="containerMoveForm" action="{{ route('stock.move.container') }}" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            @csrf
            <input type="hidden" name="submit" value="1" />
            <input type="hidden" name="container-move" value="1" />
            <input type="hidden" id="containerMoveItemID" name="item_id" />
            <input type="hidden" id="containerMoveShelf" name="shelf_id" />
            <input type="hidden" id="containerMoveQuantity" name="quantity" />
            <input type="hidden" id="containerMoveCurrentStock" name="current_stock" value="{{ $stock_data['id'] }}"/>
            <input type="hidden" id="containerMoveCurrentShelf" name="current_shelf" />
            <input type="hidden" id="containerMoveCurrentSerial" name="current_serial" />
            <input type="hidden" id="containerMoveCurrentManufacturer" name="current_manufacturer" />
            <input type="hidden" id="containerMoveCurrentComments" name="current_comments" />
            <input type="hidden" id="containerMoveCurrentCost" name="current_cost" />
            <input type="hidden" id="containerMoveCurrentUPC" name="current_upc" />
        </form>
        <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
            <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Move Container Item</h4>
            <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="moveContainerItemID"></td><th style="padding-right:5px">Name:</th><td id="moveContainerItemName"></td></tr></tbody></table>
            <table class="centertable" style="margin-top:10px">
                <tbody style="border: none">
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will KEEP all child objects attached and move them with the current item. All child objects will be moved to the new location.">Move container and contents</or></td>
                        <td style="border: none"><input type="submit" form="containerMoveForm" class="btn btn-danger" name="container-move-all" value="Move All" /></td>
                    </tr>
                    <tr class="text-center align-middle" style="border: none">
                        <td class="text-center align-middle" style="padding-right:5px;border: none"><or class="title" title="This will UNLINK all child objects and ONLY move the selected item. All child objects will remain in the same location.">Move container only</or></td>
                        <td style="border: none"><input type="submit" form="containerMoveForm" class="btn btn-warning" name="container-move-single" value="Move Single Item" style="margin-top:5px;margin-bottom:5px"/></td>
                    </tr>
                </tbody>
            </table>
            <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                <p><strong>Contents</strong> - Items: <strong id="moveContainerChildCount" class="green"></strong></p>
                <table id="containerMoveContentsTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                    <thead>
                        <tr>
                            <th><!-- Image --></th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Name</th>
                            <th class="text-center align-middle">Description</th>
                            <th><!-- Unlink --></th>
                        </tr>
                    </thead>
                    <tbody id="containerMoveContentsTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
            <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseContainerMoveConfirmation()">Cancel</button>
        </span>
    </div>
</div>
<!-- End of Move Container Modal-->
@include('includes.stock.transactions')