<?php
// config/get_data_lecturer.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'lecturer') {
  http_response_code(403);
  echo json_encode(['error'=>'Forbidden']);
  exit;
}
$lecturer = $_SESSION['user']['name'];

require __DIR__.'/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  // Halls
  $halls = [];
  $rs = $conn->query("SELECT id, code, name, capacity, equipment FROM halls ORDER BY name");
  while ($row = $rs->fetch_assoc()) $halls[] = $row;

  // Courses
  $courses = [];
  $rs = $conn->query("SELECT code, title FROM courses ORDER BY title");
  while ($row = $rs->fetch_assoc()) $courses[] = $row;

  // Bookings for this lecturer only
  $stmt = $conn->prepare("
    SELECT id, course_code, lecturer_name, day, time_slot, expected_size, hall_id, hall_name, notes
    FROM bookings
    WHERE lecturer_name = ?
    ORDER BY day, time_slot
  ");
  $stmt->bind_param('s', $lecturer);
  $stmt->execute();
  $rs = $stmt->get_result();
  $bookings = [];
  while ($row = $rs->fetch_assoc()) {
    $bookings[] = [
      'id'       => (int)$row['id'],
      'course'   => $row['course_code'],
      'lecturer' => $row['lecturer_name'],
      'day'      => $row['day'],
      'time'     => $row['time_slot'],
      'size'     => (int)$row['expected_size'],
      'hallId'   => $row['hall_id'],
      'hallName' => $row['hall_name'],
      'notes'    => $row['notes'] ?? ''
    ];
  }
  echo json_encode(compact('halls','courses','bookings'), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error'=>$e->getMessage()]);
}
