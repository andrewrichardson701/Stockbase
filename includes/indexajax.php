<?php

if (isset($_GET['request-inventory']) && $_GET['request-inventory'] == 1) {

    $results = [];

    $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
    $site = isset($_GET['site']) ? $_GET['site'] : "0";
    $area = isset($_GET['area']) ? $_GET['area'] : "0";
    $name = isset($_GET['name']) ? $_GET['name'] : "";
    $sku = isset($_GET['sku']) ? $_GET['sku'] : "";
    $location = isset($_GET['location']) ? $_GET['location'] : "";
    $shelf = isset($_GET['shelf']) ? $_GET['shelf'] : "";
    $label = isset($_GET['label']) ? $_GET['label'] : "";
    $manufacturer = isset($_GET['manufacturer']) ? $_GET['manufacturer'] : "";

    $area_array = [];

    if (isset($_GET['rows'])) {
        if ($_GET['rows'] == 50 || $_GET['rows'] == 100) {
            $rowSelectValue = $_GET['rows'];
        } else {
            $rowSelectValue = 10;
        }
    } else {
        $rowSelectValue = 10;
    }

    
    include 'dbh.inc.php';
    
    if ($site == "0") { $area = "0"; }
        
    // check if the current site and area can be together, if not, set to 0
    if ($area !== 0) {
        $sql_site = "SELECT id FROM area WHERE site_id=$site AND id=$area";
        $stmt_site = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_site);
            $result_site = mysqli_stmt_get_result($stmt_site);
            $rowCount_site = $result_site->num_rows;
            $siteCount = $rowCount_site;
            if ($rowCount_site < 1) {
                $area = "0";
            }
        }
    }

    if ($site !== 0) {
        $sql_areas = "SELECT id, name 
                        FROM area 
                        WHERE site_id=$site";
        $stmt_areas = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_areas, $sql_areas)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_areas);
            $result_areas = mysqli_stmt_get_result($stmt_areas);
            $rowCount_areas = $result_areas->num_rows;
            
            if ($area == 0) { $selected = 1; } else { $selected = 0; }
            $area_array[] = array('id' => 0, 'name' => 'All', 'selected' => $selected);
            if ($rowCount_areas >= 1) {
                while ($row_areas = $result_areas->fetch_assoc()) {
                    if ($area == $row_areas['id']) {
                        $area_array[] = array('id' => $row_areas['id'], 'name' => $row_areas['name'], 'selected' => 1);
                    } else { 
                        $area_array[] = array('id' => $row_areas['id'], 'name' => $row_areas['name'], 'selected' => 0);
                    }
                }
            }
        }
    } else {
        $area_array[] = array('id' => 0, 'name' => 'All', 'selected' => 1);
    }
        










    $sql_inv_count = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, 
                        stock.min_stock AS stock_min_stock, stock.is_cable AS stock_is_cable,
                    GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
                    site.id AS site_id, site.name AS site_name, site.description AS site_description,";
    if ($area != 0) {
        $sql_inv_count .=" area.id as area_id_global,";
    }
    $sql_inv_count .=   " (SELECT SUM(quantity) 
                        FROM item 
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        WHERE item.stock_id=stock.id AND area.site_id=site.id";
    if ($area != 0) {
        $sql_inv_count .=       " AND shelf.area_id=area_id_global";
    }
    $sql_inv_count .=   " ) AS item_quantity,
                    label_names.label_names AS label_names,
                    label_ids.label_ids AS label_ids,
                    stock_img_image.stock_img_image
                FROM stock
                LEFT JOIN item ON stock.id=item.stock_id
                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                LEFT JOIN area ON shelf.area_id=area.id 
                LEFT JOIN site ON area.site_id=site.id
                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                LEFT JOIN (
                    SELECT stock_img.stock_id, MIN(stock_img.image) AS stock_img_image
                    FROM stock_img
                    GROUP BY stock_img.stock_id
                ) AS stock_img_image
                    ON stock_img_image.stock_id = stock.id
                LEFT JOIN (SELECT stock_label.stock_id, GROUP_CONCAT(DISTINCT label.name SEPARATOR ', ') AS label_names
                        FROM stock_label 
                        INNER JOIN label ON stock_label.label_id = label.id
                        GROUP BY stock_label.stock_id) AS label_names
                    ON label_names.stock_id = stock.id
                LEFT JOIN (SELECT stock_label.stock_id, GROUP_CONCAT(DISTINCT label_id SEPARATOR ', ') AS label_ids
                        FROM stock_label
                        GROUP BY stock_label.stock_id) AS label_ids
                    ON label_ids.stock_id = stock.id
                    WHERE stock.is_cable=0 AND stock.deleted=0 ";
    $sql_inv_add = '';
    if ($site !== '0') { $sql_inv_add  .= " AND site.id=$site";} 
    if ($area !== '0') { $sql_inv_add  .= " AND area.id=$area";} 
    if ($name !== '') { $sql_inv_add  .= " AND stock.name LIKE CONCAT('%', '$name', '%')";}
    if ($sku !== '') { $sql_inv_add  .= " AND stock.sku LIKE CONCAT('%', '$sku', '%')";}
    if ($location !== '') { $sql_inv_add  .= " AND area.name LIKE CONCAT('%', '$location', '%')";}
    if ($shelf !== '') { $sql_inv_add  .= " AND shelf.name LIKE CONCAT('%', '$shelf', '%')";}
    if ($label !== '') { $sql_inv_add  .= " AND label_names LIKE CONCAT('%', '$label', '%')";}
    if ($manufacturer !== '') { $sql_inv_add  .= " AND manufacturer.name LIKE CONCAT('%', '$manufacturer', '%')";}
    if ($showOOS == 0) { 
        $sql_inv_add  .= " AND item.deleted=0 AND 
            (SELECT SUM(quantity) 
                FROM item 
                INNER JOIN shelf ON item.shelf_id=shelf.id
                INNER JOIN area ON shelf.area_id=area.id
                WHERE item.stock_id=stock.id AND area.site_id=site.id
            )!='null'";
    } 
    $sql_inv_count .= $sql_inv_add;
    // $sql_inv .= " ORDER BY stock.name;";
    $sql_inv_count .= " GROUP BY 
                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable, 
                    site_id, site_name, site_description, stock_img_image.stock_img_image";
    if ($area != 0) { $sql_inv_count .= ", area.id"; }
    if ($showOOS == 1) {
        $sql_inv_count .= " HAVING item_quantity IS NULL OR item_quantity = 0";
    }
    $sql_inv_count .= " ORDER BY stock.name";

    $stmt_inv_count = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_inv_count, $sql_inv_count)) {
        echo("ERROR getting entries");
    } else {
        mysqli_stmt_execute($stmt_inv_count);
        $result_inv_count = mysqli_stmt_get_result($stmt_inv_count);
        $totalRowCount = $result_inv_count->num_rows;
        
        // Pagination settings
        $results_per_page = $rowSelectValue; // Number of rows per page - based no the querystring (or 10 by default)
        $total_pages = ceil($totalRowCount / $results_per_page);

        $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($current_page < 1) {
            $current_page = 1;
        } elseif ($current_page > $total_pages) {
            $current_page = $total_pages;
        } 

        // Calculate the offset for the query
        $offset = ($current_page - 1) * $results_per_page;
        if ($offset < 0) {
            $offset = $results_per_page;
        }

        $sql_inv_pagination = " LIMIT $results_per_page OFFSET $offset;";

        $sql_inv = $sql_inv_count .= $sql_inv_pagination;

        // echo '<pre hidden>'.$sql_inv.'</pre>';

        $stmt_inv = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_execute($stmt_inv);
            $result_inv = mysqli_stmt_get_result($stmt_inv);
            $rowCount_inv = $result_inv->num_rows;

            $pageNumberArea = '';

            if ($total_pages > 1) {
                if ($current_page > 1) {
                    $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } else {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    }
                }

                if ($current_page < $total_pages) {
                    $pageNumberArea .= '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
                }  
            }

            if ($site !== 0 && $site !== '0') { // checker for whether or not to show the site heading on the table.
                $results[-1]['siteNeeded'] = 0;
            } else {
                $results[-1]['siteNeeded'] = 1;
            }
            $results[-1]['site'] = $site;
            $results[-1]['area'] = $area;
            $results[-1]['shelf'] = $shelf;
            $results[-1]['name'] = $name;
            $results[-1]['sku'] = $sku;
            $results[-1]['location'] = $location;
            $results[-1]['label'] = $label;
            $results[-1]['manufacturer'] = $manufacturer;
            $results[-1]['total-pages'] = $total_pages;
            $results[-1]['page-number-area'] = $pageNumberArea;
            $results[-1]['page'] = $current_page;
            $results[-1]['rows'] = $rowSelectValue;
            $results[-1]['url'] = "./?oos=$showOOS&site=$site&area=$area&name=$name&sku=$sku&shelf=$shelf&manufacturer=$manufacturer&label=$label&page=$current_page&rows=$rowSelectValue";
            $results[-1]['sql'] = $sql_inv;
            $results[-1]['areas'] = $area_array;
            
            // ----
            $img_directory = "assets/img/stock/"; 
            if ($rowCount_inv < 1) {
                $result = "<tr><td colspan=100%>No Inventory Found</td></tr>";
                $results[] = $result;
            } else {
                while ($row = $result_inv->fetch_assoc()) {
                    $stock_id = $row['stock_id'];
                    $stock_img_file_name = $row['stock_img_image'];
                    $stock_name = $row['stock_name'];
                    $stock_sku = $row['stock_sku'];
                    $stock_quantity_total = $row['item_quantity'];
                    $stock_locations = $row['area_names'];
                    $stock_site_id = $row['site_id'];
                    $stock_site_name = $row['site_name'];
                    $stock_label_names = ($row['label_names'] !== null) ? explode(", ", $row['label_names']) : '---';
                    

                    // Echo each row (inside of SQL results)

                    $result =
                    '<tr class="vertical-align align-middle"id="'.$stock_id.'">
                        <td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>
                        <td class="align-middle" id="'.$stock_id.'-img-td">
                        ';
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                        <td class="align-middle link gold" id="'.$stock_id.'-name" onclick="navPage(\'./stock.php?stock_id='.$stock_id.'\')">'.$stock_name.'</td>
                        <td class="align-middle" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                        <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                    if ($stock_quantity_total == 0) {
                        $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';
                    if ($site == 0) { $result .= '<td class="align-middle link gold" id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'; }
                    $result .= '<td class="align-middle" id="'.$stock_id.'-label">';
                    if (is_array($stock_label_names)) {
                        for ($o=0; $o < count($stock_label_names); $o++) {
                            $divider = $o < count($stock_label_names)-1 ? ', ' : '';
                            $result .= '<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'label\', \''.$stock_label_names[$o].'\'))">'.$stock_label_names[$o].'</or>'.$divider;
                        }
                    } 
                    $result .= '</td>
                    <td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                    </tr>
                    ';
                    $results[] = $result;
                }
            }
            // echo('<table><tbody>');
            // print_r('<pre>');
            // print_r(($results));
            // print_r('</pre>');
            
            // for ($r=0; $r<count($results)-1; $r++) {
            //     $result = $results[$r];
            //     echo $result;
            //     echo '<br>';
            // }
            // echo('</tbody></table>');

            echo(json_encode($results));
        }
    }
}



?>