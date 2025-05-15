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
use App\Models\ChangelogModel;
use App\Models\User;

class ChangelogController extends Controller
{
    //
    static public function index(Request $request, $start_date = null, $end_date = null, $table = null, $user = null): View|RedirectResponse  
    {
        $nav_highlight = 'changelog'; // for the nav highlighting
        
        $page = $request['page'] ?? 1;
        $limit = 50;
        $offset = 0;
        if ($page > 1) {
            $offset = $page-1*$limit;
        }

        if ($start_date == null || GeneralModel::validateDate($start_date) == false) {
            $start_date = date("Y-m-d", strtotime('-2 weeks'));
        }
        if ($end_date == null || GeneralModel::validateDate($end_date) == false) {
            $end_date = date("Y-m-d");
        }

        $start_date .= ' 00:00:00'; // add time so that it can filter for the same day
        $end_date .= ' 23:59:59'; // add time so that it can filter for the same day

        // if end date is before start date, make them the same
        if ($end_date < $start_date) {
            $end_date = $start_date;
        }

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $params = ['start_date' => $start_date, 'end_date' => $end_date, 'table' => $table, 'user' => $user, 'page' => $page, 'request' => $request];
        
        $db_tables = GeneralModel::getDbTableNames(1);
        $db_users = GeneralModel::formatArrayOnId(User::get()->toArray());
        $changelog_params = [];
        $changelog_params['start_date'] = ['key' => 'timestamp', 'operator' => '>=', 'value' => date('Y-m-d H:i:s', strtotime($start_date))];
        $changelog_params['end_date'] = ['key' => 'timestamp', 'operator' => '<=', 'value' => date('Y-m-d H:i:s', strtotime($end_date))];
        if ($table !== null) {
            if (in_array($table, $db_tables)) {
                $changelog_params['table_name'] = ['key' => 'table_name', 'operator' => '=', 'value' => $table];
            }
        }
        if ($user !== null) {
            if (array_key_exists($user, $db_users)) {
                $changelog_params['user_id'] = ['key' => 'user_id', 'operator' => '=', 'value' => $user];
            }
        }

        $changelog = GeneralModel::formatArrayOnIdAndCount(ChangelogModel::getChangelogFull($limit, $offset, $changelog_params));
        $changelog_total_count = count(ChangelogModel::getChangelog(null, 0, $changelog_params));
        $changelog['total_count'] = $changelog_total_count;
        $page_count = (int)ceil($changelog_total_count/$limit);
        $changelog['pages'] = $page_count;

        // needed info:
        //// total row count of all pages
        //// total page count (total row count / limit)

        return view('changelog', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'changelog' => $changelog,
                                'tables' => $db_tables,
                                'users' => $db_users,
                                ]);
    }
}
