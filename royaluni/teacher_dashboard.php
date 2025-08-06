<?php
require 'auth.php';
require 'db_connection.php';
if ($_SESSION['role'] != 'Teacher') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="icon" href="favicon.png" type="image/png">
    
</head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
<div class="dashboard-links">
    <a href="upload_assignment.php">Upload Assignment</a>
    <a href="mark_attendance.php">Mark Attendance</a>
    <a href="announcements.php">Announcements</a>
    <a href="profile.php">View Profile</a>
</div>
<a href="index.php" class="logout">Logout</a>
</body>
</html>
