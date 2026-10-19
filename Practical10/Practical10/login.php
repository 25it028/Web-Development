
<?php
require_once 'auth.php';
require_once 'db.php';

// Redirect authenticated users to their respective dashboards.
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: student.php');
    }
    exit;
}

$error = '';
$reason = $_GET['reason'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate CSRF token.
    $csrf = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrf) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrf)
    ) {
        http_response_code(400);
        exit('Invalid request. Please reload the login page.');
    }

    // Read and validate input.
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        !is_string($email) ||
        !is_string($password) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $error = 'Invalid email or password.';
    } else {

        // Retrieve user using a prepared statement.
        $stmt = $pdo->prepare(
            'SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = ?
             LIMIT 1'
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify the submitted password against its stored hash.
        if (
            $user &&
            password_verify($password, $user['password_hash'])
        ) {

            // Prevent session fixation.
            session_regenerate_id(true);

            // Create a fresh authenticated session.
            $_SESSION = [
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'last_activity' => time(),
                'csrf_token' => bin2hex(random_bytes(32))
            ];

            // Update last successful login timestamp.
            $update = $pdo->prepare(
                'UPDATE users
                 SET last_login = NOW()
                 WHERE id = ?'
            );

            $update->execute([$user['id']]);

            // Optional Remember Me feature.
            if (
                isset($_POST['remember']) &&
                $_POST['remember'] === '1'
            ) {
                // Generate a secure selector and validator.
                $selector = bin2hex(random_bytes(12));
                $validator = bin2hex(random_bytes(32));

                // Store only the validator hash in the database.
                $validatorHash = hash('sha256', $validator);

                $expires = time() + (30 * 24 * 60 * 60);
                $expiresAt = date('Y-m-d H:i:s', $expires);

                $tokenStmt = $pdo->prepare(
                    'INSERT INTO remember_tokens
                     (user_id, selector, validator_hash, expires_at)
                     VALUES (?, ?, ?, ?)'
                );

                $tokenStmt->execute([
                    $user['id'],
                    $selector,
                    $validatorHash,
                    $expiresAt
                ]);

                $isHttps = !empty($_SERVER['HTTPS'])
                    && $_SERVER['HTTPS'] !== 'off';

                // Store the raw token only in the browser cookie.
                setcookie(
                    'remember_me',
                    $selector . ':' . $validator,
                    [
                        'expires' => $expires,
                        'path' => '/',
                        'secure' => $isHttps,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
            }

            // Redirect according to the user's role.
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } elseif ($user['role'] === 'student') {
                header('Location: student.php');
            } else {
                // Reject unexpected roles.
                $_SESSION = [];
                session_regenerate_id(true);
                http_response_code(403);
                exit('Unauthorized user role.');
            }

            exit;

        } else {
            // Avoid revealing whether an email exists.
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Secure Login - Practical 10</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
        }

        .login-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #1e293b;
        }

        label {
            display: block;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        button {
            width: 100%;
            padding: 11px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            padding: 10px;
            border-radius: 5px;
        }

        .message {
            color: #1d4ed8;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h2>Secure Login</h2>

    <?php if ($reason === 'timeout'): ?>
        <p class="message">
            Your session expired due to inactivity.
            Please log in again.
        </p>
    <?php elseif ($reason === 'login'): ?>
        <p class="message">
            Please log in to continue.
        </p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            required
            autocomplete="username"
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
        >

        <label class="remember">
            <input
                type="checkbox"
                name="remember"
                value="1"
            >
            Remember me for 30 days
        </label>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
