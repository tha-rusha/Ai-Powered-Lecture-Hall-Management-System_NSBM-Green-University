<?php
// config/get_student_data.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized (login as student)']);
  exit;
}

require __DIR__.'/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

// pull whatever you need for the student
$me = (int)$_SESSION['user']['id'];
// example:
$bookings = [];
$rs = $conn->query("SELECT day,time_slot AS time,hall_id,course_code FROM bookings ORDER BY day,time_slot");
while($row = $rs->fetch_assoc()) $bookings[] = $row;

echo json_encode(['me'=>$_SESSION['user'], 'bookings'=>$bookings]);
