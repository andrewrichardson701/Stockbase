<!-- NEW STOCK -->
<div class="container well-nopad theme-divBg">
    <input id="hidden-page-number" type="hidden" value="{{ $params['page'] }}'" />
    <pre id="hidden-sql" hidden></pre>
@if ($params['stock_id'] == 0 && $params['add_new'] == 'new')
    <!-- /stock/0/new -->
    <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
        <!-- this is for the stock-modify.inc.php page -->
        <!-- Include CSRF token in the form -->
        @csrf
        <input type="hidden" name="stock-add" value="1" /> 
        <div class="container well-nopad theme-divBg" style="margin-bottom:5px">
            <h3 style="font-size:22px; margin-left:25px">Add New Stock</h3>
            <div class="row">
                <div class="col-sm text-left" id="stock-info-left">
                    <div class="nav-row">
                        <div class="nav-row" id="name-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name <or class="red">*</or></label></div>
                            <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c stock-inputSize" value="{{ htmlspecialchars($params['request']['name'] ?? '', ENT_QUOTES, 'UTF-8') }}" required></input></div>
                        </div>
                        <div class="nav-row" id="sku-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                            <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['sku'] ?? null }}" pattern="^[A-Za-z0-9\p{P}]+$"></input></div>
                        </div>
                        <div class="nav-row" id="description-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                            <div><textarea class="form-control nav-v-c stock-inputSize" id="description" name="description" rows="3" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="{{ htmlspecialchars($params['request']['description'] ?? '', ENT_QUOTES, 'UTF-8') }}" ></textarea></div>
                        </div>
                        <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                            <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['min_stock'] ?? null }}"></input></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4" id="stock-info-right" style="margin-left:0px !important"> 
                    <div id="image-preview" style="height:150px;margin:auto;text-align:center">
                        <img class="nav-v-c" id="upload-img-pre" style="max-width:150px;max-height:150px" />
                    </div>
                    <div class="nav-row"  id="images-row" style="margin-top:25px">
                        <table class="centertable">
                            <tbody>
                                <tr>
                                    <td style="padding-right:25px" class="text-center viewport-small-empty">Image</td>
                                </tr>
                                <tr>
                                    <td style="padding-right:25px" class="viewport-large-empty">Image:</td>
                                    <td><input class=" text-center" type="file" accept="image/*" style="width: 15vw" id="image" name="image" onchange="loadImage(event)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <script>
                    var loadImage = function(event) {
                        var preview = document.getElementById('upload-img-pre');
                        preview.src = URL.createObjectURL(event.target.files[0]);
                        preview.onload = function() {
                        URL.revokeObjectURL(preview.src) // free memory
                        }
                    };
                    </script>
                </div>
                <div class="nav-row" id="tags-row" style="margin-top:25px;padding-left:15px;padding-right:15px">
                    <div class="stock-inputLabelSize"><label class="text-right" style="padding-top:5px;width:100%" for="tags" id="labels-tag">Tags</label></div>
                    <div>
                        <select class="form-control stock-inputSize" id="tag-select" name="tags-init">
                            <option value="" selected disabled hidden>-- Select a tag if needed --</option>
                        </select>

                        <select id="tags" name="tags[]" multiple class="form-control stock-inputSize" style="margin-top:2px;display: inline-block;height:40px"></select>
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
                        var selectBox = document.getElementById("tag-select");
                        var selectedBox = document.getElementById("tags");

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
                        </script>
                    </div>
                    <div>
                        <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('tag')">Add New</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="container well-nopad theme-divBg">
            <div class="row">
                <div class="text-left" id="stock-info-left" style="padding-left:15px">
                    <div class="nav-row" style="margin-bottom:25px">
                        <div class="nav-row" id="upc-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="upc" id="upc-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Universal Product Code for item">UPC</or></label></div>
                            <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['upc'] ?? null }}"></input></div>
                        </div>
                        <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                            <div  class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer <or class="red">*</or></label></div>
                            <div>
                                <select name="manufacturer" id="manufacturer-select" class="form-control stock-inputSize" required>
                                    <option value="" selected disabled hidden>Select Manufacturer</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('manufacturer')">Add New</label>
                            </div>
                        </div>
                        <div class="nav-row" id="site-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="site" name="site" required>
                                    <option value="" selected disabled hidden>Select Site</option>
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}" @if (isset($params['request']['site']) && $params['request']['site'] == $site['id']) selected @endif >{{ $site['name'] }}</option>
                                    @endforeach
                                @else
                                    <option value="0">No Sites Found...</option>
                                @endif
                                </select>
                            </div>
                            @if (in_array($head_data['user']['role_id'], [0,2]))
                            <div>
                                <label class="text-right orangebrown clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('site')">Add New (admin only)</label>
                            </div>
                            @endif
                        </div>
                        <div class="nav-row" id="area-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="area" name="area" disabled required>
                                    <option value="" selected disabled hidden>Select Area</option>
                                </select>
                            </div>
                            <div>
                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('area')">Add New</label>
                            </div>
                        </div>
                        <div class="nav-row" id="shelf-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="shelf" name="shelf" disabled required>
                                    <option value="" selected disabled hidden>Select Shelf</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('shelf')">Add New</label>
                            </div>
                        </div>
                        <div class="nav-row" id="container-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="container" id="container-label">Container</div>
                            <div>
                                <select class="form-control stock-inputSize" id="container" name="container" disabled>
                                    <option value="" selected disabled hidden>Select Container</option>
                                </select>
                            </div>
                        </div>
                        <div class="nav-row" id="cost-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost ({{ $head_data['config_compare']['currency'] }})</label></div>
                            <div><input type="number" step=".01" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize" value="0" value="{{ $params['request']['cost'] ?? null }}" required></input></div>
                        </div>
                    </div>
                    <hr style="border-color: gray; margin-right:15px">
                    <div class="nav-row" style="margin-bottom:25px">
                        <div class="nav-row" id="quantity-row" style="margin-top:10px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity <or class="red">*</or></label></div>
                            <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="{{ $params['request']['quantity'] ?? null }}" required></input></div>
                        </div>
                            <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                                <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                                <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['serial_number'] ?? null }}"></input></div>
                            </div>
                        <div class="nav-row" id="reason-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason <or class="red">*</or></label></div>
                            <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c stock-inputSize" value="New Stock" value="{{ htmlspecialchars($params['request']['reason'] ?? '', ENT_QUOTES, 'UTF-8') }}"></input></div>
                        </div>
                        <div class="nav-row" id="submit-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"></div>
                            <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success" /></div>
                        </div>
                        <div class="nav-row" id="submit-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"></div>
                            <div><p class="red" style="font-size:12px;margin-bottom:0px">* Required field.</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@elseif ($params['stock_id'] == 0 && $params['add_new'] == null)
    <!-- /stock/0/add -->
    <form action="" method="GET" style="margin-bottom:0px">
        <div class="container" id="stock-info-left">
            <div class="nav-row" id="search-stock-row">
                <table>
                    <tbody>
                        <tr>
                            <td style="padding-right:20px">Search for item</td>
                            <td><input class="form-control stock-inputSize" type="text" id="search" name="search" oninput="getInventory(1)" placeholder="Search for item" value="{{ htmlspecialchars($params['request']['search'] ?? '', ENT_QUOTES, 'UTF-8') }}"/></td>
                            <td class="text-right viewport-mid-large" style="padding-left:20px;padding-right:20px">or</td>
                            <td class="viewport-mid-large"><a class="link btn btn-success cw" onclick="navPage(`{{ url('stock') }}/0/add/new?name=`+document.getElementById('search').value)">Add New Stock</a></td>
                        </tr>
                        <tr class="viewport-small-only-empty">
                            <td class="text-right" style="padding-right:20px">or</td>
                            <td><a class="link btn btn-success cw" onclick="navPage(updateQueryParameter(updateQueryParameter('', 'stock_id', 0), 'name', document.getElementById('search').value))">Add New Stock</a></td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
        </div>
    </form>
    <div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">
        <input type="hidden" id="inv-action-type" name="inv-action-type" value="add" />
        <table class="table table-dark theme-table" id="inventoryTable" style="padding-bottom:0px;margin-bottom:0px">
            <thead style="text-align: center; white-space: nowrap;">
                <tr class="theme-tableOuter">
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th hidden>Descritpion</th>
                    <th class="viewport-large-empty">SKU</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody id="inv-body" class="align-middle" style="text-align: center; white-space: nowrap;">                       
            </tbody>
        </table>
        <table class="table table-dark theme-table centertable">
            <tbody>
                <tr class="theme-tableOuter">
                    <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                    <div class="row">
                        <div class="col text-center"></div>
                        <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                        </div>
                        <div class="col text-center">
                        </div>
                    </div>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <!-- /stock/#/add -->
    @if (is_array($stock_data) && $stock_data['id'] == $params['stock_id'])
    <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
        @if ($stock_data['is_cable'] == 0)
        <input type="hidden" name="stock-add" value="1" />
        @else
        <input type="hidden" name="cablestock-add" value="1" /> 
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ $stock_data['id'] }}" />
        <div class="nav-row" style="margin-bottom:10px">
            <div class="nav-row" id="heading-row" style="margin-top:10px">
                <div class="stock-inputLabelSize"></div>
                <div id="heading-heading">
                    <a href="../stock.php?stock_id='.$stock_id.'"><h2>{{ $stock_data['name'] }}</h2></a>
                    <p id="sku"><strong>SKU:</strong> <or class="blue">{{ $stock_data['sku'] }}</or></p>
                    <p id="locations" style="margin-bottom:0px"><strong>Locations:</strong><br>
                    @if (isset($stock_inv_data) && $stock_inv_data['count'] > 0)
                        <table>
                            <tbody>
                            @foreach($stock_inv_data['rows'] as $row)
                                <tr>
                                    <td>{{ $row['site_name'] }}, {{ $row['area_name'] }}, {{ $row['shelf_name'] }}</td>
                                    <td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">{{ $row['quantity'] }}</or></a></or></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        No locations linked.
                    @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="container well-nopad theme-divBg">
            <div class="row">
                <div class="text-left" id="stock-info-left" style="padding-left:15px">
                    <div class="nav-row" style="margin-bottom:25px">
                        @if ($stock_data['is_cable'] == 0)
                        <div class="nav-row" id="upc-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="upc" id="upc-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Universal Product Code for item">UPC</or></label></div>
                            <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['upc'] ?? null }}"></input></div>
                        </div>
                        <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                            <div  class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer <or class="red">*</or></label></div>
                            <div>
                                <select name="manufacturer" id="manufacturer-select" class="form-control stock-inputSize" required>
                                    <option value="" selected disabled hidden>Select Manufacturer</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('manufacturer')">Add New</label>
                            </div>
                        </div>
                        @endif
                        <div class="nav-row" id="site-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="site" name="site" required>
                                    <option value="" selected disabled hidden>Select Site</option>
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}" @if (isset($params['request']['site']) && $params['site'] == $site['id']) selected @endif >{{ $site['name'] }}</option>
                                    @endforeach
                                @else
                                    <option value="0">No Sites Found...</option>
                                @endif
                                </select>
                            </div>
                            @if (in_array($head_data['user']['role_id'], [0,2]))
                            <div>
                                <label class="text-right orangebrown clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('site')">Add New (admin only)</label>
                            </div>
                            @endif
                        </div>
                        <div class="nav-row" id="area-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="area" name="area" disabled required>
                                    <option value="" selected disabled hidden>Select Area</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('area')">Add New</label>
                            </div>
                        </div>
                        <div class="nav-row" id="shelf-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Shelf <or class="red">*</or></label></div>
                            <div>
                                <select class="form-control stock-inputSize" id="shelf" name="shelf" disabled required>
                                    <option value="" selected disabled hidden>Select Shelf</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('shelf')">Add New</label>
                            </div>
                        </div>
                        <div class="nav-row" id="container-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="container" id="container-label">Container</div>
                            <div>
                                <select class="form-control stock-inputSize" id="container" name="container" disabled>
                                    <option value="" selected disabled hidden>Select Container</option>
                                </select>
                            </div>
                        </div>
                        @if ($stock_data['is_cable'] == 0)
                        <div class="nav-row" id="cost-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost ({{ $head_data['config_compare']['currency'] }})</label></div>
                            <div><input type="number" step=".01" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize" value="0" value="'.$input_cost.'" required></input></div>
                        </div>
                        @endif
                    </div>
                    <hr style="border-color: gray; margin-right:15px">
                    <div class="nav-row" style="margin-bottom:25px">
                        <div class="nav-row" id="quantity-row" style="margin-top:10px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity <or class="red">*</or></label></div>
                            <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="{{ $params['request']['quantity'] ?? null}}" required></input></div>
                        </div>
                        @if ($stock_data['is_cable'] == 0)
                        <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                            <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize" value="{{ $params['request']['serial_number'] ?? null}}"></input></div>
                        </div>
                        @endif
                        <div class="nav-row" id="reason-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason <or class="red">*</or></label></div>
                            <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c stock-inputSize" value="New Stock" value="{{ htmlspecialchars($params['request']['reason'] ?? '', ENT_QUOTES, 'UTF-8') }}"></input></div>
                        </div>
                        <div class="nav-row" id="submit-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"></div>
                            <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success" /></div>
                        </div>
                        <div class="nav-row" id="submit-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"></div>
                            <div><p class="red" style="font-size:12px;margin-bottom:0px">* Required field.</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <p class="red">No Stock Info Found...</p>
    @endif
@endif
    @include('includes.stock.new-properties')
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/stock-add.js') }}"></script>
</div>