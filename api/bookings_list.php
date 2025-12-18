<?php
require __DIR__ . '/db.php';

// Optional filters (?day=Monday&hall_id=3)
$day     = $_GET['day']     ?? null;
$hall_id = $_GET['hall_id'] ?? null;

$sql = "
  SELECT 
    b.id,
    b.course_code,
    c.title AS course_title,
    b.lecturer_name,
    b.day,
    b.time_slot,
    b.expected_size,
    b.hall_id,
    h.code AS hall_code,
    h.name AS hall_name,
    b.notes,
    b.created_at
  FROM bookings b
  LEFT JOIN courses c ON c.code = b.course_code
  LEFT JOIN halls h    ON h.id   = b.hall_id
  WHERE 1=1
";
$params = [];
if ($day)     { $sql .= " AND b.day = :day";        $params[':day'] = $day; }
if ($hall_id) { $sql .= " AND b.hall_id = :hall_id";$params[':hall_id'] = $hall_id; }

$sql .= "
  ORDER BY FIELD(b.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
           SUBSTRING_INDEX(b.time_slot,'-',1)
";

$stmt = pdo()->prepare($sql);
$stmt->execute($params);
json($stmt->fetchAll());
