<?php
require_once __DIR__.'/../config/bootstrap.php';

$mode = $_GET['m'] ?? '';

if($mode==='utilization'){
  $halls = (int)db()->query("SELECT COUNT(*) c FROM halls")->fetch()['c'];
  $sessions = (int)db()->query("SELECT COUNT(*) c FROM bookings")->fetch()['c'];
  $slots = 5 * 9; // Mon-Fri (08..17 => ~9 slots/day)
  $util = $halls>0 ? round(($sessions/($halls*$slots))*100) : 0;
  json(['utilization'=>$util]);
}

if($mode==='daily-load'){
  $rows = db()->query("SELECT day, COUNT(*) n FROM bookings GROUP BY day")->fetchAll();
  $map = ['Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0];
  foreach($rows as $r){ $map[$r['day']] = (int)$r['n']; }
  json($map);
}

bad('Unsupported',405);
