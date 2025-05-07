<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FavouritesModel;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property int $id
 * @property int $stock_id
 * @property string|null $upc
 * @property int $quantity
 * @property string|null $cost
 * @property string|null $serial_number
 * @property string|null $comments
 * @property int|null $manufacturer_id
 * @property int $shelf_id
 * @property int $is_container
 * @property int|null $deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereIsContainer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereManufacturerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereShelfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereStockId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereUpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemModel extends Model
{
    //
    protected $table = 'item'; // Specify your table name
    protected $fillable = ['stock_id', 'upc', 'quantity', 'cost', 'serial_number', 'comments', 'manufacturer_id', 'shelf_id', 'is_container', 'deleted'];

    public static function index()
    {

    }
}
