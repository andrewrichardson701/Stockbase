<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="sessionmanagement-settings" onclick="toggleSection(this, 'sessionmanagement')">Session Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Session Management -->
    <div style="padding-top: 20px" id="sessionmanagement" hidden>

        @include('includes.response-handling', ['section' => 'sessionmanagement-settings'])
        <table id="sessionsTable" class="table table-dark theme-table" style="max-width:max-content">
            <thead>
                <tr id="sessions_table_info_tr" hidden>
                    <td colspan=8 id="sessions_table_info_td"></td>
                </tr>
                <tr class="text-center theme-tableOuter">
                    <th>ID</th>
                    <th>Session ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>IP</th>
                    <th>Login Time</th>
                    <th hidden>Logout Time</th>
                    <th>Last Activity</th>
                    <th>Browser</th>
                    <th>OS</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @if ($active_sessions['count'] > 0) 
                @foreach ($active_sessions['rows'] as $session)
                <tr class="text-center" style="vertical-align: middle;">
                    <form action="{{ route('admin.killUserSession') }}" method="POST">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $session['sl_sessions_id']}}" />
                        <td id="sessions_{{ $session['id'] }}_id" style="vertical-align: middle;">{{ $session['id'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_id" style="vertical-align: middle;">{{ $session['sl_sessions_id'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_user_id" style="vertical-align: middle;">{{ $session['sl_user_id'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_username" style="vertical-align: middle;">{{ $session['u_username'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_ip" style="vertical-align: middle;">{{ $session['sl_ip'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_login_time" style="vertical-align: middle;">{{ $session['sl_login_time'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_logout_time" style="vertical-align: middle;" hidden>{{ $session['sl_logout_time'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_last_activity" style="vertical-align: middle;">{{ $session['sl_last_activity'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_browser" style="vertical-align: middle;">{{ $session['sl_browser'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_os" style="vertical-align: middle;">{{ $session['sl_os'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_status" style="vertical-align: middle;">{{ $session['sl_status'] }}</td>
                        <td id="sessions_{{ $session['id'] }}_kill" style="vertical-align: middle;"><input type="submit" class="btn btn-danger" name="session-kill-submit" value="Kill" @if ($head_data['session']['id'] == $session['sl_sessions_id']) title="Current Session" disabled @endif></td>
                    </form>
                </tr>
                @endforeach  
            @else
                <tr colspan="100%"><td>No active sessions.</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>