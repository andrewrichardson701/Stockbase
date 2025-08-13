<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
// use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;

use App\Models\GeneralModel;
use Illuminate\Support\Facades\Session;

use App\Models\User;

use Illuminate\Support\Facades\DB;

class TwoFactorController extends Controller
{
    protected $twoFactorProvider;

    public function __construct(TwoFactorAuthenticationProvider $twoFactorProvider)
    {
        $this->twoFactorProvider = $twoFactorProvider;
    }

    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            // No secret yet — generate new one and store in session
            $secret = $this->twoFactorProvider->generateSecretKey();
            session(['two_factor_secret_temp' => $secret]);
        } else {
            // Already has one — decrypt before using
            $secret = decrypt($user->two_factor_secret);
        }

        $companyName = config('app.name');
        $email = $user->email;

        $qrCodeUrl = $this->twoFactorProvider->qrCodeUrl($companyName, $email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new ImagickImageBackEnd() // requires imagick PHP extension, or use SvgImageBackEnd() if not installed
        );

        $writer = new Writer($renderer);

        $qrCodeImageData = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.two-factor-setup', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'qrCodeImageData' => $qrCodeImageData,
        ]);
    }

    public function enable(Request $request)
    {
        // dd($request->input()); 
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        $secret = session('two_factor_secret_temp', $user->two_factor_secret);

        if (! $secret) {
            return redirect()->route('two-factor.setup')->withErrors(['code' => 'No secret key found. Please try again.']);
        }

        try {
            $secretToVerify = decrypt($secret);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decrypt fails, assume it's already plain
            $secretToVerify = $secret;
        }
        

        if (! $this->twoFactorProvider->verify($secretToVerify, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->forceFill([
            'two_factor_secret' => encrypt($secretToVerify),
            'two_factor_enabled' => 1,
        ])->save();

        session()->forget('two_factor_secret_temp');
        Session::put('two_factor_request', 'complete');

        return redirect()->route('index')->with('status', 'Two-factor authentication enabled successfully.');
    }

    public function challenge(Request $request)
    {
        $userId = $request->session()->get('login.id');
        // $user = User::find($userId);

        // dd($request->session(), $userId, $user->email);
        return view('auth.two-factor-challenge', [
            
        ]);
    }

    public function challengeCheck(Request $request) 
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'code' => ['required', 'digits:6'],
            ]);
            $google2fa = new Google2FA();

            $google2fa->setWindow(2); // ±2 time steps (about ±1 minute)

            // Get stored secret
            $user = GeneralModel::getuser();
            $secret = decrypt($user['two_factor_secret']);

            // Get the code the user entered
            $code = $request->input('code');

            // Verify
            $isValid = $google2fa->verifyKey($secret, $code);

            if ($isValid) {
                // Mark 2FA as complete
                session(['two_factor_request' => 'complete']);
                return redirect()->intended('/');
            } else {
                return back()->withErrors(['code' => 'Invalid verification code'])->withInput();
            }
        } else {
            return back()->withErrors(['code' => 'Missing CSRF'])->withInput();
        }
    }
}