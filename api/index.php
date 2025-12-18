<?php
require_once __DIR__.'/../config/bootstrap.php';
$path = trim($_GET['q'] ?? ($_SERVER['PATH_INFO'] ?? ''), '/');

switch ($path) {
  case 'bootstrap': require __DIR__.'/bootstrap_data.php'; break;
  case 'bookings' : require __DIR__.'/bookings.php'; break;
  case 'halls'    : require __DIR__.'/halls.php'; break;
  case 'courses'  : require __DIR__.'/courses.php'; break;
  case 'lecturers': require __DIR__.'/lecturers.php'; break;
  case 'analytics': require __DIR__.'/analytics.php'; break;
  default: bad('Not found',404);
}
