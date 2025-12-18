<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='student') { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$me=$_SESSION['user'];

require __DIR__.'/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try{
  $data=json_decode(file_get_contents('php://input'),true,512,JSON_THROW_ON_ERROR);
  $courses=array_values(array_unique(array_filter(array_map('trim',$data['courses']??[]))));
  $conn->begin_transaction();
  $stmt=$conn->prepare("DELETE FROM enrollments WHERE user_id=?");
  $stmt->bind_param('i',$me['id']); $stmt->execute();
  if($courses){
    $ins=$conn->prepare("INSERT INTO enrollments (user_id,course_code) VALUES (?,?)");
    foreach($courses as $c){ $ins->bind_param('is',$me['id'],$c); $ins->execute(); }
  }
  $conn->commit();
  echo json_encode(['success'=>true]);
}catch(Throwable $e){
  $conn->rollback();
  http_response_code(500); echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
