<?php
require_once __DIR__.'/../config/bootstrap.php';
$rows = db()->query("SELECT code, title FROM courses ORDER BY code")->fetchAll();
$out = array_map(fn($c)=>['code'=>$c['code'],'name'=>$c['title']], $rows);
json($out);
