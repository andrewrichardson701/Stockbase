<?php

$servername = "localhost";
$dBUsername = 'admin';
$dBPassword = 'admin';
$dBName = "inventory";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

if (!$conn) {
	die("Connection Failed: ".mysqli_connect_error());
}
