<form action="{{ route('stock.add.new') }}" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
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
                        <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c stock-inputSize theme-input" value="{{ htmlspecialchars($params['request']['name'] ?? '', ENT_QUOTES, 'UTF-8') }}" required></input></div>
                    </div>
                    <div class="nav-row" id="sku-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                        <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c stock-inputSize theme-input" value="{{ $params['request']['sku'] ?? null }}" pattern="^[A-Za-z0-9\p{P}]+$"></input></div>
                    </div>
                    <div class="nav-row" id="description-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                        <div><textarea class="form-control nav-v-c stock-inputSize theme-input" id="description" name="description" rows="3" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="{{ htmlspecialchars($params['request']['description'] ?? '', ENT_QUOTES, 'UTF-8') }}" ></textarea></div>
                    </div>
                    <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                        <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c stock-inputSize theme-input" value="{{ $params['request']['min_stock'] ?? null }}"></input></div>
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
                    <select class="form-control stock-inputSize theme-dropdown" id="tag-select" name="tags-init">
                        <option value="" selected disabled hidden>-- Select a tag if needed --</option>
                    </select>

                    <select id="tags" name="tags[]" multiple class="tags-special form-control stock-inputSize theme-dropdown" style="margin-top:2px;display: inline-block;height:40px"></select>
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
                    <div class="nav-row" id="site-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="site" id="site-label">Site <or class="red">*</or></label></div>
                        <div>
                            <select class="form-control stock-inputSize theme-dropdown" id="site" name="site" required>
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
                        @if ($head_data['user']['permissions']['root'] == 1 || $head_data['user']['permission']['admin == 1'])
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
                        <div><input type="number" step=".01" name="cost" placeholder="0" id="cost" class="form-control nav-v-c stock-inputSize theme-input" value="0" value="{{ $params['request']['cost'] ?? null }}" required></input></div>
                    </div>
                </div>
                <hr style="border-color: gray; margin-right:15px">
                <div class="nav-row" style="margin-bottom:25px">
                    <div class="nav-row" id="quantity-row" style="margin-top:10px">
                        <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="quantity" id="quantity-label">Quantity <or class="red">*</or></label></div>
                        <div><input type="number" name="quantity" placeholder="Quantity" id="quantity" class="form-control nav-v-c stock-inputSize theme-input" value="1" value="{{ $params['request']['quantity'] ?? null }}" required></input></div>
                    </div>
                        <div class="nav-row" id="serial-number-row" style="margin-top:25px">
                            <div class="stock-inputLabelSize"><label class="nav-v-c text-right" style="width:100%" for="serial-number" id="serial-number-label"><or style="text-decoration:underline; text-decoration-style:dotted" title="Any Serial Numbers to be tracked. These should be seperated by commas. e.g. serial1, serial2, serial3...">Serial Numbers</or></label></div>
                            <div><input type="text" name="serial-number" placeholder="Serial Numbers" id="serial-number" class="form-control nav-v-c stock-inputSize theme-input" value="{{ $params['request']['serial_number'] ?? null }}"></input></div>
                        </div>
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