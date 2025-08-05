<div style="padding-bottom:0px">
    <div class="container">
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="emailtemplates-settings" onclick="toggleSection(this, 'emailtemplates')">Email Templates <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    </div>
    <!-- Notification Settings -->
    <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="emailtemplates" hidden>

        @include('includes.response-handling', ['section' => 'emailtemplates-settings'])

        @if (isset($email_templates) && !empty($email_templates['rows']))
        <table class="table table-dark theme-table centertable">
            <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border-width:1px; border-color: #565758; border-style:solid">
                <tr style="border:0px !important">
                @foreach($email_templates['rows'] as $template)
                    <th class="clickable th-noBorder templateHeading @if($loop->first) th-selected @endif" id="template-{{ $template['slug'] }}-heading" onclick="changeTemplate('{{ $template['slug'] }}', this)">{{ $template['name'] }}</th>
                @endforeach                    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan=100% class="theme-th-selected">
                    @foreach($email_templates['rows'] as $template)
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
                                            <th class="align-middle" style="text-align:right;border: none;white-space: nowrap;">From:</th>
                                            <td class="align-middle" style="text-align:left;border: none;">
                                                <input class="form-control theme-input-alt" type="text" name="from" value="{{ $head_data['config_compare']['smtp_from_name'] }} <{{ $head_data['config_compare']['smtp_from_email'] }}>" disabled>
                                            </td>
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
        <p class="red">No email templates found. Please add in the email_templates table.</p>
        @endif

        <div id="modalDivViewTemplate" class="modal" style="display: none;">
            <span class="close" onclick="modalCloseViewTemplate()">×</span>
            <div class="container well-nopad theme-divBg" style="padding:25px">
                <iframe id="emailTemplateView" frameborder="0" style="display:block;overflow:hidden;min-height:400px;width:100%" height="100%" width="100%" src="{{ route('admin.smtpTemplate') }}?template=echo&body={{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\''.url('stock').'/1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}"></iframe>
                <input type="hidden" value="{{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\''.url('stock').'/=1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}" id="email-template-body" />
                <a style="margin-left:5px" href="{{ route('admin.emailTemplatePreview') }}?template_id={{ $template['id'] }}'" target="_blank">View in new tab</a>
            </div>
        </div>
    
    </div>
</div>
{{-- {{ dd($email_templates) }} --}}