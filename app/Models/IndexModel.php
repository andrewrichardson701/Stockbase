<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndexModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndexModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndexModel query()
 * @mixin \Eloquent
 */
class IndexModel extends Model
{
    //
    static public function queryData($request)
    {
        $q_oos = isset($request['oos']) ? (int)$request['oos'] : 0;
        $q_site = isset($request['site']) ? (int)$request['site'] : 0;
        $q_area = isset($request['area']) ? (int)$request['area'] : 0;
        $q_shelf = isset($request['shelf']) ? $request['shelf'] : "";
        $q_name = isset($request['name']) ? $request['name'] : "";
        $q_sku = isset($request['sku']) ? $request['sku'] : "";
        $q_tag = isset($request['tag']) ? $request['tag'] : "";
        $q_manufacturer = isset($request['manufacturer']) ? $request['manufacturer'] : "";
        $q_page = isset($request['page']) ? (int)$request['page'] : 1;
        if ($q_page == '' || $q_page < 1) { $q_page = 1; }
        $q_rows = isset($request['rows']) ? ($request['rows'] == 50 || $request['rows'] == 100 ? (int)$request['rows'] : 10) : 10 ;
        
        $q_data = ['oos' => $q_oos,
                    'site' => $q_site,
                    'area' => $q_area,
                    'shelf' => $q_shelf,
                    'name' => $q_name,
                    'sku' => $q_sku,
                    'tag' => $q_tag,
                    'manufacturer' => $q_manufacturer,
                    'page' => $q_page,
                    'rows' => $q_rows,
                ];

        return $q_data;
    }
}
