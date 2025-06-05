<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="attributemanagement-settings" onclick="toggleSection(this, 'attributemanagement')">Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Attribute Management Settings -->
    <div style="padding-top: 20px" id="attributemanagement" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'attributemanagement-settings'])
        <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Tags<a class="align-middle link" style="margin-left:30px;font-size:12px" href="tags.php">View all</a></h4>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement-tag')) {
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
                            <button id="show-deleted-tag" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('tag', 1)" @if (isset($tags['deleted_count']) && $tags['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-tag" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('tag', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($tags['count'] > 0)
                    @foreach ($tags['rows'] as $tag)
                        @if ($tag['deleted'] == 1)
                        <tr id="tag-row-{{ $tag['id'] }}" class="align-middle red theme-divBg tag-deleted" hidden>
                        @else 
                        <tr id="tag-row-{{ $tag['id'] }}" class="align-middle">
                        @endif
                    
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="tag"/>
                            <input type="hidden" name="id" value="{{ $tag['id'] }}">
                            <td id="tag-{{ $tag['id'] }}-id" class="text-center align-middle">{{ $tag['id'] }}</td>
                            <td id="tag-{{ $tag['id'] }}-name" class="text-center align-middle">{{ $tag['name'] }}</td>
                            <td class="text-center align-middle">{{ $tag_links[$tag['id']]['count'] ?? 0 }}</td>
                            <td class="text-center align-middle">
                            @if ((int) $tag['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit"
                                    @if (($tag_links[$tag['id']]['count'] ?? 0) !== 0)
                                        disabled title="Tag still linked to stock. Remove these links before deleting."
                                    @endif
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @endif
                            <td class="text-center align-middle" colspan="2">
                                @if ((int)$tag['deleted'] !== 1) 
                                    @if (array_key_exists($tag['id'], $tag_links) && ((int)$tag_links[$tag['id']]['count'] ?? 0) !== 0)
                                        <button class="btn btn-warning" id="tag-{{ $tag['id'] }}-links" type="button" onclick="showLinks('tag', '{{ $tag['id'] }}')">Show Links</button> 
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($tag['id'], $tag_links) && ((int)$tag_links[$tag['id']]['count'] ?? 0) !== 0)
                        <tr id="tag-row-{{ $tag['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>ID</th>
                                                <th>Stock ID</th>
                                                <th>Stock Name</th>
                                                <th>Tag ID</th>
                                                <th>Tag Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($tag_links[$tag['id']]['rows'] as $link) 
                                            <tr class="clickable" onclick="navPage('stock?stock_id={{ $link['stock_id'] }}')">
                                                <td class="text-center">{{ $link['id'] }}</td>
                                                <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $link['stock_id'] }}</a></td>
                                                <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $stock['rows'][$link['stock_id']]['name'] }}</a></td>
                                                <td class="text-center">{{ $link['tag_id'] }}</td>
                                                <td class="text-center">{{ $tags['rows'][$link['tag_id']]['name'] }}</td>

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
                    <tr class="align-middle"><td colspan="100%">No tags found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>

        <hr style="border-color:white; margin-left:10px">
        <h4 style="margin-left:10px; margin-right:10px; margin-top:20px; font-size:20px; margin-bottom:10px">Manufacturers</h4>

        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                            <button id="show-deleted-manufacturer" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 1)" @if (isset($manufacturers['deleted_count']) && $manufacturers['deleted_count'] == 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                    
                                </span>
                            </button>
                            <button id="hide-deleted-manufacturer" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 0)" hidden>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                </span>
                            
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($manufacturers['count'] > 0)
                    @foreach ($manufacturers['rows'] as $manufacturer)
                        @if ($manufacturer['deleted'] == 1)
                        <tr id="manufacturer-row-{{ $manufacturer['id'] }}" class="align-middle red theme-divBg manufacturer-deleted" hidden>
                        @else 
                        <tr id="manufacturer-row-{{ $manufacturer['id'] }}" class="align-middle">
                        @endif
                    
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            @csrf
                            <input type="hidden" name="attribute-type" value="manufacturer"/>
                            <input type="hidden" name="id" value="{{ $manufacturer['id'] }}">
                            <td id="manufacturer-{{ $manufacturer['id'] }}-id" class="text-center align-middle">{{ $manufacturer['id'] }}</td>
                            <td id="manufacturer-{{ $manufacturer['id'] }}-name" class="text-center align-middle">{{ $manufacturer['name'] }}</td>
                            <td class="text-center align-middle">{{ $manufacturer_links[$manufacturer['id']]['count'] ?? 0 }}</td>
                            <td class="text-center align-middle">
                            @if ((int) $manufacturer['deleted'] === 0)
                                <button class="btn btn-danger" type="submit" name="attributemanagement-submit"
                                    @if (($manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                                        disabled title="Tag still linked to stock. Remove these links before deleting."
                                    @endif
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-success" type="submit" name="attributemanagement-restore">
                                    <i class="fa fa-trash-restore"></i>
                                </button>
                            @endif
                            <td class="text-center align-middle" colspan="2">
                                @if ((int)$manufacturer['deleted'] !== 1) 
                                    @if (array_key_exists($manufacturer['id'], $manufacturer_links) && ((int)$manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                                        <button class="btn btn-warning" id="manufacturer-{{ $manufacturer['id'] }}-links" type="button" onclick="showLinks('manufacturer', '{{ $manufacturer['id'] }}')">Show Links</button> 
                                    @endif
                                @endif
                            </td>
                        </form>
                    </tr>
                        @if (array_key_exists($manufacturer['id'], $manufacturer_links) && ((int)$manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                        <tr id="manufacturer-row-{{ $manufacturer['id'] }}-links" class="align-middle" hidden>
                            <td colspan="100%">
                                <div>
                                    <table class="table table-dark theme-table">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th>ID</th>
                                                <th>Stock ID</th>
                                                <th>Stock Name</th>
                                                <th>Manufacturer ID</th>
                                                <th>Manufacturer Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($manufacturer_links[$manufacturer['id']]['rows'] as $link) 
                                            <tr class="clickable" onclick="navPage('stock?stock_id={{ $link['stock_id'] }}')">
                                                <td class="text-center">{{ $link['id'] }}</td>
                                                <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $link['stock_id'] }}</a></td>
                                                <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $stock['rows'][$link['stock_id']]['name'] }}</a></td>
                                                <td class="text-center">{{ $link['manufacturer_id'] }}</td>
                                                <td class="text-center">{{ $manufacturers['rows'][$link['manufacturer_id']]['name'] }}</td>

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
                    <tr class="align-middle"><td colspan="100%">No manufacturers found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>      
    </div>
</div>