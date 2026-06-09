<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>CPUs</title>
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
                    CPUs
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
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(`{{ route('cpus') }}`)">
                        <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="add-cpu" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" @if($params['add_form'] == 1) hidden @endif>
                        <i class="fa fa-plus" style="padding-top:4px"></i> Add CPU
                    </button>
                    <button id="add-cpu-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" @if($params['add_form'] == 0) hidden @endif>
                        Hide Add CPU
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="show-deleted-cpus" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter('', 'deleted', 1))" @if ($params['deleted'] == 1) hidden @endif>
                        View Deleted
                    </button>
                    <button id="hide-deleted-cpus" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter('', 'deleted', 0))" @if ($params['deleted'] == 0) hidden @endif>
                        Hide Deleted
                    </button>
                </div>
            </div>
            <!-- CPU parameter selection area -->
            <div class="row centertable" style="max-width:max-content; margin-top:10px">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Vendor:</label>
                    <select name="vendor" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'vendor', this.value))">
                        <option value="0" @if ($params['cpu_vendor'] == 0) selected @endif >All</option>
                    @if ($cpu_vendors['count'] > 0)
                        @foreach ($cpu_vendors['rows'] as $cpu_vendor) 
                        <option value="{{ $cpu_vendor['id'] }}" @if ($params['cpu_vendor'] == $cpu_vendor['id']) selected @endif >{{ $cpu_vendor['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Model:</label>
                    <select name="model" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'model', this.value))">
                        <option value="0" @if ($params['cpu_model'] == 0) selected @endif >All</option>
                    @if ($cpu_models['count'] > 0)
                        @foreach ($cpu_models['rows'] as $cpu_model) 
                        <option value="{{ $cpu_model['id'] }}" @if ($params['cpu_model'] == $cpu_model['id']) selected @endif >{{ $cpu_model['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Socket:</label>
                    <select name="socket" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'socket', this.value))">
                        <option value="0" @if ($params['cpu_socket'] == 0) selected @endif >All</option>
                    @if ($cpu_sockets['count'] > 0)
                        @foreach ($cpu_sockets['rows'] as $cpu_socket) 
                        <option value="{{ $cpu_socket['socket'] }}" @if ($params['cpu_socket'] == $cpu_socket['socket']) selected @endif >{{ $cpu_socket['socket'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">CPU Family:</label>
                    <select name="cpu_family" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'cpu_family', this.value))">
                        <option value="0" @if ($params['cpu_family'] == 0) selected @endif >All</option>
                    @if ($cpu_families['count'] > 0)
                        @foreach ($cpu_families['rows'] as $cpu_family) 
                        <option value="{{ $cpu_family['cpu_family'] }}" @if ($params['cpu_family'] == $cpu_family['cpu_family']) selected @endif >{{ $cpu_family['cpu_family'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Core Count:</label>
                    <select name="core_count" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'ssd', this.value))">
                        <option value="0" @if ($params['cpu_core_count'] == 0) selected @endif >All</option>
                    @if ($cpu_core_counts['count'] > 0)
                        @foreach ($cpu_core_counts['rows'] as $cpu_core_count) 
                        <option value="{{ $cpu_core_count['core_count']}}" @if ($params['cpu_core_count'] == $cpu_core_count['core_count']) selected @endif >{{ $cpu_core_count['core_count'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Clock Speed:</label>
                    <select name="clock_speed" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'clock_speed', this.value))">
                        <option value="0" @if ($params['cpu_clock_speed'] == 0) selected @endif >All</option>
                    @if ($cpu_clock_speeds['count'] > 0)
                        @foreach ($cpu_clock_speeds['rows'] as $cpu_clock_speed) 
                        <option value="{{ $cpu_clock_speed['clock_speed'] }}" @if ($params['cpu_clock_speed'] == $cpu_clock_speed['clock_speed']) selected @endif >{{ $cpu_clock_speed['clock_speed'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
            </div>
        <!-- End CPU parameter selection area -->
        </div>

        <!-- Add cpu form section area-->
        <div class="container" id="add-cpu-section" style="margin-bottom:20px" @if($params['add_form'] == 0) hidden @endif>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new cpu</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="cpu-add-response" hidden></p>
                <form id="add-cpu-form" action="{{ route('cpus.add') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
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
                                <select id="cpu_vendor-select" name="vendor" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($cpu_vendors['count'] > 0)
                                    <option value="" @if ($params['form_vendor'] == 0) selected @endif >Select Vendor</option>
                                    @foreach ($cpu_vendors['rows'] as $cpu_vendor) 
                                    <option value="{{ $cpu_vendor['id'] }}" @if ($params['form_vendor'] == $cpu_vendor['id']) selected @endif >{{ $cpu_vendor['name'] }}</option>
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
                                <select id="cpu_model-select" name="model" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($cpu_models['count'] > 0)
                                    <option value="" @if ($params['form_model'] == 0) selected @endif >Select Model</option>
                                    @foreach ($cpu_models['rows'] as $cpu_model) 
                                    <option value="{{ $cpu_model['id'] }}" @if ($params['form_model'] == $cpu_model['id']) selected @endif >{{ $cpu_model['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Models Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewModel()">Add New</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="site-add_cpu" name="site" class="form-control text-center theme-dropdown" style="border-color:black;" required>
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
                                <select id="area-add_cpu" name="area" class="form-control text-center theme-dropdown" style="border-color:black;"  @if ($params['form_area'] == 0) disabled @endif required>
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
                                <select id="shelf-add_cpu" name="shelf" class="form-control text-center theme-dropdown" style="border-color:black;" @if ($params['form_shelf'] == 0) disabled @endif required>
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
                            <button id="cpu-add-single" class="btn btn-success align-bottom" type="submit" name="add-cpu-submit" style="" value="1">Add</button>
                        </div>
                        <div class="col-sm text-right" style="margin-top:10px">
                            <a href="cpu-import.php" class="link" style="font-size:12px; padding-bottom:10px" hidden>Import from CSV</a>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    <!-- End Add cpu form section area-->

        <div class="container">
            <div class="container">
                <hr class="viewport-hr" style="border-color:#9f9d9d; margin-left:10px">
                <div class="row centertable">
                    <div class="col-3 float-left viewport-font" >
                        Count: <or class="green">{{ $cpus_data['total_count'] }}</or>
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
                                        <option value="socket" @if($params['sort'] == "socket" || $params['sort'] == null) selected @endif>Socket</option>
                                        <option value="clock_speed" @if($params['sort'] == "clock_speed") selected @endif>Clock Speed</option>
                                        <option value="core_count" @if($params['sort'] == "core_count") selected @endif>Core Count</option>
                                        <option value="cpu_family" @if($params['sort'] == "cpu_family") selected @endif>CPU Family</option>
                                        <option value="model" @if($params['sort'] == "model") selected @endif>Model</option>
                                        <option value="vendor" @if($params['sort'] == "vendor") selected @endif>Vendor</option>
                                        <option value="serial" @if($params['sort'] == "serial") selected @endif>Serial</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cpus-table" class="text-center" style="max-width:max-content; margin:auto">
                <table id="table-cpus" class="table table-dark theme-table centertable" style="max-width:max-content;padding-bottom:0px;margin-bottom:0px;">
                    <thead>
                        <tr class="theme-tableOuter align-middle text-center">
                            <th hidden>ID</th>
                            <th>Vendor</th>
                            <th>Model</th>
                            <th>Socket</th>
                            <th>CPU Family</th>
                            <th>Core Count</th>
                            <th>Clock Speed</th>
                            <th>Serial Number</th>
                            <th>Location</th>
                            <th colspan=2></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($cpus_data['count'] == 0)
                        <tr>
                            <td colspan=100% class="text-center align-middle">No CPUs Found.</td>
                        </tr>
                    @else
                        @foreach($cpus_data['rows'] as $row)
                        <tr class="row-show align-middle text-center  @if($row['deleted'] == 1) red @endif" id="cpu-{{ $row['id'] }}">
                            <form id="cpuForm-{{ $row['id'] }}" action="{{ route('cpus.restore') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" form="cpuForm-{{ $row['id'] }}" value="{{ $row['id'] }}" name="id"/>
                            </form>
                            <td class="align-middle" hidden>{{ $row['id'] }}</td>
                            <td class="align-middle">{{ $cpu_vendors['rows'][$row['vendor_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $cpu_models['rows'][$row['model_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $row['socket'] }}</td>
                            <td class="align-middle">{{ $row['cpu_family'] }}</td>
                            <td class="align-middle">{{ $row['core_count'] }}</td>
                            <td class="align-middle">{{ $row['clock_speed'] }}</td>
                            <td class="align-middle" id="cpu-serial-{{ $row['id'] }}">{{ $row['serial_number'] }}</td>
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
                                <button id="move-btn-{{ $row['id'] }}" class="btn btn-info" style="padding-left:10px;padding-right:10px" type="button" value="move" title="Edit?" onclick="modalLoadEditCpu('{{ $row['id'] }}')">
                                    <i class="fa fa-pencil" style="color:white"></i>
                                </button>
                            </td>
                            <td class="align-middle" style="padding-left:5px">
                            @if ($row['deleted'] == 1) 
                                <button class="btn btn-success" type="submit" form="cpuForm-{{ $row['id'] }}" name="cpu-restore-submit" value="1" title="Restore?">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @else 
                                <button class="btn btn-danger" type="button" value="1" title="Delete?" onclick="modalLoadDeleteCPU('{{ $row['id'] }}')">
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
                                @if ($cpus_data['pages'] > 1 && $cpus_data['pages'] <=15)
                                    @if ($cpus_data['page'] > 1)
                                        <or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $cpus_data['page']-1 }}') + '')"><</or>
                                    @endif
                                    @if ($cpus_data['pages'] > 5)
                                        @for ($i = 1; $i <= $cpus_data['pages']; $i++)
                                            @if ($i == $cpus_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @elseif ($i == 1 && $cpus_data['page'] > 5)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                                            @elseif ($i < $cpus_data['page'] && $i >= $cpus_data['page']-2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i > $cpus_data['page'] && $i <= $cpus_data['page']+2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i == $cpus_data['pages'])
                                                <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= $cpus_data['pages']; $i++)
                                            @if ($i == $cpus_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @else
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @endif

                                    @if ($cpus_data['page'] < $cpus_data['pages'])
                                        <or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $cpus_data['page'] + 1}}') + '')">></or>
                                    @endif
                                        @if (isset($cpus_data['view']) && $cpus_data['view'] !== 'cpus_data')
                                            &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('cpus_data') }}/{{ $params['stock_id'] }}')">view all</or>
                                        @endif
                                @else 
                                    <form style="margin-bottom:0px">
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'page', document.getElementById('page-select').value))" name="page">
                                                        @for ($i = 1; $i <= $cpus_data['pages']; $i++) 
                                                            <option value="{{ $i }}" @if ($i == $cpus_data['page']) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                        </select>
                                                    </td>
                                                    @if (isset($cpus_data['view']) && $cpus_data['view'] !== 'cpus_data')
                                                    <td><or class="specialColor clickable" onclick="navPage('{{ url('cpus_data') }}/{{ $params['stock_id'] }}')">view all</or></td>
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

    @include('includes.assets.cpus-modals')

    <!-- Add the JS for the file -->
    <script src={{ asset('js/cpus.js') }}></script>

    @include('foot')
</body>
