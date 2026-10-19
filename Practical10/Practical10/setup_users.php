
<?php
require_once 'db.php';

$users = [
    [
        'Admin User',
        'admin@example.com',
        'Admin@123',
        'admin'
    ],
    [
        'Student User',
        'student@example.com',
        'Student@123',
        'student'
    ]
];

$sql = "INSERT INTO users (name, email, password_hash, role)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        password_hash = VALUES(password_hash),
        role = VALUES(role)";

$stmt = $pdo->prepare($sql);

foreach ($users as $user) {
    $hash = password_hash($user[2], PASSWORD_DEFAULT);
    $stmt->execute([
        $user[0],
        $user[1],
        $hash,
        $user[3]
    ]);
}

echo "Seed users created successfully.";
