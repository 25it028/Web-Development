
<?php
require_once 'auth.php';
requireRole('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <p>Welcome,
        <?= htmlspecialchars(
            $_SESSION['name'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>!
    </p>

    <p>Your role is:
        <?= htmlspecialchars(
            $_SESSION['role'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>Only authorized administrators can view this page.</p>

    <form action="logout.php" method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars(
                   $_SESSION['csrf_token'],
                   ENT_QUOTES,
                   'UTF-8'
               ) ?>">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
