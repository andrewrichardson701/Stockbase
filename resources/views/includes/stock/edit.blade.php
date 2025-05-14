<!-- Edit Stock -->
<!-- This cannot have a stock id of 0 - must be specified. -->
<div class="container well-nopad theme-divBg" style="margin-bottom:5px">
    <div class="row">
        <div class="col-sm-7 text-left" id="stock-info-left">
            <form id="edit-form" action="{{ route('stock.edit') }}" method="POST" enctype="multipart/form-data">
                <!-- below input is used for the stock-modify.inc.php page -->
                <!-- Include CSRF token in the form -->
                @csrf
                <input type="hidden" id="inv-action-type" name="inv-action-type" value="edit" />
                <input type="hidden" name="stock-edit" value="1" />
                <div class="nav-row" style="margin-bottom:25px">
                    <div class="nav-row" id="id-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="id" id="id-label">ID</label></div>
                        <div><input type="text" name="id-visible" placeholder="X" id="id-visible" class="form-control nav-v-c stock-inputSize" style="color:black;background-color:#adadad !important" value="{{ $stock_data['id'] }}" disabled></input></div>
                        <input type="hidden" name="id" id="id" value="{{ $stock_data['id'] }}" />
                    </div>
                    <div class="nav-row" id="name-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="name" id="name-label">Name</label></div>
                        <div><input type="text" name="name" placeholder="Name" id="name" class="form-control nav-v-c stock-inputSize" value="{{ htmlspecialchars($stock_data['name'], ENT_QUOTES, 'UTF-8') ?? '' }}" required></input></div>
                    </div>
                    <div class="nav-row" id="sku-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="sku" id="sku-label">SKU</label></div>
                        <div><input type="text" name="sku" placeholder="Auto generated if blank" id="sku" class="form-control nav-v-c stock-inputSize" value="{{ $stock_data['sku'] }}" pattern="^[A-Za-z0-9\p{P}]+$"></div>
                    </div>
                    <div class="nav-row" id="description-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="text-right" style="padding-top:5px;width:100%" for="description" id="description-label">Description</label></div>
                        <div><textarea class="form-control nav-v-c stock-inputSize" id="description" name="description" rows="3" style="resize: both; overflow: auto; word-wrap: break-word;" placeholder="Stock description/summary" value="{{ htmlspecialchars($stock_data['description'], ENT_QUOTES, 'UTF-8') }}" >{{ str_replace(array("\r\n","\\r\\n"), "&#010;", $stock_data['description']) }}</textarea></div>
                    </div>
                    @if ($stock_data['is_cable'] == 0)
                    <div class="nav-row" id="tags-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px">
                            <label class="text-right" style="padding-top:5px;width:100%" for="tags" id="tags-label">Tags</label>
                        </div>
                        <div id="tags-group">
                            <input id="tags-selected" name="tags-selected" type="hidden" />
                            <select id="tags" name="tags[]" multiple class="tags-special form-control nav-trans stock-inputSize">
                            @if ($tag_data['tagged']['count'] > 0)
                                @foreach($tag_data['tagged']['rows'] as $tagged) 
                                <option class="btn-stock clickable" style="margin-top:1px;border:1px solid gray" value="{{ $tagged['id'] }}" selected>{{ $tagged['name'] }} ✕</option>
                                @endforeach
                            @endif
                            </select>
                            <select class="form-control stock-inputSize" id="tags-init" name="tags-init" style="margin-top:2px">
                                <option value="" selected>-- Add Tags --</option>
                            @if ($tag_data['untagged']['count'] > 0)
                                @foreach($tag_data['untagged']['rows'] as $untagged) 
                                <option class="btn-stock clickable" style="margin-top:1px;border:1px solid gray" value="{{ $untagged['id'] }}" selected>{{ $untagged['name'] }}</option>
                                @endforeach
                            @else
                                <option value="0" selected disabled>No Tags Remaining</option>
                            @endif
                            </select>
                        </div>
                        <div class="viewport-large">
                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('tag')">Add New</label>
                        </div>
                    </div>
                    <div class="nav-row viewport-small" id="tags-row-add">
                        <div class="stock-inputLabelSize" style="max-width:200px">
                        </div>
                        <div style="width:max-content">
                            <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px" onclick="modalLoadProperties('tag')">Add New</label>
                        </div>
                    </div>
                    @endif
                    <div class="nav-row" id="min-stock-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"><label class="nav-v-c text-right" style="width:100%" for="min-stock" id="min-stock-label">Minimum Stock Count</label></div>
                        <div><input type="number" name="min-stock" placeholder="Default = 0" id="min-stock" class="form-control nav-v-c stock-inputSize" value="{{ $stock_data['min_stock'] }}"></input></div>
                    </div>
                    <div class="nav-row" id="submit-row" style="margin-top:25px">
                        <div class="stock-inputLabelSize" style="max-width:200px"></div>
                        <div class="stock-inputSize"><input id="form-submit" type="submit" value="Save" name="submit" class="nav-v-c btn btn-success" /></div>
                        <div>
                        @if (isset($params['request']['images']) && ($params['request']['images'] == 'edit')) 
                            <button type="button" class="nav-v-c btn btn-warning" onclick="navPage(`{{ url('stock')}}/{{ $stock_data['id'] }}/edit`)" disabled>Cancel</button>
                        @else
                            <button type="button" class="nav-v-c btn btn-warning" onclick="navPage(`{{ url('stock')}}/{{ $stock_data['id'] }}`)">Cancel</button>
                        @endif
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>

        <div class="col text-right" id="stock-info-right">
        @if (!isset($params['request']['images']) || ($params['request']['images'] !== 'edit')) 
            @if ($stock_data['img_data']['count'] > 0)
                <div class="well-nopad theme-divBg nav-right stock-imageBox">
                    <div class="nav-row stock-imageMainSolo">
                    @foreach($stock_data['img_data']['rows'] as $img_data)
                        @if ($loop->first)
                        <div class="thumb theme-divBg-m text-center @if($loop->iteration == $stock_data['img_data']['count']) stock-imageMainSolo @else stock-imageMain @endif" onclick="modalLoad(this.children[0])">
                            <img class="nav-v-c  @if($loop->iteration == $stock_data['img_data']['count']) stock-imageMainSolo @else stock-imageMain @endif" id="stock-{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" alt="{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" src="{{ asset('img/stock/'.$img_data['image']) }}" />
                        </div>
                        <span id="side-images" style="margin-left:5px">
                        @endif

                        @if ($loop->iteration == 2 || $loop->iteration == 3)
                            <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoad(this.children[0])">
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" alt="{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" src="{{ asset('img/stock/'.$img_data['image']) }}"/>
                            </div>
                        @endif

                        @if ($loop->iteration == 4)
                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoad(this.children[0])">
                            @if ($loop->iteration < $stock_data['img_data']['count'])
                                <p class="nav-v-c text-center stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-more">+{{( $stock_data['img_data']['count']-3) }}</p>
                            @else
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" src="{{ asset('img/stock/'.$img_data['image']) }}" onclick="modalLoad(this)"/>
                            @endif
                            </div>
                        @endif

                        @if ($loop->last)
                        </span>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div id="edit-images-div" class="nav-right text-center stock-imageMainSolo" style="height:max-content !important">
                    <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter('', 'images', 'edit'))">
                        <i class="fa fa-pencil"></i> Edit images
                    </a>
                </div>
                
                
            @else
                <div id="edit-images-div" class="nav-div-mid nav-v-c">
                    <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter(`{{ url('stock/'.$stock_data['id'].'/edit') }}`, 'images', 'edit'))">
                        <i class="fa fa-plus"></i> Add images
                    </button>
                </div> 
            @endif
        @else
            @if ($stock_data['img_data']['count'] > 0) 
            <table style="width:100%">
                <tbody>
                @foreach ($stock_data['img_data']['rows'] as $img_data)
                    <tr>
                        <form action="{{ route('stock.edit.imageunlink') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to unlink this image?\nThe file will remain on the system.');">
                            <!-- Include CSRF token in the form -->
                            @csrf
                            <input type="hidden" name="stock-edit" value="1" />
                            <td class="theme-divBg-m" style="padding-right:5px">
                                <input type="hidden" name="stock_id" value="{{ $stock_data['id'] }}" />
                                <input type="hidden" name="img_id" value="{{ $img_data['id'] }}" />
                                <input type="hidden" name="submit" value="image-delete" />
                                <div class="thumb theme-divBg-m text-center" style="width:75px;height:75px;margin:5px" onclick="modalLoad(this.children[0])">
                                    <img class="nav-v-c" id="stock-{{ $stock_data['id'] }}-img-{{ $img_data['id'] }}" style="max-height:80px; max-width:75px" alt="{{ $stock_data['name'] }} - image {{ $loop->iteration }}" src="{{ asset('img/stock/'.$img_data['image']) }}"/>
                                
                                </div>
                            </td>
                            <td class="theme-divBg-m uni" style="font-size:14px">img/stock/{{ $img_data['image'] }}</td>
                            <td class="theme-divBg-m" style="padding-left:10px;padding-right:10px">
                                <button id="edit-images" class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </form>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
            <div id="edit-images-div" class="nav-div-mid" style="margin-top:10px">
                <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="modalLoadSelection()">
                    <i class="fa fa-plus"></i> Add existing image
                </a>
                <a id="edit-images" class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="modalLoadUpload()">
                    <i class="fa fa-plus"></i> Add new image
                </a><br>
                <button type="button" style="margin-top:15px" class="nav-v-b btn btn-warning" onclick="navPage(updateQueryParameter('', 'images', ''))">Cancel</button>
            </div>
        @endif
        </div>
    </div>
