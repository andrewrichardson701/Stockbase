<!-- Modal Image Properties Div -->
<div id="modalDivProperties" class="modal">
<!-- <div id="modalDivProperties" style="display: block;"> -->
    <span class="close" onclick="modalCloseProperties()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <!-- Tag -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-tag" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="tag_name" class="nav-v-c align-middle">Tag Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="tag_name" name="property_name" /></td>
                            <td></td>
                        </tr>
                        <tr class="nav-row">
                            <td style="width: 150px"><label for="tag_description" class="nav-v-c align-middle">Tag Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="tag_description" name="description" /></td>              
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Tag" class="btn btn-success"/></td> -->
                        </tr>
                        <tr class="nav-row">
                            <td style="width:150px"></td>
                            <td style="margin-top:10px;margin-left:10px"><button type="submit" name="submit" value="Add Tag" class="btn btn-success" onclick="addProperty('tag')">Add Tag</button></td>
                            <td hidden><input id="tag_type" type="hidden" name="type" value="tag" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Manufacturer -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-manufacturer" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td><label for="manufacturer_name" class="nav-v-c align-middle">New Manufacturer:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="manufacturer_name" name="property_name" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Manufacturer" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Manufacturer" class="btn btn-success" onclick="addProperty('manufacturer')">Add Manufacturer</button></td>
                            <td hidden><input id="manufacturer_type" type="hidden" name="type" value="manufacturer" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Site -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-site" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable" style="border-collapse: collapse;table-layout:fixed;">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width: 130px"><label for="site_name" class="nav-v-c align-middle">New Site Name:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="site_name" name="property_name" /></td>           
                        </tr>
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width: 130px"><label for="site_description" class="nav-v-c align-middle">Site Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="site_description" name="description" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Site" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Site" class="btn btn-success" onclick="addProperty('site')">Add Site</button></td>
                            <td hidden><input type="hidden" name="type" value="site" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Area -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-area" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row">
                            <td style="width:100px"><label for="area_name" class="nav-v-c align-middle">Site:</label></td>
                            <td style="margin-left:10px">
                                <select class="form-control" name="site_id">
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
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width:100px"><label for="area_name" class="nav-v-c align-middle">New Area:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="area_name" name="property_name" /></td>           
                            <td hidden><input type="hidden" name="type" value="area" /></td>
                        </tr>
                        <tr class="nav-row" style="margin-top:10px">
                            <td style="width: 100px"><label for="area_description" class="nav-v-c align-middle">Description:</label></td>
                            <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="area_description" name="description" /></td>   
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Area" class="btn btn-success" onclick="addProperty('area')">Add Area</button></td>        
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Area" class="btn btn-success"/></td> -->
                            <td hidden><input type="hidden" name="type" value="area" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
        <!-- Shelf -->
        <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-shelf" hidden>
            <!-- <form action="includes/stock-new-properties.inc.php" method="POST" enctype="multipart/form-data"> -->
                <table class="centertable">
                    <tbody>
                        <tr class="nav-row" >
                            <td style="width:150px">Site: </td>
                            <td>
                                <select class="form-control" id="site-properties" name="site_id" style="width:300px" required>
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
                        <tr class="nav-row" >
                            <td style="width:150px">Area: </td>
                            <td>
                                <select class="form-control" id="area-properties" name="area_id" style="width:300px" disabled required>
                                    <option value="" selected disabled hidden>Select Area</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="nav-row" >
                            <td style="width:150px"><label for="shelf_name" class="nav-v-c align-middle">New Shelf Name:</label></td>
                            <td><input type="text" class="form-control nav-v-c align-middle" id="shelf_name" name="property_name" /></td>           
                            <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Shelf" class="btn btn-success"/></td> -->
                            <td style="margin-left:5px"><button type="submit" name="submit" value="Add Shelf" class="btn btn-success" onclick="addProperty('shelf')">Add Shelf</button></td>
                            <td hidden><input type="hidden" name="type" value="shelf" /></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </form> -->
        </div>
    </div> 
</div>
<!-- End of Modal Image Properties Div -->

<script src="js/new-properties.js"></script>
