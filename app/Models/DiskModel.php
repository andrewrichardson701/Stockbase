<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiskModel extends Model
{
    //

    static public function generateDiskWhereArray($array) 
    {
        
        $return = [];
        $disk_keys = ['type', 'speed', 'capacity', 'caddy', 'vendor', 'destroy', 'ssd', 'form_factor', 'rpm'];

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
                    $return[] = ['where' => "(disk_item.serial_number LIKE ? 
                                                OR disk_item.model LIKE ? 
                                                OR disk_item.form_factor LIKE ?
                                                OR disk_vendor.name LIKE ?
                                                OR disk_type.name LIKE ? 
                                                OR disk_capacity.name LIKE ?
                                                OR disk_caddy.name LIKE ?
                                                OR disk_speed.name LIKE ?
                                                OR disk_rpm.name LIKE ?)", 
                                                'value' => ["%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%"]];
                } elseif (in_array($key, $disk_keys)) {
                    if ($key == "form_factor") {
                        $return[] = ['where' => "disk_item.form_factor = ?", 'value' => $array[$key]];
                    } elseif ($key == "destroy") {
                        $return[] = ['where' => "disk_item.destroy = ?", 'value' => $array[$key]];
                    } elseif ($key == "ssd") {
                        $return[] = ['where' => "disk_item.ssd = ?", 'value' => $array[$key]];
                    } else {
                        $return[] = ['where' => "disk_$key.id = ?", 'value' => $array[$key]];
                    }
                }
            }
        } 

        return $return;        

    }

    static public function getDisksOrderBy($orderby) 
    {
        $order = "disk_type.id, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
        switch ($orderby) {
            case 'type':
                $order = "disk_type.name, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            case 'model':
                $order = "disk_item.model, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_type.name, disk_item.serial_number";
                break;
            case 'speed':
                $order = "disk_speed.id, disk_type.name, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            case 'rpm':
                $order = "disk_rpm.id, disk_type.name, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            case 'caddy':
                $order = "disk_caddy.name, disk_type.name, disk_vendor.name, disk_capacity.name, disk_item.model, disk_item.serial_number";
                break;
            case 'serial':
                $order = "disk_item.serial_number, disk_type.name, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model";
                break;
            case 'vendor':
                $order = "disk_vendor.name, disk_type.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            case 'destroy':
                $order = "disk_item.destroy DESC, disk_type.name, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            case 'capacity':
                $order = "disk_capacity.name, disk_type.name, disk_vendor.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
            default:
                $order = "disk_type.id, disk_vendor.name, disk_capacity.name, disk_caddy.name, disk_item.model, disk_item.serial_number";
                break;
        }

        return $order;
    }

    static public function getDisksList($where_array = [], $orderby = 'type', $deleted = 0, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('disk_item');

        $query = $instance->select(
                        ['disk_item.id AS id',
                        'disk_item.model AS model',
                        'disk_item.serial_number AS serial_number',
                        'disk_item.deleted AS deleted',
                        'disk_vendor.id AS vendor_id',
                        'disk_vendor.name AS vendor_name',
                        'disk_type.id AS type_id',
                        'disk_type.name AS type_name',
                        'disk_speed.id AS speed_id',
                        'disk_speed.name AS speed_name',
                        'disk_rpm.id AS rpm_id',
                        'disk_rpm.name AS rpm_name',
                        'disk_item.form_factor AS form_factor',
                        'disk_caddy.id AS caddy_id',
                        'disk_caddy.name AS caddy_name',
                        'disk_capacity.id AS capacity_id',
                        'disk_capacity.name AS capacity',
                        'disk_item.ssd AS ssd',
                        'site.id AS site_id',
                        'site.name AS site_name',
                        'area.id AS area_id',
                        'area.name AS area_name',
                        'area.site_id AS area_site_id',
                        'shelf.id AS shelf_id',
                        'shelf.name AS shelf_name',
                        'shelf.area_id AS shelf_area_id',
                        'disk_item.destroy AS destroy',
                        ])
                    ->join('disk_vendor', 'disk_item.vendor_id', '=', 'disk_vendor.id')
                    ->join('disk_type', 'disk_item.type_id', '=', 'disk_type.id')
                    ->join('disk_caddy', 'disk_item.caddy_id', '=', 'disk_caddy.id')
                    ->join('disk_speed', 'disk_item.speed_id', '=', 'disk_speed.id')
                    ->join('disk_rpm', 'disk_item.rpm_id', '=', 'disk_rpm.id')
                    ->join('disk_capacity', 'disk_item.capacity_id', '=', 'disk_capacity.id')
                    ->join('shelf', 'disk_item.shelf_id', '=', 'shelf.id')
                    ->join('area', 'shelf.area_id', '=', 'area.id')
                    ->join('site', 'area.site_id', '=', 'site.id');
                    
                    

        if (!empty($where_array)) {
            foreach ($where_array as $where) {
                $query->whereRaw($where['where'], $where['value']);
            }
        }

        $query->where('disk_item.deleted', '=', $deleted);

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

    static public function getDisks($where_array = [], $orderby = 'type', $deleted = 0, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $wheres = DiskModel::generateDiskWhereArray($where_array) ;
      
        $order = DiskModel::getDisksOrderBy($orderby);

        $totalCount = count(DiskModel::getDisksList($wheres, $order, $deleted, 0, 0));

        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $disks = GeneralModel::formatArrayOnIdAndCount(DiskModel::getDisksList($wheres, $order, $deleted, $limit, $offset));

        $disks['total_count'] = $totalCount;
        $disks['pages'] = (int)ceil($totalCount / $limit);
        $disks['page'] = $page;
        $disks['results_per_page'] = $limit;
        $disks['offset'] = $offset;

        return $disks;
    }


    static public function addDisk($request)
    {
        $previous = GeneralModel::previousURL();
        $query = http_build_query(
                [
                    'form_serial' => $request['serial'] ?? '', 
                    'form_model' => $request['model'] ?? '',
                    'form_capacity' => $request['capacity'] ?? '',
                    'form_vendor' => $request['vendor'] ?? '',
                    'form_type' => $request['type'] ?? '',
                    'form_speed' => $request['speed'] ?? '',
                    'form_rpm' => $request['rpm'] ?? '',
                    'form_caddy' => $request['caddy'] ?? '',
                    'form_site' => $request['site'] ?? '',
                    'form_ssd' => $request['ssd'] ?? '',
                    'form_form_factor' => $request['form_factor'] ?? '',
                    'form_destroy' => $request['destroy'] ?? '',
                    'form_area' => $request['area'] ?? '',
                    'form_shelf' => $request['shelf'] ?? '',
                ]
            );
        $url = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . $query;
          
        $user = GeneralModel::getUser();

        // see if disk serial exists
        $find = DB::table('disk_item')->where('serial_number', $request['serial'])->first();
        
        // check for ids of each field
        foreach (['vendor', 'type', 'capacity', 'speed', 'caddy', 'rpm'] as $param) {
           $find_params = DB::table('disk_'.$param)->where('id', $request[$param])->where('deleted', 0)->first();
           if (!$find_params) {
                return redirect()->to(route('disks', ['error' => 'Disk '.$param.' not found for id: '.$request[$param]]));
            } 
        }

        // make sure shelf exists
        $find_shelf = DB::table('shelf')->where('id', $request['shelf'])->where('deleted', 0)->first();
        if (!$find_shelf) {
            return redirect()->to(route('disks', ['error' => 'Shelf not found for id: '.$request['shelf']]));
        } 
        $values = [
                    'model' => $request['model'],
                    'vendor_id' => $request['vendor'],
                    'serial_number' => $request['serial'],
                    'type_id' => $request['type'],
                    'caddy_id' => $request['caddy'],
                    'capacity_id' => $request['capacity'],
                    'ssd' => $request['ssd'],
                    'speed_id' => $request['speed'],
                    'rpm_id' => $request['rpm'],
                    'destroy' => $request['destroy'],
                    'shelf_id' => $request['shelf'],
                    'form_factor' => $request['form_factor'],
                    'quantity' => 1,
                    'created_at' => now(), 
                    'updated_at' => now()
                ];
        
        if (!$find) {
            // add disk

            $insert = DB::table('disk_item')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'disk_item',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'serial_number',
                    'previous_value' => '',
                    'new_value' => $request['serial']
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'disk_item',
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
                TransactionModel::addDiskTransaction($transaction);
                return redirect()->to($url)->with('success', 'Disk added: "'.$request['serial'].'" with id: '.$insert.'.');
            } else {
                if ($find->deleted == 1) {
                    // remove delete, and update any changes.
                    // update 
                    unset($values['serial_number']);

                    foreach (array_keys((array)$find) as $key) {
                        if (!in_array($key, ['id', 'serial_number', 'updated_at', 'created_at'])) {
                            if ($values[$key] !== $find->$key) {
                                // update
                                $update = DB::table('disk_item')->where('id', $find->id)->update([$key => $values[$key]]);

                                if ($update) {
                                    // changelog
                                    $changelog_info = [
                                        'user' => $user,
                                        'table' => 'disk_item',
                                        'record_id' => $find->id,
                                        'action' => 'Update record',
                                        'field' => $key,
                                        'previous_value' => $find->$key,
                                        'new_value' => $values[$key]
                                    ];

                                    GeneralModel::updateChangelog($changelog_info);
                                    $transaction = [
                                        'table_name' => 'disk_item',
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
                                    TransactionModel::addDiskTransaction($transaction);
                                } else {
                                    return redirect()->to($url)->with('error', 'Unable to insert database entry.');
                                }
                            }
                        }
                    }   
                    $data = ['id' => $find->id];
                    return DiskModel::restore($data);
 
                } else {
                    return redirect()->to($url)->with('error', 'Disk already exists.');
                }  
            }
        } else {
            return redirect()->to(route('disks', ['error' => 'Disk already exists for serial number: '.$request['serial']]));
        }
    }



    static public function restoreDisk($request)
    {
        $disk_id = $request['id'];
        $user = GeneralModel::getUser();

        $find = DB::table('disk_item')->where('id', $disk_id)->where('deleted', 1)->first();

        if ($find) {
            $update = DB::table('disk_item')->where('id', $find->id)->update(['deleted' => 0]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'disk_item',
                    'record_id' => $disk_id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);

                $transaction = [
                    'table_name' => 'disk_item',
                    'item_id' => $disk_id,
                    'type' => 'restore',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => 'Item Restored',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addDiskTransaction($transaction);
                return redirect()->to(GeneralModel::previousURL())->with('success', 'Disk restored, with id: '.$disk_id.'.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to insert database entry.');
            }
        } else {
            // disk doesnt exist
            return redirect()->to(GeneralModel::previousURL())->with('error', 'Disk not found with id: '.$disk_id.'.');
        }
    }

    static public function deleteDisk($request)
    {
        $disk_id = $request['id'];
        $reason = $request['reason'];
        $user = GeneralModel::getUser();

        // see if disk exists
        $find = DB::table('disk_item')->where('id', $disk_id)->first();

        if ($find && $find->deleted == 0) {
            $update = DB::table('disk_item')->where('id', $disk_id)->update(['deleted' => 1]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'disk_item',
                    'record_id' => $find->id,
                    'action' => 'Delete record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 1
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'disk_item',
                    'item_id' => $disk_id,
                    'type' => 'delete',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => $find->shelf_id,
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addDiskTransaction($transaction);
                return redirect(GeneralModel::previousURL())->with('success', 'Disk with serial number: '.$find->serial_number.' and id: '.$disk_id.' delete.');
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to delete disk with id: '.$disk_id.'.');
            }
        } elseif ($find && $find->deleted == 1) {
            return redirect()->to(route('disks', ['error' => 'Disk already deleted for id: '.$disk_id]));
        } else {
            return redirect()->to(route('disks', ['error' => 'Disk not found for id: '.$disk_id.'.']));
        }
    }

    static public function moveDisk($request)
    {
        $disk_id = $request['id'];
        $shelf_id = $request['shelf'];
        $user = GeneralModel::getUser();

        // see if disk exists
        $find = DB::table('disk_item')->where('id', $disk_id)->first();

        if ($find) {
            // check if shelf exists
            $find_shelf = DB::table('shelf')->where('id', $shelf_id)->where('deleted', 0)->first();

            if ($find_shelf) {
                $update = DB::table('disk_item')->where('id', $disk_id)->update(['shelf_id' => $shelf_id]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'disk_item',
                        'record_id' => $disk_id,
                        'action' => 'Move record',
                        'field' => 'shelf_id',
                        'previous_value' => $find->shelf_id,
                        'new_value' => $shelf_id
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                    $transaction = [
                        'table_name' => 'disk_item',
                        'item_id' => $disk_id,
                        'type' => 'move',
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'username' => $user['username'],
                        'shelf_id' => $shelf_id,
                        'reason' => 'Move disk',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    TransactionModel::addDiskTransaction($transaction);
                    return redirect()->to(GeneralModel::previousURL())->with('success', 'Disk for id: '.$disk_id.' moved.');
                } else {
                    return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to move disk with id: '.$disk_id.'.');
                }
            } else {
                return redirect()->to(route('disks', ['error' => 'Shelf not found for id: '.$shelf_id.'.']));
            }
        } else {
            return redirect()->to(route('disks', ['error' => 'Disk not found for id: '.$disk_id.'.']));
        }
    }

    static public function serialMatchChecker($request)
    {
        // search for the matching item
        $find = DB::table('disk_item')->where('serial_number', $request['serial'])->first();
        
        if ($find) {
            if ($find->deleted == 0) {
                $results['error'] = "Disk already exists.";
            } else {
                $results['error'] = "Found a matching deleted disk. Please restore this disk instead of adding.";
            }
        } else {
            $results['skip'] = 1;
        }

        return $results;
    }  

    public static function returnDiskInfoAjax($id, $request)
    {
        $find = DB::Table('disk_item')->where('id', $id)->first();

        if ($find) {
            $find_shelf = DB::Table('shelf')->where('id', $find->shelf_id)->first();
            $find_area = DB::Table('area')->where('id', $find_shelf->area_id)->first();
            $find->area_id = $find_area->id;
            $find->site_id = $find_area->site_id;
            return $find;
        } else {
            return ['error' => 'Disk not found for id: '.$id];
        }
    }

    static public function editDisk($request)
    {
        $disk_id = $request['id'];
        $user = GeneralModel::getUser();

        // see if disk exists
        $find = DB::table('disk_item')->where('id', $disk_id)->first();

        if ($find) {
            $update_data = [];
            foreach ($request as $key => $value) {
                if (in_array($key, ['model', 'vendor_id', 'serial_number', 'type_id', 'caddy_id', 'capacity_id', 'ssd', 'speed_id', 'rpm_id', 'destroy', 'shelf_id', 'form_factor'])) {
                    if ($value != $find->$key) {
                        $update_data[$key] = $value;
                    }
                }
            }

            if (!empty($update_data)) {
                $update_data['updated_at'] = now();
                $update = DB::table('disk_item')->where('id', $disk_id)->update($update_data);

                if ($update) {
                    foreach ($update_data as $key => $value) {
                        if (in_array($key, ['model', 'vendor_id', 'serial_number', 'type_id', 'caddy_id', 'capacity_id', 'ssd', 'speed_id', 'rpm_id', 'destroy', 'shelf_id', 'form_factor'])) {
                            if ($value != $find->$key) {
                                $update_data[$key] = $value;

                                // changelog
                                $changelog_info = [
                                    'user' => $user,
                                    'table' => 'disk_item',
                                    'record_id' => $disk_id,
                                    'action' => 'Edit record',
                                    'field' => $key,
                                    'previous_value' => $find->$key,
                                    'new_value' => $value
                                ];

                                GeneralModel::updateChangelog($changelog_info);

                                if ($key == 'shelf_id') {
                                    $transaction = [
                                        'table_name' => 'disk_item',
                                        'item_id' => $disk_id,
                                        'type' => 'move',
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'username' => $user['username'],
                                        'shelf_id' => $value,
                                        'reason' => 'Move Disk',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                    TransactionModel::addDiskTransaction($transaction);
                                }
                            }
                        }
                    }
                    return redirect()->to(GeneralModel::previousURL())->with('success', 'Disk with id: '.$disk_id.' updated.');
                } else {
                    return redirect()->to(GeneralModel::previousURL())->with('error', 'Unable to update disk with id: '.$disk_id.'.');
                }
            } else {
                return redirect()->to(GeneralModel::previousURL())->with('info', 'No changes made to disk with id: '.$disk_id.'.');
            }
        } else {
            return redirect()->to(route('disks', ['error' => 'Disk not found for id: '.$disk_id.'.']));
        }
    }
}
