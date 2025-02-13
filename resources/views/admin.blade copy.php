<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')

    <title>{{ $head_data['config_compare']['system_name'] }} - Admin</title>
</head>
<body>
    <script>
        // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
        // if (!
        // <?php 
        // echo json_encode(in_array($_SESSION['role'], $head_data['config']_admin_roles_array)); 
        // ?>
        // ) {
        //     window.location.href = './login.php';
        // }
    </script>

    <!-- hidden link, commented out as no purpose currently -->
    <!-- <a href="changelog.php" class="skip-nav-link-inv">changelog</a> -->

    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="container" style="padding-top:60px;padding-bottom:20px">
        <h2 class="header-small">Admin</h2>
    </div>

    
    <div style="padding-bottom:75px">
        <!-- location modals -->
        <div class="container" style="padding-bottom:0px">
            <?php
            
            // if (!isset($_GET['section']) || (isset($_GET['section']) && $_GET['section'] == 'none')) {
            //     showResponse();
            // }
            ?>
            {!! $response_handling !!}
            
            <div id="modalDivAdd" class="modal">
                <span class="close" onclick="modalCloseAdd()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                        <div style="display:block"> 
                            <h2 style="margin-bottom:20px">Add new Site / Area / Shelf</h2>
                            <form id="locationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                @csrf
                                <input type="hidden" name="admin" value="1" />
                                <table class="centertable">
                                    <thead>
                                        <tr>
                                            <th style="padding-left:20px">Type</th>
                                            <th style="padding-left:5px" class="specialInput shelf area" hidden>Parent</th>
                                            <th style="padding-left:5px" class="specialInput shelf area site" hidden>Name</th>
                                            <th style="padding-left:5px" class="specialInput area site" hidden>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding-left:15px;padding-right:15px">
                                                <select id="addLocation-type" class="form-control" name="type" onchange="showInput()">
                                                    <option selected disabled>Select a Type</option>
                                                    <option value="site">Site</option>
                                                    <option value="area">Area</option>
                                                    <option value="shelf">Shelf</option>
                                                </select>
                                            </td>
                                            <td style="padding-right:15px" class="specialInput area shelf" hidden>
                                                <select id="addLocation-parent" class="form-control" name="parent" disabled>
                                                </select>
                                            </td>
                                            <td style="padding-right:15px" class="specialInput area shelf site" hidden><input class="form-control" type="text" name="name" placeholder="Name"/></td>
                                            <td style="padding-right:15px" class="specialInput area site" hidden><input class="form-control" type="text" name="description" placeholder="Description"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-submit" style="margin-left:10px" value="1">Submit</button></td>
                                        </tr>
                                    </tbody>
                                </table>        
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalDivEdit" class="modal">
                <span class="close" onclick="modalCloseEdit()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                        <div style="display:block"> 
                            <h2 style="margin-bottom:20px">Edit Location</h2>
                            <form id="editLocationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                @csrf
                                <table class="centertable">
                                    <tbody>
                                        <tr class="align-middle">
                                            <th style="padding-right:15px">Type:</th>
                                            <td>
                                                <input id="location-type-input" type="hidden" name="location-type" value="" />
                                                <label style="margin-bottom:0px" id="location-type-text"></label>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <th style="padding-top:15px; padding-right:10px; padding-bottom:10px ">ID:</th>
                                            <td>
                                                <input id="location-id-input" type="hidden" name="location-id" value="" />
                                                <label style="margin-bottom:0px" id="location-id-text"></label>
                                            </td>
                                        </tr>
                                        <tr id="location-parent-site-tr" class="align-middle">
                                            <th id="location-parent-site-th" style="padding-right:15px">Site:</th>
                                            <td>
                                                <select class="form-control" id="location-parent-site-input" name="location-parent-site"></select>
                                            </td>
                                        </tr>
                                        <tr id="location-parent-area-tr" class="align-middle">
                                            <th id="location-parent-area-th" style="padding-right:15px">Area:</th>
                                            <td>
                                                <select class="form-control" id="location-parent-area-input" name="location-parent-area"></select>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <th style="padding-right:15px">Name:</th>
                                            <td>
                                                <input type="text" class="form-control" id="location-name-input" name="location-name" value="" />
                                            </td>
                                        </tr>
                                        <tr id="location-description-tr" class="align-middle">
                                            <th style="padding-right:15px">Description:</th>
                                            <td>
                                                <input type="text" class="form-control" style="width:400px" id="location-description-input" name="location-description" value="" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-edit-submit" style="margin-left:10px;margin-top:20px" value="1">Save</button></td>
                                        </tr>
                                    </tbody>
                                </table>        
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- global -->
        @include('includes/admin/global')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="font-size:22px" id="global-settings" onclick="toggleSection(this, 'global')">Global Settings <i class="fa-solid fa-chevron-up fa-2xs" style="margin-left:10px"></i></h3>
            <!-- Global Settings -->
            <div style="padding-top: 20px" id="global" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'global-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <form id="globalForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    @csrf
                    <table id="globalTable">
                        <tbody>
                            <tr class="" id="ldap-headings">
                                <th style="width:250px;margin-left:25px;padding-bottom:20px"></th>
                                <th style="width: 250px;padding-bottom:20px">Change</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Current</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Default</th>
                            </tr>
                            <tr class="">
                                <td id="system_name-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="system_name">System Name:</p>
                                </td>
                                <td id="system_name-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="system_name" name="system_name">
                                </td>
                                <td style="min-width:230px;margin-left:10px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['system_name'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['system_name'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" id="banner-color" style="margin-top:20px">
                                <td id="banner-color-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="banner_color">Banner Colour:</p>
                                </td>
                                <td id="banner-color-picker" style="width:250px;padding-bottom:20px">
                                    <label class="tag-color">
                                        <input class="form-control input-color color" id="banner_color" name="banner_color" placeholder="#XXXXXX" data-value="#xxxxxx" value="{{ $head_data['config']['banner_color'] }}"/>
                                    </label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color: {{ $head_data['extras']['banner_text_color'] }} ;background-color: {{ $head_data['config']['banner_color'] }}">{{ $head_data['config']['banner_color'] }}</span></label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color: {{ $head_data['extras']['default_banner_text_color'] }} ;background-color:{{ $head_data['default_config']['banner_color'] }}">{{ $head_data['default_config']['banner_color'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="banner-logo">
                                <td id="banner-logo-label" style="width:250px;margin-left:25px;padding-bottom:20px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="logo_image">Banner Logo:</p>
                                </td>
                                <td id="banner-logo-file">
                                    <input class="" type="file" style="width: 250px;padding-bottom:20px" id="logo_image" name="logo_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['config']['logo_image'] }}" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['default_config']['logo_image'] }}" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="favicon-image">
                                <td id="favicon-image-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="favicon_image">Favicon Image:</p>
                                </td>
                                <td id="favicon-image-file" style="padding-bottom:20px">
                                    <input class="" type="file" style="width: 250px" id="favicon_image" name="favicon_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['config']['favicon_image'] }}" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['default_config']['favicon_image'] }}" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="currency-selector-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="currency_selection">Currency:</p>
                                </td>
                                <td id="currency-selector" style="width:250px;padding-bottom:20px">
                                    <select id="currency_selection" name="currency_selection" placeholder="£" class="form-control" style="width:150px">
                                        <option alt="Pounds Sterling" value="£" @if ($head_data['config']['currency'] == '£') selected @endif>£ (Pound)</option>
                                        <option alt="Dollar"          value="$" @if ($head_data['config']['currency'] == '$') selected @endif>$ (Dollar)</option>
                                        <option alt="Euro"            value="€" @if ($head_data['config']['currency'] == '€') selected @endif>€ (Euro)</option>
                                        <option alt="Yen"             value="¥" @if ($head_data['config']['currency'] == '¥') selected @endif>¥ (Yen)</option>
                                        <option alt="Franc"           value="₣" @if ($head_data['config']['currency'] == '₣') selected @endif>₣ (Franc)</option>
                                        <option alt="Rupee"           value="₹" @if ($head_data['config']['currency'] == '₹') selected @endif>₹ (Rupee)</option>
                                        <option alt="Mark"            value="₻" @if ($head_data['config']['currency'] == '₻') selected @endif>₻ (Mark)</option>
                                        <option alt="Ruouble"         value="₽" @if ($head_data['config']['currency'] == '₽') selected @endif>₽ (Ruouble)</option>
                                        <option alt="Lira"            value="₺" @if ($head_data['config']['currency'] == '₺') selected @endif>₺ (Lira)</option>
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['currency'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['currency'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="sku-prefix-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="sku_prefix"><or class="title" title="Prefix for SKU element on stock. e.g. ITEM-00001 or SKU-00001">SKU Prefix:</or></p>
                                </td>
                                <td id="sku-prefix-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="sku_prefix" name="sku_prefix">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['sku_prefix'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['sku_prefix'] }}</span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="base-url-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class="align-middle" for="base_url"><or class="title" title="This only changes the URL for any links or emails, not the web connection url. This needs to be changed in the web config file.">Base URL:</or></p>
                                </td>
                                <td id="base-url-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="base_url" name="base_url">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['base_url'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['base_url'] }}</span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="default-theme-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="default_theme">Default Theme:</p>
                                </td>
                                <td id="default-theme-set" style="width:250px;padding-bottom:20px">
                                    <select id="default_theme_selection" name="default_theme" placeholder="Dark" class="form-control" style="width:150px">
                                    @if ($themes['count'] > 0)
                                        @foreach ($themes['rows'] as $theme) 
                                        <option value="{{ $theme['id'] }}" @if ($head_data['config']['default_theme_id'] == $theme['id']) selected @endif title="{{ $theme['file_name'] }}">{{ $theme['name'] }}</option>
                                        @endforeach
                                    @else
                                        <option selected disabled>No themes found</option>
                                    @endif
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['default_theme_id'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['default_theme_id'] }}</span></label>
                                </td>
                            </tr>


                            <tr class="" style="margin-top:20px;margin-left:25px;padding-bottom:20px">
                                <td style="width:250px">
                                    <input id="global-submit" type="submit" name="global-submit" class="btn btn-success" value="Save" />
                                </td>
                                <td style="width:250px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                    <input id="global-restore-defaults" type="submit" name="global-restore-defaults" class="btn btn-danger" style="margin-left:15px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <!-- footer -->
        @include('includes/admin/footer')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="footer-settings" onclick="toggleSection(this, 'footer')">Footer <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Footer -->
            <div style="padding-top: 20px" id="footer" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'footer')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p id="footer-output" class="last-edit-T" hidden></p>
                    <table>
                        <tbody>
                            <tr>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Footer at the bottom of each page.">Enable Footer:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="normal-footer" onchange="toggleFooter(this, 1)" @if ($head_data['config']['footer_enable'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="left-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable GitLab link on the footer.">Enable GitLab Link:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="left-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="left-footer" onchange="toggleFooter(this, 2)" @if ($head_data['config']['footer_left_enable'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="right-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Road Map link on the footer.">Enable Road Map Link:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="right-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="right-footer" onchange="toggleFooter(this, 3)" @if ($head_data['config']['footer_right_enable'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- users -->
        @include('includes/admin/users')
        <div style="padding-bottom:0px">
            <div class="container">
                <h3 class="clickable" style="margin-top:50px;font-size:22px" id="users-settings" onclick="toggleSection(this, 'users')">Users <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            </div>
            <!-- Users Settings -->
            <div class="align-middle text-center" style="margin-left:5vw;margin-right:5vw; padding-top: 20px" id="users" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'users')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <table id="usersTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr id="users_table_info_tr" hidden>
                            <td colspan=8 id="users_table_info_td"></td>
                        </tr>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Auth</th>
                            <th>Enabled</th>
                            <th>Password</th>
                            <th>2FA</th>
                            @if ($head_data['user']['role_id'] == 0) <th></th> @endif
                        </tr>
                    </thead>
                    <tbody>
                    @if ($users['count'] < 1)
                        <tr><td colspan=9><or class="red">No Users in table: `users`.</or></td></tr>
                    @else
                        @foreach ($users['rows'] as $user)
                        <tr class="text-center" style="vertical-align: middle;">
                            <td id="user_{{ $user['id'] }}_id" style="vertical-align: middle;">{{ $user['id'] }}</td>
                            <td id="user_{{ $user['id'] }}_username" style="vertical-align: middle;">{{ $user['username'] }}</td>
                            <td id="user_{{ $user['id'] }}_first_name" style="vertical-align: middle;">{{ $user['first_name'] }}</td>
                            <td id="user_{{ $user['id'] }}_last_name" style="vertical-align: middle;">{{ $user['last_name'] }}</td>
                            <td id="user_{{ $user['id'] }}_email" style="vertical-align: middle;">{{ $user['email'] }}</td>
                            <td id="user_{{ $user['id'] }}_role" style="vertical-align: middle;">
                                <select class="form-control" id="user_{{ $user['id'] }}_role_select" style="min-width:max-content; padding-top:0px; padding-bottom:0px" onchange="userRoleChange('{{ $user['id'] }}')" @if ((int)$user['id'] == 0) disabled @endif >
                                @foreach ($user_roles['rows'] as $role)
                                    <option value="{{ $role['id'] }}" title="{{ $role['description'] }}" @if ($user['role_id'] == $role['id']) selected @endif @if ((int)$role['id'] == 0) disabled @endif>{{ ucwords($role['name']) }}</option>
                                @endforeach
                                </select>
                            </td>
                            <td id="user_{{ $user['id'] }}_auth" style="vertical-align: middle;">{{ $user['auth'] }}</td>
                            <td style="vertical-align: middle;"><input type="checkbox" id="user_{{ $user['id'] }}_enabled_checkbox" @if ($user['enabled'] == 1) checked @endif onchange="usersEnabledChange('{{ $user['id'] }}')"></td>
                            <td style="vertical-align: middle;">
                                <button class="btn btn-warning" style="padding: 2px 6px 2px 6px" id="user_{{ $user['id'] }}_pwreset" onclick="resetPassword('{{ $user['id'] }}')" @if ($user['auth'] == 'ldap' || (int)$user['role_id'] == 0 || (int)$user['role_id'] == 2) disabled @endif >Reset</button>
                            </td>
                            <td style="vertical-align: middle;">
                            @if ($user['id'] !== 0)
                                <button class="btn btn-primary" id="reset_2fa" style="padding: 2px 6px 2px 6px" onclick="modalLoadReset2FA({{ $user['id'] }})">Reset 2FA</button>
                            @endif
                            </td>
                            @if ((int)$head_data['user']['role_id'] == 0)
                            <td style="vertical-align: middle;">   
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" style="padding:0px;margin:0px">
                                    @csrf
                                    <button type="submit" style="padding:2px 8px 2px 8px" class="btn btn-info" id="user_{{ $user['id'] }}_impersonate" title="Impersonate" @if ((int)$user['id'] == (int)$head_data['user']['id']) disabled @endif ><i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
                                    <input type="hidden" name="user-impersonate" value="impersonate"/>
                                    <input type="hidden" name="role" value="Root" />
                                    <input type="hidden" name="user-id" value="{{ $user['id'] }}" />
                                </form>
                            </td>
                            @endif
                        @endforeach
                    @endif
                        <tr class="theme-tableOuter"><td></td><td colspan="100%"><button class="btn btn-success" type="button" onclick="navPage('addlocaluser');"><i class="fa fa-plus"></i> Add</button></td></tr>
                    </tbody>
                </table>
            </div>
            <div id="modalDivReset2FA" class="modal" style="display: none;">
                <span class="close" onclick="modalCloseReset2FA()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div style="margin:auto;text-align:center;margin-top:10px">
                        <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="2fareset_submit" value="admin" />
                            <input type="hidden" name="2fa_user_id" id="2fareset_user_id" value=""/>
                            <p>Are you sure you want to reset the 2FA for <or class="green" id="2fareset_username"></or>?<br>
                            This will prompt a reset on the user's next login.</p>
                            <span>
                                <button class="btn btn-danger" type="submit" name="submit" value="1">Reset</button>
                                <button class="btn btn-warning" type="button" onclick="modalCloseReset2FA()">Cancel</button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- user-roles -->
        @include('includes/admin/user-roles')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="usersroles-settings" onclick="toggleSection(this, 'usersroles')">User Roles <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Users Roles -->
            <div style="padding-top: 20px" id="usersroles" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'users-roles')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <table id="usersTable" class="table table-dark theme-table" style="max-width:max-content">
                    <thead>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="title" title="Can access the Optics page">Optics</th>
                            <th>Administrator</th>
                            <th>Root</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($user_roles['count'] > 0)
                        @foreach ($user_roles['rows'] as $role)
                        <tr class="text-center">
                            <td>{{ $role['id'] }}</td>
                            <td>{{ $role['name'] }}</td>
                            <td>{{ $role['description'] }}</td>
                            <td style="vertical-align: middle;">@if ($role['is_optic'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                            <td style="vertical-align: middle;">@if ($role['is_admin'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                            <td style="vertical-align: middle;">@if ($role['is_root'] == 1)<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>@else<i class="fa-solid fa-xmark" style="color: #ff0000;"></i> @endif</td>
                        </tr>
                        @endforeach
                    @else 
                        <tr><td colspan=6>No roles found.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    
        <!-- authentication -->
        @include('includes/admin/authentication')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="authentication-settings" onclick="toggleSection(this, 'authentication')">Authentication <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Authentication -->
            <div style="padding-top: 20px" id="authentication" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'authentication')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <p id="authentication-output" class="last-edit-T" hidden></p>
                <table>
                    <tbody>
                        <tr>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable the signup.php self sign up page for locally authenticated users">Self sign up allowed:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="signup_allowed_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="signup_allowed" onchange="authSettings(this, 'signup_allowed')" @if ((int)$head_data['config']['signup_allowed'] == 1) checked @endif>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle">Enable 2FA:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enable_2fa_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="enable_2fa" onchange="authSettings(this, '2fa_enabled')" @if ((int)$head_data['config']['2fa_enabled'] == 1) checked @endif>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enforce the use of 2FA for ALL users (except Root)">Enforce 2FA:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enforce_2fa_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="enforce_2fa" onchange="authSettings(this, '2fa_enforced')" @if ((int)$head_data['config']['2fa_enforced'] == 1) checked @endif>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- session management -->
        @include('includes/admin/session-management')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="sessionmanagement-settings" onclick="toggleSection(this, 'sessionmanagement')">Session Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Session Management -->
            <div style="padding-top: 20px" id="sessionmanagement" hidden>
            <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'sessionmanagement')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <table id="sessionsTable" class="table table-dark theme-table" style="max-width:max-content">
                    <thead>
                        <tr id="sessions_table_info_tr" hidden>
                            <td colspan=8 id="sessions_table_info_td"></td>
                        </tr>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>IP</th>
                            <th>Login Time</th>
                            <th hidden>Logout Time</th>
                            <th>Last Activity</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($active_sessions['count'] > 0) 
                        @foreach ($active_sessions as $session)
                        <tr class="text-center" style="vertical-align: middle;">
                            <form action="includes/admin.inc.php" method="POST">
                                <@csrf
                                <input type="hidden" name="session_id" value="{{ $session['sl_id'] }}" />
                                <td id="sessions_{{ $session['sl_id'] }}_id" style="vertical-align: middle;">{{ $session['sl_id'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_user_id" style="vertical-align: middle;">{{ $session['sl_user_id'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_username" style="vertical-align: middle;">{{ $session['u_username'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_ip" style="vertical-align: middle;">{{ $session['sl_ip'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_login_time" style="vertical-align: middle;">{{ $session['sl_login_time'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_logout_time" style="vertical-align: middle;" hidden>{{ $session['logout_time'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_last_activity" style="vertical-align: middle;">{{ $session['sl_last_activity'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_browser" style="vertical-align: middle;">{{ $session['sl_browser'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_os" style="vertical-align: middle;">{{ $session['sl_os'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_status" style="vertical-align: middle;">{{ $session['sl_status'] }}</td>
                                <td id="sessions_{{ $session['sl_id'] }}_kill" style="vertical-align: middle;"><input type="submit" class="btn btn-danger" name="session-kill-submit" value="Kill" @if ((int)$head_data['session']['id'] == (int)$session['sl_id']) title="Current Session" disabled @endif></td>
                            </form>
                        </tr>
                        @endforeach  
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- image management -->
        @include('includes/admin/image-management')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="imagemanagement-settings" onclick="toggleSection(this, 'imagemanagement')">Image Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Image Management Settings -->
            <div style="padding-top: 20px" id="imagemanagement" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'imagemanagement')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p>Image Count: <or class="green">{{ $image_management_count }}</or></p>
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter" style="width:130px; position: sticky; top: -1;">Image</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">File</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="image-management-tbody">
                            <tr id="loader-tr">
                                <td id="loader-td" colspan="100%" class="algin-middle text-center">
                                    <div id="loader-outerdiv">
                                        <button class="btn btn-info" id="show-images" onclick="loadAdminImages(0, 1)">Load Images</button>
                                        <div class="loader" id="loaderDiv" style="margin-top:10px;width:130px;display:none">
                                            <div class="loaderBar"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- attribute management -->
        @include('includes/admin/attribute-management')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="attributemanagement-settings" onclick="toggleSection(this, 'attributemanagement')">Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Attribute Management Settings -->
            <div style="padding-top: 20px" id="attributemanagement" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Tags<a class="align-middle link" style="margin-left:30px;font-size:12px" href="tags.php">View all</a></h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement-tag')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                ?>
                {!! $response_handling !!}
            
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-tag" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('tag', 1)" @if (isset($tags['deleted_count']) && $tags['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-tag" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('tag', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($tags['count'] > 0)
                            @foreach ($tags['rows'] as $tag)
                                @if ($tag['deleted'] == 1)
                                <tr id="tag-row-{{ $tag['id'] }}" class="align-middle red theme-divBg tag-deleted" hidden>
                                @else 
                                <tr id="tag-row-{{ $tag['id'] }}" class="align-middle">
                                @endif
                            
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="attribute-type" value="tag"/>
                                    <input type="hidden" name="id" value="{{ $tag['id'] }}">
                                    <td id="tag-{{ $tag['id'] }}-id" class="text-center align-middle">{{ $tag['id'] }}</td>
                                    <td id="tag-{{ $tag['id'] }}-name" class="text-center align-middle">{{ $tag['name'] }}</td>
                                    <td class="text-center align-middle">{{ $tag_links[$tag['id']]['count'] ?? 0 }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int) $tag['deleted'] === 0)
                                        <button class="btn btn-danger" type="submit" name="attributemanagement-submit"
                                            @if (($tag_links[$tag['id']]['count'] ?? 0) !== 0)
                                                disabled title="Tag still linked to stock. Remove these links before deleting."
                                            @endif
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success" type="submit" name="attributemanagement-restore">
                                            <i class="fa fa-trash-restore"></i>
                                        </button>
                                    @endif
                                    <td class="text-center align-middle" colspan="2">
                                        @if ((int)$tag['deleted'] !== 1) 
                                            @if (array_key_exists($tag['id'], $tag_links) && ((int)$tag_links[$tag['id']]['count'] ?? 0) !== 0)
                                                <button class="btn btn-warning" id="tag-{{ $tag['id'] }}-links" type="button" onclick="showLinks('tag', '{{ $tag['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore tag?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($tag['id'], $tag_links) && ((int)$tag_links[$tag['id']]['count'] ?? 0) !== 0)
                                <tr id="tag-row-{{ $tag['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>ID</th>
                                                        <th>Stock ID</th>
                                                        <th>Stock Name</th>
                                                        <th>Tag ID</th>
                                                        <th>Tag Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($tag_links[$tag['id']]['rows'] as $link) 
                                                    <tr class="clickable" onclick="navPage('stock?stock_id={{ $link['stock_id'] }}')">
                                                        <td class="text-center">{{ $link['id'] }}</td>
                                                        <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $link['stock_id'] }}</a></td>
                                                        <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $stock['rows'][$link['stock_id']]['name'] }}</a></td>
                                                        <td class="text-center">{{ $link['tag_id'] }}</td>
                                                        <td class="text-center">{{ $tags['rows'][$link['tag_id']]['name'] }}</td>

                                                    </tr>
                                                @endforeach                                          
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No tags found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-manufacturer" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 1)" @if (isset($manufacturers['deleted_count']) && $manufacturers['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-manufacturer" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($manufacturers['count'] > 0)
                            @foreach ($manufacturers['rows'] as $manufacturer)
                                @if ($manufacturer['deleted'] == 1)
                                <tr id="manufacturer-row-{{ $manufacturer['id'] }}" class="align-middle red theme-divBg manufacturer-deleted" hidden>
                                @else 
                                <tr id="manufacturer-row-{{ $manufacturer['id'] }}" class="align-middle">
                                @endif
                            
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="attribute-type" value="manufacturer"/>
                                    <input type="hidden" name="id" value="{{ $manufacturer['id'] }}">
                                    <td id="manufacturer-{{ $manufacturer['id'] }}-id" class="text-center align-middle">{{ $manufacturer['id'] }}</td>
                                    <td id="manufacturer-{{ $manufacturer['id'] }}-name" class="text-center align-middle">{{ $manufacturer['name'] }}</td>
                                    <td class="text-center align-middle">{{ $manufacturer_links[$manufacturer['id']]['count'] ?? 0 }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int) $manufacturer['deleted'] === 0)
                                        <button class="btn btn-danger" type="submit" name="attributemanagement-submit"
                                            @if (($manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                                                disabled title="Tag still linked to stock. Remove these links before deleting."
                                            @endif
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success" type="submit" name="attributemanagement-restore">
                                            <i class="fa fa-trash-restore"></i>
                                        </button>
                                    @endif
                                    <td class="text-center align-middle" colspan="2">
                                        @if ((int)$manufacturer['deleted'] !== 1) 
                                            @if (array_key_exists($manufacturer['id'], $manufacturer_links) && ((int)$manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                                                <button class="btn btn-warning" id="manufacturer-{{ $manufacturer['id'] }}-links" type="button" onclick="showLinks('manufacturer', '{{ $manufacturer['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore manufacturer?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($manufacturer['id'], $manufacturer_links) && ((int)$manufacturer_links[$manufacturer['id']]['count'] ?? 0) !== 0)
                                <tr id="manufacturer-row-{{ $manufacturer['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>ID</th>
                                                        <th>Stock ID</th>
                                                        <th>Stock Name</th>
                                                        <th>Manufacturer ID</th>
                                                        <th>Manufacturer Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($manufacturer_links[$manufacturer['id']]['rows'] as $link) 
                                                    <tr class="clickable" onclick="navPage('stock?stock_id={{ $link['stock_id'] }}')">
                                                        <td class="text-center">{{ $link['id'] }}</td>
                                                        <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $link['stock_id'] }}</a></td>
                                                        <td class="text-center"><a href="stock?stock_id={{ $link['stock_id'] }}">{{ $stock['rows'][$link['stock_id']]['name'] }}</a></td>
                                                        <td class="text-center">{{ $link['manufacturer_id'] }}</td>
                                                        <td class="text-center">{{ $manufacturers['rows'][$link['manufacturer_id']]['name'] }}</td>

                                                    </tr>
                                                @endforeach                                          
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No manufacturers found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                
                        
            </div>
        </div>
        
        <!-- optic attribute management -->
        @include('includes/admin/optic-attribute-management')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="opticattributemanagement-settings" onclick="toggleSection(this, 'opticattributemanagement')">Optic Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Optic Attribute Management Settings -->
            <div style="padding-top: 20px" id="opticattributemanagement" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Vendors</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_vendors')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                ?>
                {!! $response_handling !!}

                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-optic_vendor" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_vendor', 1)" @if (isset($optic_vendor['deleted_count']) && $optic_vendor['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-optic_vendor" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_vendor', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($optic_vendors['count'] > 0)
                            @foreach ($optic_vendors['rows'] as $optic_vendor)

                                @if ($optic_vendor['deleted'] == 1)
                                <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}" class="align-middle red theme-divBg optic_vendor-deleted" hidden>
                                @else 
                                <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}" class="align-middle">
                                @endif
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="attribute-type" value="optic_vendor"/>
                                    <input type="hidden" name="id" value="{{ $optic_vendor['id'] }}">
                                    <td id="optic_vendor-{{ $optic_vendor['id'] }}-id" class="text-center align-middle">{{ $optic_vendor['id'] }}</td>
                                    <td id="optic_vendor-{{ $optic_vendor['id'] }}-name" class="text-center align-middle">{{ $optic_vendor['name'] }}</td>
                                    <td class="text-center align-middle">{{ (int)($optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int)$optic_vendor['deleted'] === 0)
                                        <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                        @if (($optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0) 
                                            disabled title="optic_vendor still linked to stock. Remove these links before deleting."
                                        @endif
                                        ><i class="fa fa-trash"></i></button></td>
                                    @else 
                                        <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                    @endif
                                    <td class="text-center align-middle">
                                        @if ((int)$optic_vendor['deleted'] !== 1) 
                                            @if (array_key_exists($optic_vendor['id'], $optic_vendor_links) && ((int)$optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0) 
                                                <button class="btn btn-warning" id="optic_vendor-{{ $optic_vendor['id'] }}-links" type="button" onclick="showLinks('optic_vendor', '{{ $optic_vendor['id'] }}')">Show Links</button>
                                            @else 
                                                <or class="green">Restore?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>optic_vendor_links
                            </tr>
                                @if (array_key_exists($optic_vendor['id'], $optic_vendor_links) && ((int)$optic_vendor_links[$optic_vendor['id']]['count'] ?? 0) !== 0)
                                <tr id="optic_vendor-row-{{ $optic_vendor['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>Optic ID</th>
                                                        <th>Optic Model</th>
                                                        <th>Optic Serial</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($optic_vendor_links[$optic_vendor['id']]['rows'] as $link)
                                                        <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                            <td class="text-center">{{ $link['id'] }}</td>
                                                            <td class="text-center">{{ $link['model'] }}</td>
                                                            <td class="text-center">{{ $link['serial_number'] }}</td>

                                                        </tr>
                                                @endforeach                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No vendors found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div> 

                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Types</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_types')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }

                ?>
                {!! $response_handling !!}

                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-optic_type" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_type', 1)" @if (isset($optic_type['deleted_count']) && $optic_type['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-optic_type" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_type', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($optic_types['count'] > 0)
                            @foreach ($optic_types['rows'] as $optic_type)

                                @if ($optic_type['deleted'] == 1)
                                <tr id="optic_type-row-{{ $optic_type['id'] }}" class="align-middle red theme-divBg optic_type-deleted" hidden>
                                @else 
                                <tr id="optic_type-row-{{ $optic_type['id'] }}" class="align-middle">
                                @endif
                                <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="attribute-type" value="optic_type"/>
                                    <input type="hidden" name="id" value="{{ $optic_type['id'] }}">
                                    <td id="optic_type-{{ $optic_type['id'] }}-id" class="text-center align-middle">{{ $optic_type['id'] }}</td>
                                    <td id="optic_type-{{ $optic_type['id'] }}-name" class="text-center align-middle">{{ $optic_type['name'] }}</td>
                                    <td class="text-center align-middle">{{ (int)($optic_type_links[$optic_type['id']]['count'] ?? 0) }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int)$optic_type['deleted'] == 0)
                                        <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                        @if (($optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0) 
                                            disabled title="optic_type still linked to stock. Remove these links before deleting."
                                        @endif
                                        ><i class="fa fa-trash"></i></button></td>
                                    @else 
                                        <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                    @endif
                                    <td class="text-center align-middle">
                                        @if ((int)$optic_type['deleted'] !== 1) 
                                            @if (array_key_exists($optic_type['id'], $optic_type_links) && ((int)$optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0) 
                                                <button class="btn btn-warning" id="optic_type-{{ $optic_type['id'] }}-links" type="button" onclick="showLinks('optic_type', '{{ $optic_type['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($optic_type['id'], $optic_type_links) && ((int)$optic_type_links[$optic_type['id']]['count'] ?? 0) !== 0)
                                <tr id="optic_type-row-{{ $optic_type['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>Optic ID</th>
                                                        <th>Optic Model</th>
                                                        <th>Optic Serial</th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                                @foreach ($optic_type_links[$optic_type['id']]['rows'] as $link)
                                                        <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                            <td class="text-center">{{ $link['id'] }}</td>
                                                            <td class="text-center">{{ $link['model'] }}</td>
                                                            <td class="text-center">{{ $link['serial_number'] }}</td>

                                                        </tr>
                                                @endforeach                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No types found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div> 

                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Speeds</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_speeds')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                ?>
                {!! $response_handling !!}

                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-optic_speed" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_speed', 1)" @if (isset($optic_speed['deleted_count']) && $optic_speed['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-optic_speed" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_speed', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($optic_speeds['count'] > 0)
                            @foreach ($optic_speeds['rows'] as $optic_speed)

                                @if ($optic_speed['deleted'] == 1)
                                <tr id="optic_speed-row-{{ $optic_speed['id'] }}" class="align-middle red theme-divBg optic_speed-deleted" hidden>
                                @else 
                                <tr id="optic_speed-row-{{ $optic_speed['id'] }}" class="align-middle">
                                @endif
                                <form encspeed="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="attribute-speed" value="optic_speed"/>
                                    <input type="hidden" name="id" value="{{ $optic_speed['id'] }}">
                                    <td id="optic_speed-{{ $optic_speed['id'] }}-id" class="text-center align-middle">{{ $optic_speed['id'] }}</td>
                                    <td id="optic_speed-{{ $optic_speed['id'] }}-name" class="text-center align-middle">{{ $optic_speed['name'] }}</td>
                                    <td class="text-center align-middle">{{ (int)($optic_speed_links[$optic_speed['id']]['count'] ?? 0) }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int)$optic_speed['deleted'] == 0)
                                        <button class="btn btn-danger" type="submit" name="attributemanagement-submit" 
                                        @if (($optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0) 
                                            disabled title="optic_speed still linked to stock. Remove these links before deleting."
                                        @endif
                                        ><i class="fa fa-trash"></i></button></td>
                                    @else 
                                        <button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                    @endif
                                    <td class="text-center align-middle">
                                        @if ((int)$optic_speed['deleted'] !== 1) 
                                            @if (array_key_exists($optic_speed['id'], $optic_speed_links) && ((int)$optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0) 
                                                <button class="btn btn-warning" id="optic_speed-{{ $optic_speed['id'] }}-links" type="button onclick="showLinks('optic_speed', '{{ $optic_speed['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($optic_speed['id'], $optic_speed_links) && ((int)$optic_speed_links[$optic_speed['id']]['count'] ?? 0) !== 0)
                                <tr id="optic_speed-row-{{ $optic_speed['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>Optic ID</th>
                                                        <th>Optic Model</th>
                                                        <th>Optic Serial</th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                                @foreach ($optic_speed_links[$optic_speed['id']]['rows'] as $link)
                                                        <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                            <td class="text-center">{{ $link['id'] }}</td>
                                                            <td class="text-center">{{ $link['model'] }}</td>
                                                            <td class="text-center">{{ $link['serial_number'] }}</td>
                                                        </tr>
                                                @endforeach                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No speeds found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                
                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Connectors</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_connectors')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                ?>
                {!! $response_handling !!}
                
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-optic_connector" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_connector', 1)" @if (isset($optic_connector['deleted_count']) && $optic_connector['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-optic_connector" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_connector', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($optic_connectors['count'] > 0)
                            @foreach ($optic_connectors['rows'] as $optic_connector)

                                @if ($optic_connector['deleted'] == 1)
                                <tr id="optic_connector-row-{{ $optic_connector['id'] }}" class="align-middle red theme-divBg optic_connector-deleted" hidden>
                                @else 
                                <tr id="optic_connector-row-{{ $optic_connector['id'] }}" class="align-middle">
                                @endif
                                <form encconnector="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input connector="hidden" name="attribute-connector" value="optic_connector"/>
                                    <input connector="hidden" name="id" value="{{ $optic_connector['id'] }}">
                                    <td id="optic_connector-{{ $optic_connector['id'] }}-id" class="text-center align-middle">{{ $optic_connector['id'] }}</td>
                                    <td id="optic_connector-{{ $optic_connector['id'] }}-name" class="text-center align-middle">{{ $optic_connector['name'] }}</td>
                                    <td class="text-center align-middle">{{ (int)($optic_connector_links[$optic_connector['id']]['count'] ?? 0) }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int)$optic_connector['deleted'] == 0)
                                        <button class="btn btn-danger" connector="submit" name="attributemanagement-submit" 
                                        @if (($optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0) 
                                            disabled title="optic_connector still linked to stock. Remove these links before deleting."
                                        @endif
                                        ><i class="fa fa-trash"></i></button></td>
                                    @else 
                                        <button class="btn btn-success" connector="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                    @endif
                                    <td class="text-center align-middle">
                                        @if ((int)$optic_connector['deleted'] !== 1) 
                                            @if (array_key_exists($optic_connector['id'], $optic_connector_links) && ((int)$optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0) 
                                                <button class="btn btn-warning" id="optic_connector-{{ $optic_connector['id'] }}-links" connector="button" onclick="showLinks('optic_connector', '{{ $optic_connector['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($optic_connector['id'], $optic_connector_links) && ((int)$optic_connector_links[$optic_connector['id']]['count'] ?? 0) !== 0)
                                <tr id="optic_connector-row-{{ $optic_connector['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>Optic ID</th>
                                                        <th>Optic Model</th>
                                                        <th>Optic Serial</th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                                @foreach ($optic_connector_links[$optic_connector['id']]['rows'] as $link)
                                                        <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                            <td class="text-center">{{ $link['id'] }}</td>
                                                            <td class="text-center">{{ $link['model'] }}</td>
                                                            <td class="text-center">{{ $link['serial_number'] }}</td>
                                                        </tr>
                                                @endforeach                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No connectors found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                
                
                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Distances</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_distances')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                ?>
                {!! $response_handling !!}
                
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                    <button id="show-deleted-optic_distance" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_distance', 1)" @if (isset($optic_distance['deleted_count']) && $optic_distance['deleted_count'] == 0) hidden @endif>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            
                                        </span>
                                    </button>
                                    <button id="hide-deleted-optic_distance" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_distance', 0)" hidden>
                                        <span class="zeroStockFont">
                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                        </span>
                                    
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($optic_distances['count'] > 0)
                            @foreach ($optic_distances['rows'] as $optic_distance)

                                @if ($optic_distance['deleted'] == 1)
                                <tr id="optic_distance-row-{{ $optic_distance['id'] }}" class="align-middle red theme-divBg optic_distance-deleted" hidden>
                                @else 
                                <tr id="optic_distance-row-{{ $optic_distance['id'] }}" class="align-middle">
                                @endif
                                <form encdistance="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input distance="hidden" name="attribute-distance" value="optic_distance"/>
                                    <input distance="hidden" name="id" value="{{ $optic_distance['id'] }}">
                                    <td id="optic_distance-{{ $optic_distance['id'] }}-id" class="text-center align-middle">{{ $optic_distance['id'] }}</td>
                                    <td id="optic_distance-{{ $optic_distance['id'] }}-name" class="text-center align-middle">{{ $optic_distance['name'] }}</td>
                                    <td class="text-center align-middle">{{ (int)($optic_distance_links[$optic_distance['id']]['count'] ?? 0) }}</td>
                                    <td class="text-center align-middle">
                                    @if ((int)$optic_distance['deleted'] == 0)
                                        <button class="btn btn-danger" distance="submit" name="attributemanagement-submit" 
                                        @if (($optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0) 
                                            disabled title="optic_distance still linked to stock. Remove these links before deleting."
                                        @endif
                                        ><i class="fa fa-trash"></i></button></td>
                                    @else 
                                        <button class="btn btn-success" distance="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                    @endif
                                    <td class="text-center align-middle">
                                        @if ((int)$optic_distance['deleted'] !== 1) 
                                            @if (array_key_exists($optic_distance['id'], $optic_distance_links) && ((int)$optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0) 
                                                <button class="btn btn-warning" id="optic_distance-{{ $optic_distance['id'] }}-links" distance="button" onclick="showLinks('optic_distance', '{{ $optic_distance['id'] }}')">Show Links</button> 
                                            @else 
                                                <or class="green">Restore?</or>
                                            @endif
                                        @endif
                                    </td>
                                </form>
                            </tr>
                                @if (array_key_exists($optic_distance['id'], $optic_distance_links) && ((int)$optic_distance_links[$optic_distance['id']]['count'] ?? 0) !== 0)
                                <tr id="optic_distance-row-{{ $optic_distance['id'] }}-links" class="align-middle" hidden>
                                    <td colspan="100%">
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>Optic ID</th>
                                                        <th>Optic Model</th>
                                                        <th>Optic Serial</th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                                @foreach ($optic_distance_links[$optic_distance['id']]['rows'] as $link)
                                                        <tr class="clickable" onclick=navPage("optics?search={{ $link['serial_number'] }}")>
                                                            <td class="text-center">{{ $link['id'] }}</td>
                                                            <td class="text-center">{{ $link['model'] }}</td>
                                                            <td class="text-center">{{ $link['serial_number'] }}</td>
                                                        </tr>
                                                @endforeach                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr class="align-middle"><td colspan="100%">No distances found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>

        <!-- stock management -->
        @include('includes/admin/stock-management')
        <div class="container" style="padding-bottom:0px">       
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stockmanagement-settings" onclick="toggleSection(this, 'stockmanagement')">Stock Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Stock Management Settings -->
            <div style="padding-top: 20px" id="stockmanagement" hidden>
                <h4 style="margin-left:10px; margin-right:10px; margin-top:5px; font-size:20px; margin-bottom:10px">Cost Enablement</h4>
                {!! $response_handling !!}
                
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p id="cost-output" class="last-edit-T" hidden></p>
                    <table>
                        <tbody>
                            <tr>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-cost">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for normal stock items.">Normal Stock Cost:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-cost-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="normal-cost" onchange="toggleCost(this, 1)" @if ($head_data['config']['cost_enable_normal'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="cable-cost">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for cable stock items.">Cable Stock Cost:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="cable-cost-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="cable-cost" onchange="toggleCost(this, 2)"" @if ($head_data['config']['cost_enable_cable'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4 style="margin-left:10px; margin-right:10px; margin-top:20px; font-size:20px; margin-bottom:10px">Deleted Stock</h4>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'stockmanagement')) {
                //     echo('<div style="margin-right: 10px; margin-left: 10px">');
                //     showResponse();
                //     echo('</div>');
                // }
                // cost/price toggles for both normal stock and cable stock
                ?>
                {!! $response_handling !!}

                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p class="margin-left:10px">Count: <or class="green">@if (!empty($deleted_stock['count'])) {{ $deleted_stock['count'] }} @else 0 @endif</or></p>
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">SKU</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Description</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Cable?</th>
                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Restore</th>
                            </tr>
                        </thead>
                        <tbody>
                    @if ($deleted_stock['count'] > 0)
                        @foreach ($deleted_stock['rows'] as $stock)
                            <tr id="deleted-stock-row-{{ $stock['id'] }}" class="align-middle">
                                <form enctype="multipart/form-data" action="./includes/stock-modify.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="stockmanagement-type" value="deleted"/>
                                    <input type="hidden" name="id" value="{{ $stock['id'] }}">
                                    <td class="align-middle text-center">{{ $stock['id'] }}</td>
                                    <td class="align-middle text-center"><a class="link" href="stock?stock_id={{ $stock['id'] }}">{{ $stock['name'] }}</a></td>
                                    <td class="align-middle text-center">{{ $stock['sku'] }}</td>
                                    <td class="align-middle text-center">
                                        @if (strlen($stock['description']) > 30) 
                                            <or title="{{ $stock['description'] }}">{{ substr($stock['description'], 0, 27) . '...' }}</or>
                                        @else
                                            <or>{{ $stock['description'] }}</or>
                                        @endif
                                        </or>
                                    </td>
                                    <td class="align-middle text-center">@if ((int)$stock['is_cable'] == 1) <or class="green">Yes</or> @else <or class="red">No</or>@endif</td>
                                    <td class="align-middle text-center"><button class="btn btn-success" type="submit" name="stockmanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                </form>
                            </tr>
                        @endforeach
                    @else 
                            <tr class="align-middle"><td colspan="100%">No deleted stock found.</td></tr>
                    @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- stock locations -->
        @include('includes/admin/stock-locations')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stocklocations-settings" onclick="toggleSection(this, 'stocklocations')">Stock Location Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Stock Location Settings -->
            <div style="padding-top: 20px" id="stocklocations" hidden>

                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'stocklocation-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                    
                <table class="table table-dark theme-table text-center" style="max-width:max-content; vertical-align: middle;">
                    <thead>
                        <tr class="theme-tableOuter">
                            <th>site_id</th>
                            <th>site_name</th>
                            <th hidden>site_description</th>
                            <th style="border-left:2px solid #95999c">area_id</th>
                            <th>area_name</th>
                            <th hidden>area_description</th>
                            <th hidden>area_site_id</th>
                            <th hidden>area_parent_id</th>
                            <th style="border-left:2px solid #95999c">shelf_id</th>
                            <th>shelf_name</th>
                            <th hidden>shelf_area_id</th>
                            <th style="border-left:2px solid #95999c" colspan=3>
                                <button id="show-deleted-location" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('location', 1)" @if ($sites['deleted_count'] + $areas['deleted_count'] + $shelves['deleted_count'] > 0) hidden @endif>
                                <span class="zeroStockFont">
                                    <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                </span>
                                </button>
                                <button id="hide-deleted-location" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('location', 0)" hidden>
                                    <span class="zeroStockFont">
                                        <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                    </span>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($sites['rows'] as $site)
                        @if (!$loop->first)
                        <tr class="theme-tableOuter @if ($site['deleted'] == 1) location-deleted" hidden @else " @endif><td colspan=9></td></tr>
                        @endif
                        @if ($site['deleted'] == 1)
                        <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                        @else
                        <tr style="background-color:{{ $location_colors[$loop->iteration % 2]['site'] !important; color:black">
                        @endif
                            <form id="siteForm-{{ $site['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                @csrf
                                <input type="hidden" id="site-{{ $site['id'] }}-type" name="type" value="site" />
                                <input type="hidden" id="site-{{ $site['id'] }}-id" name="id" value="{{ $site['id'] }}" />
                                <td class="stockTD" style="">{{ $site['id'] }}</td>
                                <td class="stockTD" style=""><input id="site-{{ $site['id'] }}-name" class="form-control stockTD-input" name="name" type="text" value="{{ htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                                <td hidden><input id="site-{{ $site['id'] }}-description" class="form-control stockTD-input" type="text" name="description" value="{{ htmlspecialchars($site['description'], ENT_QUOTES, 'UTF-8') }}" /></td>
                                <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td> <td hidden></td> <td hidden></td> 
                                <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </td>
                                <td class="stockTD theme-table-blank" ">
                                    <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $site['id'] }}', 'site')">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </td>
                            </form>
                            <form id="siteForm-delete-{{ $site['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                @csrf
                                <input type="hidden" name="location-id" value="{{ $site['id'] }}" />
                                <td class="stockTD theme-table-blank">');
                                @if ($site['deleted'] !== 1)
                                    <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" @if ($site_links[$site['id']]['count'] > 0) disabled title="Dependencies exist for this object." @else title="Delete object" @endif >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="site" type="submit" title="Restore object">
                                        <i class="fa fa-trash-restore"></i>
                                    </button>
                                @endif
                                </td>
                            </form>
                        </tr>
                        @foreach ($areas['rows'] as $area)
                            @if ($area['deleted'] == 1)
                            <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                            @else
                            <tr style="background-color:{{ $location_colors[$loop->parent->iteration % 2]['area'] !important; color:black">
                            @endif
                                <form id="areaForm-{{ $area['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" id="area-{{ $area['id'] }}-type" name="type" name="type" value="area" />
                                    <input type="hidden" id="area-{{ $area['id'] }}-id" name="id" value="{{ $area['id'] }}" />
                                    <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td>
                                    <td class="stockTD" style="border-left:2px solid #454d55; ">{{ $area['id'] }}</td>
                                    <td class="stockTD" style=""><input id="area-{{ $area['id'] }}-name" class="form-control stockTD-input" type="text" name="name" value="{{ htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                                    <td class="stockTD" hidden><input id="area-{{ $area['id'] }}-description" class="form-control stockTD-input" type="text" name="description" value="{{ htmlspecialchars($area['description'], ENT_QUOTES, 'UTF-8') }}" /></td>
                                    <td class="stockTD" hidden><input id="area-{{ $area['id'] }}-parent" type="hidden" name="area-site-id" value="{{ $area['site_id'] }}" /></td>
                                    <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                    <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                        <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </td>
                                    <td class="stockTD theme-table-blank">
                                        <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $area['id'] }}', 'area')">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </td>
                                </form>
                                <form id="areaForm-delete-{{ $area['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                    @csrf
                                    <input type="hidden" name="location-id" value="{{ $area['id'] }}" />
                                    <td class="stockTD theme-table-blank">');
                                    @if ($area['deleted'] !== 1)
                                        <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" @if ($area_links[$area['id']]['count'] > 0) disabled title="Dependencies exist for this object." @else title="Delete object" @endif >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="area" type="submit" title="Restore object">
                                            <i class="fa fa-trash-restore"></i>
                                        </button>
                                    @endif
                                    echo('
                                    </td>
                                </form>
                            </tr>
                            
                            @foreach($shelves['rows'] as $shelf)
                                @if ($shelf['deleted'] == 1)
                                <tr class="location-deleted" style="background-color:{{ $location_colors['deleted'] }} !important; color:black" hidden>
                                @else
                                <tr style="background-color:{{ $location_colors[$loop->parent->parent->iteration % 2]['shelf'] !important; color:black">
                                @endif
                                    <form id="shelfForm-{{ $shelf['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                        @csrf
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-site" name="site" value="{{ $site['id'] }}" />
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-type" name="type" value="shelf" />
                                        <input type="hidden" id="shelf-{{ $shelf['id'] }}-id" name="id" value="{{ $shelf['id'] }}" />
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td> 
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55;"></td> <td class="theme-table-blank"></td> <td hidden></td> <td hidden></td> <td hidden></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">{{ $shelf['id'] }}</td>
                                        <td class="stockTD" style=""><input id="shelf-{{ $shelf['id'] }}-name" class="form-control stockTD-input" type="text" name="name" value="{{ htmlspecialchars($shelf['name'], ENT_QUOTES, 'UTF-8') }}" style="width:150px"/></td>
                                        <td class="stockTD" hidden><input id="shelf-{{ $shelf['id'] }}-parent" type="hidden" name="shelf-area-id" value="{{ $shelf['area_id']  }}" /></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit('{{ $shelf['id'] }}', 'shelf')" >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>
                                    </form>
                                    <form id="shelfForm-delete-{{ $shelf['id'] }}" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                        @csrf
                                        <input type="hidden" name="location-id" value="{{ $shelf['id'] }}" />
                                        <td class="stockTD theme-table-blank">');
                                        @if ($shelf['deleted'] !== 1)
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" @if ($shelf_links[$shelf['id']]['count'] > 0) disabled title="Dependencies exist for this object." @else title="Delete object" @endif >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="area" type="submit" title="Restore object">
                                                <i class="fa fa-trash-restore"></i>
                                            </button>
                                        @endif
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="theme-table-blank @if ($site['deleted'] == 1) location-deleted" hidden @else " @endif>
                            <td colspan=6 class="stockTD">
                                <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px" onclick="modalLoadAdd({{ $site['id'] }})">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                            <td colspan=3 style="border-left:2px solid #454d55">  
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- ldap -->
        @include('includes/admin/ldap')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'ldap')">LDAP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- LDAP Settings -->
            <div style="padding-top: 20px" id="ldap" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'ldap-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}

                <?php
        
                // if (isset($_GET['ldapUpload'])) {
                //     echo ('<p id="success-output" class="green" style="margin-left:25px">');
                //     if ($_GET['ldapUpload'] == 'success') { echo('LDAP config uploaded!'); }
                //     if ($_GET['ldapUpload'] == 'configRestored') { echo('LDAP config restored to defaults!'); }
                //     echo('</p>');
                // }
                ?>
                <form id="ldapToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    @csrf
                    <input type="hidden" name="ldap-toggle-submit" value="set" />
                    <table id="ldapToggleTable">
                        <tbody>
                            <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                                <td style="width:150px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable LDAP</p>
                                    </td>
                                <td class="align-middle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="ldap-enabled" id="ldap-enabled-toggle" @if ($head_data['config']['ldap_enabled'] == 1) checked @endif >
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                
                <form id="ldapForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" @if ($head_data['config']['ldap_enabled'] == 0) hidden @endif >
                    @csrf
                    <hr style="border-color:white; margin-left:10px">
                    <table id="ldapTable">
                        <tbody>
                            <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px; margin-right:10px">
                                <th style="width:250px;margin-left:25px"></th>
                                <th style="width: 250px">Custom</th>
                                <th style="margin-left:25px">Default</th>
                            </tr>
                            <tr class="nav-row" id="ldap-auth-username">
                                <td id="ldap-auth-username-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Authentication Username:</p>
                                </td>
                                <td id="ldap-auth-username-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-username" name="auth-username" value="@if (isset($response_data['auth-username']) ? {{ $reponse_data['auth-username'] }} : {{ $head_data['config']['ldap_username'] }}@endif" required>
                                </td>
                                <td id="ldap-auth-username-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-username-default">{{ $head_data['default_config']['ldap_username'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password">
                                <td id="ldap-auth-password-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password">Authentication Password:</p>
                                </td>
                                <td id="ldap-auth-password-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password" name="auth-password" value="password" required>
                                </td>
                                <td id="ldap-auth-password-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-password-default" @if ($head_data['default_config']['ldap_password'] !== '') type="password" @endif>@if ($head_data['default_config']['ldap_password'] == '') Default missing... @else {{ $head_data['default_config']['ldap_password'] }} @endif</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password-confirm">
                                <td id="ldap-auth-password-confirm-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password-confirm">Confirm Password:</p>
                                </td>
                                <td id="ldap-auth-passowrd-confirm-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password-confirm" name="auth-password-confirm" value="password" required>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-domain">
                                <td id="ldap-auth-domain-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-domain">Domain:</p>
                                </td>
                                <td id="ldap-auth-domain-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-domain" name="auth-domain" value="@if (isset($response_data['auth-domain']) ? {{ $reponse_data['auth-domain'] }} : {{ $head_data['config']['ldap_domain'] }} @endif" required>
                                </td>
                                <td id="ldap-auth-domain-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-domain-default">{{ $head_data['default_config']['ldap_domain'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                                <td id="ldap-auth-host-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host">Host:</p>
                                </td>
                                <td id="ldap-auth-host-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host" name="auth-host" value="@if (isset($response_data['auth-host']) ? {{ $reponse_data['auth-host'] }} : {{ $head_data['config']['ldap_host'] }} @endif" required>
                                </td>
                                <td id="ldap-auth-host-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-default">{{ $head_data['default_config']['ldap_host'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                                <td id="ldap-auth-host-secondary-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host-secondary">Secondary Host:</p>
                                </td>
                                <td id="ldap-auth-host-secondary-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host-secondary" name="auth-host-secondary" value="@if (isset($response_data['auth-host-secondary']) ? {{ $reponse_data['auth-host-secondary'] }} : {{ $head_data['config']['ldap_host_secondary'] }} @endif">
                                </td>
                                <td id="ldap-auth-host-secondary-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-secondary-default">{{ $head_data['default_config']['ldap_host_secondary'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-port">
                                <td id="ldap-auth-port-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-port">Port:</p>
                                </td>
                                <td id="ldap-auth-port-input">
                                    <input class="form-control nav-v-c" type="number" style="width: 250px" id="auth-port" name="auth-port" value="@if (isset($response_data['auth-port']) ? {{ $reponse_data['auth-port'] }} : {{ $head_data['config']['ldap_port'] }} @endif" required>
                                </td>
                                <td id="ldap-auth-port-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-port-default">{{ $head_data['default_config']['ldap_port'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-basedn">
                                <td id="ldap-auth-basedn-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-basedn">Base DN:</p>
                                </td>
                                <td id="ldap-auth-basedn-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-basedn" name="auth-basedn" value="@if (isset($response_data['auth-basedn']) ? {{ $reponse_data['auth-basedn'] }} : {{ $head_data['config']['ldap_basedn'] }} @endif">
                                </td>
                                <td id="ldap-auth-basedn-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-basedn-default">{{ $head_data['default_config']['ldap_basedn'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-usergroup">
                                <td id="ldap-auth-usergroup-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-usergroup">User Group:</p>
                                </td>
                                <td id="ldap-auth-usergroup-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-usergroup" name="auth-usergroup" value="@if (isset($response_data['auth-usergroup']) ? {{ $reponse_data['auth-usergroup'] }} : {{ $head_data['config']['ldap_usergroup'] }} @endif">
                                </td>
                                <td id="ldap-auth-usergroup-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-usergroup-default">{{ $head_data['default_config']['ldap_usergroup'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-userfilter">
                                <td id="ldap-auth-userfilter-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-userfilter">User Filter:</p>
                                </td>
                                <td id="ldap-auth-userfilter-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-userfilter" name="auth-userfilter" value="@if (isset($response_data['auth-userfilter']) ? {{ $reponse_data['auth-userfilter'] }} : {{ $head_data['config']['userfilter'] }} @endif">
                                </td>
                                <td id="ldap-auth-userfilter-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-userfilter-default">{{ $head_data['default_config']['ldap_userfilter'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:250px">
                                    <input id="ldap-submit" type="submit" name="ldap-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                                </td>
                                <td style="width:250px">
                                    <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testLDAP()">Test config</a>
                                </td>
                                <td style="margin-left:25px">
                                    <input id="ldap-restore-defaults" type="submit" name="ldap-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        
        <!-- smtp -->
        @include('includes/admin/smtp')
        <div class="container" style="padding-bottom:0px">   
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="smtp-settings" onclick="toggleSection(this, 'smtp')">SMTP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- SMTP Settings -->
            <div style="padding-top: 20px" id="smtp" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'smtp-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <form id="smtpToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    @csrf
                    <input type="hidden" name="smtp-toggle-submit" value="set" />
                    <table id="smtpToggleTable">
                        <tbody>
                            <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                                <td style="width:150px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable SMTP</p>
                                    </td>
                                <td class="align-middle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="smtp-enabled" id="smtp-enabled-toggle" @if ($head_data['config']['smtp_enabled'] == 1) checked @endif >
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                <form id="smtpForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" @if ($head_data['config']['smtp_enabled'] == 0) hidden @endif >
                    @csrf
                    <hr style="border-color:white; margin-left:10px">
                    <table id="smtpTable">
                        <tbody>
                            <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                                <th style="width:250px;margin-left:25px"></th>
                                <th style="width: 250px">Custom</th>
                                <th style="margin-left:25px">Default</th>
                            </tr>
                            <tr class="nav-row" id="smtp-host-row">
                                <td id="smtp-host-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-host">SMTP Host:</p>
                                </td>
                                <td id="smtp-host-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-host" name="smtp-host" value="{{ $head_data['config']['smtp_host'] }}" required>
                                </td>
                                <td id="smtp-host-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-host-default">{{ $head_data['default_config']['smtp_host'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-port-row" style="margin-top:20px">
                                <td id="smtp-port-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-port">SMTP Port:</p>
                                </td>
                                <td id="smtp-port-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-port" name="smtp-port" value="{{ $head_data['config']['smtp_port'] }}" required>
                                </td>
                                <td id="smtp-port-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-port-default">{{ $head_data['default_config']['smtp_port'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-encryption-row" style="margin-top:20px">
                                <td id="smtp-encryption-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-encryption">SMTP Encryption:</p>
                                </td>
                                <td id="smtp-encryption-input">
                                    <select id="smtp-encryption" name="smtp-encryption" style="width:250px" class="form-control nav-v-c" required>
                                        <option value="none" @if ($head_data['config']['smtp_encryption'] == '' || $head_data['config']['smtp_encryption'] == null) selected @endif>None</option>
                                        <option value="starttls" @if ($head_data['config']['smtp_encryption'] == 'starttls') selected @endif>STARTTLS</option>
                                        <option value="tls" @if ($head_data['config']['smtp_encryption'] == 'tls') selected @endif>Transport Layer Security (TLS)</option>
                                        <option value="ssl" @if ($head_data['config']['smtp_encryption'] == 'ssl') selected @endif>Secure Sockets Layer (SSL)</option>
                                    </select>
                                </td>
                                <td id="smtp-encryption-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-encryption-default">
                                        @switch($head_data['default_config']['smtp_encryption'])
                                            @case('none')
                                            @case('')
                                            @case(null)
                                                None
                                                @break

                                            @case('starttls')
                                                STARTTLS
                                                @break

                                            @case('tls')
                                                Transport Layer Security (TLS)
                                                @break

                                            @case('ssl')
                                                Secure Sockets Layer (SSL)
                                                @break

                                            @default
                                                None
                                        @endswitch
                                    </p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-username-row" style="margin-top:20px">
                                <td id="smtp-username-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-username">SMTP Username:</p>
                                </td>
                                <td id="smtp-username-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-username" name="smtp-username" value="{{ $head_data['config']['smtp_username'] }}">
                                </td>
                                <td id="smtp-username-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-username-default">{{ $head_data['default_config']['smtp_username']  }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-password-row" style="margin-top:20px">
                                <td id="smtp-password-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-password">SMTP Password:</p>
                                </td>
                                <td id="smtp-password-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="smtp-password" name="smtp-password" value="password">
                                </td>
                                <td id="smtp-password-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-password-default"><or class="green">{{ $head_data['default_config']['smtp_password'] }}</or></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-from-email-row" style="margin-top:20px">
                                <td id="smtp-from-email-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-email">SMTP From Email:</p>
                                </td>
                                <td id="smtp-from-email-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-email" name="smtp-from-email" value="{{ $head_data['config']['smtp_from_email'] }}" required>
                                </td>
                                <td id="smtp-from-email-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-email-default">{{ $head_data['default_config']['smtp_from_email'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-from-name-row" style="margin-top:20px">
                                <td id="smtp-from-name-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-name">SMTP From Name:</p>
                                </td>
                                <td id="smtp-from-name-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-name" name="smtp-from-name" value="{{ $head_data['config']['smtp_from_name'] }}" required>
                                </td>
                                <td id="smtp-from-name-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-name-default">{{ $head_data['default_config']['smtp_from_name'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-backup-to-row" style="margin-top:20px">
                                <td id="smtp-backup-to-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-backup-to">SMTP To Email (Backup):</p>
                                </td>
                                <td id="smtp-backup-to-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-backup-to" name="smtp-backup-to" value="{{ $head_data['config']['smtp_to_email'] }}" required>
                                </td>
                                <td id="smtp-backup-to-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-backup-to-default">{{ $head_data['default_config']['smtp_to_email'] }}</p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:250px">
                                    <input id="smtp-submit" type="submit" name="smtp-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                                </td>
                                <td style="width:250px">
                                    <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testSMTP()">Test config</a>
                                    <i id="smtp-success-icon" class="fa-solid fa-check fa-lg" style="color: lime; margin-left:10px; display: none;" ></i>
                                    <i id="smtp-fail-icon" class="fa-solid fa-xmark fa-lg" style="color: red; margin-left:10px; display: none;" ></i>
                                    <i id="smtp-loading-icon" class="fa-solid fa-spinner fa-spin fa-lg" style="color: cyan; margin-left:10px; display: none;" ></i>
                                </td>
                                <td style="margin-left:25px">
                                    <input id="smtp-restore-defaults" type="submit" name="smtp-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        
        <!-- notifications -->
        @include('includes/admin/notifications')
        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="notification-settings" onclick="toggleSection(this, 'notification')">Email Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- Notification Settings -->
            <div style="padding-top: 20px" id="notification" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'notification-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}

            
                @if ($head_data['config']['smtp_enabled'] == 1)
                    @if ($notifications['count'] > 0)
                    <p id="notification-output" class="last-edit-T" hidden></p>
                    <table>
                        <tbody>
                        @foreach ($notifications['rows'] as $notification)
                            @if ($loop->first)
                            <tr>
                            @endif
                            @if (($loop->iteration -1) %4 == 0)
                            </tr><tr>
                            @endif
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="notif-{{ $notification['id'] }}">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="{{ $notification['description'] }}">{{ $notification['title'] }}:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="notif-{{ $notification['id'] }}-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                        <input type="checkbox" name="{{ $notification['name'] }}" onchange="mailNotification(this, {{ $notification['id'] }})" @if ($notification['enabled'] == 1) checked @endif>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            @if ($loop->last)
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    @else
                    <p id="notification-output"><or class="red">No notifications settings found in table...</or></p>
                    @endif
                @else
                    <p class="blue">SMTP is disabled. All email notifications have been disabled.</p>
                @endif
                <div class="well-nopad theme-divBg" style="margin-top:20px">
                    <h4>Email example</h4>
                    <input type="hidden" value="{{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\'stock.php?stock_id=1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}" id="email-template-body" />
                    <div id="email-template" style="margin-top:20px;margin-bottom:10px">
                    </div>
                    <a style="margin-left:5px" href="includes/smtp.inc.php?template=echo&body={{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\'stock.php?stock_id=1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}" target="_blank">View in new tab</a>
                </div>
            </div>
        </div>

        <!-- changelog --> 
        @include('includes/admin/changelog')
        <div style="padding-bottom:0px">
            <div class="container">
                <h3 class="clickable" style="margin-top:50px;font-size:22px" id="changelog-settings" onclick="toggleSection(this, 'changelog')">Changelog <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            </div>
            <!-- Changelog -->
            <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="changelog" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'changelog')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}

                <div class="content">
                    @if ($changelog['count'] > 0)
                    <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th>id</th>
                                <th style="min-width: 110px;">timestamp</th>
                                <th>user_id</th>
                                <th>user_username</th>
                                <th>action</th>
                                <th>table_name</th>
                                <th>record_id</th>
                                <th>field_name</th>
                                <th>value_old</th>
                                <th>value_new</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changelog['rows'] as $log)
                            <tr>
                                <td>{{ $log['id'] }}</td>
                                <td>{{ $log['timestamp'] }}</td>
                                <td>{{ $log['user_id'] }}</td>
                                <td>{{ $log['user_username'] }}</td>
                                <td>{{ $log['action'] }}</td>
                                <td>{{ $log['table_name'] }}</td>
                                <td>{{ $log['record_id'] }}</td>
                                <td>{{ $log['field_name'] }}</td>
                                <td>{{ $log['value_old'] }}</td>
                                <td>{{ $log['value_new'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a class="clickable" href="changelog.php">Full Changelog</a>
                </div>
            </div>
        </div>
        
        <!-- password reset modal -->
        <div id="modalDivResetPW" class="modal" style="display: none;">
            <span class="close" onclick="modalCloseResetPW()">×</span>
            <div class="container well-nopad theme-divBg" style="padding:25px">
                <div style="margin:auto;text-align:center;margin-top:10px">
                    <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin-pwreset-submit" value="set" />
                        <input type="hidden" name="user-id" id="modal-user-id" value=""/>
                        <table class="centertable">
                            <tbody>
                                <tr>
                                    <td class="align-middle" style="padding-right:20px">
                                        New Password:
                                    </td>
                                    <td class="align-middle" style="padding-right:20px">
                                        <input type="password" name="password" id="reset-password" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="submit" name="submit" class="btn btn-success" value="Change">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->
    
    
    <script>
        // blade reliant scripts

        // scripts for users modifications
        function userRoleChange(id) {
            var select = document.getElementById("user_"+id+"_role_select");
            var selectedValue = select.value;

            $.ajax({
                type: "POST",
                url: "./includes/admin.inc.php",
                data: {
                    user_id: id,
                    user_new_role: selectedValue,
                    user_role_submit: 'yes',
                    csrf_token: '{{ csrf_token() }}'
                },
                dataType: "html",
                success: function(response) {
                    console.log(response);
                    var tr = document.getElementById('users_table_info_tr');
                    var td = document.getElementById('users_table_info_td');
                    tr.hidden = false;
                    var result = response;
                    if (result.startsWith("Error:")) {
                        td.classList.add("red");
                    } else {
                        td.classList.add("green");
                    }
                    td.textContent = result;
                },
                async: true
            });
        }
        function usersEnabledChange(id) {
            var checkbox = document.getElementById("user_"+id+"_enabled_checkbox");
            if (checkbox.checked == true) {
                var checkboxValue = 1;
            } else {
                var checkboxValue = 0;
            }

            $.ajax({
                type: "POST",
                url: "./includes/admin.inc.php",
                data: {
                    user_id: id,
                    user_new_enabled: checkboxValue,
                    user_enabled_submit: 'yes',
                    csrf_token: '{{ csrf_token() }}'
                },
                dataType: "html",
                success: function(response) {
                    var tr = document.getElementById('users_table_info_tr');
                    var td = document.getElementById('users_table_info_td');
                    tr.hidden = false;
                    var result = response;
                    if (result.startsWith("Error:")) {
                        td.classList.add("red");
                    } else {
                        td.classList.add("green");
                    }
                    td.textContent = result;
                },
                async: true
            });
        }
    </script>
    
    <!-- Add the JS for the file -->
    <script src="js/admin.js"></script>
    
@include('foot')


</body>
