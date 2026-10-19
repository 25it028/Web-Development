
<?php
require_once 'auth.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: student.php');
    }
} else {
    header('Location: login.php');
}

exit;
