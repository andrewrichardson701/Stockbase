<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="sso-settings" onclick="toggleSection(this, 'sso')">SSO Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- SSO Settings -->
    <div style="padding-top: 20px" id="sso" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'sso-settings')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'sso-settings'])

        <form id="ssoToggleForm" enctype="multipart/form-data" action="{{ route('admin.ssoToggle') }}" method="POST">
            @csrf
            <input type="hidden" name="sso-toggle-submit" value="set" />
            <table id="ssoToggleTable">
                <tbody>
                    <tr class="nav-row" id="sso-headings" style="margin-bottom:10px">
                        <td style="width:150px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="saml_enabled">Enable SSO</p>
                            </td>
                        <td class="align-middle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="saml_enabled" id="sso-enabled-toggle" @if ($head_data['config']['saml_enabled'] == 1) checked @endif >
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <form id="ssoForm" enctype="multipart/form-data" action="{{ route('admin.ssoSettings') }}" method="POST" @if ($head_data['config']['saml_enabled'] == 0) hidden @endif >
            @csrf
            <hr style="border-color:white; margin-left:10px">
            <table id="ssoTable">
                <tbody>
                    <tr class="nav-row" id="sso-headings" style="margin-bottom:10px; margin-right:10px">
                        <th style="width:250px;margin-left:25px"></th>
                        <th style="width: 250px">Custom</th>
                        <th style="margin-left:25px">Default</th>
                    </tr>
                    <tr class="nav-row" id="sso-auth-username">
                        <td id="sso-auth-username-label" style="width:250px;margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Tenant ID:</p>
                        </td>
                        <td id="sso-auth-username-input">
                            <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="saml_tenant_id" name="saml_tenant_id" value="{{ $response_data['saml_tenant_id'] ?? $head_data['config']['saml_tenant_id'] }}" required>
                        </td>
                        <td id="sso-auth-username-default-cell" style="margin-left:25px">
                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="saml_tenant_id_default">{{ $head_data['default_config']['saml_tenant_id'] }}</p>
                        </td>
                    </tr>
                    <tr class="nav-row" style="margin-top:20px">
                        <td style="width:250px">
                            <input id="sso-submit" type="submit" name="sso-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                        </td>
                        <td style="width:250px">
                            <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" href="{{ route('saml.login', ['uuid' => 'm365']) }}" target="_blank">Test SSO</a>
                        </td>
                        <td style="margin-left:25px">
                            <input id="sso-restore-defaults" type="submit" name="sso-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>