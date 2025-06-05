<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="usersroles-settings" onclick="toggleSection(this, 'usersroles')">User Roles <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Users Roles -->
    <div style="padding-top: 20px" id="usersroles" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'users-roles')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'usersroles-settings'])
        <table id="usersTable" class="table table-dark theme-table" style="max-width:max-content">
            <thead>
                <tr class="text-center theme-tableOuter">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="title" title="Can access the Optics page">Optics</th>
                    <th>Administrator</th>
                    <th>Root</th>
                </tr>
            </thead>
            <tbody>
            @if ($user_roles['count'] > 0)
                @foreach ($user_roles['rows'] as $role)
                <tr class="text-center">
                    <td>{{ $role['id'] }}</td>
                    <td>{{ $role['name'] }}</td>
                    <td>{{ $role['description'] }}</td>
                    <td style="vertical-align: middle;">@if ($role['is_optic'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                    <td style="vertical-align: middle;">@if ($role['is_admin'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                    <td style="vertical-align: middle;">@if ($role['is_root'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                </tr>
                @endforeach
            @else 
                <tr><td colspan=6>No roles found.</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>