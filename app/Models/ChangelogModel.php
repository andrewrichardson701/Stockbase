<?php

namespace App\Models;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel query()
 * @mixin \Eloquent
 */
class ChangelogModel extends Model
{
    //
    static public function getChangelog($limit = null, $offset = 0, $params = []) 
    {
        $instance = new self();
        $instance->setTable('changelog');

        return $instance->where('table_name', 'not like', '%login%')
                        ->when($limit !== null && is_numeric($limit), function ($query) use ($limit) {
                            $query->limit($limit);
                        })
                        ->when($offset > 0, function ($query) use ($offset) {
                            $query->offset($offset);
                        })
                        ->when(!empty($params), function ($query) use ($params) {
                            foreach($params as $param) {
                                $query->where($param['key'], $param['operator'], $param['value']);
                            }
                        })
                        ->orderby('id', 'desc')
                        ->orderby('timestamp', 'desc')
                        ->get()
                        ->toarray();
    }

    static public function getChangelogInfo($changelog = [], $params = [])
    {
        $table_names = GeneralModel::getDbTableNames();

        if (in_array($changelog['table_name'], $table_names)) {
            $record = DB::table($changelog['table_name'])
                ->where('id', $changelog['record_id'])
                ->when(!empty($params), function ($query) use ($params) {
                    foreach($params as $key => $param) {
                        $query->where($key, '=', $param);
                    }
                })
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                })
                ->toArray();

            if ($record && array_key_exists(0, $record)) {;
                return $record[0];
            } else {
                return 'deleted';
            }
        } else {
            return null;
        }
        
    }

    static public function getChangelogFull($limit = null, $offset = 0, $params = []) 
    {
        $changelog = ChangelogModel::getChangelog($limit, $offset, $params);

        foreach($changelog as $key => $log) {
            $changelog[$key]['info'] = ChangelogModel::getChangelogInfo($log);
        }

        return $changelog;
    }
}
