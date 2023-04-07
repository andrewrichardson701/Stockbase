<div class="nav inv-nav">
    <div id="nav-row" class="nav-row">
        <div class="logo-div">
            <a href="./">
                <img class="logo" src="assets/img/config/<?php echo($config_logo_image); ?>" />
            </a>
        </div>
        <?php
        if (isset($show_inventory)) {
            if ($show_inventory == 1) {
                $site = isset($_GET['site']) ? $_GET['site'] : "0";
                $area = isset($_GET['area']) ? $_GET['area'] : "0";
                $name = isset($_GET['name']) ? $_GET['name'] : "";
                $sku = isset($_GET['sku']) ? $_GET['sku'] : "";
                $location = isset($_GET['shelf']) ? $_GET['location'] : "";
                $shelf = isset($_GET['shelf']) ? $_GET['shelf'] : "";
                $area_names_array = [];
                
                include 'includes/dbh.inc.php';

                $sql_site = "SELECT DISTINCT site.id, site.name, site.description
                            FROM site 
                            ORDER BY site.id";
                $stmt_site = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_site);
                    $result_site = mysqli_stmt_get_result($stmt_site);
                    $rowCount_site = $result_site->num_rows;
                    if ($rowCount_site < 1) {
                        echo ("No sites found");
                        exit();
                    } else {
                        echo ('
                        <div id="site-dropdown-div" class="nav-div">
                            <select id="site-dropdown" name="site" class="nav-trans form-control nav-v-c cw" style="margin:0" onchange="siteChange(\'site-dropdown\')">
                            <option value="0"'); if ($area == 0) { echo('selected'); } echo('>All</option>
                        ');
                        while( $row = $result_site->fetch_assoc() ) {
                            $site_id = $row['id'];
                            $site_name = $row['name'];
                            $site_description = $row['description'];
                            echo('<option value="'.$site_id.'"'); if ($site == $site_id) { echo('selected'); } echo('>'.$site_name.'</option>');
                        }
                        echo('
                            </select>
                        </div>
                        ');
                        $sql_area = "SELECT DISTINCT area.id, area.name, area.description, area.site_id
                                    FROM area 
                                    INNER JOIN site ON site.id=area.site_id
                                    WHERE site.id=?
                                    ORDER BY area.id";
                        $stmt_area = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_area, $sql_area)) {
                            echo("ERROR getting entries");
                        } else {
                            mysqli_stmt_bind_param($stmt_area, "s", $site);
                            mysqli_stmt_execute($stmt_area);
                            $result_area = mysqli_stmt_get_result($stmt_area);
                            $rowCount_area = $result_area->num_rows;
                            if ($rowCount_area < 1) {
                                // echo ("No areas found");
                                // exit();
                            } else {
                                echo ('
                                <div id="area-dropdown-div" class="nav-div">
                                    <select id="area-dropdown" name="area" class="nav-trans form-control nav-v-c cw" style="margin:0" onchange="areaChange(\'area-dropdown\')">
                                    <option value="0"'); if ($area == 0) { echo('selected'); } echo('>All</option>
                                ');
                                while( $row = $result_area->fetch_assoc() ) {
                                    $area_id = $row['id'];
                                    $area_name = $row['name'];
                                    $area_description = $row['description'];
                                    $area_names_array[$area_id] = $area_name;
                                    echo('<option value="'.$area_id.'"'); if ($area == $area_id) { echo('selected'); } echo('>'.$area_name.'</option>');
                                }
                                echo($area);
                                echo('
                                    </select>
                                </div>
                                ');
                            }
                        }
            
                    }
                }
            }
        }
        
        ?>
        <?php
        $nav_right_set = 0;
        $nav_right = 'nav-right';

        if (isset($profile_name)) {
            echo('
            <div id="profile-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                <button id="profile" class="nav-v-c nav-trans cw" onclick="window.location=\'./profile.php\';">'.$profile_name.'</button>
            </div> 
            ');
        }
        if (isset($loggedin_role)) {
            if ($loggedin_role == "admin") {
                echo('
                <div id="admin-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <button id="admin" class="nav-v-c nav-trans cw" onclick="window.location=\'./admin.php\';">Admin</button>
                </div> 
                ');
            }
        }
        if (isset($profile_name)) {
            echo ('
                <div id="logout-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo('nav-div">
                    <button id="logout" class="nav-v-c nav-trans cw" onclick="window.location=\'./logout.php\';">Logout</button>
                </div> 
            ');
        }
        
        ?>
    </div>
</div>