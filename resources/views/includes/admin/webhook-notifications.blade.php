<div style="padding-bottom:0px">
    <div class="container" style="padding-bottom:0px">
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="webhook-settings" onclick="toggleSection(this, 'webhook')">Webhook Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    </div>
    <!-- Webhook Settings -->
    <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="webhook" hidden>

        @include('includes.response-handling', ['section' => 'webhook-settings'])

        {{-- 
            Enable/Disable
            Webhook type
            Friendly name
            Webhook URL
            Bot Display Name
            Prefix Custom Message
            
            Testing
        --}}
        <div class="container">
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
                        <tr class="nav-row" style="margin-top:20px" id="webhook-friendly-name">
                            <td id="webhook-friendly-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="friendly-name">Friendly Name:</p>
                            </td>
                            <td id="webhook-friendly-name-input">
                                <input class="form-control nav-v-c theme-input" style="width: 250px" id="friendly-name" name="webhook_friendly_name" value="{{ $head_data['config']['webhook_friendly_name'] }}" required>
                            </td>
                            <td id="webhook-friendly-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="friendly-name-default" >{{ $head_data['default_config']['webhook_friendly_name'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-url">
                            <td id="webhook-url-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="url">Webhook URL:</p>
                            </td>
                            <td id="webhook-url-input">
                                <input class="form-control nav-v-c theme-input" style="width: 250px" id="url" name="webhook_url" value="{{ $head_data['config']['webhook_url'] }}" required>
                            </td>
                            <td id="webhook-url-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="url-default" >{{ $head_data['default_config']['webhook_url'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-display-name">
                            <td id="webhook-display-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="display-name">Display Name:</p>
                            </td>
                            <td id="webhook-display-name-input">
                                <input class="form-control nav-v-c theme-input" style="width: 250px" id="display-name" name="webhook_display_name" value="{{ $head_data['config']['webhook_display_name'] }}" required>
                            </td>
                            <td id="webhook-display-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="display-name-default" >{{ $head_data['default_config']['webhook_display_name'] }}</p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="webhook-prefix-message">
                            <td id="webhook-prefix-message-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="prefix-message">Prefix Message:</p>
                            </td>
                            <td id="webhook-prefix-message-input">
                                <input class="form-control nav-v-c theme-input" style="width: 250px" id="prefix-message" name="webhook_prefix_message" value="{{ $head_data['config']['webhook_prefix_message'] }}" required>
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
        </div>

        <hr style="border-color:white; margin-left:10px">

        @if (isset($webhook_templates) && !empty($webhook_templates['rows']))
        <table class="table table-dark theme-table centertable">
            <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border-width:1px; border-color: #565758; border-style:solid">
                <tr style="border:0px !important">
                @foreach($webhook_templates['rows'] as $template)
                    <th class="clickable th-noBorder templateHeading @if($loop->first) th-selected @endif" id="template-{{ $template['slug'] }}-heading" onclick="changeTemplate('{{ $template['slug'] }}', this)">{{ $template['name'] }}</th>
                @endforeach                    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan=100% class="theme-th-selected">
                    @foreach($webhook_templates['rows'] as $template)
                        <div class="theme-table templateDiv" style="width:100%" id="template-{{ $template['slug'] }}-div" @if(!$loop->first) hidden @endif>
                            <form id="template-{{ $template['slug'] }}-form" enctype="multipart/form-data" action="{{ route('admin.emailTemplate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template['id'] }}">
                                <input type="hidden" name="slug" value="{{ $template['slug'] }}">
                                <div class="container theme-table">
                                    <table class="table table-dark theme-table centertable" id="template-{{ $template['slug'] }}-table" style="padding-bottom:0px;margin-bottom:0px;border: none;">
                                        <tr class="vertical-align align-middle" style="border: none;white-space: nowrap;">
                                            <td class="blue align-middle" style="border: none;"" colspan=100%>{{ $template['description'] }}</td>
                                        </tr>
                                        <tr class="vertical-align align-middle" style="border: none;">
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;">Subject:</th>
                                            <td class="align-middle" style="text-align:left;border: none;">
                                                <input class="form-control theme-input" type="text" name="subject" value="{{ $template['subject'] }}">
                                            </td>
                                        </tr>
                                        <tr class="vertical-align align-middle" style="border: none;">
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;">Email Content:</th>
                                            <td class="align-middle" style="text-align:left;border: none;">
                                                <textarea class="form-control theme-input" rows="6" type="text" name="body" value="{{ $template['body'] }}">{{ $template['body'] }}</textarea>
                                            </td>
                                        </tr>
                                        <tr class="vertical-align align-middle" style="border: none;">
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;">Variables:</th>
                                            <td class="align-middle" style="text-align:left;border: none;">
                                                {{ $template['variables'] }}
                                            </td>
                                        </tr>
                                        <tr class="vertical-align align-middle" style="border: none;">
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;"></th>
                                            <td class="align-middle" style="text-align:left;border: none;">
                                                <button class="btn btn-success" name="submit" value="update" type="submit">Update</button>
                                                <button class="btn btn-info" style="margin-left:20px" type="button" onclick="modalLoadViewTemplate('{{ $template['id'] }}')">View Template</button>
                                                <button class="btn btn-danger" style="margin-left:20px" name="submit" value="restore" type="submit">Restore Default</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                        </div>
                    @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
        @else
        <p class="red">No webhook templates found. Please add in the webhook_templates table.</p>
        @endif
        
    </div>
</div>