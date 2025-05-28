<form action="" method="GET" style="margin-bottom:0px">
    <div class="container" id="stock-info-left">
        <div class="nav-row" id="search-stock-row">
            <table>
                <tbody>
                    <tr>
                        <td style="padding-right:20px">Search for item</td>
                        <td><input class="form-control stock-inputSize theme-input" type="text" id="search" name="search" oninput="getInventory(1)" placeholder="Search for item" value="{{ htmlspecialchars($params['request']['search'] ?? '', ENT_QUOTES, 'UTF-8') }}"/></td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </div>
</form>
<div class="container well-nopad theme-divBg" style="margin-top:20px;padding-left:20px">
    <input type="hidden" id="inv-action-type" name="inv-action-type" value="move" />
    <table class="table table-dark theme-table" id="inventoryTable" style="padding-bottom:0px;margin-bottom:0px">
        <thead style="text-align: center; white-space: nowrap;">
            <tr class="theme-tableOuter">
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th hidden>Descritpion</th>
                <th class="viewport-large-empty">SKU</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody id="inv-body" class="align-middle" style="text-align: center; white-space: nowrap;">                       
        </tbody>
    </table>
    <table class="table table-dark theme-table centertable">
        <tbody>
            <tr class="theme-tableOuter">
                <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                <div class="row">
                    <div class="col text-center"></div>
                    <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                    </div>
                    <div class="col text-center">
                    </div>
                </div>
            </tr>
        </tbody>
    </table>
</div>