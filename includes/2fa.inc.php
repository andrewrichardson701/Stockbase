<?php
function create2FA($accountName) {
    require_once 'GoogleAuthenticator/PHPGangsta/GoogleAuthenticator.php';

    $g = new PHPGangsta_GoogleAuthenticator();
    $secret = $g->createSecret();

    $issuer = 'testing'; // Replace with your website or app name
    $qrCodeUrl = $g->getQRCodeGoogleUrl($issuer . ':' . $accountName, $secret, $issuer);

    // echo "Secret: " . $secret . "<br>";
    // echo "QR-Code: <img src='" . $qrCodeUrl . "'><br>";

    $qr_img = "<img src='" . $qrCodeUrl . "'>";

    $return = array('secret' => $secret, 'qr_url' => $qrCodeUrl, 'qr_img' => $qr_img);
    return $return;
}

function OTPverify($user_id, $secret, $otp) {

    require_once 'GoogleAuthenticator/PHPGangsta/GoogleAuthenticator.php';

    $g = new PHPGangsta_GoogleAuthenticator();

    $checkResult = $g->verifyCode($secret, $otp, 2);    // 2 = 2*30sec clock tolerance

    if ($checkResult) {
        $return ='2FA verification successful';
        saveSecret($user_id, $secret);
    } else {
        $return = 'Invalid OTP';
    }

    return $return;

}

function makeOTPPrompt($data, $accountName, $user_id, $redirect_url, $format) {
    $info = '<div class="modal" style="display:block">
                <div id="2fa-qr" class="container col-md-2 text-center well-nopad theme-divBg" style="padding:30px">
                    <h2 style="margin-bottom:20px">Two-Factor Authentication</h2>
                    <p>Enter 6-digit code from Authenticator</p>
                    <p id="status_info" style="display:none"></p>
                    <span>
                        <input id="otp_code" type="text" class="form-control text-center" style="max-width:150px; display:revert;margin-right:5px;margin-top:1px" name="otp" placeholder="######">
                        <button class="form-control btn btn-success" style="max-width:max-content; margin-bottom:1px" onclick="checkotp()">Submit</button>
                    </span>
                    <span>
                        <input type="checkbox" id="bypass_2fa" name="bypass_2fa" style="margin-top:20px;margin-right:10px">
                        <label class="title" title="Don\'t use 2FA for 30 days on this device">Remember me</label>
                    </span>
                    <input id="otp_secret" type="hidden" name="otp_secret" value="'.$data['secret'].'">
                    <input id="account_name" type="hidden" name="account_name" value="'.$accountName.'">
                    <input id="redirect_url" type="hidden" name="redirect_url" value="'.$redirect_url.'">
                    <input id="user_id" type="hidden" name="user_id" value="'.$user_id.'">
                </div>
            </div>';

    if ($format == 'print') {
        echo $info;
    } else {
        return $info;
    }
}

function make2FAPrompt($data, $accountName, $user_id, $redirect_url, $format) {
    $info = '<div class="modal" style="display:block">
                <div id="2fa-qr" class="container col-md-2 text-center well-nopad theme-divBg" style="padding:30px">
                    <h2 style="margin-bottom:20px">Two-Factor Authentication</h2>
                    <p>Scan this QR code with a valid authenticator app.</p>
                    <img src='.$data['qr_url'].' id="2fa_qr" style="margin-bottom:20px">
                    <p>Enter 6-digit code from Authenticator</p>
                    <p id="status_info" style="display:none"></p>
                    <span>
                        <input id="otp_code" type="text" class="form-control text-center" style="max-width:150px; display:revert;margin-right:5px;margin-top:1px" name="otp" placeholder="######">
                        <button class="form-control btn btn-success" style="max-width:max-content; margin-bottom:1px" onclick="checkotp()">Submit</button>
                    </span>
                    <span>
                        <input type="checkbox" id="bypass_2fa" name="bypass_2fa" style="margin-top:20px;margin-right:10px">
                        <label class="title" title="Don\'t use 2FA for 30 days on this device">Remember me</label>
                    </span>
                    <input id="otp_secret" type="hidden" name="otp_secret" value="'.$data['secret'].'">
                    <input id="account_name" type="hidden" name="account_name" value="'.$accountName.'">
                    <input id="redirect_url" type="hidden" name="redirect_url" value="'.$redirect_url.'">
                    <input id="user_id" type="hidden" name="user_id" value="'.$user_id.'">
                </div>
            </div>';

    if ($format == 'print') {
        echo $info;
    } else {
        return $info;
    }
    
}

function saveSecret($user_id, $secret) {
    include 'dbh.inc.php';

    $sql = "UPDATE users SET 2fa_secret=? WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("ERROR getting entries");
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $secret, $user_id);
        mysqli_stmt_execute($stmt);
    }
}

