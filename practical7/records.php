```php
<?php

$file = "registrations.csv";

echo "<h2>Registered Students</h2>";

if (!file_exists($file)) {

    echo "<p>No records found.</p>";

} else {

    $handle = fopen($file, "r");

    echo "<table border='1' cellpadding='10'>";

    echo "<tr>";
    echo "<th>Name</th>";
    echo "<th>Email</th>";
    echo "<th>Mobile</th>";
    echo "<th>Course</th>";
    echo "</tr>";

    // Skip header row
    fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== false) {

        echo "<tr>";

        echo "<td>" . htmlspecialchars($data[0]) . "</td>";
        echo "<td>" . htmlspecialchars($data[1]) . "</td>";
        echo "<td>" . htmlspecialchars($data[2]) . "</td>";
        echo "<td>" . htmlspecialchars($data[3]) . "</td>";

        echo "</tr>";
    }

    echo "</table>";

    fclose($handle);
}

echo '<br><a href="index.php">Back to Registration</a>';

?>
```
