<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if (isset($_GET['getsites'])) {
    if (is_numeric($_GET['getsites'])) {
        if ($_GET['getsites'] == 1) {

            $sites = [];

            include 'dbh.inc.php';
            $sql = "SELECT id, name
                    FROM site
                    WHERE deleted=0
                    ORDER BY id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                // fails to connect
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    // no rows found
                } else {
                    // rows found
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $sites[] = array('id' => $id, 'name' => $name);
                    }
                    echo(json_encode($sites));
                }
            }

        } elseif ($_GET['getsites'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['site'])) {
    if (is_numeric($_GET['site'])) {
        if ($_GET['site'] > 0) {

            $site = $_GET['site'];

            $areas = [];

            include 'dbh.inc.php';
            $sql = "SELECT id, name
                    FROM area
                    WHERE site_id=? AND deleted=0
                    ORDER BY id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                // fails to connect
            } else {
                mysqli_stmt_bind_param($stmt, "s", $site);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    // no rows found
                } else {
                    // rows found
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $areas[] = array('id' => $id, 'name' => $name);
                    }
                    echo(json_encode($areas));
                }
            }

        } elseif ($_GET['site'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['area'])) {
    if (is_numeric($_GET['area'])) {
        if ($_GET['area'] > 0) {

            $area = $_GET['area'];

            $shelves = [];

            include 'dbh.inc.php';
            $sql = "SELECT id, name
                    FROM shelf
                    WHERE area_id=? AND deleted=0
                    ORDER BY id";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                // fails to connect
            } else {
                mysqli_stmt_bind_param($stmt, "s", $area);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                if ($rowCount < 1) {
                    // no rows found
                } else {
                    // rows found
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $shelves[] = array('id' => $id, 'name' => $name);
                    }
                    echo(json_encode($shelves));
                }
            }

        } elseif ($_GET['area'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['container-shelf'])) {
    if (is_numeric($_GET['container-shelf'])) {
        if ($_GET['container-shelf'] > 0) {

            $shelf_id = $_GET['container-shelf'];

            $containers = [];

            include 'dbh.inc.php';
            $sql_near = "SELECT c.id AS c_id, c.name AS c_name
                            FROM container AS c
                            WHERE c.shelf_id = $shelf_id AND c.deleted=0";
            $stmt_near = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_near, $sql_near)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_near);
                $result_near = mysqli_stmt_get_result($stmt_near);
                $rowCount_near = $result_near->num_rows;
                $siteCount = $rowCount_near;
                if ($rowCount_near > 0) {
                    while ($row_near = $result_near->fetch_assoc()) {
                        $c_id = $row_near['c_id'];
                        $c_name = $row_near['c_name'];

                        $c_info = array('id' => $c_id, 'name' => $c_name);
                        $containers['container'][] = $c_info;
                    }
                }
            }
            
            $sql_near = "SELECT i.id AS i_id, s.id AS s_id, s.name AS s_name
                            FROM item AS i
                            INNER JOIN shelf AS sh ON sh.id = i.shelf_id
                            INNER JOIN stock AS s ON s.id = i.stock_id
                            WHERE i.deleted=0 AND sh.id=$shelf_id AND i.is_container=1";
            $stmt_near = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_near, $sql_near)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_near);
                $result_near = mysqli_stmt_get_result($stmt_near);
                $rowCount_near = $result_near->num_rows;
                $siteCount = $rowCount_near;
                if ($rowCount_near > 0) {
                    while ($row_near = $result_near->fetch_assoc()) {
                        $c_id = $row_near['i_id'];
                        $c_name = $row_near['s_name'];
                        
                        $c_info = array('id' => $c_id, 'name' => $c_name);
                        $containers['item_container'][] = $c_info;
                    }
                }
            }
            echo(json_encode($containers));

        } elseif ($_GET['area'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];

    if ($type !== "site" ) {
        $output = [];

        if ($type == "area") { 
            $table = "site"; 
        } elseif ($type == "shelf") {
            $table = "area";
        }

        include 'dbh.inc.php';
        $sql = "SELECT id, name
                FROM $table WHERE deleted=0
                ORDER BY id";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            // fails to connect
        } else {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                // no rows found
            } else {
                // rows found
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $name = $row['name'];
                    $output[] = array('id' => $id, 'name' => $name);
                }
                echo(json_encode($output));
            }
        }
    } else {
        echo "";
    }
}

