<?php 
// ADMIN PAGE - SHOWS CONFIGURATION OPTIONS AND ONLY VISIBLE TO ADMIN USERS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 

?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Admin</title>
</head>
<body>
    <?php // dependency PHP    
    // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
    if (!in_array($_SESSION['role'], $config_admin_roles_array)) {
        header("Location: ./login.php");
        exit();
    }
    ?>


    <!-- set to index.php for now as there is nothing to put here. But i will forget about it if i remove it -->
    <a href="changelog.php" class="skip-nav-link-inv">changelog</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Admin</h2>
    </div>

    <script> // Toggle hide/show section
        function toggleSection(element, section) {
            var div = document.getElementById(section);
            var icon = element.children[0];
            if (div.hidden == false) {
                div.hidden=true;
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            } else {
                div.hidden=false;
                icon.classList.remove("fa-chevron-down");
                icon.classList.add("fa-chevron-up");
            }
        }
    </script>
    <div class="container content">

        <div id="modalDivAdd" class="modal">
        <!-- <div id="modalDivAdd" style="display: block;"> -->
            <span class="close" onclick="modalCloseAdd()">×</span>
            <div class="container well-nopad bg-dark" style="padding:25px">
                <div class="well-nopad bg-dark" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                    <div style="display:block"> 
                        <h2 style="margin-bottom:20px">Add new Site / Area / Shelf</h2>
                        <form id="locationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
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
        <!-- <div id="modalDivEdit" style="display: block;"> -->
            <span class="close" onclick="modalCloseEdit()">×</span>
            <div class="container well-nopad bg-dark" style="padding:25px">
                <div class="well-nopad bg-dark" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                    <div style="display:block"> 
                        <h2 style="margin-bottom:20px">Edit Location</h2>
                        <form id="editLocationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            <table class="centertable">
                                <tbody>
                                    <tr class="align-middle">
                                        <th style="padding-right:15px">Type:</th>
                                        <td>
                                            <input id="location-type-input" type="hidden" name="location-type" value="" />
                                            <label style="margin-bottom:0" id="location-type-text"></label>
                                        </td>
                                    </tr>
                                    <tr class="align-middle">
                                        <th style="padding-top:15px; padding-right:10px; padding-bottom:10px ">ID:</th>
                                        <td>
                                            <input id="location-id-input" type="hidden" name="location-id" value="" />
                                            <label style="margin-bottom:0" id="location-id-text"></label>
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


        <h3 class="clickable" style="font-size:22px" id="global-settings" onclick="toggleSection(this, 'global')">Global Settings <i class="fa-solid fa-chevron-up fa-2xs" style="margin-left:10px"></i></h3>
        <!-- Global Settings -->
        <div style="padding-top: 20px" id="global" hidden>
            <form id="globalForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <table id="globalTable">
                    <tbody>
                        <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Change</th>
                            <th style="min-width:230px;margin-left:25px">Custom</th>
                            <th style="min-width:230px;margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row">
                            <td id="system_name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="system_name">System Name:</p>
                            </td>
                            <td id="system_name-set" style="width:250px">
                                <input class="form-control nav-v-c" type="text" style="width: 150px" id="system_name" name="system_name">
                            </td>
                            <td style="min-width:230px;margin-left:10px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_system_name); ?></span></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_system_name); ?></span></label>
                            </td>
                        </tr>
                        <tr class="nav-row" id="banner-color" style="margin-top:20px">
                            <td id="banner-color-label" style="width:250px;margin-left:25px">
                                <!-- Custodian Colour: #72BE2A -->
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="banner_color">Banner Colour:</p>
                            </td>
                            <td id="banner-color-picker" style="width:250px">
                                <label class="label-color">
                                    <input class="form-control input-color color" id="banner_color" name="banner_color" placeholder="#XXXXXX" data-value="#xxxxxx" value="<?php echo($current_banner_color); ?>"/>
                                </label>
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><span class="uni" style="color:<?php echo(getWorB($current_banner_color)); ?>;background-color:<?php echo($current_banner_color); ?>"><?php echo($current_banner_color); ?></span></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><span class="uni" style="color:<?php echo(getWorB($default_banner_color)); ?>;background-color:<?php echo($default_banner_color); ?>"><?php echo($default_banner_color); ?></span></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="banner-logo">
                            <td id="banner-logo-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="logo_image">Banner Logo:</p>
                            </td>
                            <td id="banner-logo-file">
                                <input class="nav-v-c" type="file" style="width: 250px" id="logo_image" name="logo_image">
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($current_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($default_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="favicon-image">
                            <td id="favicon-image-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="favicon_image">Favicon Image:</p>
                            </td>
                            <td id="favicon-image-file">
                                <input class="nav-v-c" type="file" style="width: 250px" id="favicon_image" name="favicon_image">
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($current_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <label class="nav-v-c"><img class="thumb" src="./assets/img/config/<?php echo($default_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px">
                            <td id="currency-selector-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="currency_selection">Currency:</p>
                            </td>
                            <td id="currency-selector" style="width:250px">
                                <select id="currency_selection" name="currency_selection" placeholder="£" class="form-control" style="width:150px">
                                    <option alt="Pounds Sterling" value="£" <?php if ($current_currency == "£") { echo("selected"); } ?>>£ (Pound)</option>
                                    <option alt="Dollar"          value="$" <?php if ($current_currency == "$") { echo("selected"); } ?>>$ (Dollar)</option>
                                    <option alt="Euro"            value="€" <?php if ($current_currency == "€") { echo("selected"); } ?>>€ (Euro)</option>
                                    <option alt="Yen"             value="¥" <?php if ($current_currency == "¥") { echo("selected"); } ?>>¥ (Yen)</option>
                                    <option alt="Franc"           value="₣" <?php if ($current_currency == "₣") { echo("selected"); } ?>>₣ (Franc)</option>
                                    <option alt="Rupee"           value="₹" <?php if ($current_currency == "₹") { echo("selected"); } ?>>₹ (Rupee)</option>
                                    <option alt="Mark"            value="₻" <?php if ($current_currency == "₻") { echo("selected"); } ?>>₻ (Mark)</option>
                                    <option alt="Ruouble"         value="₽" <?php if ($current_currency == "₽") { echo("selected"); } ?>>₽ (Ruouble)</option>
                                    <option alt="Lira"            value="₺" <?php if ($current_currency == "₺") { echo("selected"); } ?>>₺ (Lira)</option>
                                </select>
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_currency); ?></span></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_currency); ?></span></label>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px">
                            <td id="sku-prefix-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="sku_prefix">SKU Prefix:</p>
                            </td>
                            <td id="sku-prefix-set" style="width:250px">
                                <input class="form-control nav-v-c" type="text" style="width: 150px" id="sku_prefix" name="sku_prefix">
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_sku_prefix); ?></span></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_sku_prefix); ?></span></label>
                            </td>
                        </tr>

                        <tr class="nav-row" style="margin-top:20px">
                            <td id="base-url-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="base_url">Base URL:</p>
                            </td>
                            <td id="base-url-set" style="width:250px">
                                <input class="form-control nav-v-c" type="text" style="width: 150px" id="base_url" name="base_url">
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($current_base_url); ?></span></label>
                            </td>
                            <td style="min-width:230px;margin-left:25px; padding-left:15px">
                                <label class="nav-v-c"><span class="uni"><?php echo($default_base_url); ?></span></label>
                            </td>
                        </tr>


                        <tr class="nav-row" style="margin-top:20px;margin-left:25px">
                            <td style="width:250px">
                                <input id="global-submit" type="submit" name="global-submit" class="btn btn-success" value="Save" />
                            </td>
                            <td style="width:250px">
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                            </td>
                            <td style="min-width:230px;margin-left:25px">
                                <input id="global-restore-defaults" type="submit" name="global-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="users-settings" onclick="toggleSection(this, 'users')">Users <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
        <!-- Users Settings -->
        <div style="padding-top: 20px" id="users" hidden>
            <table id="usersTable" class="table table-dark" style="max-width:max-content">
                <thead>
                    <tr id="users_table_info_tr" hidden>
                        <td colspan=8 id="users_table_info_td"></td>
                    </tr>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Auth</th>
                        <th>Enabled</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // GET users from table

                        $sql_users = "SELECT users.id as id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.enabled as enabled FROM users 
                                        INNER JOIN users_roles ON users.role_id = users_roles.id";
                        $stmt_users = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                            echo('<td colspan=9><or class="red">SQL Issue with `users` table.</or></td>');
                        } else {
                            mysqli_stmt_execute($stmt_users);
                            $result_users = mysqli_stmt_get_result($stmt_users);
                            $rowCount_users = $result_users->num_rows;
                            if ($rowCount_users < 1) {
                                echo ('<td colspan=9><or class="red">No Users in table: `users`.</or></td>');
                            } else {
                                while($row_users = $result_users->fetch_assoc()) {
                                    $user_id = $row_users['id'];
                                    $user_username = $row_users['username'];
                                    $user_first_name = $row_users['first_name'];
                                    $user_last_name = $row_users['last_name'];
                                    $user_email = $row_users['email'];
                                    $user_role = $row_users['role'];
                                    $user_auth = $row_users['auth'];
                                    $user_enabled = $row_users['enabled'];
                                    $user_roles = [];

                                    $sql_roles = "SELECT * FROM users_roles";
                                    $stmt_roles = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
                                        echo('SQL Issue with `users` table.');
                                    } else {
                                        mysqli_stmt_execute($stmt_roles);
                                        $result_roles = mysqli_stmt_get_result($stmt_roles);
                                        $rowCount_roles = $result_roles->num_rows;
                                        if ($rowCount_roles < 1) {
                                            echo ('No Roles in table: `users_roles`.');
                                        } else {
                                            $i = 0;
                                            while ($row_roles = $result_roles->fetch_assoc()) {
                                                $user_roles[$i]['id'] = $row_roles['id'];
                                                $user_roles[$i]['name'] = $row_roles['name'];
                                                $i++;
                                            }
                                        }
                                    }


                                    
                                    echo('<tr class="text-center" style="vertical-align: middle;">
                                        <td id="user_'.$user_id.'_id" style="vertical-align: middle;">'.$user_id.'</td>
                                        <td id="user_'.$user_id.'_username" style="vertical-align: middle;">'.$user_username.'</td>
                                        <td id="user_'.$user_id.'_first_name" style="vertical-align: middle;">'.$user_first_name.'</td>
                                        <td id="user_'.$user_id.'_last_name" style="vertical-align: middle;">'.$user_last_name.'</td>
                                        <td id="user_'.$user_id.'_email" style="vertical-align: middle;">'.$user_email.'</td>
                                        <td id="user_'.$user_id.'_role" style="vertical-align: middle;">
                                            <select class="form-control" id="user_'.$user_id.'_role_select" style="padding-top:0px; padding-bottom:0px" onchange="userRoleChange(\''.$user_id.'\') "'); if ($user_id == 0 || $user_id == '0') { echo("disabled"); } echo('>');
                                            foreach ($user_roles as $role) {
                                                echo('<option value="'.$role['id'].'"');
                                                // check if the user role matches or not, and mark it as selected
                                                if ($role['name'] == $user_role) {
                                                    echo (' selected');
                                                }
                                                // check if the user role is the root user role (0), if it is, disable it so that it cannot be selected.
                                                if ($role['id'] == 0 || $role['id'] == '0') {
                                                    echo(' disabled');
                                                }
                                                echo('>'.ucwords($role['name']).'</option>');
                                            }
                                            echo('
                                            </select>
                                        </td>
                                        <td id="user_'.$user_id.'_auth" style="vertical-align: middle;">'.$user_auth.'</td>
                                        <td style="vertical-align: middle;"><input type="checkbox" id="user_'.$user_id.'_enabled_checkbox"');
                                            if ($user_enabled == 1) {
                                                echo('checked');
                                            }
                                        echo(' onchange="usersEnabledChange(\''.$user_id.'\')"/>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning" id="user_'.$user_id.'_pwreset" onclick="resetPassword(\''.$user_id.'\')"'); if ($user_auth == "ldap" || $user_role == "Admin" || $user_role == "Root") { echo("disabled"); } echo('>Reset Password</button>
                                        </td>
                                        ');
                                }
                                echo('<tr style="background-color:#21272b"><td></td><td colspan=8><button class="btn btn-success" type="button" onclick="navPage(\'addlocaluser.php\');"><i class="fa fa-plus"></i> Add</button></td></tr>');
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="usersroles-settings" onclick="toggleSection(this, 'usersroles')">User Roles <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
        <!-- Users Settings -->
        <div style="padding-top: 20px" id="usersroles" hidden>
            <table id="usersTable" class="table table-dark" style="max-width:max-content">
                <thead>
                    <tr id="users_table_info_tr" hidden>
                        <td colspan=8 id="users_table_info_td"></td>
                    </tr>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Administrator</th>
                        <th>Root</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql_roles = "SELECT * FROM users_roles";
                        $stmt_roles = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
                            echo('<td colspan=7><or class="red">SQL Issue with `users` table.</or></td>');
                        } else {
                            mysqli_stmt_execute($stmt_roles);
                            $result_roles = mysqli_stmt_get_result($stmt_roles);
                            $rowCount_roles = $result_roles->num_rows;
                            if ($rowCount_roles < 1) {
                                echo ('<td colspan=7><or class="red">No Roles in table: `users`.</or></td>');
                            } else {
                                while ($row_roles = $result_roles->fetch_assoc()) {
                                    echo('<tr class="text-center">
                                    <td>'.$row_roles['id'].'</td>
                                    <td>'.$row_roles['name'].'</td>
                                    <td>'.$row_roles['description'].'</td>
                                    <td style="vertical-align: middle;">'); if ($row_roles['is_admin'] == 1) { echo('<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>'); } else { echo('<i class="fa-solid fa-xmark" style="color: #ff0000;"></i>'); } echo ('</td>
                                    <td style="vertical-align: middle;">'); if ($row_roles['is_root'] == 1) { echo('<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>'); } else { echo('<i class="fa-solid fa-xmark" style="color: #ff0000;"></i>'); } echo ('</td>
                                    </tr>');
                                }
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="imagemanagement-settings" onclick="toggleSection(this, 'imagemanagement')">Image Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
        <!-- Image Management Settings -->
        <div style="padding-top: 20px" id="imagemanagement" hidden>
            <div style="height:75%;overflow-x: hidden;overflow-y: auto;">
                <table class="table table-dark" style="max-width:max-content">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:130px">Image</th>
                            <th class="text-center">File</th>
                            <th class="text-center">Links</th>
                            <th class="text-center">Delete</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $filepath = 'assets/img/stock';
                        $files = array_values(array_diff(scandir($filepath), array('..', '.')));
                        // print_r($files);
                        

                        for ($f=0; $f<count($files); $f++) {
                            $filename = $files[$f];

                            $sql_images = "SELECT * FROM stock_img WHERE image='$filename'";
                            $stmt_images = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_images, $sql_images)) {
                                echo("ERROR getting entries");
                            } else {
                                mysqli_stmt_execute($stmt_images);
                                $result_images = mysqli_stmt_get_result($stmt_images);
                                $rowCount_images = $result_images->num_rows;
                                $links = $rowCount_images;
                            }
                            echo('
                                <tr id="image-row-'.$f.'" class="align-middle">
                                    <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                        <input type="hidden" name="file-name" value="'.$filename.'" />
                                        <input type="hidden" name="file-links" value="'.$links.'" />
                                        <td id="image-'.$f.'-thumb" class="text-center align-middle" style="width:130px"><img id="image-'.$f.'-img" class="inv-img-main thumb" alt="'.$filename.'" src="'.$filepath.'/'.$filename.'" onclick="modalLoad(this)"></td>
                                        <td id="image-'.$f.'-name" class="text-center align-middle">'.$filepath.'/'.$filename.'</td>
                                        <td class="text-center align-middle">'.$links.'</td>
                                        <td class="text-center align-middle"><button class="btn btn-danger" type="submit" name="imagemanagement-submit" '); if ($links !== 0) { echo('disabled title="Image still linked to stock. Remove these links before deleting."'); } echo('><i class="fa fa-trash"></i></button></td>
                                        <td class="text-center align-middle">'); if ($links !== 0) { echo('<button class="btn btn-warning" id="image-'.$f.'-links" type="button" onclick="showImageLinks(\''.$f.'\')">Show Links</button>'); } echo('</td>
                                    </form>
                                </tr>
                            ');
                            if ($links !== 0) { 
                                echo('
                                    <tr id="image-row-'.$f.'-links" class="align-middle" hidden>
                                        <td colspan=100%>
                                            <div>
                                                <table class="table table-dark">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</td>
                                                            <th>Stock ID</td>
                                                            <th>Image</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>');
                                                    while ($row_images = $result_images->fetch_assoc()) {
                                                        echo('
                                                            <tr class="clickable" onclick=navPage("stock.php?stock_id='.$row_images['stock_id'].'")>
                                                                <td>'.$row_images['id'].'</td>
                                                                <td><a href="stock.php?stock_id='.$row_images['stock_id'].'">'.$row_images['stock_id'].'</a></td>
                                                                <td>'.$row_images['image'].'</td>
                                                            </tr>
                                                        ');
                                                    }
                                                    
                                                    echo('
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                ');
                            }
                        }
                        

                    ?>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stocklocations-settings" onclick="toggleSection(this, 'stocklocations')">Stock Location Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
        <!-- Stock Location Settings -->
        <div style="padding-top: 20px" id="stocklocations" hidden>

            <?php
            $locations = [];
            $sql_locations = "SELECT site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    area.id AS area_id, area.name AS area_name, area.description AS area_description, area.site_id AS area_site_id, area.parent_id AS area_parent_id,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name, shelf.area_id AS shelf_area_id
                                FROM site
                                LEFT JOIN area ON site.id = area.site_id
                                LEFT JOIN shelf ON area.id = shelf.area_id
                                WHERE site.deleted=0 AND area.deleted=0 AND shelf.deleted=0
                                ORDER BY site.id, area.id, shelf.id";
            $stmt_locations = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_locations, $sql_locations)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_locations);
                $result_locations = mysqli_stmt_get_result($stmt_locations);
                $rowCount_locations = $result_locations->num_rows;
                if ($rowCount_locations < 1) {
                    echo ("No sites found");
                } else {
                    while( $row_locations = $result_locations->fetch_assoc() ) {  
                        $locations[$row_locations['site_id']]['site_id'] = $row_locations['site_id'];
                        $locations[$row_locations['site_id']]['site_name'] = $row_locations['site_name'];
                        $locations[$row_locations['site_id']]['site_description'] = $row_locations['site_description'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_id'] = $row_locations['area_id'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_name'] = $row_locations['area_name'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_description'] = $row_locations['area_description'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_site_id'] = $row_locations['area_site_id'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_parent_id'] = $row_locations['area_parent_id'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_id'] = $row_locations['shelf_id'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_name'] = $row_locations['shelf_name'];
                        $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_area_id'] = $row_locations['shelf_area_id'];
                    }
                    // print_r('<pre>');
                    // print_r($locations);
                    // print_r('</pre>');
                    $l = 0;
                    echo('<table class="table table-dark text-center" style="max-width:max-content; vertical-align: middle;">
                            <thead>
                                <tr>
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
                                    <th style="border-left:2px solid #95999c"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>');
                            foreach ($locations as $site) {
                                $l++;
                                if ($l % 2 == 0) {
                                    $color1 = '#F4BB44';
                                    $color2 = '#ffe47a';
                                    $color3 = '#FFDEAD';
                                } else {
                                    $color1 = '#6abad6';
                                    $color2 = '#99d4ef';
                                    $color3 = '#c1e9fc';
                                    
                                }
                                if ($l > 1) {
                                    echo('<tr style="background-color:#343a40"><td colspan=9></td></tr>');
                                }

                                $site_id_check = $site['site_id'];

                                $sql_site_check = "SELECT * FROM area WHERE site_id=$site_id_check;";
                                $stmt_site_check = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_site_check, $sql_site_check)) {
                                    echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                } else {
                                    mysqli_stmt_execute($stmt_site_check);
                                    $result_site_check = mysqli_stmt_get_result($stmt_site_check);
                                    $rowCount_site_check = $result_site_check->num_rows;
                                }

                                echo('<tr style="background-color:'.$color1.' !important; color:black">
                                        <form id="siteForm-'.$site['site_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" id="site-'.$site['site_id'].'-type" name="type" value="site" />
                                            <input type="hidden" id="site-'.$site['site_id'].'-id" name="id" value="'.$site['site_id'].'" />
                                            <td class="stockTD" style="">'.$site['site_id'].'</td>
                                            <td class="stockTD" style=""><input id="site-'.$site['site_id'].'-name" class="form-control stockTD-input" name="name" type="text" value="'.$site['site_name'].'" style="width:150px"/></td>
                                            <td hidden><input id="site-'.$site['site_id'].'-description" class="form-control stockTD-input" type="text" name="description" value="'.$site['site_description'].'" /></td>
                                            <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td> <td hidden></td> <td hidden></td> 
                                            <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                            <td class="stockTD" style="background-color:#21272b; border-left:2px solid #454d55; ">
                                                <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                    <i class="fa fa-save"></i>
                                                </button>
                                            </td>
                                            <td class="stockTD" style="background-color:#21272b; ">
                                                <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$site['site_id'].'\', \'site\')">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </td>
                                        </form>
                                        <form id="siteForm-delete-'.$site['site_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                        <input type="hidden" name="location-id" value="'.$site_id_check.'" />
                                            <td class="stockTD" style="background-color:#21272b; ">
                                                <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" '); 
                                                if ($rowCount_site_check > 0 ) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                echo('>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </form>
                                    </tr>');
                                foreach ($site['areas'] as $area) {
                                    if ($area['area_id'] !== '' && $area['area_id'] !== null) {
                                        $area_id_check = $area['area_id'];

                                        $sql_area_check = "SELECT * FROM shelf WHERE area_id=$area_id_check;";
                                        $stmt_area_check = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_area_check, $sql_area_check)) {
                                            echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                        } else {
                                            mysqli_stmt_execute($stmt_area_check);
                                            $result_area_check = mysqli_stmt_get_result($stmt_area_check);
                                            $rowCount_area_check = $result_area_check->num_rows;
                                        }

                                        echo('<tr style="background-color:'.$color2.' !important; color:black">
                                                <form id="areaForm-'.$area['area_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                    <input type="hidden" id="area-'.$area['area_id'].'-type" name="type" name="type" value="area" />
                                                    <input type="hidden" id="area-'.$area['area_id'].'-id" name="id" value="'.$area['area_id'].'" />
                                                    <td class="stockTD" style=" background-color:#21272b"></td> <td style="background-color:#21272b"></td> <td hidden></td>
                                                    <td class="stockTD" style="border-left:2px solid #454d55; ">'.$area['area_id'].'</td>
                                                    <td class="stockTD" style=""><input id="area-'.$area['area_id'].'-name" class="form-control stockTD-input" type="text" name="name" value="'.$area['area_name'].'" style="width:150px"/></td>
                                                    <td class="stockTD" hidden><input id="area-'.$area['area_id'].'-description" class="form-control stockTD-input" type="text" name="description" value="'.$area['area_description'].'" /></td>
                                                    <td class="stockTD" hidden><input id="area-'.$area['area_id'].'-parent" type="hidden" name="area-site-id" value="'.$area['area_site_id'].'" /></td>
                                                    <td class="stockTD" hidden>'.$area['area_parent_id'].'</td>
                                                    <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                                    <td class="stockTD" style="background-color:#21272b; border-left:2px solid #454d55; ">
                                                        <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                            <i class="fa fa-save"></i>
                                                        </button>
                                                    </td>
                                                    <td class="stockTD" style="background-color:#21272b; ">
                                                        <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$area['area_id'].'\', \'area\')">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                    </td>
                                                </form>
                                                <form id="areaForm-delete-'.$area['area_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                <input type="hidden" name="location-id" value="'.$area_id_check.'" />
                                                    <td class="stockTD" style="background-color:#21272b; ">
                                                        <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" '); 
                                                        if ($rowCount_area_check != 0) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                        echo('>
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </form>
                                            </tr>');
                                        foreach ($area['shelves'] as $shelf) {
                                            if ($shelf['shelf_id'] !== '' && $shelf['shelf_id'] !== null) {
                                                $shelf_id_check = $shelf['shelf_id'];

                                                $sql_shelf_check = "SELECT * FROM item WHERE shelf_id=$shelf_id_check;";
                                                $stmt_shelf_check = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt_shelf_check, $sql_shelf_check)) {
                                                    echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                                } else {
                                                    mysqli_stmt_execute($stmt_shelf_check);
                                                    $result_shelf_check = mysqli_stmt_get_result($stmt_shelf_check);
                                                    $rowCount_shelf_check = $result_shelf_check->num_rows;
                                                }

                                                echo('<tr style="background-color:'.$color3.' !important; color:black">
                                                        <form id="shelfForm-'.$shelf['shelf_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                            <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-site" name="site" value="'.$site['site_id'].'" />
                                                            <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-type" name="type" value="shelf" />
                                                            <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-id" name="id" value="'.$shelf['shelf_id'].'" />
                                                            <td class="stockTD" style="background-color:#21272b"></td> <td style="background-color:#21272b"></td> <td hidden></td> 
                                                            <td class="stockTD" style="border-left:2px solid #454d55; background-color:#21272b"></td> <td style="background-color:#21272b"></td> <td hidden></td> <td hidden></td> <td hidden></td>
                                                            <td class="stockTD" style="border-left:2px solid #454d55; ">'.$shelf['shelf_id'].'</td>
                                                            <td class="stockTD" style=""><input id="shelf-'.$shelf['shelf_id'].'-name" class="form-control stockTD-input" type="text" name="name" value="'.$shelf['shelf_name'].'" style="width:150px"/></td>
                                                            <td class="stockTD" hidden><input id="shelf-'.$shelf['shelf_id'].'-parent" type="hidden" name="shelf-area-id" value="'.$shelf['shelf_area_id'].'" /></td>
                                                            <td class="stockTD" style="background-color:#21272b; border-left:2px solid #454d55; ">
                                                                <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                                    <i class="fa fa-save"></i>
                                                                </button>
                                                            </td>
                                                            <td class="stockTD" style="background-color:#21272b; ">
                                                                <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$shelf['shelf_id'].'\', \'shelf\')" >
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            </td>
                                                        </form>
                                                        <form id="shelfForm-delete-'.$shelf['shelf_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                            <input type="hidden" name="location-id" value="'.$shelf_id_check.'" />
                                                            <td class="stockTD" style="background-color:#21272b; ">
                                                                <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="shelf" type="submit" '); 
                                                                if ($rowCount_shelf_check != 0) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                                echo('>
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </form>
                                                    </tr>');
                                            }
                                        }
                                    }
                                }
                                echo('<tr style="background-color:#21272b">
                                    <td colspan=6 class="stockTD">
                                        <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px" onclick="modalLoadAdd(\''.$site['site_id'].'\')">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                    <td colspan=3 style="border-left:2px solid #454d55">  
                                    </td>
                                </tr>');
                            }
                            // echo('<tr style="background-color:#21272b">
                            //         <td></td> <td></td> <td hidden></td> <td></td> <td></td> <td hidden></td> <td hidden></td> <td hidden></td> <td></td> <td></td> <td hidden></td>
                            //             <td>
                            //                 <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px">
                            //                     <i class="fa fa-plus"></i>
                            //                 </button>
                            //             </td>
                            //         </tr>');
                    echo('  </tbody>
                        </table>');
                }
            }   





            ?>

        </div>

        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'ldap')">LDAP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

        <!-- LDAP Settings -->
        <div style="padding-top: 20px" id="ldap" hidden>
            <?php 
            if (isset($_GET['error'])) {
                echo ('<p id="error-output" class="red" style="margin-left:25px">');
                if ($_GET['error'] == 'emptyFields') { echo('Empty fields in config.'); }
                echo('</p>');
            }
            if (isset($_GET['ldapUpload'])) {
                echo ('<p id="success-output" class="green" style="margin-left:25px">');
                if ($_GET['ldapUpload'] == 'success') { echo('LDAP config uploaded!'); }
                if ($_GET['ldapUpload'] == 'configRestored') { echo('LDAP config restored to defaults!'); }
                echo('</p>');
            }
            ?>
            <form id="ldapToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <input type="hidden" name="ldap-toggle-submit" value="set" />
                <table id="ldapToggleTable">
                    <tbody>
                        <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                            <td style="width:150px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-username">Enable LDAP</p>
                                </td>
                            <td class="align-middle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                    <input type="checkbox" name="ldap-enabled" id="ldap-enabled-toggle" <?php if($current_ldap_enabled == 1) { echo("checked"); } ?> >
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            
            <form id="ldapForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" <?php if($current_ldap_enabled == 0) { echo("hidden"); } ?>>
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
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-username">Authentication Username:</p>
                            </td>
                            <td id="ldap-auth-username-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-username" name="auth-username" value="<?php echo(isset($_GET['auth-username']) ? $_GET['auth-username'] : $current_ldap_username); ?>" required>
                            </td>
                            <td id="ldap-auth-username-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-username-default"><?php echo($default_ldap_username); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password">
                            <td id="ldap-auth-password-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-password">Authentication Password:</p>
                            </td>
                            <td id="ldap-auth-password-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password" name="auth-password" value="password" required>
                            </td>
                            <td id="ldap-auth-password-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-password-default" <?php echo(($config_d_ldap_password !== '') ? 'type="password"' : ''); ?>><?php echo($default_ldap_password); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password-confirm">
                            <td id="ldap-auth-password-confirm-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-password-confirm">Confirm Password:</p>
                            </td>
                            <td id="ldap-auth-passowrd-confirm-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password-confirm" name="auth-password-confirm" value="password" required>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-domain">
                            <td id="ldap-auth-domain-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-domain">Domain:</p>
                            </td>
                            <td id="ldap-auth-domain-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-domain" name="auth-domain" value="<?php echo(isset($_GET['auth-domain']) ? $_GET['auth-domain'] : $current_ldap_domain); ?>" required>
                            </td>
                            <td id="ldap-auth-domain-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-domain-default"><?php echo($default_ldap_domain); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                            <td id="ldap-auth-host-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-host">Host:</p>
                            </td>
                            <td id="ldap-auth-host-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host" name="auth-host" value="<?php echo(isset($_GET['auth-host']) ? $_GET['auth-host'] : $current_ldap_host); ?>" required>
                            </td>
                            <td id="ldap-auth-host-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-host-default"><?php echo($default_ldap_host); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                            <td id="ldap-auth-host-secondary-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-host-secondary">Secondary Host:</p>
                            </td>
                            <td id="ldap-auth-host-secondary-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host-secondary" name="auth-host-secondary" value="<?php echo(isset($_GET['auth-host-secondary']) ? $_GET['auth-host-secondary'] : $current_ldap_host_secondary); ?>">
                            </td>
                            <td id="ldap-auth-host-secondary-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-host-secondary-default"><?php echo($default_ldap_host_secondary); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-port">
                            <td id="ldap-auth-port-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-port">Port:</p>
                            </td>
                            <td id="ldap-auth-port-input">
                                <input class="form-control nav-v-c" type="number" style="width: 250px" id="auth-port" name="auth-port" value="<?php echo(isset($_GET['auth-port']) ? $_GET['auth-port'] : $current_ldap_port); ?>" required>
                            </td>
                            <td id="ldap-auth-port-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-port-default"><?php echo($default_ldap_port); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-basedn">
                            <td id="ldap-auth-basedn-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-basedn">Base DN:</p>
                            </td>
                            <td id="ldap-auth-basedn-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-basedn" name="auth-basedn" value="<?php echo(isset($_GET['auth-basedn']) ? $_GET['auth-basedn'] : $current_ldap_basedn); ?>">
                            </td>
                            <td id="ldap-auth-basedn-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-basedn-default"><?php echo($default_ldap_basedn); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-usergroup">
                            <td id="ldap-auth-usergroup-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-usergroup">User Group:</p>
                            </td>
                            <td id="ldap-auth-usergroup-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-usergroup" name="auth-usergroup" value="<?php echo(isset($_GET['auth-usergroup']) ? $_GET['auth-usergroup'] : $current_ldap_usergroup); ?>">
                            </td>
                            <td id="ldap-auth-usergroup-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-usergroup-default"><?php echo($default_ldap_usergroup); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="ldap-auth-userfilter">
                            <td id="ldap-auth-userfilter-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-userfilter">User Filter:</p>
                            </td>
                            <td id="ldap-auth-userfilter-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-userfilter" name="auth-userfilter" value="<?php echo(isset($_GET['auth-userfilter']) ? $_GET['auth-userfilter'] : $current_ldap_userfilter); ?>">
                            </td>
                            <td id="ldap-auth-userfilter-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="auth-userfilter-default"><?php echo($default_ldap_userfilter); ?></p>
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
        <h3 class="clickable" style="margin-top:50px;font-size:22px" id="smtp-settings" onclick="toggleSection(this, 'smtp')">SMTP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

        <!-- SMTP Settings -->
        <div style="padding-top: 20px" id="smtp" hidden>
            <form id="smtpToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                <input type="hidden" name="smtp-toggle-submit" value="set" />
                <table id="smtpToggleTable">
                    <tbody>
                        <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                            <td style="width:150px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="auth-username">Enable SMTP</p>
                                </td>
                            <td class="align-middle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                    <input type="checkbox" name="smtp-enabled" id="smtp-enabled-toggle" <?php if($current_smtp_enabled == 1) { echo("checked"); } ?> >
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <form id="smtpForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" <?php if($current_smtp_enabled == 0) { echo("hidden"); } ?>>
                <table id="smtpTable">
                    <tbody>
                        <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                            <th style="width:250px;margin-left:25px"></th>
                            <th style="width: 250px">Custom</th>
                            <th style="margin-left:25px">Default</th>
                        </tr>
                        <tr class="nav-row" id="smtp-host-row">
                            <td id="smtp-host-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-host">SMTP Host:</p>
                            </td>
                            <td id="smtp-host-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-host" name="smtp-host" value="<?php echo $current_smtp_host; ?>" required>
                            </td>
                            <td id="smtp-host-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-host-default"><?php echo $default_smtp_host; ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-port-row" style="margin-top:20px">
                            <td id="smtp-port-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-port">SMTP Port:</p>
                            </td>
                            <td id="smtp-port-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-port" name="smtp-port" value="<?php echo $current_smtp_port; ?>" required>
                            </td>
                            <td id="smtp-port-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-port-default"><?php echo $default_smtp_port; ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-encryption-row" style="margin-top:20px">
                            <td id="smtp-encryption-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-encryption">SMTP Encryption:</p>
                            </td>
                            <td id="smtp-encryption-input">
                                <select id="smtp-encryption" name="smtp-encryption" style="width:250px" class="form-control nav-v-c" required>
                                    <option value="none" <?php if ($current_smtp_encryption == 'starttls' || $current_smtp_encryption == '' || $current_smtp_encryption == null) { echo 'selected'; } ?>>None</option>
                                    <option value="starttls" <?php if ($current_smtp_encryption == 'starttls') { echo 'selected'; } ?>>STARTTLS</option>
                                    <option value="tls" <?php if ($current_smtp_encryption == 'tls') { echo 'selected'; } ?>>Transport Layer Security (TLS)</option>
                                    <option value="ssl" <?php if ($current_smtp_encryption == 'ssl') { echo 'selected'; } ?>>Secure Sockets Layer (SSL)</option>
                                </select>
                            </td>
                            <td id="smtp-encryption-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-encryption-default">
                                    <?php 
                                        switch ($default_smtp_encryption) {
                                            case 'none':
                                            case '':
                                            case null:
                                                echo 'None';
                                                break;
                                            case 'starttls':
                                                echo 'STARTTLS';
                                                break;
                                            case 'tls':
                                                echo 'Transport Layer Security (TLS)';
                                                break;
                                            case 'ssl':
                                                echo 'Secure Sockets Layer (SSL)';
                                                break;
                                            default:
                                                echo 'None';
                                        }
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-username-row" style="margin-top:20px">
                            <td id="smtp-username-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-username">SMTP Username:</p>
                            </td>
                            <td id="smtp-username-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-username" name="smtp-username" value="<?php echo $current_smtp_username; ?>" required>
                            </td>
                            <td id="smtp-username-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-username-default"><?php echo $default_smtp_username; ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-password-row" style="margin-top:20px">
                            <td id="smtp-password-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-password">SMTP Password:</p>
                            </td>
                            <td id="smtp-password-input">
                                <input class="form-control nav-v-c" type="password" style="width: 250px" id="smtp-password" name="smtp-password" value="password" required>
                            </td>
                            <td id="smtp-password-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-password-default"><or class="green"><?php echo $default_smtp_password; ?></or></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-from-email-row" style="margin-top:20px">
                            <td id="smtp-from-email-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-from-email">SMTP From Email:</p>
                            </td>
                            <td id="smtp-from-email-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-email" name="smtp-from-email" value="<?php echo $current_smtp_from_email; ?>" required>
                            </td>
                            <td id="smtp-from-email-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-from-email-default"><?php echo $default_smtp_from_email; ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-from-name-row" style="margin-top:20px">
                            <td id="smtp-from-name-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-from-name">SMTP From Name:</p>
                            </td>
                            <td id="smtp-from-name-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-name" name="smtp-from-name" value="<?php echo $current_smtp_from_name; ?>" required>
                            </td>
                            <td id="smtp-from-name-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-from-name-default"><?php echo $default_smtp_from_name; ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" id="smtp-backup-to-row" style="margin-top:20px">
                            <td id="smtp-backup-to-label" style="width:250px;margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="smtp-backup-to">SMTP To Email (Backup):</p>
                            </td>
                            <td id="smtp-backup-to-input">
                                <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-backup-to" name="smtp-backup-to" value="<?php echo $current_smtp_to_email; ?>" required>
                            </td>
                            <td id="smtp-backup-to-default-cell" style="margin-left:25px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" id="smtp-backup-to-default"><?php echo $default_smtp_to_email; ?></p>
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

    <div id="modalDivResetPW" class="modal" style="display: none;">
        <span class="close" onclick="modalCloseResetPW()">×</span>
        <div class="container well-nopad bg-dark" style="padding:25px">
            <div style="margin:auto;text-align:center;margin-top:10px">
                <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
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
    <script> // MODAL SCRIPT
        // Get the modal
        function resetPassword(user_id) {
            var modal = document.getElementById("modalDivResetPW");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            modal.style.display = "block";
            var user_id_element = document.getElementById('modal-user-id');
            user_id_element.value = user_id;
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalCloseResetPW = function() { 
            var modal = document.getElementById("modalDivResetPW");
            modal.style.display = "none";
        }
    </script>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->
    
    
    <script>
    function testLDAP() {
        var ldap_username = $('#auth-username').val();
        var ldap_password = $('#auth-password').val();
        var ldap_password_confirm = $('#auth-password-confirm').val();
        var ldap_domain = $('#auth-domain').val();
        var ldap_host = $('#auth-host').val();
        var ldap_host_secondary = $('#auth-host-secondary').val();
        var ldap_port = $('#auth-port').val();
        var ldap_basedn = $('#auth-basedn').val();
        var ldap_usergroup = $('#auth-usergroup').val();
        var ldap_userfilter = $('#auth-userfilter').val();

        var ldapForm = document.getElementById("ldapForm");
        var outputPre = document.getElementById("ldapTestOutput");
        if (outputPre !== null) {
            outputPre.parentNode.removeChild(outputPre)
        }
        var newOutputPre = document.createElement("pre");
        newOutputPre.setAttribute("class", "well-nopad bg-dark");
        newOutputPre.setAttribute("id", "ldapTestOutput");
        newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
        ldapForm.parentNode.insertBefore(newOutputPre, ldapForm.nextSibling);

        $.ajax({
        type: "POST",
        url: "./includes/ldap-test.inc.php",
        data: {ldap_username: ldap_username, 
            ldap_password: ldap_password, 
            ldap_password_confirm: ldap_password_confirm, 
            ldap_domain: ldap_domain,
            ldap_host: ldap_host,
            ldap_host_secondary: ldap_host_secondary,
            ldap_port: ldap_port,
            ldap_basedn: ldap_basedn,
            ldap_usergroup: ldap_usergroup,
            ldap_userfilter: ldap_userfilter
        },
        dataType: "json",
        success: function(response){
            var userlist = response;
            var div = document.getElementById('ldapTestOutput');
            // console.log(response);
            if (Array.isArray(userlist)) {
                for (var i = 0; i < userlist.length; i++) {
                var user = userlist[i];
                // console.log(user);
                div.textContent += user+"\n";
                }
            } else {
                div.textContent += userlist+"\n";
            } 
        },
        async: false // <- this turns it into synchronous
        });
    }

    function testSMTP() {
        var smtpLoading = document.getElementById("smtp-loading-icon");
        var smtpSuccess = document.getElementById("smtp-success-icon");
        var smtpFail = document.getElementById("smtp-fail-icon");
        smtpLoading.style.display = "inline-block";
        smtpSuccess.style.display = "none";
        smtpFail.style.display = "none";

        var smtp_host = $('#smtp-host').val();
        var smtp_port = $('#smtp-port').val();
        var smtp_encryption = $('#smtp-encryption').val();
        var smtp_username = $('#smtp-username').val();
        var smtp_password = $('#smtp-password').val();
        var smtp_from_email = $('#smtp-from-email').val();
        var smtp_from_name = $('#smtp-from-name').val();
        var smtp_to_email = $('#smtp-backup-to').val();

        var smtpForm = document.getElementById("smtpForm");
        var outputPre = document.getElementById("smtpTestOutput");
        if (outputPre !== null) {
            outputPre.parentNode.removeChild(outputPre)
        }
        var newOutputPre = document.createElement("pre");
        newOutputPre.setAttribute("class", "well-nopad bg-dark");
        newOutputPre.setAttribute("id", "smtpTestOutput");
        newOutputPre.setAttribute("style", "color:white;margin-bottom:50px");
        smtpForm.parentNode.insertBefore(newOutputPre, smtpForm.nextSibling);

        $.ajax({
            type: "POST",
            url: "./includes/smtp-test.inc.php",
            data: {
                smtp_host: smtp_host,
                smtp_port: smtp_port,
                smtp_encryption: smtp_encryption,
                smtp_username: smtp_username,
                smtp_password: smtp_password,
                smtp_from_email: smtp_from_email,
                smtp_from_name: smtp_from_name,
                smtp_to_email: smtp_to_email
            },
            dataType: "html",
            success: function(response) {
                var result = response;
                var div = document.getElementById('smtpTestOutput');

                div.textContent += result + "\n";

                // Continue with the rest of the code once the AJAX request is complete
                processLastLine();
                newOutputPre.scrollIntoView();
            },
            async: true
        });

        function processLastLine() {
            var div = document.getElementById('smtpTestOutput');

            // Get the content of the <pre> element
            var divContent = div.textContent || div.innerText;
            // Split the content into an array of lines
            var lines = divContent.trim().split('\n');
            // Get the last line
            var lastLine = lines[lines.length - 1];
            // Check if the last line starts with "221"
            if (lastLine.startsWith('221')) {
                // Show some text on the screen or perform any desired action
                console.log('SMTP success code 221 found');
                smtpLoading.style.display = "none";
                smtpSuccess.style.display = "inline";
                smtpFail.style.display = "none";
            } else {
                console.log('SMTP error');
                smtpLoading.style.display = "none";
                smtpSuccess.style.display = "none";
                smtpFail.style.display = "inline";
            }
        }
    }

    </script>
    <script> // color-picker box json - for Admin.php
        $("input.color").each(function() {
            var that = this;
            $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
                that.type = (that.type == "color") ? "text" : "color";
            }));
        }).change(function() {
            $(this).attr("data-value", this.value);
            this.type = "text";
        });
    </script>

    <script> // script for users modifications
        function userRoleChange(id) {
            var select = document.getElementById("user_"+id+"_role_select");
            var selectedValue = select.value;

            $.ajax({
                type: "POST",
                url: "./includes/admin.inc.php",
                data: {
                    user_id: id,
                    user_new_role: selectedValue,
                    user_role_submit: 'yes'
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
                    user_enabled_submit: 'yes'
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
        // Function to extract the anchor and split it before the first hyphen
        function extractParamsFromAnchor(anchor) {
            const params = anchor.split('-');
            return {
                param1: anchor,
                param2: params[0],
            };
        }

        // Check for anchors in the URL and call toggleSection function if present
        window.onload = function () {
            const anchor = window.location.hash.substring(1); // Remove the leading '#'
            if (anchor) {
                const { param1, param2 } = extractParamsFromAnchor(anchor);
                // console.log(param1);
                // console.log(param2);
                toggleSection(document.getElementById(param1), param2);

                // Scroll to the anchor ID after the toggleSection function is done
                const anchorElement = document.getElementById(anchor);
                if (anchorElement) {
                    anchorElement.scrollIntoView({ behavior: 'smooth' });
                }
            } else {
                toggleSection(document.getElementById("global-settings"), "global");

            }
        };

        
        // LDAP TOGGLE ENABLE STUFF

        // Get the initial state of the LDAP enable toggle checkbox
        let isLdapCheckboxChecked = document.getElementById("ldap-enabled-toggle").checked;

        // Add an event listener to the checkbox
        document.getElementById("ldap-enabled-toggle").addEventListener("change", function (event) {
            // Check if the checkbox is being unchecked
            const isUncheck = !this.checked;

            // If the checkbox is being unchecked, display the confirmation popup
            if (isUncheck) {
                const confirmed = confirm(
                    'Disabling LDAP will force local user login.\nMake sure you have a local user available.\nAre you sure you want to do this?'
                );

                // If the user cancels, revert the checkbox back to its previous state
                if (!confirmed) {
                    this.checked = true; // Revert the checkbox back to checked state
                    return;
                }
            }

            // Update the initial state of the checkbox for the next change event
            isLdapCheckboxChecked = this.checked;

            // If the checkbox is not being unchecked or the user confirmed, submit the form
            document.getElementById("ldapToggleForm").submit();
        });

        // SMTP TOGGLE ENABLE STUFF

        // Get the initial state of the SMTP enable toggle checkbox
        let isSmtpCheckboxChecked = document.getElementById("smtp-enabled-toggle").checked;

        // Add an event listener to the checkbox
        document.getElementById("smtp-enabled-toggle").addEventListener("change", function (event) {
            // Check if the checkbox is being unchecked
            const isUncheck = !this.checked;

            // If the checkbox is being unchecked, display the confirmation popup
            if (isUncheck) {
                const confirmed = confirm(
                    'Disabling SMTP will stop ALL email notifications.\nAre you sure you want to do this?'
                );

                // If the user cancels, revert the checkbox back to its previous state
                if (!confirmed) {
                    this.checked = true; // Revert the checkbox back to checked state
                    return;
                }
            }

            // Update the initial state of the checkbox for the next change event
            isSmtpCheckboxChecked = this.checked;

            // If the checkbox is not being unchecked or the user confirmed, submit the form
            document.getElementById("smtpToggleForm").submit();
        });

    </script>

    <script> // MODAL SCRIPT
        // Get the modal
        function modalLoadAdd(site_id) {
            //get the modal div with the property
            var modal = document.getElementById("modalDivAdd");
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalCloseAdd = function() { 
            var modal = document.getElementById("modalDivAdd");
            modal.style.display = "none";
        }

    </script>

    <script>
        function populateSites(field, current_site) {
            // Make an AJAX request to retrieve the corresponding sites

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "includes/stock-selectboxes.inc.php?getsites=1", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the response and populate the shelf select box
                    var sites = JSON.parse(xhr.responseText);
                    var select = field;
                    select.options.length = 0;
                    select.options[0] = new Option("Select Site", "");
                    select.options[0].disabled = true;
                    for (var i = 0; i < sites.length; i++) {
                        select.options[select.options.length] = new Option(sites[i].name, sites[i].id);
                    }
                    select.disabled = (select.options.length === 1);
                    for (var i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === current_site) {
                            select.options[i].selected = true;
                        }
                    }
                }
            };
            xhr.send();
        }
        function populateAreas(field, current_site, current_area) {
            // Make an AJAX request to retrieve the corresponding areas

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "includes/stock-selectboxes.inc.php?site=" + current_site, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the response and populate the shelf select box
                    var areas = JSON.parse(xhr.responseText);
                    var select = field;
                    select.options.length = 0;
                    select.options[0] = new Option("Select Area", "");
                    select.options[0].disabled = true;
                    for (var i = 0; i < areas.length; i++) {
                        select.options[select.options.length] = new Option(areas[i].name, areas[i].id);
                    }
                    select.disabled = (select.options.length === 1);
                    for (var i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === current_area) {
                            select.options[i].selected = true;
                        }
                    }
                }
            };
            xhr.send();
        }
        function populateAreasUpdate() {
            // Get the selected site
            var site = document.getElementById("location-parent-site-input").value;
            var type = document.getElementById("location-type-input").value;
            if (type === "shelf") {
                // Make an AJAX request to retrieve the corresponding areas
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "includes/stock-selectboxes.inc.php?site=" + site, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Parse the response and populate the area select box
                        var areas = JSON.parse(xhr.responseText);
                        var select = document.getElementById("location-parent-area-input");
                        select.options.length = 0;
                        select.options[0] = new Option("Select Area", "");
                        select.options[0].disabled = true;
                        for (var i = 0; i < areas.length; i++) {
                            select.options[select.options.length] = new Option(areas[i].name, areas[i].id);
                        }
                        select.disabled = (select.options.length === 1);
                    }
                };
                xhr.send();
            }
        }
        document.getElementById("location-parent-site-input").addEventListener("change", populateAreasUpdate);
    </script>

    <script> // MODAL SCRIPT
        // Get the modal
        function modalLoadEdit(id, type) {
            //get the modal div with the property
            var modal = document.getElementById("modalDivEdit");
            modal.style.display = "block";

            var input_parent_site = document.getElementById('location-parent-site-input');
            var input_parent_area = document.getElementById('location-parent-area-input');
            var input_parent_site_tr = document.getElementById('location-parent-site-tr');
            var input_parent_area_tr = document.getElementById('location-parent-area-tr');
            var input_parent_site_th = document.getElementById('location-parent-site-th');
            var input_parent_area_th = document.getElementById('location-parent-area-th');

            var input_type = document.getElementById('location-type-input');
            var text_type = document.getElementById('location-type-text');

            var input_id = document.getElementById('location-id-input');
            var text_id = document.getElementById('location-id-text');

            var input_name = document.getElementById('location-name-input');

            var input_description_tr = document.getElementById('location-description-tr');
            var input_description = document.getElementById('location-description-input');

            // input_parent.value = '';
            // input_parent_site.value = '';
            input_parent_area.options.length = 0;
            input_parent_site.options.length = 0;
            input_parent_area_tr.hidden=true;
            input_parent_site_tr.hidden=true;
            input_description_tr.hidden=true;
            
            if (type !== "site") {
                if (type == "area") {
                    input_parent_site_tr.hidden=false;
                    populateSites(input_parent_site, document.getElementById(type+'-'+id+'-parent').value);

                } 
                if (type == "shelf") {
                    input_parent_area_tr.hidden=false;
                    input_parent_site_tr.hidden=false;
                    populateSites(input_parent_site, document.getElementById(type+'-'+id+'-site').value);
                    populateAreas(input_parent_area, document.getElementById(type+'-'+id+'-site').value, document.getElementById(type+'-'+id+'-parent').value);
                }
            } 

            input_type.value = type;
            if (type.length > 0) {
                type_cap = type.charAt(0).toUpperCase() + type.slice(1);
            }
            text_type.textContent = type_cap;

            input_id.value = document.getElementById(type+'-'+id+'-id').value;
            text_id.textContent = document.getElementById(type+'-'+id+'-id').value;

            input_name.value = document.getElementById(type+'-'+id+'-name').value;

            if (type !== "shelf") {
                input_description_tr.hidden=false;
                input_description.value = document.getElementById(type+'-'+id+'-description').value;
            }
            
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalCloseEdit = function() { 
            var modal = document.getElementById("modalDivEdit");
            modal.style.display = "none";

            var input_parent_site = document.getElementById('location-parent-site-input');
            var input_parent_area = document.getElementById('location-parent-area-input');
            var input_parent_site_tr = document.getElementById('location-parent-site-tr');
            var input_parent_area_tr = document.getElementById('location-parent-area-tr');
            var input_parent_site_th = document.getElementById('location-parent-site-th');
            var input_parent_area_th = document.getElementById('location-parent-area-th');

            var input_type = document.getElementById('location-type-input');
            var text_type = document.getElementById('location-type-text');

            var input_id = document.getElementById('location-id-input');
            var text_id = document.getElementById('location-id-text');

            var input_name = document.getElementById('location-name-input');

            var input_description_tr = document.getElementById('location-description-tr');
            var input_description = document.getElementById('location-description-input');

            input_parent_area.value = '';
            input_parent_site.value = '';
            input_parent_area.options.length = 0;
            input_parent_site.options.length = 0;
            input_parent_area_tr.hidden=true;
            input_parent_site_tr.hidden=true;

            input_type.value = '';
            text_type.textContent = '';

            input_id.value = '';
            text_id.textContent = '';

            input_name.value = '';

            input_description_tr.hidden=true;
            input_description.value = '';
        }

    </script>

    <script> // show input for the ShowADD section
        function showInput() {
            var type = document.getElementById("addLocation-type");
            var selectedType = type.options[type.selectedIndex].value;

            var inputContainers = document.getElementsByClassName("specialInput");
            for (var i = 0; i < inputContainers.length; i++) {
                inputContainers[i].hidden = true;
                inputContainers[i].value = '';
            }

            var modifyContainers = document.getElementsByClassName(selectedType);
            for (var i = 0; i < modifyContainers.length; i++) {
                modifyContainers[i].hidden = false;
            }
        }
    </script>

    <script>
        function populateParent() {
        // Get the selected type
        var type = document.getElementById("addLocation-type").value;
        
        // Make an AJAX request to retrieve the corresponding parents
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/stock-selectboxes.inc.php?type=" + type, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the area select box
                var select = document.getElementById("addLocation-parent");
                    select.options.length = 0;
                    select.options[0] = new Option("Select Parent", "");
                if (xhr.responseText !== '') {
                    var parents = JSON.parse(xhr.responseText);
                    for (var i = 0; i < parents.length; i++) {
                        select.options[select.options.length] = new Option(parents[i].name, parents[i].id);
                    }
                    
                } 
                select.disabled = (select.options.length === 1);
            }
            
        };
        xhr.send();
        }
        document.getElementById("addLocation-type").addEventListener("change", populateParent);
    </script>

    <script>
        function showImageLinks(num) {
            var button = document.getElementById('image-'+num+'-links');
            var linksRow = document.getElementById('image-row-'+num+'-links');

            if (linksRow.hidden === true) {
                button.className = "btn btn-dark";
                button.innerText = "Hide Links";
                linksRow.hidden = false;
            } else {
                button.className = "btn btn-warning";
                button.innerText = "Show Links";
                linksRow.hidden = true;
            }
        }
    </script>

    
<?php include 'foot.php'; ?>


</body>