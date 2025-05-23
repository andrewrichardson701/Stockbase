<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Optics</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')

    {{-- <div class="content viewport-content" style="padding-top:80px"> --}}
        <div class="min-h-screen-sub20">
            <!-- Page Heading -->
            <header class="theme-divBg shadow" style="padding-top:60px">
                <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl  leading-tight headerfix">
                        Optics
                    </h2>
                </div>
            </header>
    <!-- Selection Area -->
        <div id="selection" class="viewport-selection" style="margin-top:20px; margin-bottom:15px">
        <!-- Small viewport selection area -->
            <div class="centertable viewport-small" style="max-width:max-content">
                <table class="centertable">
                    <tbody>
                        <tr>
                            <td>Site:</td>
                            <td>Search:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="site" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'site', this.value))">
                                    <option value="0" @if ($params['site'] == 0) selected @endif >All</option>
                                @if ($sites['count'] > 0)
                                    @foreach ($sites['rows'] as $site) 
                                    <option value="{{ $site['id'] }}" @if ($params['site'] == $site['id']) selected @endif >{{ $site['name'] }}</option>
                                    @endforeach
                                @endif
                                </select>
                            </td>
                            <td>
                                <form action="" method="GET" style="display:inline-block">
                                    <span style="">
                                        <input type="text" id="search" name="search" placeholder="Search" class="form-control" style="display:inline !important; width:100px;padding-right:0px" @if ($params['search'] !== null) value="{{ $params['search'] }}" @endif>
                                        <button id="search-submit" class="btn btn-info" style="vertical-align:middle;margin-top: 0px !important;padding: 5px 6px 5px 6px !important;opacity:80%;color:black;" type="submit">
                                            <i class="fa fa-search" style="padding-top:4px"></i>
                                        </button>
                                    </span>
                                </form>
                            </td>
                            <td>
                                <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(`{{ url('optics') }}`)">
                                    <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                                </button>
                            </td>
                            <td>
                                <button id="add-optic-small" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 2px 5px 2px" onclick="toggleAddDivSmall()" @if($params['add_form'] == 1) hidden @endif>
                                    <i class="fa fa-plus" style="padding-top:4px"></i> Add
                                </button>
                                <button id="add-optic-hide-small" class="btn btn-danger nav-v-b viewport-small" style="opacity:80%;color:black;padding:6px 2px 5px 2px" onclick="toggleAddDivSmall()" @if($params['add_form'] == 0) hidden @endif>
                                    Hide Add
                                </button>
                            </td>
                            <td>
                                <button id="show-deleted-optics-small" class="btn btn-success nav-v-b" style="padding:6px 2px 5px 2px;opacity:80%;color:white" onclick="navPage(updateQueryParameter('', 'deleted', 1))" @if ($params['deleted'] == 1) hidden @endif>
                                    Deleted
                                </button>
                                <button id="hide-deleted-optics-small" class="btn btn-danger nav-v-b viewport-small" style="padding:6px 2px 5px 2px;opacity:80%;color:black" onclick="navPage(updateQueryParameter('', 'deleted', 0))" @if ($params['deleted'] == 0) hidden @endif>
                                    Deleted
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <!-- End Small viewport selection area -->

        <!-- Large viewport selection area -->
            <div class="row centertable viewport-large" style="max-width:max-content">
                <div class="col align-middle viewport-large" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Site:</label>
                    <select name="site" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'site', this.value))">
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
                            <input type="text" id="search" name="search" placeholder="Search" class="form-control" style="display:inline !important; width:200px;padding-right:0px" @if ($params['search'] !== null) value="{{ $params['search'] }}" @endif>
                            <button id="search-submit" class="btn btn-info" style="margin-top:-3px;vertical-align:middle;padding: 6px 6px 6px 6px;opacity:80%;color:black" type="submit">
                                <i class="fa fa-search" style="padding-top:4px"></i>
                            </button>
                        </span>
                    </form>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(`{{ url('optics') }}`)">
                        <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="add-optic" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" @if($params['add_form'] == 1) hidden @endif>
                        <i class="fa fa-plus" style="padding-top:4px"></i> Add Optic
                    </button>
                    <button id="add-optic-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" @if($params['add_form'] == 0) hidden @endif>
                        Hide Add Optic
                    </button>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <button id="show-deleted-optics" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter('', 'deleted', 1))" @if ($params['deleted'] == 1) hidden @endif>
                        View Deleted
                    </button>
                    <button id="hide-deleted-optics" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter('', 'deleted', 0))" @if ($params['deleted'] == 0) hidden @endif>
                        Hide Deleted
                    </button>
                </div>
            </div>
        <!-- End Large viewport selection area --> 
        
        <!-- Optic paramater selection area -->
            <div class="row centertable" style="max-width:max-content; margin-top:10px">
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Type:</label>
                    <select name="type" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'type', this.value))">
                        <option value="0" @if ($params['optic_type'] == 0) selected @endif >All</option>
                    @if ($optic_types['count'] > 0)
                        @foreach ($optic_types['rows'] as $optic_type) 
                        <option value="{{ $optic_type['id'] }}" @if ($params['optic_type'] == $optic_type['id']) selected @endif >{{ $optic_type['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Speed:</label>
                    <select name="speed" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'speed', this.value))">
                        <option value="0" @if ($params['optic_speed'] == 0) selected @endif >All</option>
                    @if ($optic_speeds['count'] > 0)
                        @foreach ($optic_speeds['rows'] as $optic_speed) 
                        <option value="{{ $optic_speed['id'] }}" @if ($params['optic_speed'] == $optic_speed['id']) selected @endif >{{ $optic_speed['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Mode:</label>
                    <select name="mode" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'mode', this.value))">
                        <option value="0" @if ($params['optic_mode'] == 0) selected @endif >All</option>
                    @if ($optic_modes['count'] > 0)
                        @foreach ($optic_modes['rows'] as $optic_mode) 
                        <option value="{{ $optic_mode['id'] }}" @if ($params['optic_mode'] == $optic_mode['id']) selected @endif >{{ $optic_mode['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Connector:</label>
                    <select name="connector" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'connector', this.value))">
                        <option value="0" @if ($params['optic_connector'] == 0) selected @endif >All</option>
                    @if ($optic_connectors['count'] > 0)
                        @foreach ($optic_connectors['rows'] as $optic_connector) 
                        <option value="{{ $optic_connector['id'] }}" @if ($params['optic_connector'] == $optic_connector['id']) selected @endif >{{ $optic_connector['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col align-middle" style="max-width:max-content">
                    <label class="align-middle" style="padding-right:15px;padding-top:7px">Distance:</label>
                    <select name="distance" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter('', 'distance', this.value))">
                        <option value="0" @if ($params['optic_distance'] == 0) selected @endif >All</option>
                    @if ($optic_distances['count'] > 0)
                        @foreach ($optic_distances['rows'] as $optic_distance) 
                        <option value="{{ $optic_distance['id'] }}" @if ($params['optic_distance'] == $optic_distance['id']) selected @endif >{{ $optic_distance['name'] }}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
            </div>
        <!-- End Optic parameter selection area -->
        </div>
    <!-- End Selection Area -->

    <!-- Add optic form section area-->
        <div class="container" id="add-optic-section" style="margin-bottom:20px" @if($params['add_form'] == 0) hidden @endif>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new optic</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="optic-add-response" hidden></p>
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    @csrf
                    <div class="row" style="margin-right:25px;margin-top:5px">
                        <div class="col">
                            <div>Serial Number</div>
                            <div><input class="form-control text-center" type="text" id="serial" name="serial" style="min-width:120px" placeholder="Serial" oninput="searchSerial(this.value)" required/></div>
                        </div>
                        <div class="col">
                            <div>Model</div>
                            <div>
                                <input class="form-control text-center" id="model" type="text" list="names" name="model" placeholder="Model" style="min-width:120px" @if($params['form_model'] !== null) value="{{ $params['form_model'] }}" @endif required/>
                                <datalist id="names">
                                @if ($optic_models['count'] > 0)
                                    @foreach ($optic_models['rows'] as $optic_model) 
                                    <option value="{{ $optic_model['model'] }}">{{ $optic_model['model'] }}</option>
                                    @endforeach
                                @endif
                                </datalist>
                            </div>
                        </div>
                        <div class="col">
                            <div>Spectrum</div>
                            <div><input class="form-control text-center" type="text" id="spectrum" name="spectrum" style="min-width:120px" placeholder="1310nm" @if($params['form_spectrum'] !== null) value="{{ $params['form_spectrum'] }}" @endif required/></div>
                        </div>
                        <div class="col">
                            <div>Vendor</div>
                            <div>
                                <select id="vendor" name="vendor" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_vendors['count'] > 0)
                                    <option value="" @if ($params['form_vendor'] == 0) selected @endif >Select Vendor</option>
                                    @foreach ($optic_vendors['rows'] as $optic_vendor) 
                                    <option value="{{ $optic_vendor['id'] }}" @if ($params['form_vendor'] == $optic_vendor['id']) selected @endif >{{ $optic_vendor['name'] }}</option>
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
                            <div>Type</div>
                            <div>
                                <select id="type" name="type" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_types['count'] > 0)
                                    <option value="" @if ($params['form_type'] == 0) selected @endif >Select Type</option>
                                    @foreach ($optic_types['rows'] as $optic_type) 
                                    <option value="{{ $optic_type['id'] }}" @if ($params['form_type'] == $optic_type['id']) selected @endif >{{ $optic_type['name'] }}</option>
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
                    </div>
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Speed</div>
                            <div>
                                <select id="speed" name="speed" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_speeds['count'] > 0)
                                    <option value="" @if ($params['form_speed'] == 0) selected @endif >Select Speed</option>
                                    @foreach ($optic_speeds['rows'] as $optic_speed) 
                                    <option value="{{ $optic_speed['id'] }}" @if ($params['form_speed'] == $optic_speed['id']) selected @endif >{{ $optic_speed['name'] }}</option>
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
                            <div>Connector</div>
                            <div>
                                <select id="connector" name="connector" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_connectors['count'] > 0)
                                    <option value="" @if ($params['form_connector'] == 0) selected @endif >Select Connector</option>
                                    @foreach ($optic_connectors['rows'] as $optic_connector) 
                                    <option value="{{ $optic_connector['id'] }}" @if ($params['form_connector'] == $optic_connector['id']) selected @endif >{{ $optic_connector['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Connectors Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewConnector()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Distance</div>
                            <div>
                                <select id="distance" name="distance" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_distances['count'] > 0)
                                    <option value="" @if ($params['form_distance'] == 0) selected @endif >Select Distance</option>
                                    @foreach ($optic_distances['rows'] as $optic_distance) 
                                    <option value="{{ $optic_distance['id'] }}" @if ($params['form_distance'] == $optic_distance['id']) selected @endif >{{ $optic_distance['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Distances Found</option>
                                @endif
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewDistance()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Mode</div>
                            <div>
                                <select id="mode" name="mode" class="form-control text-center" style="border-color:black;" required>
                                @if ($optic_modes['count'] > 0)
                                    <option value="" @if ($params['form_mode'] == 0) selected @endif >Select Mode</option>
                                    @foreach ($optic_modes['rows'] as $optic_mode) 
                                    <option value="{{ $optic_mode['id'] }}" @if ($params['form_mode'] == $optic_mode['id']) selected @endif >{{ $optic_mode['name'] }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>No Modes Found</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="type" name="type" class="form-control text-center" style="border-color:black;" required>
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
                    </div>
                    <div class="row align-middle" style="margin-right:25px">
                        <div class="col-sm" style="margin-top:10px"> 
                        </div>
                        <div class="col" style="margin-top:10px">
                            <button id="optic-add-single" class="btn btn-success align-bottom" type="submit" name="add-optic-submit" style="" value="1">Add</button>
                        </div>
                        <div class="col-sm text-right" style="margin-top:10px">
                            <a href="optic-import.php" class="link" style="font-size:12px; padding-bottom:10px">Import from CSV</a>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    <!-- End Add optic form section area-->

        <div class="container">
            <div class="container">
                <hr class="viewport-hr" style="border-color:#9f9d9d; margin-left:10px">
                <div class="row centertable">
                    <div class="col-3 float-left viewport-font" >
                        Count: <or class="green">{{ $optics_data['total_count'] }}</or>
                    </div>
                    <div class="col">
                        {!! $response_handling !!}
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
                                        <option value="connector" @if($params['sort'] == "connector") selected @endif>Connector</option>
                                        <option value="distance" @if($params['sort'] == "distance") selected @endif>Distance</option>
                                        <option value="model" @if($params['sort'] == "model") selected @endif>Model</option>
                                        <option value="speed" @if($params['sort'] == "speed") selected @endif>Speed</option>
                                        <option value="mode" @if($params['sort'] == "mode") selected @endif>Mode</option>
                                        <option value="serial" @if($params['sort'] == "serial") selected @endif>Serial</option>
                                        <option value="vendor" @if($params['sort'] == "vendor") selected @endif>Vendor</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="optics-table" class="text-center" style="max-width:max-content; margin:auto">
                <table class="table table-dark theme-table centertable viewport-font" style="max-width:max-content;padding-bottom:0px;margin-bottom:0px;">
                    <thead>
                        <tr class="align-middle text-center theme-tableOuter viewport-large-empty">
                            <th hidden>ID</th>
                            <th>Type</th>
                            <th>Connector</th>
                            <th>Model</th>
                            <th>Speed</th>
                            <th>Mode</th>
                            <th>Spectrum</th>
                            <th>Distance</th>
                            <th>Serial</th>
                            <th>Vendor</th>
                            <th @if($params['site'] !== 0) hidden @endif>Site</th>
                            <th>Comments</th>
                            <th hidden>Quantity</th>
                            <th colspan=2></th>
                        <tr>
                        <tr class="align-middle text-center theme-tableOuter viewport-small-empty">
                            <th hidden>ID</th>
                            <th>Type</th>
                            <th>Conn.</th>
                            <th>Model</th>
                            <th>Speed</th>
                            <th>Mode</th>
                            <th>Spect.</th>
                            <th>Dist.</th>
                            <th>S/N</th>
                            <th>Vendor</th>
                            <th @if($params['site'] !== 0) hidden @endif>Site</th>
                            <th>Comm.</th>
                            <th hidden>Quantity</th>
                            <th colspan=2></th>
                        <tr>
                    </thead>
                    <tbody>
                    @if ($optics_data['count'] == 0)
                        <tr>
                            <td colspan=100% class="text-center align-middle">No Optics Found.</td>
                        </tr>
                    @else
                        @foreach($optics_data['rows'] as $row)
                        <tr id="item-{{ $row['id'] }}" class="row-show align-middle text-center @if($row['deleted'] == 1) red @endif">
                            <form id="opticForm-{{ $row['id'] }}"action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                <!-- Include CSRF token in the form -->
                                @csrf
                                <input type="hidden" form="opticForm-{{ $row['id'] }}" value="{{ $row['id'] }}" name="id"/>
                            </form>
                            <td class="align-middle" hidden>{{ $row['id'] }}</td>
                            <td class="align-middle">{{ $row['type_name'] }}</td>
                            <td class="align-middle">{{ $row['connector_name'] }}</td>
                            <td class="align-middle">{{ $row['model'] }}</td>
                            <td class="align-middle">{{ $row['speed_name'] }}</td>
                            <td class="align-middle">{{ $row['mode'] }}</td>
                            <td class="align-middle">{{ $row['spectrum'] }}</td>
                            <td class="align-middle">{{ $row['distance_name'] }}</td>
                            <td class="align-middle" id="optic-serial-{{ $row['id'] }}">{{ $row['serial_number'] }}</td>
                            <td class="align-middle">{{ $row['vendor_name'] }}</td>
                            <td class="align-middle link gold" style="white-space: nowrap !important;" onclick="navPage(updateQueryParameter('', 'site', {{ $row['site_id'] }}))" @if($params['site'] !== 0) hidden @endif>{{ $row['site_name'] }}</td>
                            <td class="align-middle">
                                <div style="position: relative; display: inline-block;">
                                    @if ($row['comment_data']['count'] > 0)
                                    <i class="fa-solid fa-message clickable gold" style="font-size:20; padding:5px" onclick="toggleAddComment('{{ $row['id'] }}', 1)"></i>
                                    <span class="uni theme-inv-textColor" style="pointer-events: none; font-size:10px; position: absolute; top: 3px; right: 5px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment('{{ $row['id'] }}', 1)">{{ $row['comment_data']['count'] }}</span>
                                    @else
                                    <i class="fa-regular fa-message clickable gold" style="font-size:18px; padding:5px" onclick="toggleAddComment('{{ $row['id'] }}', 0)"></i>
                                    <span class="uni gold" style="pointer-events: none; font-size:12px; position: absolute; top: 1px; right: 6px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment('{{ $row['id'] }}', 0)">+</span>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle" hidden>{{ $row['quantity'] }}</td>
                            <td class="align-middle" style="padding-right:5px">
                                <button id="move-btn-{{ $row['id'] }}" class="btn btn-warning" style="padding-left:10px;padding-right:10px" type="button" value="move" title="Move?" onclick="modalLoadMoveOptic('{{ $row['id'] }}')">
                                    <i class="fa fa-arrows-h" style="color:black"></i>
                                </button>
                            </td>
                            <td class="align-middle" style="padding-left:5px">
                            @if ($row['deleted'] == 1) 
                                <button class="btn btn-success" type="submit" form="opticForm-{{ $row['id'] }}" name="optic-restore-submit" value="1" title="Restore?">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @else 
                                <button class="btn btn-danger" type="button" value="1" title="Delete?" onclick="modalLoadDeleteOptic('{{ $row['id'] }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                            </td>
                        </tr>
                        <tr id="item-{{ $row['id'] }}-add-comments" class="row-add-hide align-middle text-center" hidden>
                            <td colspan="100%">
                                <div class="container">
                                    <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                        <!-- Include CSRF token in the form -->
                                        @csrf
                                        <div class="row centertable" style="max-width:max-content">
                                            <div class="col" style="max-width:max-content">
                                                <label class="nav-v-c">Comment:</label>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <input type="hidden" name="id" value="{{ $row['id'] }}" />
                                                <input name="comment" class="form-control row-dropdown" type="text" style="padding: 2px 7px 2px 7px; width:250px" placeholder="Comment..."/>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <button class="btn btn-success align-bottom" type="submit" name="optic-comment-add" style="margin-left:10px" value="1">Add</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @if ($row['comment_data']['count'] > 0)
                        <tr id="item-{{ $row['id'] }}-comments" class="row-hide align-middle text-center" hidden>
                            <td colspan="100%">
                                <div class="container">
                                    <table class="centertable" style="border: 1px solid #454d55;">
                                        <thead>
                                            <tr class="row-show align-middle text-center">
                                                <th hidden>ID</th>
                                                <th>Username</th>
                                                <th>Comment</th>
                                                <th>Timestamp</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($row['comment_data']['rows'] as $comment)
                                            <tr id="comment-{{ $comment['id'] }}" class="row-show align-middle text-center">
                                                <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                                    <!-- Include CSRF token in the form -->
                                                    @csrf
                                                    <input type="hidden" value="{{ $comment['id'] }}" name="id"/>
                                                    <td class="align-middle" hidden>{{ $comment['id'] }}</td>
                                                    <td class="align-middle">{{ $comment['username'] }}</td>
                                                    <td class="align-middle">{{ $comment['comment'] }}</td>
                                                    <td class="align-middle">{{ $comment['timestamp'] }}</td>
                                                    <td class="align-middle"><button class="btn btn-danger" type="submit" name="optic-comment-delete" value="1"><i class="fa fa-trash"></i></button></td>
                                                </form>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
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
                                @if ($optics_data['pages'] > 1 && $optics_data['pages'] <=15)
                                    @if ($optics_data['page'] > 1)
                                        <or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $optics_data['page']-1 }}') + '')"><</or>
                                    @endif
                                    @if ($optics_data['pages'] > 5)
                                        @for ($i = 1; $i <= $optics_data['pages']; $i++)
                                            @if ($i == $optics_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @elseif ($i == 1 && $optics_data['page'] > 5)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                                            @elseif ($i < $optics_data['page'] && $i >= $optics_data['page']-2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i > $optics_data['page'] && $i <= $optics_data['page']+2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @elseif ($i == $optics_data['pages'])
                                                <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= $optics_data['pages']; $i++)
                                            @if ($i == $optics_data['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @else
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @endif

                                    @if ($optics_data['page'] < $optics_data['pages'])
                                        <or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $optics_data['page'] + 1}}') + '')">></or>
                                    @endif
                                        @if (isset($optics_data['view']) && $optics_data['view'] !== 'optics_data')
                                            &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('optics_data') }}/{{ $params['stock_id'] }}')">view all</or>
                                        @endif
                                @else 
                                    <form style="margin-bottom:0px">
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'page', document.getElementById('page-select').value + '#optics_data'))" name="page">
                                                        @for ($i = 1; $i <= $optics_data['pages']; $i++) 
                                                            <option value="{{ $i }}" @if ($i == $optics_data['page']) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                        </select>
                                                    </td>
                                                    @if (isset($optics_data['view']) && $optics_data['view'] !== 'optics_data')
                                                    <td><or class="specialColor clickable" onclick="navPage('{{ url('optics_data') }}/{{ $params['stock_id'] }}')">view all</or></td>
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
            </div>
        </div>
    </div>  
    

    @include('includes.optics.optics-modals')

    <!-- Add the JS for the file -->
    <script src="{{ asset('js/optics.js') }}"></script>
    @include('foot')
</body>