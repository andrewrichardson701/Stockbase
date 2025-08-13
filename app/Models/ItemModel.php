<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FavouritesModel;
use Illuminate\Support\Facades\DB;

/**
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
    public static function editItem($request) 
    {
        $user = GeneralModel::getUser();
        if (isset($request['container-toggle']) && $request['container-toggle'] == 'on') {
            $is_container = 1;
        } else {
            $is_container = 0;
        }
        // find the match
        $find = DB::table('item')
                ->where('id', '=', $request['item-id'])
                ->where('deleted', '=', 0)
                ->first();

        if ($find) {
            // item exists 
            $update = DB::table('item')
                        ->where('id', $request['item-id'])
                        ->update(['manufacturer_id' => $request['manufacturer_id'], 
                                            'upc' => $request['upc'], 
                                            'serial_number' => $request['serial_number'], 
                                            'cost' => $request['cost'],
                                            'comments' => $request['comments'],
                                            'is_container' => $is_container,
                                            'updated_at' => now()]);
            if ($update) {
                // update changelog for each event

                if ($find->manufacturer_id !== $request['manufacturer_id']) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'manufacturer_id',
                        'previous_value' => $find->manufacturer_id,
                        'new_value' => $request['manufacturer_id']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                if ($find->upc !== $request['upc']) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'upc',
                        'previous_value' => $find->upc,
                        'new_value' => $request['upc']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                if ($find->serial_number !== $request['serial_number']) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'serial_number',
                        'previous_value' => $find->serial_number,
                        'new_value' => $request['serial_number']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                if ($find->cost !== $request['cost']) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'cost',
                        'previous_value' => $find->cost,
                        'new_value' => $request['cost']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                if ($find->comments !== $request['comments']) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'comments',
                        'previous_value' => $find->comments,
                        'new_value' => $request['comments']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                if ($find->is_container !== $is_container) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $find->id,
                        'action' => 'Update record',
                        'field' => 'is_container',
                        'previous_value' => $find->is_container,
                        'new_value' => $is_container
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                }

                return redirect(GeneralModel::previousURL())->with('success', 'Item updated for id: '.$find->id.'.');
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Update not saved.');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'Item not found');
        }

    }
}
