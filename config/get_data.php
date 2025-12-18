<?php
// config/get_data.php
declare(strict_types=1);

// Make sure nothing else is printed before JSON:
if (function_exists('ob_get_level')) { while (ob_get_level()) ob_end_clean(); }
header('Content-Type: application/json; charset=utf-8');

// Turn off display_errors so PHP notices don’t pollute JSON:
ini_set('display_errors', '0');

require __DIR__ . '/db.php';

try {
    // --- Halls ---
    $halls = [];
    $rs = $conn->query("SELECT id, name, capacity, equipment FROM halls ORDER BY name");
    while ($row = $rs->fetch_assoc()) {
        // normalize equipment to array (front-end expects array)
        $row['equipment'] = array_values(array_filter(array_map('trim', explode(',', (string)$row['equipment']))));
        $halls[] = $row;
    }

    // --- Courses (your table has code + title) ---
    // return as {code, name} because front-end uses "name"
    $courses = [];
    $rs = $conn->query("SELECT code, title FROM courses ORDER BY title");
    while ($row = $rs->fetch_assoc()) {
        $courses[] = ['code' => $row['code'], 'name' => $row['title']]; // maps nicely to UI
    }

    // --- Lecturers (users with role=lecturer) ---
    $lecturers = [];
    $rs = $conn->query("SELECT name FROM users WHERE role = 'lecturer' ORDER BY name");
    while ($row = $rs->fetch_assoc()) {
        $lecturers[] = $row['name'];
    }

    // --- Bookings ---
    $bookings = [];
    $rs = $conn->query("SELECT id, course_code, lecturer_name, day, time_slot, expected_size, hall_id, notes FROM bookings ORDER BY id DESC");
    while ($row = $rs->fetch_assoc()) {
        // normalize field names to what app.js expects
        $bookings[] = [
            'id'        => (int)$row['id'],
            'course'    => $row['course_code'],
            'lecturer'  => $row['lecturer_name'],
            'day'       => $row['day'],
            'time'      => $row['time_slot'],
            'size'      => (int)$row['expected_size'],
            'hallId'    => $row['hall_id'],
            'notes'     => $row['notes'] ?? ''
        ];
    }

    echo json_encode([
        'halls'      => $halls,
        'courses'    => $courses,
        'lecturers'  => $lecturers,
        'bookings'   => $bookings,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    // Log it to server logs, but DO NOT echo it (keeps JSON clean)
    error_log('[get_data.php] ' . $e->getMessage());
    echo json_encode(['error' => 'server_error']);
}

$rs = $conn->query("SELECT id, code, name, capacity, equipment FROM halls ORDER BY name");

