<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendEmail($to, $toName, $fromName, $subject, $body, $notif_id, $overrides=[])
    {
        $notif_id = $overrides['notif_id']       ?? $notif_id;
        $to       = $overrides['smtp_to_email']  ?? $to;
        $toName   = $overrides['smtp_to_name']   ?? $toName;
        $fromName = $overrides['smtp_from_name'] ?? $fromName;
        $subject  = $overrides['subject']        ?? $subject;
        $body     = $overrides['body']           ?? $body;

        try {
            // 1. Check if notification is enabled
            $notification = DB::table('notifications')->find($notif_id);
            if (!$notification || !$notification->enabled) {
                Log::warning("Notification $notif_id disabled or not found.");
                return false;
            }

            // 2. Get SMTP config (adjust table/column names if needed)
            $config = DB::table('config')->select(
                'smtp_host', 'smtp_port', 'smtp_encryption',
                'smtp_username', 'smtp_password',
                'smtp_from_email', 'smtp_from_name', 'smtp_to_email',
                'smtp_auth_type', 'smtp_oauth_provider',
                'smtp_client_id', 'smtp_client_secret', 'smtp_refresh_token'
            )->first();

            if (!$config) {
                throw new \Exception('No SMTP config found in database.');
            }

            // 3. Handle defaults
            if ($to === 'use-default') $to = $config->smtp_to_email;
            if ($toName === 'use-default') $toName = $config->smtp_to_email;
            if ($fromName === 'use-default') $fromName = $config->smtp_from_name;

            // 4. Setup PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPDebug = $overrides['debug'] ?? 0;
            $mail->Host = $overrides['smtp_host'] ?? $config->smtp_host;
            $mail->Port = $overrides['smtp_port'] ?? $config->smtp_port;

            // Encryption handling
            $encryption = $overrides['smtp_encryption'] ?? $config->smtp_encryption;
            switch ($encryption) {
                case 'none':
                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;
                    break;
                case 'starttls':
                    $mail->SMTPSecure = 'tls';
                    $mail->SMTPAutoTLS = true;
                    break;
                case 'tls':
                    $mail->SMTPSecure = 'tls';
                    break;
                case 'ssl':
                    $mail->SMTPSecure = 'ssl';
                    break;
            }

            // 5. Authentication (OAuth2 or Basic)
            $auth_type = $overrides['smtp_auth_type'] ?? $config->smtp_auth_type;
            if ($auth_type === 'oauth2') {
                $mail->AuthType = 'XOAUTH2';
                $oauth_provider = $overrides['smtp_oauth_provider'] ?? $config->smtp_oauth_provider;
                $provider = match ($oauth_provider) {
                    'google'    => new Google([
                        'clientId'     => $overrides['smtp_client_id'] ?? $config->smtp_client_id,
                        'clientSecret' => $overrides['smtp_client_secret'] ?? $config->smtp_client_secret,
                    ]),
                    'microsoft' => new Microsoft([
                        'clientId'     => $overrides['smtp_client_id'] ?? $config->smtp_client_id,
                        'clientSecret' => $overrides['smtp_client_secret'] ?? $config->smtp_client_secret,
                    ]),
                    default      => throw new \Exception('Invalid OAuth2 provider')
                };

                $mail->setOAuth(new OAuth([
                    'provider'     => $oauth_provider,
                    'clientId'     => $overrides['smtp_client_id']     ?? $config->smtp_client_id,
                    'clientSecret' => $overrides['smtp_client_secret'] ?? $config->smtp_client_secret,
                    'refreshToken' => $overrides['smtp_refresh_token'] ?? $config->smtp_refresh_token,
                    'userName'     => $overrides['smtp_from_email']    ?? $config->smtp_from_email,
                ]));
            } else {
                $mail->SMTPAuth = true;
                $mail->Username = $overrides['smtp_username'] ?? $config->smtp_username;
                $mail->Password = $overrides['smtp_password'] ?? base64_decode($config->smtp_password);
            }

            // 6. Send Email
            $mail->setFrom($overrides['smtp_from_email'] ?? $config->smtp_from_email, $fromName);
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            
            $send = $mail->send();
            if ($send) {
                if (isset($overrides['debug']) && $overrides['debug'] == 1) {
                    echo "\n221 - Test email sent successfully to $to!";
                }
                return $send;
            } else {
                if (isset($overrides['debug']) && $overrides['debug'] == 1) {
                    echo "\nTest email could not be sent. Error: " . $mail->ErrorInfo;
                }
                return $send;
            }
            
        } catch (Exception $e) {
            if (isset($overrides['debug']) && $overrides['debug'] == 1) {
                echo "\nTest email could not be sent. Error: " . $e->getMessage();
            }
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

}
