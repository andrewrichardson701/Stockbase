<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'ldap')">LDAP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- LDAP Settings -->
    <div style="padding-top: 20px" id="ldap" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'ldap-settings')) {
        //     showResponse();
        // }
        ?>
        {!! $response_handling !!}

        <?php

        // if (isset($_GET['ldapUpload'])) {
        //     echo ('<p id="success-output" class="green" style="margin-left:25px">');
        //     if ($_GET['ldapUpload'] == 'success') { echo('LDAP config uploaded!'); }
        //     if ($_GET['ldapUpload'] == 'configRestored') { echo('LDAP config restored to defaults!'); }
        //     echo('</p>');
        // }
        ?>
        <form id="ldapToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
            @csrf
            <input type="hidden" name="ldap-toggle-submit" value="set" />
            <table id="ldapToggleTable">
                <tbody>
                    <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                        <td style="width:150px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable LDAP</p>
                            </td>
                        <td class="align-middle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="ldap-enabled" id="ldap-enabled-toggle" @if ($head_data['config']['ldap_enabled'] == 1) checked @endif >
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        
        <form id="ldapForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" @if ($head_data['config']['ldap_enabled'] == 0) hidden @endif >
            @csrf
            <hr style="border-color:white; margin-left:10px">
            <table id="ldapTable">
                <tbody>
                    <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px; margin-right:10px">
                        <th style="width:250px;margin-left:25px"></th>
                        <th style="width: 250px">Custom</th>
                        <th style="margin-left:25px">Default</th>
                    </tr>
                    <tr class="nav-row" id="ldap-auth-username">
                        <td id="ldap-auth-username-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Authentication Username:</p>
                        </td>
                        <td id="ldap-auth-username-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-username" name="auth-username" value="{{ $response_data['auth-username'] ?? $head_data['config']['ldap_username'] }}" required>
                        </td>
                        <td id="ldap-auth-username-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-username-default">{{ $head_data['default_config']['ldap_username'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password">
                        <td id="ldap-auth-password-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password">Authentication Password:</p>
                        </td>
                        <td id="ldap-auth-password-input">
                            <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password" name="auth-password" value="password" required>
                        </td>
                        <td id="ldap-auth-password-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-password-default" @if ($head_data['default_config']['ldap_password'] !== '') type="password" @endif>@if ($head_data['default_config']['ldap_password'] == '') Default missing... @else {{ $head_data['default_config']['ldap_password'] }} @endif</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password-confirm">
                        <td id="ldap-auth-password-confirm-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password-confirm">Confirm Password:</p>
                        </td>
                        <td id="ldap-auth-passowrd-confirm-input">
                            <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password-confirm" name="auth-password-confirm" value="password" required>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-domain">
                        <td id="ldap-auth-domain-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-domain">Domain:</p>
                        </td>
                        <td id="ldap-auth-domain-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-domain" name="auth-domain" value="{{ $response_data['auth-domain'] ?? $head_data['config']['ldap_domain'] }}" required>
                        </td>
                        <td id="ldap-auth-domain-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-domain-default">{{ $head_data['default_config']['ldap_domain'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                        <td id="ldap-auth-host-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host">Host:</p>
                        </td>
                        <td id="ldap-auth-host-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host" name="auth-host" value="{{ $response_data['auth-host'] ?? $head_data['config']['ldap_host'] }}" required>
                        </td>
                        <td id="ldap-auth-host-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-default">{{ $head_data['default_config']['ldap_host'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host-secondary">
                        <td id="ldap-auth-host-secondary-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host-secondary">Secondary Host:</p>
                        </td>
                        <td id="ldap-auth-host-secondary-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host-secondary" name="auth-host-secondary" value="{{ $response_data['auth-host-secondary'] ?? $head_data['config']['ldap_host_secondary'] }}">
                        </td>
                        <td id="ldap-auth-host-secondary-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-secondary-default">{{ $head_data['default_config']['ldap_host_secondary'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-port">
                        <td id="ldap-auth-port-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-port">Port:</p>
                        </td>
                        <td id="ldap-auth-port-input">
                            <input class="form-control nav-v-c" type="number" style="width: 250px" id="auth-port" name="auth-port" value="{{ $response_data['auth-port'] ?? $head_data['config']['ldap_port'] }}" required>
                        </td>
                        <td id="ldap-auth-port-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-port-default">{{ $head_data['default_config']['ldap_port'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-basedn">
                        <td id="ldap-auth-basedn-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-basedn">Base DN:</p>
                        </td>
                        <td id="ldap-auth-basedn-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-basedn" name="auth-basedn" value="{{ $response_data['auth-basedn'] ?? $head_data['config']['ldap_basedn'] }}">
                        </td>
                        <td id="ldap-auth-basedn-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-basedn-default">{{ $head_data['default_config']['ldap_basedn'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-usergroup">
                        <td id="ldap-auth-usergroup-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-usergroup">User Group:</p>
                        </td>
                        <td id="ldap-auth-usergroup-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-usergroup" name="auth-usergroup" value="{{ $response_data['auth-usergroup'] ?? $head_data['config']['ldap_usergroup'] }}">
                        </td>
                        <td id="ldap-auth-usergroup-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-usergroup-default">{{ $head_data['default_config']['ldap_usergroup'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px" id="ldap-auth-userfilter">
                        <td id="ldap-auth-userfilter-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-userfilter">User Filter:</p>
                        </td>
                        <td id="ldap-auth-userfilter-input">
                            <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-userfilter" name="auth-userfilter" value="{{ $response_data['auth-userfilter'] ?? $head_data['config']['ldap_userfilter'] }}">
                        </td>
                        <td id="ldap-auth-userfilter-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-userfilter-default">{{ $head_data['default_config']['ldap_userfilter'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px">
                        <td style="width:250px">
                            <input id="ldap-submit" type="submit" name="ldap-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                        </td>
                        <td style="width:250px">
                            <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testLDAP()">Test config</a>
                        </td>
                        <td style="margin-left:25px">
                            <input id="ldap-restore-defaults" type="submit" name="ldap-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>