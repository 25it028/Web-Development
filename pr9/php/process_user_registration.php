<?php

require "db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Server-side validation
    if (empty($name) || empty($email) || empty($password)) {

        die("All fields are required.");

    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        die("Please enter a valid email address.");

    }

    if (strlen($password) < 6) {

        die("Password must contain at least 6 characters.");

    }

    // Check duplicate email
    $check_sql = "SELECT id FROM users WHERE email = ?";

    $check_stmt = $conn->prepare($check_sql);

    $check_stmt->bind_param("s", $email);

    $check_stmt->execute();

    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {

        die("This email is already registered.");

    }

    $check_stmt->close();

    // Hash password
    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert user
    $sql = "INSERT INTO users
            (name, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $name,
        $email,
        $hashed_password
    );

    if ($stmt->execute()) {

        echo "<h2>Registration Successful!</h2>";
        echo "<p>User has been registered securely using MySQLi.</p>";
        echo "<a href='../pages/register_user.html'>Register Another User</a>";

    } else {

        echo "Registration failed: " . $stmt->error;

    }

    $stmt->close();

    $conn->close();

}

?>