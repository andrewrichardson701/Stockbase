<div class="container" style="padding-bottom:0px">   
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="smtp-settings" onclick="toggleSection(this, 'smtp')">SMTP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- SMTP Settings -->
    <div style="padding-top: 20px" id="smtp" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'smtp-settings')) {
        //     showResponse();
        // }
        ?>
        {!! $response_handling !!}
        <form id="smtpToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
            @csrf
            <input type="hidden" name="smtp-toggle-submit" value="set" />
            <table id="smtpToggleTable">
                <tbody>
                    <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                        <td style="width:150px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable SMTP</p>
                            </td>
                        <td class="align-middle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="smtp-enabled" id="smtp-enabled-toggle" @if ($head_data['config']['smtp_enabled'] == 1) checked @endif >
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <form id="smtpForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" @if ($head_data['config']['smtp_enabled'] == 0) hidden @endif >
            @csrf
            <hr style="border-color:white; margin-left:10px">
            <table id="smtpTable">
                <tbody>
                    <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                        <th style="width:250px;margin-left:25px"></th>
                        <th style="width: 250px">Custom</th>
                        <th style="margin-left:25px">Default</th>
                    </tr>
                    <tr class="nav-row" id="smtp-host-row">
                        <td id="smtp-host-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-host">SMTP Host:</p>
                        </td>
                        <td id="smtp-host-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-host" name="smtp-host" value="{{ $head_data['config']['smtp_host'] }}" required>
                        </td>
                        <td id="smtp-host-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-host-default">{{ $head_data['default_config']['smtp_host'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-port-row" style="margin-top:20px">
                        <td id="smtp-port-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-port">SMTP Port:</p>
                        </td>
                        <td id="smtp-port-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-port" name="smtp-port" value="{{ $head_data['config']['smtp_port'] }}" required>
                        </td>
                        <td id="smtp-port-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-port-default">{{ $head_data['default_config']['smtp_port'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-encryption-row" style="margin-top:20px">
                        <td id="smtp-encryption-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-encryption">SMTP Encryption:</p>
                        </td>
                        <td id="smtp-encryption-input">
                            <select id="smtp-encryption" name="smtp-encryption" style="width:250px" class="form-control nav-v-c" required>
                                <option value="none" @if ($head_data['config']['smtp_encryption'] == '' || $head_data['config']['smtp_encryption'] == null) selected @endif>None</option>
                                <option value="starttls" @if ($head_data['config']['smtp_encryption'] == 'starttls') selected @endif>STARTTLS</option>
                                <option value="tls" @if ($head_data['config']['smtp_encryption'] == 'tls') selected @endif>Transport Layer Security (TLS)</option>
                                <option value="ssl" @if ($head_data['config']['smtp_encryption'] == 'ssl') selected @endif>Secure Sockets Layer (SSL)</option>
                            </select>
                        </td>
                        <td id="smtp-encryption-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-encryption-default">
                                @switch($head_data['default_config']['smtp_encryption'])
                                    @case('none')
                                    @case('')
                                    @case(null)
                                        None
                                        @break

                                    @case('starttls')
                                        STARTTLS
                                        @break

                                    @case('tls')
                                        Transport Layer Security (TLS)
                                        @break

                                    @case('ssl')
                                        Secure Sockets Layer (SSL)
                                        @break

                                    @default
                                        None
                                @endswitch
                            </p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-username-row" style="margin-top:20px">
                        <td id="smtp-username-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-username">SMTP Username:</p>
                        </td>
                        <td id="smtp-username-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-username" name="smtp-username" value="{{ $head_data['config']['smtp_username'] }}">
                        </td>
                        <td id="smtp-username-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-username-default">{{ $head_data['default_config']['smtp_username']  }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-password-row" style="margin-top:20px">
                        <td id="smtp-password-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-password">SMTP Password:</p>
                        </td>
                        <td id="smtp-password-input">
                            <input class="form-control nav-v-c" type="password" style="width: 250px" id="smtp-password" name="smtp-password" value="password">
                        </td>
                        <td id="smtp-password-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-password-default"><or class="green">{{ $head_data['default_config']['smtp_password'] }}</or></p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-from-email-row" style="margin-top:20px">
                        <td id="smtp-from-email-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-email">SMTP From Email:</p>
                        </td>
                        <td id="smtp-from-email-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-email" name="smtp-from-email" value="{{ $head_data['config']['smtp_from_email'] }}" required>
                        </td>
                        <td id="smtp-from-email-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-email-default">{{ $head_data['default_config']['smtp_from_email'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-from-name-row" style="margin-top:20px">
                        <td id="smtp-from-name-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-name">SMTP From Name:</p>
                        </td>
                        <td id="smtp-from-name-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-name" name="smtp-from-name" value="{{ $head_data['config']['smtp_from_name'] }}" required>
                        </td>
                        <td id="smtp-from-name-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-name-default">{{ $head_data['default_config']['smtp_from_name'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" id="smtp-backup-to-row" style="margin-top:20px">
                        <td id="smtp-backup-to-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-backup-to">SMTP To Email (Backup):</p>
                        </td>
                        <td id="smtp-backup-to-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-backup-to" name="smtp-backup-to" value="{{ $head_data['config']['smtp_to_email'] }}" required>
                        </td>
                        <td id="smtp-backup-to-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-backup-to-default">{{ $head_data['default_config']['smtp_to_email'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px">
                        <td style="width:250px">
                            <input id="smtp-submit" type="submit" name="smtp-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                        </td>
                        <td style="width:250px">
                            <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testSMTP()">Test config</a>
                            <i id="smtp-success-icon" class="fa-solid fa-check fa-lg" style="color: lime; margin-left:10px; display: none;" ></i>
                            <i id="smtp-fail-icon" class="fa-solid fa-xmark fa-lg" style="color: red; margin-left:10px; display: none;" ></i>
                            <i id="smtp-loading-icon" class="fa-solid fa-spinner fa-spin fa-lg" style="color: cyan; margin-left:10px; display: none;" ></i>
                        </td>
                        <td style="margin-left:25px">
                            <input id="smtp-restore-defaults" type="submit" name="smtp-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>