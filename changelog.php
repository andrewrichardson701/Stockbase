<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

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
        $sql = "SELECT * FROM changelog ORDER BY id DESC, timestamp DESC";
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
                            <tr class="align-middle text-center">
                                <td class="align-middle text-center" colspan=100%>
                                    <table class="centertable">
                                    ');
                                    $table_name = $row['table_name'];
                                    $record_id = $row['record_id'];
                                    $user_id = $row['user_id'];

                                    $sql_user = "SELECT * FROM users WHERE id=$user_id";
                                    $stmt_user = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_user, $sql_user)) {
                                        echo("<p class='red'>Error reaching changelog table</p>");
                                    } else {
                                        mysqli_stmt_execute($stmt_user);
                                        $result_user = mysqli_stmt_get_result($stmt_user);
                                        $rowCount_user = $result_user->num_rows;
                                        if ($rowCount_user < 1) {
                                            echo("<tr><td colspan=100%>User not found</td></tr>");
                                        } else {
                                            $row_user = $result_user->fetch_assoc();
                                            $username = $row_user['username'];
                                            

                                            $sql_table = "SELECT COLUMN_NAME
                                                            FROM INFORMATION_SCHEMA.COLUMNS
                                                            WHERE TABLE_SCHEMA = 'inventory' 
                                                                AND TABLE_NAME='$table_name';";
                                            $stmt_table = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_table, $sql_table)) {
                                                echo("<p class='red'>Error reaching changelog table</p>");
                                            } else {
                                                mysqli_stmt_execute($stmt_table);
                                                $result_table = mysqli_stmt_get_result($stmt_table);
                                                $rowCount_table = $result_table->num_rows;
                                                if ($rowCount_table < 1) {
                                                    echo("<tr><td colspan=100%>Table: $table_name not found.</td></tr>");
                                                } else {
                                                    $column_names = [];
                                                    while ($row_table = $result_table->fetch_assoc()) {
                                                        $column_names[] = $row_table['COLUMN_NAME'];
                                                    }

                                                    if (!empty($column_names) && count($column_names)>0) {

                                                        $sql_record = "SELECT * FROM $table_name WHERE id=$record_id";
                                                        $stmt_record = mysqli_stmt_init($conn);
                                                        if (!mysqli_stmt_prepare($stmt_record, $sql_record)) {
                                                            echo("<p class='red'>Error reaching changelog table</p>");
                                                        } else {
                                                            mysqli_stmt_execute($stmt_record);
                                                            $result_record = mysqli_stmt_get_result($stmt_record);
                                                            $rowCount_record = $result_record->num_rows;
                                                            if ($rowCount_record < 1) {
                                                                echo("<tr><td colspan=100%>Record not found.</td></tr>");
                                                            } else {
                                                                $row_record = $result_record->fetch_assoc();

                                                                echo('
                                                                    <thead>
                                                                        <tr class="align-middle text-center">
                                                                        ');
                                                                        foreach($column_names as $column) {
                                                                            echo('<th>'.$column.'</th>');
                                                                        }
                                                                        echo('
                                                                        </tr>
                                                                    </thead>
                                                                ');
                                                                echo('
                                                                    <tbody>
                                                                        <tr>
                                                                        ');
                                                                        foreach($column_names as $column2) {
                                                                            echo('<td>'.$row_record[$column2].'</td>');
                                                                        }
                                                                        echo('
                                                                        </tr>
                                                                    </tbody>
                                                                ');
                                                            }
                                                        }
                                                    }
                                                }
                                            }                                          
                                        }
                                    }
                                    echo('  
                                    </table>
                                </td>
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
