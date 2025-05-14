<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\StockModel;
use App\Models\ResponseHandlingModel;
use App\Models\TransactionModel;

class ChangelogController extends Controller
{
    //
    static public function index(Request $request, $start_date = null, $end_date = null, $table = null, $user = null): View|RedirectResponse  
    {
        $nav_highlight = 'changelog'; // for the nav highlighting

        $page = $request['page'] ?? 1;

        if ($start_date == null || GeneralModel::validateDate($start_date) == false) {
            $start_date = date("Y-m-d", strtotime('-2 weeks'));
        }
        if ($end_date == null || GeneralModel::validateDate($end_date) == false) {
            $end_date = date("Y-m-d");
        }

        // if end date is before start date, make them the same
        if ($end_date < $start_date) {
            $end_date = $start_date;
        }

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $params = ['start_date' => $start_date, 'end_date' => $end_date, 'table' => $table, 'user' => $user, 'page' => $page, 'request' => $request];
        
        return view('changelog', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                ]);
    }
}
