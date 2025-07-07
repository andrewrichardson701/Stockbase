<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CablestockModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CablestockModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CablestockModel query()
 * @mixin \Eloquent
 */
class CablestockModel extends Model
{
    //
    static public function index()
    {

    }

    static public function getCableTypesByParent() 
    {
        $instance = new self();
        $instance->setTable('cable_types');
        $cable_types = $instance->orderby('parent')
                        ->get()
                        ->toArray();

        return $cable_types;
    }

    static public function getCablesByName() 
    {
        $instance = new self();
        $instance->setTable('stock');
        $cables = $instance->where('is_cable', '=', 1)
                        ->orderby('name')
                        ->get()
                        ->toArray();

        return $cables;
    }

    static public function returnCablesAjax($request)
    {
        $oos = isset($request['oos']) ? (int)$request['oos'] : 0;
        $site = isset($request['site']) ? (int)$request['site'] : 0;
        $name = isset($request['name']) ? $request['name'] : "";
        $cableType = isset($request['cable']) ? $request['cable'] : 'copper';
        if ($cableType == '') {
            $cableType = 'copper';
        }
        $cable_typesType = isset($request['type']) ? $request['type'] : '';
        if (isset($request['rows'])) {
            if ($request['rows'] == 50 || $request['rows'] == 100) {
                $results_per_page = htmlspecialchars($request['rows']);
            } else {
                $results_per_page = 10;
            }
        } else {
            $results_per_page = 10;
        }

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));

        $instance = new self();
        $instance->setTable('cable_item');

        // Select the necessary columns
        $instance = $instance->select(
                ['stock.id AS stock_id',
                'stock.name AS stock_name',
                'stock.description AS stock_description',
                'stock.sku AS stock_sku',
                'stock.min_stock AS stock_min_stock',
                'stock.is_cable AS stock_is_cable',
                'cable_item.id AS cable_item_id',
                'cable_item.stock_id AS cable_item_stock_id',
                'cable_item.quantity AS cable_item_quantity',
                'cable_item.cost AS cable_item_cost',
                'cable_item.shelf_id AS cable_item_shelf_id',
                'cable_item.type_id AS cable_item_type_id',
                'cable_types.id AS cable_types_id',
                'cable_types.name AS cable_types_name',
                'cable_types.description AS cable_types_description',
                'cable_types.parent AS cable_types_parent',
                'site.id AS site_id',
                'site.name AS site_name',
                'area.id AS area_id',
                'stock_img_image.stock_img_image']
            )
            ->leftJoin('stock', 'cable_item.stock_id', '=', 'stock.id')
            ->leftJoin('shelf', 'cable_item.shelf_id', '=', 'shelf.id')
            ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
            ->leftJoin('site', 'area.site_id', '=', 'site.id')
            ->leftJoin('cable_types', 'cable_item.type_id', '=', 'cable_types.id')
            ->leftJoinSub(
                \DB::table('stock_img')
                    ->select('stock_id', \DB::raw('MIN(image) AS stock_img_image'))
                    ->groupBy('stock_id'),
                'stock_img_image',
                'stock_img_image.stock_id',
                '=',
                'stock.id'
            )
            ->where('stock.is_cable', '=', 1);

        // Add filters conditionally
        if ($site !== 0) {
            $instance = $instance->where('site.id', '=', $site);
        }

        if ($oos == 0) {
            $instance = $instance->where('cable_item.quantity', '!=', 0);
        }

        if ($name !== '' && !empty($name)) {
            $instance = $instance->where('stock.name', 'LIKE', '%' . $name . '%');
        }

        if (is_numeric($cable_typesType) && $cable_typesType !== '') {
            $instance = $instance->where('cable_types.id', '=', $cable_typesType);
        }

        if (isset($cableType)) {
            if (is_numeric($cable_typesType) && $cable_typesType !== '') {
                // Get the parent of the cableType
                $parent_array = GeneralModel::getAllWhere('cable_types', ['id' => $cable_typesType]);

                if (count($parent_array) < 1) {
                    if (empty($name) || $name == '') {
                        $cable_type = ucwords($cableType);
                        $instance = $instance->where('cable_types.parent', '=', $cable_type);
                    }
                } else {
                    $query_parent = $parent_array[0]['parent'] ?? null;
                    if (empty($name || $name == '') && $query_parent) {
                        $cableType = strtolower($query_parent);
                        $cable_type = ucwords($cableType);
                        $instance = $instance->where('cable_types.parent', '=', $cable_type);
                    }
                }
            } else {
                if (empty($name) || $name == '') {
                    $cable_type = ucwords($cableType);
                    $instance = $instance->where('cable_types.parent', '=', $cable_type);
                }
            }
        }

        // Add GROUP BY and ORDER BY
        $query = $instance->groupBy([
                'stock.id',
                'stock.name',
                'stock.description',
                'stock.sku',
                'stock.min_stock',
                'stock.is_cable',
                'cable_item.id',
                'site.id',
                'site.name',
                'stock_img_image.stock_img_image',
            ])
            ->orderBy('stock.name');

        // Execute and fetch results
        // return $query->toSql();
        $all_rows = $query->get()->toArray();

        
        $all_rows_count = count($all_rows);

        $total_pages = ceil($all_rows_count / $results_per_page);

        $current_page = isset($request['page']) ? intval($request['page']) : 1;

        if ($current_page < 1) {
            $current_page = 1;
        } elseif ($current_page > $total_pages) {
            $current_page = $total_pages;
        } 

        $offset = ($current_page - 1) * $results_per_page;
        if ($offset < 0) {
            $offset = $results_per_page;
        }
        $page_number_area = CablestockModel::getPageNumberArea($total_pages, $current_page);

        $requested_rows_query = $query
                            ->when($results_per_page !== 0, function ($query) use ($results_per_page) {
                                $query->limit($results_per_page);
                            })
                            ->when($offset > 0, function ($query) use ($offset) {
                                $query->offset($offset);
                            });
        $requested_rows_array = $requested_rows_query->get()->toArray();
        
        $results[-1]['site'] = $site;
        $results[-1]['name'] = $name;
        $results[-1]['total-pages'] = $total_pages;
        $results[-1]['page-number-area'] = $page_number_area;
        $results[-1]['page'] = $page = $current_page;
        $results[-1]['rows'] = $rows = $results_per_page;
        $results[-1]['cable_type'] = $cableType;
        $results[-1]['type'] = $cable_typesType;
        $results[-1]['oos'] = $oos;
        $results[-1]['url'] = "cablestock?site=$site&name=$name&page=$page&rows=$rows&cable=$cableType&type=$cable_typesType&oos=$oos";
        $results[-1]['sql'] = GeneralModel::interpolatedQuery($query->toSql(),$query->getBindings());
        $results[-1]['query_data'] = $requested_rows_array;

        $img_directory = 'img/stock/';
        if (empty($all_rows)) {
            $result = '<tr><td colspan=100%>No Inventory Found</td></tr>';
            $results[] = $result;
        } else {
            if (count($requested_rows_array) < 1) {
                $result = "<tr><td colspan=100%>No Inventory Found</td></tr>";
                $results[] = $result;
            } else {
                foreach ($requested_rows_array as $row) {
                    $stock_id = $row['stock_id'];
                    $stock_img_file_name = $row['stock_img_image'];
                    $stock_name = $row['stock_name'];
                    $stock_sku = $row['stock_sku'];
                    $stock_quantity_total = $row['cable_item_quantity'];
                    $stock_site_id = $row['site_id'];
                    $stock_area_id = $row['area_id'];
                    $stock_shelf_id = $row['cable_item_shelf_id'];
                    $stock_site_name = $row['site_name'];
                    $stock_min_stock = $row['stock_min_stock'];
                    $cable_item_id = $row['cable_item_id'];
                    $cable_item_cost = $row['cable_item_cost'];
                    $cable_types_id = $row['cable_types_id'];
                    $cable_types_name = $row['cable_types_name'];
                    $cable_types_description = $row['cable_types_description']; 
                    $cable_types_parent = $row['cable_types_parent'];     
                    
                    // Echo each row (inside of SQL results)
                    if (isset($request['cableItemID']) && $request['cableItemID'] == $cable_item_id) { 
                        $last_edited = ' last-edit'; 
                    } else {
                        $last_edited = '';
                    }

                    $result =
                    '<tr class="vertical-align align-middle'.$last_edited.' row-show highlight" id="'.$cable_item_id.'">
                        <td hidden>
                            <form id="modify-cable-item-'.$cable_item_id.'" action="'.route('cablestock.modifyStock').'" method="POST" enctype="multipart/form-data">
                                <!-- Include CSRF token in the form -->
                                ' . csrf_field() . '
                                <input type="hidden" name="stock-id" value="'.$stock_id.'" />
                                <input type="hidden" name="cable-item-id" value="'.$cable_item_id.'" />
                            </form>
                        </td>
                        <td class="align-middle" id="'.$cable_item_id.'-stock-id" hidden>'.$stock_id.'</td>
                        <td class="align-middle" id="'.$cable_item_id.'-item-id" hidden>'.$cable_item_id.'</td>
                        <td class="align-middle" id="'.$cable_item_id.'-img-td">';
                    
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$cable_item_id.'-img" class="inv-img-50h thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                    <td class="align-middle" id="'.$cable_item_id.'-name"><a class="link" href="stock/'.$stock_id.'">'.$stock_name.'</a></td>
                    <td class="align-middle" id="'.$cable_item_id.'-type-id" hidden>'.$cable_types_id.'</td>
                    <td class="align-middle viewport-large-empty" id="'.$cable_item_id.'-type"><or title="'.$cable_types_description.'">'.$cable_types_name.'</or></td> 
                    <td class="align-middle link gold" id="'.$cable_item_id.'-site-name" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>
                    <td class="align-middle" id="'.$cable_item_id.'-quantity">';
                    if ($stock_quantity_total == 0) {
                        $result .= "<or class='red' title='Out of Stock'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Out of stock. Order more if necessary.'>0 <i class='fa fa-warning' /></u></or>";
                    } elseif ($stock_quantity_total < $stock_min_stock) {
                        $result .= "<or class='red'><u style='border-bottom: 1px dashed #999; text-decoration: none' title='Below minimum stock count. Order more!'>$stock_quantity_total</u></or>";
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';

                    $result .= '<td class="align-middle" id="'.$cable_item_id.'-min-stock"  style="color:#8f8f8f">'.$stock_min_stock.'</td>
                    <td class="align-middle" id="'.$cable_item_id.'-add"><button id="'.$stock_id.'-add-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-success cw nav-v-b btn-cableStock" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                    <td class="align-middle" id="'.$cable_item_id.'-remove"><button id="'.$stock_id.'-remove-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-danger cw nav-v-b btn-cableStock" type="submit" name="action" value="remove" '; if ($stock_quantity_total == 0) { $result .= "disabled"; } $result .= '><i class="fa fa-minus"></i></button></td>
                    <td class="align-middle" id="'.$cable_item_id.'-move"><button id="'.$stock_id.'-move-btn" form="modify-cable-item-'.$cable_item_id.'" class="btn btn-warning cw nav-v-b btn-cableStock" type="button" value="move" onclick="toggleHidden(\''.$cable_item_id.'\')" '; if ($stock_quantity_total == 0) { $result .= "disabled"; } $result .= '><i class="fa fa-arrows-h" style="color:black"></i></button></td>
                    </tr>
                    <tr class="vertical-align align-middle'.$last_edited.' move-hide" id="'.$cable_item_id.'-move-hidden" hidden>
                        <td colspan=100%>
                            <form class="centertable" action="'.route('cablestock.moveStock').'" method="POST" enctype="multipart/form-data" style="max-width:max-content;margin-bottom:0px">
                                <!-- Include CSRF token in the form -->
                                ' . csrf_field() . '
                                <table class="centertable" style="border: 1px solid #454d55; width:100%"> 
                                    <input type="hidden" name="cablestock-move" value="1">
                                    <input type="hidden" id="'.$stock_id.'-c-stock" name="current_cable_item" value="'.$cable_item_id.'">
                                    <input type="hidden" id="'.$stock_id.'-c-stock" name="current_stock" value="'.$stock_id.'">
                                    <input type="hidden" id="'.$stock_id.'-c-site" name="current_site" value="'.$stock_site_id.'">
                                    <input type="hidden" id="'.$stock_id.'-c-area" name="current_area" value="'.$stock_area_id.'">
                                    <input type="hidden" id="'.$stock_id.'-c-shelf" name="current_shelf" value="'.$stock_shelf_id.'">
                                    <input type="hidden" id="'.$stock_id.'-c-cost" name="current_cost" value="'.$cable_item_cost.'">
                                    <input type="hidden" id="'.$stock_id.'-c-quantity" name="current_quantity" value="'.$stock_quantity_total.'">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="container">
                                                    <div class="row centertable" style="max-width:max-content">
                                                        <div class="col" style="max-width:max-content !important">
                                                            <label class="nav-v-c">To:</label>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <select class="form-control nav-v-c row-dropdown theme-dropdown" id="'.$stock_id.'-n-site" name="site" style="min-width:50px; padding:2px 0px 2px 0px;  width:max-content !important" required onchange="populateAreasMove(\''.$stock_id.'\')">
                                                                <option value="" selected disabled hidden>Site</option>
                                                                ';
                                                                if ($sites['count'] > 0 && !empty($sites['rows'])) {
                                                                    foreach($sites['rows'] as $site) {
                                                                        $result .= '<option value="'.$site['id'].'">'.$site['name'].'</option>';
                                                                    }
                                                                } else {
                                                                    $result .= '<option value="" selected disabled>No sites found.</option>';
                                                                }
                                                        $result .= '</select>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <select class="form-control nav-v-c row-dropdown theme-dropdown" id="'.$stock_id.'-n-area" name="area" style="min-width:50px; padding: 2px 0px 2px 0px; max-width:max-content !important" disabled="" required onchange="populateShelvesMove(\''.$stock_id.'\')">
                                                                <option value="" selected="" disabled="" hidden="">Area</option>
                                                            </select>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <select class="form-control nav-v-c row-dropdown theme-dropdown" id="'.$stock_id.'-n-shelf" name="shelf" style="min-width:50px; padding: 2px 0px 2px 0px; max-width:max-content !important" disabled="" required>
                                                                <option value="" selected="" disabled="" hidden="">Shelf</option>
                                                            </select>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <label class="nav-v-c" for="0-n-quantity">Quantity: </label>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <input type="number" class="form-control nav-v-c row-dropdown theme-input" id="'.$stock_id.'-n-quantity" name="quantity" style="min-width: 20px; padding: 2px 7px 2px 7px; max-width:50px;" placeholder="1" value="1" min="1" max="'.$stock_quantity_total.'" required>
                                                        </div>
                                                        <div class="col" style="max-width:max-content !important">
                                                            <input type="submit" class="btn btn-warning nav-v-c btn-move" id="'.$stock_id.'-n-submit" value="Move" style="opacity:80%;" name="submit" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </td>
                    </tr>';
                    
                    $results[] = $result;
                }
            }
        }
        return $results;
    }

    static public function getPageNumberArea($total_pages, $current_page) 
    {
        $pageNumberArea = '';

        if ($total_pages > 1) {
            if ($current_page > 1) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
            }
            if ($total_pages > 5) {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } elseif ($i == 1 && $current_page > 5) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                    } elseif ($i < $current_page && $i >= $current_page-2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i > $current_page && $i <= $current_page+2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i == $total_pages) {
                        $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                    }
                }
            } else {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } else {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    }
                }
            }

            if ($current_page < $total_pages) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
            }  
        }
        
        return $pageNumberArea;
    }

    static public function queryData($request)
    {
        $q_oos = isset($request['oos']) ? (int)$request['oos'] : 0;
        $q_site = isset($request['site']) ? (int)$request['site'] : 0;
        $q_name = isset($request['name']) ? $request['name'] : "";
        $q_page = isset($request['page']) ? (int)$request['page'] : 1;
        $q_cable_type = isset($request['cable']) ? $request['cable'] : "";
        $q_type = isset($request['type']) ? (int)$request['type'] : "";

        if ($q_page == '' || $q_page < 1) { $q_page = 1; }
        $q_rows = isset($request['rows']) ? ($request['rows'] == 50 || $request['rows'] == 100 ? (int)$request['rows'] : 10) : 10 ;
        
        $q_data = ['oos' => $q_oos,
                    'site' => $q_site,
                    'name' => $q_name,
                    'page' => $q_page,
                    'rows' => $q_rows,
                    'cable_type' => $q_cable_type,
                    'type' => $q_type,
                ];

        return $q_data;
    }

    static public function getCableData($stock_id, $item_id)
    {
        $record = DB::table('cable_item')
            ->where('stock_id', $stock_id)
            ->where('id', $item_id)
            ->first();

        return $record;
    }

    static public function adjustQuantity($stock_id, $item_id, $action, $quantity=1)
    {
        // adjust cable quantity
        $return = ['id' => $item_id, 'stock_id' => $stock_id, 'quantity' => $quantity, 'action' => $action];

        // allowed actions
        $actions = ['add', 'remove'];
        $user = GeneralModel::getUser();

        $changelog_info = [
            'user' => $user,
            'table' => 'cable_item',
            'record_id' => $item_id,
            'field' => '',
            'new_value' => '',
            'action' => '',
            'previous_value' => ''
        ];
        
        // if it is a valid action, continue \/
        if (in_array($action, $actions)) {
            $current_data = (array)CablestockModel::getCableData($stock_id, $item_id);
            // if row exists \/
            if ($current_data) {
                // cable item exists
                if (isset($current_data['quantity'])) {
                    if ($action == 'add') {
                        if ($current_data['deleted'] == 1 && $current_data['quantity'] == 0) {
                            // set deleted to 0 and quantity to 0 first
                            DB::table('cable_item')->where('id', $item_id)->update(['deleted' => 0, 'quantity' => 0]);
                            $restore_changelog = $changelog_info;
                            $restore_changelog['field'] = 'deleted';
                            $restore_changelog['new_value'] = 0;
                            $restore_changelog['previous_value'] = 1;
                            $restore_changelog['action'] = 'Restore entry';
                            GeneralModel::updateChangelog(info: $restore_changelog);
                        }
                        // do the add
                        $new_quantity = $current_data['quantity'] + $quantity;
                        DB::table('cable_item')->where('id', $item_id)->update(['quantity' => $new_quantity]);
                        $add_changelog = $changelog_info;
                        $add_changelog['field'] = 'quantity';
                        $add_changelog['new_value'] = $new_quantity;
                        $add_changelog['previous_value'] = $current_data['quantity'];
                        $add_changelog['action'] = 'Update entry';
                        GeneralModel::updateChangelog($add_changelog);
                        // add transaction
                        $transaction = [
                            'stock_id' => $current_data['stock_id'],
                            'item_id' => $current_data['id'],
                            'type' => $action,
                            'quantity' => 1,
                            'date' => date('Y-m-d'),
                            'time' => date('H:i:s'),
                            'username' => $user['username'],
                            'shelf_id' => $current_data['shelf_id'],
                            'reason' => 'Add quantity',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        TransactionModel::addCableTransaction($transaction);
                        $return['success'] = 'Added';
                        $return['id'] = $current_data['id'];
                    } elseif ($action == 'remove') {
                        if ($current_data['quantity'] >= $quantity) {
                            // removal quantity is valid
                            $new_quantity = $current_data['quantity'] - $quantity;
                            DB::table('cable_item')->where('id', $item_id)->update(['quantity' => $new_quantity]);
                            $remove_changelog = $changelog_info;
                            $remove_changelog['field'] = 'quantity';
                            $remove_changelog['new_value'] = $new_quantity;
                            $remove_changelog['previous_value'] = $current_data['quantity'];
                            $remove_changelog['action'] = 'Update entry';
                            GeneralModel::updateChangelog($remove_changelog);
                            //add transaction
                            $transaction = [
                                'stock_id' => $current_data['stock_id'],
                                'item_id' => $current_data['id'],
                                'type' => $action,
                                'quantity' => -1,
                                'date' => date('Y-m-d'),
                                'time' => date('H:i:s'),
                                'username' => $user['username'],
                                'shelf_id' => $current_data['shelf_id'],
                                'reason' => 'Remove quantity',
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            TransactionModel::addCableTransaction($transaction);
                            $return['success'] = 'Removed';
                            $return['id'] = $current_data['id'];
                        } else {
                            $return ['errors'][] = 'Not enough quantity to remove';
                        }
                    } else {
                        // nothing to be here yet
                        $return['errors'][] = 'invalid modify action';
                    }
                } else {
                    $return['errors'][] = 'No quantity currently set on item';
                }
            } else {
                $return['errors'][] = 'Cable item not found';
            }
        } else {
            $return['error'][] = 'Invalid adjustment action';
        }

        return $return;
    }

    static public function moveStockCable($request) 
    {
        $user = GeneralModel::getUser();
        $find = DB::table('cable_item')
                ->where('deleted', 0)
                ->where('cost', $request['current_cost'])
                ->where('shelf_id', $request['current_shelf'])
                ->where('stock_id', $request['current_stock'])
                ->first();
        
        if ($find) {
            $cable_id = $find->id;

            if ($request['shelf'] == $find->shelf_id) {
                // same shelf, do not continue
                $error = 'No move necessary. Same shelf selected.';
                return redirect(GeneralModel::previousURL())->with('error', $error);
            }
            $find_quantity = $find->quantity;
            if ($request['quantity'] <= $find_quantity) {
                // enough quantity, continue

                // get the new shelf
                $find_new = DB::table('cable_item')
                            ->where('deleted', 0)
                            ->where('cost', $request['current_cost'])
                            ->where('shelf_id', $request['shelf'])
                            ->where('stock_id', $request['current_stock'])
                            ->first();

                if (!$find_new) {
                    // create the row
                    $insert = DB::table('cable_ite')->insertGetId([
                                                                'stock_id' => $find->stock_id,
                                                                'quantity' => 0,
                                                                'cost' => $find->cost,
                                                                'shelf_id' => $request['shelf'],
                                                                'type_id' => $find->type_id,
                                                                'deleted' => 0,
                                                                'updated_at' => now(),
                                                                'created_at' => now()
                                                            ]);
                    if ($insert) {
                        $find_new = DB::table('cable_item')
                            ->where('id', $insert)
                            ->first();
                    } else {
                        $error = 'Failed to create new cable_item entry.';
                        return redirect(GeneralModel::previousURL())->with('error', $error);
                    }
                } 

                if ($find_new) {
                    $new_quantity = ($find->quantity)-$request['quantity'];
                    $update = DB::table('cable_item')->where('id', $cable_id)->update(['quantity' => $new_quantity, 'updated_at' => now()]);
                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => $user,
                            'table' => 'cable_item',
                            'record_id' => $cable_id,
                            'action' => 'Update record',
                            'field' => 'quantity',
                            'previous_value' => $find->quantity,
                            'new_value' => $new_quantity
                        ];

                        GeneralModel::updateChangelog($changelog_info);

                        // add Transaction
                        $transaction_data_remove = [
                            'stock_id' => $find->stock_id,
                            'item_id' => $cable_id,
                            'type' => 'Remove quantity',
                            'quantity' => ($request['quantity'])*-1,
                            'date' => date('Y-m-d'),
                            'time' => date('H:i:s'),
                            'username' => $user['username'],
                            'shelf_id' => $find->shelf_id,
                            'reason' => 'Move quantity',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $transaction_remove = TransactionModel::addCableTransaction($transaction_data_remove);

                        if (!array_key_exists('error', $transaction_remove)) {
                            // add Transactio
                            // update the new row
                            $new_quantity = ($find_new->quantity)+$request['quantity'];
                            $update = DB::table('cable_item')->where('id', $find_new->id)->update(['quantity' => $new_quantity, 'updated_at' => now()]);
                            if ($update) {
                                // changelog
                                $changelog_info = [
                                    'user' => $user,
                                    'table' => 'cable_item',
                                    'record_id' => $find_new->id,
                                    'action' => 'Update record',
                                    'field' => 'quantity',
                                    'previous_value' => $find_new->quantity,
                                    'new_value' => $new_quantity
                                ];

                                GeneralModel::updateChangelog($changelog_info);

                                // add Transaction
                                $transaction_data_add = [
                                    'stock_id' => $find_new->stock_id,
                                    'item_id' => $find_new->id,
                                    'type' => 'Add quantity',
                                    'quantity' => $request['quantity'],
                                    'date' => date('Y-m-d'),
                                    'time' => date('H:i:s'),
                                    'username' => $user['username'],
                                    'shelf_id' => $request['shelf'],
                                    'reason' => 'Move quantity',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                $transaction_add = TransactionModel::addCableTransaction($transaction_data_add);

                                if (!array_key_exists('error', $transaction_add)) {
                                    // return with success
                                    $success = 'Quantity moved.';
                                    return redirect(GeneralModel::previousURL())->with('success', $success);
                                } else {
                                    $errors[] = 'transaction not added for id: '.$cable_id.', type = move, quantity = 1.';
                                }
                                
                            } else {
                                $errors[] = $error = 'unable to move id: '.$cable_id;
                                return redirect(GeneralModel::previousURL())->with('error', $error);
                            }
                        } else {
                            $errors[] = 'transaction not added for id: '.$cable_id.', type = move, quantity = -1.';
                        }
                    } else {
                        $errors[] = $error = 'unable to move id: '.$cable_id;
                        return redirect(GeneralModel::previousURL())->with('error', $error);
                    }
                } else {
                    $error = 'No new row found.';
                    return redirect(GeneralModel::previousURL())->with('error', $error);
                }

            } else {
                // not enough quantity, error out
                $error = 'Not enough quantity to remove.';
                return redirect(GeneralModel::previousURL())->with('error', $error);
            }
        } else {
            // no match found
            $error = 'No matching item found.';
            return redirect(GeneralModel::previousURL())->with('error', $error);
        }

    }

    static public function addCableStock($request)
    {
        // check for matching cable name
        // make sure the site area and shelf are all valid together
        // make sure the cable-type is valid

        // add the cable stock
            // get the newest SKU CABLE-xxxxxx

        // add the item with quantity

        // changelog for each event
        // transaction for quantity adding

        $user = GeneralModel::getUser();
        $find = DB::table('stock')
                ->where('deleted', 0)
                ->where('name', $request['stock-name'])
                ->first();

        if ($find) {
            // already exists, error back
            return redirect(GeneralModel::previousURL())->with('error', 'Cable already exists with that name.');
        }

        // check the site/area/shelf are a match

        $location_check = DB::table('shelf')
                            ->join('area', 'shelf.area_id' ,'=', 'area.id')
                            ->join('site', 'area.site_id' ,'=', 'site.id')
                            ->where('shelf.deleted', 0)
                            ->where('shelf.id', $request['shelf'])
                            ->where('area.id', $request['area'])
                            ->where('site.id', $request['site'])
                            ->first();

        if (!$location_check) {
            // location doesnt match, error back
            return redirect(GeneralModel::previousURL())->with('error', 'Location does match.');
        }

        $cable_type_check = DB::table('cable_types')
                                    ->where('id', $request['cable-type'])
                                    ->first();

        if (!$cable_type_check) {
            // cable type does exist
            return redirect(GeneralModel::previousURL())->with('error', 'Cable type not found.');
        }

        // get the next SKU value 
        $next_sku = StockModel::getNextSKU('CABLE-');

        if ($next_sku) {
            // insert the stock
            $insert_stock = DB::table('stock')->insertGetId([
                        'name' => $request['stock-name'],
                        'description' => $request['stock-description'],
                        'sku' => $next_sku,
                        'min_stock' => $request['stock-min-stock'],
                        'is_cable' => 1,
                        'deleted' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            if ($insert_stock) {
                $stock_id = $insert_stock;

                // changelog and transactions
                $info = [
                    'user' => $user,
                    'table' => 'stock',
                    'record_id' => $stock_id,
                    'field' => 'name',
                    'new_value' => $request['stock-name'],
                    'action' => 'New record',
                    'previous_value' => '',
                ];
                GeneralModel::updateChangelog($info);


                // created the item
                $insert_item = DB::table('cable_item')->insertGetId([
                        'stock_id' => $insert_stock,
                        'quantity' => $request['item-quantity'],
                        'cost' => $request['item-cost'],
                        'shelf_id' => $request['shelf'],
                        'type_id' => $request['cable-type'],
                        'deleted' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                        ]);

                if ($insert_item) {
                    // item added, do transactions
                    $transaction_data_add = [
                        'stock_id' => $stock_id,
                        'item_id' => $insert_item,
                        'type' => 'Add quantity',
                        'quantity' => $request['item-quantity'],
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'username' => $user['username'],
                        'shelf_id' => $request['shelf'],
                        'reason' => 'Add quantity',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $transaction_add = TransactionModel::addCableTransaction($transaction_data_add);

                    if ($transaction_add) {
                        // added, continue

                        // add image
                        if ($request->hasFile('image')) {
                            $request['id'] = $stock_id;
                            $uploaded = StockModel::imageUpload($request);
                            if ($uploaded == 0) {
                                return redirect(GeneralModel::previousURL())->with('error', 'Image link failed.');
                            }
                        }

                        return redirect(GeneralModel::previousURL())->with('success', 'Cable: "'.$request['name'].'" added successfully with id: "'.$stock_id.'".');
                    } else {
                        return redirect(GeneralModel::previousURL())->with('error', 'Transaction not added.');
                    }

                } else {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unable to create item in the database.');
                }


                

            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Unable to created stock entry.');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'Unable to get SKU.');
        }
    
    }

    static public function checkCableQuantity($request)
    {
        // find the row we need
        $find = DB::table('stock')
                ->join('cable_item', 'stock.id', '=', 'cable_item.stock_id')
                ->where('cable_item.deleted', 0)
                ->where('stock.deleted', 0)
                ->where('cable_item.shelf_id', $request['shelf_id'])
                ->where('stock.id', $request['stock_id'])
                ->first();
        
        if ($find) {
            // row found
            $quantity = $find->quantity;
        } else {
            $quantity = 0;
        }

        return $quantity;
    }
}

