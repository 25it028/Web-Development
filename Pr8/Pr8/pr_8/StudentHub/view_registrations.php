<?php

require_once "db.php";

$sql = "SELECT
            r.registration_id,
            s.name,
            s.email,
            e.event_name,
            e.event_date,
            e.venue,
            r.registration_date
        FROM registrations r
        INNER JOIN students s
            ON r.student_id = s.student_id
        INNER JOIN events e
            ON r.event_id = e.event_id
        ORDER BY r.registration_date DESC";

$stmt = $pdo->query($sql);

$registrations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>

<head>
    <title>StudentHub Registrations</title>
</head>

<body>

<h2>StudentHub Event Registrations</h2>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Email</th>
    <th>Event</th>
    <th>Date</th>
    <th>Venue</th>
    <th>Registered On</th>
</tr>

<?php foreach ($registrations as $registration): ?>

<tr>

    <td>
        <?php echo htmlspecialchars($registration["registration_id"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["name"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["email"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["event_name"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["event_date"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["venue"]); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($registration["registration_date"]); ?>
    </td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>