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

        if (isset($request['getcontainers'])) {
            $containers = AjaxController::getSelectBoxInUseContainers($request['shelf'], $request['manufacturer'], $request['stock']);
            return $containers;
        }

        if (isset($request['getserials'])) {
            $serials = AjaxController::getSelectBoxRemoveSerials($request['shelf'], $request['manufacturer'], $request['stock'], $request['container']);
            return $serials;
        }

        if (isset($request['getquantity'])) {
            $quantity = AjaxController::getSelectBoxRemoveQuantity($request['shelf'], $request['manufacturer'], $request['stock'], $request['container'], $request['serial']);
            return $quantity;
        }

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

    public function getSelectBoxRemoveSerials($shelf_id, $manufacturer_id, $stock_id, $container_id)
    {
        $return = [];
        if (is_numeric($stock_id) && $stock_id > 0) {
            if (is_numeric($shelf_id) && $shelf_id > 0) {
                if (is_numeric($manufacturer_id) && $manufacturer_id > 0) {

                    if (!is_numeric($container_id)) {
                        $container_id = 0;
                    } 

                    if ($container_id < 0) {
                        $container_id = $container_id * -1;
                        $is_item = 1;
                    } else {
                        $is_item = 0;
                    }

                    if ($container_id !== 0) {
                        $data = ContainersModel::getContainerChildrenInfo($container_id, ['item.manufacturer_id' => $manufacturer_id, 'item.shelf_id' => $shelf_id, 'item.stock_id' => $stock_id, 'item_container.container_is_item' => $is_item]);
                        foreach($data as $sn) {
                            $return[] = ['id' => $sn['id'], 'serial_number' => $sn['serial_number']];
                        }
                    }

                }
            }
        }

        return $return;

    }

    public function getSelectBoxRemoveQuantity($shelf_id, $manufacturer_id, $stock_id, $container_id, $serial_number)
    {
        $return = array(0 => ['quantity' => 0 , 'data' => []]);

        if (is_numeric($stock_id) && $stock_id > 0) {
            if (is_numeric($shelf_id) && $shelf_id > 0) {
                if (is_numeric($manufacturer_id) && $manufacturer_id > 0) {

                    if (!is_numeric($container_id)) {
                        $container_id = 0;
                    } 

                    if ($container_id < 0) {
                        $container_id = $container_id * -1;
                        $is_item = 1;
                    } else {
                        $is_item = 0;
                    }

                    if ($container_id !== 0) {
                        $data = ContainersModel::getContainerChildrenInfo($container_id, ['item.manufacturer_id' => $manufacturer_id, 'item.shelf_id' => $shelf_id, 'item.stock_id' => $stock_id, 'item_container.container_is_item' => $is_item, 'item.serial_number' => $serial_number]);
                        $return[0]['quantity'] = count($data);
                        $return[0]['data'] = $data;
                    }

                }
            }
        }

        return $return;

    }

    public function getSelectBoxInUseContainers($shelf_id, $manufacturer_id, $stock_id) 
    {
        
        if (is_numeric($stock_id) && $stock_id > 0) {
            if (is_numeric($shelf_id) && $shelf_id > 0) {
                if (is_numeric($manufacturer_id) && $manufacturer_id > 0) {
                    $containers = [];

                    $containers = ContainersModel::getContainersInUse($shelf_id, $manufacturer_id, $stock_id);
                    if (ContainersModel::checkForUncontaineredItems($shelf_id, $manufacturer_id, $stock_id, count($containers)) == 1) {
                        $newEntry = [
                            "c_id" => null,
                            "c_name" => 'No container',
                            "c_description" => null,
                            "ic_id" => null,
                            "ic_item_id" => null,
                            "ic_container_id" => null,
                            "ic_container_is_item" => null,
                            "icontainer_id" => null,
                            "scontainer_id" => null,
                            "scontainer_name" => null,
                            "scontainer_description" => null,
                            "i_id" => null,
                            "c_sh_id" => null,
                            "i_sh_id" => null,
                            "c_location" => null,
                            "i_location" => null,
                            "s_id" => null,
                            "s_name" => "---",
                            "s_description" => null,
                            "object_count" => null,
                            "simgcontainer_id" => null,
                            "simgcontainer_image" => null,
                            "simg_id" => null,
                            "simg_image" => null
                        ];
                        
                        array_unshift($containers, $newEntry);
                    }

                    if ($containers !== null) {
                        return $containers;
                    } else {
                        return null;
                    }
                }
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
        if($request['_token'] == csrf_token()) {
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
        } else {
            return null;
        }
              
    }

}
