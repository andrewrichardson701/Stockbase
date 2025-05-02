<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FavouritesModel;
use Illuminate\Support\Facades\DB;

class ItemModel extends Model
{
    //
    protected $table = 'item'; // Specify your table name
    protected $fillable = ['stock_id', 'upc', 'quantity', 'cost', 'serial_number', 'comments', 'manufacturer_id', 'shelf_id', 'is_container', 'deleted'];

    public static function index()
    {

    }
}
