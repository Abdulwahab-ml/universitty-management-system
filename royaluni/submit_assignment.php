<?php
session_start();
require 'db_connection.php'; // Include your database connection

// Check if the student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Student') {
    echo "Access denied. Only students can access this page.";
    exit;
}

$student_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];

    // Check if the student has already submitted this assignment
    $check_query = "SELECT * FROM submissions WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $error_message = "You have already submitted this assignment.";
    } else {
        // Check if file is uploaded
        if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/submissions/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Insert submission into the database
                $sql = "INSERT INTO submissions (assignment_id, student_id, file_path, submitted_at) 
                        VALUES ('$assignment_id', '$student_id', '$target_file', NOW())";
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Assignment submitted successfully!";
                } else {
                    $error_message = "Database error: " . $conn->error;
                }
            } else {
                $error_message = "Failed to upload the file.";
            }
        } else {
            $error_message = "Error uploading file: " . $_FILES["file"]["error"];
        }
    }
}

// Fetch assignments for the logged-in student
$query = "
    SELECT a.assignment_id, a.deadline, c.course_name, a.file_path, 
           IF(s.assignment_id IS NOT NULL, 'Submitted', 'Not Submitted') AS submission_status
    FROM assignments a
    INNER JOIN teacher_courses tc ON a.teacher_course_id = tc.teacher_course_id
    INNER JOIN courses c ON tc.course_id = c.course_id
    INNER JOIN student_enrollments se ON se.course_id = c.course_id
    LEFT JOIN submissions s ON a.assignment_id = s.assignment_id AND s.student_id = '$student_id'
    WHERE se.student_id = '$student_id'
    ORDER BY a.deadline ASC;
";
$assignments_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assignments</title>
    <link rel="stylesheet" href="submit_assignment.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
    <h1>Assignments</h1>

    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div style="color: green;"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div style="color: red;"><?= $error_message ?></div>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>Course</th>
                <th>Deadline</th>
                <th>Assignment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($assignments_result->num_rows > 0): ?>
                <?php while ($row = $assignments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['course_name'] ?></td>
                        <td><?= $row['deadline'] ?></td>
                        <td>
                            <a href="<?= $row['file_path'] ?>" target="_blank">View Assignment</a>
                        </td>
                        <td><?= $row['submission_status'] ?></td>
                        <td>
                            <?php if ($row['submission_status'] == 'Not Submitted'): ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="assignment_id" value="<?= $row['assignment_id'] ?>">
                                    <input type="file" name="file" accept=".pdf" required>
                                    <button type="submit">Upload Solution</button>
                                </form>
                            <?php else: ?>
                                <span>Submitted</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No assignments available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="student_dashboard.php" class="back-to-dashboard">Back to Dashboard</a>

</body>
</html>
