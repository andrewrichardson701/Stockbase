<div class="container-fluid" style="padding-bottom:0px">
    <h3 style="margin-top:50px;font-size:22px" id="memoryattributemanagement-settings">Memory Attribute Management</h3> 
    <!-- Memory Attribute Management Settings -->
    <div class="adminContent" id="memoryattributemanagement">

        @include('includes.response-handling', ['section' => 'memoryattributemanagement-settings'])

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Vendors</h4>

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
                            <button id="show-deleted-memory_vendor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_vendor', 1)" @if (isset($memory_vendor['deleted_count']) && $memory_vendor['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_vendor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_vendor', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_vendors['count'] > 0)
                    @foreach ($memory_vendors['rows'] as $memory_vendor)

                        @if ($memory_vendor['deleted'] == 1)
                        <tr id="memory_vendor-row-{{ $memory_vendor['id'] }}" class="align-middle red theme-divBg memory_vendor-deleted" hidden>
                        @else 
                        <tr id="memory_vendor-row-{{ $memory_vendor['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_vendor"/>
                            <input type="hidden" name="id" value="{{ $memory_vendor['id'] }}">
                            <td id="memory_vendor-{{ $memory_vendor['id'] }}-id" class="text-center align-middle">{{ $memory_vendor['id'] }}</td>
                            <td id="memory_vendor-{{ $memory_vendor['id'] }}-name" class="text-center align-middle">{{ $memory_vendor['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_vendor_links[$memory_vendor['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_vendor['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_vendor_links[$memory_vendor['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_vendor still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_vendor['deleted'] !== 1) 
                                    @if (array_key_exists($memory_vendor['id'], $memory_vendor_links) && ((int)$memory_vendor_links[$memory_vendor['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_vendor-{{ $memory_vendor['id'] }}-links" type="button" onclick="showLinks('memory_vendor', '{{ $memory_vendor['id'] }}')">Show Links</button>
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_vendor['id'], $memory_vendor_links) && ((int)$memory_vendor_links[$memory_vendor['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_vendor-row-{{ $memory_vendor['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_vendor_links[$memory_vendor['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">ECC Types</h4>

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
                            <button id="show-deleted-memory_ecc_type" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_ecc_type', 1)" @if (isset($memory_ecc_type['deleted_count']) && $memory_ecc_type['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_ecc_type" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_ecc_type', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_ecc_types['count'] > 0)
                    @foreach ($memory_ecc_types['rows'] as $memory_ecc_type)

                        @if ($memory_ecc_type['deleted'] == 1)
                        <tr id="memory_ecc_type-row-{{ $memory_ecc_type['id'] }}" class="align-middle red theme-divBg memory_ecc_type-deleted" hidden>
                        @else 
                        <tr id="memory_ecc_type-row-{{ $memory_ecc_type['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_ecc_type"/>
                            <input type="hidden" name="id" value="{{ $memory_ecc_type['id'] }}">
                            <td id="memory_ecc_type-{{ $memory_ecc_type['id'] }}-id" class="text-center align-middle">{{ $memory_ecc_type['id'] }}</td>
                            <td id="memory_ecc_type-{{ $memory_ecc_type['id'] }}-name" class="text-center align-middle">{{ $memory_ecc_type['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_ecc_type_links[$memory_ecc_type['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_ecc_type['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_ecc_type_links[$memory_ecc_type['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_ecc_type still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_ecc_type['deleted'] !== 1) 
                                    @if (array_key_exists($memory_ecc_type['id'], $memory_ecc_type_links) && ((int)$memory_ecc_type_links[$memory_ecc_type['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_ecc_type-{{ $memory_ecc_type['id'] }}-links" type="button" onclick="showLinks('memory_ecc_type', '{{ $memory_ecc_type['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_ecc_type['id'], $memory_ecc_type_links) && ((int)$memory_ecc_type_links[$memory_ecc_type['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_ecc_type-row-{{ $memory_ecc_type['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_ecc_type_links[$memory_ecc_type['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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
                            <button id="show-deleted-memory_speed" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_speed', 1)" @if (isset($memory_speed['deleted_count']) && $memory_speed['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_speed" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_speed', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_speeds['count'] > 0)
                    @foreach ($memory_speeds['rows'] as $memory_speed)

                        @if ($memory_speed['deleted'] == 1)
                        <tr id="memory_speed-row-{{ $memory_speed['id'] }}" class="align-middle red theme-divBg memory_speed-deleted" hidden>
                        @else 
                        <tr id="memory_speed-row-{{ $memory_speed['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_speed"/>
                            <input type="hidden" name="id" value="{{ $memory_speed['id'] }}">
                            <td id="memory_speed-{{ $memory_speed['id'] }}-id" class="text-center align-middle">{{ $memory_speed['id'] }}</td>
                            <td id="memory_speed-{{ $memory_speed['id'] }}-name" class="text-center align-middle">{{ $memory_speed['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_speed_links[$memory_speed['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_speed['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_speed_links[$memory_speed['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_speed still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_speed['deleted'] !== 1) 
                                    @if (array_key_exists($memory_speed['id'], $memory_speed_links) && ((int)$memory_speed_links[$memory_speed['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_speed-{{ $memory_speed['id'] }}-links" type="button" onclick="showLinks('memory_speed', '{{ $memory_speed['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_speed['id'], $memory_speed_links) && ((int)$memory_speed_links[$memory_speed['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_speed-row-{{ $memory_speed['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_speed_links[$memory_speed['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Generations</h4>

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
                            <button id="show-deleted-memory_generation" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_generation', 1)" @if (isset($memory_generation['deleted_count']) && $memory_generation['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_generation" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_generation', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_generations['count'] > 0)
                    @foreach ($memory_generations['rows'] as $memory_generation)

                        @if ($memory_generation['deleted'] == 1)
                        <tr id="memory_generation-row-{{ $memory_generation['id'] }}" class="align-middle red theme-divBg memory_generation-deleted" hidden>
                        @else 
                        <tr id="memory_generation-row-{{ $memory_generation['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_generation"/>
                            <input type="hidden" name="id" value="{{ $memory_generation['id'] }}">
                            <td id="memory_generation-{{ $memory_generation['id'] }}-id" class="text-center align-middle">{{ $memory_generation['id'] }}</td>
                            <td id="memory_generation-{{ $memory_generation['id'] }}-name" class="text-center align-middle">{{ $memory_generation['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_generation_links[$memory_generation['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_generation['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_generation_links[$memory_generation['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_generation still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_generation['deleted'] !== 1) 
                                    @if (array_key_exists($memory_generation['id'], $memory_generation_links) && ((int)$memory_generation_links[$memory_generation['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_generation-{{ $memory_generation['id'] }}-links" type="button" onclick="showLinks('memory_generation', '{{ $memory_generation['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_generation['id'], $memory_generation_links) && ((int)$memory_generation_links[$memory_generation['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_generation-row-{{ $memory_generation['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_generation_links[$memory_generation['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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
                    <tr class="align-middle"><td colspan="100%">No generations found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
        
        
        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Capacity</h4>

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
                            <button id="show-deleted-memory_capacity" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_capacity', 1)" @if (isset($memory_capacity['deleted_count']) && $memory_capacity['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_capacity" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_capacity', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_capacities['count'] > 0)
                    @foreach ($memory_capacities['rows'] as $memory_capacity)

                        @if ($memory_capacity['deleted'] == 1)
                        <tr id="memory_capacity-row-{{ $memory_capacity['id'] }}" class="align-middle red theme-divBg memory_capacity-deleted" hidden>
                        @else 
                        <tr id="memory_capacity-row-{{ $memory_capacity['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_capacity"/>
                            <input type="hidden" name="id" value="{{ $memory_capacity['id'] }}">
                            <td id="memory_capacity-{{ $memory_capacity['id'] }}-id" class="text-center align-middle">{{ $memory_capacity['id'] }}</td>
                            <td id="memory_capacity-{{ $memory_capacity['id'] }}-name" class="text-center align-middle">{{ $memory_capacity['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_capacity_links[$memory_capacity['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_capacity['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_capacity_links[$memory_capacity['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_capacity still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_capacity['deleted'] !== 1) 
                                    @if (array_key_exists($memory_capacity['id'], $memory_capacity_links) && ((int)$memory_capacity_links[$memory_capacity['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_capacity-{{ $memory_capacity['id'] }}-links" type="button" onclick="showLinks('memory_capacity', '{{ $memory_capacity['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_capacity['id'], $memory_capacity_links) && ((int)$memory_capacity_links[$memory_capacity['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_capacity-row-{{ $memory_capacity['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_capacity_links[$memory_capacity['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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
                    <tr class="align-middle"><td colspan="100%">No capacities found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>

        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Form Factor</h4>

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
                            <button id="show-deleted-memory_form_factor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('memory_form_factor', 1)" @if (isset($memory_form_factor['deleted_count']) && $memory_form_factor['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-memory_form_factor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('memory_form_factor', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($memory_form_factors['count'] > 0)
                    @foreach ($memory_form_factors['rows'] as $memory_form_factor)

                        @if ($memory_form_factor['deleted'] == 1)
                        <tr id="memory_form_factor-row-{{ $memory_form_factor['id'] }}" class="align-middle red theme-divBg memory_form_factor-deleted" hidden>
                        @else 
                        <tr id="memory_form_factor-row-{{ $memory_form_factor['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="memory_form_factor"/>
                            <input type="hidden" name="id" value="{{ $memory_form_factor['id'] }}">
                            <td id="memory_form_factor-{{ $memory_form_factor['id'] }}-id" class="text-center align-middle">{{ $memory_form_factor['id'] }}</td>
                            <td id="memory_form_factor-{{ $memory_form_factor['id'] }}-name" class="text-center align-middle">{{ $memory_form_factor['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($memory_form_factor_links[$memory_form_factor['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$memory_form_factor['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($memory_form_factor_links[$memory_form_factor['id']]['count'] ?? 0) !== 0) 
                                    disabled title="memory_form_factor still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$memory_form_factor['deleted'] !== 1) 
                                    @if (array_key_exists($memory_form_factor['id'], $memory_form_factor_links) && ((int)$memory_form_factor_links[$memory_form_factor['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="memory_form_factor-{{ $memory_form_factor['id'] }}-links" type="button" onclick="showLinks('memory_form_factor', '{{ $memory_form_factor['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($memory_form_factor['id'], $memory_form_factor_links) && ((int)$memory_form_factor_links[$memory_form_factor['id']]['count'] ?? 0) !== 0)
                        <tr id="memory_form_factor-row-{{ $memory_form_factor['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Memory ID</th>
                                                <th>Memory Model</th>
                                                <th>Memory Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($memory_form_factor_links[$memory_form_factor['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("memory?search={{ $link['serial_number'] }}")>
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
                    <tr class="align-middle"><td colspan="100%">No form factors found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
        
    </div>
</div>