<?php
if (isset($_GET['sqlerror'])) {
    if ($_GET['sqlerror'] == 'credentials') {
        $error = '<p class="red">There was an authentication issue connecting to the system database.<br>Please check your credentials in <or class="blue">includes/dbh.inc.php</or> and try again.</p>';
    } else {
        header("Location: /");
    }
} else {
    header("Location: /");
}

// anti clickjacking defense
header("X-Frame-Options: DENY");
// Set a cookie with the Secure flag for defense against cookie attacks
setcookie("stockbase_cookie", bin2hex(random_bytes(32)), [ 'expires' => time() + 3600, 'path' => "/", 'domain' => $current_base_url, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' ]);
?>
<head>
    <!-- CSP headers -->
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' https://ajax.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline';
        style-src 'self' https://stackpath.bootstrapcdn.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com 'unsafe-inline';
        font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com;
        img-src 'self' data:;
    ">
    <meta charset="utf-8">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" href="./assets/img/config/<?php echo($current_favicon_image); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/inv.css">
    <title>System Error</title>
</head>

<body>
    <div class="align-middle text-center" style="margin-top:35vh">
        <h1>System Error</h2>
        <p>An error has occured, please see the details below:</p>
        <?php echo $error; ?>
        <a class="btn btn-info" style="color:white !important" id="home-btn" href="/">Home</a>
    </div>
</body>