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
        <div class="min-h-screen-sub20">
            <!-- Page Heading -->
            <header class="theme-divBg shadow" style="padding-top:60px">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl leading-tight headerfix">
                        Profile
                    </h2>
                </div>
            </header>
            <!-- Page Content -->
            <main>
                <div class="py-12">
                    {!! $response_handling !!}
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium ">
                                            {{ __('Profile Information') }}
                                        </h2>

                                        <p class="mt-1 text-sm">
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
                                            <label class="block font-medium text-sm" 
                                                for="name">{{ __('Name') }}</label>
                                            <input class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full theme-input" 
                                                    id="name" name="name" type="text" value="{{ old('name', $user->name) }}" 
                                                    required="required" autofocus="autofocus" autocomplete="name">
                                            @if ($errors->get('name'))
                                                @foreach($errors->get('name') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm" 
                                                for="username">{{ __('Username') }}</label>
                                            <input disabled=""
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full  theme-input"
                                                style="cursor:not-allowed;" id="username" name="username" type="text"
                                                value="{{ old('username', $user->username) }}" autofocus="autofocus" autocomplete="username">
                                            @if ($errors->get('username'))
                                                @foreach($errors->get('username') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm"
                                                for="email">{{ __('Email') }}</label>
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full theme-input"
                                                id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                                required="required" autocomplete="email">
                                            @if ($errors->get('email'))
                                                @foreach($errors->get('email') as $error)
                                                <p class="red">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                                <div>
                                                    <p class="text-sm mt-2 ">
                                                        {{ __('Your email address is unverified.') }}

                                                        <button form="send-verification" class="underline text-sm hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
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
                                            <label class="block font-medium text-sm"
                                                for="theme_id">{{ __('Theme') }}</label>
                                            <select id="theme_id" name="theme_id"
                                                class="mt-1 font-medium rounded-md text-gray-500 dark:text-gray-400  dark:bg-gray-900 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none theme-dropdown">
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
                                                <p class="block font-medium">Role:</p>
                                            </div>
                                            <div class="col">
                                                <p name="role" value="{{ $user->role_id }}">{{ $head_data['user']['role_data']['name'] }}</p>
                                            </div>

                                            <div class="col">
                                                <p class="block font-medium">Auth:</p>
                                            </div>
                                            <div class="col">
                                                <p name="auth" value="{{ $user->auth }}">{{ $user->auth }}</p>
                                            </div>

                                            <div class="col">
                                                <p class="block font-medium">Verified:</p>
                                            </div>
                                            <div class="col">
                                                <p name="theme" value="{{ $user->email_verified_at }}">{{ $user->email_verified_at ?? 'Never' }}</p>
                                            </div>
                                            
                                        </div>

                                        <p class="gold link" onclick="modalLoadLoginHistory()" style="margin-top:20px">View login history</p>
                                        
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
                        </div>

                        <div class="p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium">
                                            {{ __('Update Password') }}
                                        </h2>

                                        <p class="mt-1 text-sm">
                                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
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
                        </div>
                        <div class="p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium">
                                            {{ __('Two-Factor Authentication') }}
                                        </h2>

                                        <p class="mt-1 text-sm">
                                            {{ __('Ensure your account is secure by enabling two-factor authentication.') }}
                                        </p>
                                    </header>

                                    <div class="row">
                                        <div class="col">
                                            <label class="block font-medium text-sm title" style="width:max-content"
                                                for="enable_2fa_checkbox"
                                                title="You will be required to input your 2FA code on login.">
                                                {{ __('Enable 2FA') }}
                                            </label>
                                            <form id="enable_2fa_form" action="{{ route('profile.enable2FA') }}"  method="POST" enctype="multipart/form-data" style="margin:0px; padding:0px">
                                                @csrf
                                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                                    <input type="checkbox" name="enable-2fa" id="enable_2fa_checkbox" @if ($head_data['config']['2fa_enforced'] == 1) checked disabled @elseif ($head_data['user']['2fa_enabled'] == 1) checked @endif >
                                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                                </label>
                                            </form>
                                        </div>
                                        <div class="col">
                                            <label class="block font-medium text-sm title" style="width:max-content"
                                                for="reset_2fa"
                                                title="You will be prompted to reset your 2FA on next login.">
                                                {{ __('Reset 2FA') }}
                                            </label>
                                            <button id="reset_2fa"
                                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 btn-danger"
                                                style="color:black !important;"
                                                onclick="modalLoadReset2FA({{ $head_data['user']['id'] }})">
                                                {{ __('Reset') }}
                                            </button>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div id="modalDivReset2FA" class="modal" style="display: none;">
            <span class="close" onclick="modalCloseReset2FA()">×</span>
            <div class="container well-nopad theme-divBg" style="padding:25px">
                <div style="margin:auto;text-align:center;margin-top:10px">
                    <form action="{{ route('profile.reset2FA') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="2fareset_submit" value="set" />
                        <input type="hidden" name="2fa_user_id" id="2fareset_user_id" value=""/>
                        <p>Are you sure you want to reset your 2FA?<br>
                        This will prompt a reset on your next login.</p>
                        <span>
                            <button class="btn btn-danger" type="submit" name="submit" value="1">Reset</button>
                            <button class="btn btn-warning" type="button" onclick="modalCloseReset2FA()">Cancel</button>
                        </span>
                    </form>
                </div>
            </div>
        </div>

        <div id="modalDivLoginHistory" class="modal">
            <span class="close" onclick="modalCloseLoginHistory()">&times;</span>
            <div class="container well-nopad theme-divBg" style="padding:25px">
                <h2 style="margin-left:20px">Login History</h2>
                <div class="well-nopad theme-divBg" style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-top:50px">
                    <table class="table table-dark theme-table centertable" style="max-width:max-content">
                        <thead class="text-center align-middle theme-tableOuter">
                            <th class="text-center align-middle">id</th>
                            <th class="text-center align-middle">type</th>
                            <th class="text-center align-middle">username</th>
                            <th class="text-center align-middle">user_id</th>
                            <th class="text-center align-middle">ipv4</th>
                            <th class="text-center align-middle">ipv6</th>
                            <th class="text-center align-middle">timestamp</th>
                            <th class="text-center align-middle">auth</th>
                        </thead>
                        <tbody>
                            @if (isset($login_history) && !empty($login_history))
                                @if (isset($log_colors) && !empty($log_colors))
                                    @foreach($login_history as $log)
                                        <tr class="text-center align-middle {{ $log_colors[$log['type']] }}">
                                            <td class="text-center align-middle">{{ $log['id'] }}</td>
                                            <td class="text-center align-middle">{{ $log['type'] }}</td>
                                            <td class="text-center align-middle">{{ $log['username'] }}</td>
                                            <td class="text-center align-middle">{{ $log['user_id'] }}</td>
                                            <td class="text-center align-middle">{{ long2ip($log['ipv4']) }}</td>
                                            <td class="text-center align-middle">{{ $log['ipv6'] }}</td>
                                            <td class="text-center align-middle">{{ $log['timestamp'] }}</td>
                                            <td class="text-center align-middle">{{ $log['auth'] }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                <tr class="text-center align-middle">
                                    <td colspan=100%>Error: Transaction colours missing...</td>
                                </tr>
                                @endif
                            @else
                            <tr class="text-center align-middle">
                                <td colspan=100%>No login history found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add the JS for the file -->
        <script src="{{ asset('js/profile.js') }}"></script>

        @include('foot')
    </body>
</html>