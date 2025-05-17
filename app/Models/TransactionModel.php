<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property int $id
 * @property int $stock_id
 * @property int $item_id
 * @property string $type
 * @property int $quantity
 * @property string|null $price
 * @property string|null $serial_number
 * @property string $reason
 * @property string|null $comments
 * @property string $date
 * @property string $time
 * @property string $username
 * @property int|null $shelf_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereShelfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereStockId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionModel whereUsername($value)
 * @mixin \Eloquent
 */
class TransactionModel extends Model
{
    //
    protected $table = 'transaction'; // Specify your table name
    protected $fillable = ['stock_id', 'item_id', 'type', 'quantity', 'price', 'serial_number', 'reason', 'comments', 'date', 'time', 'username', 'shelf_id'];

    static public function getTransactions($stock_id, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $totalCount = count(TransactionModel::getTransactionsList($stock_id, 0, 0));
        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $transactions = GeneralModel::formatArrayOnIdAndCount(TransactionModel::getTransactionsList($stock_id, $limit, $offset));
        
        $transactions['total_count'] = $totalCount;
        $transactions['pages'] = (int)ceil($totalCount / $limit);
        $transactions['page'] = $page;
        $transactions['results_per_page'] = $limit;
        $transactions['offset'] = $offset;

        return $transactions;
    }

    static public function getTransactionsList($stock_id=null, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('transaction as t');

        $query = $instance->select(
                                ['t.id as id',
                                't.stock_id AS stock_id',
                                't.item_id AS item_id',
                                't.type AS type',
                                't.quantity as quantity',
                                't.price AS price',
                                't.serial_number AS serial_number',
                                't.reason AS reason',
                                't.comments AS comments',
                                't.date AS date',
                                't.time AS time',
                                't.username AS username',
                                't.shelf_id AS shelf_id',
                                's.name AS shelf_name',
                                'a.id AS area_id',
                                'a.name AS area_name',
                                'si.id AS site_id',
                                'si.name AS site_name']
                            )
                            ->leftJoin('shelf as s', 's.id', '=', 't.shelf_id')
                            ->leftJoin('area as a', 'a.id', '=', 's.area_id')
                            ->leftJoin('site as si', 'si.id', '=', 'a.site_id')
                            ->when($stock_id !== null, function ($query) use ($stock_id) {
                                $query->where('t.stock_id', '=', $stock_id);
                            })
                            ->groupBy('t.id',
                                        't.item_id',
                                        't.stock_id',
                                        't.type',
                                        't.price',
                                        't.serial_number',
                                        't.reason',
                                        't.comments',
                                        't.date',
                                        't.time',
                                        't.username',
                                        't.shelf_id',
                                        's.name',
                                        'a.id',
                                        'a.name',
                                        'si.id',
                                        'si.name',
                                        'quantity') 
                            ->orderBy('t.date', 'desc') 
                            ->orderBy('t.time', 'desc')
                            ->orderBy('quantity', 'desc');

        if ($limit != 0) {
            $query->limit($limit);
        }
        
        if ($offset != 0) {
            $query->offset($offset);
        }
        $rows = $query->get()->toArray(); // Fetch results as an array

        $return = [];
        foreach ($rows as $row) {
            $row['class'] = TransactionModel::getTransactionColorClasses($row['type']);
            $return[] = $row;
        }

        return $return;
    }

    static public function getTransactionColorClasses($type=null)
    {
        $array = array(
                    'add' => 'transactionAdd',
                    'remove' => 'transactionRemove',
                    'delete' => 'transactionDelete',
                    'move' => 'transactionMove',
                    );
        
        if (isset($type)) {
            if (isset($array[$type])) {
                return $array[$type];
            } else {
                return null;
            }
        }

        return $array;
    }

    static public function addTransaction($request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'stock_id' => 'integer|required',
                'item_id' => 'integer|required',
                'type' => 'string|required',
                'quantity' => 'integer|required',
                'price' => 'numeric|nullable',
                'serial_number' => 'string|nullable',
                'date' => 'string|required',
                'time' => 'string|required',
                'username' => 'required',
                'shelf_id' => 'integer|required',
                'reason' => 'string|required'
            ]);
    
            $insert = TransactionModel::create($request->toArray());
            $id = $insert->id;

            if (is_numeric($id)) {
                return ['success' => $id];
            } else {
                return ['error' => 'non-numeric id'];
            }
        } else {
            return null;
        }
    }
}
