<div style="padding-bottom:0px">
            <div class="container">
                <h3 class="clickable" style="margin-top:50px;font-size:22px" id="users-settings" onclick="toggleSection(this, 'users')">Users <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            </div>
            <!-- Users Settings -->
            <div class="align-middle text-center" style="margin-left:5vw;margin-right:5vw; padding-top: 20px" id="users" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'users')) {
                //     showResponse();
                // }
                ?>
                @include('includes.response-handling', ['section' => 'users-settings'])
                <table id="usersTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr id="users_table_info_tr" hidden>
                            <td colspan=8 id="users_table_info_td"></td>
                        </tr>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Auth</th>
                            <th>Enabled</th>
                            <th>Password</th>
                            <th>2FA</th>
                            @if ($head_data['user']['role_id'] == 1) <th></th> @endif
                        </tr>
                    </thead>
                    <tbody>
                    @if ($users['count'] < 1)
                        <tr><td colspan=9><or class="red">No Users in table: `users`.</or></td></tr>
                    @else
                        @foreach ($users['rows'] as $user)
                        <tr class="text-center" style="vertical-align: middle;">
                            <td id="user_{{ $user['id'] }}_id" style="vertical-align: middle;">{{ $user['id'] }}</td>
                            <td id="user_{{ $user['id'] }}_username" style="vertical-align: middle;">{{ $user['username'] }}</td>
                            <td id="user_{{ $user['id'] }}_name" style="vertical-align: middle;">{{ $user['name'] }}</td>
                            <td id="user_{{ $user['id'] }}_email" style="vertical-align: middle;">{{ $user['email'] }}</td>
                            <td id="user_{{ $user['id'] }}_role" style="vertical-align: middle;">
                                <select class="form-control theme-dropdown" id="user_{{ $user['id'] }}_role_select" style="min-width:max-content; padding-top:0px; padding-bottom:0px" onchange="userRoleChange('{{ $user['id'] }}')" @if ((int)$user['id'] == 1) disabled @endif >
                                @foreach ($user_roles['rows'] as $role)
                                    <option value="{{ $role['id'] }}" title="{{ $role['description'] }}" @if ($user['role_id'] == $role['id']) selected @endif @if ((int)$role['id'] == 1) disabled @endif>{{ ucwords($role['name']) }}</option>
                                @endforeach
                                </select>
                            </td>
                            <td id="user_{{ $user['id'] }}_auth" style="vertical-align: middle;">{{ $user['auth'] }}</td>
                            <td style="vertical-align: middle;"><input type="checkbox" id="user_{{ $user['id'] }}_enabled_checkbox" @if ($user['enabled'] == 1) checked @endif onchange="usersEnabledChange('{{ $user['id'] }}')" @if((int)$user['id'] == 1 || ((int)$user['id'] == (int)$head_data['user']['id'])) title="Unable to disable this user" style="cursor:not-allowed" disabled @endif></td>
                            <td style="vertical-align: middle;">
                                <button class="btn btn-warning" style="padding: 2px 6px 2px 6px" id="user_{{ $user['id'] }}_pwreset" onclick="resetPassword('{{ $user['id'] }}')" 
                                    @if ($user['auth'] == 'ldap') 
                                        {{-- user to show is an ldap user, cant reset  --}}
                                        disabled
                                    @elseif ((int)$user['role_id'] == 1 || (int)$user['role_id'] == 3)
                                        {{-- role of user to show is 1 or 3 (root or admin) --}}
                                        @if ((int)$user['id'] == 1)
                                            {{-- user to show is root user --}}
                                            disabled
                                        @elseif ((int)$head_data['user']['role_id'] !== 1)
                                            {{-- current user isnt root --}}
                                            disabled
                                            {{-- This means admin user passwords can only be reset by the root user --}}
                                        @endif
                                    @endif
                                >Reset</button>
                            </td>
                            <td style="vertical-align: middle;">
                            @if ($user['id'] !== 1)
                                <button class="btn btn-primary" id="reset_2fa" style="padding: 2px 6px 2px 6px" onclick="modalLoadReset2FA({{ $user['id'] }})">Reset 2FA</button>
                            @endif
                            </td>
                            @if ((int)$head_data['user']['role_id'] == 1)
                            <td style="vertical-align: middle;">   
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" style="padding:0px;margin:0px">
                                    @csrf
                                    <button type="submit" style="padding:2px 8px 2px 8px" class="btn btn-info" id="user_{{ $user['id'] }}_impersonate" title="Impersonate" @if ((int)$user['id'] == (int)$head_data['user']['id']) disabled @endif ><i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
                                    <input type="hidden" name="user-impersonate" value="impersonate"/>
                                    <input type="hidden" name="role" value="Root" />
                                    <input type="hidden" name="user-id" value="{{ $user['id'] }}" />
                                </form>
                            </td>
                            @endif
                        @endforeach
                    @endif
                        <tr class="theme-tableOuter"><td></td><td colspan="100%"><button class="btn btn-success" type="button" onclick="navPage('addlocaluser');"><i class="fa fa-plus"></i> Add</button></td></tr>
                    </tbody>
                </table>
            </div>
            <div id="modalDivReset2FA" class="modal" style="display: none;">
                <span class="close" onclick="modalCloseReset2FA()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div style="margin:auto;text-align:center;margin-top:10px">
                        <form action="{{ route('admin.userSettings') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="reset_2fa_submit" value="admin" />
                            <input type="hidden" name="user_id" id="2fareset_user_id" value=""/>
                            <p>Are you sure you want to reset the 2FA for <or class="green" id="2fareset_username"></or>?<br>
                            This will prompt a reset on the user's next login.</p>
                            <span>
                                <button class="btn btn-danger" type="submit" name="submit" value="1">Reset</button>
                                <button class="btn btn-warning" type="button" onclick="modalCloseReset2FA()">Cancel</button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>