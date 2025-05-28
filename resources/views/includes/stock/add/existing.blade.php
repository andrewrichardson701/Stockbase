@if (is_array($stock_data) && $stock_data['id'] == $params['stock_id'])
<form action="{{ route('stock.add.existing') }}" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
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
                <a href="{{ url('stock/'.$stock_data['id']) }}"><h2>{{ $stock_data['name'] }}</h2></a>
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
                        <div><input type="text" name="upc" placeholder="UPC - if available" id="upc" class="form-control nav-v-c stock-inputSize theme-input" value="{{ $params['request']['upc'] ?? null }}"></input></div>
                    </div>
                    <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                        <div  class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer <or class="red">*</or></label></div>
                        <div>
                            <select name="manufacturer" id="manufacturer-select" class="form-control stock-inputSize theme-dropdown" required>
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
                            <select class="form-control stock-inputSize theme-dropdown" id="site" name="site" required>
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
                        @if (in_array($head_data['user']['role_id'], [1,3]))
                        <div>
                            <label class="text-right orangebrown clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('site')">Add New (admin only)</label>
                        </div>
                        @endif
                    </div>
                    <div class="nav-row" id="area-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="area" id="area-label">Area <or class="red">*</or></label></div>
                        <div>
                            <select class="form-control stock-inputSize theme-dropdown" id="area" name="area" disabled required>
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
                            <select class="form-control stock-inputSize theme-dropdown" id="shelf" name="shelf" disabled required>
                                <option value="" selected disabled hidden>Select Shelf</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('shelf')">Add New</label>
                        </div>
                    </div>
                    @if ($stock_data['is_cable'] == 0)
                    <div class="nav-row" id="container-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="container" id="container-label">Container</div>
                        <div>
                            <select class="form-control stock-inputSize theme-dropdown" id="container" name="container" disabled>
                                <option value="" selected disabled hidden>Select Container</option>
                            </select>
                        </div>
                    </div>
                    <div class="nav-row" id="cost-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="cost" id="cost-label">Item Cost ({{ $head_data['config_compare']['currency'] }})</label></div>
                        <div><input type="number" step=".01" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize theme-input" value="0" value="'.$input_cost.'" required></input></div>
                    </div>
                    @endif
                </div>
                <hr style="border-color: gray; margin-right:15px">
                <div class="nav-row" style="margin-bottom:25px">
                    <div class="nav-row" id="quantity-row" style="margin-top:10px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity <or class="red">*</or></label></div>
                        <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize theme-input" value="1" value="{{ $params['request']['quantity'] ?? null}}" required></input></div>
                    </div>
                    @if ($stock_data['is_cable'] == 0)
                    <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                        <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize theme-input" value="{{ $params['request']['serial_number'] ?? null}}"></input></div>
                    </div>
                    @endif
                    <div class="nav-row" id="reason-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason <or class="red">*</or></label></div>
                        <div><input type="text" name="reason" placeholder="New Stock" id="reason" class="form-control nav-v-c stock-inputSize theme-input" value="New Stock" value="{{ htmlspecialchars($params['request']['reason'] ?? '', ENT_QUOTES, 'UTF-8') }}"></input></div>
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