<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

$return = [];
if (isset($_POST['credentials_check'])) {
    include 'get-config.inc.php'; // global config stuff
    include 'session.inc.php'; // session management stuff
    // csrf_token management
    if (isset($_POST['csrf_token'])) {
        if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
            $return['error'] = 'CSRF Token missmatch';
            echo(json_encode($return));
            exit();
        }
    } else {
        $return['error'] = 'CSRF Token missing';
        echo(json_encode($return));
        exit();
    }
    if (!isset($_POST['type']) || !isset($_POST['data'])) {
        $return['error'] = 'Missing data';
    } else {
        $type = $_POST['type'];
        $data = $_POST['data'];
        // do the SQL checking bits 
        $sql = "SELECT * FROM users WHERE $type = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $return['error'] = 'SQL Error';
            echo(json_encode($return));
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $data);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            $return['success'] = 1;
            if ($rowCount > 0) {
                $return['match'] = 1;
            } else {
                $return['match'] = 0;
            }
        }
    } 
} else {
    $return['error'] = "No Submit";
    echo(json_encode($return));
    exit();
}
echo json_encode($return);
?>