</div>
<!-- Modal Image Selection Div -->
@if (isset($params['request']['images']) && ($params['request']['images'] == 'edit')) 
<div id="modalDivSelection" class="modal">
<!-- <div id="modalDivSelection" style="display: block;"> -->
    <span class="close" onclick="modalCloseSelection()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div  class="well-nopad theme-divBg" style="overflow-y:auto; height:450px">
            <div class="nav-row">
            @if (count($img_files) > 0)
                @foreach ($img_files as $file)
                <div class="thumb theme-divBg-m text-center" id="add-image-{{ $loop->index }}-div" style="width:200px;margin:2px">
                    <img class="nav-v-c" id="add-image-{{ $loop->index }}" style="max-width:200px; max-height:200px; margin: auto" alt="{{ $file }}" src="{{ asset('/img/stock/'.$file) }}" onclick="modalImageInputFill(this);"/>
                </div>
                @endforeach
            @else
                No Files
            @endif
            </div>
        </div>
        <form action="{{ route('stock.edit.imagelink') }}" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            @csrf
            <input type="hidden" name="stock-edit" value="1" />
            <div class="nav-row well-nopad theme-divBg">
                <div class="nav-row" style="padding:25px 50px 25px 50px;width:750px">
                    <div>
                        <input class="nav-v-c form-control" style="height:35px;width:500px;background-color:#adadad !important; color:black !important" name="img-file-name-visible" id="img-file-name-visible" type="text" placeholder="path/to/file.png" disabled />
                        <input type="hidden" id="img-file-name" name="img-file-name"/>
                        <input type="hidden" id="stock_id" name="stock_id" value="{{ $stock_data['id'] ?? '' }}" />
                    </div>
                    <div style="padding-left:25px">
                        <input class="btn btn-success" type="submit" name="submit" value="Add Image" />
                    </div>
                </div>
                <div class="thumb theme-divBg-m" style="width:85px;height:85px;margin:2px">
                    <img class="nav-v-c" id="img-selected-thumb" style="width:85px" />
                </div>
                <div style="padding-left:100px" class="">
                    <button class="btn btn-warning nav-v-c" type="button" onclick="modalCloseSelection()">Cancel</button>
                </div>
            </div>
        </form>
    </div> 