if (isset($_GET['getserials'])) {
    if (is_numeric($_GET['getserials'])) {
        if ($_GET['getserials'] == 1) {
            if (isset($_GET['shelf']) && isset($_GET['stock']) && isset($_GET['container'])) {
                $serials = [];

                $containerNum = 0;
                if ($_GET['shelf'] < 0) {
                    $containerNum = 1;
                    $_GET['shelf'] = $_GET['shelf'] *-1;
                }

                include 'dbh.inc.php';

                $manu = '';
                if (isset($_GET['manufacturer'])) {
                    $manufacturer = $_GET['manufacturer'];
                    $manu = " AND i.manufacturer_id=$manufacturer ";
                }
                $cont = '';
                if (isset($_GET['container']) && $_GET['container'] != 0) {
                    $container = $_GET['container'];
                    $cont = " INNER JOIN item_container AS ic ON i.id=ic.item_id AND ic.container_id=$container ";
                }

                // empty serial
                $sql = "SELECT DISTINCT i.serial_number AS serial_number
                        FROM item AS i

                        $cont

                        WHERE i.shelf_id=?
                        AND (i.serial_number = null 
                            OR i.serial_number = '')
                        AND i.deleted=0 AND i.stock_id=?
                        AND i.is_container=$containerNum
                        $manu
                        ORDER BY serial_number";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $_GET['shelf'], $_GET['stock']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        // no rows found
                    } else {
                        // rows found
                        while ($row = $result->fetch_assoc()) {
                            $id = 0;
                            $serial_number = $row['serial_number'];
                            $serials[] = array('id' => $id, 'serial_number' => $serial_number);
                        }
                    }
                }

                // serial
                $sql = "SELECT DISTINCT i.id AS id, i.serial_number AS serial_number
                        FROM item AS i

                        $cont

                        WHERE i.shelf_id=?
                        AND (i.serial_number != null 
                            OR i.serial_number != '') 
                        AND i.deleted=0 AND i.stock_id=?
                        $manu
                        ORDER BY serial_number";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $_GET['shelf'], $_GET['stock']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        // no rows found
                    } else {
                        // rows found
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            $serial_number = $row['serial_number'];
                            $serials[] = array('id' => $id, 'serial_number' => $serial_number);
                        }
                    }
                }
                echo(json_encode($serials));
            } else {

            }
        } elseif ($_GET['getserials'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['getcontainers'])) {
    if (is_numeric($_GET['getcontainers'])) {
        if ($_GET['getcontainers'] == 1) {
            if (isset($_GET['shelf']) && isset($_GET['stock']) && isset($_GET['manufacturer'])) {
                $containers = [];

                $containerNum = 0;
                if ($_GET['shelf'] < 0) {
                    $containerNum = 1;
                    $_GET['shelf'] = $_GET['shelf'] *-1;
                }

                include 'dbh.inc.php';

                // get total item count for the shelf
                $sql_1 = "SELECT i.id
                        FROM item AS i 
                        WHERE i.deleted=0 AND i.shelf_id=? AND i.stock_id=? AND i.manufacturer_id=? AND i.is_container=?";
                $stmt_1 = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_1, $sql_1)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt_1, "ssss", $_GET['shelf'], $_GET['stock'], $_GET['manufacturer'], $containerNum);
                    mysqli_stmt_execute($stmt_1);
                    $result_1 = mysqli_stmt_get_result($stmt_1);
                    $totalCount = $result_1->num_rows;

                    // in containers count
                    $sql = "SELECT c.id AS c_id, c.name AS c_name, item_c.id AS item_d_id, item_c_stock.id AS item_c_stock_id, item_c_stock.name AS item_c_stock_name
                            FROM item_container AS ic
                            INNER JOIN item AS i ON i.id=ic.item_id AND i.is_container=$containerNum

                            LEFT JOIN container AS c ON ic.container_id=c.id AND ic.container_is_item=0
                            LEFT JOIN item AS item_c ON item_c.id=ic.container_id AND ic.container_is_item=1
                            LEFT JOIN stock AS item_c_stock ON item_c_stock.id=item_c.stock_id 

                            WHERE i.deleted=0 AND i.shelf_id=? AND i.stock_id=? AND i.manufacturer_id=? 
                            ORDER BY c_name, item_c_stock_name";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        // fails to connect
                    } else {
                        mysqli_stmt_bind_param($stmt, "sss", $_GET['shelf'], $_GET['stock'], $_GET['manufacturer']);
                        mysqli_stmt_execute($stmt);
                        $resultCont = mysqli_stmt_get_result($stmt);
                        $rowCountCont = $resultCont->num_rows;
                        
                        if ($rowCountCont < $totalCount) {
                            $containers[] = array('container_id' => 0, 'container_name' => '');
                        }
                    }

                    $sql = "SELECT DISTINCT c.id AS c_id, c.name AS c_name, item_c.id AS item_d_id, item_c_stock.id AS item_c_stock_id, item_c_stock.name AS item_c_stock_name
                            FROM item_container AS ic
                            INNER JOIN item AS i ON i.id=ic.item_id AND i.is_container=$containerNum

                            LEFT JOIN container AS c ON ic.container_id=c.id AND ic.container_is_item=0
                            LEFT JOIN item AS item_c ON item_c.id=ic.container_id AND ic.container_is_item=1
                            LEFT JOIN stock AS item_c_stock ON item_c_stock.id=item_c.stock_id 

                            WHERE i.deleted=0 AND i.shelf_id=? AND i.stock_id=? AND i.manufacturer_id=? 
                            ORDER BY c_name, item_c_stock_name";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        // fails to connect
                    } else {
                        mysqli_stmt_bind_param($stmt, "sss", $_GET['shelf'], $_GET['stock'], $_GET['manufacturer']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;

                        if ($rowCount < 1) {
                            // no rows found
                        } else {
                            // rows found
                            while ($row = $result->fetch_assoc()) {
                                $container_id = is_null($row['item_c_stock_id']) ? $row['c_id'] : ((int)$row['c_id'])*-1;
                                $container_name = !is_null($row['c_name']) ? $row['c_name'] : $row['item_c_stock_name'];
                                // *-1 if item
                                $containers[] = array('container_id' => $container_id, 'container_name' => $container_name);
                            }
                        }
                    }

                    echo(json_encode($containers));
                    }
            } else {

            }
        } elseif ($_GET['getserials'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['getremoveshelves'])) {
    if (is_numeric($_GET['getremoveshelves'])) {
        if ($_GET['getremoveshelves'] == 1) {
            if (isset($_GET['manufacturer']) && isset($_GET['stock'])) {
                $locations = [];
                //'.$temp_data['site_name'].' - '.$temp_data['area_name'].' - '.$temp_data['shelf_name'].'
                include 'dbh.inc.php';

                $sql = "SELECT DISTINCT i.id AS i_id, item.shelf_id AS item_shelf_id, shelf.name AS shelf_name, area.name AS area_name, site.name AS site_name, item.manufacturer_id AS item_manufacturer_id, item.is_container AS item_is_container
                        FROM item
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        INNER JOIN site ON area.site_id=site.id
                        LEFT JOIN item AS i ON item.id=i.id AND item.is_container = 1
                        WHERE item.manufacturer_id=? AND item.deleted=0 AND item.stock_id=?
                        ORDER BY item.shelf_id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $_GET['manufacturer'], $_GET['stock']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    if ($rowCount < 1) {
                        // no rows found
                    } else {
                        // rows found
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['item_shelf_id'];
                            $location = $row['site_name'].' - '.$row['area_name'].' - '.$row['shelf_name'];
                            $i_id = $row['i_id'];
                            if (isset($row['item_is_container']) && $row['item_is_container'] == 1) {
                                $id = $id *-1;
                                $location = $location.' (container, ID: '.$i_id.')';
                            }
                            
                            $locations[] = array('id' => $id, 'location' => $location, 'item_id' => $i_id);
                        }
                        echo(json_encode($locations));
                    }
                }
                
            } else {

            }
        } elseif ($_GET['getremoveshelves'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['getquantity'])) {
    if (is_numeric($_GET['getquantity'])) {
        if ($_GET['getquantity'] == 1) {
            if (isset($_GET['manufacturer']) && isset($_GET['shelf']) && isset($_GET['serial']) && isset($_GET['stock'])) {
                if (isset($_GET['container'])) {
                    $container = (int)$_GET['container'];
                } else {
                    $container = 0;
                }

                if ($_GET['serial'] == 0 || $_GET['serial'] == null) {
                    $serial = '';
                } else { 
                    $serial = $_GET['serial'];
                }
                
                $quantityArr = [];
                include 'dbh.inc.php';

                if ($container !== 0) {
                    $cont = " INNER JOIN item_container ON item.id = item_container.item_id AND item_container.container_id = $container ";
                } else {
                    $cont = '';
                }
                
                $containerNum = 0;
                if ($_GET['shelf'] < 0) {
                    $_GET['shelf'] = $_GET['shelf'] *-1;
                    $containerNum = 1;
                }

                $sql = "SELECT * 
                        FROM item
                        $cont
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        INNER JOIN site ON area.site_id=site.id
                        WHERE item.manufacturer_id=? AND item.shelf_id=? AND item.serial_number=? AND item.deleted=0 AND item.stock_id=? AND item.is_container=?
                        ORDER BY item.shelf_id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "sssss", $_GET['manufacturer'], $_GET['shelf'], $serial, $_GET['stock'], $containerNum);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;

                    $id = 0;
                    $quantity = $rowCount;
                    $quantityArr[$id] = array('id' => $id, 'quantity' => $quantity);
                }

                echo(json_encode($quantityArr));
            } else {

            }
        } elseif ($_GET['getquantity'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

if (isset($_GET['getquantitycable'])) {
    if (is_numeric($_GET['getquantitycable'])) {
        if ($_GET['getquantitycable'] == 1) {
            if (isset($_GET['shelf']) && isset($_GET['stock'])) {
                $quantityArr = [];
                include 'dbh.inc.php';

                $sql = "SELECT * 
                        FROM cable_item
                        INNER JOIN shelf ON cable_item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        INNER JOIN site ON area.site_id=site.id
                        WHERE cable_item.shelf_id=? AND cable_item.deleted=0 AND cable_item.stock_id=?
                        ORDER BY cable_item.shelf_id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $_GET['shelf'], $_GET['stock']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;
                    $row = $result->fetch_assoc();
                    $quantity = $row['quantity'];
                    $id = 0;
                    $quantityArr[$id] = array('id' => $id, 'quantity' => $quantity);

                    echo(json_encode($quantityArr));
                }
                
            } else {

            }
        } elseif ($_GET['getquantitycable'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

?>