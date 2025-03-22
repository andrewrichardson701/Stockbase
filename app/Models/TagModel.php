<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
