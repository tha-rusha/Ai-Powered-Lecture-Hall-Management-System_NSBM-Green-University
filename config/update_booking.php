<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

    $id       = (int)($data['id'] ?? 0);
    $course   = trim((string)($data['course']   ?? ''));
    $lecturer = trim((string)($data['lecturer'] ?? ''));
    $day      = trim((string)($data['day']      ?? ''));
    $time     = trim((string)($data['time']     ?? ''));
    $size     = (int)($data['size'] ?? 0);
    $hall_id  = (int)($data['hallId'] ?? 0);
    $notes    = trim((string)($data['notes']    ?? ''));

    if ($id<=0 || $course==='' || $lecturer==='' || $day==='' || $time==='' || $size<=0 || $hall_id<=0) {
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>'Missing or invalid fields']);
        exit;
    }

    $stmt = $conn->prepare("
      UPDATE bookings
      SET course_code=?, lecturer_name=?, day=?, time_slot=?, expected_size=?, hall_id=?, notes=?
      WHERE id=?
    ");
    $stmt->bind_param('ssssissi', $course, $lecturer, $day, $time, $size, $hall_id, $notes, $id);
    $stmt->execute();

    echo json_encode(['success'=>true, 'affected'=>$stmt->affected_rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
