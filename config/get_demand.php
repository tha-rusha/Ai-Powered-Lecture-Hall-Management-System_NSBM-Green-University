<?php
// /config/get_demand.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

try {
  // Total demand per (day, time_slot)
  $bySlot = [];
  $rs = $conn->query("
      SELECT day, time_slot, COUNT(*) AS cnt
      FROM bookings
      GROUP BY day, time_slot
  ");
  while ($r = $rs->fetch_assoc()) {
    $bySlot[] = [
      'day'  => $r['day'],
      'time' => $r['time_slot'],
      'cnt'  => (int)$r['cnt']
    ];
  }

  // Total demand per hall
  $byHall = [];
  $rs = $conn->query("
      SELECT h.id, h.name, COUNT(b.id) AS cnt
      FROM halls h
      LEFT JOIN bookings b ON b.hall_id = h.id
      GROUP BY h.id, h.name
      ORDER BY cnt DESC, h.name
  ");
  while ($r = $rs->fetch_assoc()) {
    $byHall[] = [
      'hallId'   => (int)$r['id'],
      'hallName' => $r['name'],
      'cnt'      => (int)$r['cnt']
    ];
  }

  // Hotspots = top (day,time) slots by count (top 8 for UI)
  usort($bySlot, fn($a,$b)=> $b['cnt'] <=> $a['cnt']);
  $hotspots = array_slice($bySlot, 0, 8);

  echo json_encode([
    'bySlot'    => $bySlot,
    'byHall'    => $byHall,
    'hotspots'  => $hotspots
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
