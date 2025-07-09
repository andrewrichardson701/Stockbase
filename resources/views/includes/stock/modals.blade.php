<!-- Start Modal for uninking from container -->
<div id="modalDivUnlinkContainer" class="modal">
    <span class="close" onclick="modalCloseUnlinkContainer()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
            <form action="{{ route('containers.unlinkFromContainer') }}" method="POST" enctype="multipart/form-data">
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
        <form enctype="multipart/form-data" action="{{ route('containers.linkToContainer') }}" method="POST" style="padding: 0px; margin:0px">
            <!-- Include CSRF token in the form -->
            @csrf
            <input type="hidden" name="container-link-fromstock" value="1" />
            <input type="hidden" id="addChildrenContID" name="container_id" value="" />
            <input type="hidden" id="addChildrenStockID" name="stock_id" value="" />
            <input type="hidden" id="addChildrenItemID" name="item_id" value="" />
            <input type="hidden" id="addChildrenIsItem" name="is_item" value="1" />
            <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                <input id="submit-button-addChildren" type="submit" name="submit" value="Link" class="btn btn-success" style="margin:10px 10px 0px 10px" disabled></input>
                <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseAddChildren()">Cancel</button>
            </span>
        </form>
    </div>
</div>
<!-- End of Container Add item Modal -->

<!-- Container Add item Modal -->
<div id="modalDivLinkToContainer" class="modal">
    <span class="close" onclick="modalCloseLinkToContainer()">&times;</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
            <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add to container</h4>
            <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="linkToContainerItemID"></td><th style="padding-right:5px">Item Name:</th><td id="linkToContainerItemName"></td></tr></tbody></table>
            <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                <p><strong>Containers</strong></p>
                <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Name</th>
                            <th class="text-center align-middle">Description</th>
                            <th class="text-center align-middle">Container is item</th>
                        </tr>
                    </thead>
                    <tbody id="containerSelectTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <form class="padding:0px;margin:0px" action="{{ route('containers.linkToContainer') }}" method="POST" enctype="multipart/form-data">
            <!-- Include CSRF token in the form -->
            @csrf
            <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                <input type="hidden" name="container-link" value="1" />
                <input type="hidden" id="linkToContainerTableItemID" name="item_id" />
                <input type="hidden" id="linkToContainerTableID" name="container_id" />
                <input type="hidden" id="linkToContainerTableItem" name="is_item" />
                <input type="submit" id="containerLink-submit-button" name="submit" class="btn btn-success" style="margin:10px 10px 0px 10px" value="Link" disabled>
                <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseLinkToContainer()">Cancel</button>
            </span>
        </form>
    </div>
</div>
<!-- End of Link to Container-->