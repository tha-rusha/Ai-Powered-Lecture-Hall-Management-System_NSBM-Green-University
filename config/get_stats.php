<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  $halls    = (int)$conn->query("SELECT COUNT(*) c FROM halls")->fetch_assoc()['c'];
  $courses  = (int)$conn->query("SELECT COUNT(*) c FROM courses")->fetch_assoc()['c'];
  $features = (int)$conn->query("SELECT COUNT(*) c FROM features WHERE enabled=1")->fetch_assoc()['c'];
  echo json_encode(['success'=>true,'halls'=>$halls,'courses'=>$courses,'features'=>$features]);
} catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
