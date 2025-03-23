<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

class OpticsModel extends Model
{
    //
    static public function generateOpticWhereArray($array) 
    {
        // type, speed, mode, connector, distance, site

        // search
        
        $return = [];
        $optic_keys = ['type', 'speed', 'mode', 'connector', 'distance'];

        if (!empty($array)) {
            foreach($array as $key => $row) {
                if ($key == "site") {
                    $return[] = ['where' => "site.id = ?", 'value' => $array[$key]];
                } elseif ($key == "search") {
                    $value = $array[$key];
                    $return[] = ['where' => "(optic_item.serial_number LIKE '%$value%' 
                                                OR optic_item.model LIKE '%$value%' 
                                                OR optic_item.spectrum LIKE '%$value%' 
                                                OR optic_vendor.name LIKE '%$value%' 
                                                OR optic_type.name LIKE '%$value%' 
                                                OR optic_connector.name LIKE '%$value%'
                                                OR optic_distance.name LIKE '%$value%'
                                                OR optic_item.mode LIKE '%$value%' 
                                                OR optic_speed.name LIKE '%?%')", 'value' => $value];
                } elseif (in_array($key, $optic_keys)) {
                    if ($key == "mode") {
                        $return[] = ['where' => "optic_item.mode = ?", 'value' => $array[$key]];
                    } else {
                        $return[] = ['where' => "optic_$key.id = ?", 'value' => $array[$key]];
                    }
                }
            }
        } 
        
        return $return;

    }

    static public function getOptics($where_array = [], $orderby = 'type', $deleted = 0, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $wheres = OpticsModel::generateOpticWhereArray($where_array) ;
        $order = OpticsModel::getOpitcsOrderBy($orderby);

        $totalCount = count(OpticsModel::getOpticsList($wheres, $order, $deleted, 0, 0));

        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $optics = GeneralModel::formatArrayOnIdAndCount(OpticsModel::getOpticsList($wheres, $order, $deleted, $limit, $offset));
        
        foreach ($optics['rows'] as $key => $optic){
            $comment_data = GeneralModel::formatArrayOnIdAndCount(OpticsModel::getOpticsComments($optic['id']));
            $optics['rows'][$key]['comment_data'] = $comment_data;
        }

        $optics['total_count'] = $totalCount;
        $optics['pages'] = (int)ceil($totalCount / $limit);
        $optics['page'] = $page;
        $optics['results_per_page'] = $limit;
        $optics['offset'] = $offset;

        return $optics;
    }

    static public function getOpitcsOrderBy($orderby) 
    {
        $order = "optic_type.id, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
        switch ($orderby) {
            case 'type':
                $order = "optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'connector':
                $order = "optic_connector.name, optic_type.name, optic_vendor.name, optic_item.model, optic_item.serial_number";
                break;
            case 'distance':
                $order = "optic_distance.name, optic_type.name, optic_connector.id, optic_vendor.name, optic_item.model, optic_item.serial_number";
                break;
            case 'model':
                $order = "optic_item.model, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.serial_number";
                break;
            case 'speed':
                $order = "optic_speed.id, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'mode':
                $order = "optic_item.mode, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'serial':
                $order = "optic_item.serial_number, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model";
                break;
            case 'vendor':
                $order = "optic_vendor.name, optic_type.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            default:
                $order = "optic_type.id, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
        }

        return $order;
    }

    static public function getOpticsComments($optic_id)
    {
        $return = [];

        $instance = new self();
        $instance->setTable('optic_comment');

        $data = $instance->select('optic_comment.id AS id',
                                    'optic_comment.item_id AS item_id',
                                    'optic_comment.comment AS comment',
                                    'optic_comment.user_id AS user_id',
                                    'optic_comment.timestamp AS timestamp',
                                    'optic_comment.deleted AS deleted',
                                    'users.username AS username')
                            ->join('users', 'users.id', '=', 'optic_comment.user_id')
                            ->where('optic_comment.item_id', '=', $optic_id)
                            ->where('optic_comment.deleted', '=', 0)
                            ->orderBy('timestamp', 'desc')
                            ->get()
                            ->toArray();

        return $data;
    }

    static public function getOpticsList($where_array = [], $orderby = 'type', $deleted = 0, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('optic_item');

        $query = $instance->select(
                        'optic_item.id AS id',
                        'optic_item.model AS model',
                        'optic_item.serial_number AS serial_number',
                        'optic_item.mode AS mode',
                        'optic_item.spectrum AS spectrum',
                        'optic_item.quantity AS quantity',
                        'optic_item.deleted AS deleted',
                        'optic_vendor.id AS vendor_id',
                        'optic_vendor.name AS vendor_name',
                        'optic_type.id AS type_id',
                        'optic_type.name AS type_name',
                        'optic_connector.id AS connector_id',
                        'optic_connector.name AS connector_name',
                        'optic_distance.id AS distance_id',
                        'optic_distance.name AS distance_name',
                        'optic_speed.id AS speed_id',
                        'optic_speed.name AS speed_name',
                        'site.id AS site_id',
                        'site.name AS site_name')
                    ->join('optic_vendor', 'optic_item.vendor_id', '=', 'optic_vendor.id')
                    ->join('optic_type', 'optic_item.type_id', '=', 'optic_type.id')
                    ->join('optic_connector', 'optic_item.connector_id', '=', 'optic_connector.id')
                    ->join('optic_speed', 'optic_item.speed_id', '=', 'optic_speed.id')
                    ->join('optic_distance', 'optic_item.distance_id', '=', 'optic_distance.id')
                    ->join('site', 'optic_item.site_id', '=', 'site.id');

        if (!empty($where_array)) {
            foreach ($where_array as $where) {
                $query->whereRaw($where['where'], $where['value']);
            }
        }

        $query->where('optic_item.deleted', '=', $deleted);

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
}
