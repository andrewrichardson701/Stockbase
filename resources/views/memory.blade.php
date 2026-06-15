<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>Memory</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 ">
                <h2 class="font-semibold text-xl  leading-tight nav-div-alt headerfix">
                    Memory
                </h2>
            </div>
        </header>

        <div id="selection" class="viewport-selection" style="margin-top:20px; margin-bottom:15px">
            <div class="row centertable" style="max-width:max-content">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Site:</label>
                    <select name="site" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'site', this.value))">
                        <option value="0" @if ($params['site'] == 0) selected @endif >All</option>
                    @if ($sites['count'] > 0)
                        @foreach ($sites['rows'] as $site) 
                        <option value="{{ $site['id'] }}" @if ($params['site'] == $site['id']) selected @endif >{{ $site['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="display:inline-block;max-width:max-content">
                    <form action="" method="GET" style="display:inline-block">
                        <label class="align-middle" style="padding-top:7px;padding-right:15px;">Search:</label>
                        <span style="">
                            <input type="text" id="search" name="search" placeholder="Search" class="form-control theme-input-alt" style="display:inline !important; width:200px;padding-right:0px" @if ($params['search'] !== null) value="{{ $params['search'] }}" @endif>
                            <button id="search-submit" class="btn btn-info" style="margin-top:-3px;vertical-align:middle;padding: 6px 6px 6px 6px;opacity:80%;color:black" type="submit">
                                <i class="fa fa-search" style="padding-top:4px"></i>
                            </button>
                        </span>
                    </form>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(`{{ route('memory') }}`)">
                        <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="add-memory" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" @if($params['add_form'] == 1) hidden @endif>
                        <i class="fa fa-plus" style="padding-top:4px"></i> Add Memory
                    </button>
                    <button id="add-memory-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" @if($params['add_form'] == 0) hidden @endif>
                        Hide Add Memory
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="show-deleted-memory" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter('', 'deleted', 1))" @if ($params['deleted'] == 1) hidden @endif>
                        View Deleted
                    </button>
                    <button id="hide-deleted-memory" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter('', 'deleted', 0))" @if ($params['deleted'] == 0) hidden @endif>
                        Hide Deleted
                    </button>
                </div>
            </div>
            <!-- Memory parameter selection area -->
            <div class="row centertable" style="max-width:max-content; margin-top:10px">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">ECC Type:</label>
                    <select name="ecc_type" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'ecc_type', this.value))">
                        <option value="0" @if ($params['memory_ecc_type'] == 0) selected @endif >All</option>
                    @if ($memory_ecc_types['count'] > 0)
                        @foreach ($memory_ecc_types['rows'] as $memory_ecc_type) 
                        <option value="{{ $memory_ecc_type['id'] }}" @if ($params['memory_ecc_type'] == $memory_ecc_type['id']) selected @endif >{{ $memory_ecc_type['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Speed:</label>
                    <select name="speed" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'speed', this.value))">
                        <option value="0" @if ($params['memory_speed'] == 0) selected @endif >All</option>
                    @if ($memory_speeds['count'] > 0)
                        @foreach ($memory_speeds['rows'] as $memory_speed) 
                        <option value="{{ $memory_speed['id'] }}" @if ($params['memory_speed'] == $memory_speed['id']) selected @endif >{{ $memory_speed['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Form Factor:</label>
                    <select name="form_factor" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'form_factor', this.value))">
                        <option value="0" @if ($params['memory_form_factor'] == 0) selected @endif >All</option>
                    @if ($memory_form_factors['count'] > 0)
                        @foreach ($memory_form_factors['rows'] as $memory_form_factor) 
                        <option value="{{ $memory_form_factor['id'] }}" @if ($params['memory_form_factor'] == $memory_form_factor['id']) selected @endif >{{ $memory_form_factor['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Capacity:</label>
                    <select name="capacity" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'capacity', this.value))">
                        <option value="0" @if ($params['memory_capacity'] == 0) selected @endif >All</option>
                    @if ($memory_capacities['count'] > 0)
                        @foreach ($memory_capacities['rows'] as $memory_capacity) 
                        <option value="{{ $memory_capacity['id'] }}" @if ($params['memory_capacity'] == $memory_capacity['id']) selected @endif >{{ $memory_capacity['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Generation:</label>
                    <select name="generation" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'generation', this.value))">
                        <option value="0" @if ($params['memory_generation'] == 0) selected @endif >All</option>
                    @if ($memory_generations['count'] > 0)
                        @foreach ($memory_generations['rows'] as $memory_generation) 
                        <option value="{{ $memory_generation['id'] }}" @if ($params['memory_generation'] == $memory_generation['id']) selected @endif >{{ $memory_generation['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Vendor:</label>
                    <select name="vendor" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'vendor', this.value))">
                        <option value="0" @if ($params['memory_vendor'] == 0) selected @endif >All</option>
                    @if ($memory_vendors['count'] > 0)
                        @foreach ($memory_vendors['rows'] as $memory_vendor) 
                        <option value="{{ $memory_vendor['id'] }}" @if ($params['memory_vendor'] == $memory_vendor['id']) selected @endif >{{ $memory_vendor['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
            </div>
        <!-- End Memory parameter selection area -->
        </div>

        <!-- Add memory form section area-->
        <div class="container" id="add-memory-section" style="margin-bottom:20px" @if($params['add_form'] == 0) hidden @endif>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new memory</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="memory-add-response" hidden></p>
                <form id="add-memory-form" action="{{ route('memory.add') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <div class="row" style="margin-right:25px;margin-top:5px">
                        <div class="col">
                            <div>Serial Number</div>
                            <div><input class="form-control text-center theme-input" type="text" id="serial" name="serial" style="min-width:120px" placeholder="Serial" oninput="searchSerial(this.value)"/></div>
                        </div>
                        <div class="col">
                            <div>Vendor</div>
                            <div>
                                <select id="memory_vendor-select" name="vendor" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_vendors['count'] > 0)
                                    <option value="" @if ($params['form_vendor'] == 0) selected @endif >Select Vendor</option>
                                    @foreach ($memory_vendors['rows'] as $memory_vendor) 
                                    <option value="{{ $memory_vendor['id'] }}" @if ($params['form_vendor'] == $memory_vendor['id']) selected @endif >{{ $memory_vendor['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Vendors Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewVendor()">Add New</label>
                            </div>
                        </div>
                        <div class="col">
                            <div>Model</div>
                            <div>
                                <input class="form-control text-center theme-input" id="model" type="text" list="names" name="model" placeholder="Model" style="min-width:120px" @if($params['form_model'] !== null) value="{{ $params['form_model'] }}" @endif required/>
                                <datalist id="names">
                                @if ($memory_models['count'] > 0)
                                    @foreach ($memory_models['rows'] as $memory_model) 
                                    <option value="{{ $memory_model['model'] }}">{{ $memory_model['model'] }}</option>
                                    @endforeach
                                @endif
                                </datalist>
                            </div>
                        </div>
                        <div class="col">
                            <div>Generation</div>
                            <div>
                                <select id="memory_generation-select" name="generation" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_generations['count'] > 0)
                                    <option value="" @if ($params['form_generation'] == 0) selected @endif >Select Gen.</option>
                                    @foreach ($memory_generations['rows'] as $memory_generation) 
                                    <option value="{{ $memory_generation['id'] }}" @if ($params['form_generation'] == $memory_generation['id']) selected @endif >{{ $memory_generation['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Generations Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewGeneration()">Add New</label>
                            </div>
                        </div>
                        <div class="col">
                            <div>ECC Type</div>
                            <div>
                                <select id="memory_type-select" name="ecc_type" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_ecc_types['count'] > 0)
                                    <option value="" @if ($params['form_ecc_type'] == 0) selected @endif >Select Type</option>
                                    @foreach ($memory_ecc_types['rows'] as $memory_ecc_type) 
                                    <option value="{{ $memory_ecc_type['id'] }}" @if ($params['form_ecc_type'] == $memory_ecc_type['id']) selected @endif >{{ $memory_ecc_type['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Types Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewEccType()">Add New</label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Capacity</div>
                            <div>
                                <select id="memory_capacity-select" name="capacity" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_capacities['count'] > 0)
                                    <option value="" @if ($params['form_capacity'] == 0) selected @endif >Select Capacity</option>
                                    @foreach ($memory_capacities['rows'] as $memory_capacity) 
                                    <option value="{{ $memory_capacity['id'] }}" @if ($params['form_capacity'] == $memory_capacity['id']) selected @endif >{{ $memory_capacity['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Capacities Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewCapacity()">Add New</label>
                            </div>
                        </div>
                        <div class="col">
                            <div>Form Factor</div>
                            <div>
                                <select id="memory_form_factor-select" name="form_factor" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_form_factors['count'] > 0)
                                    <option value="" @if ($params['form_form_factor'] == 0) selected @endif >Select Form Factor</option>
                                    @foreach ($memory_form_factors['rows'] as $memory_form_factor) 
                                    <option value="{{ $memory_form_factor['id'] }}" @if ($params['form_form_factor'] == $memory_form_factor['id']) selected @endif >{{ $memory_form_factor['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Form Factors Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewFormFactor()">Add New</label>
                            </div>
                        </div>
                        <div class="col">
                            <div>Speed</div>
                            <div>
                                <select id="memory_speed-select" name="speed" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($memory_speeds['count'] > 0)
                                    <option value="" @if ($params['form_speed'] == 0) selected @endif >Select Speed</option>
                                    @foreach ($memory_speeds['rows'] as $memory_speed) 
                                    <option value="{{ $memory_speed['id'] }}" @if ($params['form_speed'] == $memory_speed['id']) selected @endif >{{ $memory_speed['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Speeds Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewSpeed()">Add New</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="site-add_memory" name="site" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($sites['count'] > 0)
                                    <option value="" @if ($params['form_site'] == 0) selected @endif >Select Site</option>
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}" @if ($params['form_site'] == $site['id']) selected @endif >{{ $site['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Sites Found</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Area</div>
                            <div>
                                <select id="area-add_memory" name="area" class="form-control text-center theme-dropdown" style="border-color:black;"  @if ($params['form_area'] == 0) disabled @endif required>
                                @if ($areas['count'] > 0)
                                    <option value="" @if ($params['form_area'] == 0) selected @endif disabled>Select Area</option>
                                    @foreach ($areas['rows'] as $area) 
                                    <option value="{{ $area['id'] }}" @if ($params['form_area'] == $area['id']) selected @endif >{{ $area['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Areas Found</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Shelf</div>
                            <div>
                                <select id="shelf-add_memory" name="shelf" class="form-control text-center theme-dropdown" style="border-color:black;" @if ($params['form_shelf'] == 0) disabled @endif required>
                                @if ($shelves['count'] > 0)
                                    <option value="" @if ($params['form_shelf'] == 0) selected @endif disabled>Select Shelf</option>
                                    @foreach ($shelves['rows'] as $shelf) 
                                    <option value="{{ $shelf['id'] }}" @if ($params['form_shelf'] == $shelf['id']) selected @endif >{{ $shelf['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Shelves Found</option>
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row align-middle" style="margin-right:25px">
                        <div class="col-sm" style="margin-top:10px"> 
                        </div>
                        <div class="col" style="margin-top:10px">
                            <button id="memory-add-single" class="btn btn-success align-bottom" type="submit" name="add-memory-submit" style="" value="1">Add</button>
                            <button id="memory-add-multiple" class="btn btn-success align-bottom" type="submit" name="add-memory-submit-multiple" style="margin-left:20px" value="2">Add Multiple</button>
                        </div>
                        <div class="col-sm text-right" style="margin-top:10px">
                            <a href="memory-import.php" class="link" style="font-size:12px; padding-bottom:10px" hidden>Import from CSV</a>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    <!-- End Add memory form section area-->

        <div class="container">
            <div class="container">
                <hr class="viewport-hr" style="border-color:#9f9d9d; margin-left:10px">
                <div class="row centertable">
                    <div class="col-3 float-left viewport-font" >
                        Count: <or class="green">{{ $memory_data['total_count'] }}</or>
                    </div>
                    <div class="col">
                        @include('includes.response-handling')
                    </div>
                    <div class="col align-middle viewport-padding-0-lr" style="max-width:max-content;white-space: nowrap;padding-bottom:10px">
                        <table class="viewport-font viewport-table">
                            <tr class="align-middle">
                                <td class="align-middle" style="padding-right:10px">
                                    Sort By:
                                </td>
                                <td class="align-middle">
                                    <select name="sort" class="form-control row-dropdown viewport-width-50" style="width:max-content;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'sort', this.value))">
                                        <option value="ecc_type" @if($params['sort'] == "ecc_type" || $params['sort'] == null) selected @endif>ECC Type</option>
                                        <option value="speed" @if($params['sort'] == "speed") selected @endif>Speed</option>
                                        <option value="generation" @if($params['sort'] == "generation") selected @endif>Generation</option>
                                        <option value="capacity" @if($params['sort'] == "capacity") selected @endif>Capacity</option>
                                        <option value="vendor" @if($params['sort'] == "vendor") selected @endif>Vendor</option>
                                        <option value="model" @if($params['sort'] == "model") selected @endif>Model</option>
                                        <option value="serial" @if($params['sort'] == "serial") selected @endif>Serial</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="memory-table" class="text-center" style="max-width:max-content; margin:auto">
                <table id="table-memory" class="table table-dark theme-table centertable" style="max-width:max-content;padding-bottom:0px;margin-bottom:0px;">
                    <thead>
                        <tr class="theme-tableOuter align-middle text-center">
                            <th hidden>ID</th>
                            <th>Vendor</th>
                            <th>Model</th>
                            <th>Serial Number</th>
                            <th>Generation</th>
                            <th>Capacity</th>
                            <th>Speed</th>
                            <th>ECC Type</th>
                            <th>Form Factor</th>
                            <th>Location</th>
                            <th colspan=2></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($memory_data['count'] == 0)
                        <tr>
                            <td colspan=100% class="text-center align-middle">No Memory Found.</td>
                        </tr>
                    @else
                        @foreach($memory_data['rows'] as $row)
                        <tr class="row-show align-middle text-center  @if($row['deleted'] == 1) red @endif" id="memory-{{ $row['id'] }}">
                            <form id="memoryForm-{{ $row['id'] }}" action="{{ route('memory.restore') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" form="memoryForm-{{ $row['id'] }}" value="{{ $row['id'] }}" name="id"/>
                            </form>
                            <td class="align-middle" hidden>{{ $row['id'] }}</td>
                            <td class="align-middle">{{ $memory_vendors['rows'][$row['vendor_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $row['model'] }}</td>
                            <td class="align-middle" id="memory-serial-{{ $row['id'] }}">{{ $row['serial_number'] }}</td>
                            <td class="align-middle">{{ $memory_generations['rows'][$row['generation_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $memory_capacities['rows'][$row['capacity_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $memory_speeds['rows'][$row['speed_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $memory_ecc_types['rows'][$row['ecc_type_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $memory_form_factors['rows'][$row['form_factor_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">
                                <or class="gold link" onclick="navPage(updateQueryParameter('', 'site', {{ $areas['rows'][$shelves['rows'][$row['shelf_id']]['area_id']]['site_id'] }}))">
                                    {{ $sites['rows'][$areas['rows'][$shelves['rows'][$row['shelf_id']]['area_id']]['site_id']]['name'] }}
                                </or>, 
                                <or class="gold link" onclick="navPage(updateQueryParameter('', 'area', {{ $shelves['rows'][$row['shelf_id']]['area_id'] }}))">
                                    {{ $areas['rows'][$shelves['rows'][$row['shelf_id']]['area_id']]['name'] }}
                                </or>,
                                <or class="gold link" onclick="navPage(updateQueryParameter('', 'shelf', {{ $row['shelf_id'] }}))">
                                    {{ $shelves['rows'][$row['shelf_id']]['name'] }}
                                </or>
                            </td>
                            <td class="align-middle" style="padding-right:5px">
                                <button id="move-btn-{{ $row['id'] }}" class="btn btn-info" style="padding-left:10px;padding-right:10px" type="button" value="edit" title="Edit?" onclick="modalLoadEditMemory('{{ $row['id'] }}')">
                                    <i class="fa fa-pencil" style="color:white"></i>
                                </button>
                            </td>
                            <td class="align-middle" style="padding-left:5px">
                            @if ($row['deleted'] == 1) 
                                <button class="btn btn-success" type="submit" form="memoryForm-{{ $row['id'] }}" name="memory-restore-submit" value="1" title="Restore?">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @else 
                                <button class="btn btn-danger" type="button" value="1" title="Delete?" onclick="modalLoadDeleteMemory('{{ $row['id'] }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>  
                </table>
                <table class="table table-dark theme-table centertable">
                    <tbody>
                        <tr class="theme-tableOuter">
                            <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                            <div class="row">
                                <div class="col text-center"></div>
                                <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                                @if ($memory_data['pages'] > 1 && $memory_data['pages'] <=15)
                                    @if ($memory_data['page'] > 1)
                                        <or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $memory_data['page']-1 }}') + '')"><</or>
                                    @endif
                                    @if ($memory_data['pages'] > 5)
                                        @for ($i = 1; $i <= $memory_data['pages']; $i++)
                                            @if ($i == $memory_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @elseif ($i == 1 && $memory_data['page'] > 5)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                                            @elseif ($i < $memory_data['page'] && $i >= $memory_data['page']-2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i > $memory_data['page'] && $i <= $memory_data['page']+2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i == $memory_data['pages'])
                                                <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= $memory_data['pages']; $i++)
                                            @if ($i == $memory_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @else
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @endif

                                    @if ($memory_data['page'] < $memory_data['pages'])
                                        <or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $memory_data['page'] + 1}}') + '')">></or>
                                    @endif
                                        @if (isset($memory_data['view']) && $memory_data['view'] !== 'memory_data')
                                            &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('memory_data') }}/{{ $params['stock_id'] }}')">view all</or>
                                        @endif
                                @else 
                                    <form style="margin-bottom:0px">
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'page', document.getElementById('page-select').value))" name="page">
                                                        @for ($i = 1; $i <= $memory_data['pages']; $i++) 
                                                            <option value="{{ $i }}" @if ($i == $memory_data['page']) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                        </select>
                                                    </td>
                                                    @if (isset($memory_data['view']) && $memory_data['view'] !== 'memory_data')
                                                    <td><or class="specialColor clickable" onclick="navPage('{{ url('memory_data') }}/{{ $params['stock_id'] }}')">view all</or></td>
                                                    @endif
                                                <tr>
                                            </tbody>
                                        </table>        
                                    </form>
                                @endif
                                </div>
                                <div class="col text-center">
                                    <table style="margin-left:auto; margin-right:20px">
                                        <tbody>
                                            <tr>
                                                <td class="theme-textColor align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                    Rows: 
                                                </td>
                                                <td class="align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                    <select id="tableRowCount" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" name="rows" onchange="navPage(updateQueryParameter('', 'rows', this.value))">
                                                        <option id="rows-10"  value="10" @if($params['rows'] == 10) selected @endif>10</option>
                                                        <option id="rows-20"  value="20" @if($params['rows'] == 20) selected @endif>20</option>
                                                        <option id="rows-50"  value="50" @if($params['rows'] == 50) selected @endif>50</option>
                                                        <option id="rows-100" value="100" @if($params['rows'] == 100) selected @endif>100</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </tr>
                    </tbody>
                </table>
                {{-- @dd(get_defined_vars()) --}}
        </div>

    </div>

    @include('includes.assets.memory-modals')

    <!-- Add the JS for the file -->
    <script src={{ asset('js/memory.js') }}></script>

    @include('foot')
</body>
