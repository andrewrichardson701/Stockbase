<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\CablestockModel;
use App\Models\ResponseHandlingModel;
use App\Models\PropertiesModel;

class CablestockController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'cables'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $cable_types = GeneralModel::formatArrayOnIdAndCount(CablestockModel::getCableTypesByParent());
        $cables = GeneralModel::formatArrayOnIdAndCount(CablestockModel::getCablesByName());
        $q_data = CablestockModel::queryData($request); // query string data

        return view('cablestock', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'cable_types' => $cable_types,
                                'cables' => $cables,
                                'q_data' => $q_data,
                            ]);
    }

    static public function modifyCableStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock-id' => 'integer|required',
                'cable-item-id' => 'integer|required',
                'action' => 'string|required'
            ]);

            $redirect_array = GeneralModel::getURLQuery(GeneralModel::previousURL());

            $data = $request->toArray();
            $push = CablestockModel::adjustQuantity($data['stock-id'], $data['cable-item-id'], $data['action'], 1);

            if (isset($push['errors']) && !empty($push['errors'])) {
                $errortext = explode('\n', $push['erorrs']);
                return redirect(GeneralModel::previousURL())->with('error', $errortext);
            } elseif (isset($push['success'])) {
                $redirect_array['stock_id'] = $data['stock-id'];
                $redirect_array['item_id'] = $data['cable-item-id'];
                $redirect_array['action'] = $data['action'];
                $redirect_array['success'] = $push['success'];
                return redirect()->route('cablestock', $redirect_array);
            } else {
                return redirect(GeneralModel::previousURL())->with('error', value: 'Undefined error in '. __FILE__ .' Line: '. __LINE__ .'.');
            }

        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function moveCableStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'current_i' => 'integer|nullable', // row in the table on the move page
                'current_stock' => 'required|integer',
                'current_shelf' => 'required|integer',
                'current_cost' => 'numeric|nullable',

                'site' => 'integer|required',
                'area' => 'integer|required',
                'shelf' => 'integer|required',
                'quantity' => 'integer|required',
            ]);

            return CablestockModel::moveStockCable($request);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function addCableStock(Request $request) 
    {
        // dd($request->file());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'site' => 'integer|required',
                'area' => 'integer|required',
                'shelf' => 'integer|required',
                'stock-name' => 'string|required',
                'stock-description' => 'string|nullable',
                'cable-type' => 'integer|required',
                'stock-min-stock' => 'integer|required',
                'item-quantity' => 'integer|required',
                'item-cost' => 'numeric|required',
            ]);

            return CablestockModel::addCableStock($request);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function addCableType(Request $request) 
    {
        // dd($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'type' => 'string|required', // the table field to update in the db
                'property_name' => 'string|required', // the name to go in the db
                'description' => 'string|required', // description to go in the db
                'parent' => 'string|required', // parent to go in the db 
            ]);

            $return = PropertiesModel::addProperty($request);
            if (!str_contains($return, 'Error')) {
                $key = 'success';
            } else {
                $key = 'error';
            }
            return redirect(GeneralModel::previousURL())->with($key, $return);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function checkCableQuantity(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required', 
                'shelf_id' => 'integer|required'
            ]);
            return response()->json(CablestockModel::checkCableQuantity($request->input()));
        } else {
            return response()->json('Error: CSRF missmatch');
        }
    }
}
