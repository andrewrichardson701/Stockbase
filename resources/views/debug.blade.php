<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Debug</title>
</head>
<body>
    @include('nav')
    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl  leading-tight headerfix">
                    Debug
                </h2>
            </div>
        </header>
        <div class="container" style="margin-top:25px;">
            <div class="row">
                <div class="col">
                    <h3 style="font-size:22px">StockBase Debug ({{$head_data['version_number']}})</h3>
                    <div style="padding-top: 20px;">
                        <table class="table table-dark theme-table">
                            <tr>
                                <th>Key</th>
                                <th>Values</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>System Name</td>
                                <td>{{ $head_data['config_compare']['system_name'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Base URL</td>
                                <td>{{ $head_data['config']['base_url'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Version Number</td>
                                <td>{{ $head_data['version_number'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Update Available?</td>
                                <td>{{ $head_data['update_data']['update_available'] ? 'Yes' : 'No' }}</td>
                                <td>{{ $head_data['update_data']['latest_version'] }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SMTP Enabled?</td>
                                <td>{{ $head_data['config']['smtp_enabled'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Webhook Enabled?</td>
                                <td>{{ $head_data['config']['webhook_enabled'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2FA Enabled?</td>
                                <td>{{ $head_data['config']['two_factor_enabled'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2FA Enforced?</td>
                                <td>{{ $head_data['config']['two_factor_enforced'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Signup Allowed?</td>
                                <td>{{ $head_data['config']['signup_allowed'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>LDAP Enabled?</td>
                                <td>{{ $head_data['config']['ldap_enabled'] ? 'Yes' : 'No' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Default Theme</td>
                                <td>{{ $head_data['default_theme']['id'] }}</td>
                                <td>{{ $head_data['default_theme']['name'] }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Current Theme</td>
                                <td>{{ $head_data['user']['theme_data']['id'] }}</td>
                                <td>{{ $head_data['user']['theme_data']['name'] }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>{{ $head_data['user']['id'] }}</td>
                                <td>{{ $head_data['user']['name'] }}</td>
                                <td>{{ $head_data['user']['username'] }}</td>
                            </tr>
                            <tr>
                                <td>Impersonation</td>
                                <td>@if($head_data['impersonation']['active']) Active @else Inactive @endif</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nav Primary Colour</td>
                                <td style="background-color: {{ $head_data['config']['banner_color'] }}">{{ $head_data['config']['banner_color'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nav Secondary Colour</td>
                                <td style="background-color: {{ $head_data['extras']['nav_secondary_color'] }}">{{ $head_data['extras']['nav_secondary_color'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Favourites Button Hover Background Colour</td>
                                <td style="background-color: {{ $head_data['extras']['fav_btn_hover_bg'] }}">{{ $head_data['extras']['fav_btn_hover_bg'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Favourites Button Hover Text Colour</td>
                                <td style="background-color: {{ $head_data['extras']['fav_btn_hover_text'] }}">{{ $head_data['extras']['fav_btn_hover_text'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Inverted Banner Colour</td>
                                <td style="background-color: {{ $head_data['extras']['invert_banner_color'] }}">{{ $head_data['extras']['invert_banner_color'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Inverted Banner Text Colour</td>
                                <td style="background-color: {{ $head_data['extras']['invert_banner_text_color'] }}">{{ $head_data['extras']['invert_banner_text_color'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Default Banner Text Colour</td>
                                <td style="background-color: {{ $head_data['extras']['default_banner_text_color'] }}">{{ $head_data['extras']['default_banner_text_color'] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-1"></div>
                <div class="col-4 changelog" id="version-changelog" style="max-height:60vh;overflow-x: hidden;overflow-y: auto; ">
                    {!! file_get_contents(public_path('CHANGELOG.md')) !!}
                </div>
            </div>
        </div>
    </div>
        
@include('foot')

</body>
