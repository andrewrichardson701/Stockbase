<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\ResponseHandlingModel;
use App\Models\ProfileModel;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // dd($request->all());
        $login_history = ProfileModel::getLoginHistory();
        $log_colors = ProfileModel::getLoginColorClasses();
        
        $response_handling = ResponseHandlingModel::responseHandling($request->all());

        return view('profile', [
            'nav_data' => GeneralModel::navData('profile'),
            'response_handling' => $response_handling,
            'user' => $request->user(),
            'themes' => GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('theme')),
            'login_history' => $login_history,
            'log_colors' => $log_colors,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    static public function themeTesting(Request $request) 
    {
        $nav_highlight = 'changelog'; // for the nav highlighting
        $nav_data = GeneralModel::navData($nav_highlight);

        $response_handling = ResponseHandlingModel::responseHandling($request->all());

        $themes = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('theme'));

        $params = [];
        return view('theme-testing', ['params' => $params,
                                                'nav_data' => $nav_data,
                                                'response_handling' => $response_handling,
                                                'themes' => $themes,
                                                ]
                                            );
    }

    public static function uploadTheme(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'theme-name' => 'string|required',
                'theme-file-name' => 'string|required',
                'css-file' => 'required|file|mimetypes:text/css,text/plain|max:10000'
            ]);
            if ($request->hasFile('css-file')) {
                $upload = ProfileModel::themeUpload($request);
                if (array_key_exists('success', $upload)) {
                    return redirect(GeneralModel::previousURL())->with('success', $upload['success']);
                } elseif (array_key_exists('error', $upload)) {
                    return redirect(GeneralModel::previousURL())->with('error', $upload['error']);
                } else {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unknown error in file upload');
                }
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'No file found');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    public static function reset2FA(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                '2fa_user_id' => 'integer|required',
                'submit' => 'required',
                '2fareset_submit' => 'required'
            ]);
            $data = $request->toArray();
            $user = GeneralModel::getUser();
            if ($data['2fa_user_id'] == $user['id']) {
                if (ProfileModel::reset2FA($user['id']) == 1){
                    return redirect(GeneralModel::previousURL())->with('success', 'Reset successfully');
                } else {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unabled to reset');
                }
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'User id missmtach');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    public static function enable2FA(Request $request)
    {
        if ($request['_token'] == csrf_token()) {

            $data = $request->toArray();
            $user = GeneralModel::getUser();

            if (isset($data['enable-2fa']) && $data['enable-2fa'] == 'on') {
                $state = 1;
            } else {
                $state = 0;
            }

            if (ProfileModel::enable2FA($user['id'], $state) == 1) {
                if ($state == 1) {
                    $status = 'enabled';
                } else {
                    $status = 'disabled';
                }
                return redirect(GeneralModel::previousURL())->with('success', '2FA '.$status);
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Unable to change 2FA');
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function resetPasswordView(Request $request)
    {
        $response_handling = ResponseHandlingModel::responseHandling($request->all());
        $user = $request->user();
        if ($user->password_expired !== 1) {
            return redirect()->route('index');
        }

        return view('auth.password-expired', [
            'response_handling' => $response_handling,
            'user' => $request->user(),
        ]);
    }

    static public function sendPasswordResetEmail(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'email' => 'string|required',
            ]);

            $reset = ProfileModel::sendPasswordResetEmail($request['email']);
            if ($reset) {
                if ($reset['type'] == 'status') {
                    return back()->with('status', __($reset['value']));
                } else {
                    return back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($reset['value'])]);
                }
            } else {
                return back()->withInput($request->only('email'))
                            ->withErrors(['email' => __('Unknown error occured.')]);
            }
            // return back()->with('status', __('Please check your inbox for the reset email, providing an account exists with this email.'));
            // return redirect(GeneralModel::previousURL())->with('success', 'Please check your inbox for the reset email, providing an account exists with this email.');
            
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }
}
