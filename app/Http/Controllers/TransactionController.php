<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\StockModel;
use App\Models\ResponseHandlingModel;
use App\Models\TransactionModel;

class TransactionController extends Controller
{
    //
    static public function index(Request $request, $type = null, $stock_id = null): View|RedirectResponse
    {
        $nav_highlight = 'transactions'; // for the nav highlighting

        $page = $request['page'];
        $params = ['stock_id' => $stock_id, 'page' => $page, 'type' => $type];

        $type_array = [
            'stock', 
            'cables', 
            'optics', 
            // 'cpus', 
            'memory', 
            'disks', 
            // 'fans', 
            // 'cpus'
        ];
        
        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $stock = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock'));

        $stock_data = StockModel::getStockData($stock_id) ?? null;
        if (!isset($stock_data['is_cable'])) {
            $stock_data['is_cable'] = 0;
        }

        if ($type == 'cables') {
            $stock_data['is_cable'] = 1;
        }

        if (!in_array($type, $type_array)) {
            return redirect()->to(route('transactions', ['type' => 'stock']))->with('error', 'Unknown Type');
        } 
        
        $transactions = TransactionModel::getTransactions($type, $stock_id, $stock_data['is_cable'], 100, $page);

        $transactions['view'] = 'transactions';
        $q_data = TransactionModel::queryData($request); // query string data

        return view('transactions', ['params' => $params,
                                    'nav_data' => $nav_data,
                                    'response_handling' => $response_handling,
                                    'stock_data' => $stock_data,
                                    'stock_id' => $stock_id,
                                    'stock' => $stock,
                                    'transactions' => $transactions,
                                    'q_data' => $q_data
                                    ]);
    }
}
