<?php
require_once __DIR__.'/../config/bootstrap.php';

/* halls: { id:'FOC-201', name:'FOC-201', capacity:120, equipment:[...] } */
$halls = db()->query("
  SELECT h.id, h.code, h.name, h.capacity
  FROM halls h ORDER BY h.code
")->fetchAll();
$eqStmt = db()->query("
  SELECT h.code AS hall_code, e.name AS eq
  FROM halls h
  JOIN hall_equipment he ON he.hall_id=h.id
  JOIN equipment e ON e.id=he.equipment_id
");
$equipmentByHall = [];
foreach($eqStmt as $r){ $equipmentByHall[$r['hall_code']][] = $r['eq']; }

$hallsOut = [];
foreach($halls as $h){
  $id = $h['code'];
  $hallsOut[] = [
    'id' => $id,
    'name' => $h['name'],
    'capacity' => (int)$h['capacity'],
    'equipment' => $equipmentByHall[$h['code']] ?? []
  ];
}

/* courses: { code:'MIS401', name:'AI for MIS' } */
$courses = db()->query("SELECT code, title FROM courses ORDER BY code")->fetchAll();
$coursesOut = array_map(fn($c)=>['code'=>$c['code'],'name'=>$c['title']], $courses);

/* lecturers: ['Dr. Silva', ...] */
$lecturers = db()->query("SELECT name FROM users WHERE role='lecturer' ORDER BY name")->fetchAll();
$lecturersOut = array_map(fn($r)=>$r['name'], $lecturers);

/* bookings: 
   { id:'...', course:'SE302', lecturer:'Ms. Perera', day:'Tuesday', time:'10:00-12:00', size:120, hallId:'FOC-201', notes:'' }
*/
$bookings = db()->query("
  SELECT b.id, c.code AS course, u.name AS lecturer, b.day,
         DATE_FORMAT(b.start_time,'%H:%i') AS s, DATE_FORMAT(b.end_time,'%H:%i') AS e,
         b.size, h.code AS hallId, COALESCE(b.notes,'') AS notes
  FROM bookings b
  JOIN courses c ON c.id=b.course_id
  JOIN users u ON u.id=b.lecturer_id
  JOIN halls h ON h.id=b.hall_id
  ORDER BY FIELD(b.day,'Monday','Tuesday','Wednesday','Thursday','Friday'), b.start_time
")->fetchAll();
$bookingsOut = array_map(fn($b)=>[
  'id'=>$b['id'], 'course'=>$b['course'], 'lecturer'=>$b['lecturer'], 'day'=>$b['day'],
  'time'=>$b['s'].'-'.$b['e'], 'size'=>(int)$b['size'], 'hallId'=>$b['hallId'], 'notes'=>$b['notes']
], $bookings);

json([
  'halls'=>$hallsOut,
  'courses'=>$coursesOut,
  'lecturers'=>$lecturersOut,
  'bookings'=>$bookingsOut
]);
