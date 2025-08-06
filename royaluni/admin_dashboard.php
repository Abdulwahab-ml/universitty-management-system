<?php
session_start();
require 'db_connection.php';
require 'auth.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$status_message = null;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admission_id'], $_POST['status'])) {
    $admission_id = intval($_POST['admission_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $update_query = "UPDATE admissions SET admission_status = ? WHERE admission_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $status, $admission_id);
    
    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Status updated successfully!";
    } else {
        $_SESSION['status_message'] = "Error updating status: " . $conn->error;
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle announcement submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string(trim($_POST['content']));
    $created_by = $_SESSION['user_id'];

    $query = "INSERT INTO announcements (title, content, created_by, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $title, $content, $created_by);

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Announcement posted successfully!";
    } else {
        $_SESSION['status_message'] = "Error posting announcement: " . $conn->error;
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Retrieve and display the status message, if any
if (isset($_SESSION['status_message'])) {
    $status_message = $_SESSION['status_message'];
    unset($_SESSION['status_message']);
}

 $notifications_query = "SELECT COUNT(*) AS notification_count FROM notifications"; 
 $notifications_result = $conn->query($notifications_query); 
 $notifications_row = $notifications_result->fetch_assoc();
  $notification_count = $notifications_row['notification_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="manage_courses.php">Manage Courses</a> |
        <a href="#manage-students">Manage Students</a> |
        <a href="#announcements">Announcements</a> |
        <a href="index.php">Logout</a>
    </nav>
    <hr>

  
    <hr> <?php if ($status_message): ?> <p style="color: green;"><?php echo htmlspecialchars($status_message); ?></p> <?php endif; ?> <!-- Section: Notifications -->
         <section id="notifications"> <h2>Notifications</h2> <p>You have <a href="view_notifications.php"><?php echo $notification_count; ?> notifications</a> in your inbox.</p> </section> <hr>
    <section id="manage-teachers">
        <h2>Manage Teachers</h2>
        <?php
        $query = "SELECT * FROM users WHERE role = 'Teacher'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assigned Courses</th>
                </tr>";
            while ($row = $result->fetch_assoc()) {
                $teacher_id = $row['user_id'];
                echo "<tr>
                    <td>{$row['user_id']}</td>
                    <td>{$row['first_name']} {$row['last_name']}</td>
                    <td>{$row['email']}</td>
                    <td>";
                // Fetch assigned courses
                $course_query = "SELECT courses.course_name FROM teacher_courses 
                                 JOIN courses ON teacher_courses.course_id = courses.course_id 
                                 WHERE teacher_courses.teacher_id = $teacher_id";
                $course_result = $conn->query($course_query);
                if ($course_result->num_rows > 0) {
                    while ($course = $course_result->fetch_assoc()) {
                        echo $course['course_name'] . "<br>";
                    }
                } else {
                    echo "No courses assigned.";
                }
            }
            echo "</table>";
        } else {
            echo "<p>No teachers found.</p>";
        }
        ?>
    </section>
    <hr>

    <!-- Section: Manage Students -->
    <section id="manage-students">
    <h2>Manage Students</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Total Fee (PKR)</th>
            <th>Paid Fee (PKR)</th>
            <th>Remaining Fee (PKR)</th>
            <th>Actions</th>
        </tr>
        <?php
        $query = "SELECT admissions.admission_id, users.first_name, users.last_name, users.email, admissions.admission_status, users.user_id
                  FROM admissions
                  JOIN users ON admissions.user_id = users.user_id";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $admission_id = htmlspecialchars($row['admission_id']);
                $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                $email = htmlspecialchars($row['email']);
                $status = htmlspecialchars($row['admission_status']);
                $student_id = htmlspecialchars($row['user_id']);

                // Calculate total fee
                $course_query = "SELECT COUNT(*) AS course_count FROM student_enrollments WHERE student_id = $student_id";
                $course_result = $conn->query($course_query);
                $course_row = $course_result->fetch_assoc();
                $total_fee = $course_row['course_count'] * 10000;

                // Calculate paid fee
                $payment_query = "SELECT SUM(10000) AS paid_fee FROM payments WHERE student_id = $student_id";
                $payment_result = $conn->query($payment_query);
                $payment_row = $payment_result->fetch_assoc();
                $paid_fee = $payment_row['paid_fee'];

                // Calculate remaining fee
                $remaining_fee = $total_fee - $paid_fee;

                echo "<tr>
                    <td>$admission_id</td>
                    <td>$full_name</td>
                    <td>$email</td>
                    <td>
                        <form method='POST' style='display: inline-block;'>
                            <input type='hidden' name='admission_id' value='$admission_id'>
                            <select name='status' onchange='this.form.submit()'>
                                <option value='Pending'" . ($status == 'Pending' ? ' selected' : '') . ">Pending</option>
                                <option value='Approved'" . ($status == 'Approved' ? ' selected' : '') . ">Approved</option>
                                <option value='Rejected'" . ($status == 'Rejected' ? ' selected' : '') . ">Rejected</option>
                            </select>
                        </form>
                    </td>
                    <td>" . number_format($total_fee) . "</td>
                    <td>" . number_format($paid_fee) . "</td>
                    <td>" . number_format($remaining_fee) . "</td>
                    <td>
                        <a href='view_student.php?admission_id=$admission_id'>View</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No students found.</td></tr>";
        }
        ?>
    </table>
</section>

    <hr>

    <!-- Section: Announcements -->
    <section id="announcements">
        <h2>Post Announcements</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Announcement Title" required><br><br>
            <textarea name="content" rows="4" cols="50" placeholder="Write your announcement content here..." required></textarea><br><br>
            <button type="submit">Post Announcement</button>
        </form>

        <h3>Recent Announcements</h3>
        <ul>
            <?php
            $query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 5";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $title = htmlspecialchars($row['title']);
                    $content = htmlspecialchars($row['content']);
                    $date = htmlspecialchars($row['created_at']);

                    echo "<li>
                        <strong>Title:</strong> $title<br>
                        <strong>Content:</strong> $content<br>
                        <strong>Posted on:</strong> $date
                    </li>";
                }
            } else {
                echo "<li>No announcements found.</li>";
            }
            ?>
        </ul>
    </section>
</body>
</html>
