<div style="padding-bottom:0px">
    <div class="container-fluid" style="padding-bottom:0px">
        <h3 style="margin-top:50px;font-size:22px" id="webhooktemplates-settings">Webhook Templates</h3> 
    </div>
    <!-- Webhook Settings -->
    <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="webhooktemplates">

        @include('includes.response-handling', ['section' => 'webhooktemplates-settings'])

        @if (isset($webhook_templates) && !empty($webhook_templates['rows']))
        <table class="table table-dark theme-table centertable">
            <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border-width:1px; border-color: #565758; border-style:solid">
                <tr style="border:0px !important">
                @foreach($webhook_templates['rows'] as $template)
                    <th class="clickable th-noBorder webhooktemplateHeading @if($loop->first) th-selected @endif" id="webhooktemplate-{{ $template['slug'] }}-heading" onclick="changeWebhookTemplate('{{ $template['slug'] }}', this)">{{ $template['name'] }}</th>
                @endforeach                    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan=100% class="theme-th-selected">
                    @foreach($webhook_templates['rows'] as $template)
                        <div class="theme-table webhooktemplateDiv" style="width:100%" id="webhooktemplate-{{ $template['slug'] }}-div" @if(!$loop->first) hidden @endif>
                            <form id="webhooktemplate-{{ $template['slug'] }}-form" enctype="multipart/form-data" action="{{ route('admin.webhookTemplate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template['id'] }}">
                                <input type="hidden" name="slug" value="{{ $template['slug'] }}">
                                <div class="container theme-table">
                                    <table class="table table-dark theme-table centertable" id="webhooktemplate-{{ $template['slug'] }}-table" style="padding-bottom:0px;margin-bottom:0px;border: none;">
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
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;">Message Content:</th>
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