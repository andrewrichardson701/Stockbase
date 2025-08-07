<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockModel;

/**
 * @property int $id
 * @property int $user_id
 * @property int $stock_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel whereStockId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FavouritesModel whereUserId($value)
 * @mixin \Eloquent
 */
class FavouritesModel extends Model
{
    //
    protected $table = 'favourites'; // Specify your table name
    protected $fillable = [
        'user_id',
        'stock_id'
    ];

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
        $return['rows'] = [];
        
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

    public static function addFavourite($user_id, $stock_id)
    {
        $return = [];
        $return['type'] = 'add';

        $request = ['user_id' => $user_id, 'stock_id' => $stock_id];
        $insert = FavouritesModel::create($request);

        $id = $insert->id;
        if (is_numeric($id)) {
            $return['status'] = 'true';
        } else {
            $return['status'] = 'false';
        }

        echo(json_encode($return));
    }

    public static function removeFavourite($user_id, $stock_id)
    {
        $return = [];
        $return['type'] = 'remove';
        
        $record = FavouritesModel::where('user_id', $user_id)
            ->where('stock_id', $stock_id)
            ->first();

        if ($record) {
            $record->delete();
            $return['status'] = 'true';
        } else {
            $return['status'] = 'false';
        }

        echo(json_encode($return));
    }
}
