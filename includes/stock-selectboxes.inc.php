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

?>