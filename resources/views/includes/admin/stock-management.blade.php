<div class="container" style="padding-bottom:0px">       
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stockmanagement-settings" onclick="toggleSection(this, 'stockmanagement')">Stock Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Stock Management Settings -->
    <div style="padding-top: 20px" id="stockmanagement" hidden>
        <h4 style="margin-left:10px; margin-right:10px; margin-top:5px; font-size:20px; margin-bottom:10px">Cost Enablement</h4>
        @include('includes.response-handling', ['section' => 'stockmanagement-settings'])
        
        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <p id="cost-output" class="last-edit-T" hidden></p>
            <table>
                <tbody>
                    <tr>
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-cost">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for normal stock items.">Normal Stock Cost:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-cost-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="normal-cost" onchange="toggleCost(this, 1)" @if ($head_data['config']['cost_enable_normal'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="cable-cost">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for cable stock items.">Cable Stock Cost:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="cable-cost-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="cable-cost" onchange="toggleCost(this, 2)"" @if ($head_data['config']['cost_enable_cable'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4 style="margin-left:10px; margin-right:10px; margin-top:20px; font-size:20px; margin-bottom:10px">Deleted Stock</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'stockmanagement')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }
        // cost/price toggles for both normal stock and cable stock
        ?>
        @include('includes.response-handling')

        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <p class="margin-left:10px">Count: <or class="green">@if (!empty($deleted_stock['count'])) {{ $deleted_stock['count'] }} @else 0 @endif</or></p>
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">SKU</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Description</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Cable?</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Restore</th>
                    </tr>
                </thead>
                <tbody>
            @if ($deleted_stock['count'] > 0)
                @foreach ($deleted_stock['rows'] as $stock)
                    <tr id="deleted-stock-row-{{ $stock['id'] }}" class="align-middle">
                        <form enctype="multipart/form-data" action="./includes/stock-modify.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="stockmanagement-type" value="deleted"/>
                            <input type="hidden" name="id" value="{{ $stock['id'] }}">
                            <td class="align-middle text-center">{{ $stock['id'] }}</td>
                            <td class="align-middle text-center"><a class="link" href="stock?stock_id={{ $stock['id'] }}">{{ $stock['name'] }}</a></td>
                            <td class="align-middle text-center">{{ $stock['sku'] }}</td>
                            <td class="align-middle text-center">
                                @if (strlen($stock['description']) > 30) 
                                    <or title="{{ $stock['description'] }}">{{ substr($stock['description'], 0, 27) . '...' }}</or>
                                @else
                                    <or>{{ $stock['description'] }}</or>
                                @endif
                                </or>
                            </td>
                            <td class="align-middle text-center">@if ((int)$stock['is_cable'] == 1) <or class="green">Yes</or> @else <or class="red">No</or>@endif</td>
                            <td class="align-middle text-center"><button class="btn btn-success" type="submit" name="stockmanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                        </form>
                    </tr>
                @endforeach
            @else 
                    <tr class="align-middle"><td colspan="100%">No deleted stock found.</td></tr>
            @endif
                </tbody>
            </table>
        </div>
    </div>
</div>