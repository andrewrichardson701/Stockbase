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
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="cpu_vendor_name" name="name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="cpu-vendor-add" value="Add Vendor" class="btn btn-success" onclick="addCpuProperty('cpu_vendor')">Add Vendor</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewVendor Div -->

<!-- Modal NewVendor Div -->
<div id="modalDivNewModel" class="modal">
<!-- <div id="modalDivNewModel" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewModel()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <table class="centertable">
                <tbody>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="model_name" class="nav-v-c align-middle">Model Name:</label></td>
                        <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle theme-input" id="cpu_model_name" name="name" /></td>
                        <td></td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="socket" class="nav-v-c align-middle">Socket:</label></td>
                        <td style="margin-left:10px">
                            <input class="form-control theme-input" id="socket" type="text" list="sockets" name="socket" placeholder="LGA####" style="min-width:120px" required/>
                            <datalist id="sockets" name="sockets">
                                @if ($cpu_sockets['count'] > 0)
                                    @foreach ($cpu_sockets['rows'] as $cpu_socket)
                                        <option value="{{ $cpu_socket['socket'] }}">{{ $cpu_socket['socket'] }}</option>
                                    @endforeach
                                @endif
                            </datalist>
                        </td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="cpu_family" class="nav-v-c align-middle">CPU Family:</label></td>
                        <td style="margin-left:10px">
                            <input type="text" class="form-control nav-v-c align-middle theme-input" id="cpu_family" name="cpu_family" /></td>
                        </td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="core_count" class="nav-v-c align-middle">Core Count:</label></td>
                        <td style="margin-left:10px">
                            <input type="number" class="form-control nav-v-c align-middle theme-input" id="core_count" name="core_count" /></td>
                        </td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width: 150px"><label for="clock_speed" class="nav-v-c align-middle">Clock Speed:</label></td>
                        <td style="margin-left:10px">
                            <input type="text" class="form-control nav-v-c align-middle theme-input" id="clock_speed" name="clock_speed" /></td>
                        </td>
                    </tr>
                    <tr class="nav-row">
                        <td style="width:150px"></td>
                        <td style="margin-top:10px;margin-left:10px"><button type="button" name="cpu-model-add" value="Add Model" class="btn btn-success" onclick="addCpuProperty('cpu_model')">Add Model</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</div>
<!-- End of Modal NewModel Div -->

<!-- Modal DeleteCpu Div -->
<div id="modalDivDeleteCpu" class="modal">
    <span class="close" onclick="modalCloseDeleteCpu()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('cpus.delete') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="delete-cpu-serial" style="margin-bottom:20px"></h3></td>
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
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Delete" class="btn btn-danger" name="cpu-delete-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseDeleteCpu()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>
<!-- End of DeleteCpu Div -->
<!-- Modal MoveCpu Div -->
<div id="modalDivMoveCpu" class="modal">
    <span class="close" onclick="modalCloseMoveCpu()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('cpus.move') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="move-cpu-serial" style="margin-bottom:20px"></h3></td>
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
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Move" class="btn btn-success" name="cpu-move-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseMoveCpu()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>

<!-- Modal EditCpu Div -->
<div id="modalDivEditCpu" class="modal">
{{-- <div id="modalDivEditCpu" class="modal" style="display: block;"> --}}
    <span class="close" onclick="modalCloseEditCpu()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="{{ route('cpus.edit') }}" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="edit-cpu-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Vendor:</td>
                            <td class="align-middle text-left" colspan=3 style="border:none; padding-right:0px;">
                                <select name="vendor_id" id="vendor_cpu_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                @if ($cpu_vendors['count'] > 0)
                                    @foreach ($cpu_vendors['rows'] as $vendor)
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
                                <select name="model_id" id="model_cpu_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                @if ($cpu_models['count'] > 0)
                                    @foreach ($cpu_models['rows'] as $model)
                                    <option value="{{ $model['id'] }}">{{ $model['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" colspan=3 style="border:none; padding-right:20px">Serial:</td>
                            <td class="align-middle text-left" colspan=2 style="border:none; padding-right:0px;">
                                <input type="text" class="form-control theme-input" id="serial_cpu_edit" name="serial_number" />
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Site:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="site_id" id="site_cpu_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($sites['count'] > 0)
                                        @foreach ($sites['rows'] as $site)
                                        <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Area:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="area_id" id="area_cpu_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
                                    @if ($areas['count'] > 0)
                                        @foreach ($areas['rows'] as $area)
                                        <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle text-right" style="border:none; padding-right:20px">Shelf:</td>
                            <td class="align-middle text-left" style="border:none; padding-right:20px;">
                                <select name="shelf_id" id="shelf_cpu_edit" class="form-control theme-dropdown" style="display:inline !important; max-width:max-content">
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
                                <button type="submit" name="cpu-edit-submit" value="Add Capacity" class="btn btn-success">Save</button>
                                <button type="button" style="margin-left:20px" class="btn btn-warning" onclick="modalCloseEditCpu()">Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>