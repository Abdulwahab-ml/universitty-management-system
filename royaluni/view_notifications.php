<?php
session_start();
require 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission to update notification status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'], $_POST['status'])) {
    $id = intval($_POST['notification_id']);
    $status = $_POST['status'];

    if ($id > 0 && in_array($status, ['pending', 'completed'])) {
        $stmt = $conn->prepare("UPDATE notifications SET status = ? WHERE notification_id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            // Redirect back to the admin dashboard after successful update
            header("Location: view_notifications.php");
            exit();
        } else {
            $error = "Failed to update status.";
        }
        $stmt->close();
    } else {
        $error = "Invalid input data.";
    }
}

// Fetch recent notifications
$recentQuery = "SELECT notification_id, title, email, description, created_at, status FROM notifications ORDER BY created_at DESC LIMIT 5";
$recentNotifications = $conn->query($recentQuery);

// Fetch completed notifications
$completedQuery = "SELECT notification_id, title, email, description, created_at FROM notifications WHERE status = 'completed' ORDER BY created_at DESC";
$completedNotifications = $conn->query($completedQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <link rel="stylesheet" href="view_notification.css">
    <link rel="icon" href="favicon.png" type="image/png">
    
</head>
<body>
    <h1>Notifications</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Recent Notifications Section -->
    <h2>Recent Notifications</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Email</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $recentNotifications->fetch_assoc()): ?>
                <tr class="<?php echo $row['status']; ?>">
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <form method="post" action="view_notifications.php">
                            <input type="hidden" name="notification_id" value="<?php echo $row['notification_id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Completed Notifications Section -->
    <h2>Completed Notifications</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Email</th>
                <th>Description</th>
                <th>Completed At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $completedNotifications->fetch_assoc()): ?>
                <tr class="completed">
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Back to Admin Dashboard Button -->
    <form action="admin_dashboard.php">
        <button type="submit">Back to Admin Dashboard</button>
    </form>
</body>
</html>
