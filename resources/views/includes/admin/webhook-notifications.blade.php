<div class="container-fluid" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="webhook-settings">Webhook Notification Settings</h3> 

    <!-- Webhook Settings -->
    <div class="adminContent" id="webhook">

        @include('includes.response-handling', ['section' => 'webhook-settings'])


        <div class="container-fluid">
            <form id="webhookToggleForm" enctype="multipart/form-data" action="{{ route('admin.webhookSettings') }}" method="POST">
                @csrf
                <input type="hidden" name="webhook-toggle-submit" value="set" />
                <table id="webhookToggleTable">
                    <tbody>
                        <tr class="nav-row" id="webhook-headings" style="margin-bottom:10px">
                            <td style="width:150px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Enable Webhooks</p>
                                </td>
                            <td class="align-middle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                    <input type="checkbox" name="webhook-enabled" id="webhook-enabled-toggle" @if ($head_data['config']['webhook_enabled'] == 1) checked @endif >
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        @if ($head_data['config']['webhook_enabled'] == 1)
            <form id="webhookForm" enctype="multipart/form-data" action="{{ route('admin.webhookSettings') }}" method="POST" @if ($head_data['config']['webhook_enabled'] == 0) hidden @endif >
                @csrf
                <hr style="border-color:white; margin-left:10px">
                <table id="webhookTable">
                    <tbody>
                        <tr class="nav-row" id="webhook-headings" style="margin-bottom:10px; margin-right:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Custom</th>
                            <th style="margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row" id="webhook-webhook-type">
                            <td id="webhook-webhook-type-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="webhook-type">Webhook Type:</p>
                            </td>
                            <td id="webhook-webhook-type-input">
                                <select class="form-control nav-v-c theme-input" name="webhook_type" id="webhook-type" style="width: 250px" >
                                    <option value="" @if ($head_data['config']['webhook_type'] == "") selected @endif>None</option>
                                    <option value="slack" @if ($head_data['config']['webhook_type'] == "slack") selected @endif>Slack</option>
                                    <option value="discord" @if ($head_data['config']['webhook_type'] == "discord" ) selected @endif>Discord</option>
                                    <option value="teams" @if ($head_data['config']['webhook_type'] == "teams") selected @endif>Microsoft Teams</option>
                                </select>
                            </td>
                            <td id="webhook-webhook-type-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="webhook-type-default">
                                    @switch ($head_data['default_config']['webhook_type'])
                                        @case('slack')
                                            Slack
                                            @break
                                        @case('discord')
                                            Discord
                                            @break
                                        @case('teams')
                                            Teams
                                            @break
                                        @case ('')
                                            None
                                            @break
                                        @default
                                            {{ $head_data['default_config']['webhook_type'] }}
                                    @endswitch
                                </p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-friendly-name-tr">
                            <td id="webhook-friendly-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="webhook-friendly-name">Friendly Name:</p>
                            </td>
                            <td id="webhook-friendly-name-input">
                                <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="webhook-friendly-name" name="webhook_friendly_name" value="{{ $head_data['config']['webhook_friendly_name'] }}" required>
                            </td>
                            <td id="webhook-friendly-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="webhook-friendly-name-default" >{{ $head_data['default_config']['webhook_friendly_name'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-url-tr">
                            <td id="webhook-url-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="url">Webhook URL:</p>
                            </td>
                            <td id="webhook-url-input">
                                <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="webhook-url" name="webhook_url" value="{{ $head_data['config']['webhook_url'] }}" required>
                            </td>
                            <td id="webhook-url-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="url-default" >{{ $head_data['default_config']['webhook_url'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-avatar_url-tr">
                            <td id="webhook-avatar_url-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="webhook-avatar-url">Avatar URL:</p>
                            </td>
                            <td id="webhook-avatar_url-input">
                                <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="webhook-avatar-url" name="webhook_avatar_url" value="{{ $head_data['config']['webhook_avatar_url'] }}" required>
                            </td>
                            <td id="webhook-avatar_url-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="avatar-url-default" >{{ $head_data['default_config']['webhook_avatar_url'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-display-name-tr">
                            <td id="webhook-display-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="webhook-display-name">Display Name:</p>
                            </td>
                            <td id="webhook-display-name-input">
                                <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="webhook-display-name" name="webhook_display_name" value="{{ $head_data['config']['webhook_display_name'] }}" required>
                            </td>
                            <td id="webhook-display-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="display-name-default" >{{ $head_data['default_config']['webhook_display_name'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-prefix-message-tr">
                            <td id="webhook-prefix-message-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="webhook-prefix-message">Prefix Message:</p>
                            </td>
                            <td id="webhook-prefix-message-input">
                                <input class="form-control nav-v-c theme-input" type="text" style="width: 250px" id="webhook-prefix-message" name="webhook_prefix_message" value="{{ $head_data['config']['webhook_prefix_message'] }}">
                            </td>
                            <td id="webhook-prefix-message-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="prefix-message-default" >{{ $head_data['default_config']['webhook_prefix_message'] }}</p>
                            </td>
                        </tr>   
                        <tr class="nav-row" style="margin-top:20px">
                            <td style="width:250px">
                                <input id="webhook-submit" type="submit" name="webhook-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                            </td>
                            <td style="width:250px">
                                <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testWebhook()">Test config</a>
                                <i id="webhook-success-icon" class="fa-solid fa-check fa-lg" style="color: lime; margin-left:10px; display: none;" ></i>
                                <i id="webhook-fail-icon" class="fa-solid fa-xmark fa-lg" style="color: red; margin-left:10px; display: none;" ></i>
                                <i id="webhook-loading-icon" class="fa-solid fa-spinner fa-spin fa-lg" style="color: cyan; margin-left:10px; display: none;" ></i>
                            </td>
                            <td style="margin-left:25px">
                                <input id="webhook-restore-defaults" type="submit" name="webhook-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                            </td>
                        </tr>             
                    </tbody>
                </table>
            </form>

            <hr style="border-color:white; margin-left:10px; margin-bottom:20px">

            @if ($webhook_notifications['count'] > 0)
            <p id="webhooknotification-output" class="last-edit-T" hidden></p>
            <table>
                <tbody>
                @foreach ($webhook_notifications['rows'] as $notification)
                    @if ($loop->first)
                    <tr>
                    @endif
                    @if (($loop->iteration -1) %4 == 0)
                    </tr><tr>
                    @endif
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="webhookwebhooknotif-{{ $notification['id'] }}">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="{{ $notification['description'] }}">{{ $notification['title'] }}:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="webhooknotif-{{ $notification['id'] }}-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="{{ $notification['name'] }}" onchange="webhookNotification(this, {{ $notification['id'] }})" @if ($notification['enabled'] == 1) checked @endif @if($notification['id'] == 1) disabled @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)  @if($notification['id'] == 1) ;background-color:grey; cursor: not-allowed @endif"></span>
                            </label>
                        </td>
                    @if ($loop->last)
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            @else
            <p id="webhooknotification-output"><or class="red">No notifications settings found in table...</or></p>
            @endif
        @else
        <p class="blue">Webhooks are disabled. All webhook notifications have been disabled.</p>
        @endif
        </div>
    </div>
</div>