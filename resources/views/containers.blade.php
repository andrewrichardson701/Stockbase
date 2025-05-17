<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Containers</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl  leading-tight headerfix">
                    Containers
                </h2>
            </div>
        </header>
        {!! $response_handling !!}
        <div id='container_data' hidden>@dump($container_data)</div>
        

    @if (!empty($container_data))
        <div style="padding-bottom:75px">
            <table class="table table-dark theme-table centertable" style="max-width:max-content;margin-bottom:0px;">
                <thead style="text-align: center; white-space: nowrap;">
                    <tr class="theme-tableOuter align-middle text-center">
                        <th></th>
                        <th class="align-middle">ID</th>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Description</th>
                        <th class="align-middle">Location</th>
                        <th class="align-middle">Objects</th>
                        <th class="align-middle">Item?</th>
                        <th class="align-middle">Edit</th>
                        <th class="align-middle"><!--Delete Col--></th>
                        <th colspan=2 class="align-middle"><button type="button" style="padding: 3px 6px 3px 6px" class="btn btn-success" onclick="modalLoadAddContainer()">+ Add New</button></th>
                    </tr>
                </thead>
                <tbody>
        @foreach ($container_data['container'] as $container)
                    <tr id="container-{{ $container['id'] }}">
                        <td class="text-center align-middle">@if($container['img_id'] !== '' && $container['img_id'] !== null) <img id="image-{{ $container['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="img/stock/{{ $container['img_image'] }}">@endif</td>
                        <td class="text-center align-middle">{{ $container['id'] }}</td>
                        <td id="container-{{ $container['id'] }}-name" class="text-center align-middle" style="width:300px">{{$container['name']}}</td>
                        <td class="text-center align-middle">{{ $container['description'] }}</td>
                        <td class="text-center align-middle">{{ $container['location'] }}</td>
                        <td class="text-center align-middle @if ($container['count'] < 1) red @endif">{{ $container['count'] }}</td>
                        <td class="text-center align-middle red">No</td>
                        <td class="text-center align-middle"><button class="btn btn-info" name="submit" title="Edit" onclick="toggleEditcontainer('{{ $container['id'] }}')"><i class="fa fa-pencil"></i></button></td>
                        <td class="text-center align-middle" style="padding-left:0px;padding-right:0px">
                        @if ($container['count'] == 0) 
                            <form action="containers.deleteContainer" method="POST" id="form-container-{{$container['id']}}-delete" enctype="multipart/form-data" hidden>
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" name="container_id" form="form-container-{{$container['id']}}-delete" value="{{$container['id']}}" />
                            </form>
                            <button class="btn btn-danger" type="submit" form="form-container-{{$container['id']}}-delete" title="Delete Container" name="delete-submit"><i class="fa fa-trash"></i></button>
                        @endif
                        </td>
                        <th class="text-center align-middle @if (!empty($container['object']) && count($container['object']) > 0) clickable @endif" style="width:50px" id="container-{{$container['id']}}-toggle" @if (!empty($container['object']) && count($container['object']) > 0) onclick="toggleHiddencontainer('{{$container['id']}}')">+@else ><button style="padding: 0px 3px 0px 3px; color:black" class="btn btn-success" onclick="modalLoadAddChildren('{{$container['id']}}', 1)">+ <i class="fa fa-link" style="color:black"></i></button> @endif </th>
                    </tr>
                    <tr id="container-{{ $container['id'] }}-edit" hidden>
                        <form action="containers.editContainer" method="POST" id="form-container-{{ $container['id'] }}-edit" enctype="multipart/form-data">
                            <!-- Include CSRF token in the form -->
                            @csrf
                            <input type="hidden" name="container_edit_submit" form="form-container-{{ $container['id'] }}-edit" value="1" />
                            <input type="hidden" name="container_id" form="form-container-{{ $container['id'] }}-edit" value="{{ $container['id'] }}" />
                            <td class="text-center align-middle">@if($container['img_id'] !== '' && $container['img_id'] !== null) <img id="image-{{ $container['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="img/stock/{{ $container['img_image'] }}">@endif</td>
                            <td class="text-center align-middle">{{ $container['id'] }}</td>
                            <td class="text-center align-middle" style="width:300px"><input type="text" class="form-control text-center" style="max-width:100%" name="container_name" form="form-container-{{ $container['id'] }}-edit" value="{{ htmlspecialchars($container['name'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle"><input type="text" class="form-control text-center" style="max-width:100%" name="container_description" form="form-container-{{ $container['id'] }}-edit" value="{{ htmlspecialchars($container['description'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle">{{$container['location']}}</th>
                            <td class="text-center align-middle @if ($container['count'] < 1) red @endif">{{$container['count']}}</td>
                            <td class="text-center align-middle red">No</td>
                            <td class="text-center align-middle" style=""><span style="white-space: nowrap"><button class="btn btn-success" type="submit" form="form-container-{{$container['id']}}-edit" title="Save" style="margin-right:10px"><i class="fa fa-save"></i></button><button type="button" class="btn btn-warning" name="submit" style="padding:3px 12px 3px 12px" onclick="toggleEditcontainer('{{$container['id']}}')">Cancel</button></span></td>
                            <td></td>
                            <th class="text-center align-middle @if (!empty($container['object']) && count($container['object']) > 0) clickable @endif" style="width:50px" id="container-'{{$container['id']}}'-toggle" @if (!empty($container['object']) && count($container['object']) > 0) onclick="toggleHiddencontainer('{{$container['id']}}')">+ @else ><button style="padding: 0px 3px 0px 3px; color:black" class="btn btn-success" onclick="modalLoadAddChildren('{{$container['id']}}', 1)">+ <i class="fa fa-link" style="color:black"></i></button>@endif</th>
                        </form>
                    </tr>
                @if (!empty($container['object']) && count($container['object']) > 0)
                    <tr id="container-{{$container['id']}}-objects" hidden>
                        <td colspan=100% style="position: relative;">
                            <div style="margin: 5px 20px 10px 20px;">
                                <table class="table table-dark theme-table centertable" style="margin:0px;max-width:100%;border: 1px solid #454d55;">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr class="theme-tableOuter">
                                            <th></th>
                                            <th>Item ID</th>
                                            <th>Stock ID</th>
                                            <th>Stock Name</th>
                                            <th style="width:85px"></th>
                                            <button type="button" style="padding: 3px 6px 3px 6px; position: absolute; top: 26px; right: 40px;" class="btn btn-success" onclick="modalLoadAddChildren({{$container['id']}}, 0)">+ Add More</button>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($container['object'] as $stock)
                                        <tr id="container-{{$container['id']}}-stock-{{$stock['id']}}">
                                            <td class="text-center align-middle">@if ($stock['img_id'] !== null)<img id="image-{{ $stock['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="/img/stock/{{$stock['img_image']}}">@endif</td>
                                            <td class="text-center align-middle">{{$stock['item_id']}}</td>
                                            <td class="text-center align-middle">{{$stock['id']}}</td>
                                            <td class="text-center align-middle link" ><a href="stock/{{ $stock['id'] }}" id="container-{{$container['id']}}-item-{{$stock['item_id']}}-name">{{$stock['name']}}</a></td>
                                            <td class="text-center align-middle"  style="width:85px">
                                                <form>
                                                    <button class="btn btn-danger" type="button" name="submit" onclick="modalLoadUnlinkContainer({{$container['id']}}, {{$stock['item_id']}}, 0)" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                        <i class="fa fa-unlink"></i>
                                                    </button>
                                                </form>
                                            </td> 
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endif
        @endforeach
        @foreach ($container_data['itemcontainer'] as $container)
                    <tr id="container-{{ $container['id'] }}">
                        <td class="text-center align-middle">@if($container['img_id'] !== '' && $container['img_id'] !== null) <img id="image-{{ $container['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="img/stock/{{ $container['img_image'] }}">@endif</td>
                        <td class="text-center align-middle">{{ $container['id'] }}</td>
                        <td id="container-{{ $container['id'] }}-name" class="text-center align-middle" style="width:300px">{{$container['name']}}</td>
                        <td class="text-center align-middle">{{ $container['description'] }}</td>
                        <td class="text-center align-middle">{{ $container['location'] }}</td>
                        <td class="text-center align-middle @if ($container['count'] < 1) red @endif">{{ $container['count'] }}</td>
                        <td class="text-center align-middle green">Yes</td>
                        <td class="text-center align-middle"><button class="btn btn-info" name="submit" title="Edit" onclick="toggleEditcontainer('{{ $container['id'] }}')"><i class="fa fa-pencil"></i></button></td>
                        <td class="text-center align-middle" style="padding-left:0px;padding-right:0px">
                        @if ($container['count'] == 0) 
                            <form action="includes/containers.inc.php" method="POST" id="form-container-{{$container['id']}}-delete" enctype="multipart/form-data" hidden>
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" name="container_delete_submit" form="form-container-{{$container['id']}}-delete" value="1" />
                                <input type="hidden" name="container_id" form="form-container-{{$container['id']}}-delete" value="{{$container['id']}}" />
                            </form>
                            <button class="btn btn-danger" type="submit" form="form-container-{{$container['id']}}-delete" title="Delete Container" name="delete-submit"><i class="fa fa-trash"></i></button>
                        @endif
                        </td>
                        <th class="text-center align-middle @if (!empty($container['object']) && count($container['object']) > 0) clickable @endif" style="width:50px" id="container-{{$container['id']}}-toggle" @if (!empty($container['object']) && count($container['object']) > 0) onclick="toggleHiddencontainer('{{$container['id']}}')">+@else ><button style="padding: 0px 3px 0px 3px; color:black" class="btn btn-success" onclick="modalLoadAddChildren('{{$container['id']}}', 1)">+ <i class="fa fa-link" style="color:black"></i></button> @endif </th>
                    </tr>
                    <tr id="container-{{ $container['id'] }}-edit" hidden>
                        <form action="includes/containers.inc.php" method="POST" id="form-container-{{ $container['id'] }}-edit" enctype="multipart/form-data">
                            <!-- Include CSRF token in the form -->
                            @csrf
                            <input type="hidden" name="container_edit_submit" form="form-container-{{ $container['id'] }}-edit" value="1" />
                            <input type="hidden" name="container_id" form="form-container-{{ $container['id'] }}-edit" value="{{ $container['id'] }}" />
                            <td class="text-center align-middle">@if($container['img_id'] !== '' && $container['img_id'] !== null) <img id="image-{{ $container['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="img/stock/{{ $container['img_image'] }}">@endif</td>
                            <td class="text-center align-middle">{{ $container['id'] }}</td>
                            <td class="text-center align-middle" style="width:300px"><input type="text" class="form-control text-center" style="max-width:100%" name="container_name" form="form-container-{{ $container['id'] }}-edit" value="{{ htmlspecialchars($container['name'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle"><input type="text" class="form-control text-center" style="max-width:100%" name="container_description" form="form-container-{{ $container['id'] }}-edit" value="{{ htmlspecialchars($container['description'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle">{{$container['location']}}</th>
                            <td class="text-center align-middle @if ($container['count'] < 1) red @endif">{{$container['count']}}</td>
                            <td class="text-center align-middle red">No</td>
                            <td class="text-center align-middle" style=""><span style="white-space: nowrap"><button class="btn btn-success" type="submit" form="form-container-{{$container['id']}}-edit" title="Save" style="margin-right:10px"><i class="fa fa-save"></i></button><button type="button" class="btn btn-warning" name="submit" style="padding:3px 12px 3px 12px" onclick="toggleEditcontainer('{{$container['id']}}')">Cancel</button></span></td>
                            <td></td>
                            <th class="text-center align-middle @if (!empty($container['object']) && count($container['object']) > 0) clickable @endif" style="width:50px" id="container-'{{$container['id']}}'-toggle" @if (!empty($container['object']) && count($container['object']) > 0) onclick="toggleHiddencontainer('{{$container['id']}}')">+ @else ><button style="padding: 0px 3px 0px 3px; color:black" class="btn btn-success" onclick="modalLoadAddChildren('{{$container['id']}}', 1)">+ <i class="fa fa-link" style="color:black"></i></button>@endif</th>
                        </form>
                    </tr>
                @if (!empty($container['object']) && count($container['object']) > 0)
                    <tr id="container-{{$container['id']}}-objects" hidden>
                        <td colspan=100% style="position: relative;">
                            <div style="margin: 5px 20px 10px 20px;">
                                <table class="table table-dark theme-table centertable" style="margin:0px;max-width:100%;border: 1px solid #454d55;">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr class="theme-tableOuter">
                                            <th></th>
                                            <th>Item ID</th>
                                            <th>Stock ID</th>
                                            <th>Stock Name</th>
                                            <th style="width:85px"></th>
                                            <button type="button" style="padding: 3px 6px 3px 6px; position: absolute; top: 26px; right: 40px;" class="btn btn-success" onclick="modalLoadAddChildren({{$container['id']}}, 1)">+ Add More</button>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($container['object'] as $stock)
                                        <tr id="containeritem-{{$container['id']}}-stock-{{$stock['id']}}">
                                            <td class="text-center align-middle">@if ($stock['img_id'] !== null)<img id="image-{{ $stock['img_id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="/img/stock/{{$stock['img_image']}}">@endif</td>
                                            <td class="text-center align-middle">{{$stock['item_id']}}</td>
                                            <td class="text-center align-middle">{{$stock['id']}}</td>
                                            <td class="text-center align-middle link" ><a href="stock/{{ $stock['id'] }}" id="containeritem-{{$container['id']}}-item-{{$stock['item_id']}}-name">{{$stock['name']}}</a></td>
                                            <td class="text-center align-middle"  style="width:85px">
                                                <form>
                                                    <button class="btn btn-danger" type="button" name="submit" onclick="modalLoadUnlinkContainer({{$container['id']}}, {{$stock['item_id']}}, 1)" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                        <i class="fa fa-unlink"></i>
                                                    </button>
                                                </form>
                                            </td> 
                                        </tr>
                                    @endforeach
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
    @else
        <div class="container">No Collections Found</div>
    @endif
    
    @include('includes.stock.new-properties')

    </div>
    <!-- Start Modal for uninking from container -->
    <div id="modalDivUnlinkContainer" class="modal">
        <span class="close" onclick="modalCloseUnlinkContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="/containers.unlinkFromContainer" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <input type="hidden" id="form-unlink-container-item-id" name="item_id" value=""  />
                    <input type="hidden" name="container-unlink" value="1"/>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <th colspan=100%>Container:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row" style="padding-top:20px">
                                <th colspan=100%>Item to unlink:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row text-center align-middle" style="padding-top:10px">
                                <td class="text-center align-middle" colspan=100% style="width:100%">
                                    <span style="white-space:nowrap; width:100%">
                                        <button class="btn btn-danger" type="submit" name="submit" style="color:black !important; margin-right:10px">Unlink <i style="margin-left:5px" class="fa fa-unlink"></i></button>
                                        <button class="btn btn-warning" type="button" onclick="modalCloseUnlinkContainer()" style="margin-left:10px">Cancel</button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal for uninking from container -->
    <!-- Container Add New Modal -->
    <div id="modalDivAddContainer" class="modal">
        <span class="close" onclick="modalCloseAddContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="/containers.addContainer" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 200px"><label for="container_name" class="nav-v-c align-middle">Container Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="container_name" name="container_name" placeholder="Name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Container Description:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="container_description" name="container_description" placeholder="Description" /></td>              
                                <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Container" class="btn btn-success"/></td> -->
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Site:</label></td>
                                <td style="margin-left:10px">
                                    <select class="form-control stock-inputSize" id="site" name="site" style="width:228px !important" required>
                                        
                                        @if ($sites['rows'] !== null && count($sites['rows']) > 0)
                                            <option value="" selected disabled hidden>Select Site</option>
                                            @foreach ($sites['rows'] as $site)
                                                <option value="{{$site['id']}}">{{$site['name']}}</option>
                                            @endforeach
                                        @else
                                            <option value="0">No Sites Found...</option>
                                        @endif
                                    
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Area:</label></td>
                                <td style="margin-left:10px">
                                    <select class="form-control stock-inputSize" id="area" name="area" style="width:228px !important" disabled required>
                                        <option value="" selected disabled hidden>Select Area</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Shelf:</label></td>
                                <td style="margin-left:10px">
                                <select class="form-control stock-inputSize" id="shelf" name="shelf" style="width:228px !important" disabled required>
                                        <option value="" selected disabled hidden>Select Shelf</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="container_add_submit" value="Add Container" class="btn btn-success">Add Container</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Link to Container Modal -->
    <div id="modalDivAddChildren" class="modal">
        <span class="close" onclick="modalCloseAddChildren()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
                <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add item to selected container</h4>
                <table class="centertable"><tbody><tr><th style="padding-right:5px">Container ID:</th><td style="padding-right:20px" id="contID"></td><th style="padding-right:5px">Container Name:</th><td id="contName"></td></tr></tbody></table>
                <div class="row" id="TheRow" style="min-width: 100%; max-width:1920px; flex-wrap:nowrap !important; padding-left:10px;padding-right:10px; max-width:max-content">
                    <div class="col well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px;">
                        <p><strong>Stock</strong></p>
                        <input type="text" name="search" class="form-control" style="width:300px; margin-bottom:5px" placeholder="Search" oninput="addChildrenSearch(document.getElementById('contID').innerHTML, this.value)"/>
                        <div style=" overflow-y:auto; overflow-x: hidden; height:300px; ">
                            <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th class='text-center align-middle'>Stock ID</th>
                                        <th class='text-center align-middle'>Name</th>
                                        <th class='text-center align-middle'>Serial Number</th>
                                        <th class='text-center align-middle'>Quantity</th>
                                        <th class='text-center align-middle'>Item ID</th>
                                    </tr>
                                </thead>
                                <tbody id="addChildrenTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <form enctype="multipart/form-data" action="/containers.linkToContainer" method="POST" style="padding: 0px; margin:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                <input type="hidden" name="container-link-fromcontainers" value="1" />
                <input type="hidden" id="addChildrenIsItem" name="is_item" value="" />
                <input type="hidden" id="addChildrenContID" name="container_id" value="" />
                <input type="hidden" id="addChildrenStockID" name="stock_id" value="" />
                <input type="hidden" id="addChildrenItemID" name="item_id" value="" />
                <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                    <input id="submit-button-addChildren" type="submit" name="submit" value="Link" class="btn btn-success" style="margin:10px 10px 0px 10px" disabled></input>
                    <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseAddChildren()">Cancel</button>
                </span>
            </form>
        </div>
    </div>
    <!-- End of Container Add item Modal -->

    <!-- Add the JS for the file -->
    <script src="js/containers.js"></script>

    @include('foot')
</body>