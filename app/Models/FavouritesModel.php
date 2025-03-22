<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockModel;

class FavouritesModel extends Model
{
    //
    static public function getUserFavourites($user_id)
    {
        $return = [];
        $instance = new self();
        $instance->setTable('favourites');

        $data = $instance->where('user_id', '=', $user_id)
                        ->join('stock', 'stock.id', '=', 'favourites.stock_id')
                        ->orderby('stock.name')
                        ->get()
                        ->toarray();

        $return['count'] = count($data) ?? 0;
        foreach ($data as $row) {
            $return['rows'][$row['stock_id']] = $row;
        }

        return $return;
    }

    public static function getFavouriteData($user_id)
    {
        $return = [];
        $return['rows'] = [];

        $favourites = FavouritesModel::getUserFavourites($user_id);

        foreach ($favourites['rows'] as $favourite) {
            $stock_id = $favourite['stock_id'];
            $favourite_data = StockModel::getFavouriteStockData($stock_id);
            $return['rows'][$stock_id] = $favourite_data;
        }
        
        $return['count'] = count($return['rows']) ?? 0;

        return $return;
    }
}
