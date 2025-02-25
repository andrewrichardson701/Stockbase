<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;


use App\Models\StockModel;

class IndexController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'index'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        // $head_data = GeneralModel::headData();
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

    static public function test(Request $request, $stock_id, $modify_type = null)
    {
        $nav_highlight = 'stock'; // for the nav highlighting
$stock_id=126;
        $modify_types = ['add', 'remove', 'edit', 'move'];

        // handle redirection on invalid modify type
        if (!empty($modify_type) && !in_array($modify_type, $modify_types)) {
            $modify_type = null;
            return redirect()->route('stock', ['stock_id' => $stock_id]);
        }

        $params = ['stock_id' => $stock_id, 'modify_type' => $modify_type];
        
        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $stock_data = StockModel::getStockData($stock_id);

        $favourited = StockModel::checkFavourited($stock_id);

        $stock_inv_data = StockModel::getStockInvData($stock_id, $stock_data['is_cable']);

        $stock_item_data = StockModel::getStockItemData($stock_id, $stock_data['is_cable']);

        $serial_numbers = StockModel::getDistinctSerials($stock_id);

        $container_data = StockModel::getAllContainerData($stock_id);
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer'));
        
        dd('stock', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'stock_data' => $stock_data,
                                'stock_inv_data' => $stock_inv_data,
                                'stock_item_data' => $stock_item_data,
                                'favourited' => $favourited,
                                'serial_numbers' => $serial_numbers,
                                'container_data' => $container_data,
                                'manufacturers' => $manufacturers,
                                ]);
    }
}
