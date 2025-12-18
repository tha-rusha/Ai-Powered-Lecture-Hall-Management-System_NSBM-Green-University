<?php
// /auth/api_login.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

try {
  $input = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
  $email = trim((string)($input['email'] ?? ''));
  $pass  = (string)($input['password'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    echo json_encode(['success'=>false,'errors'=>['Invalid email or password.']]);
    exit;
  }

  $stmt = $conn->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res  = $stmt->get_result();
  $user = $res->fetch_assoc();
  $stmt->close();

  if (!$user || !password_verify($pass, $user['password_hash'])) {
    echo json_encode(['success'=>false,'errors'=>['Invalid email or password.']]);
    exit;
  }
$_SESSION['user'] = [
  'id'    => (int)$user['id'],
  'name'  => $user['name'],
  'email' => $user['email'],
  'role'  => $user['role'], // 'student' for students
];
session_regenerate_id(true);

  // Start session
  session_regenerate_id(true);
  $_SESSION['user_id']   = (int)$user['id'];
  $_SESSION['user_name'] = $user['name'];
  $_SESSION['user_role'] = $user['role'];

  // Where to send each role (change to match your structure)
  $ROLE_REDIRECTS = [
    'admin'    => '../Admin/dashboard.php',
    'lecturer' => '../index.php', // or '../index.php'
    'student'  => '../index.php'
  ];
  $redirect = $ROLE_REDIRECTS[$user['role']] ?? '../pages/dashboard.php';

  echo json_encode(['success'=>true, 'role'=>$user['role'], 'redirect'=>$redirect]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'errors'=>['Server error: '.$e->getMessage()]]);
}
