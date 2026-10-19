
<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

$csrf = $_POST['csrf_token'] ?? '';

if (!is_string($csrf) ||
    !hash_equals($_SESSION['csrf_token'], $csrf)) {
    http_response_code(403);
    exit('Invalid request.');
}

// Remove all session variables.
$_SESSION = [];

// Expire the session cookie.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]
    );
}

// Destroy the server-side session.
session_destroy();

header('Location: login.php?reason=login');
exit;
