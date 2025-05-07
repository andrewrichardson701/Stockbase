<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\PropertiesModel;
use App\Models\StockModel;
use App\Models\CablestockModel;
use App\Models\ContainersModel;
use App\Models\FavouritesModel;
// use App\Models\ResponseHandlingModel;

class AjaxController extends Controller
{
    //
    public function getStockAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $request = $request->all(); // turn request into an array
        $stock = StockModel::returnStockAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getCablesAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $request = $request->all(); // turn request into an array
        $stock = CablestockModel::returnCablesAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getNearbyStockAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $request = $request->all(); // turn request into an array
        $stock = StockModel::getNearbyStockAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getSelectBoxes(Request $request)
    {
        $request = $request->all(); // turn request into an array
        
        if (isset($request['site'])) {
            $areas = AjaxController::getSelectBoxAreas(htmlspecialchars($request['site']));

            return $areas;
        }

        if (isset($request['area'])) {
            $shelves = AjaxController::getSelectBoxShelves(htmlspecialchars($request['area']));

            return $shelves;
        }
        
        if (isset($request['getremoveshelves']) && isset($request['manufacturer']) && isset($request['stock'])) {
            $shelves = AjaxController::getSelectBoxShelvesByManufacturer($request['manufacturer'], $request['stock']);

            return $shelves;
        }
    }

    public function getSelectBoxAreas($site) 
    {
        if (is_numeric($site) && $site > 0) {
            $areas = [];

            $areas = GeneralModel::allDistinctAreas($site, 0);

            if ($areas !== null) {
                return $areas;
            } else {
                return null;
            }
        }
    }

    public function getSelectBoxShelves($area) 
    {
        if (is_numeric($area) && $area > 0) {
            $shelves = [];

            $shelves = GeneralModel::allDistinctShelves($area, 0);

            if ($shelves !== null) {
                return $shelves;
            } else {
                return null;
            }
        }
    }

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
            $redirect = GeneralModel::redirectURL($previous_url, ['error' => 'csrfMissmatch']);
            return redirect()->$redirect;
        }
    }

    static public function loadProperty(Request $request)
    {
        $type = $request['type'];

        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'type' => 'required',
                'load_property' => 'required',
                'submit' => 'required'
            ]);
        }

        $where['deleted'] = 0;

        
        if (isset($request['container_shelf']) && $request['container_shelf'] > 0) {
            // loading containers on the add page
            $return = ContainersModel::getContainersByShelf($request['container_shelf']) ?? [];
        } else {
            $return = GeneralModel::getAllWhere($type, $where, 'name') ?? [];
        }

        
        return $return;
    }

    static public function getSelectBoxShelvesByManufacturer($manufacturer_id, $stock_id) 
    {
        if (is_numeric($manufacturer_id) && $manufacturer_id > 0) {
            $shelves = [];

            $shelves = GeneralModel::allDistinctShelvesByManufacturer($manufacturer_id, $stock_id, 0);

            if ($shelves !== null) {
                return $shelves;
            } else {
                return null;
            }
        }
    }

    static public function favouriteStock(Request $request)
    {
        $request->validate([
            'stock_id' => 'integer|required'
        ]);
        $stock_id = $request->stock_id;
        $user = GeneralModel::getUser();

        $favourited = StockModel::checkFavourited($stock_id);
        
        if ($favourited == 1) {
            FavouritesModel::removeFavourite($user['id'], $stock_id);
        } else {
            FavouritesModel::addFavourite($user['id'], $stock_id);
        }        
    }

}
