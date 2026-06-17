<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\SmtpModel;
use App\Models\GeneralModel;
use App\Services\EmailService;

class SmtpController extends Controller
{
    //
    static public function template(Request $request) 
    {
        $body = $request['body'] ?? '';
        $template_usage = "Usage: ?template=echo&body=&lt;p&gt;Body text&lt;/p&gt;";

        $template = $request->template ?? false;

        if ($template) {
            if ($template == 'echo') {
                echo(SmtpModel::buildEmail($body, 1)); 
            } else {
                echo('<or class="red">AJAX request failed... Incorrect Template.</or><br>'.$template_usage);
            }
        } else {
            echo('Error: Unknown state.');
        }
        
    }

    public static function smtpTest(Request $request)
    {
        SmtpModel::smtpTest($request->input());
    }

    public static function emailTemplatePreview(Request $request)
    {
        $template_id = $request['template_id'];
        return SmtpModel::emailTemplatePreview($template_id);
    }

    public static function getEmailTemplateUrl(Request $request)
    {
        $template_id = $request['template_id'];

        $url = route('admin.emailTemplatePreview').'?template_id='.$template_id;

        echo $url;
    }

}
