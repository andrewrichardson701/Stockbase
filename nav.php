<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.
?>

<!-- Navigation Bar for the top of the page, using the config settings for logo and colour -->
<a href="./" class="nav-head" style="color:<?php echo($current_banner_text_color); ?> !important"><?php echo($current_system_name); ?></a>
<header class="nav inv-nav">
    <div id="nav-row" class="nav-row viewport-large">
        <div class="logo-div">
            <a href="./">
                <img class="logo" src="assets/img/config/<?php echo($current_logo_image); ?>" />
            </a>
        </div>
        <?php 
        if ((isset($_SESSION['username'])) && ($_SESSION['username'] !== '')) {
            echo('
            <div id="add-div" class="nav-div" style="margin-right:5px">
                <button id="add-stock" class="btn btn-success cw nav-v-c btn-nav" style="opacity:90%" onclick="navPage(updateQueryParameter(\'stock.php\', \'modify\', \'add\'))">
                    <i class="fa fa-plus"></i> Add 
                </button>
            </div> 
            <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                <button id="remove-stock" class="btn btn-danger cw nav-v-c btn-nav" onclick="navPage(updateQueryParameter(\'stock.php\', \'modify\', \'remove\'))">
                    <i class="fa fa-minus"></i> Remove 
                </button>
            </div>
            <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                <button id="transfer-stock" class="btn btn-warning nav-v-c btn-nav" style="color:black" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'move\'))">
                    <i class="fa fa-arrows-h"></i> Move 
                </button>
            </div>
            ');
            
            $nav_right_set = 0;

            if (isset($_SESSION['impersonate'])) {
                $impersonate = $_SESSION['impersonate'];
                if ($impersonate == 1) {
                    echo('
                    <div id="impersonate-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                        <form enctype="multipart/form-data" class="nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0;padding:0">
                            <input type="hidden" name="user-stop-impersonate" value="1"/>
                            <button type="submit" id="impersonate" style="border-radius: 8px;padding-left:10px;padding-right:10px;margin-top:2.5%;height:80%;color:'.getWorB(getComplement($current_banner_color)).';background-color:'.getComplement($current_banner_color).' !important;margin-bottom:10%" onclick="window.location=\'./admin.php\';">Stop Impersonating</button>
                        </form>
                    </div> 
                    ');
                }
            }
            if (isset($profile_name)) {
                echo('
                <div id="profile-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <button id="profile" class="nav-v-c nav-trans" style="height:100%;color:'.$current_banner_text_color.'" onclick="window.location=\'./profile.php\';">'.$profile_name.'</button>
                </div> 
                ');
            }
            if (isset($loggedin_role)) {
                if (in_array($loggedin_role, $config_admin_roles_array)) {
                    echo('
                    <div id="admin-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                        <button id="admin" class="nav-v-c nav-trans" style="height:100%;color:'.$current_banner_text_color.'" onclick="window.location=\'./admin.php\';">Admin</button>
                    </div> 
                    ');
                }
            }
            if (isset($profile_name)) {
                echo ('
                    <div id="logout-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                        <button id="logout" class="nav-v-c nav-trans" style="height:100%;color:'.$current_banner_text_color.'" onclick="window.location=\'./logout.php\';">Logout</button>
                    </div> 
                ');
            }
        }
        
        ?>
    </div>

    <div id="nav-row" class="nav-row viewport-small">
        <div class="logo-div">
            <a href="./">
                <img class="logo" src="assets/img/config/<?php echo($current_logo_image); ?>" />
            </a>
        </div>
        
        <?php
            $nav_right = 0;
            echo('<div id="impersonate-div" class="'); if($nav_right == 0) { echo('nav-right'); $nav_right = 1; } echo(' nav-div" style="margin-right:0">
                    <form enctype="multipart/form-data" class="nav-v-c nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0;padding:0">
                        <input type="hidden" name="user-stop-impersonate" value="1"/>
                        <button type="submit" style="border-radius: 8px; height:90%;color:'.getWorB(getComplement($current_banner_color)).';background-color:'.getComplement($current_banner_color).' !important; onclick="window.location=\'./admin.php\';">Stop <i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
                    </form>
                </div>');
            echo('<div class="nav-div '); if($nav_right == 0) { echo('nav-right'); $nav_right = 1; } echo('">');
            if (isset($profile_name)) { 
                echo('
                    <ul class="nav-links">
                        <li style="color:'.getWorB(getComplement($current_banner_color)).';background-color:'.getComplement($current_banner_color).' !important"<a href="/">Stop </a></li>
                        <li><a href="./profile.php">'.$profile_name.'</a></li>');
                        // if (isset($loggedin_role)) {
                        //     if (in_array($loggedin_role, $config_admin_roles_array)) {
                        //         echo('<li><a href="./admin.php">Admin</a></li>');
                        //     }
                        // }
                        echo('<li><a href="./logout.php">Logout</a></li>
                    </ul>
                    ');
            }
            echo('
                    <div class="burger-menu nav-v-c theme-burger" style="color:'.$current_banner_text_color.' !important" '); if(!isset($profile_name)) { echo ('hidden'); } echo('><i class="fa-solid fa-bars"></i></div>
                </div>
            ');
        ?>
    </div>
</header>
<?php
if ((isset($_SESSION['username'])) && ($_SESSION['username'] !== '')) {
?>
<header class="nav inv-nav-secondary viewport-small">
    <table class="centertable">
        <tbody>
            <tr>
                <td>
                    <div style="margin-right:5vw">
                        <button id="add-stock" class="btn btn-success cw nav-v-b btn-nav scale_1-15" style="width:80px;opacity:90%" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'add'))">
                            <i class="fa fa-plus"></i> Add 
                        </button>
                    </div>
                </td>
                <td>
                    <div>
                        <button id="remove-stock" class="btn btn-danger cw btn-nav scale_1-15" style="width:80px;" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'remove'))">
                            <i class="fa fa-minus"></i> Remove 
                        </button>
                    </div>
                </td>
                <td>
                    <div style="margin-left:5vw">
                        <button id="transfer-stock" class="btn btn-warning btn-nav scale_1-15" style="width:80px;color:black" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'move'))">
                            <i class="fa fa-arrows-h"></i> Move 
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</header>
<?php
}
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const burgerMenu = document.querySelector('.burger-menu');
    const navLinks = document.querySelector('.nav-links');

    burgerMenu.addEventListener('click', function () {
        navLinks.classList.toggle('show');
        if (burgerMenu.innerHTML == '<i class="fa-solid fa-bars"></i>') {
            burgerMenu.innerHTML = '<i class="fa-solid fa-bars fa-rotate-90"></i>';
        } else {
            burgerMenu.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
    });
});
</script>
