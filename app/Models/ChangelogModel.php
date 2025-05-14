<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangelogModel query()
 * @mixin \Eloquent
 */
class ChangelogModel extends Model
{
    //
    static public function getChangelog($limit = null) 
    {
        $instance = new self();
        $instance->setTable('changelog');

        return $instance->where('table_name', 'not like', '%login%')
                        ->when($limit !== null && is_numeric($limit), function ($query) use ($limit) {
                            $query->limit($limit);
                        })
                        ->orderby('id', 'desc')
                        ->orderby('timestamp', 'desc')
                        ->get()
                        ->toarray();
    }
}
