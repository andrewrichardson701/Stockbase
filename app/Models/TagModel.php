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
            $user = GeneralModel::getUser();
            $info = [
                'user' => $user,
                'table' => 'stock_tag',
                'record_id' => $insert,
                'field' => 'stock_id',
                'new_value' => $stock_id,
                'action' => 'New record',
                'previous_value' => '',
            ];
            GeneralModel::updateChangelog($info);
            return $insert;
        } else {
            return 0;
        }
    }

    static public function removeTagFromStock($tag_id, $stock_id) 
    {
        if (TagModel::verifyTagId($tag_id) == 1) {
            // tag verified 
            $record = DB::table('stock_tag')
                ->where('stock_id', $stock_id)
                ->where('tag_id', $tag_id)
                ->first();

            if ($record) {
                DB::table('stock_tag')
                    ->where('stock_id', $stock_id)
                    ->where('tag_id', $tag_id)
                    ->delete();

                $user = GeneralModel::getUser();
                $info = [
                    'user' => $user,
                    'table' => 'stock_tag',
                    'record_id' => $record->id,
                    'field' => 'stock_id',
                    'new_value' => '',
                    'action' => 'Delete record',
                    'previous_value' => $stock_id,
                ];
                GeneralModel::updateChangelog($info);
                return $record->id;
            } else {
                return 0;
            }
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

    static public function getTagsForStock($stock_id)
    {
        // get a list of tags assigned to a stock_id
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $rows = $instance->select(['stock_tag.id as id', 'stock_tag.stock_id as stock_id', 'tag.id as tag_id', 'tag.name as tag_name', 
                                            'stock_tag.created_at as created_at', 'stock_tag.created_at as updated_at'])
                        ->where('stock_id', '=', $stock_id)
                        ->join('tag', 'tag.id', '=', 'stock_tag.tag_id')
                        ->get()
                        ->toArray();

        $return = GeneralModel::formatArrayOnIdAndCount($rows);

        return $return;
    }


}
