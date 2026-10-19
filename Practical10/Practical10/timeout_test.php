
<?php
require_once 'auth.php';
requireLogin();

// Simulate 16 minutes of inactivity.
$_SESSION['last_activity'] = time() - 960;

echo 'Timeout simulated. Now open admin.php or student.php.';
