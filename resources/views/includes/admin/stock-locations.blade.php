<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stocklocations-settings" onclick="toggleSection(this, 'stocklocations')">Stock Location Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Stock Location Settings -->
    <div style="padding-top: 20px" id="stocklocations" hidden>

        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'stocklocation-settings')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'stocklocations-settings'])
        <table class="table table-dark theme-table text-center" style="max-width:max-content; vertical-align: middle;">
            <thead>
                <tr class="theme-tableOuter">
                    <th>site_id</th>
                    <th>site_name</th>
                    <th hidden>site_description</th>
                    <th style="border-left:2px solid #95999c">area_id</th>
                    <th>area_name</th>
                    <th hidden>area_description</th>
                    <th hidden>area_site_id</th>
                    <th hidden>area_parent_id</th>
                    <th style="border-left:2px solid #95999c">shelf_id</th>
                    <th>shelf_name</th>
                    <th hidden>shelf_area_id</th>
                    <th style="border-left:2px solid #95999c" colspan=3>
                        <button id="show-deleted-location" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('location', 1)" @if ($sites['deleted_count'] + $areas['deleted_count'] + $shelves['deleted_count'] == 0) hidden @endif>
                        <span class="zeroStockFont">
                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                        </span>
                        </button>
                        <button id="hide-deleted-location" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('location', 0)" hidden>
                            <span class="zeroStockFont">
                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                            </span>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($sites['rows'] as $site)
                @if (!$loop->first)
                <tr class="theme-tableOuter @if ($site['deleted'] == 1) location-deleted" hidden @else " @endif><td colspan=9></td></tr>
                @endif
                @if ($site['deleted'] == 1)
                <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                @else
                <tr style="background-color:{{ $location_colors[$loop->iteration % 2]['site'] }} !important; color:black">
                @endif
                    <form id="siteForm-{{ $site['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                        @csrf
                        <input type="hidden" id="site-{{ $site['id'] }}-type" name="type" value="site" />
                        <input type="hidden" id="site-{{ $site['id'] }}-id" name="id" value="{{ $site['id'] }}" />
                        <td class="stockTD" style="">{{ $site['id'] }}</td>
                        <td class="stockTD" style=""><input id="site-{{ $site['id'] }}-name" class="form-control stockTD-input theme-input" name="name" type="text" value="{{ htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                        <td hidden><input id="site-{{ $site['id'] }}-description" class="form-control stockTD-input theme-input" type="text" name="description" value="{{ htmlspecialchars($site['description'], ENT_QUOTES, 'UTF-8') }}" /></td>
                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td> <td hidden></td> <td hidden></td> 
                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-edit-submit" value="1" type="submit">
                                <i class="fa fa-save"></i>
                            </button>
                        </td>
                        <td class="stockTD theme-table-blank" ">
                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $site['id'] }}', 'site')">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </td>
                    </form>
                    <form id="siteForm-delete-{{ $site['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $site['id'] }}" />
                        <input type="hidden" name="type" value="site" />
                        <td class="stockTD theme-table-blank">
                        @if ($site['deleted'] != 1)
                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" 
                            @if (array_key_exists($site['id'], $site_links_optics))
                                @if (($site_links[$site['id']]['count'] + $site_links_optics[$site['id']]['count'] ?? 0) !== 0) 
                                    disabled title="Dependencies exist for this object." 
                                @else 
                                    title="Delete object" 
                                @endif 
                            @else
                                @if (($site_links[$site['id']]['count'] ?? 0) !== 0) 
                                    disabled title="Dependencies exist for this object." 
                                @else 
                                    title="Delete object" 
                                @endif 
                            @endif
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        @else
                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="site" type="submit" title="Restore object">
                                <i class="fa fa-trash-restore"></i>
                            </button>
                        @endif
                        </td>
                    </form>
                </tr>
                @foreach ($areas['rows'] as $area)
                    @if ($area['site_id'] == $site['id'])
                        @if ($area['deleted'] == 1)
                        <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                        @else
                        <tr style="background-color:{{ $location_colors[$loop->parent->iteration % 2]['area'] }} !important; color:black">
                        @endif
                            <form id="areaForm-{{ $area['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                                @csrf
                                <input type="hidden" id="area-{{ $area['id'] }}-type" name="type" value="area" />
                                <input type="hidden" id="area-{{ $area['id'] }}-id" name="id" value="{{ $area['id'] }}" />
                                <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td>
                                <td class="stockTD" style="border-left:2px solid #454d55; ">{{ $area['id'] }}</td>
                                <td class="stockTD" style=""><input id="area-{{ $area['id'] }}-name" class="form-control stockTD-input theme-input" type="text" name="name" value="{{ htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                                <td class="stockTD" hidden><input id="area-{{ $area['id'] }}-description" class="form-control stockTD-input theme-input" type="text" name="description" value="{{ htmlspecialchars($area['description'], ENT_QUOTES, 'UTF-8') }}" /></td>
                                <td class="stockTD" hidden><input id="area-{{ $area['id'] }}-parent" type="hidden" name="area-site-id" value="{{ $area['site_id'] }}" /></td>
                                <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </td>
                                <td class="stockTD theme-table-blank">
                                    <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $area['id'] }}', 'area')">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </td>
                            </form>
                            <form id="areaForm-delete-{{ $area['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $area['id'] }}" />
                                <input type="hidden" name="type" value="area" />
                                <td class="stockTD theme-table-blank">
                                @if ($area['deleted'] != 1)
                                    <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" @if (($area_links[$area['id']]['count'] ?? 0) !== 0) disabled title="Dependencies exist for this object." @else title="Delete object" @endif >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="area" type="submit" title="Restore object">
                                        <i class="fa fa-trash-restore"></i>
                                    </button>
                                @endif
                                </td>
                            </form>
                        </tr>
                        
                        @foreach($shelves['rows'] as $shelf)
                            @if ($shelf['area_id'] == $area['id'])
                                @if ($shelf['deleted'] == 1)
                                <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                                @else
                                <tr style="background-color:{{ $location_colors[$loop->parent->parent->iteration % 2]['shelf'] }} !important; color:black">
                                @endif
                                    <form id="shelfForm-{{ $shelf['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                                        @csrf
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-site" name="site" value="{{ $site['id'] }}" />
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-type" name="type" value="shelf" />
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-id" name="id" value="{{ $shelf['id'] }}" />
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td> 
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55;"></td> <td class="theme-table-blank"></td> <td hidden></td> <td hidden></td> <td hidden></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">{{ $shelf['id'] }}</td>
                                        <td class="stockTD" style=""><input id="shelf-{{ $shelf['id'] }}-name" class="form-control stockTD-input theme-input" type="text" name="name" value="{{ htmlspecialchars($shelf['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                                        <td class="stockTD" hidden><input id="shelf-{{ $shelf['id'] }}-parent" type="hidden" name="shelf-area-id" value="{{ $shelf['area_id']  }}" /></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $shelf['id'] }}', 'shelf')" >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>
                                    </form>
                                    <form id="shelfForm-delete-{{ $shelf['id'] }}" enctype="multipart/form-data" action="{{ route('admin.stockLocationSettings') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $shelf['id'] }}" />
                                        <input type="hidden" name="type" value="shelf" />
                                        <td class="stockTD theme-table-blank">
                                        @if ($shelf['deleted'] != 1)
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="shelf" type="submit" 
                                            @if (array_key_exists($shelf['id'], $shelf_links_containers))
                                                @if (($shelf_links[$shelf['id']]['count'] + $shelf_links_containers[$shelf['id']]['count'] ?? 0) !== 0)
                                                    disabled title="Dependencies exist for this object." 
                                                @else 
                                                    title="Delete object" 
                                                @endif 
                                            @else
                                                @if (($shelf_links[$shelf['id']]['count'] ?? 0) !== 0) 
                                                    disabled title="Dependencies exist for this object." 
                                                @else 
                                                    title="Delete object" 
                                                @endif 
                                            @endif
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="shelf" type="submit" title="Restore object">
                                                <i class="fa fa-trash-restore"></i>
                                            </button>
                                        @endif
                                        </td>
                                    </form>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                <tr class="theme-table-blank @if ($site['deleted'] == 1) location-deleted" hidden @else " @endif>
                    <td colspan=6 class="stockTD">
                        <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px" onclick="modalLoadAdd({{ $site['id'] }})">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td colspan=3 style="border-left:2px solid #454d55">  
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>