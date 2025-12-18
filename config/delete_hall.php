<?php
// config/delete_hall.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
  $id = (int)($data['id'] ?? 0);
  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Invalid id']);
    exit;
  }

  // Optional: guard if hall is used by bookings
  // $used = $conn->query("SELECT COUNT(*) c FROM bookings WHERE hall_id={$id}")->fetch_assoc()['c'];
  // if ($used > 0) { echo json_encode(['success'=>false,'error'=>'Hall has bookings']); exit; }

  $stmt = $conn->prepare("DELETE FROM halls WHERE id=?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();

  echo json_encode(['success'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
