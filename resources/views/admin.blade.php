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
        @include('includes.admin.location-modals')

        <!-- global -->
        @include('includes.admin.global')

        <!-- footer -->
        @include('includes.admin.footer')

        <!-- users -->
        @include('includes.admin.users')
        
        <!-- user-roles -->
        @include('includes.admin.user-roles')
    
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

        <!-- stock management -->
        @include('includes.admin.stock-management')

        <!-- stock locations -->
        @include('includes.admin.stock-locations')
        
        <!-- ldap -->
        @include('includes.admin.ldap')
        
        <!-- smtp -->
        @include('includes.admin.smtp')
        
        <!-- notifications -->
        @include('includes.admin.notifications')

        <!-- changelog --> 
        @include('includes.admin.changelog')
        
        <!-- password reset modal -->
        @include('includes.admin.password-reset')
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
