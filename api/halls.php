<?php
require_once __DIR__.'/../config/bootstrap.php';
$rows = db()->query("SELECT code AS id, name, capacity FROM halls ORDER BY code")->fetchAll();

$eq = db()->query("
  SELECT h.code AS id, e.name AS eq
  FROM halls h
  JOIN hall_equipment he ON he.hall_id=h.id
  JOIN equipment e ON e.id=he.equipment_id
")->fetchAll();

$map=[]; foreach($eq as $r){ $map[$r['id']][] = $r['eq']; }
$out = array_map(function($h) use ($map){
  return ['id'=>$h['id'],'name'=>$h['name'],'capacity'=>(int)$h['capacity'],'equipment'=>$map[$h['id']] ?? []];
}, $rows);

json($out);
