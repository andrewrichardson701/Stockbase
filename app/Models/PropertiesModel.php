<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// use App\Models\FunctionsModel;
use App\Models\GeneralModel;


/**
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
        $valid_types = ['tag', 'manufacturer', 'site', 'area', 'shelf', 'optic_vendor', 'optic_type', 'optic_speed', 'optic_connector', 'optic_distance', 'cable_types'];
        
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
        if ($type == 'cable_types') {
            $params['parent'] = $request['type-parent'];
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
                case 'cable_types':
                    $insert_data['description'] = $description;
                    $insert_data['parent'] = $request['parent'];
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

    static public function addFirstLocations($request)
    {
        $user = GeneralModel::getUser();

        // make sure the shelf count, area count and site count are all 0
        $find_site = DB::table('site')->first();
        $find_area = DB::table('area')->first();
        $find_shelf = DB::table('shelf')->first();

        if (!$find_site || !$find_area || !$find_shelf) {
            // there is no matching site area and shelf

            // add the site first
            $add_site = DB::table('site')->insertGetId(['name' => $request['site_name'], 
                                                        'description' => $request['site_description'], 
                                                        'updated_at' => now(), 
                                                        'created_at' => now()
                                                        ]);
            if ($add_site) {
                // site added - do the changelog

                $changelog_info = [
                    'user' => $user,
                    'table' => 'site',
                    'record_id' => $add_site,
                    'action' => 'New record',
                    'field' => 'name',
                    'previous_value' => '',
                    'new_value' => $request['site_name']
                ];

                GeneralModel::updateChangelog($changelog_info);
                
                // add the area with $add_site as site_id
                $add_area = DB::table('area')->insertGetId(['name' => $request['area_name'], 
                                                        'description' => $request['area_description'], 
                                                        'site_id' => $add_site,
                                                        'updated_at' => now(), 
                                                        'created_at' => now()
                                                        ]);

                if ($add_area) {
                    // area added - do the changelog

                    $changelog_info = [
                        'user' => $user,
                        'table' => 'area',
                        'record_id' => $add_area,
                        'action' => 'New record',
                        'field' => 'name',
                        'previous_value' => '',
                        'new_value' => $request['area_name']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                    
                    // add the shelf with $add_area as area_id
                    $add_shelf = DB::table('shelf')->insertGetId(['name' => $request['shelf_name'], 
                                                            'area_id' => $add_area,
                                                            'updated_at' => now(), 
                                                            'created_at' => now()
                                                            ]);
                    if ($add_shelf) {
                        // area added - do the changelog

                        $changelog_info = [
                            'user' => $user,
                            'table' => 'shelf',
                            'record_id' => $add_shelf,
                            'action' => 'New record',
                            'field' => 'name',
                            'previous_value' => '',
                            'new_value' => $request['shelf_name']
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect(GeneralModel::previousURL())->with('success', 'Initial locations added!'); 
                    } else {
                        return redirect(GeneralModel::previousURL())->with('error', 'Unable to add shelf to database.'); 
                    }
                } else {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unable to add area to database.'); 
                }
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Unable to add site to database.'); 
            }
        } else {
           return redirect(GeneralModel::previousURL())->with('error', 'Cannot add locations. There are already locations in the DB.'); 
        }
    }
}
