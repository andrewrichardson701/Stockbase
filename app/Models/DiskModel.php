<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiskModel extends Model
{
    //

    static public function generateDiskWhereArray($array) 
    {
        
        $return = [];
        $disk_keys = ['type', 'speed', 'capacity', 'caddy', 'vendor'];

        if (!empty($array)) {
            foreach($array as $key => $row) {
                if ($key == "site") {
                    $return[] = ['where' => "site.id = ?", 'value' => $array[$key]];
                } elseif ($key == "search") {
                    $value = $array[$key];
                    $return[] = ['where' => "(disk_item.serial_number LIKE ? 
                                                OR disk_item.model LIKE ? 
                                                OR disk_item.form_factor LIKE ?
                                                OR disk_vendor.name LIKE ?
                                                OR disk_type.name LIKE ? 
                                                OR disk_capacity.capacity LIKE ?
                                                OR disk_caddy.vendor LIKE ?
                                                OR disk_speed.speed LIKE ?)", 
                                                'value' => ["%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%"]];
                } 
            }
        } 
        
        return $return;

    }

    static public function getDisksOrderBy($orderby) 
    {
        $order = "disk_type.id, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
        switch ($orderby) {
            case 'type':
                $order = "disk_type.name, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
                break;
            case 'model':
                $order = "disk_item.model, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_type.name, disk_item.serial_number";
                break;
            case 'speed':
                $order = "disk_speed.id, disk_type.name, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
                break;
            case 'caddy':
                $order = "disk_caddy.vendor, disk_type.name, disk_vendor.name, disk_capacity.capacity, disk_item.model, disk_item.serial_number";
                break;
            case 'serial':
                $order = "disk_item.serial_number, disk_type.name, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model";
                break;
            case 'vendor':
                $order = "disk_vendor.name, disk_type.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
                break;
            case 'destroy':
                $order = "disk_item.destroy DESC, disk_type.name, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
                break;
            case 'capacity':
                $order = "disk_capacity.capacity, disk_type.name, disk_vendor.name, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
                break;
            default:
                $order = "disk_type.id, disk_vendor.name, disk_capacity.capacity, disk_caddy.vendor, disk_item.model, disk_item.serial_number";
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
                        'disk_speed.speed AS speed_name',
                        'disk_item.form_factor AS form_factor',
                        'disk_caddy.id AS caddy_id',
                        'disk_caddy.vendor AS caddy_vendor',
                        'disk_capacity.id AS capacity_id',
                        'disk_capacity.capacity AS capacity',
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
}
