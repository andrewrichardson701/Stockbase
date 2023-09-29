<?php


include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Changelog</title>
</head>
<body>
    <?php // dependency PHP    
    // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
    if (!in_array($_SESSION['role'], $config_admin_roles_array)) {
        header("Location: ./login.php");
        exit();
    }
    ?>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Changelog</h2>
    </div>

    <div class="content">
        <?php 
        include 'includes/dbh.inc.php';
        $sql = "SELECT * FROM changelog ORDER BY timestamp DESC";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo("<p class='red'>Error reaching changelog table</p>");
        } else {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                echo("<p>No entries found.</p>");
            } else {
                ?>
                <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr class="theme-tableOuter">
                            <th>id</th>
                            <th>timestamp</th>
                            <th>user_id</th>
                            <th>user_username</th>
                            <th>action</th>
                            <th>table_name</th>
                            <th>record_id</th>
                            <th>field_name</th>
                            <th>value_old</th>
                            <th>value_new</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo('
                            <tr>
                                <td>'.$row['id'].'</td>
                                <td>'.$row['timestamp'].'</td>
                                <td>'.$row['user_id'].'</td>
                                <td>'.$row['user_username'].'</td>
                                <td>'.$row['action'].'</td>
                                <td>'.$row['table_name'].'</td>
                                <td>'.$row['record_id'].'</td>
                                <td>'.$row['field_name'].'</td>
                                <td>'.$row['value_old'].'</td>
                                <td>'.$row['value_new'].'</td>
                            </tr>
                            ');
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>
    </div>

    <?php include 'foot.php'; ?>

</body>
