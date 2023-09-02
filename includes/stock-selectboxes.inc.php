<?php
if (isset($_GET['getsites'])) {
    if (is_numeric($_GET['getsites'])) {
        if ($_GET['getsites'] == 1) {

            $sites = [];

            include 'dbh.inc.php';
            $sql = "SELECT id, name
                    FROM site
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
                    WHERE site_id=?
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
                    WHERE area_id=?
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
                FROM $table
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
            if (isset($_GET['shelf']) && isset($_GET['stock'])) {
                $serials = [];

                include 'dbh.inc.php';

                // empty serial
                $sql = "SELECT DISTINCT serial_number
                        FROM item
                        WHERE shelf_id=?
                        AND (serial_number = null 
                            OR serial_number = '')
                        AND item.deleted=0 AND item.stock_id=?
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
                $sql = "SELECT DISTINCT id, serial_number
                        FROM item
                        WHERE shelf_id=?
                        AND (serial_number != null 
                            OR serial_number != '') 
                        AND item.deleted=0 AND item.stock_id=?
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


if (isset($_GET['getremoveshelves'])) {
    if (is_numeric($_GET['getremoveshelves'])) {
        if ($_GET['getremoveshelves'] == 1) {
            if (isset($_GET['manufacturer']) && isset($_GET['stock'])) {
                $locations = [];
                //'.$temp_data['site_name'].' - '.$temp_data['area_name'].' - '.$temp_data['shelf_name'].'
                include 'dbh.inc.php';

                $sql = "SELECT DISTINCT item.shelf_id AS item_shelf_id, shelf.name AS shelf_name, area.name AS area_name, site.name AS site_name, item.manufacturer_id AS item_manufacturer_id
                        FROM item
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        INNER JOIN site ON area.site_id=site.id
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
                            $locations[] = array('id' => $id, 'location' => $location);
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

                if ($_GET['serial'] == 0) {
                    $serial = '';
                } else { 
                    $serial = $_GET['serial'];
                }
                
                $quantityArr = [];
                include 'dbh.inc.php';

                $sql = "SELECT * 
                        FROM item
                        INNER JOIN shelf ON item.shelf_id=shelf.id
                        INNER JOIN area ON shelf.area_id=area.id
                        INNER JOIN site ON area.site_id=site.id
                        WHERE item.manufacturer_id=? AND item.shelf_id=? AND item.serial_number=? AND item.deleted=0 AND item.stock_id=?
                        ORDER BY item.shelf_id";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    // fails to connect
                } else {
                    mysqli_stmt_bind_param($stmt, "ssss", $_GET['manufacturer'], $_GET['shelf'], $serial, $_GET['stock']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = $result->num_rows;

                    $id = 0;
                    $quantity = $rowCount;
                    $quantityArr[$id] = array('id' => $id, 'quantity' => $quantity);

                    echo(json_encode($quantityArr));
                }
                
            } else {

            }
        } elseif ($_GET['getquantity'] == 0) {

        } else {

        }
    } else {
        // not numeric
    }
}

?>