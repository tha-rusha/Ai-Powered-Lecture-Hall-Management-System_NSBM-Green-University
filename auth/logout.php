<?php
// auth/logout.php
declare(strict_types=1);

// Start the current session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    // setcookie(name, value, expire, path, domain, secure, httponly, samesite not in older PHP)
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Destroy the session data on server
session_destroy();

// Regenerate a fresh session id (optional hardening)
session_start();
session_regenerate_id(true);

// Redirect to login with a small flag
header('Location: /nsbm/auth/login.php?logged_out=1');
exit;
