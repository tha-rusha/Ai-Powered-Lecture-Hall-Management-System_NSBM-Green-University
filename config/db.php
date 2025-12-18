<?php
// config/db.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // throw on SQL errors
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'nsbm';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');
