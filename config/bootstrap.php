<?php
session_start();
require_once __DIR__.'/db.php';

function json($data,$code=200){ http_response_code($code); header('Content-Type: application/json'); echo json_encode($data); exit; }
function bad($msg,$code){ json(['error'=>$msg],$code); }
function timeOverlap($aS,$aE,$bS,$bE){ return max(strtotime($aS),strtotime($bS)) < min(strtotime($aE),strtotime($bE)); }
