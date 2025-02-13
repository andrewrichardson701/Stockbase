<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ContainersModel;
use App\Models\ResponseHandlingModel;

class ContainersController extends Controller
{
    //
    static public function index(Request $request) 
    {
        $nav_highlight = 'containers'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);
        
        $container_data = ContainersModel::compileContainers();

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));

        return view('containers', ['nav_data' => $nav_data,
                                    'response_handling' => $response_handling,
                                    'container_data' => $container_data,
                                    'sites' => $sites,
                                ]);
    }

    static public function addContainer(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'container_name' => 'required',
                'container_description' => 'required',
                'site' => 'required|integer',
                'area' => 'required|integer',
                'shelf' => 'required|integer'
            ]);

            return ContainersModel::addContainer($request->input());
        } else {
            return null;
        }
    
    }

    static public function deleteContainer(Request $request)
    {   
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'container_id' => 'integer|required'
            ]);
            return ContainersModel::deleteContainer($request->input());
        } else {
            return null;
        }
    }

    static public function editContainer(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'container_name' => 'string|required',
                'container_description' => 'string|required',
                'container_id' => 'integer|required'
            ]);
            return ContainersModel::editContainer($request->input());
        } else {
            return null;
        }
    }

    static public function unlinkFromContainer(Request $request) 
    {
        $request->validate([
            'item_id' => 'integer|required'
        ]);
        return ContainersModel::unlinkFromContainer($request->input());
    }

    static public function linkToContainer(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'is_item' => 'integer|required',
                'stock_id' => 'integer|required',
                'container_id' => 'integer|required',
                'item_id' => 'integer|required'
            ]);
            return ContainersModel::linkToContainer($request->input());
        } else {
            return null;
        }
    }
}
