<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MemoryModel extends Model
{
    //
    static public function getMemory($where_array = [], $orderby = 'type', $deleted = 0, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $wheres = MemoryModel::generateMemoryWhereArray($where_array) ;
      
        $order = MemoryModel::getMemoryOrderBy($orderby);

        $totalCount = count(MemoryModel::getMemoryList($wheres, $order, $deleted, 0, 0));

        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $memory = GeneralModel::formatArrayOnIdAndCount(MemoryModel::getMemoryList($wheres, $order, $deleted, $limit, $offset));

        $memory['total_count'] = $totalCount;
        $memory['pages'] = (int)ceil($totalCount / $limit);
        $memory['page'] = $page;
        $memory['results_per_page'] = $limit;
        $memory['offset'] = $offset;

        return $memory;
    }

    static public function getMemoryList($where_array = [], $orderby = 'type', $deleted = 0, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('memory_item');

        $query = $instance->select(
                        ['memory_item.id AS id',
                        'memory_item.model AS model',
                        'memory_item.serial_number AS serial_number',
                        'memory_item.deleted AS deleted',
                        'memory_vendor.id AS vendor_id',
                        'memory_vendor.name AS vendor_name',
                        'memory_ecc_type.id AS ecc_type_id',
                        'memory_ecc_type.name AS ecc_type_name',
                        'memory_speed.id AS speed_id',
                        'memory_speed.name AS speed_name',
                        'memory_generation.id AS generation_id',
                        'memory_generation.name AS generation_name',
                        'memory_form_factor.id AS form_factor_id',
                        'memory_form_factor.name AS form_factor_name',
                        'memory_capacity.id AS capacity_id',
                        'memory_capacity.name AS capacity',
                        'site.id AS site_id',
                        'site.name AS site_name',
                        'area.id AS area_id',
                        'area.name AS area_name',
                        'area.site_id AS area_site_id',
                        'shelf.id AS shelf_id',
                        'shelf.name AS shelf_name',
                        'shelf.area_id AS shelf_area_id'
                        ])
                    ->join('memory_vendor', 'memory_item.vendor_id', '=', 'memory_vendor.id')
                    ->join('memory_speed', 'memory_item.speed_id', '=', 'memory_speed.id')
                    ->join('memory_ecc_type', 'memory_item.ecc_type_id', '=', 'memory_ecc_type.id')
                    ->join('memory_generation', 'memory_item.generation_id', '=', 'memory_generation.id')
                    ->join('memory_form_factor', 'memory_item.form_factor_id', '=', 'memory_form_factor.id')
                    ->join('memory_capacity', 'memory_item.capacity_id', '=', 'memory_capacity.id')
                    ->join('shelf', 'memory_item.shelf_id', '=', 'shelf.id')
                    ->join('area', 'shelf.area_id', '=', 'area.id')
                    ->join('site', 'area.site_id', '=', 'site.id');
                    
                    

        if (!empty($where_array)) {
            foreach ($where_array as $where) {
                $query->whereRaw($where['where'], $where['value']);
            }
        }

        $query->where('memory_item.deleted', '=', $deleted);

        $query->orderByRaw($orderby);

        if ($limit != 0) {
            $query->limit($limit);
        }
        
        if ($offset != 0) {
            $query->offset($offset);
        }

        $rows = $query->get()->toArray();

        return $rows;

    }

    static public function generateMemoryWhereArray($array) 
    {
        
        $return = [];
        $memory_keys = ['ecc_type', 'speed', 'capacity', 'generation', 'vendor', 'form_factor'];

        if (!empty($array)) {
            foreach($array as $key => $row) {
                if ($array[$key] == '' || $array[$key] == null) {
                    continue;
                }
                if ($array[$key] == 0) {
                    continue;
                }
                if ($key == "site") {
                    $return[] = ['where' => "site.id = ?", 'value' => $array[$key]];
                } elseif ($key == "area") {
                    $return[] = ['where' => "area.id = ?", 'value' => $array[$key]];
                } elseif ($key == "shelf") {
                    $return[] = ['where' => "shelf.id = ?", 'value' => $array[$key]];
                } elseif ($key == "search") {
                    $value = $array[$key];
                    $return[] = ['where' => "(memory_item.serial_number LIKE ? 
                                                OR memory_item.model LIKE ? 
                                                OR memory_form_factor.name LIKE ?
                                                OR memory_vendor.name LIKE ?
                                                OR memory_ecc_type.name LIKE ? 
                                                OR memory_capacity.name LIKE ?
                                                OR memory_speed.name LIKE ?
                                                OR memory_generation.name LIKE ?)", 
                                                'value' => ["%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%"]];
                } elseif (in_array($key, $memory_keys)) {
                    if ($key == "form_factor") {
                        $return[] = ['where' => "memory_form_factor.id = ?", 'value' => $array[$key]];
                    } else {
                        $return[] = ['where' => "memory_$key.id = ?", 'value' => $array[$key]];
                    }
                }
            }
        } 

        return $return;        

    }

    static public function getMemoryOrderBy($orderby) 
    {
        $order = "memory_ecc_type.id, memory_vendor.name, memory_capacity.name, memory_generation.name, memory_item.model, memory_item.serial_number";
        switch ($orderby) {
            case 'type':
                $order = "memory_ecc_type.name, memory_vendor.name, memory_capacity.name, memory_generation.name, memory_item.model, memory_item.serial_number";
                break;
            case 'model':
                $order = "memory_item.model, memory_vendor.name, memory_capacity.name, memory_generation.name, memory_ecc_type.name, memory_item.serial_number";
                break;
            case 'speed':
                $order = "memory_speed.id, memory_ecc_type.name, memory_vendor.name, memory_capacity.name, memory_generation.name, memory_item.model, memory_item.serial_number";
                break;
            case 'generation':
                $order = "memory_generation.name, memory_ecc_type.name, memory_vendor.name, memory_capacity.name, memory_item.model, memory_item.serial_number";
                break;
            case 'serial':
                $order = "memory_item.serial_number, memory_generation.name, memory_ecc_type.name, memory_vendor.name, memory_capacity.name, memory_item.model";
                break;
            case 'vendor':
                $order = "memory_vendor.name, memory_generation.name, memory_ecc_type.name, memory_capacity.name, memory_item.model, memory_item.serial_number";
                break;
            case 'capacity':
                $order = "memory_capacity.name, memory_generation.name, memory_ecc_type.name, memory_vendor.name, memory_item.model, memory_item.serial_number";
                break;
            default:
                $order = "memory_generation.id, memory_vendor.name, memory_capacity.name, memory_item.model, memory_item.serial_number";
                break;
        }

        return $order;
    }

    static public function addMemory($request)
    {
        $previous = GeneralModel::previousURL();

        // Extract existing components
        $urlParts = parse_url($previous);
        $existingParams = [];

        // Parse the existing query string into an array
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $existingParams);
        }

        // Filter out any keys starting with 'form_'
        $filteredParams = array_filter($existingParams, function($key) {
            return strpos($key, 'form_') !== 0;
        }, ARRAY_FILTER_USE_KEY);

        unset($filteredParams['error'], $filteredParams['success']); // remove multiple if it exists

        // Build your new data
        $newData = [
            'form_serial' => $request['serial'] ?? '', 
            'form_model' => $request['model'] ?? '',
            'form_capacity' => $request['capacity'] ?? '',
            'form_vendor' => $request['vendor'] ?? '',
            'form_ecc_type' => $request['ecc_type'] ?? '',
            'form_speed' => $request['speed'] ?? '',
            'form_generation' => $request['generation'] ?? '',
            'form_site' => $request['site'] ?? '',
            'form_form_factor' => $request['form_factor'] ?? '',
            'form_area' => $request['area'] ?? '',
            'form_shelf' => $request['shelf'] ?? '',
        ];

        if ($request['multiple'] ?? false) {
            $newData['add_form'] = 1;
        } else {
            $newData['add_form'] = 0;
        }

        // Merge filtered old params with new data
        $finalQuery = http_build_query(array_merge($filteredParams, $newData));

        // Reconstruct the URL
        $url = $urlParts['scheme'] ?? 'http' . '://' . $urlParts['host'] . ($urlParts['path'] ?? '');
        $url .= '?' . $finalQuery;

        $user = GeneralModel::getUser();

        // see if memory serial exists
        if ($request['serial'] && $request['serial'] !== '') {
            $find = DB::table('memory_item')->where('serial_number', $request['serial'])->first();
        } else {
            $find = null;
        }

        // check for ids of each field
        foreach (['vendor', 'ecc_type', 'capacity', 'speed', 'generation', 'form_factor'] as $param) {
           $find_params = DB::table('memory_'.$param)->where('id', $request[$param])->where('deleted', 0)->first();
           if (!$find_params) {
                return redirect()->to(route('memory', ['error' => 'Memory '.$param.' not found for id: '.$request[$param]]));
            } 
        }

        // make sure shelf exists
        $find_shelf = DB::table('shelf')->where('id', $request['shelf'])->where('deleted', 0)->first();
        if (!$find_shelf) {
            return redirect()->to(route('memory', ['error' => 'Shelf not found for id: '.$request['shelf']]));
        } 
        $values = [
                    'model' => $request['model'],
                    'vendor_id' => $request['vendor'],
                    'serial_number' => $request['serial'],
                    'ecc_type_id' => $request['ecc_type'],
                    'generation_id' => $request['generation'],
                    'capacity_id' => $request['capacity'],
                    'speed_id' => $request['speed'],
                    'shelf_id' => $request['shelf'],
                    'form_factor_id' => $request['form_factor'],
                    'quantity' => 1,
                    'created_at' => now(), 
                    'updated_at' => now()
                ];
        
        if (!$find) {
            // add memory

            $insert = DB::table('memory_item')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'memory_item',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'serial_number',
                    'previous_value' => '',
                    'new_value' => $request['serial']
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'memory_item',
                    'item_id' => $insert,
                    'type' => 'add',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $request['shelf'],
                    'reason' => 'Item Added',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addMemoryTransaction($transaction);
                return redirect()->to($url)->with('success', 'Memory added: "'.$request['serial'].'" with id: '.$insert.'.');
            } else {
                if ($find->deleted == 1) {
                    // remove delete, and update any changes.
                    // update 
                    unset($values['serial_number']);

                    foreach (array_keys((array)$find) as $key) {
                        if (!in_array($key, ['id', 'serial_number', 'updated_at', 'created_at'])) {
                            if ($values[$key] !== $find->$key) {
                                // update
                                $update = DB::table('memory_item')->where('id', $find->id)->update([$key => $values[$key]]);

                                if ($update) {
                                    // changelog
                                    $changelog_info = [
                                        'user' => $user,
                                        'table' => 'memory_item',
                                        'record_id' => $find->id,
                                        'action' => 'Update record',
                                        'field' => $key,
                                        'previous_value' => $find->$key,
                                        'new_value' => $values[$key]
                                    ];

                                    GeneralModel::updateChangelog($changelog_info);
                                    $transaction = [
                                        'table_name' => 'memory_item',
                                        'item_id' => $find->id,
                                        'type' => 'restore',
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'username' => $user['username'],
                                        'shelf_id' => $request['shelf'],
                                        'reason' => 'Item Restored',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                    TransactionModel::addMemoryTransaction($transaction);
                                } else {
                                    return redirect()->to($url)->with('error', 'Unable to insert database entry.');
                                }
                            }
                        }
                    }   
                    $data = ['id' => $find->id];
                    return MemoryModel::restoreMemory($data);
 
                } else {
                    return redirect()->to($url)->with('error', 'Memory already exists.');
                }  
            }
        } else {
            return redirect()->to(route('memory', ['error' => 'Memory already exists for serial number: '.$request['serial']]));
        }
    }

    public static function returnMemoryInfoAjax($id, $request)
    {
        $find = DB::Table('memory_item')->where('id', $id)->first();

        if ($find) {
            $find_shelf = DB::Table('shelf')->where('id', $find->shelf_id)->first();
            $find_area = DB::Table('area')->where('id', $find_shelf->area_id)->first();
            $find->area_id = $find_area->id;
            $find->site_id = $find_area->site_id;
            return $find;
        } else {
            return ['error' => 'Memory not found for id: '.$id];
        }
    }

    static public function deleteMemory($request)
    {
        $memory_id = $request['id'];
        $reason = $request['reason'];
        $user = GeneralModel::getUser();

        // see if memory exists
        $find = DB::table('memory_item')->where('id', $memory_id)->first();

        if ($find && $find->deleted == 0) {
            $update = DB::table('memory_item')->where('id', $memory_id)->update(['deleted' => 1, 'quantity' => 0, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'memory_item',
                    'record_id' => $find->id,
                    'action' => 'Delete record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 1
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'memory_item',
                    'item_id' => $memory_id,
                    'type' => 'delete',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addMemoryTransaction($transaction);
                return redirect(GeneralModel::previousURL())->with('success', 'Memory with serial number: '.$find->serial_number.' and id: '.$memory_id.' delete.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to delete memory with id: '.$memory_id.'.');
            }
        } elseif ($find && $find->deleted == 1) {
            return redirect()->to(route('memory', ['error' => 'Memory already deleted for id: '.$memory_id]));
        } else {
            return redirect()->to(route('memory', ['error' => 'Memory not found for id: '.$memory_id.'.']));
        }
    }

    static public function editMemory($request)
    {
        $memory_id = $request['id'];
        $user = GeneralModel::getUser();

        // see if memory exists
        $find = DB::table('memory_item')->where('id', $memory_id)->first();

        if ($find) {
            $update_data = [];
            foreach ($request as $key => $value) {
                if (in_array($key, ['model', 'vendor_id', 'serial_number', 'ecc_type_id', 'capacity_id', 'generation_id', 'speed_id', 'shelf_id', 'form_factor_id'])) {
                    if ($value != $find->$key) {
                        $update_data[$key] = $value;
                    }
                }
            }

            if (!empty($update_data)) {
                $update_data['updated_at'] = now();
                $update = DB::table('memory_item')->where('id', $memory_id)->update($update_data);

                if ($update) {
                    foreach ($update_data as $key => $value) {
                        if (in_array($key, ['model', 'vendor_id', 'serial_number', 'ecc_type_id', 'capacity_id', 'generation_id', 'speed_id', 'shelf_id', 'form_factor_id'])) {
                            if ($value != $find->$key) {
                                $update_data[$key] = $value;

                                // changelog
                                $changelog_info = [
                                    'user' => $user,
                                    'table' => 'memory_item',
                                    'record_id' => $memory_id,
                                    'action' => 'Edit record',
                                    'field' => $key,
                                    'previous_value' => $find->$key,
                                    'new_value' => $value
                                ];

                                GeneralModel::updateChangelog($changelog_info);

                                if ($key == 'shelf_id') {
                                    $transaction = [
                                        'table_name' => 'memory_item',
                                        'item_id' => $memory_id,
                                        'type' => 'move',
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'username' => $user['username'],
                                        'shelf_id' => $value,
                                        'reason' => 'Move Memory',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                    TransactionModel::addMemoryTransaction($transaction);
                                }
                            }
                        }
                    }
                    return redirect()->to(GeneralModel::previousURL())->with('success', 'Memory with id: '.$memory_id.' updated.');
                } else {
                    return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to update memory with id: '.$memory_id.'.');
                }
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('info', 'No changes made to memory with id: '.$memory_id.'.');
            }
        } else {
            return redirect()->to(route('memory', ['error' => 'Memory not found for id: '.$memory_id.'.']));
        }
    }

    static public function restoreMemory($request)
    {
        $memory_id = $request['id'];
        $user = GeneralModel::getUser();

        $find = DB::table('memory_item')->where('id', $memory_id)->where('deleted', 1)->first();

        if ($find) {
            $update = DB::table('memory_item')->where('id', $find->id)->update(['deleted' => 0, 'quantity' => 1, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'memory_item',
                    'record_id' => $memory_id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);

                $transaction = [
                    'table_name' => 'memory_item',
                    'item_id' => $memory_id,
                    'type' => 'restore',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => 'Item Restored',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addMemoryTransaction($transaction);
                return redirect()->to(GeneralModel::previousURL())->with('success', 'Memory restored, with id: '.$memory_id.'.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to insert database entry.');
            }
        } else {
            // memory doesnt exist
            return redirect()->to(GeneralModel::previousURL())->with('error', 'Memory not found with id: '.$memory_id.'.');
        }
    }

    static public function serialMatchChecker($request)
    {
        // search for the matching item

        if ($request['serial'] && $request['serial'] !== '') {
            $find = DB::table('memory_item')->where('serial_number', $request['serial'])->first();
        } else {
            $find = null;
        }
        
        if ($find) {
            if ($find->deleted == 0) {
                $results['error'] = "Memory already exists.";
            } else {
                $results['error'] = "Found matching deleted memory. Please restore this memory instead of adding.";
            }
        } else {
            $results['skip'] = 1;
        }

        return $results;
    }  
}
