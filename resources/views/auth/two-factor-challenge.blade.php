<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <div style="max-width: 400px; margin: 2rem auto; padding: 1rem; border: 1px solid #ccc; border-radius: 8px;">
        <h1 class="block text-gray-700 dark:text-gray-300" style="font-size: 14pt; margin-bottom:10px">Two-Factor Authentication</h1>

        <x-input-label value="Please enter the 6-digit code from your authenticator app (Duo, Google Authenticator, Authy, etc):"/>

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <x-text-input id="code" class="block mt-1 w-full" name="code" placeholder="Enter your 2FA code" style="margin-top:10px" />
            @error('code')
                <div style="color: red; margin-bottom: 1rem;">{{ $message }}</div>
            @enderror
            <div class="flex items-center mt-4">
                <table style="width:100%">
                    <tr>
                        <td class="align-left">
                            <x-primary-button class="ms-2 me-2" type="submit" style="background-color:#28a745">Verify</x-primary-button>
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
