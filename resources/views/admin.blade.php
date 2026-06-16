<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')

    <title>{{ $head_data['config_compare']['system_name'] }} - Admin</title>
</head>
<body>

    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <!-- Admin Nav -->
    
    <!-- End of Admin Nav -->
    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="z-index:2; position:sticky; padding-top:60px; margin-bottom:20px">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl  leading-tight headerfix">
                    Admin
                </h2>
            </div>
        </header>
        <div class="row" style="width:max-content">
            <div class="col-md-auto">
                @include('includes.admin.nav')
                <div style="width:330px"></div>
            </div>
            <div class="col" style="padding-right:0px">
                <div class="" style="padding-bottom:75px">

            @switch($nav_secondary) 
                @case('global')
                    @include('includes.admin.global')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.footer')
                    @break
                @case('users')
                    @include('includes.admin.users')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.users-permission-presets')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.session-management')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.password-reset')
                    @break
                @case('authentication')
                    @include('includes.admin.authentication')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.ldap')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.sso')
                    @break
                @case('image-management')
                    @include('includes.admin.image-management')
                    @break
                @case('stock-management')
                    @include('includes.admin.stock-management')
                    @break
                @case('stock-attributes')
                    @include('includes.admin.attribute-management')
                    @break
                @case('optic-attributes')
                    @include('includes.admin.optic-attribute-management')
                    @break
                @case('cpu-attributes')
                    @include('includes.admin.cpu-attribute-management')
                    @break
                @case('memory-attributes')
                    @include('includes.admin.memory-attribute-management')
                    @break
                @case('disk-attributes')
                    @include('includes.admin.disk-attribute-management')
                    @break
                @case('stock-locations')
                    @include('includes.admin.stock-locations')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.location-modals')
                    @break
                @case('email')
                    @include('includes.admin.smtp')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.email-notifications')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.email-templates')
                    @break
                @case('webhook')
                    @include('includes.admin.webhook-notifications')
                    <hr style="margin-top:50px;border-color:#9f9d9d; margin-left:10px">
                    @include('includes.admin.webhook-templates')
                    @break
                @case('changelog')
                    @include('includes.admin.changelog')
                    @break
            @endswitch
            
            {{-- @include('includes.admin.location-modals')

            <!-- global -->
            @include('includes.admin.global')

            <!-- footer -->
            @include('includes.admin.footer')

            <!-- users -->
            @include('includes.admin.users')
            
            <!-- user-roles -->
            @include('includes.admin.users-permission-presets')
        
            <!-- authentication -->
            @include('includes.admin.authentication')
            
            <!-- session management -->
            @include('includes.admin.session-management')

            <!-- image management -->
            @include('includes.admin.image-management')
            
            <!-- attribute management -->
            @include('includes.admin.attribute-management')
            
            <!-- optic attribute management -->
            @include('includes.admin.optic-attribute-management')

            <!-- cpu attribute management -->
            @include('includes.admin.cpu-attribute-management')

            <!-- memory attribute management -->
            @include('includes.admin.memory-attribute-management')

            <!-- disk attribute management -->
            @include('includes.admin.disk-attribute-management')

            <!-- stock management -->
            @include('includes.admin.stock-management')

            <!-- stock locations -->
            @include('includes.admin.stock-locations')
            
            <!-- ldap -->
            @include('includes.admin.ldap')

            <!-- sso -->
            @include('includes.admin.sso')

            <!-- smtp -->
            @include('includes.admin.smtp')
            
            <!-- email notifications -->
            @include('includes.admin.email-notifications')

            <!-- email templates -->
            @include('includes.admin.email-templates')

            <!-- webhook notifications -->
            @include('includes.admin.webhook-notifications')

            <!-- webhook templates -->
            @include('includes.admin.webhook-templates')

            <!-- changelog --> 
            @include('includes.admin.changelog')
            
            <!-- password reset modal -->
            @include('includes.admin.password-reset') --}}
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
    
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/admin.js') }}"></script>
    
@include('foot')

</body>
