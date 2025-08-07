<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\GeneralModel;
use App\Models\LdapModel;
use App\Models\LoginLogModel;
use App\Models\SessionModel;

use Illuminate\Support\Facades\DB;

use LdapRecord\Models\ActiveDirectory\User as LdapUser;

use LdapRecord\Auth\BindException;
use LdapRecord\Models\Model;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request_input = $request->input();
        
        if (isset($request_input['local']) && $request_input['local'] == 'on') {
            // local auth
            $request->authenticate();

            $request->session()->regenerate();

            $user = GeneralModel::getuser();
            // update login_log with successful login
            LoginLogModel::updateLoginLog('login', $user['auth'], $user['email'], $user['id']);

            SessionModel::expireOldSessions(); // expire any old sessions

            return redirect()->intended(route('index', absolute: false));
        } else {
            // ldap
            $credentials = $request->only('email', 'password');

            // check if email is already in use
            $email_used = Generalmodel::getFirstwhere('users', ['email' => strtolower($credentials['email']), 'ldap_guid' => NULL]);
            if ($email_used) {
                return back()->withErrors([
                    'email' => 'LDAP auth failed. Email already in use.',
                ]);
            }

            // Attempt LDAP
            try {
                if (Auth::guard('ldap')->attempt([
                    'mail' => strtolower($credentials['email']), // or 'uid'/'sAMAccountName'
                    'password' => $credentials['password'],
                ])) {
                    /** @var LdapUser $ldapUser */
                    $ldapUser = Auth::guard('ldap')->user();
                    
                    /** @var User $localUser */
                    // Optionally sync to local users table
                    $localUser = User::firstOrCreate(
                        ['email' => strtolower($credentials['email'])],
                        ['auth' => 'ldap']
                    );

                    if ($localUser->auth !== 'ldap') {
                        DB::table('users')->where('id', $localUser->id)->update(['auth' => 'ldap']);
                    }

                    // check for user permissions
                    $permissions = GeneralModel::getFirstWhere('users_permissions', ['id' => $localUser->id]);

                    if (!$permissions) {
                        DB::table('users_permissions')->insert(['id' => $localUser->id, 'stock' => 1, 'created_at' => now(), 'updated_at' => now()]);
                    }

                    Auth::login($localUser);

                    return redirect()->intended(route('index', absolute: false));
                } else {
                    return back()->withErrors([
                        'email' => 'Failed to Auth.',
                    ]);
                }
            } catch (BindException $e) {
                Log::warning('LDAP bind failed: ' . $e->getMessage());
            }

            // Attempt local DB login
            if (Auth::attempt($credentials)) {
                return redirect()->intended('dashboard');
            }

            return back()->withErrors([
                'email' => 'Authentication failed via LDAP and database.',
            ]);
        }
        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = GeneralModel::getuser();
        // update the login_log with logout
        LoginLogModel::updateLoginLog('logout', $user['auth'], $user['email'], $user['id']);

        SessionModel::expireOldSessions(); // expire any old sessions
        
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        

        return redirect('/');
    }

    static public function ldapLogin($credentials) 
    {
        
    }

}
