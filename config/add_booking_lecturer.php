<?php
// config/add_booking_lecturer.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'lecturer') {
  http_response_code(403);
  echo json_encode(['success'=>false,'error'=>'Forbidden']); exit;
}
$lecturer = $_SESSION['user']['name'];

require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  $raw  = file_get_contents('php://input');
  $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

  $course = trim((string)($data['course'] ?? ''));
  $day    = trim((string)($data['day'] ?? ''));
  $time   = trim((string)($data['time'] ?? ''));
  $size   = (int)($data['size'] ?? 0);
  // Either hallId or hallName may be supplied from the form; support both:
  $hallId   = $data['hallId'] ?? null;
  $hallName = $data['hallName'] ?? null;
  $notes  = trim((string)($data['notes'] ?? ''));

  if ($course==='' || $day==='' || $time==='' || $size<=0 || (!$hallId && !$hallName)) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Missing or invalid fields']); exit;
  }

  // If we only have hallName, look up its id
  if (!$hallId && $hallName) {
    $stmt = $conn->prepare("SELECT id FROM halls WHERE name=? LIMIT 1");
    $stmt->bind_param('s', $hallName);
    $stmt->execute();
    $stmt->bind_result($hid);
    if ($stmt->fetch()) $hallId = (int)$hid;
    $stmt->close();
  }

  // Fetch hall name (store both so UI can show name easily)
  $stmt = $conn->prepare("SELECT name, capacity FROM halls WHERE id=? LIMIT 1");
  $stmt->bind_param('i', $hallId);
  $stmt->execute();
  $stmt->bind_result($resolvedName, $capacity);
  if (!$stmt->fetch()) { throw new Exception('Unknown hall'); }
  $stmt->close();
  if ($size > (int)$capacity) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Hall capacity exceeded']); exit;
  }

  $stmt = $conn->prepare("
    INSERT INTO bookings (course_code, lecturer_name, day, time_slot, expected_size, hall_id, hall_name, notes)
    VALUES (?,?,?,?,?,?,?,?)
  ");
  $stmt->bind_param('ssssisss', $course, $lecturer, $day, $time, $size, $hallId, $resolvedName, $notes);
  $stmt->execute();
  $newId = $stmt->insert_id;
  $stmt->close();

  echo json_encode(['success'=>true, 'id'=>$newId]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
