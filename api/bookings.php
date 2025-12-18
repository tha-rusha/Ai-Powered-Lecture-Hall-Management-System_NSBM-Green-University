<?php
require_once __DIR__.'/../config/bootstrap.php';
$M = $_SERVER['REQUEST_METHOD'];

if($M==='GET'){
  // Return the same booking shape as bootstrap for your table/timetable
  $sql = "SELECT b.id, c.code AS course, u.name AS lecturer, b.day,
                 DATE_FORMAT(b.start_time,'%H:%i') AS s, DATE_FORMAT(b.end_time,'%H:%i') AS e,
                 b.size, h.code AS hallId, COALESCE(b.notes,'') AS notes
          FROM bookings b
          JOIN courses c ON c.id=b.course_id
          JOIN users u ON u.id=b.lecturer_id
          JOIN halls h ON h.id=b.hall_id
          ORDER BY FIELD(b.day,'Monday','Tuesday','Wednesday','Thursday','Friday'), b.start_time";
  $rows = db()->query($sql)->fetchAll();
  $out = array_map(fn($b)=>[
    'id'=>$b['id'], 'course'=>$b['course'], 'lecturer'=>$b['lecturer'], 'day'=>$b['day'],
    'time'=>$b['s'].'-'.$b['e'], 'size'=>(int)$b['size'], 'hallId'=>$b['hallId'], 'notes'=>$b['notes']
  ], $rows);
  json($out);
}

if($M==='POST'){
  // expected body: { course:'SE302', lecturer:'Ms. Perera', day:'Tuesday', time:'10:00-12:00', size:120, hallId:'FOC-201', notes:'' }
  $in = json_decode(file_get_contents('php://input'), true);

  // lookups
  $course_id = db()->prepare("SELECT id FROM courses WHERE code=?");
  $course_id->execute([$in['course']]); $course_id = $course_id->fetch()['id'] ?? null;

  $lect_id = db()->prepare("SELECT id FROM users WHERE role='lecturer' AND name=?");
  $lect_id->execute([$in['lecturer']]); $lect_id = $lect_id->fetch()['id'] ?? null;

  $hall_id = db()->prepare("SELECT id, capacity FROM halls WHERE code=?");
  $hall_id->execute([$in['hallId']]); $h = $hall_id->fetch(); $hall_db_id = $h['id'] ?? null; $capacity = (int)($h['capacity'] ?? 0);

  if(!$course_id || !$lect_id || !$hall_db_id) bad('Invalid course/lecturer/hall',422);

  // capacity check
  $size = (int)$in['size'];
  if($size > $capacity) bad("Hall capacity ($capacity) is less than expected size ($size).",422);

  // parse time "HH:MM-HH:MM"
  if(!preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/',$in['time'])) bad('Invalid time format',422);
  [$s,$e] = explode('-', $in['time']);

  // conflict check
  $q = db()->prepare("SELECT start_time,end_time FROM bookings WHERE hall_id=? AND day=?");
  $q->execute([$hall_db_id, $in['day']]);
  foreach($q as $r){ if(timeOverlap($r['start_time'],$r['end_time'],$s,$e)) bad('Time slot already booked for this hall.',409); }

  // insert
  $stmt = db()->prepare("INSERT INTO bookings(course_id, lecturer_id, hall_id, day, start_time, end_time, size, notes)
                         VALUES(?,?,?,?,?,?,?,?)");
  $stmt->execute([$course_id,$lect_id,$hall_db_id,$in['day'],$s,$e,$size,$in['notes']??null]);

  // return created (same shape)
  json(['ok'=>true,'id'=>db()->lastInsertId()]);
}

if($M==='DELETE'){
  $id = (int)($_GET['id'] ?? 0);
  if($id<=0) bad('Missing id',422);
  db()->prepare("DELETE FROM bookings WHERE id=?")->execute([$id]);
  json(['ok'=>true]);
}

bad('Unsupported',405);
