<?php
// config/update_hall.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

  $id   = (int)($data['id'] ?? 0);
  $code = trim((string)($data['code'] ?? ''));
  $name = trim((string)($data['name'] ?? ''));
  $capacity = (int)($data['capacity'] ?? 0);
  $equipment = $data['equipment'] ?? '';

  if ($id <= 0 || $code === '' || $name === '' || $capacity <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Missing/invalid fields']);
    exit;
  }
  if (is_array($equipment)) {
    $equipment = implode(',', array_map('trim', $equipment));
  } else {
    $equipment = trim((string)$equipment);
  }

  $stmt = $conn->prepare("UPDATE halls SET code=?, name=?, capacity=?, equipment=? WHERE id=?");
  $stmt->bind_param('ssisi', $code, $name, $capacity, $equipment, $id);
  $stmt->execute();
  $stmt->close();

  echo json_encode(['success'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
