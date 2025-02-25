<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\StockModel;
use App\Models\ResponseHandlingModel;

class StockController extends Controller
{
    //
    static public function index(Request $request, $stock_id, $modify_type = null): View|RedirectResponse  
    {
        $nav_highlight = 'stock'; // for the nav highlighting

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

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        
        return view('stock', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'stock_data' => $stock_data,
                                'stock_inv_data' => $stock_item_data,
                                'stock_item_data' => $stock_item_data,
                                'favourited' => $favourited,
                                'serial_numbers' => $serial_numbers,
                                'container_data' => $container_data,
                                'manufacturers' => $manufacturers,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,
                                ]);
    }
}
