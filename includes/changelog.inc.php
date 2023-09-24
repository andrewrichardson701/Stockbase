<?php

function addChangelog($user_id, $user_username, $action, $table_name, $record_id, $field_name, $value_old, $value_new) {
    $timestamp = date('Y-m-d H:i:s');
    include 'dbh.inc.php';

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
                        <p>Use the below SQL to add this:</p>
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