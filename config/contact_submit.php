<?php
// config/contact_submit.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

try {
    // Accept JSON or form-encoded
    $payload = [];
    if (($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?: [];
    } else {
        $payload = $_POST;
    }

    $name    = trim((string)($payload['name'] ?? ''));
    $email   = trim((string)($payload['email'] ?? ''));
    $subject = trim((string)($payload['subject'] ?? ''));
    $message = trim((string)($payload['message'] ?? ''));

    // Simple validation
    $errors = [];
    if ($name === '')                      $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($subject === '')                   $errors[] = 'Subject is required.';
    if ($message === '' || strlen($message) < 10)   $errors[] = 'Message must be at least 10 characters.';

    if ($errors) {
        http_response_code(422);
        echo json_encode(['success'=>false, 'errors'=>$errors]);
        exit;
    }

    // Attach session user if available
    $userId = null;
    if (!empty($_SESSION['user']['id'])) {
        $userId = (int)$_SESSION['user']['id'];
    } elseif (!empty($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
    }

    $stmt = $conn->prepare("
        INSERT INTO contact_messages (id, name, email, subject, message)
        VALUES (?, ?, ?, ?, ?)
    ");
    // i = int, s = string
    $stmt->bind_param('issss', $userId, $name, $email, $subject, $message);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();

    echo json_encode(['success'=>true, 'id'=>$newId]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
