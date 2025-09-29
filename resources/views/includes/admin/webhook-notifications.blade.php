<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="webhook-settings" onclick="toggleSection(this, 'webhook')">Webhook Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- Webhook Settings -->
    <div style="padding-top: 20px" id="webhook" hidden>

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
        <p class="red">No email templates found. Please add in the webhook_templates table.</p>
        @endif
        
    </div>
</div>