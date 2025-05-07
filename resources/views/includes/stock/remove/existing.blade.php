<form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
    <!-- Include CSRF token in the form -->
    @csrf
    @if ($stock_data['is_cable'] == 0) 
        <input type="hidden" name="stock-remove" value="1" />
    @else 
        <input type="hidden" name="cablestock-remove" value="1" />
    @endif
    <div class="nav-row" style="margin-bottom:10px">
        <div class="nav-row" id="heading-row" style="margin-top:10px">
            <div class="stock-inputLabelSize"></div>
            <div id="heading-heading">
                <a href="{{ url('stock/'.$stock_data['id']) }}"><h2 id="stock_name">{{ $stock_data['name'] }}</h2></a>
                <p id="sku"><strong>SKU:</strong> <or class="blue">{{ $stock_data['sku'] }}</or></p>
                <p id="locations" style="margin-bottom:0px"><strong>Locations:</strong><br>
                @if ($stock_inv_data['count'] > 0)
                <table>
                    <tbody>
                    @foreach ($stock_inv_data['rows'] as $row)
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
    @if ($stock_item_data['count'] == 0) 
        <div class="container red text-center" style="padding-bottom:10px" id="no-stock-found">
            <div class="row">
                <div class="stock-inputLabelSize"></div>
                <div>No Stock Found</div>
            </div>
        </div>
    @endif
    <div class="container well-nopad theme-divBg">
        <div class="row">
            <div class="text-left" id="stock-info-left" style="padding-left:15px">
                <div class="nav-row" style="margin-bottom:25px">
                    <input type="hidden" id="stock-id" value="{{ $stock_data['id'] }}" name="stock_id" />
                    <input type="hidden" value="{{ $stock_data['sku'] }}" name="stock_sku" />
                @if ($stock_data['is_cable'] == 0)
                    <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                        <div>
                            <select name="manufacturer" id="manufacturer" class="form-control stock-inputSize" onchange="populateRemoveShelves(this)" required @if($stock_item_data['count'] == 0) disabled @endif>
                                <option value="" selected disabled hidden>Select Manufacturer</option>
                                @foreach ( $stock_inv_data['manufacturers'] as $manufacturer) 
                                    <option value={{ $manufacturer['id'] }}>{{ $manufacturer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="nav-row" id="shelf-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Location</label></div>
                        <div>
                            <select class="form-control stock-inputSize" id="shelf" name="shelf" required onchange="populateContainers(this)" disabled>
                                <option value="" selected disabled hidden>Select Location</option>
                            </select>
                        </div>
                    </div>
                    <div class="nav-row" id="container-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="container" id="container-label">Container</label></div>
                        <div>
                            <select class="form-control stock-inputSize" id="container" name="container" required onchange="populateSerials(this)" disabled>
                                <option value="" selected disabled hidden>Select Container</option>
                            </select>
                        </div>
                    </div>
                @else
                    <div class="nav-row" id="shelf-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize">
                            <label class="nav-v-c text-right" style="width:100%" for="shelf" id="shelf-label">Location</label>
                        </div>
                        <div>
                            <select class="form-control stock-inputSize" id="shelf" name="shelf" required onchange="getQuantityCable()" required @if($stock_item_data['count'] == 0) disabled @endif>
                                <option value="" selected disabled hidden>Select Location</option>
                                @foreach ( $stock_inv_data['rows'] as $location) 
                                    <option value={{ $location['shelf_id'] }}>{{ $location['site_name'] }}, {{ $location['area_name'] }}, {{ $location['shelf_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                    <div class="nav-row" id="price-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="price" id="price-label">Sale Price ({{ $head_data['config_compare']['currency'] }})</label></div>
                            <div>
                                <input type="number" step=".01" name="price" placeholder="0" id="price" class="form-control nav-v-c stock-inputSize" value="0" value="{{ $params['request']['cost'] ?? null }}" required @if($stock_item_data['count'] == 0) disabled @endif></input>
                            </div>
                        </div>
                    </div>
                    <hr style="border-color: gray; margin-right:15px">
                    <div class="nav-row" style="margin-bottom:0px">
                        <div class="nav-row" id="date-row" style="margin-top:10px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="transaction_date" id="date-label">Transaction Date</label></div>
                            <div><input type="date" value="{{ date('Y-m-d') }}" name="transaction_date" id="transaction_date" class="form-control" style="width:150px" required @if($stock_item_data['count'] == 0) disabled @endif/></div>
                        </div>
                    @if ($stock_data['is_cable'] == 0) 
                        <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Number to be tracked.">Serial Numbers</or></label></div>
                            <div>
                                <select name="serial-number" id="serial-number" class="form-control stock-inputSize" value="{{ $params['request']['serial_number'] ?? null }}" disabled onchange="getQuantity()">
                                    <option value="" selected disabled hidden>Serial...</option>
                                </select>
                            </div>
                        </div>
                    @endif
                        <div class="nav-row" id="quantity-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize">
                                <label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity</label>
                            </div>
                            <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize" value="1" value="{{ $params['request']['quantity'] ?? 1 }}" min="1" required @if($stock_item_data['count'] == 0) disabled @endif ></input></div>
                        </div>
                        <div class="nav-row" id="reason-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="reason" id="reason-label">Reason</label></div>
                            <div><input type="text" name="reason" placeholder="Customer sale, ID: XXXXXX" id="reason" class="form-control nav-v-c stock-inputSize" value="{{ htmlspecialchars($params['request']['reason'] ?? '', ENT_QUOTES, 'UTF-8')  }}" required @if($stock_item_data['count'] == 0) disabled @endif ></input></div>
                        </div>
                        <div class="nav-row" id="reason-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"></div>
                            <div>
                            @if ($stock_item_data['count'] > 0)
                                <input type="submit" id="removeButton" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" />
                                <button type="button" id="removeContButton" name="submit" value="Remove Stock" class="nav-v-c btn btn-danger"onclick="modalLoadContainerRemoveConfirmation()"  hidden disabled>Remove Stock</button>
                            @else
                                <input type="submit" value="Remove Stock" name="submit" class="nav-v-c btn btn-danger" disabled />');
                                <a href="#" onclick="confirmAction('{{ addslashes(htmlspecialchars($stock_data['name'])) }} ', '{{ $stock_data['sku'] }}', 'includes/stock-modify.inc.php?stock_id={{ $stock_data['id'] }}&type=delete')" class="nav-v-c btn btn-danger cw" style="margin-left:300px"><strong><u>Delete Stock</u></strong></a>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>