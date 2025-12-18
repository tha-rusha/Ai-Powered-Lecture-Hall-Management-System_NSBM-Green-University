<?php
// /auth/guard.php
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit;
}
