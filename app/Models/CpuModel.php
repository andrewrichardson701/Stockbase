<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpuModel extends Model
{
    //

    static public function generateCpuWhereArray($array) 
    {
        
        $return = [];

        if (!empty($array)) {
            foreach($array as $key => $row) {
                if ($array[$key] == '' || $array[$key] == null) {
                    continue;
                }
                if ($array[$key] == 0 && !in_array($key, ['destroy', 'ssd'])) {
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
                    $return[] = ['where' => "(cpu_item.serial_number LIKE ? 
                                                OR cpu_model.name LIKE ? 
                                                OR cpu_model.cpu_family LIKE ?
                                                OR cpu_model.core_count LIKE ?
                                                OR cpu_model.clock_speed LIKE ?
                                                OR cpu_vendor.name LIKE ?
                                                OR cpu_model.socket LIKE ?)", 
                                                'value' => ["%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%"]];
                }
            }
        } 

        return $return;        

    }

    static public function getCpusOrderBy($orderby) 
    {
        $order = "cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.core_count, cpu_model.clock_speed, cpu_model.cpu_family, cpu_item.serial_number";
        switch ($orderby) {
            case 'socket':
                $order = "cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.core_count, cpu_model.clock_speed, cpu_model.cpu_family, cpu_item.serial_number";
                break;
            case 'model':
                $order = "cpu_model.name, cpu_vendor.name, cpu_model.core_count, cpu_model.clock_speed, cpu_model.cpu_family, cpu_item.serial_number";
                break;
            case 'clock_speed':
                $order = "cpu_model.clock_speed, cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.core_count, cpu_model.cpu_family, cpu_item.serial_number";
                break;
            case 'core_count':
                $order = "cpu_model.core_count, cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.clock_speed, cpu_model.cpu_family, cpu_item.serial_number";
                break;
            case 'cpu_family':
                $order = "cpu_model.cpu_family, cpu_model.core_count, cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.clock_speed, cpu_item.serial_number";
                break;
            case 'serial':
                $order = "cpu_item.serial_number, cpu_model.name, cpu_vendor.name, cpu_model.cpu_family, cpu_model.core_count, cpu_model.clock_speed";
                break;
            case 'vendor':
                $order = "cpu_vendor.name, cpu_model.name, cpu_model.cpu_family, cpu_model.core_count, cpu_model.clock_speed, cpu_item.serial_number";
                break;
            default:
                $order = "cpu_model.socket, cpu_vendor.name, cpu_model.name, cpu_model.core_count, cpu_model.clock_speed, cpu_model.cpu_family, cpu_item.serial_number";
                break;
        }

        return $order;
    }

    static public function getCpusList($where_array = [], $orderby = 'type', $deleted = 0, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('cpu_item');

        $query = $instance->select(
                        ['cpu_item.id AS id',
                        'cpu_item.serial_number AS serial_number',
                        'cpu_item.deleted AS deleted',
                        'cpu_model.name AS model_name',
                        'cpu_model.id AS model_id',
                        'cpu_vendor.id AS vendor_id',
                        'cpu_vendor.name AS vendor_name',
                        'cpu_model.socket AS socket',
                        'cpu_model.cpu_family AS cpu_family',
                        'cpu_model.core_count AS core_count',
                        'cpu_model.clock_speed AS clock_speed',
                        'site.id AS site_id',
                        'site.name AS site_name',
                        'area.id AS area_id',
                        'area.name AS area_name',
                        'area.site_id AS area_site_id',
                        'shelf.id AS shelf_id',
                        'shelf.name AS shelf_name',
                        'shelf.area_id AS shelf_area_id',
                        ])
                    ->join('cpu_vendor', 'cpu_item.vendor_id', '=', 'cpu_vendor.id')
                    ->join('cpu_model', 'cpu_item.model_id', '=', 'cpu_model.id')
                    ->join('shelf', 'cpu_item.shelf_id', '=', 'shelf.id')
                    ->join('area', 'shelf.area_id', '=', 'area.id')
                    ->join('site', 'area.site_id', '=', 'site.id');
                    
                    

        if (!empty($where_array)) {
            foreach ($where_array as $where) {
                $query->whereRaw($where['where'], $where['value']);
            }
        }

        $query->where('cpu_item.deleted', '=', $deleted);

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

    static public function getCpus($where_array = [], $orderby = 'type', $deleted = 0, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $wheres = CpuModel::generateCpuWhereArray($where_array) ;
      
        $order = CpuModel::getCpusOrderBy($orderby);

        $totalCount = count(CpuModel::getCpusList($wheres, $order, $deleted, 0, 0));

        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $cpus = GeneralModel::formatArrayOnIdAndCount(CpuModel::getCpusList($wheres, $order, $deleted, $limit, $offset));

        $cpus['total_count'] = $totalCount;
        $cpus['pages'] = (int)ceil($totalCount / $limit);
        $cpus['page'] = $page;
        $cpus['results_per_page'] = $limit;
        $cpus['offset'] = $offset;

        return $cpus;
    }

    static public function addCpu($request)
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

        unset($filteredParams['error'], $filteredParams['success']); 

        // Build your new data
        $newData = [
            'form_serial' => $request['serial'] ?? '', 
            'form_model' => $request['model'] ?? '',
            'form_vendor' => $request['vendor'] ?? '',
            'form_site' => $request['site'] ?? '',
            'form_area' => $request['area'] ?? '',
            'form_shelf' => $request['shelf'] ?? '',
        ];

        $newData['add_form'] = ($request['multiple'] ?? false) ? 1 : 0;

        // Merge filtered old params with new data
        $finalQuery = http_build_query(array_merge($filteredParams, $newData));

        // Reconstruct the URL using ONLY the path
        $path = $urlParts['path'] ?? '/';
        $url = $path . '?' . $finalQuery;
          
        $user = GeneralModel::getUser();

        // see if cpu serial exists
        $find = DB::table('cpu_item')->where('serial_number', $request['serial'])->first();
        
        // check for ids of each field
        foreach (['vendor', 'model'] as $param) {
           $find_params = DB::table('cpu_'.$param)->where('id', $request[$param])->where('deleted', 0)->first();
           if (!$find_params) {
                return redirect()->to(route('cpus', ['error' => 'CPU '.$param.' not found for id: '.$request[$param]]));
            } 
        }

        // make sure shelf exists
        $find_shelf = DB::table('shelf')->where('id', $request['shelf'])->where('deleted', 0)->first();
        if (!$find_shelf) {
            return redirect()->to(route('cpus', ['error' => 'Shelf not found for id: '.$request['shelf']]));
        } 
        $values = [
                    'model_id' => $request['model'],
                    'vendor_id' => $request['vendor'],
                    'serial_number' => $request['serial'],
                    'shelf_id' => $request['shelf'],
                    'quantity' => 1,
                    'created_at' => now(), 
                    'updated_at' => now()
                ];
        
        if (!$find) {
            // add cpu

            $insert = DB::table('cpu_item')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'cpu_item',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'serial_number',
                    'previous_value' => '',
                    'new_value' => $request['serial']
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'cpu_item',
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
                TransactionModel::addCpuTransaction($transaction);
                return redirect()->to($url)->with('success', 'CPU added: "'.$request['serial'].'" with id: '.$insert.'.');
            } else {
                if ($find->deleted == 1) {
                    // remove delete, and update any changes.
                    // update 
                    unset($values['serial_number']);

                    foreach (array_keys((array)$find) as $key) {
                        if (!in_array($key, ['id', 'serial_number', 'updated_at', 'created_at'])) {
                            if ($values[$key] !== $find->$key) {
                                // update
                                $update = DB::table('cpu_item')->where('id', $find->id)->update([$key => $values[$key]]);

                                if ($update) {
                                    // changelog
                                    $changelog_info = [
                                        'user' => $user,
                                        'table' => 'cpu_item',
                                        'record_id' => $find->id,
                                        'action' => 'Update record',
                                        'field' => $key,
                                        'previous_value' => $find->$key,
                                        'new_value' => $values[$key]
                                    ];

                                    GeneralModel::updateChangelog($changelog_info);
                                    $transaction = [
                                        'table_name' => 'cpu_item',
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
                                    TransactionModel::addCpuTransaction($transaction);
                                } else {
                                    return redirect()->to($url)->with('error', 'Unable to insert database entry.');
                                }
                            }
                        }
                    }   
                    $data = ['id' => $find->id];
                    return CpuModel::restoreCpu($data);
 
                } else {
                    return redirect()->to($url)->with('error', 'CPU already exists.');
                }  
            }
        } else {
            return redirect()->to(route('cpus', ['error' => 'CPU already exists for serial number: '.$request['serial']]));
        }
    }

    static public function serialMatchChecker($request)
    {
        // search for the matching item
        $find = DB::table('cpu_item')->where('serial_number', $request['serial'])->first();
        
        if ($find) {
            if ($find->deleted == 0) {
                $results['error'] = "CPU already exists.";
            } else {
                $results['error'] = "Found a matching deleted CPU. Please restore this CPU instead of adding.";
            }
        } else {
            $results['skip'] = 1;
        }

        return $results;
    }  

    public static function returnCpuInfoAjax($id, $request)
    {
        $find = DB::Table('cpu_item')->where('id', $id)->first();

        if ($find) {
            $find_shelf = DB::Table('shelf')->where('id', $find->shelf_id)->first();
            $find_area = DB::Table('area')->where('id', $find_shelf->area_id)->first();
            $find->area_id = $find_area->id;
            $find->site_id = $find_area->site_id;
            return $find;
        } else {
            return ['error' => 'CPU not found for id: '.$id];
        }
    }

    static public function editCpu($request)
    {
        $cpu_id = $request['id'];
        $user = GeneralModel::getUser();

        // see if cpu exists
        $find = DB::table('cpu_item')->where('id', $cpu_id)->first();

        if ($find) {
            $update_data = [];
            foreach ($request as $key => $value) {
                if (in_array($key, ['model_id', 'vendor_id', 'serial_number', 'shelf_id'])) {
                    if ($value != $find->$key) {
                        $update_data[$key] = $value;
                    }
                }
            }

            if (!empty($update_data)) {
                $update_data['updated_at'] = now();
                $update = DB::table('cpu_item')->where('id', $cpu_id)->update($update_data);

                if ($update) {
                    foreach ($update_data as $key => $value) {
                        if (in_array($key, ['model_id', 'vendor_id', 'serial_number', 'shelf_id'])) {
                            if ($value != $find->$key) {
                                $update_data[$key] = $value;

                                // changelog
                                $changelog_info = [
                                    'user' => $user,
                                    'table' => 'cpu_item',
                                    'record_id' => $cpu_id,
                                    'action' => 'Edit record',
                                    'field' => $key,
                                    'previous_value' => $find->$key,
                                    'new_value' => $value
                                ];

                                GeneralModel::updateChangelog($changelog_info);

                                if ($key == 'shelf_id') {
                                    $transaction = [
                                        'table_name' => 'cpu_item',
                                        'item_id' => $cpu_id,
                                        'type' => 'move',
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'username' => $user['username'],
                                        'shelf_id' => $value,
                                        'reason' => 'Move CPU',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                    TransactionModel::addCpuTransaction($transaction);
                                }
                            }
                        }
                    }
                    return redirect()->to(GeneralModel::previousURL())->with('success', 'CPU with id: '.$cpu_id.' updated.');
                } else {
                    return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to update CPU with id: '.$cpu_id.'.');
                }
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('info', 'No changes made to CPU with id: '.$cpu_id.'.');
            }
        } else {
            return redirect()->to(route('cpus', ['error' => 'CPU not found for id: '.$cpu_id.'.']));
        }
    }

    static public function restoreCpu($request)
    {
        $cpu_id = $request['id'];
        $user = GeneralModel::getUser();

        $find = DB::table('cpu_item')->where('id', $cpu_id)->where('deleted', 1)->first();

        if ($find) {
            $update = DB::table('cpu_item')->where('id', $find->id)->update(['deleted' => 0, 'quantity' => 1, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'cpu_item',
                    'record_id' => $cpu_id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);

                $transaction = [
                    'table_name' => 'cpu_item',
                    'item_id' => $cpu_id,
                    'type' => 'restore',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => 'Item Restored',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addCpuTransaction($transaction);
                return redirect()->to(GeneralModel::previousURL())->with('success', 'CPU restored, with id: '.$cpu_id.'.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to insert database entry.');
            }
        } else {
            // cpu doesnt exist
            return redirect()->to(GeneralModel::previousURL())->with('error', 'CPU not found with id: '.$cpu_id.'.');
        }
    }

    static public function deleteCpu($request)
    {
        $cpu_id = $request['id'];
        $reason = $request['reason'];
        $user = GeneralModel::getUser();

        // see if cpu exists
        $find = DB::table('cpu_item')->where('id', $cpu_id)->first();

        if ($find && $find->deleted == 0) {
            $update = DB::table('cpu_item')->where('id', $cpu_id)->update(['deleted' => 1, 'quantity' => 0, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'cpu_item',
                    'record_id' => $find->id,
                    'action' => 'Delete record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 1
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'cpu_item',
                    'item_id' => $cpu_id,
                    'type' => 'delete',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addCpuTransaction($transaction);
                return redirect(GeneralModel::previousURL())->with('success', 'CPU with serial number: '.$find->serial_number.' and id: '.$cpu_id.' delete.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to delete CPU with id: '.$cpu_id.'.');
            }
        } elseif ($find && $find->deleted == 1) {
            return redirect()->to(route('cpus', ['error' => 'CPU already deleted for id: '.$cpu_id]));
        } else {
            return redirect()->to(route('cpus', ['error' => 'CPU not found for id: '.$cpu_id.'.']));
        }
    }

}
