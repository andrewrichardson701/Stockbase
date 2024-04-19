<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// THEME TEST PAGE
// EVERYTHING HERE IS HARD CODED IN SO IF THE FORMAT OF THINGS CHANGE, THIS WILL LIKELY NEED TO CHANGE TOO.
include 'session.php'; // Session setup and redirect if the session is not active 
include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings.
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>
        <?php echo ucwords($current_system_name); ?> - Theme Test
    </title>
</head>

<body id="body">
    <style id="style">
    </style>

    <div class="content" id="content">
        <?php // dependency PHP
        
        ?>

        <!-- Header and Nav -->
        <?php include 'nav.php'; ?>
        <!-- End of Header and Nav -->

        <div class="container text-center">
            <h2 class="header-small">Theme Test</h2>
        </div>
        <?php

        include 'includes/dbh.inc.php';

        $errorPprefix = '<tr><td colspan=100% class="red">Error: ';
        $errorPsuffix = '</td></tr>';
        $successPprefix = '<tr><td colspan=100% class="green">';
        $successPsuffix = '</td></tr>';
        $errorPtext = '';
        $sqlerrorPtext = '';
        $successPtext = '';

        showResponse();
        ?>
        <table class="centertable" style="margin-bottom:20px">
            <thead>
                <tr>
                    <td class="theme-textColor" style="padding-right:20px">Select Theme:</td>
                    <td>
                        <input type="hidden" id="profile-id" value="<?php echo ($_SESSION['user_id']); ?>" />
                        <select class="form-control" name="theme" id="theme-select" onchange="changeTheme()">
                            <?php
                            $sql_theme = "SELECT * FROM theme";
                            $stmt_theme = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
                                echo ("ERROR getting entries");
                            } else {
                                mysqli_stmt_execute($stmt_theme);
                                $result_theme = mysqli_stmt_get_result($stmt_theme);
                                $rowCount_theme = $result_theme->num_rows;
                                if ($rowCount_theme < 1) {
                                    echo ("No themes found.");
                                } else {
                                    while ($row_theme = $result_theme->fetch_assoc()) {
                                        $theme_id = $row_theme['id'];
                                        $theme_name = $row_theme['name'];
                                        $theme_file_name = $row_theme['file_name'];
                                        echo ('<option id="theme-select-option-' . $theme_id . '" title="' . $theme_file_name . '" alt="' . $theme_name . '" value="' . $theme_id . '" ');
                                        if ($loggedin_theme_id == $theme_id) {
                                            echo ('selected');
                                        }
                                        echo ('>' . $theme_name);
                                        if ($current_default_theme_id == $theme_id) {
                                            echo (' (default)');
                                        }
                                        echo ('</option>');
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </thead>
        </table>

        <table class="centertable">
            <thead>
                <tr class="text-center">
                    <th>CSS</th>
                    <th style="margin-right:20px">Styles used</th>
                    <th>Sample</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td id="css" class="align-top" rowspan=100%>
                        <table class="centertable" style="margin-bottom:10px;margin-top:10px">
                            <tbody>
                                <tr>
                                    <td style="padding-right:10px">
                                        Colour picker:
                                    </td>
                                    <td>
                                        <label class="tag-color">
                                            <input class="form-control input-color color" placeholder="#XXXXXX" data-value="#xxxxxx" value="<?php echo($current_banner_color); ?>"/>
                                        </label>
                                    </td>
                                <tr>
                            </tbody>
                        </table>
                        <textarea id="css-editor" class="uni theme-divBg theme-textColor" style="height:2000px;width:500px;font-size:14px" spellcheck="false"></textarea><br>
                        <button id="apply-button" class="btn btn-warning" style="margin-top:20px">Apply CSS</button>
                        <hr style="border-color:white">
                        <h5 style="margin-bottom:15px">Download theme</h5>
                        <table class="centertable">
                            <tbody>
                                <tr>
                                    <td style="padding-right:10px">
                                        Theme Name:
                                    </td>
                                    <td>
                                        <input id="download-theme-name" name="download-theme-name" placeholder="theme-name" type="text" class="form-control" style="width:150px;margin-right:5px"" required />
                                    </td>
                                    <td>
                                        <button id="download-theme" class="btn btn-success" onclick="downloadCSS()" style="margin-left:5px">Download CSS</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr style="border-color:white">
                        <h5 style="margin-bottom:15px">Upload new theme</h5>
                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                            <!-- Include CSRF token in the form -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="theme-upload" value="1"/>
                            <table class="centertable">
                                <tbody>
                                    <tr>
                                        <td style="padding-right:10px">
                                            Theme Name:
                                        </td>
                                        <td>
                                            <input id="theme-name" name="theme-name" placeholder="Theme name" type="text" class="form-control" style="width:250px;margin-right:5px"" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right:10px">
                                            <p class="title" title="Do not include the '.css'" >File Name:</p>
                                        </td>
                                        <td>
                                            <input id="theme-file-name" name="theme-file-name" placeholder="theme-custom" type="text" class="form-control" style="width:250px;margin-right:5px"" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            CSS File:
                                        </td>
                                        <td class="text-center">
                                            <input class="" type="file" style="width: 250px" id="upload-css-file" name="css-file">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="text-center">
                                            <input type="submit" id="upload-theme" class="btn btn-success" style="margin-top:10px" name="submit" value="Upload Theme" />
                                        </td>
                                    </tr>
                                    <?php 
                                    if ($errorPtext !== '') {
                                        echo $errorPprefix.$errorPtext.$errorPsuffix;
                                    }
                                    if ($sqlerrorPtext !== '') {
                                        echo $errorPprefix.$sqlerrorPtext.$errorPsuffix;
                                    }
                                    if ($successPtext !== '') {
                                        echo $successPprefix.$successPtext.$successPsuffix;
                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </form>
                    </td>
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>.theme-table<br>
                            .theme-tableOuter<br>
                            .theme-textColor<br>
                            .theme-dropdown<br>
                            .theme-dropdown option<br>
                            .clickable<br>
                            .highlight<br>
                            .gold, a<br>
                            .pageSelected
                        </p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Index</u></h4><hr style="border-color:white;margin-top:5px">
                            <div class="nav-row" style="padding-left:75px;padding-bottom:10px">
                                <span id="search-input-site-span" style="margin-right: 10px; padding-left:12px">
                                    <label for="search-input-site">Site</label><br>
                                    <select id="site-dropdown" name="site" class="form-control nav-v-b theme-dropdown">
                                        <option value="0" selected="">All</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                    </select>
                                </span>

                                <span id="search-input-name-span" style="margin-right: 10px;margin-left:10px">
                                    <label for="search-input-name">Name</label><br>
                                    <input id="search-input-name" type="text" name="name" class="form-control"
                                        style="width:160px;display:inline-block" placeholder="Search by Name" value="">
                                </span>
                                <span id="search-input-manufacturer-span" style="margin-right: 10px">
                                    <label for="search-input-manufacturer">Manufacturer</label><br>

                                    <select id="search-input-manufacturer" name="manufacturer" class="form-control"
                                        style="width:160px;display:inline-block" placeholder="Search by Manufacturer">
                                        <option value="" selected="">All</option>
                                        <option value="Cisco">Cisco</option>
                                        <option value="Dell">Dell</option>
                                        <option value="HP">HP</option>
                                    </select>
                                </span>
                                <input type="submit" value="submit" hidden="">
                                <div id="clear-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                    <button id="clear-filters" class="btn btn-warning nav-v-b"
                                        style="opacity:80%;color:black;padding:6px 6px 6px 6px">
                                        <i class="fa fa-ban fa-rotate-90" style="height:24px;padding-top:4px"></i>
                                    </button>
                                </div>
                                <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px">
                                    <button id="zerostock" class="btn btn-success nav-v-b"
                                        style="opacity:90%;color:black;padding:0px 2px 0px 2px">
                                        <span>
                                            <p style="margin:0px;padding:0px;font-size:12px"><i class="fa fa-plus"></i> Show</p>
                                            <p style="margin:0px;padding:0px;font-size:12px">0 Stock</p>
                                        </span>
                                    </button>
                                </div>
                                <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px">
                                    <button id="zerostock" class="btn btn-danger nav-v-b"
                                        style="opacity:80%;color:black;padding:0px 2px 0px 2px">
                                        <span>
                                            <p style="margin:0px;padding:0px;font-size:12px"><i class="fa fa-minus"></i> Hide</p>
                                            <p style="margin:0px;padding:0px;font-size:12px">0 Stock</p>
                                        </span>
                                    </button>
                                </div>
                                <div id="zero-div" class="nav-div" style="margin-left:15px;margin-right:0px">
                                    <button id="cable-stock" class="btn btn-dark nav-v-b"
                                        style="opacity:90%;color:white;padding:6px 6px 6px 6px">
                                        Fixed Cables
                                    </button>
                                </div>
                            </div>
                            <table class="table table-dark theme-table centertable" id="inventoryTable"
                                style="margin-bottom:0px">
                                <thead style="text-align: center; white-space: nowrap;">
                                    <tr class="theme-tableOuter">
                                        <th id="id" hidden="">id</th>
                                        <th id="img"></th>
                                        </th>
                                        <th class="clickable sorting sorting-asc">Name</th>
                                        <th class="clickable sorting" id="sku">SKU</th>
                                        <th class="clickable sorting" id="quantity">Quantity</th>
                                        <th class="clickable sorting" id="site">Site</th>
                                        <th id="lables">Tags</th>
                                        <th id="location">Location(s)</th>
                                    </tr>
                                </thead>
                                <tbody id="inv-body" class="align-middle" style="text-align: center; white-space: nowrap;">
                                    <tr class="vertical-align align-middle highlight" id="1">
                                        <td class="align-middle" id="1-id" hidden="">1</td>
                                        <td class="align-middle" id="1-img-td">
                                            <img id="1-img" class="inv-img-main thumb"
                                                src="./assets/img/config/<?php echo($current_favicon_image); ?>" alt="Cisco C3650X">
                                        </td>
                                        <td class="align-middle link gold" id="1-name">Cisco C3650X</td>
                                        <td class="align-middle" id="1-sku">STOCK-00001</td>
                                        <td class="align-middle" id="1-quantity">140</td>
                                        <td class="align-middle link gold" id="1-site">Site 1</td>
                                        <td class="align-middle" id="1-label">
                                            <or class="gold link">cisco</or>, <or class="gold link">switch</or>
                                        </td>
                                        <td class="align-middle" id="1-location">Store room 1, Store room 2</td>
                                    </tr>
                                    <tr class="vertical-align align-middle highlight" id="1">
                                        <td class="align-middle" id="1-id" hidden="">1</td>
                                        <td class="align-middle" id="1-img-td">
                                            <img id="1-img" class="inv-img-main thumb"
                                                src="./assets/img/config/<?php echo($current_favicon_image); ?>" alt="Cisco C3650X">
                                        </td>
                                        <td class="align-middle link gold" id="1-name">Cisco C3650X</td>
                                        <td class="align-middle" id="1-sku">STOCK-00001</td>
                                        <td class="align-middle" id="1-quantity">18</td>
                                        <td class="align-middle link gold" id="1-site">Site 2</td>
                                        <td class="align-middle" id="1-label">
                                            <or class="gold link">cisco</or>, <or class="gold link">switch</or>
                                        </td>
                                        <td class="align-middle" id="1-location">Store room A</td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr class="theme-tableOuter">
                                        <td colspan="100%" style="margin:0px;padding:0px">
                                            <div class="row">
                                                <div class="col text-center"></div>
                                                <div id="inv-page-numbers" class="col-6 text-center align-middle"
                                                    style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                                                    <span class="current-page pageSelected"
                                                        style="padding-right:2px;padding-left:2px">1</span>
                                                    <or class="gold clickable" style="padding-right:2px;padding-left:2px">2
                                                    </or>
                                                    <or class="gold clickable" style="padding-left:2px">&gt;</or>
                                                </div>
                                                <div class="col text-center">
                                                    <table style="margin-left:auto; margin-right:20px">
                                                        <tbody>
                                                            <tr>
                                                                <td class="theme-textColor align-middle"
                                                                    style="border:none;padding-top:4px;padding-bottom:4px">
                                                                    Rows:
                                                                </td>
                                                                <td class="align-middle"
                                                                    style="border:none;padding-top:4px;padding-bottom:4px">
                                                                    <select id="tableRowCount" class="form-control"
                                                                        style="width:50px;height:25px; padding:0px"
                                                                        name="rows">
                                                                        <option id="rows-10" value="10" selected="">10
                                                                        </option>
                                                                        <option id="rows-50" value="50">50</option>
                                                                        <option id="rows-100" value="100">100</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr id="template" class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>
                        .theme-th-selected<br>
                        .theme-table<br>
                        .theme-tableOuter<br>
                        .gold, a<br>
                        </p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Cablestock</u></h4><hr style="border-color:white;margin-top:5px">
                            <table class="table table-dark theme-table centertable" id="cableSelection" style="border:0px !important">
                    <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border:0px !important">
                        <tr style="border:0px !important">
                            <th class="clickable theme-th-selected">Copper</th>
                            <th class="clickable th-noBorder">Fibre</th>
                            <th class="clickable th-noBorder">Power</th>
                            <th class="clickable th-noBorder">Other</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="theme-th-selected">
                                <table class="table table-dark theme-table centertable" id="inventoryTable">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr>
                                            <th id="stock-id" hidden="">Stock ID</th>
                                            <th id="item-id" hidden="">Item ID</th>
                                            <th id="image"></th>
                                            <th class="clickable sorting sorting-asc" id="name">Name</th>
                                            <th id="type-id" hidden="">Type ID</th>
                                            <th class="clickable sorting" id="type">Type</th>
                                            <th class="clickable sorting" id="site-name">Site</th>
                                            <th class="clickable sorting" id="quantity">Quantity</th>
                                            <th id="min-stock" style="color:#8f8f8f">Min. stock</th>
                                            <th style="width:50px"></th>
                                            <th style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle" style="text-align: center; white-space: nowrap;">
                                        <tr class="vertical-align align-middle" id="41">
                                                <input type="hidden" name="stock-id" value="78">
                                                <input type="hidden" name="cable-item-id" value="41">
                                                <td class="align-middle" id="41-stock-id" hidden="">78</td>
                                                <td class="align-middle" id="41-item-id" hidden="">41</td>
                                                <td class="align-middle" id="41-img-td">
                                                <img id="41-img" class="inv-img-50h thumb" src="./assets/img/config/<?php echo($current_favicon_image); ?>"  alt="Cat5e Black 1m"></td>
                                                <td class="align-middle" id="41-name"><a class="link">Cat5e Black 1m</a></td>
                                                <td class="align-middle" id="41-type-id" hidden="">2</td>
                                                <td class="align-middle" id="41-type"><or title="Cat5e Copper Cable">Cat5e</or></td> 
                                                <td class="align-middle link gold" id="41-site-name">Site 1</td>
                                                <td class="align-middle" id="41-quantity">11</td>
                                                <td class="align-middle" id="41-min-stock" style="color:#8f8f8f">10</td>
                                                <td class="align-middle" id="41-add"><button id="78-add-btn" class="btn btn-success cw nav-v-b" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                                                <td class="align-middle" id="41-remove"><button id="78-remove-btn" class="btn btn-danger cw nav-v-b" type="submit" name="action" value="remove"><i class="fa fa-minus"></i></button></td>
                                        </tr>
                                        <tr class="vertical-align align-middle" id="42">
                                                <input type="hidden" name="stock-id" value="79">
                                                <input type="hidden" name="cable-item-id" value="42">
                                                <td class="align-middle" id="42-stock-id" hidden="">79</td>
                                                <td class="align-middle" id="42-item-id" hidden="">42</td>
                                                <td class="align-middle" id="42-img-td">
                                                <img id="42-img" class="inv-img-50h thumb" src="./assets/img/config/<?php echo($current_favicon_image); ?>"  alt="Cat5e Black 2m"></td>
                                                <td class="align-middle" id="42-name"><a class="link">Cat5e Black 2m</a></td>
                                                <td class="align-middle" id="42-type-id" hidden="">2</td>
                                                <td class="align-middle" id="42-type"><or title="Cat5e Copper Cable">Cat5e</or></td> 
                                                <td class="align-middle link gold" id="42-site-name">Site 1</td>
                                                <td class="align-middle" id="42-quantity"><or class="red"><u style="border-bottom: 1px dashed #999; text-decoration: none" title="Below minimum stock count. Order more!">5</u></or></td>
                                                <td class="align-middle" id="42-min-stock" style="color:#8f8f8f">10</td>
                                                <td class="align-middle" id="42-add"><button id="79-add-btn" class="btn btn-success cw nav-v-b" type="submit" name="action" value="add"><i class="fa fa-plus"></i></button></td>
                                                <td class="align-middle" id="42-remove"><button id="79-remove-btn" class="btn btn-danger cw nav-v-b" type="submit" name="action" value="remove"><i class="fa fa-minus"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                        </div>
                    </td>
                </tr>

                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>.theme-table<br>
                            .theme-tableOuter<br>
                            .theme-table-blank<br>
                            .clickable<br>
                        </p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Stock Location Settings</u></h4><hr style="border-color:white;margin-top:5px">
                            <table class="table table-dark theme-table text-center" style="max-width:max-content; vertical-align: middle;">
                                <thead>
                                    <tr class="theme-tableOuter">
                                        <th>site_id</th>
                                        <th>site_name</th>
                                        <th hidden="">site_description</th>
                                        <th style="border-left:2px solid #95999c">area_id</th>
                                        <th>area_name</th>
                                        <th hidden="">area_description</th>
                                        <th hidden="">area_site_id</th>
                                        <th hidden="">area_parent_id</th>
                                        <th style="border-left:2px solid #95999c">shelf_id</th>
                                        <th>shelf_name</th>
                                        <th hidden="">shelf_area_id</th>
                                        <th style="border-left:2px solid #95999c"></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background-color:#6abad6 !important; color:black">
                                            <td class="stockTD">1</td>
                                            <td class="stockTD"><input id="site-1-name" class="form-control stockTD-input" name="name" type="text" value="Site 1" style="width:150px"></td>
                                            <td hidden=""><input id="site-1-description" class="form-control stockTD-input" type="text" name="description" value="Custodian Data Centers, Maidstone"></td>
                                            <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td> <td hidden=""></td> <td hidden=""></td> 
                                            <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td>
                                            <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                                <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                    <i class="fa fa-save"></i>
                                                </button>
                                            </td>
                                            <td class="stockTD theme-table-blank" "="">
                                                <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </td>
                                        
                                            <td class="stockTD theme-table-blank">
                                                <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" disabled="" title="Dependencies exist for this object.">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        
                                    </tr>
                                    <tr style="background-color:#99d4ef !important; color:black">
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">1</td>
                                        <td class="stockTD"><input id="area-1-name" class="form-control stockTD-input" type="text" name="name" value="Store room 1" style="width:150px"></td>
                                        <td class="stockTD" hidden=""><input id="area-1-description" class="form-control stockTD-input" type="text" name="description" value="Store Room 1"></td>
                                        <td class="stockTD" hidden=""><input id="area-1-parent" type="hidden" name="area-site-id" value="1"></td>
                                        <td class="stockTD" hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>
                            
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" disabled="" title="Dependencies exist for this object.">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    
                                    </tr>
                                    <tr style="background-color:#c1e9fc !important; color:black">
                                        <input type="hidden" id="shelf-1-id" name="id" value="1">
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden=""></td> 
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55;"></td> <td class="theme-table-blank"></td> <td hidden=""></td> <td hidden=""></td> <td hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">1</td>
                                        <td class="stockTD"><input id="shelf-1-name" class="form-control stockTD-input" type="text" name="name" value="Shelf 1" style="width:150px"></td>
                                        <td class="stockTD" hidden=""><input id="shelf-1-parent" type="hidden" name="shelf-area-id" value="1"></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>

                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="shelf" type="submit" disabled="" title="Dependencies exist for this object.">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="theme-table-blank">
                                        <td colspan="6" class="stockTD">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td colspan="3" style="border-left:2px solid #454d55">  
                                        </td>
                                    </tr>
                                    <tr class="theme-tableOuter"><td colspan="9"></td></tr>
                                    <tr style="background-color:#F4BB44 !important; color:black">
                                        <td class="stockTD">2</td>
                                        <td class="stockTD"><input id="site-2-name" class="form-control stockTD-input" name="name" type="text" value="Site 2" style="width:150px"></td>
                                        <td hidden=""><input id="site-2-description" class="form-control stockTD-input" type="text" name="description" value="Custodian Data Centers, Dartford"></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td> <td hidden=""></td> <td hidden=""></td> 
                                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank" "="">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>
                                    

                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" disabled="" title="Dependencies exist for this object.">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>                                
                                    </tr>
                                    <tr style="background-color:#ffe47a !important; color:black">
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">4</td>
                                        <td class="stockTD"><input id="area-4-name" class="form-control stockTD-input" type="text" name="name" value="Store room A" style="width:150px"></td>
                                        <td class="stockTD" hidden=""><input id="area-4-description" class="form-control stockTD-input" type="text" name="description" value="Sote room A"></td>
                                        <td class="stockTD" hidden=""><input id="area-4-parent" type="hidden" name="area-site-id" value="2"></td>
                                        <td class="stockTD" hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden=""></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>

                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" disabled="" title="Dependencies exist for this object.">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr style="background-color:#FFDEAD !important; color:black">
                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden=""></td> 
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55;"></td> <td class="theme-table-blank"></td> <td hidden=""></td> <td hidden=""></td> <td hidden=""></td>
                                        <td class="stockTD" style="border-left:2px solid #454d55; ">10</td>
                                        <td class="stockTD"><input id="shelf-10-name" class="form-control stockTD-input" type="text" name="name" value="Shelf A-1" style="width:150px"></td>
                                        <td class="stockTD" hidden=""><input id="shelf-10-parent" type="hidden" name="shelf-area-id" value="4"></td>
                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>

                                        <td class="stockTD theme-table-blank">
                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="shelf" type="submit" disabled="" title="Dependencies exist for this object.">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="theme-table-blank">
                                        <td colspan="6" class="stockTD">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td colspan="3" style="border-left:2px solid #454d55">  
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>
                        .theme-divBg<br>
                        .serial-bg<br>
                        .gold, a<br>
                        </p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Add/Remove Stock</u></h4><hr style="border-color:white;margin-top:5px">
                            <div class="container well-nopad theme-divBg text-left">
                                <div class="nav-row" style="margin-bottom:10px">
                                    <div class="nav-row" id="heading-row" style="margin-top:10px">
                                        <div style="width:200px;margin-right:25px"></div>
                                        <div id="heading-heading">
                                            <a><h2>Cisco C3650X</h2></a>
                                            <p id="sku"><strong>SKU:</strong> <or class="blue">STOCK-00001</or></p>
                                            <p id="locations" style="margin-bottom:0px"><strong>Locations:</strong><br>
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <td>Store room A, Shelf A-1</td>
                                                            <td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">12</or></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Store room 1, Shelf 1</td>
                                                            <td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">2</or></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Store room 1, Shelf 3</td>
                                                            <td style="padding-left:5px"><a class="btn serial-bg btn-stock cw">Stock: <or class="gold">3</or></a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="well-nopad theme-divBg">
                                    <div class="row">
                                        <div class="text-left" id="stock-info-left" style="padding-left:15px">
                                            <div class="nav-row" style="margin-bottom:25px">
                                                <div class="nav-row" id="manufacturer-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"><label class="nav-v-c text-right" style="width:100%" for="manufacturer" id="manufacturer-label">Manufacturer</label></div>
                                                    <div>
                                                        <select name="manufacturer" id="manufacturer" class="form-control" style="width:300px" required="">
                                                            <option value="" selected="" disabled="" hidden="">Select Manufacturer</option><option value="1">Cisco</option><option value="2">Dell</option><option value="3">HP</option><option value="4">Cyberoam</option><option value="5">Watchguard</option><option value="6">Supermicro</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="text-right gold clickable" style="margin-left: 25px;margin-top:5px;font-size:14px">Add New</label>
                                                    </div>
                                                </div>
                                    
                                                <div class="nav-row" id="submit-row" style="margin-top:25px">
                                                    <div style="width:200px;margin-right:25px"></div>
                                                    <div><input type="submit" value="Add Stock" name="submit" class="nav-v-c btn btn-success"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>
                        .theme-textColor<br>
                        .theme-btn<br>
                        .clickable<br>
                        .theme-divBg-m<br>
                        .theme-divBg<br>
                        .specialColor<br>
                        .serial-bg
                        </p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Stock</u></h4><hr style="border-color:white;margin-top:5px">
                            <div class="container text-left" style="padding-bottom:25px">
                                <div class="nav-row" style="margin-top:10px">
                                    <h3 style="font-size:22px;margin-top:20px;margin-bottom:0px;width:max-content" id="stock-name">Cisco C3650X (STOCK-00001)</h3>
                                    <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                        <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b" style="width:110px">
                                            <i class="fa fa-pencil"></i> Edit 
                                        </button>
                                    </div> 
                                    <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                        <button id="add-stock" class="btn btn-success theme-textColor nav-v-b" style="width:110px">
                                            <i class="fa fa-plus"></i> Add 
                                        </button>
                                    </div> 
                                    <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                        <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b" style="width:110px">
                                            <i class="fa fa-minus"></i> Remove 
                                        </button>
                                    </div> 
                                    <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                        <button id="transfer-stock" class="btn btn-warning nav-v-b" style="width:110px;color:black">
                                            <i class="fa fa-arrows-h"></i> Move 
                                        </button>
                                    </div>
                                </div>
                                <p id="stock-description" style="margin-bottom:0px">Cisco Switch test</p>
                            </div>
                            <div class="container well-nopad theme-divBg">
                                <div class="row">
                                    <div class="col-sm-6 text-left" id="stock-info-left">
                                        <table class="" id="stock-info-table" style="max-width:max-content">
                                                <thead>
                                                    <tr>
                                                        <th hidden="">id</th>
                                                        <th>Site</th>
                                                        <th style="padding-left: 10px">Location</th>
                                                        <th style="padding-left: 5px">Shelf</th>
                                                        <th style="padding-left: 5px">Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="stock-row-1">
                                                        <td hidden="">1</td>
                                                        <td id="site-1"><or class="clickable">Site 1</or></td>
                                                        <td id="area-1" style="padding-left: 10px"><or class="clickable">Stoor room 1</or>:</td>
                                                        <td id="shelf-1" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold">Shelf 1</button></td>
                                                        <td style="padding-left: 5px" class="text-center theme-textColor">2</td>
                                                    </tr>
                                                    
                                                    <tr id="stock-row-1">
                                                        <td hidden="">1</td>
                                                        <td id="site-1"><or class="clickable">Site 2</or></td>
                                                        <td id="area-1" style="padding-left: 10px"><or class="clickable">Store room 2</or>:</td>
                                                        <td id="shelf-3" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold">Shelf 3</button></td>
                                                        <td style="padding-left: 5px" class="text-center theme-textColor">3</td>
                                                    </tr>
                                                    
                                                    <tr id="stock-row-1">
                                                        <td hidden="">1</td>
                                                        <td id="site-1"><or class="clickable">Site 2</or></td>
                                                        <td id="area-3" style="padding-left: 10px"><or class="clickable">Store room A</or>:</td>
                                                        <td id="shelf-7" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold">Shelf A-1</button></td>
                                                        <td style="padding-left: 5px" class="text-center theme-textColor">65</td>
                                                    </tr>
                                                    </tbody>
                                            </table>
                                        <p></p>
                                            <p id="min-stock"><strong>Minimum Stock Count:</strong> <or class="specialColor">0</or></p>
                                        
                                            <p class="clickable gold" id="extra-info-dropdown">More Info <i class="fa-solid fa-2xs fa-chevron-up" style="margin-left:10px"></i></p> 
                                            <div id="extra-info">
                                                <p id="tags-head"><strong>Tag</strong></p>
                                                <p id="tags"><button class="btn theme-btn btn-stock-click gold" id="tag-3">cisco</button> <button class="btn theme-btn btn-stock-click gold" id="tag-1">switch</button> 
                                                </p><p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers"><button class="btn theme-btn btn-stock-click gold" id="manufacturer-1">Cisco</button> <button class="btn theme-btn btn-stock-click gold" id="manufacturer-3">HP</button> </p><p id="serial-numbers-head"><strong>Serial Numbers</strong></p><p><a class="serial-bg" id="serialNumber1">testing</a></p></div>
                                    </div>
                                    
                                    <div class="col-sm-4 text-right" style="margin-left:70px" id="stock-info-right"><div class="well-nopad theme-divBg nav-right" style="margin:20px;padding:0px;width:max-content">
                                        <div class="nav-row" style="width:315px">
                                                <div class="thumb theme-divBg-m text-center" style="width:235px;height:235px">
                                                    <img class="nav-v-c" id="stock-1-img-1" style="max-width:235px; max-height:235px" alt="Cisco C3650X - image 1" src="assets/img/config/<?php echo($current_favicon_image); ?>">
                                                </div>
                                                <span id="side-images" style="margin-left:5px">
                                                
                                                <div class="thumb theme-divBg-m" style="width:75px;height:75px;margin-bottom:5px">
                                                    <img class="nav-v-c" id="stock-1-img-2" style="width:75px" alt="Cisco C3650X - image 2" src="assets/img/config/<?php echo($current_favicon_image); ?>">
                                                </div>
                                                <span></span></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr id="template" class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>.theme-profileTextColor</p>
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Profile</u></h4><hr style="border-color:white;margin-top:5px">
                            <table class="theme-profileTextColor">
                                <tbody>
                                    <input id="profile-id" type="hidden" value="2" name="id">
                                    <tr class="nav-row" id="username">
                                        <td id="username_header" style="width:200px">
                                            <!-- Custodian Colour: #72BE2A -->
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Username:</p>
                                        </td>
                                        <td id="username_info">
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">username</p>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr class="nav-row" style="margin-top:20px" id="firstname">
                                        <td id="firstname_header" style="width:200px">
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">First Name:</p>
                                        </td>
                                        <td id="firstname_info">
                                            <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle">Name</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr id="template" class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        <p>
                        .theme-table<br>
                        .theme-tableOuter<br>
                        .transactionAdd<br>
                        .transactionMove<br>
                        .transactionRemove<br> 
                        .transactionDelete<br> 

                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Transactions</u></h4><hr style="border-color:white;margin-top:5px">
                            <table class="table table-dark theme-table centertable" id="transactions">
                                <thead>
                                    <tr class="theme-tableOuter">
                                        <th hidden="">ID</th>
                                        <th hidden="">Stock ID</th>
                                        <th hidden="">Item ID</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th hidden="">Shelf</th>
                                        <th>Location</th>
                                        <th>Username</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Serial Number</th>
                                        <th hidden="">Comments</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <tr class="transactionMove">
                                        <td id="t_id" hidden="">373</td>
                                        <td hidden="">1</td>
                                        <td hidden="">404</td>
                                        <td id="t_type">Move</td>
                                        <td id="t_date">2023-09-21</td>
                                        <td id="t_time">16:04:16</td>
                                        <td hidden="">Shelf 1</td>
                                        <td id="a_name">Store room 1</td>
                                        <td id="t_username">inventory</td>
                                        <td id="t_quantity">1</td>
                                        <td>£0</td>
                                        <td>serial-01</td>
                                        <td hidden=""></td>
                                        <td id="t_reason">Move Stock</td>
                                    </tr>
                                    <tr class="transactionRemove">
                                        <td id="t_id" hidden="">334</td>
                                        <td hidden="">1</td>
                                        <td hidden="">129</td>
                                        <td id="t_type">Remove</td>
                                        <td id="t_date">2023-09-02</td>
                                        <td id="t_time">20:25:16</td>
                                        <td hidden="">Store room A</td>
                                        <td id="a_name">Shelf A-1</td>
                                        <td id="t_username">inventory</td>
                                        <td id="t_quantity">-1</td>
                                        <td>£0</td>
                                        <td></td>
                                        <td hidden=""></td>
                                        <td id="t_reason">10 test</td>
                                    </tr>
                                    <tr class="transactionAdd">
                                        <td id="t_id" hidden="">315</td>
                                        <td hidden="">1</td>
                                        <td hidden="">129</td>
                                        <td id="t_type">Add</td>
                                        <td id="t_date">2023-08-30</td>
                                        <td id="t_time">18:36:00</td>
                                        <td hidden="">Store room A</td>
                                        <td id="a_name">Shelf A-1</td>
                                        <td id="t_username">inventory</td>
                                        <td id="t_quantity">1</td>
                                        <td>£35</td>
                                        <td>serial-02</td>
                                        <td hidden=""></td>
                                        <td id="t_reason">New Stock</td>
                                    </tr>
                                    <tr class="transactionDelete">
                                        <td id="t_id" hidden="">315</td>
                                        <td hidden="">1</td>
                                        <td hidden="">129</td>
                                        <td id="t_type">Delete</td>
                                        <td id="t_date">2023-08-29</td>
                                        <td id="t_time">15:22:05</td>
                                        <td hidden="">Store room 2</td>
                                        <td id="a_name">Shelf 11</td>
                                        <td id="t_username">inventory</td>
                                        <td id="t_quantity">-1</td>
                                        <td>£0</td>
                                        <td></td>
                                        <td hidden=""></td>
                                        <td id="t_reason">New Stock</td>
                                    </tr>
                                </tbody>
                        </table>
                        </div>
                    </td>
                </tr>


                <!-- <tr><td colspan=2><div style="height:20px"></div></td></tr>
                <tr id="template" class="text-center">
                    <td class="uni" style="min-width:100px;padding-left:20px;padding-right:20px">
                        template
                    </td>
                    <td style="min-width:100px">
                        <div class="container well-nopad" style="background-color:transparent">
                            <h4 style="margin-bottom:0px"><u>Template Row</u></h4><hr style="border-color:white;margin-top:5px">
                            row
                        </div>
                    </td>
                </tr> -->


            </tbody>
        </table>

    </div>

    <?php include 'foot.php'; ?>

    <script>

        const cssEditor = document.getElementById('css-editor');
        const applyButton = document.getElementById('apply-button');
        const style = document.getElementById('style');

        document.addEventListener("load", (async () => {
            const text = await (await fetch(document.getElementById('theme-css').href)).text();
            cssEditor.innerHTML = text;
            })()
        );

        // Function to apply CSS
        function applyCSS() {
            const style = document.getElementById('style');
            const cssText = cssEditor.value;
            style.innerHTML = cssText;
        }

        // Event listener for the "Apply CSS" button
        applyButton.addEventListener('click', applyCSS);

        function changeTheme() {
            var select = document.getElementById('theme-select');
            var value = select.value;
            var css = document.getElementById('theme-css');
            var profile_id = document.getElementById('profile-id').value;
            var theme = document.getElementById('theme-select-option-' + value).title;
            var theme_name = document.getElementById('theme-select-option-' + value).alt;
            // css.href = "./assets/css/theme-"+theme+".css";
            css.href = './assets/css/' + theme;

            const cssEditor = document.getElementById('css-editor');
            (async () => {
                const text = await (await fetch(css.href)).text();
                cssEditor.innerHTML = text;
            })();

            refreshCSS = () => {
                let links = document.getElementsByTagName('link');
                for (let i = 0; i < links.length; i++) {
                    if (links[i].getAttribute('rel') == 'stylesheet') {
                        if (links[i].id !== 'google-font') {
                            let href = links[i].getAttribute('href')
                                .split('?')[0];

                            let newHref = href + '?version='
                                + new Date().getMilliseconds();

                            links[i].setAttribute('href', newHref);
                        }
                    }
                }
            }

            refreshCSS();

        }


        // Function to download CSS content as a file
        function downloadCSS() {
            const cssEditor = document.getElementById('css-editor');
            const cssText = cssEditor.value;
            const fileName = document.getElementById('download-theme-name').value !== '' ? document.getElementById('download-theme-name').value : 'new-theme';
            
            // Create a Blob containing the CSS content
            const blob = new Blob([cssText], { type: 'text/css' });
            
            // Create a temporary anchor element to trigger the download
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = fileName+'.css'; // Set the filename
            
            // Trigger the click event to initiate the download
            a.click();
            
            // Clean up
            URL.revokeObjectURL(a.href);
        }

        // color-picker box json
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

</body>