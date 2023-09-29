<?php      
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
if (!class_exists('PHPMailer\PHPMailer\PHPMailer') || !class_exists('PHPMailer\PHPMailer\Exception') || !class_exists('PHPMailer\PHPMailer\SMTP')) {
    // The classes are not already included, so include the files
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
}


// required for variables
if (!isset($loggedin_username)) {
    include '../session.php';
}

include 'get-config.inc.php';

function send_email($to, $toName, $fromName, $subject, $body, $notif_id) {
    global $current_smtp_enabled;

    if ($current_smtp_enabled == 1) { // check if smtp is enabled.
        if (is_numeric($notif_id)) {

            // get folder info to get the dbh config
            $folder = dirname($_SERVER['PHP_SELF']);
            $folder = explode('/', $folder)[1]; // inventory folder

            include $_SERVER['DOCUMENT_ROOT'].'/'.$folder.'/includes/dbh.inc.php';

            // check if notification number exists (0 = general notifications)
            $sql_notif = "SELECT * FROM notifications WHERE id=$notif_id";
            $stmt_notif = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_notif, $sql_notif)) {
                error_log('send_email() SQL connection error to notifications table! notif_id = '.$notif_id.'. Sending Failed at '.date("H:i:s").' on '.date("d/m/Y").'.');
                exit();
            } else {
                mysqli_stmt_execute($stmt_notif);
                $result_notif = mysqli_stmt_get_result($stmt_notif);
                $rowCount_notif = $result_notif->num_rows;
                if ($rowCount_notif < 1) {
                    error_log('send_email() No rows found in notifications table for id = '.$notif_id.'. Sending Failed at '.date("H:i:s").' on '.date("d/m/Y").'.');
                    exit();
                } else {
                    $row_notif = $result_notif->fetch_assoc();

                    if ($row_notif['enabled'] == 1) {
                        // Get SMTP info from DB
                        $sql_config = "SELECT smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password, smtp_from_email, smtp_from_name, smtp_to_email 
                                        FROM config ORDER BY id LIMIT 1";
                        $stmt_config = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_config, $sql_config)) {
                            header("Location: ../admin.php?sqlerror=config_default_getEntries_ldap#ldap-settings");
                            exit();
                        } else {
                            mysqli_stmt_execute($stmt_config);
                            $result_config = mysqli_stmt_get_result($stmt_config);
                            $rowCount_config = $result_config->num_rows;
                            if ($rowCount_config < 1) {
                                header("Location: ../admin.php?sqlerror=config_default_noID1_ldap#ldap-settings");
                                exit();
                            } else {
                                while ( $config = $result_config->fetch_assoc() ) {
                                    $smtp_username   = $config['smtp_username'];       
                                    $smtp_password   = base64_decode($config['smtp_password']);      
                                    $smtp_encryption    = $config['smtp_encryption'];          
                                    $smtp_host       = $config['smtp_host'];            
                                    $smtp_port       = $config['smtp_port'];            
                                    $smtp_from_email     = $config['smtp_from_email'];          
                                    $smtp_from_name  = $config['smtp_from_name'];      
                                    $smtp_to_email = $config['smtp_to_email'];     
                                }

                                // if $to is set to use-default, then use the default email
                                if ($to == 'use-default') {
                                    $to = $smtp_to_email;
                                }
                                if ($toName == 'use-default') {
                                    $toName = $smtp_to_email;
                                }
                                if ($fromName == 'use-default') {
                                    $fromName = $smtp_from_name;
                                }

                                // Create a new PHPMailer instance
                                $mail = new PHPMailer();

                                // SMTP configuration
                                $mail->isSMTP();
                                $mail->SMTPDebug = 0;
                                $mail->Host = $smtp_host;  // SMTP server address
                                $mail->Port = $smtp_port;  // SMTP server port
                                switch($smtp_encryption) {
                                    case 'none':
                                        break;
                                    case 'starttls':
                                        $mail->SMTPSecure = 'tls';  // Encryption type (ssl or tls)
                                        $mail->SMTPAutoTLS = true; // starttls
                                        break;
                                    case 'tls':
                                        $mail->SMTPSecure = 'tls';  // Encryption type (ssl or tls)
                                        break;
                                    case 'ssl':
                                        $mail->SMTPSecure = 'ssl';  // Encryption type (ssl or tls)
                                        break;
                                    default:          
                                }
                                
                                if ($smtp_username !== '' && $smtp_password !== '') {
                                    $mail->SMTPAuth = true;  // Enable SMTP authentication
                                    $mail->Username = $smtp_username;  // SMTP username
                                    $mail->Password = $smtp_password;  // SMTP password
                                }

                                // Recipient and email details
                                $mail->setFrom($smtp_from_email, $fromName);
                                $mail->addAddress($to, $toName);
                                $mail->Subject = $subject;
                                $mail->Body = $body;
                                $mail->IsHTML(true);

                                // Send the email
                                if ($mail->send()) {
                                    echo "Email sent successfully to $to!";
                                } else {
                                    echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
                                }
                            }
                        }
                    } else {
                        // error_log('notification '.$notif_id.' disabled');
                    }
                }
            }
        } else {
            error_log('send_email() notif_id is not numeric! notif_id = '.$notif_id.'. Sending Failed at '.date("H:i:s").' on '.date("d/m/Y").'.');
        }
    }
}

$comp_banner_color = getWorB($current_banner_color);
$comp_url_color = getComplement($current_banner_color);

if (isset($override_email)) {
    $loggedin_email = $override_email;
    if (!isset($loggedin_firstname) || $loggedin_firstname == '') {
        $loggedin_firstname = $override_email;
    }
}

$email_template_start = '
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body style="font-family: \'Poppins\', sans-serif; padding-left:10vw; padding-right:10vw">
    <!-- inset block -->
    <div style="padding-top:20px; background-color: '.$current_banner_color.'; text-align: center;">
        <div style="text-align: center;padding-bottom:10px">
        <a href="//'.$current_base_url.'" style="color:'.$comp_url_color.';"><h1>'.ucwords($current_system_name).'</h1></a>
        </div>
        <div style="background-color:#e8e8e8; text-align: center;  padding-top:10px; padding-bottom:10px">
            <h2>Hello, '.ucwords($loggedin_firstname).'!</h2>
';

$email_template_end = '
            <p>Regards,<br><strong>'.$current_smtp_from_name.'</strong></p>
        </div>
        <div style="padding-top:10px; padding-bottom:20px;text-align: center;">
            <p style="font-size:14; color: '.$comp_banner_color.'">Copyright &copy; '.date("Y").' <a href="https://git.ajrich.co.uk/web/inventory" style="color:'.$comp_url_color.'">StockBase</a>. All rights reserved.</p>
        </div>
    </div>
</body>
';

function createEmail ($content) {
    global $email_template_start, $email_template_end;
    return $email_template_start.$content.$email_template_end;
}

// send_email($_GET['to'], $_GET['toName'], $_GET['fromName'], $_GET['subject'], createEmail(Semail_template_start, $email_content_test, $email_template_end), 0);

?>