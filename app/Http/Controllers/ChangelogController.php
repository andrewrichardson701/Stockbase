<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\ResponseHandlingModel;
use App\Models\ChangelogModel;
use App\Models\User;

class ChangelogController extends Controller
{
    //
    static public function index(Request $request, $start_date = null, $end_date = null, $table = 'all', $user = 'all', $page = 1): View|RedirectResponse  
    {
        // dd($page);
        $nav_highlight = 'changelog'; // for the nav highlighting
        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

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

        $start_date_time = $start_date . ' 00:00:00'; // add time so that it can filter for the same day
        $end_date_time = $end_date . ' 23:59:59'; // add time so that it can filter for the same day

        $db_tables = GeneralModel::getDbTableNames(1);
        $db_users = GeneralModel::formatArrayOnIdAndCount(User::get()->toArray());
        $changelog_params = [];
        $changelog_params['start_date'] = ['key' => 'timestamp', 'operator' => '>=', 'value' => date('Y-m-d H:i:s', strtotime($start_date_time))];
        $changelog_params['end_date'] = ['key' => 'timestamp', 'operator' => '<=', 'value' => date('Y-m-d H:i:s', strtotime($end_date_time))];
        if ($table !== 'all') {
            if (in_array($table, $db_tables)) {
                $changelog_params['table_name'] = ['key' => 'table_name', 'operator' => '=', 'value' => $table];
            }
        }
        if ($user !== 'all') {
            if (array_key_exists($user, $db_users)) {
                $changelog_params['user_id'] = ['key' => 'user_id', 'operator' => '=', 'value' => $user];
            }
        }
        
        $limit = 50;
        $offset = 0;

        $changelog_total_count = count(ChangelogModel::getChangelog(null, 0, $changelog_params));
        $page_count = (int)ceil($changelog_total_count/$limit);

        if (!is_numeric((int)$page)){
            $page = 1;
        }

        if ($page > $page_count) {
            $page = $page_count;
        }

        if ($page > 1) {
            $offset = ($page-1)*$limit;
        }
        
        $changelog = GeneralModel::formatArrayOnIdAndCount(ChangelogModel::getChangelogFull($limit, $offset, $changelog_params));
        $changelog['total_count'] = $changelog_total_count;
        
        $changelog['pages'] = $page_count;

        $params = ['start_date' => $start_date, 'end_date' => $end_date, 'table' => $table, 'user' => $user, 'page' => $page, 'pages' => $page_count, 'offset'=> $offset, 'limit' => $limit, 'request' => $request];

        return view('changelog', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'changelog' => $changelog,
                                'tables' => $db_tables,
                                'users' => $db_users,
                                ]);
    }

    static public function filterChangelog(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'start-date' => 'date|nullable',
                'end-date' => 'date|nullable',
                'userid' => 'nullable',
                'table' => 'string|nullable',
                'page' => 'integer|nullable',
            ]);
            $request = $request->toArray();
            $page = isset($request['page']) ? $request['page'] : 1;
            return redirect()->route('changelog', ['start_date' => $request['start-date'], 'end_date' => $request['end-date'], 'table' => $request['table'], 'user' => $request['userid'], 'page' => $page]);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }
}
