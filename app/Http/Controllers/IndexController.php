<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\ResponseHandlingModel;


use App\Models\StockModel;
use App\Models\TagModel;
use App\Models\PropertiesModel;

use App\Models\SmtpModel;
use App\Services\EmailService;

class IndexController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'index'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        // $head_data = GeneralModel::headData();
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer', 0));
        $tags = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag', 0));
        $q_data = IndexModel::queryData($request); // query string data

        return view('index', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,
                                'manufacturers' => $manufacturers,
                                'tags' => $tags,
                                'q_data' => $q_data,
                            ]);
    }

    static public function error(Request $request)
    {
        $request = $request->all(); // turn request into an array
        return view('error');
    }

    static public function addFirstLocations(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'site-name' => 'string|required',
                'site-description' => 'string|required',
                'area-name' => 'string|required',
                'area-description' => 'string|required',
                'shelf-name' => 'string|required',                
            ]);
            return PropertiesModel::addFirstLocations($request->input());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    // static public function test(Request $request)
    // {
    //     // dd(ProfileModel::getLoginHistory());

    //     $data = StockModel::getStockNotInContainer(1, ['item.manufacturer_id' => 1, 'item.shelf_id' => 1, 'item.serial_number' => '']);
    //     dd($data);
    // }

    public function test(Request $request, EmailService $mailer)
    {
        $config = GeneralModel::configCompare();
        $user = GeneralModel::getUser();
        $template_info = SmtpModel::getTemplateInfo(1);
        $temp = $new_tags_array = TagModel::getTagsForStock(1) ?? [];
        dd ($temp);
        // if ($template_info !== false) {
        //     $array = [
        //         'to' => $user['email'],
        //         'toName' => $user['name'],
        //         'fromName' => 'use-default',
        //         'subject' => SmtpModel::convertVariables($template_info->subject),
        //         'body' => SmtpModel::buildEmail(SmtpModel::convertVariables($template_info->body)),
        //         'notif_id' => $request['email_notification_id'] // notif_id
        //     ];
        //     // echo($array['body']);
        //     // dd($array);
        //     $mail = $mailer->sendEmail(
        //         "admin@ajrich.co.uk",
        //         $user['name'],
        //         'use-default',
        //         SmtpModel::convertVariables($template_info->subject),
        //         SmtpModel::buildEmail(SmtpModel::convertVariables($template_info->body)),
        //         1 // notif_id
        //     );  
        //     echo $mail;    
        // } else {
        //     return 'Unable to find template';
        // }
    }
}

