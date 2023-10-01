<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$smtp_host = isset($_POST['smtp_host']) ? $_POST['smtp_host'] : '';
$smtp_port = isset($_POST['smtp_port']) ? $_POST['smtp_port'] : '';
$smtp_encryption = isset($_POST['smtp_encryption']) ? $_POST['smtp_encryption'] : '';
$smtp_username = isset($_POST['smtp_username']) ? $_POST['smtp_username'] : ''; 
$smtp_password = isset($_POST['smtp_password']) ? $_POST['smtp_password'] : '';
$smtp_from_email = isset($_POST['smtp_from_email']) ? $_POST['smtp_from_email'] : '';
$smtp_from_name = isset($_POST['smtp_from_name']) ? $_POST['smtp_from_name'] : '';
$smtp_to_email = isset($_POST['smtp_to_email']) ? $_POST['smtp_to_email'] : '';
// $smtp_host = 'mail.ajrich.co.uk';
// $smtp_port = '587';
// $smtp_encryption = 'starttls';
$ret = '';

if (isset($_POST['smtp_host']) && isset($_POST['smtp_port']) && isset($_POST['smtp_encryption'])) {
// if (isset($smtp_host)) {
    if ($smtp_encryption == 'starttls') {
        $host = $smtp_host;
        $port = $smtp_port;
        $timeout = 5;

        function get($socket,$length=1024){
            $send = '';
            $sr = fgets($socket,$length);
            while( $sr ){
                $send .= $sr;
                if( $sr[3] != '-' ){ break; }
                $sr = fgets($socket,$length);
            }
            return $send;
        }
        function put($socket,$cmd,$length=1024){
            fputs($socket,$cmd."\r\n",$length);
        }
        if (!($smtp = fsockopen($host, $port, $errno, $errstr, $timeout))) {
            die("Error: Unable to connect");
        }
        // echo "<pre>\n";
        echo get($smtp); // should return a 220 if you want to check
        
        $cmd = "EHLO ${_SERVER['HTTP_HOST']}";
        echo $cmd."\r\n";
        put($smtp,$cmd);
        echo get($smtp); // 250
        
        $cmd = "STARTTLS";
        echo $cmd."\r\n";
        put($smtp,$cmd);
        echo get($smtp); // 220
        if(false == stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)){
            // fclose($smtp); // unsure if you need to close as I haven't run into a security fail at this point
            die("Error: Unable to start tls encryption");
        }
        
        $cmd = "EHLO ".$_SERVER['HTTP_HOST'];
        echo $cmd;
        put($smtp,$cmd);
        echo get($smtp); // 250
        
        $cmd = "QUIT";
        echo $cmd."\r\n";
        put($smtp,$cmd);
        echo get($smtp);
        // echo "</pre>";
        
        fclose($smtp);
    } else {
        $host = $smtp_encryption.'://'.$smtp_host;
        $port = $smtp_port;
        $errorNumber;
        $error;
        $timeout = 5;
        $enableLog = true;
        $logFile = 'smtp_tester.log';
        $now = new \DateTime('now');

        if ($enableLog) {
            $fp = fopen($logFile, 'a');
        }

        $ret = '<p>Host: ' . $host . ', Port: ' . $port . ', Timeout: ' . $timeout . '</p>';

        $mTime = microtime(true);
        $connection = fsockopen($host, $port, $errorNumber, $error, $timeout);
        if (!$connection) {
            echo '<p>Connection ERROR</p>';
            echo '<p>Error no.: ' . $errorNumber . '</p>';
            echo '<p>Error: ' . $error . '</p>';
            if ($enableLog) fwrite($fp, $now->format('d.m.Y H:i:s') . ' ERROR ' . $errorNumber . ' ' . $error . chr(10));
        } else {
            echo '<p>Connection established</p>';
            if ($enableLog) fwrite($fp, $now->format('d.m.Y H:i:s') . ' SUCCESS Connection established' . chr(10));
            $res = fgets($connection, 256);
            echo '<p>Welcome res: ' . $res . '</p>';
            if (substr($res, 0, 3) !== '220') {
                echo 'Error. Status has to be 220';
                if ($enableLog) fwrite($fp, $now->format('d.m.Y H:i:s') . ' ERROR Welcome status <> 220' . chr(10));
            }

            fputs($connection, "HELO " . $_SERVER['HTTP_HOST'] . "\n");
            $res = fgets($connection,256);
            echo '<p>HELO res: ' . $res . '</p>';
            if (substr($res, 0, 3) !== '250') {
                echo 'Error. HELO was not responded with status 250';
                if ($enableLog) fwrite($fp, $now->format('d.m.Y H:i:s') . ' ERROR HELO status <> 250' . chr(10));
            }

            fputs($connection, "QUIT\n");
            $res = fgets($connection, 256);
            echo '<p>QUIT res: ' . $res . '</p>';
            if (substr($res, 0, 3) !== '221') {
                echo 'Error. QUIT was not responded with status 221';
                if ($enableLog) fwrite($fp, $now->format('d.m.Y H:i:s') . ' ERROR QUIT status <> 221' . chr(10));
            }
        }

        echo '<p>Dump SMTP connection</p><pre>';
        var_dump($connection);
        echo '</pre>';

        fclose($connection);
        echo '<p>Execution time: ' . (microtime(true) - $mTime) . '</p>';
        if ($enableLog) fclose($fp);
    }  
    
    


    if (isset($_POST['smtp_username']) && isset($_POST['smtp_password']) && isset($_POST['smtp_from_email']) && isset($_POST['smtp_from_name']) && isset($_POST['smtp_to_email'])) {
        include 'smtp.inc.php';
        $email_content_test = "<p>This is a test of the Inventory mail system. <br>You're all set!</p>";
        function send_test_email($to, $toName, $from, $fromName, $subject, $body, $host, $port, $encryption, $username, $password) {
            
            // Create a new PHPMailer instance
            $mail = new PHPMailer();

            // SMTP configuration
            $mail->isSMTP();
            $mail->SMTPDebug = 1;
            $mail->Host = $host;  // SMTP server address
            $mail->Port = $port;  // SMTP server port
            switch($encryption) {
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
            
            if ($username !== '' && $password !== '') {
                $mail->SMTPAuth = true;  // Enable SMTP authentication
                $mail->Username = $username;  // SMTP username
                $mail->Password = $password;  // SMTP password
            }

            // Recipient and email details
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to, $toName);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->IsHTML(true);

            // Send the email
            if ($mail->send()) {
                echo "\n221 - Test email sent successfully to $to!";
            } else {
                echo "\nTest email could not be sent. Error: " . $mail->ErrorInfo;
            }
        }

        $testBody = $email_template_start.$email_content_test.$email_template_end;

        send_test_email($smtp_to_email, $smtp_to_email, $smtp_from_email, $smtp_from_name, ucwords($current_system_name).' SMTP Test', $testBody, $smtp_host, $smtp_port, $smtp_encryption, $smtp_username, $smtp_password);
        // send_email($smtp_to_email, $smtp_from_email, $smtp_from_name, 'Inventory SMTP Test', 'email test body', 0);
    }

    
}