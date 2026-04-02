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
        </div>

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
                            <th>Make</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Speed</th>
                            <th>HDD/SSD</th>
                            <th>Form Factor</th>
                            <th>Caddy</th>
                            <th>Location</th>
                            <th>Destroy</th>
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
                            <td hidden>{{ $row['id'] }}</td>
                            <td>{{ $disk_vendors['rows'][$row['vendor_id']]['name'] ?? 'unknown' }}</td>
                            <td>{{ $row['model'] }}</td>
                            <td>{{ $disk_types['rows'][$row['type_id']]['name'] ?? 'unknown' }}</td>
                            <td>{{ $disk_capacities['rows'][$row['capacity_id']]['capacity'] ?? 'unknown' }}</td>
                            <td>{{ $disk_speeds['rows'][$row['speed_id']]['speed'] ?? 'unknown' }}</td>
                            <td>{{ $row['ssd'] ? 'SSD' : 'HDD' }}</td>
                            <td>{{ $row['form_factor'] ?? 'unknown' }}</td>
                            <td>{{ $disk_caddies['rows'][$row['caddy_id']]['vendor'] ?? 'unknown' }}</td>
                            <td>{{ $sites['rows'][$areas['rows'][$shelves['rows'][$row['shelf_id']]['area_id']]['site_id']]['name'] }}, {{ $areas['rows'][$shelves['rows'][$row['shelf_id']]['area_id']]['name'] }}, {{ $shelves['rows'][$row['shelf_id']]['name'] }}</td>
                            <td>{!! $row['destroy'] ? '<or class="red">SHRED</or>' : 'No' !!}</td>
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
                @dd(get_defined_vars())
        </div>

    </div>

    @include('foot')
</body>