function getSecret($user_id) {
    include 'dbh.inc.php';
    $sql_users = "SELECT 2fa_secret
                    FROM users 
                    WHERE id = ?";
    $stmt_users = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
        // error but no need to show.
    } else {
        mysqli_stmt_bind_param($stmt_users, "s", $user_id);
        mysqli_stmt_execute($stmt_users);
        $result = mysqli_stmt_get_result($stmt_users);
        $row = $result->fetch_assoc();
        $secret = $row['2fa_secret'];

        return $secret;
        
    }
}

function getAccountName($accountID) {
    include 'dbh.inc.php';
    $sql_users = "SELECT username
                    FROM users 
                    WHERE id = ?";
    $stmt_users = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
        // error but no need to show.
    } else {
        mysqli_stmt_bind_param($stmt_users, "s", $accountID);
        mysqli_stmt_execute($stmt_users);
        $result = mysqli_stmt_get_result($stmt_users);
        $row = $result->fetch_assoc();
        $username = $row['username'];

        return $username;
        
    }
}

function remember2FA($user_id, $cookie) {
    include 'session.inc.php';
    include 'dbh.inc.php';

    $ip = getIPAddress();
    $browser = getBrowser();
    $os =  getOS();

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ip_field = 'ipv4';
        $ip_insert = 'INET_ATON(?)';
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $ip_field = 'ipv6';
        $ip_insert = 'INET6_ATON(?)';
    } else {
        $ip_field = 'ipv4';
        $ip_insert = '?';
        $ip = null;
    }

    $sql = "SELECT *
            FROM bypass_2fa
            WHERE user_id=? AND cookie=? 
            AND deleted=0";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // error but no need to show.
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $cookie);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $result_count = $result->num_rows;

        if ($result_count > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];

                $sql_update = "UPDATE bypass_2fa SET deleted=1 WHERE id=?";
                $stmt_update = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_bind_param($stmt_update, "s", $id);
                    mysqli_stmt_execute($stmt_update);
                }
            }
        }

        $sql = "INSERT into bypass_2fa (user_id, cookie, $ip_field, browser, os, deleted) 
                    VALUES (?, ?, $ip_insert, ?, ?, 0)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_bind_param($stmt, "sssss", $user_id, $cookie, $ip, $browser, $os);
            mysqli_stmt_execute($stmt);
        }
    }

   

}

if (isset($_POST['makeotp'])) {
    if (isset($_POST['user_id'])) {
        if (isset($_POST['redirect_url'])) {
            $return = [];
            $redirect_url = $_POST['redirect_url'];

            $accountID = $_POST['user_id'];

            $accountName = getAccountName($accountID);
            $secret = getSecret($accountID);
            $data = array('secret'=>$secret);
            $prompt = makeOTPPrompt($data, $accountName, $accountID, $redirect_url, 'return');
            
            $return['status'] = 'true';
            $return['data'] = $prompt;
            $return['redirect_url'] = $redirect_url;

            echo json_encode($return);
        }
        
    }
}

if (isset($_POST['make2fa'])) {
    if (isset($_POST['user_id'])) {
        if (isset($_POST['redirect_url'])) {
            $return = [];
            $redirect_url = $_POST['redirect_url'];

            $accountID = $_POST['user_id'];

            $accountName = getAccountName($accountID);
            $data = create2FA($accountName);
            $prompt = make2FAPrompt($data, $accountName, $accountID, $redirect_url, 'return');
            
            $return['status'] = 'true';
            $return['data'] = $prompt;
            $return['redirect_url'] = $redirect_url;

            echo json_encode($return);
        }
        
    }
}

if (isset($_POST['checkotp'])) {
    if (isset($_POST['accountName'])) {
        if (isset($_POST['user_id'])) {
            if (isset($_POST['otp'])) {
                if (isset($_POST['secret'])) {
                    $return = [];
                    if (isset($_POST['bypass_2fa'])) {
                        $bypass_2fa = $_POST['bypass_2fa'];
                    } else {
                        $bypass_2fa = false;
                    }

                    $accountName = $_POST['accountName'];
                    $user_id = $_POST['user_id'];
                    
                    $secret = $_POST['secret'];
                    $otp = $_POST['otp'];
                    
                    $output = OTPverify($user_id, $secret, $otp);
                    
                    if ($output !== 'Invalid OTP' && ($bypass_2fa == true || $bypass_2fa == 'true'))  {
                        remember2FA($user_id, $_COOKIE['PHPSESSID'], $_SERVER);
                    }

                    $return['status'] = 'true';
                    $return['data'] = $output;
                    
                    echo json_encode($return); 
                    
                }      
            }    
        }
    }
}