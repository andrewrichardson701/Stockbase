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
}
