@if (($stock_data['count'] ?? 0) < 1)
    <div class="container" id="no-stock-found">No Stock Found</div>
@else
    <script src="{{ asset('js/favourites.js') }}"></script>
    <div id="favouriteButton" class="" style="width: max-content">
        <button onclick="favouriteStock({{ $params['stock_id'] }})" class="favouriteBtn" id="favouriteBtn" title="Favourite Stock">
            <i id="favouriteIcon" class=" @if (($favourited ?? 0) == 1) fa-solid @else fa-regular @endif fa-star"></i>
        </button>
    </div>
    <div class="container stock-heading">
        @include('includes.response-handling')
        <div class='row ' style='margin-top:5px;margin-top:10px;'>
            <div class='col' style='margin-top:auto;margin-bottom:auto;'>
                <h3 style='font-size:22px;margin-bottom:0px;' id='stock-name'>{{ $stock_data['name'] }} ({{ $stock_data['sku'] }})</h3>
                <input type='hidden' id='hiddenStockName' value='".$stock_name."'>
            </div>
            
        </div>
        <p id='stock-description' style='color:#898989;margin-bottom:0px;margin-top:10px'>{{ str_replace(array("\r\n","\\r\\n"), "<br/>", $stock_data['description']) }}</p>
        @if ($stock_data['deleted'] == 1)
            <p class="red" style="margin-top:20px;font-size:20">Stock Deleted. <a class="link" style="font-size:20" href="{{ url('admin') }}#stockmanagement-settings">Restore?</a></p>
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
                    <tbody>
                @foreach ($stock_inv_data['rows'] as $key => $row)
                    @if (((int)$row['quantity'] ?? 0) !== 0)
                        <tr id="stock-row-{{ $key }}">
                            <td hidden>{{ $row['id'] }}</td>
                            <td id="stock-row-{{ $key }}-site-{{ $row['site_id'] }}"><or class="clickable" onclick="window.location.href='{{ url('/') }}?site={{ $row['site_id'] }}">{{ $row['site_name'] }}</or></td>
                            <td id="stock-row-{{ $key }}-area-{{ $row['area_id'] }}" style="padding-left: 10px"><or class="clickable" onclick="window.location.href='{{ url('/') }}?site={{ $row['site_id'] }}&area={{ $row['area_id'] }}'">{{ $row['area_name'] }}</or>:</td>
                            <td id="stock-row-{{ $key }}-shelf-{{ $row['shelf_id'] }}" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold clickable" onclick="window.location.href='{{ url('/') }}?shelf={{ str_replace(' ', '+', $row['shelf_name']) }}'">{{ $row['shelf_name'] }}</button></td>
                            <td style="padding-left: 5px" class="text-center theme-textColor">{{ (int)$row['quantity'] }}</td>
                        </tr>
                    @endif
                @endforeach

                @if (((int)$stock_inv_data['total_quantity'] ?? 0) == 0)
                    <tr id="stock-row-na-0">
                        <td colspan=100% style="padding-left: 5px" class="text-center">N/A</td>
                    </tr>
                @endif

                    </tbody>
                </table>
                <p id="min-stock" style="margin-top:15px"><strong>Minimum Stock Count:</strong> <or class="specialColor">{{ $stock_data['min_stock'] }}</or></p>

                @if ($stock_data['is_cable'] == 0)
                    <p class="clickable gold" id="extra-info-dropdown" onclick="toggleSection(this, 'extra-info')">More Info <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></p> 
                    <div id="extra-info" hidden>
                        <p id="tags-head"><strong>Tags</strong></p>
                        <p id="tags">
                        @if (isset($stock_inv_data['tags']) && is_array($stock_inv_data['tags'])) 
                            @foreach($stock_inv_data['tags'] as $key => $tag)
                                <button class="btn theme-btn btn-stock-click gold clickable" id="tag-{{ $tag['id'] }}" onclick="window.location.href='{{ url('/') }}?tag={{ $tag['name'] }}'">{{ $tag['name'] }}</button> 
                            @endforeach
                        @else
                            None
                        @endif
                        </p>
                        <p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers">
                        @if (isset($stock_inv_data['manufacturers']) && is_array($stock_inv_data['manufacturers'])) 
                            @foreach($stock_inv_data['manufacturers'] as $key => $manufacturer)
                                <button class="btn theme-btn btn-stock-click gold clickable" id="manufacturer-{{ $manufacturer['id'] }}" onclick="window.location.href='{{ url('/') }}?manufacturer={{ $manufacturer['name'] }}'">{{ $manufacturer['name'] }}</button> 
                            @endforeach
                        @else
                            None
                        @endif
                        </p>
                        <p id="serial-numbers-head"><strong>Serial Numbers</strong></p>
                        <p>
                        @foreach ($serial_numbers as $key => $row)
                            <a class="serial-bg" id="serialNumber-{{ $key }}">{{ $row['serial_number'] }}</a>
                        @endforeach
                        </p>
            
                    </div>
                @endif
            </div>

            <div class="col text-right" id="stock-info-right">
            @if (!empty($stock_data['img_data']) && $stock_data['img_data']['count'] > 0)  
                <div class="well-nopad theme-divBg nav-right stock-imageBox">
                    <div class="nav-row stock-imageMainSolo">
                    @foreach ($stock_data['img_data']['rows'] as $key => $row)
                        @if ($loop->iteration == 1)
                            
                            @if ($stock_data['img_data']['count'] <= 1)
                                <div class="thumb theme-divBg-m text-center stock-imageMainSolo" onclick="modalLoadCarousel()">
                                    <img class="nav-v-c stock-imageMainSolo" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] }} - image {{ $loop->iteration }}" src="{{ asset('img/stock/' . $row['image']) }}" />
                                </div>
                                <span id="side-images" style="margin-left:5px">
                            @else 
                                <div class="thumb theme-divBg-m text-center stock-imageMain" onclick="modalLoadCarousel()">
                                    <img class="nav-v-c stock-imageMain" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] }} - image {{ $loop->iteration }}" src="{{ asset('img/stock/' . $row['image']) }}" />
                                </div>
                                <span id="side-images" style="margin-left:5px">
                            @endif
                        @endif
                        
                        @if ($loop->iteration == 2 || $loop->iteration == 3)
                            <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] }} - image {{ $loop->iteration }}" src="{{ asset('img/stock/' . $row['image']) }}" />
                            </div>
                        @endif
                        @if ($loop->iteration == 4)
                            @if ($loop->iteration < $stock_data['img_data']['count'])
                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                <p class="nav-v-c text-center stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-more">+{{ $stock_data['img_data']['count']-3 }}</p>
                            </div>
                            @else
                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-{{  $row['id'] }}" src="{{ asset('img/stock/' . $row['image']) }}" onclick="modalLoad(this)"/>
                            </div>
                            @endif
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
                        <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" src="{{ asset('img/stock/' . $row['image']) }}"/>
                    @endforeach
                    <img class="modal-content bg-trans" id="modalImg">
                    <div id="caption" class="modal-caption"></div>
                </div>
                <!-- End of Modal Image Div -->
                @else 
                <link rel="stylesheet" href="{{ asset('css/carousel.css') }}">
                <script src="{{ asset('js/carousel.js') }}"></script>
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
                            <div class="carousel-inner" align="centre">
                            @foreach ($stock_data['img_data']['rows'] as $key => $row)
                                <div class="item @if ($loop->iteration == 1) active @endif " align="centre">
                                <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id']}}-img-{{ $row['id'] }}" src="{{ asset('img/stock/' . $row['image']) }}"/>
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
                    <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter('{{ url('stock') }}/{{ $stock_data['id'] }}/edit', 'images', 'edit'))">
                        <i class="fa fa-plus"></i> Add images
                    </button>
                </div> 
            @endif
            </div>
        </div>
    </div>
    <div class="container well-nopad theme-divBg" style="margin-top:5px">
        <h2 style="font-size:22px">Stock</h2>

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
                        <th class="viewport-large-empty align-middle text-center" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif >Cost</th>
                        <th class="viewport-large-empty align-middle text-center">Comments</th>
                    @else 
                        <th class="viewport-large-empty align-middle text-center" @if ($head_data['config_compare']['cost_enable_cable'] == 0) hidden @endif >Cost</th>
                    @endif
                    
                    <th class="align-middle text-center">Stock</th>
                </tr>
            </thead>
            <tbody>                           
            @foreach($stock_distinct_item_data['rows'] as $row)
                <tr id="item-{{ $loop->iteration }}" @if ($stock_data['is_cable'] == 0) class="clickable row-show" onclick="toggleHiddenStock({{ $loop->iteration }})" @endif>
                    <td hidden>{{ $loop->iteration }}</td>
                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}" class="align-middle text-center">{{ $row['site_name'] }}</td>
                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}" class="align-middle text-center">{{ $row['area_name'] }}</td>
                    <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}-{{ $row['shelf_id'] }}" class="align-middle text-center">{{ $row['shelf_name'] }}</td>
                    @if ($stock_data['is_cable'] == 0)
                    <td id="item-{{ $loop->iteration }}-manu-{{ $row['manufacturer_id'] }}" class="align-middle text-center">{{  $row['manufacturer_name'] }}</td>
                    <td id="item-{{ $loop->iteration }}-upc" class="viewport-large-empty align-middle text-center">{{ $row['upc'] }}</td>
                    <td id="item-{{ $loop->iteration }}-sn" class="align-middle text-center">{{ $row['serial_number'] }}</td>
                    <td id="item-{{ $loop->iteration }}-tags" class="align-middle text-center" hidden>{{ $row['tag_names'] }}</td>
                    <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                    <td id="item-{{ $loop->iteration }}-comments" class="viewport-large-empty align-middle text-center">{{ $row['comments'] }}</td>
                    <td id="item-{{ $loop->iteration }}-stock" class="align-middle text-center">{{ (int)$row['quantity'] }}</td>
                    @else
                    <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($head_data['config_compare']['cost_enable_cable'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                    <td id="item-{{ $loop->iteration }}-stock" @if((int)$row['quantity'] < $stock_data['min_stock']) class="red align-middle text-center" title="Below minimum stock count. Please re-order." @else class="align-middle text-center" @endif >{{ (int)$row['quantity'] }}</td>
                    @endif
                </tr>
                @if ($stock_data['is_cable'] == 0)
                <tr id="item-{{ $loop->iteration }}-hidden" class="row-hide" hidden>
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
                                        <th class="align-middle text-center" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif>Cost ({{ $head_data['config_compare']['currency'] }})</th>
                                        <th class="align-middle text-center">Comments</th>
                                        <th class="align-middle text-center" colspan=2>Container</th>
                                        <th class="align-middle text-center">Stock</th>
                                        <th class="align-middle text-center"></th>
                                        <th class="align-middle text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php  $matchCount = 0; @endphp 

                                @foreach ($stock_item_data['rows'] as $item)
                                    @if ($item['manufacturer_id'] == $row['manufacturer_id'] &&
                                        $item['serial_number'] == $row['serial_number'] &&
                                        $item['comments'] == $row['comments'] &&
                                        $item['cost'] == $row['cost'] &&
                                        $item['shelf_id'] == $row['shelf_id'] &&
                                        $item['upc'] == $row['upc'] &&
                                        (int)$item['quantity'] !== 0
                                        ) 

                                        @php $matchCount++; @endphp
                                        
                                        <tr class="align-middle">
                                            <form action="includes/stock-modify.inc.php" method="POST" id="form-item-{{ $item['item_id'] }}" enctype="multipart/form-data">
                                            <!-- Include CSRF token in the form -->
                                                @csrf
                                            </form>
                                            <input type="hidden" form="form-item-{{ $item['item_id'] }}" name="submit" value="row"/>
                                            <td class="align-middle text-center"><input type="hidden" form="form-item-{{ $item['item_id'] }}" name="item-id" value="{{ $item['item_id'] }}" />{{ $item['item_id'] }}</td>
                                            <td hidden>{{ $item['site_name'] }}</td>
                                            <td hidden>{{ $item['area_name'] }}</td>
                                            <td hidden>{{ $item['shelf_name'] }}</td>
                                            <td class="align-middle text-center">
                                                <select class="form-control manufacturer-select" form="form-item-{{ $item['item_id'] }}" name="manufacturer_id" style="max-width:max-content">
                                                @if (!empty($manufacturers['rows']))
                                                    @foreach ($manufacturers['rows'] as $manufacturer) 
                                                    <option value="{{ $manufacturer['name'] }}" @if ($item['manufacturer_id'] == $manufacturer['id']) selected @endif >{{ $manufacturer['name'] }}</option>
                                                    @endforeach
                                                @else
                                                    <option disabled>No Manufacturers Found</option>
                                                @endif
                                                </select>
                                            </td>
                                            <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ $item['upc'] }}" name="upc" /></td>
                                            <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ $item['serial_number'] }}" name="serial_number" /></td>
                                            <td class="align-middle text-center" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif ><input type="number" step=".01" form="form-item-{{ $item['item_id'] }}" class="form-control" style="width:75px" value="{{ $item['cost'] }}" name="cost" min=0 /></td>
                                            <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ $item['comments'] }}" name="comments" /></td>
                                            @if (isset($container_data['rows'][$item['container_id']]) && !empty($container_data['rows'][$item['container_id']]))
                                                <td class="align-middle text-center" style="padding-right:2px" @if ($item['is_container'] == 1) colspan=2 @endif>
                                                @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0) 
                                                    <input type="checkbox" form="form-item-{{ $item['item_id'] }}" name="container-toggle" checked hidden>
                                                @endif
                                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:0px" >
                                                        <input type="checkbox" form="form-item-D"
                                                            @if ($item['is_container'] == 1) 
                                                                @if (isset($matchCount) && $matchCount > 0)
                                                                    checked name="container-toggle-disabled" disabled
                                                                @else
                                                                    checked name="container-toggle"
                                                                @endif
                                                            @else 
                                                                name="container-toggle"
                                                            @endif
                                                        >
                                                        @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0)
                                                        <span class="slider round align-middle" style="transform: scale(0.8, 0.8); opacity: 0.5; cursor: no-drop;" title="Please un-assign the children first"></span>
                                                        @else
                                                        <span class="slider round align-middle" style="transform: scale(0.8, 0.8);"></span>
                                                        @endif
                                                    </label>
                                                </td>
                                                @if ($item['is_container'] == 0)
                                                    <td class="align-middle text-center" style="padding-left:2px">
                                                            <button class="btn btn-warning" type="button" style="opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Link to container" onclick="modalLoadLinkToContainer({{ $item['item_id'] }})">
                                                                <i class="fa fa-link"></i>
                                                            </button>
                                                    </td>
                                                @endif
                                            @elseif ($item['container_id'] == null && $item['is_container'] == 0)
                                                <td class="align-middle text-center" style="padding-right:2px">
                                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:0px">
                                                        <input type="checkbox" form="form-item-{{ $item['item_id'] }}" name="container-toggle">
                                                        <span class="slider round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                                    </label>
                                                </td>
                                                <td class="align-middle text-center" style="padding-left:2px">
                                                        <button class="btn btn-warning" type="button" style="opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Link to container" onclick="modalLoadLinkToContainer({{ $item['item_id'] }})">
                                                            <i class="fa fa-link"></i>
                                                        </button>
                                                </td>
                                            @else
                                                <td class="align-middle text-center" style="padding-right:2px">
                                                    <a class="link" id="modalUnlinkContainerItemName-{{ $item['item_id'] }}" href="{{ url('containers') }}?container_id={{ $item['container_id'] }}&con_is_item={{ $item['container_is_item'] }}">@if($item['container_is_item'] == 1) {{ $item['container_item_name'] }} @else {{ $item['container_name'] }} @endif</a>
                                                </td>
                                                <td class="align-middle text-center" style="padding-left:2px">
                                                    <form action="includes/stock-modify.inc.php" method="POST" id="form-item-{{ $item['item_id'] }}-container-unlink" enctype="multipart/form-data">
                                                        <!-- Include CSRF token in the form -->
                                                        @csrf
                                                        <input type="hidden" name="item_id" value="{{ $item['item_id'] }}" form="form-item-{{ $item['item_id'] }}-container-unlink" />
                                                        <input type="hidden" name="container-unlink" value="1" form="form-item-{{ $item['item_id'] }}-container-unlink" />
                                                        <button class="btn btn-danger" type="button" name="submit" onclick='modalLoadUnlinkContainer("{{ $item['item_id'] }}", "{{ $item['container_id'] }}", 1)' form="form-item-{{ $item['item_id'] }}-container-unlink" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                            <i class="fa fa-unlink"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                            <td class="align-middle text-center">{{ (int)$item['quantity'] }}</td>
                                            <td style="padding-right:3px"><input type="submit" form="form-item-{{ $item['item_id'] }}" class="btn btn-success" name="stock-row-submit" value="Update" /></td>
                                            <td style="padding-left:3px"><button class="btn btn-danger" onclick="navPage(updateQueryParameter('stock/{{ $stock_data['id'] }}?manufacturer={{ $item['manufacturer_id'] }}&shelf={{ $item['shelf_id'] }}&serial={{ $item['serial_number'] }}', 'modify', 'remove'))" @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0) disabled @endif><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                        @if ($item['is_container'] == 1 && (int)$container_data['rows'][$item['item_id']]['count'] > 0)
                                            <tr class="theme-th-selected">
                                                <td colspan="100%">
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
                                                                    <button class="btn btn-success" type="submit" name="button" onclick="modalLoadAddChildren({{ $item['item_id'] }})" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Add more children">
                                                                        + <i class="fa fa-link"></i>
                                                                    </button>
                                                                </th>
                                                            </thead>
                                                            <tbody>
                                                            @if ((int)$container_data['rows'][$item['item_id']]['count'] == 0)
                                                                <tr><td class="align-middle text-center" colspan=100%>No contents found.</td><tr>
                                                            @else 
                                                                @foreach($container_data['rows'][$item['item_id']]['rows'] as $child)
                                                                <tr class="align-middle">
                                                                    <td class="align-middle text-center">{{ $child['item_id'] }}</td>
                                                                    <td class="align-middle text-center" style="white-space:wrap;"><a class="link" href="{{ url('stock') }}/{{ $child['stock_id'] }}" id="modalUnlinkContainerItemName-{{ $child['item_id'] }}">{{ $child['stock_name'] }}</a></td>
                                                                    <td class="align-middle text-center">{{ $child['item_upc'] }}</td>
                                                                    <td class="align-middle text-center">{{ $child['item_serial_number'] }}</td>
                                                                    <td class="align-middle text-center">{{ $child['item_comments'] }}</td>
                                                                    <td class="align-middle text-center">
                                                                        <input type="hidden" id="modalUnlinkContainerName" value="{{ $child['stock_name'] }}" />
                                                                        <button class="btn btn-danger" type="submit" name="button" onclick="modalLoadUnlinkContainer({{ $item['item_id'] }}, {{ $child['item_id']  }}, 0)" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                                            <i class="fa fa-unlink"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @if ($matchCount == 0)
                                    <tr><td colpan=100%>No Stock Found</td></tr>
                                @endif  
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>                 
@endif

@include('includes.stock.transactions')