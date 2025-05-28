<?php

namespace App\Models;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileModel query()
 * @mixin \Eloquent
 */
class ProfileModel extends Model
{
    //

    public static function themeUpload($request)
    {
        $return = [];

        $file = $request->file('css-file');
        $data = $request->toArray();
    
        // Create a unique filename
        $filename =  $data['theme-file-name'] . "." . $file->getClientOriginalExtension();
        $themename = $data['theme-name'];

        // Move to public/img/stock
        $destinationPath = public_path('css');
        if (GeneralModel::isValidCSSFile($file->getRealPath()) == true) {
            $file->move($destinationPath, $filename);
    
            // Save to DB
            $new_theme_id = DB::table('theme')->insertGetId([
                'name' => $themename,
                'file_name' => $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            // update changelog
            $user = GeneralModel::getUser();
            $info = [
                'user' => $user,
                'table' => 'theme',
                'record_id' => $new_theme_id,
                'field' => 'file_name',
                'new_value' => $themename,
                'action' => 'New record',
                'previous_value' => '',
            ];
            GeneralModel::updateChangelog($info);

            $return['success'] = 'Uploaded successfully';
        } else {
            $return['error'] = 'Not a valid CSS file';
        }
        return $return;
    }

    static public function getLoginColorClasses($type=null)
    {
        $array = array(
                    'login' => 'transactionAdd',
                    'failed' => 'transactionRemove',
                    'logout' => 'transactionDelete',
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

    static public function getLoginHistory() 
    {
        $user = GeneralModel::getUser();

        $username = $user['username'];

        $login_history = GeneralModel::getAllWhere('login_log', ['username' => $username]);
        return $login_history;

    }

    static public function reset2FA($user_id) 
    {
        $data = ['2fa_secret' => null];
        if (User::where('id', $user_id)->update($data)) {
            return 1;
        } else {
            return 0;
        }
    }
}
