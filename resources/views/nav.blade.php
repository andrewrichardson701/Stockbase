<!-- Navigation Bar for the top of the page, using the config settings for logo and colour -->
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
<a href="{{ route('index') }}" class="nav-head" style="position:fixed;z-index:999;color:{{$head_data['extras']['banner_text_color']}} !important">{{$head_data['config_compare']['system_name']}}</a>
<header class="nav inv-nav" style="position:fixed;width:100%;z-index:900">
    <div id="nav-row" class="nav-row viewport-large">
        <div class="logo-div">
            <a href="{{ route('index') }}">
                <img class="logo" src="{{ asset('img/config/'. $head_data['config_compare']['logo_image']) }}" />
            </a>
        </div>
    @if (isset($head_data['user']['username'])) 
        <div id="add-div" class="nav-div" style="margin-right:5px">
            <button id="add-stock" class="btn btn-success cw nav-v-c btn-nav" @if ($nav_data['button_dimming'] == 1) style="opacity:60%" @else style="opacity:90%" @endif onclick="navPage('{{ url('stock') }}/0/add')">
                <i class="fa fa-plus"></i> Add 
            </button>
        </div> 
        <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
            <button id="remove-stock" class="btn btn-danger cw nav-v-c btn-nav" @if ($nav_data['button_dimming'] == 1) style="opacity:60%" @endif onclick="navPage('{{ url('stock') }}/0/remove')">
                <i class="fa fa-minus"></i> Remove 
            </button>
        </div>
        <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
            <button id="transfer-stock" class="btn btn-warning nav-v-c btn-nav"  @if ($nav_data['button_dimming'] == 1) style="color:black;opacity:60%" @else style="color:black" @endif onclick="navPage('{{ url('stock') }}/0/move')">
                <i class="fa fa-arrows-h"></i> Move 
            </button>
        </div>
    @endif

    @if (isset($head_data['impersonation']['active']) && $head_data['impersonation']['active'] == 1) 
        <div id="impersonate-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
            <form enctype="multipart/form-data" class="nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0px;padding:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                <input type="hidden" name="user-stop-impersonate" value="1"/>
                <button type="submit" id="impersonate" style="border-radius: 8px;padding-left:10px;padding-right:10px;margin-top:2.5%;height:80%;color:{{$head_data['extras']['invert_banner_text_color']}};background-color:{{$head_data['extras']['invert_banner_color']}} !important;margin-bottom:10%">Stop Impersonating</button>
            </form>
        </div> 
    @endif

    @if (isset($head_data['user']['id']))
        <div id="stock-div" class="nav-right nav-div">
            <a id="stock" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:{{$head_data['extras']['banner_text_color']}} !important; @if ($nav_data['highlight_num'] == 1) text-decoration: underline !important; @endif" href="{{ route('index') }}">Stock</a>
        </div> 
        <div id="cables-div" class="nav-div">
            <a id="cables" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:{{$head_data['extras']['banner_text_color']}} !important; @if ($nav_data['highlight_num'] == 2) text-decoration: underline !important; @endif" href="{{ route('cablestock') }}">Cables</a>
        </div> 
        <div id="assets-div" class="nav-div">
            <a id="assets" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:{{$head_data['extras']['banner_text_color']}} !important; @if ($nav_data['highlight_num'] == 3) text-decoration: underline !important; @endif" href="{{ route('assets') }}">Assets</a>
        </div> 
        <div id="stock-div" class="nav-div">
            <a id="stock" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:{{$head_data['extras']['banner_text_color']}} !important; @if ($nav_data['highlight_num'] == 4) text-decoration: underline !important; @endif" href="{{ route('containers') }}">Containers</a>
        </div>
        
        @if (isset($head_data['user']['name']))
        <div id="menu-div" class="nav-menu theme-burger nav-div" style="cursor:pointer; color:{{$head_data['extras']['banner_text_color']}} !important">
            <a id="profile" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:{{$head_data['extras']['banner_text_color']}} !important">{{ucfirst($head_data['user']['name'])}}<i class="fa fa-chevron-down" style="margin-left:5px; font-size:12px"></i></a>
        </div>
        <div style="width:100%">
            <div class="nav-div float-right nav-float" style="width:120px;">
                <ul class="nav-links align-middle" style="max-width:max-content; padding-left: 30px; padding-right:30px">
                     @if ($head_data['user']['permissions']['root'] == 1 || $head_data['user']['permissions']['admin'] == 1) 
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-wrench"></i></span><a class="clickable link" style="margin-left:5px" href="{{ route('admin') }}" @if ($nav_data['highlight_num'] == 5) style="text-decoration: underline !important" @endif>Admin</a></li>
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-list-ul"></i></span><a class="clickable link" style="margin-left:5px" href="{{ route('changelog') }}" @if ($nav_data['highlight_num'] == 8) style="text-decoration: underline !important" @endif>Changelog</a></li>
                        <li class="align-middle text-center divider" style="margin-top:5px;height: 6px;">&nbsp;</li>
                     @endif
                    <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-user"></i></span><a class="clickable link" style="margin-left:5px" href="{{ route('profile.edit') }}" @if ($nav_data['highlight_num'] == 7) style="text-decoration: underline !important" @endif>Profile</a></li>
                    <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-star"></i></span><a class="clickable link" style="margin-left:5px" href="{{ route('favourites') }}" @if ($nav_data['highlight_num'] == 6) style="text-decoration: underline !important" @endif>Favourites</a></li>
                    <li>
                        <span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-right-from-bracket"></i></span>
                        <form style="display:inline" method="POST" action="{{ route('logout') }}">
                            @csrf

                            <a class="clickable link" href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </li>
                </ul>
             </div>
         </div>
        @endif
    @endif
    </div>

    <div id="nav-row" class="nav-row viewport-small">
        <div class="logo-div">
            <a href="{{ route('index') }}">
                <img class="logo" src="{{ asset('img/config/'. $head_data['config_compare']['logo_image']) }}" />
            </a>
        </div>
        
        @if (isset($head_data['impersonation']['active']) && $head_data['impersonation']['active'] == 1) 
        <div id="impersonate-div" class="nav-div" style="margin-right:0px">
            <form enctype="multipart/form-data" class="nav-v-c nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0px;padding:0px">
                <!-- Include CSRF token in the form -->
                @csrf
                <input type="hidden" name="user-stop-impersonate" value="1"/>
                <button type="submit" style="border-radius: 8px; height:90%;color:{{$head_data['extras']['invert_banner_text_color']}};background-color:{{$head_data['extras']['invert_banner_color']}} !important; >Stop <i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
            </form>
        </div>
        @endif
        <div class="nav-div nav-right">
        @if (isset($head_data['user']['name']))
            <ul class="burger-links">
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-boxes-stacked"></i></span><a href="{{ route('index') }}" @if ($nav_data['highlight_num'] == 1) style="text-decoration: underline !important" @endif>Stock</a></li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-ethernet"></i></span><a href="{{ route('cablestock') }}" @if ($nav_data['highlight_num'] == 2) style="text-decoration: underline !important" @endif>Cables</a></li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-cubes-stacked"></i></span><a href="{{ route('assets') }}" @if ($nav_data['highlight_num'] == 3) style="text-decoration: underline !important" @endif>Assets</a></li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-box-open"></i></span><a href="{{ route('containers') }}" @if ($nav_data['highlight_num'] == 4) style="text-decoration: underline !important" @endif>Containers</a></li>
                <li class="align-middle text-center divider" style="margin-top:5px;height: 6px;">&nbsp</li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-user"></i></span><a href="{{ route('profile.edit') }}" @if ($nav_data['highlight_num'] == 7) style="text-decoration: underline !important" @endif>Profile</a></li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-star"></i></span><a href="{{ route('favourites') }}" @if ($nav_data['highlight_num'] == 6) style="text-decoration: underline !important" @endif>Favourites</a></li>
                <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-right-from-bracket"></i></span><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
            <div class="burger-menu nav-v-c theme-burger" style="color:{{$head_data['extras']['banner_text_color']}} !important"><i class="fa-solid fa-bars"></i></div>
        @endif
        </div>
    </div>
</header>

@if (isset($head_data['user']['id']))
<header class="nav inv-nav-secondary viewport-small" style="position: relative;top:60px">
    <table class="centertable">
        <tbody>
            <tr>
                <td>
                    <div style="margin-right:5vw">
                        <button id="add-stock" class="btn btn-success cw nav-v-b btn-nav scale_1-15" style="width:80px;opacity:90%" onclick="navPage('{{ url('stock') }}/0/add')">
                            <i class="fa fa-plus"></i> Add 
                        </button>
                    </div>
                </td>
                <td>
                    <div>
                        <button id="remove-stock" class="btn btn-danger cw btn-nav scale_1-15" style="width:80px;" onclick="navPage('{{ url('stock') }}/0/remove')">
                            <i class="fa fa-minus"></i> Remove 
                        </button>
                    </div>
                </td>
                <td>
                    <div style="margin-left:5vw">
                        <button id="transfer-stock" class="btn btn-warning btn-nav scale_1-15" style="width:80px;color:black" onclick="navPage('{{ url('stock') }}/0/move')">
                            <i class="fa fa-arrows-h"></i> Move 
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</header> 
@endif

<!-- Add the JS for the file -->
<script src="{{ asset('js/nav.js') }}"></script>
