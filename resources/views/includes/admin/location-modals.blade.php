<div class="container" style="padding-bottom:0px">
    @include('includes.response-handling', ['section' => 'modals'])
    
    <div id="modalDivAdd" class="modal">
        <span class="close" onclick="modalCloseAdd()">×</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <div style="display:block"> 
                    <h2 style="margin-bottom:20px">Add new Site / Area / Shelf</h2>
                    <form id="locationForm" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="admin" value="1" />
                        <table class="centertable">
                            <thead>
                                <tr>
                                    <th style="padding-left:20px">Type</th>
                                    <th style="padding-left:5px" class="specialInput shelf area" hidden>Parent</th>
                                    <th style="padding-left:5px" class="specialInput shelf area site" hidden>Name</th>
                                    <th style="padding-left:5px" class="specialInput area site" hidden>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding-left:15px;padding-right:15px">
                                        <select id="addLocation-type" class="form-control" name="type" onchange="showInput()">
                                            <option selected disabled>Select a Type</option>
                                            <option value="site">Site</option>
                                            <option value="area">Area</option>
                                            <option value="shelf">Shelf</option>
                                        </select>
                                    </td>
                                    <td style="padding-right:15px" class="specialInput area shelf" hidden>
                                        <select id="addLocation-parent" class="form-control" name="parent" disabled>
                                        </select>
                                    </td>
                                    <td style="padding-right:15px" class="specialInput area shelf site" hidden><input class="form-control" type="text" name="name" placeholder="Name"/></td>
                                    <td style="padding-right:15px" class="specialInput area site" hidden><input class="form-control" type="text" name="description" placeholder="Description"/></td>
                                </tr>
                                <tr>
                                    <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-submit" style="margin-left:10px" value="1">Submit</button></td>
                                </tr>
                            </tbody>
                        </table>        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modalDivEdit" class="modal">
        <span class="close" onclick="modalCloseEdit()">×</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <div style="display:block"> 
                    <h2 style="margin-bottom:20px">Edit Location</h2>
                    <form id="editLocationForm" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                        @csrf
                        <table class="centertable">
                            <tbody>
                                <tr class="align-middle">
                                    <th style="padding-right:15px">Type:</th>
                                    <td>
                                        <input id="location-type-input" type="hidden" name="type" value="" />
                                        <label style="margin-bottom:0px" id="location-type-text"></label>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="padding-top:15px; padding-right:10px; padding-bottom:10px ">ID:</th>
                                    <td>
                                        <input id="location-id-input" type="hidden" name="id" value="" />
                                        <label style="margin-bottom:0px" id="location-id-text"></label>
                                    </td>
                                </tr>
                                <tr id="location-parent-site-tr" class="align-middle">
                                    <th id="location-parent-site-th" style="padding-right:15px">Site:</th>
                                    <td>
                                        <select class="form-control" id="location-parent-site-input" name="site_id"></select>
                                    </td>
                                </tr>
                                <tr id="location-parent-area-tr" class="align-middle">
                                    <th id="location-parent-area-th" style="padding-right:15px">Area:</th>
                                    <td>
                                        <select class="form-control" id="location-parent-area-input" name="area_id"></select>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="padding-right:15px">Name:</th>
                                    <td>
                                        <input type="text" class="form-control" id="location-name-input" name="name" value="" />
                                    </td>
                                </tr>
                                <tr id="location-description-tr" class="align-middle">
                                    <th style="padding-right:15px">Description:</th>
                                    <td>
                                        <input type="text" class="form-control" style="width:400px" id="location-description-input" name="description" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-edit-submit" style="margin-left:10px;margin-top:20px" value="1">Save</button></td>
                                </tr>
                            </tbody>
                        </table>        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modalAddPermPreset" class="modal">
        <span class="close" onclick="modalCloseAddPermPreset()">×</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <div style="display:block"> 
                    <h2 style="margin-bottom:20px">Add Permisions Preset</h2>
                    <form id="addPermPresetForm" enctype="multipart/form-data" action="{{ route('admin.userSettings') }}" method="POST">
                        @csrf
                        <table class="" style="margin-bottom:10px">
                            <tbody>
                                <tr class="align-middle">
                                    <th style="padding-right:15px">Preset Name:</th>
                                    <td>
                                        <input type="text" class="form-control" id="preset-name-input" name="name" value="" />
                                    </td>
                                </tr>
                            <tbody>
                        </table>
                        <table class="table table-dark theme-table centertable" style="min-width:100%">
                            <tbody>
                                @if ($users_permissions_roles['count'] > 0)
                                    <tr class="align-middle text-center">
                                        @foreach(array_keys($users_permissions_roles['rows'][1]) as $key)
                                            @if (in_array($key, ['id', 'name']))

                                            @elseif (in_array($key, ['created_at', 'updated_at']))
                                            
                                            @else
                                                <th>{{ $key }}</th>
                                            @endif
                                        @endforeach
                                    </tr class="align-middle text-center">
                                        @foreach(array_keys($users_permissions_roles['rows'][1]) as $key)
                                            @if (in_array($key, ['id', 'name']))

                                            @elseif (in_array($key, ['created_at', 'updated_at']))
                                            
                                            @else
                                                <th id="permissions_preset_add-{{ $key }}" style="vertical-align: middle;">
                                                    <input type="checkbox" name="{{ $key }}" @if($key == 'root') disabled title="Cannot assign this role." style="cursor:not-allowed" @endif/>
                                                </th>
                                            @endif
                                        @endforeach
                                                
                                @else 
                                    <tr class="align-middle" colspan=100%><td colspan=6>No roles found.</td></tr>
                                @endif
                                <tr>
                                    <td colspan="100%" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="user_permissions_preset_add" style="margin-left:10px;margin-top:20px" value="1">Add</button></td>
                                </tr>
                            </tbody>
                        </table>        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>