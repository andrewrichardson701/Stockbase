<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $head_data['config_compare']['system_name'] }} - Profile</title>
        @include('head')
        
    </head>
    <body class="font-sans antialiased">
        @include('nav')
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Page Heading -->
            <header class="dark:bg-gray-800 shadow" style="padding-top:60px">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Profile
                    </h2>
                </div>
            </header>
            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="p-4 sm:p-8  dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Profile Information') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Update your account\'s profile information and email address.') }}
                                        </p>
                                    </header>

                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                        @csrf
                                    </form>

                                    <form method="post" action="{{ route('profile.update') }}"
                                        class="mt-6 space-y-6">
                                        @csrf
                                        @method('patch')
                                        
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" 
                                                for="name">{{ __('Name') }}</label>
                                            <input class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full" 
                                                    id="name" name="name" type="text" value="{{ old('name', $user->name) }}" 
                                                    required="required" autofocus="autofocus" autocomplete="name">
                                            @if ($errors->get('name'))
                                                @foreach($errors->get('name') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" 
                                                for="username">{{ __('Username') }}</label>
                                            <input disabled=""
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                style="cursor:not-allowed;" id="username" name="username" type="text"
                                                value="{{ old('username', $user->username) }}" autofocus="autofocus" autocomplete="username">
                                            @if ($errors->get('username'))
                                                @foreach($errors->get('username') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="email">{{ __('Email') }}</label>
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                                required="required" autocomplete="email">
                                            @if ($errors->get('email'))
                                                @foreach($errors->get('email') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                                <div>
                                                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                                        {{ __('Your email address is unverified.') }}

                                                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                            {{ __('Click here to re-send the verification email.') }}
                                                        </button>
                                                    </p>

                                                    @if (session('status') === 'verification-link-sent')
                                                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                                            {{ __('A new verification link has been sent to your email address.') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif

                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="theme_id">{{ __('Theme') }}</label>
                                            <select id="theme_id" name="theme_id"
                                                class="mt-1 font-medium rounded-md text-gray-500 dark:text-gray-400  dark:bg-gray-900 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                            @if ($themes['count'] > 0) 
                                                @foreach ($themes['rows'] as $theme)
                                                    <option value="{{ $theme['id'] }}" @if ($head_data['user']['theme_id'] == $theme['id']) selected @endif>{{ $theme['name'] }}</option>
                                                @endforeach
                                            @else
                                                <option value="1" @if ($head_data['user']['theme_id'] < 1) selected @endif</option>))>Default</option>
                                            @endif
                                            </select>
                                            @if ($errors->get('theme_id'))
                                                @foreach($errors->get('theme_id') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                            <a style="margin-left: 15px" class="link align-middle" href="{{ url('theme-testing') }}" target="_blank">Theme testing</a>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <p class="block font-medium text-gray-700 dark:text-gray-300">Role:</p>
                                            </div>
                                            <div class="col">
                                                <p name="role" value="{{ $user->role_id }}">{{ $head_data['user']['role_data']['name'] }}</p>
                                            </div>

                                            <div class="col">
                                                <p class="block font-medium text-gray-700 dark:text-gray-300">Auth:</p>
                                            </div>
                                            <div class="col">
                                                <p name="auth" value="{{ $user->auth }}">{{ $user->auth }}</p>
                                            </div>

                                            <div class="col">
                                                <p class="block font-medium text-gray-700 dark:text-gray-300">Verified:</p>
                                            </div>
                                            <div class="col">
                                                <p name="theme" value="{{ $user->email_verified_at }}">{{ $user->email_verified_at ?? 'Never' }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                                style="color:black !important;">
                                                {{ __('Save') }}
                                            </button>
                                            @if (session('status') === 'profile-updated')
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                                            @endif
                                        </div>
                                    </form>
                                </section>
                            </div>
                        </div>

                        <div class="p-4 sm:p-8  dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Update Password') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                                        </p>
                                    </header>

                                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                        @csrf
                                        @method('put')
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="update_password_current_password">
                                                {{ __('Current Password') }}
                                            </label>
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                id="update_password_current_password" name="current_password"
                                                type="password" autocomplete="current-password">
                                            @if ($errors->updatePassword->get('current_password'))
                                                @foreach($errors->updatePassword->get('current_password') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="update_password_password">
                                                {{ __('New Password') }}
                                            </label>
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                id="update_password_password" name="password" type="password"
                                                autocomplete="new-password">
                                            @if ($errors->updatePassword->get('password'))
                                                @foreach($errors->updatePassword->get('password') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="update_password_password_confirmation">
                                                {{ __('Confirm Password') }}
                                            </label>
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
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
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                                            @endif
                                        </div>
                                    </form>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>