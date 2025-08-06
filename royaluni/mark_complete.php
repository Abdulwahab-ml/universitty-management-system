<?php
session_start();
require 'db_connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Get notification ID from the URL
$notification_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($notification_id > 0) {
    // Update the status of the notification to 'completed'
    $query = "UPDATE notifications SET status = 'completed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $notification_id);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Notification marked as complete!";
    } else {
        $_SESSION['flash_message'] = "Error updating notification: " . $conn->error;
    }

    header("Location: admin_dashboard.php");
} else {
    $_SESSION['flash_message'] = "Invalid notification ID.";
    header("Location: admin_dashboard.php");
}

$conn->close();
?>
