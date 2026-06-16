<div class="container-fluid" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="cpuattributemanagement-settings">CPU Attribute Management</h3> 
    <!-- CPU Attribute Management Settings -->
    <div class="adminContent" id="cpuattributemanagement">

        @include('includes.response-handling', ['section' => 'cpuattributemanagement-settings'])

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
                            <button id="show-deleted-cpu_vendor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('cpu_vendor', 1)" @if (isset($cpu_vendor['deleted_count']) && $cpu_vendor['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-cpu_vendor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('cpu_vendor', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($cpu_vendors['count'] > 0)
                    @foreach ($cpu_vendors['rows'] as $cpu_vendor)

                        @if ($cpu_vendor['deleted'] == 1)
                        <tr id="cpu_vendor-row-{{ $cpu_vendor['id'] }}" class="align-middle red theme-divBg cpu_vendor-deleted" hidden>
                        @else 
                        <tr id="cpu_vendor-row-{{ $cpu_vendor['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="cpu_vendor"/>
                            <input type="hidden" name="id" value="{{ $cpu_vendor['id'] }}">
                            <td id="cpu_vendor-{{ $cpu_vendor['id'] }}-id" class="text-center align-middle">{{ $cpu_vendor['id'] }}</td>
                            <td id="cpu_vendor-{{ $cpu_vendor['id'] }}-name" class="text-center align-middle">{{ $cpu_vendor['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($cpu_vendor_links[$cpu_vendor['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$cpu_vendor['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($cpu_vendor_links[$cpu_vendor['id']]['count'] ?? 0) !== 0) 
                                    disabled title="cpu_vendor still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$cpu_vendor['deleted'] !== 1) 
                                    @if (array_key_exists($cpu_vendor['id'], $cpu_vendor_links) && ((int)$cpu_vendor_links[$cpu_vendor['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="cpu_vendor-{{ $cpu_vendor['id'] }}-links" type="button" onclick="showLinks('cpu_vendor', '{{ $cpu_vendor['id'] }}')">Show Links</button>
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($cpu_vendor['id'], $cpu_vendor_links) && ((int)$cpu_vendor_links[$cpu_vendor['id']]['count'] ?? 0) !== 0)
                        <tr id="cpu_vendor-row-{{ $cpu_vendor['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>CPU ID</th>
                                                <th>CPU Model</th>
                                                <th>CPU Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cpu_vendor_links[$cpu_vendor['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("cpus?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $cpu_models['rows'][$link['model_id']]['name'] ?? '' }}</td>
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

        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Models</h4>
   
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
                            <button id="show-deleted-cpu_model" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('cpu_model', 1)" @if (isset($cpu_model['deleted_count']) && $cpu_model['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-cpu_model" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('cpu_model', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($cpu_models['count'] > 0)
                    @foreach ($cpu_models['rows'] as $cpu_model)

                        @if ($cpu_model['deleted'] == 1)
                        <tr id="cpu_model-row-{{ $cpu_model['id'] }}" class="align-middle red theme-divBg cpu_model-deleted" hidden>
                        @else 
                        <tr id="cpu_model-row-{{ $cpu_model['id'] }}" class="align-middle">
                        @endif
                        <form enctype="multipart/form-data" action="{{ route('admin.attributeSettings') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="cpu_model"/>
                            <input type="hidden" name="id" value="{{ $cpu_model['id'] }}">
                            <td id="cpu_model-{{ $cpu_model['id'] }}-id" class="text-center align-middle">{{ $cpu_model['id'] }}</td>
                            <td id="cpu_model-{{ $cpu_model['id'] }}-name" class="text-center align-middle">{{ $cpu_model['name'] }}</td>
                            <td class="text-center align-middle">{{ (int)($cpu_model_links[$cpu_model['id']]['count'] ?? 0) }}</td>
                            <td class="text-center align-middle">
                            @if ((int)$cpu_model['deleted'] == 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                @if (($cpu_model_links[$cpu_model['id']]['count'] ?? 0) !== 0) 
                                    disabled title="cpu_model still linked to stock. Remove these links before deleting."
                                @endif
                                ><i class="fa fa-trash"></i></button></td>
                            @else 
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                            @endif
                            <td class="text-center align-middle">
                                @if ((int)$cpu_model['deleted'] !== 1) 
                                    @if (array_key_exists($cpu_model['id'], $cpu_model_links) && ((int)$cpu_model_links[$cpu_model['id']]['count'] ?? 0) !== 0) 
                                        <button class="btn btn-warning" id="cpu_model-{{ $cpu_model['id'] }}-links" type="button" onclick="showLinks('cpu_model', '{{ $cpu_model['id'] }}')">Show Links</button> 
                                    @endif
                                @else 
                                    <or class="green">Restore?</or>
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($cpu_model['id'], $cpu_model_links) && ((int)$cpu_model_links[$cpu_model['id']]['count'] ?? 0) !== 0)
                        <tr id="cpu_model-row-{{ $cpu_model['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>CPU ID</th>
                                                <th>CPU Model</th>
                                                <th>CPU Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cpu_model_links[$cpu_model['id']]['rows'] as $link)
                                                <tr class="clickable" onclick=navPage("cpus?search={{ $link['serial_number'] }}")>
                                                    <td class="text-center">{{ $link['id'] }}</td>
                                                    <td class="text-center">{{ $cpu_models['rows'][$link['model_id']]['name'] ?? '' }}</td>
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
                    <tr class="align-middle"><td colspan="100%">No models found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>        
    </div>
</div>