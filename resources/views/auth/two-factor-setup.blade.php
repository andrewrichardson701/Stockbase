{{-- resources/views/auth/two-factor-setup.blade.php --}}

<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div style="max-width: 400px; margin: 2rem auto; padding: 1rem; border: 1px solid #ccc; border-radius: 8px;">
        <h1 class="block text-gray-700 dark:text-gray-300" style="font-size: 14pt; margin-bottom:10px">Two-Factor Authentication Setup</h1>

        <x-input-label value="Scan this QR code with your authenticator app (Duo, Google Authenticator, Authy, etc):" />

        <div class="text-center align-middle" style="text-align: center; margin-bottom: 1rem;">
            <img style="margin:auto; margin-top:10px" src="data:image/png;base64,{{ $qrCodeImageData }}" alt="2FA QR Code" />
        </div>

        <x-input-label value="If you cannot scan the QR code, you can enter this code manually:" />

        <pre class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                style="padding: 8px; font-weight: bold;">{{ $secret }}</pre>

        <form method="POST" action="{{ route('two-factor.enable') }}" style="margin-top:10px">
            @csrf

            <x-input-label for="code" value="Enter the 6-digit code from your app:" />
            <x-text-input class="block mt-1 w-full" 
                id="code"
                name="code"
                type="text"
                maxlength="6"
                pattern="\d{6}"
                required
                autofocus
                style="width: 100%; padding: 8px; margin-bottom: 1rem; font-size: 1.2rem;"
                placeholder="Enter your 2FA code" 
            />

            @error('code')
                <div style="color: red; margin-bottom: 1rem;">{{ $message }}</div>
            @enderror

            <div class="flex items-center mt-4">
                <table style="width:100%">
                    <tr>
                        <td class="align-left">
                            <x-primary-button class="ms-2 me-2" type="submit" style="background-color:#28a745">Enable 2FA</x-primary-button>
                        </td>
                        <td style="text-align: right">
                            <x-primary-button class="me-2" type="button" style="margin-right:.5rem; background-color:#ffc107" onclick="event.preventDefault(); document.getElementById('logout').submit();">Cancel</x-primary-button>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <form id="logout" style="display:none" method="POST" action="{{ route('logout') }}">
            @csrf
        </form>
    </div>
</x-guest-layout>

@include('foot')