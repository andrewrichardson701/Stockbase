<div class="container-fluid" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="diskattributemanagement-settings">Disk Attribute Management</h3> 
    <!-- Disk Attribute Management Settings -->
    <div class="adminContent" id="diskattributemanagement">

        @include('includes.response-handling', ['section' => 'diskattributemanagement-settings'])

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
                            <button id="show-deleted-disk_vendor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_vendor', 1)" @if (isset($disk_vendor['deleted_count']) && $disk_vendor['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_vendor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_vendor', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_vendors['count'] > 0)
                    @foreach ($disk_vendors['rows'] as $disk_vendor)

                        @if ($disk_vendor['deleted'] == 1)
                        <tr id="disk_vendor-row-{{ $disk_vendor['id'] }}" class="align-middle red theme-divBg disk_vendor-deleted" hidden>
                        @else 
                        <tr id="disk_vendor-row-{{ $disk_vendor['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_vendor"/>
                            <input type="hidden" name="id" value="{{ $disk_vendor['id'] }}">
                            <td id="disk_vendor-{{ $disk_vendor['id'] }}-id" class="text-center align-middle">{{ $disk_vendor['id'] }}</td>
                            <td id="disk_vendor-{{ $disk_vendor['id'] }}-name" class="text-center align-middle">{{ $disk_vendor['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_vendor_links[$disk_vendor['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_vendor['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_vendor_links[$disk_vendor['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_vendor still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_vendor['deleted'] !== 1) 
                                    @if (array_key_exists($disk_vendor['id'], $disk_vendor_links) && ((int)$disk_vendor_links[$disk_vendor['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_vendor-{{ $disk_vendor['id'] }}-links" type="button" onclick="showLinks('disk_vendor', '{{ $disk_vendor['id'] }}')">Show Links</button>
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_vendor['id'], $disk_vendor_links) && ((int)$disk_vendor_links[$disk_vendor['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_vendor-row-{{ $disk_vendor['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_vendor_links[$disk_vendor['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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
                            <button id="show-deleted-disk_type" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_type', 1)" @if (isset($disk_type['deleted_count']) && $disk_type['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_type" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_type', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_types['count'] > 0)
                    @foreach ($disk_types['rows'] as $disk_type)

                        @if ($disk_type['deleted'] == 1)
                        <tr id="disk_type-row-{{ $disk_type['id'] }}" class="align-middle red theme-divBg disk_type-deleted" hidden>
                        @else 
                        <tr id="disk_type-row-{{ $disk_type['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_type"/>
                            <input type="hidden" name="id" value="{{ $disk_type['id'] }}">
                            <td id="disk_type-{{ $disk_type['id'] }}-id" class="text-center align-middle">{{ $disk_type['id'] }}</td>
                            <td id="disk_type-{{ $disk_type['id'] }}-name" class="text-center align-middle">{{ $disk_type['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_type_links[$disk_type['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_type['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_type_links[$disk_type['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_type still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_type['deleted'] !== 1) 
                                    @if (array_key_exists($disk_type['id'], $disk_type_links) && ((int)$disk_type_links[$disk_type['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_type-{{ $disk_type['id'] }}-links" type="button" onclick="showLinks('disk_type', '{{ $disk_type['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_type['id'], $disk_type_links) && ((int)$disk_type_links[$disk_type['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_type-row-{{ $disk_type['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_type_links[$disk_type['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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
                            <button id="show-deleted-disk_speed" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_speed', 1)" @if (isset($disk_speed['deleted_count']) && $disk_speed['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_speed" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_speed', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_speeds['count'] > 0)
                    @foreach ($disk_speeds['rows'] as $disk_speed)

                        @if ($disk_speed['deleted'] == 1)
                        <tr id="disk_speed-row-{{ $disk_speed['id'] }}" class="align-middle red theme-divBg disk_speed-deleted" hidden>
                        @else 
                        <tr id="disk_speed-row-{{ $disk_speed['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_speed"/>
                            <input type="hidden" name="id" value="{{ $disk_speed['id'] }}">
                            <td id="disk_speed-{{ $disk_speed['id'] }}-id" class="text-center align-middle">{{ $disk_speed['id'] }}</td>
                            <td id="disk_speed-{{ $disk_speed['id'] }}-name" class="text-center align-middle">{{ $disk_speed['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_speed_links[$disk_speed['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_speed['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_speed_links[$disk_speed['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_speed still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_speed['deleted'] !== 1) 
                                    @if (array_key_exists($disk_speed['id'], $disk_speed_links) && ((int)$disk_speed_links[$disk_speed['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_speed-{{ $disk_speed['id'] }}-links" type="button" onclick="showLinks('disk_speed', '{{ $disk_speed['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_speed['id'], $disk_speed_links) && ((int)$disk_speed_links[$disk_speed['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_speed-row-{{ $disk_speed['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_speed_links[$disk_speed['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Capacities</h4>

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
                            <button id="show-deleted-disk_capacity" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_capacity', 1)" @if (isset($disk_capacity['deleted_count']) && $disk_capacity['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_capacity" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_capacity', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_capacities['count'] > 0)
                    @foreach ($disk_capacities['rows'] as $disk_capacity)

                        @if ($disk_capacity['deleted'] == 1)
                        <tr id="disk_capacity-row-{{ $disk_capacity['id'] }}" class="align-middle red theme-divBg disk_capacity-deleted" hidden>
                        @else 
                        <tr id="disk_capacity-row-{{ $disk_capacity['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_capacity"/>
                            <input type="hidden" name="id" value="{{ $disk_capacity['id'] }}">
                            <td id="disk_capacity-{{ $disk_capacity['id'] }}-id" class="text-center align-middle">{{ $disk_capacity['id'] }}</td>
                            <td id="disk_capacity-{{ $disk_capacity['id'] }}-name" class="text-center align-middle">{{ $disk_capacity['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_capacity_links[$disk_capacity['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_capacity['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_capacity_links[$disk_capacity['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_capacity still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_capacity['deleted'] !== 1) 
                                    @if (array_key_exists($disk_capacity['id'], $disk_capacity_links) && ((int)$disk_capacity_links[$disk_capacity['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_capacity-{{ $disk_capacity['id'] }}-links" type="button" onclick="showLinks('disk_capacity', '{{ $disk_capacity['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_capacity['id'], $disk_capacity_links) && ((int)$disk_capacity_links[$disk_capacity['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_capacity-row-{{ $disk_capacity['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_capacity_links[$disk_capacity['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">RPMs</h4>

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
                            <button id="show-deleted-disk_rpm" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_rpm', 1)" @if (isset($disk_rpm['deleted_count']) && $disk_rpm['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_rpm" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_rpm', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_rpms['count'] > 0)
                    @foreach ($disk_rpms['rows'] as $disk_rpm)

                        @if ($disk_rpm['deleted'] == 1)
                        <tr id="disk_rpm-row-{{ $disk_rpm['id'] }}" class="align-middle red theme-divBg disk_rpm-deleted" hidden>
                        @else 
                        <tr id="disk_rpm-row-{{ $disk_rpm['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_rpm"/>
                            <input type="hidden" name="id" value="{{ $disk_rpm['id'] }}">
                            <td id="disk_rpm-{{ $disk_rpm['id'] }}-id" class="text-center align-middle">{{ $disk_rpm['id'] }}</td>
                            <td id="disk_rpm-{{ $disk_rpm['id'] }}-name" class="text-center align-middle">{{ $disk_rpm['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_rpm_links[$disk_rpm['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_rpm['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_rpm_links[$disk_rpm['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_rpm still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_rpm['deleted'] !== 1) 
                                    @if (array_key_exists($disk_rpm['id'], $disk_rpm_links) && ((int)$disk_rpm_links[$disk_rpm['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_rpm-{{ $disk_rpm['id'] }}-links" type="button" onclick="showLinks('disk_rpm', '{{ $disk_rpm['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_rpm['id'], $disk_rpm_links) && ((int)$disk_rpm_links[$disk_rpm['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_rpm-row-{{ $disk_rpm['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_rpm_links[$disk_rpm['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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
                    <tr class="align-middle"><td colspan="100%">No rpms found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>


        <hr style="border-color:white; margin-left:10px"> 

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Caddys</h4>

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
                            <button id="show-deleted-disk_caddy" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('disk_caddy', 1)" @if (isset($disk_caddy['deleted_count']) && $disk_caddy['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-disk_caddy" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('disk_caddy', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($disk_caddies['count'] > 0)
                    @foreach ($disk_caddies['rows'] as $disk_caddy)

                        @if ($disk_caddy['deleted'] == 1)
                        <tr id="disk_caddy-row-{{ $disk_caddy['id'] }}" class="align-middle red theme-divBg disk_caddy-deleted" hidden>
                        @else 
                        <tr id="disk_caddy-row-{{ $disk_caddy['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="disk_caddy"/>
                            <input type="hidden" name="id" value="{{ $disk_caddy['id'] }}">
                            <td id="disk_caddy-{{ $disk_caddy['id'] }}-id" class="text-center align-middle">{{ $disk_caddy['id'] }}</td>
                            <td id="disk_caddy-{{ $disk_caddy['id'] }}-name" class="text-center align-middle">{{ $disk_caddy['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($disk_caddy_links[$disk_caddy['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$disk_caddy['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($disk_caddy_links[$disk_caddy['id']]['count'] ?? 0) !== 0) 
                                    disabled title="disk_caddy still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$disk_caddy['deleted'] !== 1) 
                                    @if (array_key_exists($disk_caddy['id'], $disk_caddy_links) && ((int)$disk_caddy_links[$disk_caddy['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="disk_caddy-{{ $disk_caddy['id'] }}-links" type="button" onclick="showLinks('disk_caddy', '{{ $disk_caddy['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($disk_caddy['id'], $disk_caddy_links) && ((int)$disk_caddy_links[$disk_caddy['id']]['count'] ?? 0) !== 0)
                        <tr id="disk_caddy-row-{{ $disk_caddy['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>Disk ID</th>
                                                <th>Disk Model</th>
                                                <th>Disk Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($disk_caddy_links[$disk_caddy['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("disks?search={{ $link['serial_number'] }}")>
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
                    <tr class="align-middle"><td colspan="100%">No caddies found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
        
    </div>
</div>