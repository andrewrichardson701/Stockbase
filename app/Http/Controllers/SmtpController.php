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

    public function testEmail(EmailService $mailer)
    {
        $mailer->sendEmail(
            'recipient@example.com',
            'Recipient Name',
            'use-default',
            'Test Subject',
            '<p>This is a test email</p>',
            1 // notif_id
        );
    }

    public function notificationEmail(Request $request, EmailService $mailer)
    {
        $config = GeneralModel::configCompare();
        $user = GeneralModel::getUser();
        $template_info = SmtpModel::getTemplateInfo($request['template_id']);

        if ($template_info !== false) {
            $mailer->sendEmail(
                $user['email'],
                $user['name'],
                'use-default',
                SmtpModel::convertVariables($template_info->subject),
                SmtpModel::buildEmail(SmtpModel::convertVariables($template_info->body)),
                $request['email_notification_id'] // notif_id
            );  
        } else {
            return 'Unable to find template';
        }
    }
}
