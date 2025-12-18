<?php
require_once __DIR__.'/../config/bootstrap.php';
$rows = db()->query("SELECT name FROM users WHERE role='lecturer' ORDER BY name")->fetchAll();
json(array_map(fn($r)=>$r['name'],$rows));
