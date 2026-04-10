<!-- Modal NewType Div -->
<div id="modalDivNewType" class="modal">
<!-- <div id="modalDivNewType" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewType()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="type_name" class="nav-v-c align-middle">Type Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="type_name" name="type_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-type-add" value="Add Type" class="btn btn-success" onclick="addDiskProperty('type')">Add Type</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewType Div -->
<!-- Modal NewVendor Div -->
<div id="modalDivNewVendor" class="modal">
<!-- <div id="modalDivNewVendor" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewVendor()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="vendor_name" class="nav-v-c align-middle">Vendor Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="vendor_name" name="vendor_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-vendor-add" value="Add Vendor" class="btn btn-success" onclick="addDiskProperty('vendor')">Add Vendor</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewVendor Div -->
<!-- Modal NewSpeed Div -->
<div id="modalDivNewSpeed" class="modal">
<!-- <div id="modalDivNewSpeed" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewSpeed()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="speed_name" class="nav-v-c align-middle">Speed:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="speed_name" name="speed_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-speed-add" value="Add Speed" class="btn btn-success"  onclick="addDiskProperty('speed')">Add Speed</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- Modal NewCapacity Div -->
<div id="modalDivNewCapacity" class="modal">
<!-- <div id="modalDivNewCapacity" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewCapacity()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="capacity_name" class="nav-v-c align-middle">Capacity Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="capacity_name" name="capacity_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-capacity-add" value="Add Capacity" class="btn btn-success" onclick="addDiskProperty('capacity')">Add Capacity</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- Modal NewCaddy Div -->
<div id="modalDivNewCaddy" class="modal">
    <span class="close" onclick="modalCloseNewCaddy()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="caddy_name" class="nav-v-c align-middle">Caddy Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="caddy_name" name="caddy_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-caddy-add" value="Add Caddy" class="btn btn-success" onclick="addDiskProperty('caddy')">Add Caddy</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewCaddy Div -->
<!-- Modal DeleteDisk Div -->
<div id="modalDivDeleteDisk" class="modal">
    <span class="close" onclick="modalCloseDeleteDisk()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('disks.delete') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="delete-disk-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Reason for Deletion:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <input id="delete-reason" type="text" class="form-control theme-input" placeholder="Reason..." name="reason" required/>
                                <input type="hidden" id="delete-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Delete" class="btn btn-danger" name="disk-delete-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseDeleteDisk()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>
<!-- End of DeleteDisk Div -->
<!-- Modal MoveDisk Div -->
<div id="modalDivMoveDisk" class="modal">
    <span class="close" onclick="modalCloseMoveDisk()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('disks.move') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="move-disk-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Move location:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <select name="site" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                                <input type="hidden" id="move-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Move" class="btn btn-success" name="disk-move-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseMoveDisk()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>

<!-- Modal DeleteDisk Div -->
<div id="modalDivDeleteDisk" class="modal">
    <span class="close" onclick="modalCloseDeleteDisk()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('disks.delete') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="delete-disk-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Reason for Deletion:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <input id="delete-reason" type="text" class="form-control theme-input" placeholder="Reason..." name="reason" required/>
                                <input type="hidden" id="delete-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Delete" class="btn btn-danger" name="disk-delete-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseDeleteDisk()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>
<!-- End of DeleteDisk Div -->
<!-- Modal MoveDisk Div -->
<div id="modalDivMoveDisk" class="modal">
    <span class="close" onclick="modalCloseMoveDisk()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('disks.move') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="move-disk-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Move location:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <select name="site" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                                <input type="hidden" id="move-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Move" class="btn btn-success" name="disk-move-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseMoveDisk()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>


<!-- Modal EditDisk Div -->
<div id="modalDivEditDisk" class="modal">
{{-- <div id="modalDivEditDisk" class="modal" style="display: block;"> --}}
    <span class="close" onclick="modalCloseEditDisk()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('disks.edit') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="edit-disk-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Vendor:</td>
                            <td class="align-middle text-left" colspan=3 style="border:none; padding-right:0px;">
                                <select name="vendor_id" id="vendor_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                @if ($disk_vendors['count'] > 0)
                                    @foreach ($disk_vendors['rows'] as $vendor)
                                    <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                                <input type="hidden" id="edit-id" name="id" />
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Model:</td>
                            <td class="align-middle text-left" colspan=2 style="border:none; padding-right:0px;">
                                <input type="text" class="form-control theme-input" id="model_disk_edit" name="model" />
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Serial:</td>
                            <td class="align-middle text-left" colspan=2 style="border:none; padding-right:0px;">
                                <input type="text" class="form-control theme-input" id="serial_disk_edit" name="serial_number" />
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Type:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="type_id" id="type_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($disk_types['count'] > 0)
                                        @foreach ($disk_types['rows'] as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" colspan=1 style="border:none; padding-right:20px">Capacity:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="capacity_id" id="capacity_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($disk_capacities['count'] > 0)
                                        @foreach ($disk_capacities['rows'] as $capacity)
                                        <option value="{{ $capacity['id'] }}">{{ $capacity['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Speed:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="speed_id" id="speed_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($disk_speeds['count'] > 0)
                                        @foreach ($disk_speeds['rows'] as $speed)
                                        <option value="{{ $speed['id'] }}">{{ $speed['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" colspan=1 style="border:none; padding-right:20px">Form Factor:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="form_factor" id="form_factor_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    <option value="3.5">3.5"</option>
                                    <option value="2.5">2.5"</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Caddy:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="caddy_id" id="caddy_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($disk_caddies['count'] > 0)
                                        @foreach ($disk_caddies['rows'] as $caddy)
                                        <option value="{{ $caddy['id'] }}">{{ $caddy['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" colspan=1 style="border:none; padding-right:20px">HDD/SSD:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="ssd" id="ssd_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    <option value="0">HDD</option>
                                    <option value="1">SSD</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Destroy?:</td>
                            <td class="align-middle text-left" colspan=1 style="border:none; padding-right:0px;">
                                <select name="destroy" id="destroy_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Site:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="site_id" id="site_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($sites['count'] > 0)
                                        @foreach ($sites['rows'] as $site)
                                        <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Area:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="area_id" id="area_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($areas['count'] > 0)
                                        @foreach ($areas['rows'] as $area)
                                        <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Shelf:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="shelf_id" id="shelf_disk_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($shelves['count'] > 0)
                                        @foreach ($shelves['rows'] as $shelf)
                                            <option value="{{ $shelf['id'] }}">{{ $shelf['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=3></td>
                            <td class="align-middle text-left" colspan=3 style="padding-top:10px">
                                <button type="submit" name="disk-edit-submit" value="Add Capacity" class="btn btn-success">Save</button>
                                <button type="button" style="margin-left:20px" class="btn btn-warning" onclick="modalCloseEditDisk()">Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>