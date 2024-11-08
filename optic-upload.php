<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.


include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?></title>
</head>
<body>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="content">
        <?php // Error section
        $errorPprefix = '<div class="container"><p class="red" style="padding-top:10px">Error: ';
        $errorPsuffix = '</p></div>';
        $successPprefix = '<div class="container"><p class="green" style="padding-top:10px">';
        $successPsuffix = '</p></div>';

        include 'includes/responsehandling.inc.php';
        showResponse(); // 

        ?>

        <div class="container" style="margin-top:20px">
            <h2 class="header-small" style="padding-bottom:5px"><?php if (isset($_GET['return'])) { echo('<button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href=\''.urldecode($_GET['return']).'\'"><i class="fa fa-chevron-left"></i> Back</button>'); } ?>Optic Upload</h2>
            <form enctype="multipart/form-data" action="./includes/opticsimport.inc.php" method="POST" style="margin-bottom:20px;margin-top:50px">
                <input class="" type="file" name="csv">
                <input type="submit" class="btn btn-success" name="opticsimport-submit" value="Import">
            </form>
            <a class="link" href="assets/files/optics_template.csv">Download Template</a>
        </div>



        