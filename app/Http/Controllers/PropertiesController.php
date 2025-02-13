<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ContainersModel;
use App\Models\PropertiesModel;
use App\Models\ResponseHandlingModel;

class PropertiesController extends Controller
{
    //
    static public function addProperty(Request $request) 
    {
        $previous_url = GeneralModel::previousURL();
        // get all to check for a match
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'type' => 'required',
                'description' => 'string',
                'property_name' => 'required',
                'area_id' => 'integer',
                'site_id' => 'integer'
            ]);

            return PropertiesModel::addProperty($request->input());
        } else {
            return redirect()->GeneralModel::redirectURL($previous_url, ['error' => 'csrfMissmatch']);
        }
    }
}
