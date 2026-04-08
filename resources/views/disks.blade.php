<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>Disks</title>
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
                    Disks
                </h2>
            </div>
        </header>
        @include('includes.response-handling')

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
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(`{{ route('disks') }}`)">
                        <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="add-disk" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" @if($params['add_form'] == 1) hidden @endif>
                        <i class="fa fa-plus" style="padding-top:4px"></i> Add Disk
                    </button>
                    <button id="add-disk-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" @if($params['add_form'] == 0) hidden @endif>
                        Hide Add Disk
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="show-deleted-disks" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter('', 'deleted', 1))" @if ($params['deleted'] == 1) hidden @endif>
                        View Deleted
                    </button>
                    <button id="hide-deleted-disks" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter('', 'deleted', 0))" @if ($params['deleted'] == 0) hidden @endif>
                        Hide Deleted
                    </button>
                </div>
            </div>
            <!-- Disk parameter selection area -->
            <div class="row centertable" style="max-width:max-content; margin-top:10px">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Type:</label>
                    <select name="type" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'type', this.value))">
                        <option value="0" @if ($params['disk_type'] == 0) selected @endif >All</option>
                    @if ($disk_types['count'] > 0)
                        @foreach ($disk_types['rows'] as $disk_type) 
                        <option value="{{ $disk_type['id'] }}" @if ($params['disk_type'] == $disk_type['id']) selected @endif >{{ $disk_type['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Speed:</label>
                    <select name="speed" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'speed', this.value))">
                        <option value="0" @if ($params['disk_speed'] == 0) selected @endif >All</option>
                    @if ($disk_speeds['count'] > 0)
                        @foreach ($disk_speeds['rows'] as $disk_speed) 
                        <option value="{{ $disk_speed['id'] }}" @if ($params['disk_speed'] == $disk_speed['id']) selected @endif >{{ $disk_speed['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Capacity:</label>
                    <select name="capacity" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'capacity', this.value))">
                        <option value="0" @if ($params['disk_capacity'] == 0) selected @endif >All</option>
                    @if ($disk_capacities['count'] > 0)
                        @foreach ($disk_capacities['rows'] as $disk_capacity) 
                        <option value="{{ $disk_capacity['id'] }}" @if ($params['disk_capacity'] == $disk_capacity['id']) selected @endif >{{ $disk_capacity['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">HDD/SSD:</label>
                    <select name="ssd" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'ssd', this.value))">
                        <option value="" @if ($params['disk_ssd'] == "") selected @endif >All</option>
                        <option value=0 @if ($params['disk_ssd'] === "0") selected @endif >HDD</option>
                        <option value=1 @if ($params['disk_ssd'] === "1") selected @endif >SSD</option>
                    </select>
                </div>
            </div>
            <div class="row centertable" style="max-width:max-content; margin-top:10px">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Form Factor:</label>
                    <select name="form_factor" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'form_factor', this.value))">
                        <option value="0" @if ($params['disk_form_factor'] == 0) selected @endif >All</option>
                        <option value="3.5" @if ($params['disk_form_factor'] == "3.5") selected @endif >3.5"</option>
                        <option value="2.5" @if ($params['disk_form_factor'] == "2.5") selected @endif >2.5"</option>
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Caddy:</label>
                    <select name="caddy" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'caddy', this.value))">
                        <option value="0" @if ($params['disk_caddy'] == 0) selected @endif >All</option>
                    @if ($disk_caddies['count'] > 0)
                        @foreach ($disk_caddies['rows'] as $disk_caddy) 
                        <option value="{{ $disk_caddy['id'] }}" @if ($params['disk_caddy'] == $disk_caddy['id']) selected @endif >{{ $disk_caddy['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Vendor:</label>
                    <select name="vendor" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'vendor', this.value))">
                        <option value="0" @if ($params['disk_vendor'] == 0) selected @endif >All</option>
                    @if ($disk_vendors['count'] > 0)
                        @foreach ($disk_vendors['rows'] as $disk_vendor) 
                        <option value="{{ $disk_vendor['id'] }}" @if ($params['disk_vendor'] == $disk_vendor['id']) selected @endif >{{ $disk_vendor['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Destroy:</label>
                    <select name="destroy" class="form-control theme-dropdown-alt" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'destroy', this.value))">
                        <option value="" @if ($params['disk_destroy'] == '') selected @endif >All</option>
                        <option value=0 @if ($params['disk_destroy'] == 0) selected @endif >No</option>
                        <option value="1" @if ($params['disk_destroy'] == 1) selected @endif class="red">SHRED</option>
                    </select>
                </div>
            </div>
        <!-- End Disk parameter selection area -->
        </div>

        <!-- Add disk form section area-->
        <div class="container" id="add-disk-section" style="margin-bottom:20px" @if($params['add_form'] == 0) hidden @endif>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new disk</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="disk-add-response" hidden></p>
                <form id="add-disk-form" action="{{ route('disks.add') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <div class="row" style="margin-right:25px;margin-top:5px">
                        <div class="col">
                            <div>Serial Number</div>
                            <div><input class="form-control text-center theme-input" type="text" id="serial" name="serial" style="min-width:120px" placeholder="Serial" oninput="searchSerial(this.value)" required/></div>
                        </div>
                        <div class="col">
                            <div>Vendor</div>
                            <div>
                                <select id="disk_vendor-select" name="vendor" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($disk_vendors['count'] > 0)
                                    <option value="" @if ($params['form_vendor'] == 0) selected @endif >Select Vendor</option>
                                    @foreach ($disk_vendors['rows'] as $disk_vendor) 
                                    <option value="{{ $disk_vendor['id'] }}" @if ($params['form_vendor'] == $disk_vendor['id']) selected @endif >{{ $disk_vendor['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Vendors Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewVendor()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Model</div>
                            <div>
                                <input class="form-control text-center theme-input" id="model" type="text" list="names" name="model" placeholder="Model" style="min-width:120px" @if($params['form_model'] !== null) value="{{ $params['form_model'] }}" @endif required/>
                                <datalist id="names">
                                @if ($disk_models['count'] > 0)
                                    @foreach ($disk_models['rows'] as $disk_model) 
                                    <option value="{{ $disk_model['model'] }}">{{ $disk_model['model'] }}</option>
                                    @endforeach
                                @endif
                                </datalist>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div>Type</div>
                            <div>
                                <select id="disk_type-select" name="type" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($disk_types['count'] > 0)
                                    <option value="" @if ($params['form_type'] == 0) selected @endif >Select Type</option>
                                    @foreach ($disk_types['rows'] as $disk_type) 
                                    <option value="{{ $disk_type['id'] }}" @if ($params['form_type'] == $disk_type['id']) selected @endif >{{ $disk_type['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Types Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewType()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Capacity</div>
                            <div>
                                <select id="disk_capacity-select" name="capacity" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($disk_capacities['count'] > 0)
                                    <option value="" @if ($params['form_capacity'] == 0) selected @endif >Select Capacity</option>
                                    @foreach ($disk_capacities['rows'] as $disk_capacity) 
                                    <option value="{{ $disk_capacity['id'] }}" @if ($params['form_capacity'] == $disk_capacity['id']) selected @endif >{{ $disk_capacity['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Capacities Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewCapacity()">Add New</a>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>HDD/SSD</div>
                            <div>
                                <select id="disk_ssd-select" name="ssd" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                    <option value="" @if ($params['form_ssd'] == null) selected @endif >Select HDD/SSD</option>
                                    <option value="0" @if ($params['form_ssd'] == 0) selected @endif >HDD</option>
                                    <option value="1" @if ($params['form_ssd'] == 1) selected @endif >SSD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Form Factor</div>
                            <div>
                                <select id="disk_form_factor-select" name="form_factor" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                    <option value="" @if ($params['form_form_factor'] == 0) selected @endif >Select Form Factor</option>
                                    <option value="3.5" @if ($params['form_form_factor'] == '3.5') selected @endif >3.5"</option>
                                    <option value="2.5" @if ($params['form_form_factor'] == '2.5') selected @endif >2.5"</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Speed</div>
                            <div>
                                <select id="disk_speed-select" name="speed" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($disk_speeds['count'] > 0)
                                    <option value="" @if ($params['form_speed'] == 0) selected @endif >Select Speed</option>
                                    @foreach ($disk_speeds['rows'] as $disk_speed) 
                                    <option value="{{ $disk_speed['id'] }}" @if ($params['form_speed'] == $disk_speed['id']) selected @endif >{{ $disk_speed['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Speeds Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewSpeed()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Caddy</div>
                            <div>
                                <select id="disk_caddy-select" name="caddy" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($disk_caddies['count'] > 0)
                                    <option value="" @if ($params['form_caddy'] == 0) selected @endif >Select Caddy</option>
                                    @foreach ($disk_caddies['rows'] as $disk_caddy) 
                                    <option value="{{ $disk_caddy['id'] }}" @if ($params['form_caddy'] == $disk_caddy['id']) selected @endif >{{ $disk_caddy['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Caddies Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewCaddy()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Destroy?</div>
                            <div>
                                <input type="checkbox" id="disk_destroy" name="destroy" value="1" style="margin-top:10px;width:20px;height:20px;" @if ($params['form_destroy'] == 1) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="site-add_disk" name="site" class="form-control text-center theme-dropdown" style="border-color:black;" required>
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
                                <select id="area-add_disk" name="area" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($areas['count'] > 0)
                                    <option value="" @if ($params['form_area'] == 0) selected @endif >Select Area</option>
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
                                <select id="shelf-add_disk" name="shelf" class="form-control text-center theme-dropdown" style="border-color:black;" required>
                                @if ($shelves['count'] > 0)
                                    <option value="" @if ($params['form_shelf'] == 0) selected @endif >Select Shelf</option>
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
                            <button id="disk-add-single" class="btn btn-success align-bottom" type="submit" name="add-disk-submit" style="" value="1">Add</button>
                        </div>
                        <div class="col-sm text-right" style="margin-top:10px">
                            <a href="disk-import.php" class="link" style="font-size:12px; padding-bottom:10px" hidden>Import from CSV</a>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    <!-- End Add disk form section area-->

        <div class="container">
            <div class="container">
                <hr class="viewport-hr" style="border-color:#9f9d9d; margin-left:10px">
                <div class="row centertable">
                    <div class="col-3 float-left viewport-font" >
                        Count: <or class="green">{{ $disks_data['total_count'] }}</or>
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
                                        <option value="type" @if($params['sort'] == "type" || $params['sort'] == null) selected @endif>Type</option>
                                        <option value="speed" @if($params['sort'] == "speed") selected @endif>Speed</option>
                                        <option value="caddy" @if($params['sort'] == "caddy") selected @endif>Caddy</option>
                                        <option value="capacity" @if($params['sort'] == "capacity") selected @endif>Capacity</option>
                                        <option value="vendor" @if($params['sort'] == "vendor") selected @endif>Vendor</option>
                                        <option value="model" @if($params['sort'] == "model") selected @endif>Model</option>
                                        <option value="serial" @if($params['sort'] == "serial") selected @endif>Serial</option>
                                        <option value="serial" @if($params['sort'] == "destroy") selected @endif>Destroy</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="disks-table" class="text-center" style="max-width:max-content; margin:auto">
                <table id="table-disks" class="table table-dark theme-table centertable" style="max-width:max-content;padding-bottom:0px;margin-bottom:0px;">
                    <thead>
                        <tr class="theme-tableOuter align-middle text-center">
                            <th hidden>ID</th>
                            <th>Vendor</th>
                            <th>Model</th>
                            <th>Serial Number</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Speed</th>
                            <th>HDD/SSD</th>
                            <th>Form Factor</th>
                            <th>Caddy</th>
                            <th>Location</th>
                            <th>Destroy</th>
                            <th colspan=3></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($disks_data['count'] == 0)
                        <tr>
                            <td colspan=100% class="text-center align-middle">No Disks Found.</td>
                        </tr>
                    @else
                        @foreach($disks_data['rows'] as $row)
                        <tr class="row-show align-middle text-center  @if($row['deleted'] == 1) red @endif">
                            <form id="diskForm-{{ $row['id'] }}" action="{{ route('disks.restore') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" form="diskForm-{{ $row['id'] }}" value="{{ $row['id'] }}" name="id"/>
                            </form>
                            <td class="align-middle" hidden>{{ $row['id'] }}</td>
                            <td class="align-middle">{{ $disk_vendors['rows'][$row['vendor_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $row['model'] }}</td>
                            <td class="align-middle" id="disk-serial-{{ $row['id'] }}">{{ $row['serial_number'] }}</td>
                            <td class="align-middle">{{ $disk_types['rows'][$row['type_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $disk_capacities['rows'][$row['capacity_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $disk_speeds['rows'][$row['speed_id']]['name'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $row['ssd'] ? 'SSD' : 'HDD' }}</td>
                            <td class="align-middle">{{ $row['form_factor'] ?? 'unknown' }}</td>
                            <td class="align-middle">{{ $disk_caddies['rows'][$row['caddy_id']]['name'] ?? 'unknown' }}</td>
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
                            <td class="align-middle">{!! $row['destroy'] ? '<or class="red">SHRED</or>' : 'No' !!}</td>
                            <td class="align-middle" style="padding-right:5px">
                                <button id="move-btn-{{ $row['id'] }}" class="btn btn-info" style="padding-left:10px;padding-right:10px" type="button" value="move" title="Move?" onclick="modalLoadEditDisk('{{ $row['id'] }}')">
                                    <i class="fa fa-pencil" style="color:white"></i>
                                </button>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:5px">
                                <button id="move-btn-{{ $row['id'] }}" class="btn btn-warning" style="padding-left:10px;padding-right:10px" type="button" value="move" title="Move?" onclick="modalLoadMoveDisk('{{ $row['id'] }}')">
                                    <i class="fa fa-arrows-h" style="color:black"></i>
                                </button>
                            </td>
                            <td class="align-middle" style="padding-left:5px">
                            @if ($row['deleted'] == 1) 
                                <button class="btn btn-success" type="submit" form="diskForm-{{ $row['id'] }}" name="disk-restore-submit" value="1" title="Restore?">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @else 
                                <button class="btn btn-danger" type="button" value="1" title="Delete?" onclick="modalLoadDeleteDisk('{{ $row['id'] }}')">
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
                                @if ($disks_data['pages'] > 1 && $disks_data['pages'] <=15)
                                    @if ($disks_data['page'] > 1)
                                        <or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $disks_data['page']-1 }}') + '')"><</or>
                                    @endif
                                    @if ($disks_data['pages'] > 5)
                                        @for ($i = 1; $i <= $disks_data['pages']; $i++)
                                            @if ($i == $disks_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @elseif ($i == 1 && $disks_data['page'] > 5)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                                            @elseif ($i < $disks_data['page'] && $i >= $disks_data['page']-2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i > $disks_data['page'] && $i <= $disks_data['page']+2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i == $disks_data['pages'])
                                                <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= $disks_data['pages']; $i++)
                                            @if ($i == $disks_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @else
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @endif

                                    @if ($disks_data['page'] < $disks_data['pages'])
                                        <or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $disks_data['page'] + 1}}') + '')">></or>
                                    @endif
                                        @if (isset($disks_data['view']) && $disks_data['view'] !== 'disks_data')
                                            &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('disks_data') }}/{{ $params['stock_id'] }}')">view all</or>
                                        @endif
                                @else 
                                    <form style="margin-bottom:0px">
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'page', document.getElementById('page-select').value + '#disks_data'))" name="page">
                                                        @for ($i = 1; $i <= $disks_data['pages']; $i++) 
                                                            <option value="{{ $i }}" @if ($i == $disks_data['page']) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                        </select>
                                                    </td>
                                                    @if (isset($disks_data['view']) && $disks_data['view'] !== 'disks_data')
                                                    <td><or class="specialColor clickable" onclick="navPage('{{ url('disks_data') }}/{{ $params['stock_id'] }}')">view all</or></td>
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

    @include('includes.assets.disks-modals')

    <!-- Add the JS for the file -->
    <script src={{ asset('js/disks.js') }}></script>

    @include('foot')
</body>
