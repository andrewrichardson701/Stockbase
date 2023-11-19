<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

function addChangelog($user_id, $user_username, $action, $table_name, $record_id, $field_name, $value_old, $value_new) {
    $timestamp = date('Y-m-d H:i:s');
    include 'dbh.inc.php';

    $value_old = mysqli_real_escape_string($conn, $value_old); // escape the special characters
    $value_new = mysqli_real_escape_string($conn, $value_new); // escape the special characters
    $sql = "INSERT INTO changelog (timestamp, user_id, user_username, action, table_name, record_id, field_name, value_old, value_new) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        include 'smtp.inc.php';
        $sql_command = "INSERT INTO changelog (timestamp, user_id, user_username, action, table_name, record_id, field_name, value_old, value_new) VALUES ('$timestamp', $user_id, '$user_username', '$action', '$table_name', $record_id, '$field_name', '$value_old', '$value_new');";
        $email_subject = ucwords($current_system_name)." - Changelog update issue - please correct manually.";
        $email_body = "<p>Changelog update failed. Please correct this manually by adding the below:</p>
                        <table style='margin:auto'>
                            <thead>
                                <tr>
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
                                <tr>    
                                <td>$timestamp</td>
                                <td>$user_id</td>
                                <td>$user_username</td>
                                <td>$action</td>
                                <td>$table_name</td>
                                <td>$record_id</td>
                                <td>$field_name</td>
                                <td>$value_old</td>
                                <td>$value_new</td>
                            </tbody>
                        </table>
                        <br>
                        <p>Please contact an administrator, or use the below SQL to add this:</p>
                        <p style=\"font-family: Courier New,Courier,Lucida Sans Typewriter,Lucida Typewriter,monospace; \">$sql_command</p>
                        ";
        send_email($current_smtp_to_email, "Administrator", $config_smtp_from_name, $email_subject, createEmail($email_body), 0);
        error_log("Unable to update changelog. please add manually using: \"$sql_command\"\n");
    } else {
    mysqli_stmt_bind_param($stmt, "sssssssss", $timestamp, $user_id, $user_username, $action, $table_name, $record_id, $field_name, $value_old, $value_new);
    mysqli_stmt_execute($stmt);
    }
}










?>