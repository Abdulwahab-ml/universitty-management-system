<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>announcement</title>
    <link rel="stylesheet" href="announcements.css">
</head>
<body>
    
<?php
require 'auth.php';
require 'db_connection.php';

// Fetch announcements
$announcements = $conn->query("
    SELECT Announcements.title, Announcements.content, Users.first_name, Users.last_name, Announcements.created_at 
    FROM Announcements 
    JOIN Users ON Announcements.created_by = Users.user_id 
    WHERE Users.role IN ('Admin', 'Teacher')
    ORDER BY Announcements.created_at DESC
");
?>

<h2>Announcements</h2>
<?php if ($announcements->num_rows > 0): ?>
    <ul>
        <?php while ($announcement = $announcements->fetch_assoc()): ?>
            <li>
                <h3><?= $announcement['title'] ?></h3>
                <p><?= $announcement['content'] ?></p>
                <small>Posted by: <?= $announcement['first_name'] ?> on <?= $announcement['created_at'] ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No announcements available.</p>
<?php endif; ?>
<a href="student_dashboard.php">Back to Dashboard</a>
</body>
</html>