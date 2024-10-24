<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// DB CONNECTION INFO AND CREDENTIALS 

$servername = 'localhost';
$dBUsername = 'admin';
$dBPassword = 'admin';
$dBName = 'stockbase';

try {
	$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

	// Check connection
	if ($conn->connect_error) {
		throw new Exception("Connection failed: " . $conn->connect_error);
	}
} catch (Exception $e) {
	error_log("Database connection failed: $e ");
	// redirect due to error
	echo '<script type="text/javascript">
			window.location.href = "error.php?sqlerror=credentials";
		  </script>';
	exit();
}

if (!$conn) {
	die("Connection Failed: ".mysqli_connect_error());
}
