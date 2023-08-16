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
        $message = implode(' ', array_slice($parts, 7));
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
    <title>Git Commit Messages</title>
</head>
<body>
    <h1>Git Commit Messages</h1>
    <ul>
        <?php foreach ($commitMessages as $commit): ?>
            <li>
                <strong><?= $commit['date'] ?></strong>: <?= $commit['message'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>