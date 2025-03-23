<!-- Modal NewType Div -->
<div id="modalDivNewType" class="modal">
<!-- <div id="modalDivNewType" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewType()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form id="add-optic-type-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                @foreach ($params['request'] as $key => $req)
                    <input type="hidden" name="QUERY['{{ $key }}']" value="{{ $req }}"/>
                @endforeach
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="type_name" class="nav-v-c align-middle">Type Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="type_name" name="type_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-type-add" value="Add Type" class="btn btn-success">Add Type</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
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
            <form id="add-optic-vendor-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                @foreach ($params['request'] as $key => $req)
                    <input type="hidden" name="QUERY['{{ $key }}']" value="{{ $req }}"/>
                @endforeach
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="vendor_name" class="nav-v-c align-middle">Vendor Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="vendor_name" name="vendor_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-vendor-add" value="Add Vendor" class="btn btn-success">Add Vendor</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
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
            <form id="add-optic-speed-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                @foreach ($params['request'] as $key => $req)
                    <input type="hidden" name="QUERY['{{ $key }}']" value="{{ $req }}"/>
                @endforeach
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="speed_name" class="nav-v-c align-middle">Speed:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="speed_name" name="speed_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-speed-add" value="Add Speed" class="btn btn-success">Add Speed</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div> 
</div>
<!-- Modal NewConnector Div -->
<div id="modalDivNewConnector" class="modal">
<!-- <div id="modalDivNewConnector" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewConnector()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form id="add-optic-connector-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                @foreach ($params['request'] as $key => $req)
                    <input type="hidden" name="QUERY['{{ $key }}']" value="{{ $req }}"/>
                @endforeach
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="connector_name" class="nav-v-c align-middle">Connector Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="connector_name" name="connector_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-connector-add" value="Add Connector" class="btn btn-success">Add Connector</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div> 
</div>
<!-- Modal NewDistance Div -->
<div id="modalDivNewDistance" class="modal">
<!-- <div id="modalDivNewDistance" style="display: block;"> -->
    <span class="close" onclick="modalCloseNewDistance()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form id="add-optic-distance-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                @foreach ($params['request'] as $key => $req)
                    <input type="hidden" name="QUERY['{{ $key }}']" value="{{ $req }}"/>
                @endforeach
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="distance_name" class="nav-v-c align-middle">Distance Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="distance_name" name="distance_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-distance-add" value="Add Distance" class="btn btn-success">Add Distance</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div> 
</div>
<!-- End of Modal NewDistance Div -->
<!-- Modal DeleteOptic Div -->
<div id="modalDivDeleteOptic" class="modal">
    <span class="close" onclick="modalCloseDeleteOptic()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="delete-optic-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Reason for Deletion:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <input id="delete-reason" type="text" class="form-control" placeholder="Reason..." name="reason" required/>
                                <input type="hidden" id="delete-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Delete" class="btn btn-danger" name="optic-delete-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseDeleteOptic()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>
<!-- End of DeleteOptic Div -->
<!-- Modal MoveOptic Div -->
<div id="modalDivMoveOptic" class="modal">
    <span class="close" onclick="modalCloseMoveOptic()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
            <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <table class="centertable" style="border:none">
                    <tbody style="border:none">
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="move-optic-serial" style="margin-bottom:20px"></h3></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" colspan=100% style="border:none">
                            <p style="margin-bottom:5px">Move location:</p></td>
                        </tr>
                        <tr>
                            <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                <select name="move-site" class="form-control" style="display:inline !important; max-width:max-content">');
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                                <input type="hidden" id="move-id" name="id" />
                            </td>
                            <td class="align-middle text-center" style="border:none"><input type="submit" value="Move" class="btn btn-success" name="optic-move-submit" /></td>
                            <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseMoveOptic()">Cancel</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>  
    </div>
</div>