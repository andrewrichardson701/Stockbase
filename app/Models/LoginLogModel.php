<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionsModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;


/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLogModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLogModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLogModel query()
 * @mixin \Eloquent
 */
class LoginLogModel extends Model
{
    //
    static public function updateLoginLog($type, $auth, $email, $user_id=null) 
    {
        // type = login_log type field

        $data = [
            'type' => $type,
            'email' => $email,
            'user_id' => $user_id,
            'ip_address' => Request::ip(),
            'timestamp' => now(),
            'auth' => $auth,
            'updated_at' => now(),
            'created_at' => now()
        ];

        $insert = DB::table('login_log')->insertGetId($data);

        if ($insert) {
            if ($type == 'login') {
                Session::put('login_log_id', $insert);
                SessionModel::updateSessionLog('active');
            } elseif ($type == 'logout') {
                SessionModel::updateSessionLog('inactive');
            } 
            return $insert;
        } else {
            return null;
        }

    }
}
