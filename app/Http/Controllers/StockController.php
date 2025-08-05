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
use App\Models\CablestockModel;
use App\Models\ItemModel;

class StockController extends Controller
{
    //
    static public function index(Request $request, $stock_id, $modify_type = null, $add_new = null, $search = null): View|RedirectResponse  
    {
        $nav_highlight = 'stock'; // for the nav highlighting

        $modify_types = ['add', 'remove', 'edit', 'move'];

        // filtering urls for stock
        // general rule:
        if ($stock_id < 0) {
            // route to stock/0/<modify_type>
            if (isset($modify_type)) {
                return redirect()->route('stock', ['stock_id' => 0, 'modify_type' => $modify_type]); 
            } else {
                return redirect()->route('stock', ['stock_id' => 0, 'modify_type' => 'add']);
            }
        }
        if (in_array($modify_type, $modify_types)) {
            if ($modify_type == 'add') { // Adding of stock
                // all non specified stock queries
                if ((int)$stock_id == 0) {
                    if ($add_new == 'new') {
                        // this will be for brand new stock
                        //  stock/0/add/new
                        // no redirect needed
                    } elseif ($add_new == '-') {
                        // only valid for $search being set
                        //    stock/0/add/-/search
                        if ($search !== null) {
                            // this is for the search page
                            //    stock/0/add/-/search 
                            // no redirect needed this page is valid
                        } else {
                            // route to stock/0/type
                            return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => $modify_type]);
                        }
                    } elseif ($add_new !== null || $search !== null) {
                        return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => $modify_type]);
                    } 
                } else {
                    if ($add_new !== null || $search !== null) {
                        // if the queries are filled, they shouldnt be, clear them
                        return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => $modify_type]);
                    }
                }
            } elseif ($modify_type == 'edit') {
                if ($stock_id <= 0 || !is_numeric($stock_id)) {
                    return redirect()->route('stock', ['stock_id' => 0, 'modify_type' => 'add']);
                }
            } else {
                if ($add_new !== null || $search !== null) {
                    // if the queries are filled, they shouldnt be, clear them
                    return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => $modify_type]);
                }
            }
        } elseif ($modify_type !== null) {
            // if there is a modify_type set and it isnt expected, throw back to index
            return redirect()->route('stock', ['stock_id' => $stock_id]);
        } elseif ($stock_id == 0 && $modify_type == null) {
            // if you hit /stock/0 -> go to /stock/0/add
            return redirect()->route('stock', ['stock_id' => $stock_id, 'modify_type' => 'add']);
        }


        $page = $request['page'] ?? 1;

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $params = ['stock_id' => $stock_id, 'modify_type' => $modify_type, 'page' => $page, 'add_new' => $add_new, 'search' => $search, 'request' => $request];
        
        if ($stock_id > 0 && is_numeric($stock_id)) {
            $stock_data = StockModel::getStockData($stock_id);
            if (isset($stock_data['id'])) {
                $favourited = StockModel::checkFavourited($stock_id);
                $stock_inv_data = StockModel::getStockInvData($stock_id, (int)$stock_data['is_cable']);
                $stock_item_data = StockModel::getStockItemData($stock_id, (int)$stock_data['is_cable']);
                $stock_distinct_item_data = StockModel::getDistinctStockItemData($stock_id, (int)$stock_data['is_cable']);
                $serial_numbers = StockModel::getDistinctSerials($stock_id);
                $container_data = StockModel::getAllContainerData($stock_id);
                $transactions = TransactionModel::getTransactions($stock_id, (int)$stock_data['is_cable'], 5, $page);
                $tagged = GeneralModel::formatArrayOnIdAndCount($stock_inv_data['tags']) ?? [];
                $untagged = GeneralModel::formatArrayOnIdAndCount(GeneralModel::getAllWhereNotIn('tag', ['id' => array_keys($tagged) ?? []]));
                $tag_data = ['tagged' => $tagged, 'untagged' => $untagged];
                if (isset($modify_type) && $modify_type == 'move') {
                    if ($stock_data['is_cable'] == 0) {
                        $stock_move_data = GeneralModel::formatArrayOnIdAndCount(StockModel::getMoveStockData($stock_id));
                    } else {
                        $stock_move_data = GeneralModel::formatArrayOnIdAndCount(StockModel::getMoveStockCableData($stock_id));
                    }
                }
            } else {
                // if the item doesnt have any entry in the stocm table, default back to adding a new item.
                return redirect()->route('stock', ['stock_id' => 0, 'modify_type' => 'add']);
            }
        } 

        $transactions['view'] = 'stock';

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer', 0));

        if ($modify_type == 'edit') {
            $img_files = GeneralModel::getFilesInDirectory('img/stock');
        } 
        
        return view('stock', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'stock_data' => $stock_data ?? null,
                                'stock_inv_data' => $stock_inv_data ?? null,
                                'stock_item_data' => $stock_item_data ?? null,
                                'stock_distinct_item_data' => $stock_distinct_item_data ?? null,
                                'stock_move_data' => $stock_move_data ?? null,
                                'favourited' => $favourited ?? null,
                                'serial_numbers' => $serial_numbers ?? null,
                                'container_data' => $container_data ?? null,
                                'manufacturers' => $manufacturers ?? null,
                                'sites' => $sites ?? null,
                                'areas' => $areas ?? null,
                                'shelves' => $shelves ?? null,
                                'transactions' => $transactions ?? null,
                                'tag_data' => $tag_data ?? null,
                                'img_files' => $img_files ?? null,
                                ]);
    }

    static public function addExistingStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'id' => 'integer|required',
                'stock-add' => 'integer|required',
                'upc' => 'string|nullable',
                'manufacturer' => 'integer|required',
                'site' => 'integer|required',
                'area' => 'integer|required',
                'shelf' => 'integer|required',
                'contianer' => 'integer|nullable',
                'cost' => 'numeric',
                'quantity' => 'integer|required',
                'serial-number' => 'string|nullable',
                'reason' => 'string|required'
            ]);

            return StockModel::addExistingStock($request->input());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function addNewStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'name' => 'required|string',
                'sku' => 'nullable|string',
                'description' => 'nullable|string',
                'min-stock' => 'integer|nullable',

                'stock-add' => 'integer|required',

                'upc' => 'string|nullable',
                'manufacturer' => 'integer|required',
                'site' => 'integer|required',
                'area' => 'integer|required',
                'shelf' => 'integer|required',
                'container' => 'integer|nullable',
                'cost' => 'numeric',
                'quantity' => 'integer|required',
                'serial-number' => 'string|nullable',
                'reason' => 'string|required'
            ]);
        // dd($request->toArray());
            return StockModel::addNewStock($request, 0);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function moveStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'current_i' => 'integer|nullable', // row in the table on the move page
                'current_stock' => 'required|integer',
                'current_shelf' => 'required|integer',
                'current_manufacturer' => 'integer|required',
                'current_upc' => 'string|nullable',
                'current_serial' => 'string|nullable',
                'current_comments' => 'string|nullable',
                'current_cost' => 'numeric|nullable',

                'site' => 'integer|required',
                'area' => 'integer|required',
                'shelf' => 'integer|required',
                'quantity' => 'integer|required',
            ]);
        // dd($request->toArray());
            return StockModel::moveStock($request, 0, 0);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function moveStockContainer(Request $request)
    {
        // dd($request->toArray());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'current_stock' => 'required|integer',
                'current_shelf' => 'required|integer',
                'current_manufacturer' => 'integer|required',
                'current_upc' => 'string|nullable',
                'current_serial' => 'string|nullable',
                'current_comments' => 'string|nullable',
                'current_cost' => 'numeric|nullable',

                'item_id' => 'integer|required',
                'shelf_id' => 'integer|required',
                'quantity' => 'integer|required',
            ]);
            
            if (isset($request['container-move-single'])) {
                return StockModel::moveStock($request, 1, 0);

            } elseif (isset($request['container-move-all'])) {
                return StockModel::moveStock($request, 1, 1);

            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Move status missing.');
            }
            
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function moveStockCable(Request $request)
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

    static public function editStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'name' => 'required|string',
                'sku' => 'nullable|string',
                'description' => 'nullable|string',
                'min-stock' => 'integer|nullable',
                'tags' => 'array',
            ]);
            return StockModel::editStock($request);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function uploadStockImage(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
                'image' => 'required',
            ]);
            if ($request->hasFile('image')) {
                $request->id = $request->stock_id;
                // dd($request->toArray());
                StockModel::imageUpload($request);
                $redirect_array = ['stock_id'   => $request->stock_id,
                            'modify_type' => 'edit',
                            'success' => 'Image uploaded.'];
                return redirect()->route('stock', $redirect_array);
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'No Image');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function unlinkStockImage(Request $request) 
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
                'img_id' => 'integer|required',
            ]);

            StockModel::imageUnlink($request);
            $redirect_array = ['stock_id'   => $request->stock_id,
                            'modify_type' => 'edit',
                            'success' => 'Image unlinked.'];
            return redirect()->route('stock', $redirect_array);
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
        
    } 

    static public function linkStockImage(Request $request) 
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
                'img-file-name' => 'string|required',
            ]);

            if (StockModel::imageLink($request) > 0) {
                 $redirect_array = ['stock_id'   => $request->stock_id,
                            'modify_type' => 'edit',
                            'success' => 'Image Linked.'];
                return redirect()->route('stock', $redirect_array);
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Linking failed.');
            }
           
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
        
    }  

    static public function editItem(Request $request)
    {
        // dd($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'item-id' => 'integer|required',
                'manufacturer_id' => 'integer|required',
                'upc' => 'string|nullable',
                'serial_number' => 'string|nullable',
                'cost' => 'numeric|nullable',
                'comments' => 'string|nullable',
                'container-toggle' => 'nullable'
            ]);
            return ItemModel::editItem($request->input());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }
    
    static public function removeExistingStock(Request $request)
    {
        // dd($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
                'manufacturer' => 'integer|required',
                'shelf' => 'integer|required',
                'container' => 'integer|nullable',
                'price' => 'numeric|nullable',
                'serial-number' => 'string|nullable',
                'transaction_date' => 'string|required',
                'quantity' => 'integer|required',
                'reason' => 'string|required',
            ]);
            return StockModel::removeExistingStock($request->input());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function deleteStock(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
            ]);
            return StockModel::deleteStock($request->input());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }
}
