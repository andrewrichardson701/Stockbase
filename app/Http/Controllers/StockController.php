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
use App\Models\TransactionModel;

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

        // if the modify_type isnt set to a valid type, and the id is 0, go to the add page
        if ((empty($modify_type) || (!empty($modify_type) && !in_array($modify_type, $modify_types))) && $stock_id == 0) {
            return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => 'add']);
        }

        $page = $request['page'] ?? 1;

        $params = ['stock_id' => $stock_id, 'modify_type' => $modify_type, 'page' => $page];

        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);

        if ($stock_id != 0) {
            $stock_data = StockModel::getStockData($stock_id);

            $favourited = StockModel::checkFavourited($stock_id);
            $stock_inv_data = StockModel::getStockInvData($stock_id, (int)$stock_data['is_cable']);
            $stock_item_data = StockModel::getStockItemData($stock_id, (int)$stock_data['is_cable']);
            $stock_distinct_item_data = StockModel::getDistinctStockItemData($stock_id, (int)$stock_data['is_cable']);
            $serial_numbers = StockModel::getDistinctSerials($stock_id);
            $container_data = StockModel::getAllContainerData($stock_id);
            $transactions = TransactionModel::getTransactions($stock_id, 5, $page);
            
        }

        $transactions['view'] = 'stock';

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer', 0));

        return view('stock', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'stock_data' => $stock_data ?? null,
                                'stock_inv_data' => $stock_inv_data ?? null,
                                'stock_item_data' => $stock_item_data ?? null,
                                'stock_distinct_item_data' => $stock_distinct_item_data ?? null,
                                'favourited' => $favourited ?? null,
                                'serial_numbers' => $serial_numbers ?? null,
                                'container_data' => $container_data ?? null,
                                'manufacturers' => $manufacturers ?? null,
                                'sites' => $sites ?? null,
                                'areas' => $areas ?? null,
                                'shelves' => $shelves ?? null,
                                'transactions' => $transactions ?? null,
                                ]);
    }
}
