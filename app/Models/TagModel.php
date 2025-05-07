<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagModel query()
 * @mixin \Eloquent
 */
class TagModel extends Model
{
    //
    static public function getAllStockFromTags($tags = [])
    {
        $return = [];

        foreach($tags as $tag) {
            $return['rows'][$tag['id']] = $tag;
            $return['rows'][$tag['id']]['stock_data'] = StockModel::getStockDataByTag($tag['id']);
        }
        $return['count'] = count($tags) ?? 0;

        return $return;
    }

    static public function addTagToStock($tag_id, $stock_id) 
    {
        if (TagModel::verifyTagId($tag_id) == 1) {
            // tag verified 
            $insert = DB::table('stock_tag')->insertGetId([
                'stock_id' => $stock_id,
                'tag_id' => $tag_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return $insert;
        } else {
            return 0;
        }
    }

    static public function verifyTagId($tag_id) 
    {
        // check if tag id exists
        $instance = new self();
        $instance->setTable('tag');

        $rows = $instance->where('id', '=', $tag_id)
                        ->get()
                        ->toArray();
       
        if (count($rows) > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
