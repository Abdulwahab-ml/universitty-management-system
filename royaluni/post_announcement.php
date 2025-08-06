<?php
require 'auth.php';
require 'db_connection.php';
if ($_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Post announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $created_by = $_SESSION['user_id'];

    $query = "INSERT INTO Announcements (title, content, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $content, $created_by);
    $stmt->execute();
}
?>
<link rel="icon" href="favicon.png" type="image/png">
<h2>Post Announcement</h2>
<form method="POST" action="post_announcement.php">
    <label>Title:</label>
    <input type="text" name="title" required><br>
    <label>Content:</label>
    <textarea name="content" required></textarea><br>
    <button type="submit">Post Announcement</button>
</form>
