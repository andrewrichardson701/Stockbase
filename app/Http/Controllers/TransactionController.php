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
    static public function index(Request $request, $stock_id = null): View|RedirectResponse
    {
        $nav_highlight = 'stock'; // for the nav highlighting
        
        $page = $request['page'];
        $params = ['stock_id' => $stock_id, 'page' => $page];
        
        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $transactions = TransactionModel::getTransactions($stock_id, 100, $page);
        $transactions['view'] = 'transactions';
        
        $stock_data = StockModel::getStockData($stock_id) ?? null;

        return view('transactions', ['params' => $params,
                                    'nav_data' => $nav_data,
                                    'response_handling' => $response_handling,
                                    'stock_data' => $stock_data,
                                    'transactions' => $transactions
                                    ]);
    }
}
