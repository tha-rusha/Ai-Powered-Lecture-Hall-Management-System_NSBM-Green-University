<?php
// config/get_forecast.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$ml_url = 'http://127.0.0.1:5001/forecast';

$ch = curl_init($ml_url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 10,
]);

$out = curl_exec($ch);
if ($out === false) {
  http_response_code(502);
  echo json_encode(['ok'=>false,'error'=>'ML service unreachable: '.curl_error($ch)]);
  exit;
}
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($code);
echo $out;
