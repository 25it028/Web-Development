```php
<?php

// 1. Check whether form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Get form data
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $mobile = $_POST["mobile"] ?? "";
    $course = $_POST["course"] ?? "";

    // 3. Remove unnecessary spaces
    $name = trim($name);
    $email = trim($email);
    $mobile = trim($mobile);
    $course = trim($course);

    // 4. Sanitize input
    $name = htmlspecialchars($name);
    $email = htmlspecialchars($email);
    $mobile = htmlspecialchars($mobile);
    $course = htmlspecialchars($course);

    // 5. Validation
    $errors = [];

    if ($name == "") {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must contain exactly 10 digits.";
    }

    if ($course == "") {
        $errors[] = "Please select a course.";
    }

    // 6. Check for errors
    if (count($errors) > 0) {

        echo "<h2>Registration Failed</h2>";

        echo "<ul>";

        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }

        echo "</ul>";

        echo '<a href="index.php">Go Back</a>';

    } else {

        // 7. CSV file name
        $file = "registrations.csv";

        // 8. Open CSV file
        $handle = fopen($file, "a");

        // 9. Check whether file opened successfully
        if ($handle === false) {

            echo "Error: Unable to open CSV file.";

        } else {

            // 10. Add header if file is empty
            if (filesize($file) == 0) {

                fputcsv($handle, [
                    "Name",
                    "Email",
                    "Mobile",
                    "Course"
                ]);
            }

            // 11. Store student data
            fputcsv($handle, [
                $name,
                $email,
                $mobile,
                $course
            ]);

            // 12. Close file
            fclose($handle);

            // 13. Success message
            echo "<h2>Registration Successful!</h2>";

            echo "<p>Your data has been saved successfully.</p>";

            echo '<a href="index.php">Register Another Student</a><br>';

            echo '<a href="records.php">View All Records</a>';
        }
    }

} else {

    echo "Invalid Request.";
}

?>
```
