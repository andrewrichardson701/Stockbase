<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="opticattributemanagement-settings" onclick="toggleSection(this, 'opticattributemanagement')">Optic Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Optic Attribute Management Settings -->
    <div style="padding-top: 20px" id="opticattributemanagement" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'opticattributemanagement-settings'])

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Vendors</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_vendors')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }
        ?>
        @include('includes.response-handling')

        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-optic_vendor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_vendor', 1)" @if (isset($optic_vendor['deleted_count']) && $optic_vendor['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-optic_vendor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_vendor', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($optic_vendors['count'] > 0)
                    @foreach ($optic_vendors['rows'] as $optic_vendor)

                        @if ($optic_vendor['deleted'] == 1)
                        <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}" class="align-middle red theme-divBg optic_vendor-deleted" hidden>
                        @else 
                        <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="optic_vendor"/>
                            <input type="hidden" name="id" value="{{ $optic_vendor['id'] }}">
                            <td id="optic_vendor-{{ $optic_vendor['id'] }}-id" class="text-center align-middle">{{ $optic_vendor['id'] }}</td>
                            <td id="optic_vendor-{{ $optic_vendor['id'] }}-name" class="text-center align-middle">{{ $optic_vendor['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$optic_vendor['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0) 
                                    disabled title="optic_vendor still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$optic_vendor['deleted'] !== 1) 
                                    @if (array_key_exists($optic_vendor['id'], $optic_vendor_links) && ((int)$optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="optic_vendor-{{ $optic_vendor['id'] }}-links" type="button" onclick="showLinks('optic_vendor', '{{ $optic_vendor['id'] }}')">Show Links</button>
                                    @else 
                                        <or class="green">Restore?</or>
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($optic_vendor['id'], $optic_vendor_links) && ((int)$optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0)
                        <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Optic ID</th>
                                                <th>Optic Model</th>
                                                <th>Optic Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($optic_vendor_links[$optic_vendor['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $link['model'] }}</td>
                                                    <td class="text-center">{{ $link['serial_number'] }}</td>

                                                </tr>
                                        @endforeach                                                
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="align-middle"><td colspan="100%">No vendors found.</td></tr>
                @endif
                </tbody>
            </table>
        </div> 

        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Types</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_types')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }

        ?>
        @include('includes.response-handling')

        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-optic_type" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_type', 1)" @if (isset($optic_type['deleted_count']) && $optic_type['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-optic_type" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_type', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($optic_types['count'] > 0)
                    @foreach ($optic_types['rows'] as $optic_type)

                        @if ($optic_type['deleted'] == 1)
                        <tr id="optic_type-row-{{ $optic_type['id'] }}" class="align-middle red theme-divBg optic_type-deleted" hidden>
                        @else 
                        <tr id="optic_type-row-{{ $optic_type['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="optic_type"/>
                            <input type="hidden" name="id" value="{{ $optic_type['id'] }}">
                            <td id="optic_type-{{ $optic_type['id'] }}-id" class="text-center align-middle">{{ $optic_type['id'] }}</td>
                            <td id="optic_type-{{ $optic_type['id'] }}-name" class="text-center align-middle">{{ $optic_type['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($optic_type_links[$optic_type['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$optic_type['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0) 
                                    disabled title="optic_type still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$optic_type['deleted'] !== 1) 
                                    @if (array_key_exists($optic_type['id'], $optic_type_links) && ((int)$optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="optic_type-{{ $optic_type['id'] }}-links" type="button" onclick="showLinks('optic_type', '{{ $optic_type['id'] }}')">Show Links</button> 
                                    @else 
                                        <or class="green">Restore?</or>
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($optic_type['id'], $optic_type_links) && ((int)$optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0)
                        <tr id="optic_type-row-{{ $optic_type['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Optic ID</th>
                                                <th>Optic Model</th>
                                                <th>Optic Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>');
                                        @foreach ($optic_type_links[$optic_type['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $link['model'] }}</td>
                                                    <td class="text-center">{{ $link['serial_number'] }}</td>

                                                </tr>
                                        @endforeach                                                
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="align-middle"><td colspan="100%">No types found.</td></tr>
                @endif
                </tbody>
            </table>
        </div> 

        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Speeds</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_speeds')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }
        ?>
        @include('includes.response-handling')

        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-optic_speed" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_speed', 1)" @if (isset($optic_speed['deleted_count']) && $optic_speed['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-optic_speed" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_speed', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($optic_speeds['count'] > 0)
                    @foreach ($optic_speeds['rows'] as $optic_speed)

                        @if ($optic_speed['deleted'] == 1)
                        <tr id="optic_speed-row-{{ $optic_speed['id'] }}" class="align-middle red theme-divBg optic_speed-deleted" hidden>
                        @else 
                        <tr id="optic_speed-row-{{ $optic_speed['id'] }}" class="align-middle">
                        @endif
                        <form encspeed="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-speed" value="optic_speed"/>
                            <input type="hidden" name="id" value="{{ $optic_speed['id'] }}">
                            <td id="optic_speed-{{ $optic_speed['id'] }}-id" class="text-center align-middle">{{ $optic_speed['id'] }}</td>
                            <td id="optic_speed-{{ $optic_speed['id'] }}-name" class="text-center align-middle">{{ $optic_speed['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($optic_speed_links[$optic_speed['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$optic_speed['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0) 
                                    disabled title="optic_speed still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$optic_speed['deleted'] !== 1) 
                                    @if (array_key_exists($optic_speed['id'], $optic_speed_links) && ((int)$optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="optic_speed-{{ $optic_speed['id'] }}-links" type="button onclick="showLinks('optic_speed', '{{ $optic_speed['id'] }}')">Show Links</button> 
                                    @else 
                                        <or class="green">Restore?</or>
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($optic_speed['id'], $optic_speed_links) && ((int)$optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0)
                        <tr id="optic_speed-row-{{ $optic_speed['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Optic ID</th>
                                                <th>Optic Model</th>
                                                <th>Optic Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>');
                                        @foreach ($optic_speed_links[$optic_speed['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $link['model'] }}</td>
                                                    <td class="text-center">{{ $link['serial_number'] }}</td>
                                                </tr>
                                        @endforeach                                                
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="align-middle"><td colspan="100%">No speeds found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>

        
        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Connectors</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_connectors')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }
        ?>
        @include('includes.response-handling')
        
        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-optic_connector" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_connector', 1)" @if (isset($optic_connector['deleted_count']) && $optic_connector['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-optic_connector" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_connector', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($optic_connectors['count'] > 0)
                    @foreach ($optic_connectors['rows'] as $optic_connector)

                        @if ($optic_connector['deleted'] == 1)
                        <tr id="optic_connector-row-{{ $optic_connector['id'] }}" class="align-middle red theme-divBg optic_connector-deleted" hidden>
                        @else 
                        <tr id="optic_connector-row-{{ $optic_connector['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-connector" value="optic_connector"/>
                            <input type="hidden" name="id" value="{{ $optic_connector['id'] }}">
                            <td id="optic_connector-{{ $optic_connector['id'] }}-id" class="text-center align-middle">{{ $optic_connector['id'] }}</td>
                            <td id="optic_connector-{{ $optic_connector['id'] }}-name" class="text-center align-middle">{{ $optic_connector['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($optic_connector_links[$optic_connector['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$optic_connector['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0) 
                                    disabled title="optic_connector still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$optic_connector['deleted'] !== 1) 
                                    @if (array_key_exists($optic_connector['id'], $optic_connector_links) && ((int)$optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="optic_connector-{{ $optic_connector['id'] }}-links" type="button" onclick="showLinks('optic_connector', '{{ $optic_connector['id'] }}')">Show Links</button> 
                                    @else 
                                        <or class="green">Restore?</or>
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($optic_connector['id'], $optic_connector_links) && ((int)$optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0)
                        <tr id="optic_connector-row-{{ $optic_connector['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Optic ID</th>
                                                <th>Optic Model</th>
                                                <th>Optic Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>');
                                        @foreach ($optic_connector_links[$optic_connector['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $link['model'] }}</td>
                                                    <td class="text-center">{{ $link['serial_number'] }}</td>
                                                </tr>
                                        @endforeach                                                
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="align-middle"><td colspan="100%">No connectors found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
        
        
        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Distances</h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_distances')) {
        //     echo('<div style="margin-right: 10px; margin-left: 10px">');
        //     showResponse();
        //     echo('</div>');
        // }
        ?>
        @include('includes.response-handling')
        
        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-optic_distance" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_distance', 1)" @if (isset($optic_distance['deleted_count']) && $optic_distance['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-optic_distance" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_distance', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($optic_distances['count'] > 0)
                    @foreach ($optic_distances['rows'] as $optic_distance)

                        @if ($optic_distance['deleted'] == 1)
                        <tr id="optic_distance-row-{{ $optic_distance['id'] }}" class="align-middle red theme-divBg optic_distance-deleted" hidden>
                        @else 
                        <tr id="optic_distance-row-{{ $optic_distance['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-distance" value="optic_distance"/>
                            <input type="hidden" name="id" value="{{ $optic_distance['id'] }}">
                            <td id="optic_distance-{{ $optic_distance['id'] }}-id" class="text-center align-middle">{{ $optic_distance['id'] }}</td>
                            <td id="optic_distance-{{ $optic_distance['id'] }}-name" class="text-center align-middle">{{ $optic_distance['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($optic_distance_links[$optic_distance['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$optic_distance['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0) 
                                    disabled title="optic_distance still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$optic_distance['deleted'] !== 1) 
                                    @if (array_key_exists($optic_distance['id'], $optic_distance_links) && ((int)$optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="optic_distance-{{ $optic_distance['id'] }}-links" type="button" onclick="showLinks('optic_distance', '{{ $optic_distance['id'] }}')">Show Links</button> 
                                    @else 
                                        <or class="green">Restore?</or>
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($optic_distance['id'], $optic_distance_links) && ((int)$optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0)
                        <tr id="optic_distance-row-{{ $optic_distance['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Optic ID</th>
                                                <th>Optic Model</th>
                                                <th>Optic Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>');
                                        @foreach ($optic_distance_links[$optic_distance['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $link['model'] }}</td>
                                                    <td class="text-center">{{ $link['serial_number'] }}</td>
                                                </tr>
                                        @endforeach                                                
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="align-middle"><td colspan="100%">No distances found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
        
    </div>
</div>