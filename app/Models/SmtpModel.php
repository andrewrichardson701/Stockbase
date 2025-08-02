<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Services\EmailService;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
use Illuminate\Support\Facades\Log;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpModel query()
 * @mixin \Eloquent
 */
class SmtpModel extends Model
{
    //
    static public function emailHead($test=null) 
    {
        if (!$test || $test == null) {
            $head = '
            <head>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
                <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
            </head>
            <body style="font-family: \'Poppins\', sans-serif; padding-left:10vw; padding-right:10vw">';
        } else {
            $head = '
            <div style="font-family: \'Poppins\', sans-serif; padding-left:10vw; padding-right:10vw; background-color:white;" class="theme-divBg-m">';
        }

        return $head;
    }

    static public function emailBodyTop($test=null)
    {
        $config = GeneralModel::configCompare();
        $comp_url_color = FunctionsModel::getComplement($config['banner_color']);
        $user = GeneralModel::getUser();

        $bodyTop = '
            <!-- inset block -->
            <div style="padding-top:20px; background-color: '.$config['banner_color'].'; text-align: center;">
                <div style="text-align: center;padding-bottom:10px">
                <a href="https://'.$config['base_url'].'" style="color:'.$comp_url_color.' !important;"><h1>'.ucwords($config['system_name']).'</h1></a>
                </div>
                <div style="background-color:#e8e8e8; text-align: center;  padding-top:10px; padding-bottom:10px">
                    <h2 style="color:black !important">Hello, '.ucwords($user['name']).'!</h2>
        ';
        return $bodyTop;
    }

    static public function emailBodyBottom($test=null)
    {
        $config = GeneralModel::configCompare();
        $comp_url_color = FunctionsModel::getComplement($config['banner_color']);
        $comp_banner_color = FunctionsModel::getWorB($config['banner_color']);

        $bodyBottom = '
                    <p style="color:black !important">Regards,<br><strong>'.$config['smtp_from_name'].'</strong></p>
                </div>
                <div style="padding-top:10px; padding-bottom:20px;text-align: center;">
                    <p style="font-size:14px; color: '.$comp_banner_color.'">Copyright &copy; '.date("Y").' <a href="https://gitlab.com/andrewrichardson701/stockbase" style="color:'.$comp_url_color.' !important">StockBase</a>. All rights reserved.</p>
                </div>
            </div>';
            
        return $bodyBottom;
    }

    static public function emailFooter($test=null)
    {
        if (!$test || $test == null) {
            $foot = '
            </body>';
        } else {
            $foot = '
            </div>';
        }

        return $foot;
    }

    static public function toggleSmtp($enabled)
    {
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('error', 'Permission denied.');
        }

