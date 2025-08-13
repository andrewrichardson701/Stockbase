<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <div style="max-width: 400px; margin: 2rem auto; padding: 1rem; border: 1px solid #ccc; border-radius: 8px;">

        <section>
            <header>
                <h2 class="text-lg font-medium">
                    {{ __('Password Expired') }}
                </h2>

                <p class="mt-1 text-sm">
                    {{ __('Your password has expired, please set a new secure password.') }}
                </p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')
                <div>
                    <label class="block font-medium text-sm"
                        for="update_password_current_password">
                        {{ __('Current Password') }}
                    </label>
                    <input
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full theme-input"
                        id="update_password_current_password" name="current_password"
                        type="password" autocomplete="current-password">
                    @if ($errors->updatePassword->get('current_password'))
                        @foreach($errors->updatePassword->get('current_password') as $error)
                        <p class="red">{{ $error }}</p>
                        @endforeach
                    @endif
                </div>

                <div>
                    <label class="block font-medium text-sm"
                        for="update_password_password">
                        {{ __('New Password') }}
                    </label>
                    <input
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full theme-input"
                        id="update_password_password" name="password" type="password"
                        autocomplete="new-password">
                    @if ($errors->updatePassword->get('password'))
                        @foreach($errors->updatePassword->get('password') as $error)
                        <p class="red">{{ $error }}</p>
                        @endforeach
                    @endif
                </div>

                <div>
                    <label class="block font-medium text-sm"
                        for="update_password_password_confirmation">
                        {{ __('Confirm Password') }}
                    </label>
                    <input
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full theme-input"
                        id="update_password_password_confirmation" name="password_confirmation"
                        type="password" autocomplete="new-password">
                    @if ($errors->updatePassword->get('password_confirmation'))
                        @foreach($errors->updatePassword->get('password_confirmation') as $error)
                        <p class="red">{{ $error }}</p>
                        @endforeach
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        style="color:black !important;">
                        {{ __('Save') }}
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm">{{ __('Saved.') }}</p>
                    @endif
                </div>
            </form>
        </section>
    </div>
</x-guest-layout>

@include('foot')
