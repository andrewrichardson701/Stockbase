<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// INVENTORY VIEW PAGE. SHOWS ALL INVENTORY ONCE LOGGED IN AND SHOWS FILTERS IN THE NAV
include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

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
            <h2 class="header-small" style="padding-bottom:5px"><?php if (isset($_GET['return'])) { echo('<button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href=\''.urldecode($_GET['return']).'\'"><i class="fa fa-chevron-left"></i> Back</button>'); } ?>Tags</h2>
        </div>

        <?php
        $sql = "SELECT
                    t.id AS t_id, t.name AS t_name,
                    (SELECT COUNT(s_t.id)
                    FROM stock_tag AS s_t
                    WHERE s_t.tag_id = t.id) AS object_count,
                    s.id AS s_id, s.name AS s_name, s.description AS s_description, s.sku AS s_sku,
                    (SELECT COUNT(i.id)
                    FROM item AS i
                    WHERE i.stock_id = s.id AND i.deleted = 0) AS item_count,
                    (SELECT s_i.id 
                    FROM stock_img AS s_i
                    WHERE s_i.stock_id=s.id
                    LIMIT 1) AS img_id,
                    (SELECT s_i.image
                    FROM stock_img AS s_i
                    WHERE s_i.id=img_id) AS img_image
                FROM
                    tag AS t
                LEFT JOIN
                    stock_tag AS s_t ON t.id = s_t.tag_id
                LEFT JOIN
                    stock AS s ON s_t.stock_id = s.id AND s.deleted = 0
                LEFT JOIN
                    item AS i ON i.stock_id = s.id AND i.deleted = 0
                LEFT JOIN
                    stock_img AS s_i ON s.id=s_i.stock_id
                WHERE
                    t.deleted = 0 
                GROUP BY
                    t_id, t_name, object_count, 
                    s_id, s_name, s_description, s_sku, item_count
                ORDER BY
                    t_name, s_name;
                ";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../login.php?sqlerror=getLDAPconfig");
            exit();
        } else {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                echo('<div class="container">No Collections Found</div>');
            } else {
                ?>
                <div class="container">
                    <table class="table table-dark theme-table centertable" style="margin-bottom:0px;">
                        <thead style="text-align: center; white-space: nowrap;">
                            <tr class="theme-tableOuter">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Objects</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $tag_array = [];

                while ($row = $result->fetch_assoc()) {
                    if (!array_key_exists($row['t_id'], $tag_array)) {
                        // $tag_array[$row['t_id']] = array('id' => $row['t_id'], 'name' => $row['t_name'], 'description' => $row['t_description'], 'count' => $row['object_count']);
                        $tag_array[$row['t_id']] = array('id' => $row['t_id'], 'name' => $row['t_name'], 'count' => $row['object_count']);
                    }
                    if ($row['s_id'] !== '') {
                        $tag_array[$row['t_id']]['stock'][] = array('id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'], 
                                                                        'sku' => $row['s_sku'], 'count' => $row['item_count'], 
                                                                        'img_id' => $row['img_id'], 'img_image' => $row['img_image']);                   
                    }
                }

                // print_r('<pre>');
                // print_r($tag_array);
                // print_r('</pre>');

                ?>
                <script>
                    function toggleHiddenCollection(clicked, id) {
                        var hiddenID = 'tag-'+id+'-stock';
                        var hiddenRow = document.getElementById(hiddenID);
                        if (hiddenRow.hidden == false) {
                            hiddenRow.hidden=true;
                            clicked.innerText='+';
                        } else {
                            hiddenRow.hidden=false;
                            clicked.innerText='-';
                        }
                    }
                </script>
                <?php


                foreach ($tag_array as $col) {
                    $tag_id = $col['id'];
                    $tag_name = $col['name'];
                    $tag_count = $col['count'];
                    $tag_stock = $col['stock'];
                    echo ('
                            <tr id="tag-'.$tag_id.'">
                                <td class="text-center align-middle">'.$tag_id.'</td>
                                <td class="text-center align-middle">'.$tag_name.'</td>
                                <td class="text-center align-middle">'.$tag_count.'</td>
                                <th class="text-center align-middle clickable" onclick="toggleHiddenCollection(this, \''.$tag_id.'\')">+</th>
                            </tr>
                    ');
                    if (!empty($tag_stock) || count($tag_stock) > 0){
                        echo('
                            <tr id="tag-'.$tag_id.'-stock" hidden>
                                <td colspan=100%>
                                    <div style="margin: 5px 20px 10px 20px">
                                        <table class="table table-dark theme-table centertable" style="margin:0px;max-width:100%;border: 1px solid #454d55;">
                                            <thead style="text-align: center; white-space: nowrap;">
                                                <tr class="theme-tableOuter">
                                                    <th></th>
                                                    <th>Stock ID</th>
                                                    <th>Stock Name</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                        ');
                        foreach($tag_stock as $stock) {
                            $stock_id = $stock['id'];
                            $stock_name = $stock['name'];
                            $stock_description = $stock['description'];
                            $stock_sku = $stock['sku'];
                            $stock_count = $stock['count'];
                            $stock_img_id = $stock['img_id'];
                            $stock_img_image = $stock['img_image'];
                            $img_folder = './assets/img/stock/';
                            echo('
                                                <tr id="tag-'.$tag_id.'-stock-'.$stock_id.'">
                                                    <td class="text-center align-middle">'); if (isset($stock['img_id'])) { echo('<img id="image-'.$stock_img_id.'" class="inv-img-main thumb" src="'.$img_folder.$stock_img_image.'">'); } echo('</td>
                                                    <td class="text-center align-middle">'.$stock_id.'</td>
                                                    <td class="text-center align-middle link"><a href="./stock.php?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                                                    <td class="text-center align-middle">'.$stock_count.'</td>
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
                
                <?php
            }
        }



        ?>

        <!-- <div class="container">
            <table class="table table-dark theme-table centertable" style="margin-bottom:0px;">
                <thead style="text-align: center; white-space: nowrap;">
                    <tr class="theme-tableOuter">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Objects</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">6500 Parts</td>
                        <td class="text-center align-middle">Parts and spares for Cisco 6500 chassis</td>
                        <td class="text-center align-middle">12</td>
                        <td class="text-center align-middle">+</td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">ASR 9k Parts</td>
                        <td class="text-center align-middle">Parts and spares for Cisco ASR9k chassis</td>
                        <td class="text-center align-middle">4</td>
                        <td class="text-center align-middle">-</td>
                    </tr>
                    <tr>
                        <td colspan=100%>
                            <div style="margin: 5px 20px 10px 20px">
                                <table class="table table-dark theme-table centertable" style="margin:0px;max-width:100%;border: 1px solid #454d55;">
                                    <thead style="text-align: center; white-space: nowrap;">
                                        <tr class="theme-tableOuter">
                                            <th></th>
                                            <th>Stock ID</th>
                                            <th>Stock Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center align-middle"><img class="inv-img-main thumb" src="./assets/img/stock/stock-126-img-311023154334.jpg"></td>
                                            <td class="text-center align-middle">11</td>
                                            <td class="text-center align-middle gold">Cisco 6500 Chassis</td>
                                            <td class="text-center align-middle">1</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center align-middle"><img class="inv-img-main thumb" src="./assets/img/stock/stock-124-img-311023143150.jpg"></td>
                                            <td class="text-center align-middle">12</td>
                                            <td class="text-center align-middle gold">Example part 1</td>
                                            <td class="text-center align-middle">11</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center align-middle"><img class="inv-img-main thumb" src="./assets/img/stock/stock-124-img-311023143150.jpg"></td>
                                            <td class="text-center align-middle">13</td>
                                            <td class="text-center align-middle gold">Example part 2</td>
                                            <td class="text-center align-middle">3</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center align-middle"><img class="inv-img-main thumb" src="./assets/img/stock/stock-124-img-311023143150.jpg"></td>
                                            <td class="text-center align-middle">14</td>
                                            <td class="text-center align-middle gold">Example part 3</td>
                                            <td class="text-center align-middle">4</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">3</td>
                        <td class="text-center align-middle">Example Collection</td>
                        <td class="text-center align-middle">Parts and spares for example</td>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">+</td>
                    </tr>
                </tbody>
            </table>
        </div> -->
    </div>

    <?php include 'foot.php'; ?>
</body>
