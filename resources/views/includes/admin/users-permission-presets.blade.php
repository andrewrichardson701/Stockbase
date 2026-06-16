<div style="padding-bottom:0px">
    <div class="container-fluid">
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="userspermissionspresets-settings">User Permission Presets</h3> 
        <!-- Users Roles -->
    </div>
    <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="userspermissionspresets">
        @include('includes.response-handling', ['section' => 'userspermissionspresets-settings'])
        <div class="content" style="padding-top:0px; padding-bottom:0px">
            <table id="permissionRolesTable" class="table table-dark theme-table" style="max-width:max-content;white-space:nowrap">
                <thead>
                    <tr class="text-center theme-tableOuter">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Root</th>
                        <th>Admin</th>
                        <th>Locations</th>
                        <th>Stock</th>
                        <th>Cables</th>
                        <th>Optics</th>
                        <th>CPUs</th>
                        <th>Memory</th>
                        <th>Disks</th>
                        <th>Containers</th>
                        <th>Changelog</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- {{ dd($users_permissions_roles) }} --}}
                @if ($users_permissions_roles['count'] > 0)
                    @foreach ($users_permissions_roles['rows'] as $role)
                    <tr class="text-center">
                        
                        @foreach(array_keys($role) as $key)
                            @if (in_array($key, ['id', 'name']))
                                <td>{{ $role[$key] }}</td>
                            @elseif (in_array($key, ['created_at', 'updated_at']))
                            
                            @else
                                <td id="users_permissions_roles-{{ $role['id'] }}-{{ $key }}" style="vertical-align: middle;">@if ((int)$role[$key] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                @else 
                    <tr><td colspan=6>No roles found.</td></tr>
                @endif
                <tr class="theme-tableOuter">
                    <td colspan=100%>
                        <button class="btn btn-success" onclick="modalLoadAddPermPreset()"><i class="fa fa-plus"></i> Add</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>