<?php
// auth/api_signup.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/db.php'; // adjust path if your db.php lives elsewhere
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
    // Read JSON body
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

    // Validate inputs
    $name  = trim((string)($data['name']  ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $pass  = (string)($data['password']   ?? '');
    $role  = strtolower(trim((string)($data['role'] ?? 'student')));

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        http_response_code(422);
        echo json_encode(['success'=>false, 'errors'=>['Invalid name, email, or password too short (min 6).']]);
        exit;
    }

    // Accept role exactly as selected (admin/lecturer/student)
    $allowedRoles = ['admin','lecturer','student'];
    if (!in_array($role, $allowedRoles, true)) {
        $role = 'student';
    }

    // Ensure email is unique
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['success'=>false, 'errors'=>['Email already in use.']]);
        exit;
    }
    $stmt->close();

    // Hash password
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();

    echo json_encode(['success'=>true, 'id'=>$newId, 'role'=>$role]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'errors'=>[$e->getMessage()]]);
}
