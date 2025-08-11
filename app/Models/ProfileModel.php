<?php

namespace App\Models;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Services\EmailService;
use Illuminate\Support\Facades\App;

/**
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

        $email = $user['email'];

        $login_history = GeneralModel::getAllWhere('login_log', ['email' => $email], 'id', 'desc');
        return $login_history;

    }

    static public function reset2FA($user_id) 
    {
        $data = ['two_factor_secret' => null];

        $user = User::find($user_id);

        if ($user) {
            if (User::where('id', $user_id)->update($data)) {
                // update changelog
                $user = GeneralModel::getUser();
                $info = [
                    'user' => $user,
                    'table' => 'users',
                    'record_id' => $user_id,
                    'field' => 'two_factor_secret',
                    'new_value' => null,
                    'action' => 'Update record',
                    'previous_value' => '********',
                ];
                GeneralModel::updateChangelog($info);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        
    }

    static public function enable2FA($user_id, $state) 
    {
        $data = ['two_factor_enabled' => $state];
        // check if state change needed
        $user = User::find($user_id);

        if ($user && $user['two_factor_enabled'] !== $state) {
            if (User::where('id', $user_id)->update($data)) {
                // update changelog
                $user = GeneralModel::getUser();
                $info = [
                    'user' => $user,
                    'table' => 'users',
                    'record_id' => $user_id,
                    'field' => 'two_factor_enabled',
                    'new_value' => $state,
                    'action' => 'Update record',
                    'previous_value' => $user['two_factor_enabled'],
                ];
                GeneralModel::updateChangelog($info);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    static public function generatePasswordResetToken(string $email): string
    {
        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token), // hashed for security
            'created_at' => Carbon::now(),
        ]);

        return $token; // return the raw token to include in email
    }

    static public function sendPasswordResetEmail($email)
    {
        $user = DB::table('users')->where('email', $email)->first();
        
        if ($user) {
            $config = GeneralModel::config();

            if ($config['smtp_enabled'] == 0) {
                return ['type' => 'error', 'value' => 'SMTP disabled globally. No email can be sent.'];
            }

            if ($user->auth == 'ldap') {
                return ['type' => 'error', 'value' => 'Cannot reset passwords for LDAP users. Please contact your IT administrator.'];
            }

            $reset_email = route('password.reset', ['token' => ProfileModel::generatePasswordResetToken($email)]) . '?email=' . urlencode($email);

            $mailer = App::make(EmailService::class);
            $mailer->sendEmail(
                $user->email,
                $user->name,
                'use-default',
                $config['system_name'].' - Password Reset',
                '<p>A password reset has been requested.<br>Please use the following link to reset your password: <a href="'.$reset_email.'">'.$reset_email.'</a>.</a></p>',
                1 // notif_id
            );
            return ['type' => 'status', 'value' => 'Please check your inbox for the reset email.'];
        } else {
            return ['type' => 'error', 'value' => 'No user found. Please double check the email and try again.'];
        }
    }
}
