<!-- Edit Stock -->
<!-- This cannot have a stock id of 0 - must be specified. -->
<div class="container well-nopad theme-divBg" style="margin-bottom:5px">
    <div class="row">
        <div class="col-sm-7 text-left" id="stock-info-left">
            <form id="edit-form" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
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
                            <select id="tags" name="tags[]" multiple class="form-control nav-trans stock-inputSize" style="border: 1px solid grey;display: inline-block; height:90px; white-space:wrap">
                            @if ($tag_data['count'] > 0)
                                @foreach($tag_data['tagged']['rows'] as $tagged) 
                                <option class="btn-stock clickable" style="margin-top:1px;border:1px solid gray" value="{{ $tagged['id'] }}" selected>{{ $tagged['name'] }} ✕</option>
                                @endforeach
                            @endif
                            </select>
                            <select class="form-control stock-inputSize" id="tags-init" name="tags-init" style="margin-top:2px">
                                <option value="" selected>-- Add Tags --</option>
                            @if ($tag_data['untagged']['count'] > 0)
                                @foreach($tag_data['tagged']['rows'] as $tagged) 
                                <option class="btn-stock clickable" style="margin-top:1px;border:1px solid gray" value="{{ $tagged['id'] }}" selected>{{ $tagged['name'] }}</option>
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
                            <button type="button" class="nav-v-c btn btn-warning" onclick="navPage(`{{ url('stock')}}/{{ $params['stock_id'] }}/edit`)" disabled>Cancel</button>
                        @else
                            <button type="button" class="nav-v-c btn btn-warning" onclick="navPage(`{{ url('stock')}}/{{ $params['stock_id'] }}/edit`)">Cancel</button>
                        @endif
                        </div>
                    </div>
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
                </div>
            </form>
        </div>

        <div class="col text-right" id="stock-info-right">
        @if (!isset($params['request']['images']) || ($params['request']['images'] !== 'edit')) 
            @if ($stock_data['img_data']['count'] > 0)
                <div class="well-nopad theme-divBg nav-right stock-imageBox">
                    <div class="nav-row stock-imageMainSolo">
                    @for ($i = 0; $i < $stock_data['img_data']['count']; $i++)
                        @if ($i == 0)
                        <div class="thumb theme-divBg-m text-center @if($i+1 === $stock_data['img_data']['count']) stock-imageMainSolo @else stock-imageMain @endif" onclick="modalLoadCarousel()">
                            <img class="nav-v-c  @if($i+1 === $stock_data['img_data']['count']) stock-imageMainSolo @else stock-imageMain @endif" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$i]['id'] }}" alt="{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$i]['id'] }}" src="{{ asset('img/stock/'.$stock_data['img_data']['rows'][$i]['image']) }}" />
                        </div>
                        <span id="side-images" style="margin-left:5px">
                        @endif

                        @if ($i == 1 || $i ==2)
                            <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$i]['id'] }}" alt="{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$i]['id'] }}" src="{{ asset('img/stock/'.$stock_data['img_data']['rows'][$i]['image']) }}"/>
                            </div>
                        @endif

                        @if ($i == 3)
                            <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                            @if ($i < $stock_data['img_data']['count']-1)
                                <p class="nav-v-c text-center stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-more">+{{( $stock_data['img_data']['count']-3) }}</p>
                            @else
                                <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$i]['id'] }}" src="{{ asset('img/stock/'.$stock_data['img_data']['rows'][$i]['image']) }}" onclick="modalLoad(this)"/>
                            @endif
                            </div>
                        @endif

                        @if ($i == $stock_data['img_data']['count']-1)
                        <span>
                        @endif
                    @endfor
                    </div>
                </div>
                <div id="edit-images-div" class="nav-right text-center stock-imageMainSolo" style="margin-right:20px; height:max-content !important">
                    <a id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter('', 'images', 'edit'))">
                        <i class="fa fa-pencil"></i> Edit images
                    </a>
                </div>
                @if ($stock_data['img_data']['count'] == 1)
                    <!-- Modal Image Div -->
                    <div id="modalDivCarousel" class="modal" onclick="modalCloseCarousel()">
                        <span class="close" onclick="modalCloseCarousel()">&times;</span>
                        @for ($b = 0; $b < $stock_data['img_data']['count']; $b++)
                        <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$b]['id'] }}" src="{{ asset('img/stock/'.$stock_data['img_data']['rows'][$b]['image']) }}"/>
                        @endfor
                        <img class="modal-content bg-trans" id="modalImg">
                        <div id="caption" class="modal-caption"></div>
                    </div>
                    <!-- End of Modal Image Div -->
                @else
                    <link rel="stylesheet" href="./assets/css/carousel.css">
                    <script src="assets/js/carousel.js"></script>
                    <!-- Modal Image Div -->
                    <div id="modalDivCarousel" class="modal">
                        <span class="close" onclick="modalCloseCarousel()">&times;</span>
                        <img class="modal-content bg-trans" id="modalImg">
                            <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center" style="margin-left:10vw; margin-right:10vw">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                @for ($a = 0; $a < $stock_data['img_data']['count']; $a++)
                                    @if ($a == 0)
                                    <li data-target="#myCarousel" data-slide-to="{{ $a }}" class="active"></li>
                                    @else
                                    <li data-target="#myCarousel" data-slide-to="{{ $a }}"></li>
                                    @endif
                                @endfor
                                </ol>

                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" align="centre">
                                @for ($b = 0; $b < $stock_data['img_data']['count']; $b++)
                                    @if ($b == 0) 
                                    <div class="item active" align="centre">
                                    @else
                                    <div class="item" align="centre">
                                    @endif
                                        <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data']['rows'][$b]['id'] }}" src="{{ asset('img/stock/'.$stock_data['img_data']['rows'][$b]['image']) }}"/>
                                        <div class="carousel-caption">
                                            <h3></h3>
                                            <p></p>
                                        </div>
                                    </div>
                                @endfor

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
                    <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter(`{{ url('stock')/{{ $stock_data['id'] }}/edit`, 'images', 'edit'))">
                        <i class="fa fa-plus"></i> Add images
                    </button>
                </div> 
            @endif
        @else
            @if ($stock_data['img_data']['count'] > 0) 
            <table style="width:100%">
                <tbody>
                @for ($i = 0; $i < $stock_data['img_data']['count']; $i++;)
                    <tr>
                        <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to unlink this image?\nThe file will remain on the system.');">
                            <!-- Include CSRF token in the form -->
                            @csrf
                            <input type="hidden" name="stock-edit" value="1" />
                            <td class="theme-divBg-m" style="padding-right:5px">
                                <input type="hidden" name="stock_id" value="{{ $stock_data['id'] }}" />
                                <input type="hidden" name="img_id" value="{{ $stock_data['img_data'][$i]['id'] }}" />
                                <input type="hidden" name="submit" value="image-delete" />
                                <div class="thumb theme-divBg-m text-center" style="width:75px;height:75px;margin:5px" onclick="modalLoad(this.children[0])">
                                    <img class="nav-v-c" id="stock-{{ $stock_data['id'] }}-img-{{ $stock_data['img_data'][$i]['id'] }}" style="max-height:80px; max-width:75px" alt="{{ $stock_data['name'] }} - image {{ $i+1 }}" src="{{ asset('img/stock/'.$stock_data['img_data'][$i]['image']) }}"/>
                                
                                </div>
                            </td>
                            <td class="theme-divBg-m uni" style="font-size:14px">img/stock/{{ $stock_data['img_data'][$i]['image'] }}</td>
                            <td class="theme-divBg-m" style="padding-left:10px;padding-right:10px">
                                <button id="edit-images" class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </form>
                    </tr>
                @endfor
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
<div id="modalDivSelection" class="modal">
<!-- <div id="modalDivSelection" style="display: block;"> -->
    <span class="close" onclick="modalCloseSelection()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div  class="well-nopad theme-divBg" style="overflow-y:auto; height:450px">
            <div class="nav-row">
            @if (count($img_files) > 0)
                @for ($f=0; $f < count($img_files); $f++;)
                <div class="thumb theme-divBg-m" id="add-image-'.$f.'-div" style="width:200px;height:200px;margin:2px">
                    <img class="nav-v-c" id="add-image-{{ $f }}" style="width:200px" alt="{{ $img_files[$f] }}" src="{{ asset('/img/stock/'.$img_files[$f]) }}" onclick="modalImageInputFill(this);"/>
                </div>
                @endfor
            @else
                No Files
            @endif
            </div>
        </div>
        <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
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

<!-- Modal Image Upload Div -->
<div id="modalDivUpload" class="modal">
    <span class="close" onclick="modalCloseUpload()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div style="width:250px;height:250px;margin:auto">
            <img class="nav-v-c" id="upload-img-pre" style="max-width:250px;max-height:250px" />
        </div>
        <div style="margin:auto;text-align:center;margin-top:10px">
            <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
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