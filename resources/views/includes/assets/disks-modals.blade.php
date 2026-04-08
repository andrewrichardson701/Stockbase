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
<!-- Modal NewDistance Div -->
<div id="modalDivNewDistance" class="modal">
<!-- <div id="modalDivNewDistance" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewDistance()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="distance_name" class="nav-v-c align-middle">Distance Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="distance_name" name="distance_name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="disk-distance-add" value="Add Distance" class="btn btn-success" onclick="addDiskProperty('distance')">Add Distance</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewDistance Div -->
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