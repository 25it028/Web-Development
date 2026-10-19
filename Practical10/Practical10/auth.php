
<?php
// auth.php

$isHttps = !empty($_SERVER['HTTPS'])
    && $_SERVER['HTTPS'] !== 'off';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('SESSION_TIMEOUT', 900); // 15 minutes

// Check inactivity timeout for logged-in users.
if (isset($_SESSION['user_id'])) {
    $lastActivity = $_SESSION['last_activity'] ?? 0;

    if (
        !$lastActivity ||
        (time() - $lastActivity) > SESSION_TIMEOUT
    ) {
        $_SESSION = [];

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

        session_destroy();

        header('Location: login.php?reason=timeout');
        exit;
    }

    // Update activity time after a valid request.
    $_SESSION['last_activity'] = time();
}

// CSRF token for forms.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?reason=login');
        exit;
    }
}

function requireRole(string $requiredRole): void
{
    requireLogin();

    if (($_SESSION['role'] ?? '') !== $requiredRole) {
        http_response_code(403);
        exit('403 Forbidden: You are not authorized to access this page.');
    }
}
