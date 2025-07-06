<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\Request;

class SessionModel extends Model
{
    //
    static public function createSessionLog($session_id)
    {
        $request = request(); // get current Request instance
        $user = GeneralModel::getUser();
        $data = [
            'sessions_id' => $session_id,
            'user_id' => $user['id'],
            'login_time' => time(),
            'ip_address' => Request::ip(),
            'browser' => SessionModel::getBrowser($request),
            'os' => SessionModel::getOS($request),
            'status' => 'active',
            'last_activity' => now(),
            'login_log_id' => Session::get('login_log_id') ?? 0,
            'updated_at' => now(),
            'created_at' => now()
        ];

        $insert = DB::table('session_log')->insertGetId($data);

        return $insert;
    }

    static public function updateSessionLog($status, $session_id = null)
    {
        if ($session_id == null) {
            $session_id = Session::getId();
        }


        $find_session_log = GeneralModel::getFirstWhere('session_log', ['sessions_id' => $session_id]);

        if ($find_session_log) {
            if ($status !== 'active') {
                DB::table('session_log')->where('sessions_id', $session_id)->update(['logout_time' => time(), 'status' => $status, 'last_activity' => now(), 'updated_at' => now()]);
                //check if sessions entry exists
                $find_session = DB::table('sessions')->where('id' ,$session_id)->first();
                if ($find_session) {
                    // delete sessions table entry
                    $find_session->delete();
                }
                return 1;
            } else {
                DB::table('session_log')->where('sessions_id', $session_id)->update(['status' => $status, 'last_activity' => now(), 'updated_at' => now()]);
                return 1;
            }
            
        } else {
            SessionModel::createSessionLog($session_id);
            return 1;
        }


    }

    public static function getBrowser($request)
    {
        $userAgent = $request->userAgent();
        $browser = 'Unknown';

        if (stripos($userAgent, 'MSIE') !== false || stripos($userAgent, 'Trident/') !== false) {
            $browser = 'Internet Explorer';
        } elseif (stripos($userAgent, 'Edge') !== false) {
            $browser = 'Microsoft Edge';
        } elseif (stripos($userAgent, 'OPR') !== false || stripos($userAgent, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (stripos($userAgent, 'Chrome') !== false) {
            $browser = 'Google Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $browser = 'Mozilla Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        }

        return $browser;
    }

    public static function getOS($request)
    {
        $userAgent = $request->userAgent();
        $os = 'Unknown';

        if (stripos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            $os = 'iOS';
        } elseif (stripos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (stripos($userAgent, 'Macintosh') !== false) {
            $os = 'Mac';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        }

        return $os;
    }

    static public function expireOldSessions() 
    {
        // get all session_log that are active  
        $active_sessions = GeneralModel::getAllWhere('session_log', ['status' => 'active']);

        if (count($active_sessions) > 0) {
            // check the time last activity
            foreach ($active_sessions as $session) {
                if (strtotime($session['last_activity']) < time()-1800) {
                    // expire the session
                    DB::table('session_log')->where('sessions_id', $session['id'])->update(['status' => 'expired', 'updated_at' => now()]);
                }
            }
        }
    }

    // to be run on every page
    static public function activityUpdates()
    {
        SessionModel::expireOldSessions();
        if (GeneralModel::getUser()) {
            SessionModel::updateSessionLog('active');
        }
    }

    static public function killSession($session_id)
    {
        $current_session = Session::getId();

        if ($session_id == $current_session) {
            return redirect(GeneralModel::previousURL())->with('error', 'Cannot kill current sessions.');
        }

        // kill the session
        $killed = SessionModel::updateSessionLog('killed', $session_id);

        if ($killed) {
            return redirect(GeneralModel::previousURL())->with('success', 'Session');
        }

    }

}