</div>
<!-- End of Modal Image Selection Div -->
@endif
<!-- Modal Image Upload Div -->
<div id="modalDivUpload" class="modal">
    <span class="close" onclick="modalCloseUpload()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div style="width:250px;height:250px;margin:auto">
            <img class="nav-v-c" id="upload-img-pre" style="max-width:250px;max-height:250px" />
        </div>
        <div style="margin:auto;text-align:center;margin-top:10px">
            <form action="{{ route('stock.edit.imageupload') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <input type="hidden" name="stock-edit" value="1" />
                <input type="file" accept="image/*" style="margin:auto;text-align:center" id="image" name="image" onchange="loadImage(event)"><br><br>
                <input type="hidden" id="upload_stock_id" name="stock_id" value="{{ $stock_data['id'] ?? '' }}" />
                <input type="submit" name="submit" class="btn btn-success" value="Upload" style="margin-right:25px"/><button class="btn btn-warning" type="button"  onclick="modalCloseUpload()">Cancel</button>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Image Upload Div -->
<!-- Modal Image Div -->
<div id="modalDiv" class="modal" onclick="modalClose()">
    <span class="close" onclick="modalClose()">&times;</span>
    <img class="modal-content bg-trans" id="modalImg">
    <div id="caption" class="modal-caption"></div>
</div>
<!-- End of Modal Image Div -->
@include('includes.stock.new-properties')
<!-- Add the JS for the file -->
<script src="{{ asset('js/stock-edit.js') }}"></script>