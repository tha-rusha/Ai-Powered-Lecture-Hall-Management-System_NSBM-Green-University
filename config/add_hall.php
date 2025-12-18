<?php
// config/add_hall.php
declare(strict_types=1);
ini_set('display_errors','1'); ini_set('display_startup_errors','1'); error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  $raw  = file_get_contents('php://input');
  $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

  // Read & normalize input
  $code      = trim((string)($data['code'] ?? ''));
  $name      = trim((string)($data['name'] ?? ''));
  $capacity  = (int)($data['capacity'] ?? 0);
  $equipment = $data['equipment'] ?? '';                     // may be array or string
  if (is_array($equipment)) $equipment = implode(',', array_map('trim',$equipment));
  $equipment = trim((string)$equipment);

  if ($code === '' || $name === '' || $capacity <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Invalid input (code/name/capacity).']);
    exit;
  }

  // Insert
  $stmt = $conn->prepare("INSERT INTO halls (code,name,capacity,equipment) VALUES (?,?,?,?)");
  $stmt->bind_param('ssis', $code, $name, $capacity, $equipment);
  $stmt->execute();
  $id = $stmt->insert_id;
  $stmt->close();

  echo json_encode(['success'=>true, 'id'=>$id]);
}
catch (mysqli_sql_exception $e) {
  // Duplicate code etc.
  if ($e->getCode() == 1062) {
    http_response_code(409);
    echo json_encode(['success'=>false,'error'=>'Hall code already exists.']);
  } else {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
  }
}
catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
