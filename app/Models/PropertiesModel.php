<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// use App\Models\FunctionsModel;
use App\Models\GeneralModel;


/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertiesModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertiesModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertiesModel query()
 * @mixin \Eloquent
 */
class PropertiesModel extends Model
{
    //
    static public function addProperty($request)
    {
        $valid_types = ['tag', 'manufacturer', 'site', 'area', 'shelf', 'optic_vendor', 'optic_type', 'optic_speed', 'optic_connector', 'optic_distance'];
        
        if (in_array($request['type'], $valid_types)) {
            $table = $request['type'];
        } else {
            return 'Error: invalid property.';
        }
        $name = isset($_POST['property_name']) ? $_POST['property_name'] : NULL;
        if ($name == NULL) { // all
            return 'Error: Name field empty.';
        } 
        $description = isset($request['description']) ? $request['description'] : ''; // site/area
        $site_id = isset($request['site_id']) ? $request['site_id'] : ''; // area
        $area_id = isset($request['area_id']) ? $request['area_id'] : ''; // shelf
        $type = $request['type'];

        $params['name'] = $name;
        if ($type == 'shelf') {
            $params['area_id'] = $area_id;
        }
        $matches = GeneralModel::getAllWhere($table, $params);

        if (count($matches) < 1) {
            // add the property

            $insert_data = [];
            $insert_data['name'] = $name; 
            $insert_data['created_at'] = now();
            $insert_data['updated_at'] = now();

            switch ($type) {
                case 'tag':
                case 'site':
                    $insert_data['description'] = $description;
                    break;
                case 'area':
                    $insert_data['description'] = $description;
                    $insert_data['site_id'] = $site_id;
                    break;
                case 'shelf':
                    $insert_data['area_id'] = $area_id;
                    break;
                default:

            }

            $insert = DB::table($table)->insertGetId($insert_data);
    
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => $table,
                'record_id' => $insert,
                'field' => 'name',
                'new_value' => $name,
                'action' => 'New record',
                'previous_value' => '',
            ];
    
            GeneralModel::updateChangelog($info);
    
            return 'Property Added.';

        } else {
            return 'Error: Property already exists.'; 
        }
    }

    static public function loadProperty($request)
    {
        $valid_types = ['tag', 'manufacturer', 'site', 'area', 'shelf'];
        
        if (isset($request['type']) && in_array($request['type'], $valid_types)) {
            $table = isset($request['type']);
        } else {
            return [];
        }

        $name = isset($_POST['property_name']) ? $_POST['property_name'] : NULL;
        if ($name == NULL) { // all
            return [];
        } 

        $instance = new self();
        $instance->setTable($table);
        
        return $instance->select(['id', 'name'])
                ->where('deleted', '=', 0)
                ->orderby('name')
                ->get()
                ->toArray();
    }
}
