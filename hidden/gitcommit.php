<?php
$logsPath = '../.git/logs/HEAD';
$logsContent = file_get_contents($logsPath);

$lines = explode("\n", $logsContent);
$commitMessages = [];


foreach ($lines as $line) {
    $parts = explode(' ', $line);
    if (count($parts) >= 7) {
        $timestamp = intval($parts[4]);
        $timezoneOffset = intval($parts[5]);
        $date = date('Y-m-d H:i:s', $timestamp - $timezoneOffset);
        $message = implode(' ', array_slice($parts, 6));
        $commitMessages[] = [
            'date' => $date,
            'message' => $message,
        ];
    } 
}

// Reverse the array to get messages in chronological order
$commitMessages = array_reverse($commitMessages);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">

    <!-- favicon -->
    <!--<link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="/assets/img/favicon/safari-pinned-tab.svg" color="#e1b12c">
    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#b91d47">
    <meta name="msapplication-config" content="/assets/img/favicon/browserconfig.xml">-->


    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/inv.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">
    <!-- below for colour picker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v6.4.0/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <title>Git Commit Messages</title>
</head>
<body>
    <div style="margin-left:50px; margin-right:50px; margin-top:50px; margin-bottom:50px">
        <h1 style="padding-bottom:20px">Git Commit Messages</h1>
        <ul>
            <?php foreach ($commitMessages as $commit): ?>
                <li class="uni">
                    <strong><?= $commit['date'] ?></strong>: <?= $commit['message'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>