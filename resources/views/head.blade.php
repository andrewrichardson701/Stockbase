<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// PAGE HEADER SETUP - SETS UP CSS, BOOTSTRAP AND OTHER STYLES AND SCRIPTS

// // anti clickjacking defense
// header("X-Frame-Options: DENY");
// // Set a cookie with the Secure flag for defense against cookie attacks
// setcookie("stockbase_cookie", bin2hex(random_bytes(32)), [ 'expires' => time() + 3600, 'path' => "/", 'domain' => $current_base_url, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' ]);
?>

<meta charset="utf-8">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- CSP headers -->
<meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src 'self' https://ajax.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline';
    style-src 'self' https://stackpath.bootstrapcdn.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com 'unsafe-inline';
    font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com;
    img-src 'self' https://api.qrserver.com data: blob:;
">


<link rel="icon" type="image/png" href="{{ asset('img/config/'. $head_data['config_compare']['favicon_image']) }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
<!-- <link rel="stylesheet" href="./assets/css/main.css">
<link rel="stylesheet" href="./assets/css/inv.css"> -->

<link rel="stylesheet" href="{{ asset('css/main.css') }}">
<link rel="stylesheet" href="{{ asset('css/inv.css') }}">


@if (isset($head_data['active_user']['theme_file_name']) && $head_data['active_user']['theme_file_name'] !== '') 
    <link id="theme-css" rel="stylesheet" name="user-theme" href="{{ asset('css/'. $head_data['active_user']['theme_file_name']) }}">
@elseif (isset($head_data['default_theme']['file_name']))
    <link id="theme-css" rel="stylesheet" name="default-theme" href="{{ asset('css/'. $head_data['default_theme']['file_name']) }}">
@else 
    <link id="theme-css" rel="stylesheet" name="fallback-theme" href="{{ asset('css/theme-dark.css') }}">
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">

<style>
.inv-nav {
    background-color: {{$head_data['config_compare']['banner_color']}} ;
    z-index:0px;
}
.inv-nav-secondary {
    background-color: {{$head_data['extras']['nav_secondary_color']}} ;
    z-index:0px;
}

.favouriteBtn {
    background-color: {{$head_data['config_compare']['banner_color']}} ;
    color: {{$head_data['extras']['banner_text_color']}} ;
}

.favouriteBtn:hover {
    background-color: {{$head_data['extras']['fav_btn_hover_bg']}} ;
    color: {{$head_data['extras']['fav_btn_hover_text']}} ;
}

</style>

<!-- Add the JS for the file -->
<script src="{{ asset('js/head.js') }}"></script>
