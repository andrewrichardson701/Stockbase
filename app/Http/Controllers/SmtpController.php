<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\SmtpModel;
use App\Models\GeneralModel;

class SmtpController extends Controller
{
    //
    static public function template(Request $request) 
    {
        $body = $request->body ?? '';
        $template_usage = "Usage: ?template=echo&body=&lt;p&gt;Body text&lt;/p&gt;";
        if ($request->template) {
            if ($request->template == 'echo') {
                echo(SmtpModel::templateTest($body)); 
            } else {
                echo('<or class="red">AJAX request failed... Incorrect Template.</or><br>'.$template_usage);
            }
        } else {
            echo('Error: Unknown state.');
        }
        
    }
}
