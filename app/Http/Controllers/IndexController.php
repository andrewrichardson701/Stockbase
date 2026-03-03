<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\ResponseHandlingModel;


use App\Models\StockModel;
use App\Models\WebhookModel;
use App\Models\PropertiesModel;

use App\Models\SmtpModel;
use App\Services\EmailService;

use Illuminate\Support\Facades\DB;

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

    public function test(Request $request)
    {
        // [##USER_USERNAME##, ##USER_NAME##, ##BASE_URL##, ##SYSTEM_NAME##, ##STOCK_URL_TEXT##, 
        // ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##, ##QUANTITY##, ##NEW_QUANTITY##, ##STOCK_ID##, 
        // ##STOCK_NAME##, ##STOCK_DESCRIPTION##, ##STOCK_SKU##, ##STOCK_MIN_STOCK##, ##SITE_NAME_OLD##, 
        // ##AREA_NAME_OLD##, ##SHELF_NAME_OLD##, ##SITE_NAME_NEW##, ##AREA_NAME_NEW##, ##SHELF_NAME_NEW##, 
        // ##STOCK_NAME_OLD##, ##STOCK_DESCRIPTION_OLD##, ##STOCK_SKU_OLDU##, ##STOCK_TAGS_OLD##, ##STOCK_MIN_STOCK_NEW##,
        // ##STOCK_NAME_NEW##, ##STOCK_DESCRIPTION_NEW##, ##STOCK_SKU_NEW##, ##STOCK_TAGS_NEW##, ##STOCK_RESTORE_URL_TEXT##, 
        // ##IMAGE_ID##, ##IMAGE_NAME##, ##IMAGE_URL_TEXT##]

        $data = [
            'stock_id' => 1,
            'site_id' => 1,
            'area_id' => 1,
            'shelf_id' => 1,
            'quantity' => 100,
            'new_quantity' => 999,
            'old_quantity' => 50,
            'site_id_old' => 1,
            'area_id_old' => 1,
            'shelf_id_old' => 1,
            'site_id_new' => 1,
            'area_id_new' => 1,
            'shelf_id_new' => 1,
            'site_name_old' => 'old',
            'area_name_old' => 'old',
            'shelf_name_old' => 'old',
            'site_name_new' => 'new',
            'area_name_new' => 'new',
            'shelf_name_new' => 'new',
            'stock_name_old' => 'old',
            'stock_description_old' => 'old',
            'stock_sku_old' => 'old',
            'stock_min_stock_old' => 'old',
            'stock_tags_old' => 'old',
            'stock_name_new' => 'new',
            'stock_description_new' => 'new',
            'stock_sku_new' => 'new',
            'stock_min_stock_new' => 'new',
            'stock_tags_new' => 'new',
            'img_name' => 'image.jpeg',
            'img_id' => 69,
        ];
        SmtpModel::notificationEmail(1, 1, $data);
        // dd(
        //     WebhookModel::notificationWebhook(1, 1, $data), 
        //     WebhookModel::notificationWebhook(1, 2, $data),     
        //     WebhookModel::notificationWebhook(1, 3, $data), 
        //     WebhookModel::notificationWebhook(1, 4, $data), 
        //     WebhookModel::notificationWebhook(1, 5, $data), 
        //     WebhookModel::notificationWebhook(1, 6, $data), 
        //     WebhookModel::notificationWebhook(1, 7, $data), 
        //     WebhookModel::notificationWebhook(1, 8, $data), 
        //     WebhookModel::notificationWebhook(1, 9, $data), 
        //     WebhookModel::notificationWebhook(1, 10, $data), 
        //     WebhookModel::notificationWebhook(1, 11, $data), 
        //     WebhookModel::notificationWebhook(1, 12, $data), 
        //     WebhookModel::notificationWebhook(1, 13, $data), 
        // );


    }
}

