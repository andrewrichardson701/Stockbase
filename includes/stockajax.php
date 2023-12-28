<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USED FOR THE INDEX PAGE TO GET THE INVENTORY/STOCK ON AJAX REQUEST

if (isset($_GET['request-inventory']) && $_GET['request-inventory'] == 1) {

    $results = [];

    $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
    $site = isset($_GET['site']) ? $_GET['site'] : "0";
    $area = isset($_GET['area']) ? $_GET['area'] : "0";
    $name = isset($_GET['name']) ? $_GET['name'] : "";
    $sku = isset($_GET['sku']) ? $_GET['sku'] : "";
    $location = isset($_GET['location']) ? $_GET['location'] : "";
    $shelf = isset($_GET['shelf']) ? $_GET['shelf'] : "";
    $tag = isset($_GET['tag']) ? $_GET['tag'] : "";
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
        










    $sql_inv_cte = "WITH QuantityCTE AS (
        SELECT
            stock_id,
            site_id,
            SUM(item_quantity) AS total_item_quantity
        FROM (
            SELECT
                item.stock_id,
                area.site_id,
                area.id AS area_id_global,
                SUM(quantity) AS item_quantity
            FROM
                item
                INNER JOIN shelf ON item.shelf_id = shelf.id
                INNER JOIN area ON shelf.area_id = area.id
            WHERE
                item.deleted = 0
            GROUP BY
                item.stock_id, area.site_id, area.id
        ) AS Subquery
        GROUP BY
            stock_id, site_id
    )";

    $sql_inv_count = $sql_inv_cte . "
    SELECT
        stock.id AS stock_id,
        stock.name AS stock_name,
        stock.description AS stock_description,
        stock.sku AS stock_sku,
        stock.min_stock AS stock_min_stock,
        stock.is_cable AS stock_is_cable,
        GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
        site.id AS site_id,
        site.name AS site_name,
        site.description AS site_description,
        COALESCE(cte.total_item_quantity, 0) AS item_quantity,
        tag_names.tag_names AS tag_names,
        tag_ids.tag_ids AS tag_ids,
        stock_img_image.stock_img_image
    FROM
        stock
        LEFT JOIN item ON stock.id = item.stock_id
        LEFT JOIN shelf ON item.shelf_id = shelf.id
        LEFT JOIN area ON shelf.area_id = area.id
        LEFT JOIN site ON area.site_id = site.id
        LEFT JOIN manufacturer ON item.manufacturer_id = manufacturer.id
        LEFT JOIN (
            SELECT
                stock_img.stock_id,
                MIN(stock_img.image) AS stock_img_image
            FROM
                stock_img
            GROUP BY
                stock_img.stock_id
        ) AS stock_img_image ON stock_img_image.stock_id = stock.id
        LEFT JOIN (
            SELECT
                stock_tag.stock_id,
                GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names
            FROM
                stock_tag
                INNER JOIN tag ON stock_tag.tag_id = tag.id
            GROUP BY
                stock_tag.stock_id
        ) AS tag_names ON tag_names.stock_id = stock.id
        LEFT JOIN (
            SELECT
                stock_tag.stock_id,
                GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids
            FROM
                stock_tag
            GROUP BY
                stock_tag.stock_id
        ) AS tag_ids ON tag_ids.stock_id = stock.id
        LEFT JOIN QuantityCTE cte ON stock.id = cte.stock_id AND site.id = cte.site_id
    WHERE
        stock.is_cable = 0
        AND stock.deleted = 0";
    if ($showOOS == 1) {
        $sql_inv_count .= " ";
    } else {
        $sql_inv_count .= " AND item.deleted = 0";
    }   

    if ($site !== '0') {
        $sql_inv_count .= " AND site.id = $site";
    }

    if ($area !== '0') {
        $sql_inv_count .= " AND area.id = $area";
    }

    if ($name !== '') {
        $name = mysqli_real_escape_string($conn, $name);
        $sql_inv_count .= " AND (MATCH(stock.name) AGAINST ('$name' IN NATURAL LANGUAGE MODE) 
                                    OR MATCH(stock.description) AGAINST ('$name' IN NATURAL LANGUAGE MODE) 
                                    OR stock.name LIKE CONCAT('%', '$name', '%')
                                )";
    }

    if ($sku !== '') {
        $sku = mysqli_real_escape_string($conn, $sku);
        $sql_inv_count .= " AND stock.sku LIKE CONCAT('%', '$sku', '%')";
    }

    if ($location !== '') {
        $location = mysqli_real_escape_string($conn, $location);
        $sql_inv_count .= " AND area.name LIKE CONCAT('%', '$location', '%')";
    }

    if ($shelf !== '') {
        $shelf = mysqli_real_escape_string($conn, $shelf);
        $sql_inv_count .= " AND shelf.name LIKE CONCAT('%', '$shelf', '%')";
    }

    if ($tag !== '') {
        $tag = mysqli_real_escape_string($conn, $tag);
        $sql_inv_count .= " AND tag_names LIKE CONCAT('%', '$tag', '%')";
    }

    if ($manufacturer !== '') {
        $manufacturer = mysqli_real_escape_string($conn, $manufacturer);
        $sql_inv_count .= " AND manufacturer.name LIKE CONCAT('%', '$manufacturer', '%')";
    }

    if ($showOOS == 0) {
        $sql_inv_count .= " AND item.deleted = 0";
    }

    $sql_inv_count .= "
        GROUP BY
            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
            site_id, site_name, site_description, stock_img_image.stock_img_image";

    if ($area != 0) {
        $sql_inv_count .= ", area.id";
    }

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
                if ($total_pages > 5) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                            // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                        } elseif ($i == 1 && $current_page > 5) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                        } elseif ($i < $current_page && $i >= $current_page-2) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        } elseif ($i > $current_page && $i <= $current_page+2) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        } elseif ($i == $total_pages) {
                            $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                        }
                    }
                } else {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                            // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                        } else {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        }
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
            $results[-1]['tag'] = $tag;
            $results[-1]['manufacturer'] = $manufacturer;
            $results[-1]['total-pages'] = $total_pages;
            $results[-1]['page-number-area'] = $pageNumberArea;
            $results[-1]['page'] = $current_page;
            $results[-1]['rows'] = $rowSelectValue;
            $results[-1]['url'] = "./?oos=$showOOS&site=$site&area=$area&name=$name&sku=$sku&shelf=$shelf&manufacturer=$manufacturer&tag=$tag&page=$current_page&rows=$rowSelectValue";
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
                    $stock_tag_names = ($row['tag_names'] !== null) ? explode(", ", $row['tag_names']) : '---';
                    

                    // Echo each row (inside of SQL results)

                    $result =
                    '<tr class="vertical-align align-middle highlight" id="'.$stock_id.'">
                        <td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>
                        <td class="align-middle" id="'.$stock_id.'-img-td">
                        ';
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                        <td class="align-middle gold" id="'.$stock_id.'-name" style="white-space:wrap"><a class="link" href="./stock.php?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                        <td class="align-middle viewport-large-empty" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                        <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                    if ($stock_quantity_total == 0) {
                        $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';
                    if ($site == 0) { $result .= '<td class="align-middle link gold" style="white-space: nowrap !important;"id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'; }
                    $result .= '<td class="align-middle viewport-large-empty" style="white-space: wrap" id="'.$stock_id.'-tag">';
                    if (is_array($stock_tag_names)) {
                        for ($o=0; $o < count($stock_tag_names); $o++) {
                            $divider = $o < count($stock_tag_names)-1 ? ', ' : '';
                            $result .= '<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'tag\', \''.$stock_tag_names[$o].'\'))">'.$stock_tag_names[$o].'</or>'.$divider;
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
} elseif (isset($_GET['request-inventory-audit']) && $_GET['request-inventory-audit'] == 1) {

    $results = [];

    $audit = isset($_GET['audit']) ? (int)$_GET['audit'] : 0;
    $showOOS = isset($_GET['oos']) ? (int)$_GET['oos'] : 0;
    $site = isset($_GET['site']) ? $_GET['site'] : "0";
    $area = isset($_GET['area']) ? $_GET['area'] : "0";
    $name = isset($_GET['name']) ? $_GET['name'] : "";
    $sku = isset($_GET['sku']) ? $_GET['sku'] : "";
    $location = isset($_GET['location']) ? $_GET['location'] : "";
    $shelf = isset($_GET['shelf']) ? $_GET['shelf'] : "";
    $tag = isset($_GET['tag']) ? $_GET['tag'] : "";
    $manufacturer = isset($_GET['manufacturer']) ? $_GET['manufacturer'] : "";

    $date = isset($_GET['date']) ? $_GET['date'] : '';

    $format = "Y-m-d";

    // Create a DateTime object from the string using the specified format
    $dateObj = DateTime::createFromFormat($format, $date);

    // Check if $dateObj is a valid DateTime object and matches the original string
    if ($dateObj !== false && $dateObj->format($format) === $date) {
        $date = date_format($dateObj, "Y-m-d");
    } else {
        $date = date('Y-m-d');
    }

    $area_array = [];

    if (isset($_GET['rows'])) {
        if ($_GET['rows'] == 50 || $_GET['rows'] == 100) {
            $rowSelectValue = $_GET['rows'];
        } else {
            $rowSelectValue = 20;
        }
    } else {
        $rowSelectValue = 20;
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
        










    $sql_inv_cte = "WITH QuantityCTE AS (
        SELECT
            stock_id,
            site_id,
            SUM(item_quantity) AS total_item_quantity
        FROM (
            SELECT
                item.stock_id,
                area.site_id,
                area.id AS area_id_global,
                SUM(quantity) AS item_quantity
            FROM
                item
                INNER JOIN shelf ON item.shelf_id = shelf.id
                INNER JOIN area ON shelf.area_id = area.id
            WHERE
                item.deleted = 0
            GROUP BY
                item.stock_id, area.site_id, area.id
        ) AS Subquery
        GROUP BY
            stock_id, site_id
    )";

    $sql_inv_count = $sql_inv_cte . "
    SELECT
        stock.id AS stock_id,
        stock.name AS stock_name,
        stock.description AS stock_description,
        stock.sku AS stock_sku,
        stock.min_stock AS stock_min_stock,
        stock.is_cable AS stock_is_cable,
        GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
        site.id AS site_id,
        site.name AS site_name,
        site.description AS site_description,
        COALESCE(cte.total_item_quantity, 0) AS item_quantity,
        tag_names.tag_names AS tag_names,
        tag_ids.tag_ids AS tag_ids,
        stock_img_image.stock_img_image,
        audit.id AS audit_id,
        audit.date AS audit_date,
        audit.comment AS audit_comment,
        audit.user_id AS audit_user_id,
        users.username AS users_username
    FROM
        stock
        LEFT JOIN item ON stock.id = item.stock_id
        LEFT JOIN shelf ON item.shelf_id = shelf.id
        LEFT JOIN area ON shelf.area_id = area.id
        LEFT JOIN site ON area.site_id = site.id
        LEFT JOIN manufacturer ON item.manufacturer_id = manufacturer.id
        LEFT JOIN (
            SELECT
                stock_img.stock_id,
                MIN(stock_img.image) AS stock_img_image
            FROM
                stock_img
            GROUP BY
                stock_img.stock_id
        ) AS stock_img_image ON stock_img_image.stock_id = stock.id
        LEFT JOIN (
            SELECT
                stock_tag.stock_id,
                GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names
            FROM
                stock_tag
                INNER JOIN tag ON stock_tag.tag_id = tag.id
            GROUP BY
                stock_tag.stock_id
        ) AS tag_names ON tag_names.stock_id = stock.id
        LEFT JOIN (
            SELECT
                stock_tag.stock_id,
                GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids
            FROM
                stock_tag
            GROUP BY
                stock_tag.stock_id
        ) AS tag_ids ON tag_ids.stock_id = stock.id
        LEFT JOIN QuantityCTE cte ON stock.id = cte.stock_id AND site.id = cte.site_id
        LEFT JOIN stock_audit AS audit ON audit.stock_id = stock.id
        LEFT JOIN users ON users.id = audit.user_id
    WHERE
        ( audit.id IS NULL OR audit.date < DATE_SUB(NOW(), INTERVAL 6 MONTH) OR audit.date = DATE('$date') )
        AND stock.is_cable = 0
        AND stock.deleted = 0";


    if ($site !== '0') {
        $sql_inv_count .= " AND site.id = $site";
    }

    if ($area !== '0') {
        $sql_inv_count .= " AND area.id = $area";
    }

    $sql_inv_count .= "
        GROUP BY
            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
            site_id, site_name, site_description, stock_img_image.stock_img_image, audit_date, audit_id, audit_user_id, audit_comment, users_username";

    if ($area != 0) {
        $sql_inv_count .= ", area.id";
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
                if ($total_pages > 5) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                            // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                        } elseif ($i == 1 && $current_page > 5) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                        } elseif ($i < $current_page && $i >= $current_page-2) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        } elseif ($i > $current_page && $i <= $current_page+2) {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        } elseif ($i == $total_pages) {
                            $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                        }
                    }
                } else {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                            // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                        } else {
                            $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                        }
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
            $results[-1]['tag'] = $tag;
            $results[-1]['manufacturer'] = $manufacturer;
            $results[-1]['total-pages'] = $total_pages;
            $results[-1]['page-number-area'] = $pageNumberArea;
            $results[-1]['page'] = $current_page;
            $results[-1]['rows'] = $rowSelectValue;
            $results[-1]['url'] = "./?oos=$showOOS&site=$site&area=$area&name=$name&sku=$sku&shelf=$shelf&manufacturer=$manufacturer&tag=$tag&page=$current_page&rows=$rowSelectValue";
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
                    $stock_tag_names = ($row['tag_names'] !== null) ? explode(", ", $row['tag_names']) : '---';
                    $stock_audit_id = $row['audit_id'];
                    $stock_audit_date = $row['audit_date'];
                    $stock_audit_comment = $row['audit_comment'];

                    $sql_audit = "SELECT id, date, comment
                                    FROM stock_audit
                                    WHERE stock_id = '$stock_id'
                                    ORDER BY id DESC
                                    LIMIT 1";
                    $stmt_audit = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_audit, $sql_audit)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_execute($stmt_audit);
                        $result_audit = mysqli_stmt_get_result($stmt_audit);
                        $auditRowCount = $result_audit->num_rows;
                        if ($auditRowCount > 0) {
                            $row_audit = $result_audit->fetch_assoc();
                            $audit_last_date = $row_audit['date'];
                            $audit_last_comment = 'Last: '.htmlspecialchars($row_audit['comment']);
                            $audit_last_id = $row_audit['id'];
                        } else {
                            $audit_last_date = "Never";
                            $audit_last_comment = $audit_last_id = '';
                        }
                    }
                    

                    // Echo each row (inside of SQL results)

                    $result =
                    '<tr class="vertical-align align-middle highlight" id="'.$stock_id.'">
                        <td class="align-middle" id="'.$stock_id.'-id" hidden><input type="hidden" name="audited" form="audit-'.$stock_id.'" value="'.$stock_id.'">'.$stock_id.'</td>
                        <td class="align-middle" id="'.$stock_id.'-img-td">
                        ';
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                        <td class="align-middle gold" id="'.$stock_id.'-name" style="white-space:wrap"><a class="link" href="./stock.php?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                        <td class="align-middle viewport-large-empty" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                        <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                    if ($stock_quantity_total == 0) {
                        $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';
                    if ($site == 0) { $result .= '<td class="align-middle link gold" style="white-space: nowrap !important;"id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'; }
                    $result .= '<td class="align-middle viewport-large-empty" style="white-space: wrap" id="'.$stock_id.'-tag">';
                    if (is_array($stock_tag_names)) {
                        for ($o=0; $o < count($stock_tag_names); $o++) {
                            $divider = $o < count($stock_tag_names)-1 ? ', ' : '';
                            $result .= '<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'tag\', \''.$stock_tag_names[$o].'\'))">'.$stock_tag_names[$o].'</or>'.$divider;
                        }
                    } 
                    
                    $checked = ''; // set to blank if checking is not needed
                    if (isset($stock_audit_date) && $stock_audit_date !== '' && $stock_audit_date !== 'Never') {
                        $datenow = $date; // date from the AJAX request - &date=YYYY-MM-DD

                        // Create DateTime objects for $stock_audit_date and $datenow
                        $stock_audit_date_obj = new DateTime($stock_audit_date);
                        $datenow_obj = new DateTime($datenow);
                        $date_6m = $datenow_obj->modify('-6 months');

                        // Compare $date with $date_6m
                        if ($stock_audit_date_obj >= $date_6m || $stock_audit_date_obj == $date_6m) {
                            $checked = 'checked';
                        } 
                    }
    
                    $result .= '</td>
                    <td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                    <td class="align-middle" style="border-left: 1px solid #454d55;">
                        <label class="switch" style="margin-bottom: 0px">
                            <input class="notified-toggle dbtoggle" id="audit-checkbox-'.$stock_id.'" type="checkbox" name="audited" '.$checked.'>
                            <span class="slider round" style="transform: scale(0.6, 0.6)"></span>
                        </label>
                    </td>
                    <td class="align-middle" id="audit-comment-'.$stock_id.'-'.$audit_last_id.'">'.$audit_last_date.'</td>
                    <td class="align-middle"><input id="audit-comment-'.$stock_id.'" type="text" style="width:100%" name="comments" placeholder="'.$audit_last_comment.'" class="form-control"/></td>
                    <td class="align-middle"><button name="audit-submit" id="audit-submit-'.$stock_id.'" class="btn btn-success" onclick="auditUpdate(\''.$stock_id.'\')">Save</button></td>
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
} elseif (isset($_GET['request-inventory-stock']) && $_GET['request-inventory-stock'] == 1) {
    if (isset($_GET['type'])) {
        $type = $_GET['type'];

        $results = [];

        $name = isset($_GET['name']) ? $_GET['name'] : "";

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

        $sql_inv_count = "WITH QuantityCTE AS (
                            SELECT
                                item.stock_id,
                                SUM(quantity) AS item_quantity
                            FROM
                                item
                                INNER JOIN shelf ON item.shelf_id = shelf.id
                                INNER JOIN area ON shelf.area_id = area.id
                            WHERE
                                item.deleted = 0
                            GROUP BY
                                item.stock_id
                        )
                        
                        SELECT
                            stock.id AS stock_id,
                            stock.name AS stock_name,
                            stock.description AS stock_description,
                            stock.sku AS stock_sku,
                            COALESCE(cte.item_quantity, 0) AS item_quantity,
                            stock_img_image.stock_img_image
                        FROM
                            stock
                            LEFT JOIN item ON stock.id = item.stock_id
                            LEFT JOIN shelf ON item.shelf_id = shelf.id
                            LEFT JOIN area ON shelf.area_id = area.id
                            LEFT JOIN site ON area.site_id = site.id
                            LEFT JOIN (
                                SELECT
                                    stock_img.stock_id,
                                    MIN(stock_img.image) AS stock_img_image
                                FROM
                                    stock_img
                                GROUP BY
                                    stock_img.stock_id
                            ) AS stock_img_image ON stock_img_image.stock_id = stock.id
                            LEFT JOIN QuantityCTE cte ON stock.id = cte.stock_id
                        WHERE
                            stock.is_cable = 0
                            AND stock.deleted = 0";
        if ($type !== "add") {
            $sql_inv_count .= " AND item.deleted = 0";
        } 

        if ($name !== '') {
            $name = mysqli_real_escape_string($conn, $name);
            $sql_inv_count .= " AND (MATCH(stock.name) AGAINST ('$name' IN NATURAL LANGUAGE MODE) 
                                        OR MATCH(stock.description) AGAINST ('$name' IN NATURAL LANGUAGE MODE) 
                                        OR stock.name LIKE CONCAT('%', '$name', '%')
                                    )";
        }

        $sql_inv_count .= " GROUP BY
                                stock.id, stock_name, stock_description, stock_sku, stock_img_image.stock_img_image";

        if ($type != "remove") {
            $sql_inv_count .= " HAVING item_quantity IS NULL OR item_quantity > 0 OR item_quantity = 0";
        }

        $sql_inv_count .= " ORDER BY
                                stock.name";

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
                    if ($total_pages > 5) {
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $current_page) {
                                $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                            } elseif ($i == 1 && $current_page > 5) {
                                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                            } elseif ($i < $current_page && $i >= $current_page-2) {
                                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                            } elseif ($i > $current_page && $i <= $current_page+2) {
                                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                            } elseif ($i == $total_pages) {
                                $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                            }
                        }
                    } else {
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $current_page) {
                                $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                            } else {
                                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                            }
                        }
                    }

                    if ($current_page < $total_pages) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
                    }  
                }
                $results[-1]['rows'] = $rowCount_inv;
                $results[-1]['sql'] = $sql_inv;
                $results[-1]['page-number-area'] = $pageNumberArea;
                $results[-1]['url'] = "./stock.php?modify=$type&name=$name&page=$current_page";

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
                    

                        // Echo each row (inside of SQL results)

                        $result =
                        '<tr class="clickable vertical-align align-middle" id="'.$stock_id.'" onclick="window.location.href=\'stock.php?modify='.$type.'&stock_id='.$stock_id.'\'">
                            <td class="align-middle" id="'.$stock_id.'-id">'.$stock_id.'</td>
                            <td class="align-middle" id="'.$stock_id.'-img-td">
                            ';
                        if (!is_null($stock_img_file_name)) {
                            $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                        }
                        $result .= '</td>
                            <td class="align-middle" id="'.$stock_id.'-name" style="white-space:wrap">'.$stock_name.'</td>
                            <td class="align-middle viewport-large-empty" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                            <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                        if ($stock_quantity_total == 0) {
                            $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                        } else {
                            $result .= $stock_quantity_total;
                        }
                        $result .= '</td>';
                        $results[] = $result;
                    }
                }
                // echo('<table><tbody>');
                // print_r('<pre>');
                // print_r($results);
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
}



?>