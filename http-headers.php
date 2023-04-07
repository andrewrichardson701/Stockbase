<?php
$requestedUrl = isset(explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1]) ? explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1] : '';
$requestedHttp = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ''; // IP of host server
$requestedPort = isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : '';
$requestedHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : '';
$requestedServer = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : '';
$remoteIP = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : ''; // IP of connector

$requestedUri = isset($_SERVER['HTTP_X_REQUEST_URI']) ? $_SERVER['HTTP_X_REQUEST_URI'] : '';

$serverendhttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryStringUrl = '?'.$queryString;
} else {
    $queryString = '';
    $queryStringUrl = '';
}

$fullRequestedURL = $requestedHttp.'://'.$requestedUrl.$requestedUri.$queryStringUrl;
$platform = $_SERVER["HTTP_USER_AGENT"];
?>