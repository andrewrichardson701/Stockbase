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
        $body = $request->body ?? '';
        $template_usage = "Usage: ?template=echo&body=&lt;p&gt;Body text&lt;/p&gt;";
        if ($request->template) {
            if ($request->template == 'echo') {
                echo(SmtpModel::buildEmail($body, 1)); 
            } else {
                echo('<or class="red">AJAX request failed... Incorrect Template.</or><br>'.$template_usage);
            }
        } else {
            echo('Error: Unknown state.');
        }
        
    }

    public function smtpTest(Request $request)
    {
        // https://laravel.ajrich.co.uk/admin.smtpTest?smtp_to_email=admin@ajrich.co.uk&smtp_to_name=Admin&smtp_from_name=Test&smtp_from_email=admin@ajrich.co.uk&smtp_password=DropsBuildsSkill12!!&smtp_username=admin@ajrich.co.uk&smtp_encryption=starttls&smtp_port=587&smtp_host=mail.ajrich.co.uk&debug=1&debug=1&notif_id=1
        
        // echo('test');
        SmtpModel::smtpTest($request->input());

    }

}