        if (in_array($enabled, ['on', 'off'])) {
            if ($enabled == 'on') {
                $enabled = 1;
            } else {
                $enabled = 0;
            }

            if (is_numeric($enabled)) {
                $current_data = DB::table('config')
                        ->select('smtp_enabled')
                        ->where('id', 1)
                        ->first();

                if ($current_data) {
                    $previous_value = $current_data->smtp_enabled;

                    $state = $enabled == 1 ? 'enabled' : 'disabled';

                    $update = DB::table('config')->where('id', 1)->update(['smtp_enabled' => (int)$enabled, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => GeneralModel::getUser(),
                            'table' => 'config',
                            'record_id' => 1,
                            'action' => 'Update record',
                            'field' => 'smtp_enabled',
                            'previous_value' => $previous_value,
                            'new_value' => (int)$enabled
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('success', 'SMTP '.$state.'!');
                    } else {
                        return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('error', 'No changes made. Unable to toggle SMTP');
                    }
                    
                } else {
                    return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('error', 'Unable to get current config.');
                }
            } else {
                return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('error', 'Invalid value.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'smtp-settings']) . '#smtp-settings')->with('error', 'Invalid value.');
        }
    }

    static public function buildEmail($body, $test=null)
    {
        $head = SmtpModel::emailHead($test);
        $bodyTop = SmtpModel::emailBodyTop($test);
        $bodyBottom = SmtpModel::emailBodyBottom($test);
        $footer = SmtpModel::emailFooter($test);

        return $head . $bodyTop . $body . $bodyBottom . $footer;
    }

    static public function getTemplateInfo($template_id)
    {
        $template = DB::table('email_templates')->where('id', '=', $template_id)->first();

        if ($template) {
            return $template;
        } else {
            return false;
        }
    }

    static public function convertVariables($input, $params=[])
    {
        $config = GeneralModel::configCompare();
        $user = GeneralModel::getUser();

        // Build a single array of variables
        $variables = [
            '##BASE_URL##'          => $config['base_url'],
            '##SYSTEM_NAME##'       => $config['system_name'],
            '##SYSTEM_LINK##'       => '<a href="'.$config['base_url'].'">'.$config['system_name'].'</a>',
            '##BANNER_COLOR##'      => $config['banner_color'],
            '##BANNER_TEXT_COLOR##' => FunctionsModel::getWorB($config['banner_color']),
            '##BANNER_URL_COLOR##'  => FunctionsModel::getComplement($config['banner_color']),
            '##STOCK_NAME##'        => $params['stock_name'] ?? '',
            '##STOCK_ID##'          => $params['stock_id'] ?? '',
            '##SITE_NAME##'         => $params['site_name'] ?? '',
            '##SITE_ID##'           => $params['site_id'] ?? '',
            '##AREA_NAME##'         => $params['area_name'] ?? '',
            '##AREA_ID##'           => $params['area_id'] ?? '',
            '##SHELF_NAME##'        => $params['shelf_name'] ?? '',
            '##SHELF_ID##'          => $params['shelf_id'] ?? '',
            '##SITE_NAME_OLD##'     => $params['site_name_old'] ?? '',
            '##SITE_ID_OLD##'       => $params['site_id_old'] ?? '',
            '##AREA_NAME_OLD##'     => $params['area_name_old'] ?? '',
            '##AREA_ID_OLD##'       => $params['area_id_old'] ?? '',
            '##SHELF_NAME_OLD##'    => $params['shelf_name_old'] ?? '',
            '##SHELF_ID_OLD##'      => $params['shelf_id_old'] ?? '',
            '##SITE_NAME_NEW##'     => $params['site_name_new'] ?? '',
            '##SITE_ID_NEW##'       => $params['site_id_new'] ?? '',
            '##AREA_NAME_NEW##'     => $params['area_name_new'] ?? '',
            '##AREA_ID_NEW##'       => $params['area_id_new'] ?? '',
            '##SHELF_NAME_NEW##'    => $params['shelf_name_new'] ?? '',
            '##SHELF_ID_NEW##'      => $params['shelf_id_new'] ?? '',
            '##OLD_QUANTITY##'      => $params['old_quantity'] ?? '',
            '##NEW_QUANTITY##'      => $params['new_quantity'] ?? '',
            '##IMG_FILE_NAME##'     => $params['img_file_name'] ?? '',
            '##MIN_STOCK##'         => $params['min_stock'] ?? '',
            '##USER_NAME##'         => $user['name'] ?? '',
            '##USER_EMAIL##'        => $user['email'] ?? '',
        ];

        // Replace all variables in one go
        $output = str_replace(array_keys($variables), array_values($variables), $input);
        return $output;
    }

    static public function smtpTest($request, EmailService $mailer)
    {
        $smtpConnectionOk = false;

        if ($request['smtp_encryption'] == 'starttls') {
            $host = $request['smtp_host'];
            $port = $request['smtp_port'];
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
            
            $cmd = "EHLO ".$_SERVER['HTTP_HOST'];
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
            put($smtp, $cmd);
            $response = get($smtp);
            echo $response;

            if (substr($response, 0, 3) === '221') {
                $smtpConnectionOk = true;
            }
            // echo "</pre>";
            
            fclose($smtp);
        } else {
            if ($request['smtp_encryption'] == 'none') {
                $smtp_encryption = '';
                $prefix = '';
            } else {
                $prefix="://";
            }
            $prefix = $smtp_encryption.$prefix;
            $host = $prefix.$request['smtp_host'];
            $port = $request['smtp_port'];
            // $errorNumber;
            // $error;
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
                } else {
                    $smtpConnectionOk = true;
                }
            }

            // echo '<p>Dump SMTP connection</p><pre>';
            // var_dump($connection);
            // echo '</pre>';

            fclose($connection);
            echo '<p>Execution time: ' . (microtime(true) - $mTime) . '</p>';
            if ($enableLog) fclose($fp);
        } 

        
        
        if ($smtpConnectionOk && isset($request['smtp_from_email']) && isset($request['smtp_from_name']) && isset($request['smtp_to_email'])) {

            $testBody = "<p>This is a test of the Inventory mail system. <br>You're all set!</p>";
            $mailer->sendEmail(
                $request['smtp_to_email'], 
                $request['smtp_to_name'], 
                $request['smtp_from_name'], 
                'SMTP Test Email', 
                SmtpModel::buildEmail(SmtpModel::convertVariables($testBody)),
                $request['notif_id'],
                $request
            );
        } else {
            echo('<p>SMTP connection failed.</p>');
        }
    }

}
