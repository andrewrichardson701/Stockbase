<div style="padding-bottom:0px">
    <div class="container">
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="changelog-settings" onclick="toggleSection(this, 'changelog')">Changelog <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    </div>
    <!-- Changelog -->
    <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="changelog" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'changelog')) {
        //     showResponse();
        // }
        ?>
        {!! $response_handling !!}

        <div class="content">
            @if ($changelog['count'] > 0)
            <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th>id</th>
                        <th style="min-width: 110px;">timestamp</th>
                        <th>user_id</th>
                        <th>user_username</th>
                        <th>action</th>
                        <th>table_name</th>
                        <th>record_id</th>
                        <th>field_name</th>
                        <th>value_old</th>
                        <th>value_new</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($changelog['rows'] as $log)
                    <tr>
                        <td>{{ $log['id'] }}</td>
                        <td>{{ $log['timestamp'] }}</td>
                        <td>{{ $log['user_id'] }}</td>
                        <td>{{ $log['user_username'] }}</td>
                        <td>{{ $log['action'] }}</td>
                        <td>{{ $log['table_name'] }}</td>
                        <td>{{ $log['record_id'] }}</td>
                        <td>{{ $log['field_name'] }}</td>
                        <td>{{ $log['value_old'] }}</td>
                        <td>{{ $log['value_new'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a class="clickable" href="changelog.php">Full Changelog</a>
            @endif
        </div>
    </div>
</div>