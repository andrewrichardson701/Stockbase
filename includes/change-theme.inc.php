<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if (isset($_GET['change']) && isset($_GET['theme_file_name']) && isset($_GET['value']) && isset($_GET['theme_name']) && isset($_GET['user-id'])) {

    session_start();
    $_SESSION['theme_id'] = htmlspecialchars($_GET['value']);
    $_SESSION['theme_name'] = htmlspecialchars($_GET['theme_name']);
    $_SESSION['theme_file_name'] = htmlspecialchars($_GET['theme_file_name']);

    $theme = htmlspecialchars($_GET['value']);

    include 'dbh.inc.php';
    include 'changelog.inc.php';

    // get current info
    $sql = "SELECT id, username, theme_id FROM users
                    WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $results[] = 'error';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $_GET['user-id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = $result->num_rows;
        
        $row = $result->fetch_assoc();

        $username = $row['username'];
        $old_theme = $row['theme_id'];

        //update info
        $sql_update = "UPDATE users SET theme_id='$theme' WHERE id=?";
        $stmt_update = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
            $results[] = 'error';
        } else {
            mysqli_stmt_bind_param($stmt_update, "s", $_GET['user-id']);
            mysqli_stmt_execute($stmt_update);
            
            // update changelog
            addChangelog($_GET['user-id'], $username, "Theme Change", "users", $_GET['user-id'], "theme", $old_theme, $theme);
            
            $results[] = 'success';
        }
    } 
    echo(json_encode($results));
} 