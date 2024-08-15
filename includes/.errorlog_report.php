<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (!class_exists('PHPMailer\PHPMailer\PHPMailer') || !class_exists('PHPMailer\PHPMailer\Exception') || !class_exists('PHPMailer\PHPMailer\SMTP')) {
    // The classes are not already included, so include the files
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
}

$to = 'Andrew@custodiandc.com';
$toName = 'Andrew';
$from = 'stockbase@boycie.cdc.local';
$fromName = 'stock.cdc.local';
$subject = "Stockbase Error Log - " . date('Y-m-d');

function send_email($to, $toName, $from, $fromName, $subject, $body) {

    $smtp_username   = '';       
    $smtp_password   = '';
	$smtp_encryption = 'none';          
	$smtp_host       = 'mail.cdc.local';            
	$smtp_port       = 25;            
	$smtp_from_email = $from;
	
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

//send_email('andrew@custodiandc.com', 'Andrew', 'root@boycie.cdc.local', 'Root', 'Test', 'TESTING');

// Function to get today's error log
function getErrorLogToday($file) {
    // Get today's date
    $today = date('Y/m/d');

    // Read nginx error log
    $errorLog = file($file);

    // Filter log entries for today
    $todayLogEntries = array_filter($errorLog, function($entry) use ($today) {
        return strpos($entry, $today) !== false;
    });

    return implode("<br><br>", $todayLogEntries);
}

function sendErrorLog($file, $subject_suffix) {
	global $to, $toName, $from, $fromName, $subject;
	$body = getErrorLogToday($file);
	$formatted_subject = $subject.' - '.$subject_suffix;

	if (!empty($body)) {
		// Send email with today's error log
		send_email($to, $toName, $from, $fromName, $formatted_subject, $body);
	} else {
		echo "No Errors found, no send needed.\n";
	}
}

sendErrorLog('/var/log/nginx/error-stock.log', 'local');
sendErrorLog('/var/log/nginx/error-stock_pub.log', 'public');

?>