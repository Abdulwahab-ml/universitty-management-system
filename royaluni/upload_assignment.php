<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Assignment</title>
    <link rel="stylesheet" href="upload_assignment.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>

<?php
require "db_connection.php";
session_start();

// Ensure only teachers can upload and delete assignments
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Teacher') {
    echo "Access denied. Only teachers can upload assignments.";
    exit;
}

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload_assignment'])) {
        $teacher_course_id = $_POST['teacher_course_id'];
        $deadline = $_POST['deadline'];
        $teacher_id = $_SESSION['user_id'];

        // Validate the deadline
        $today = date("Y-m-d");
        if ($deadline <= $today) {
            echo "<script>alert('Error: The assignment deadline must be greater than today.');</script>";
        } else {
            // Check if file is uploaded
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $target_dir = "uploads/assignments/";
                $target_file = $target_dir . basename($_FILES["file"]["name"]);

                // Move the uploaded file
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    // Validate teacher_course_id in teacher_courses
                    $query = "SELECT teacher_course_id FROM teacher_courses 
                              WHERE teacher_course_id = '$teacher_course_id' AND teacher_id = '$teacher_id'";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        // Insert the assignment
                        $sql = "INSERT INTO assignments (teacher_course_id, deadline, file_path, uploaded_by) 
                                VALUES ('$teacher_course_id', '$deadline', '$target_file', '$teacher_id')";
                        if ($conn->query($sql) === TRUE) {
                            // Set success message
                            $success_message = "Assignment uploaded successfully.";
                            // Redirect to prevent form resubmission
                            header("Location: " . $_SERVER['PHP_SELF'] . "?success=" . urlencode($success_message));
                            exit();
                        } else {
                            echo "<script>alert('Error: " . $conn->error . "');</script>";
                        }
                    } else {
                        echo "<script>alert('Invalid course selected. Please check your course assignments.');</script>";
                    }
                } else {
                    echo "<script>alert('File upload failed.');</script>";
                }
            } else {
                echo "<script>alert('Error uploading file: " . $_FILES["file"]["error"] . "');</script>";
            }
        }
    }

    // Handle Assignment Deletion
    if (isset($_POST['delete_assignment'])) {
        $assignment_id = $_POST['assignment_id'];

        // Ensure the assignment exists and belongs to the logged-in teacher
        $teacher_id = $_SESSION['user_id'];
        $query = "SELECT * FROM assignments WHERE assignment_id = '$assignment_id' AND uploaded_by = '$teacher_id'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Delete the assignment from the database
            $delete_query = "DELETE FROM assignments WHERE assignment_id = '$assignment_id'";
            if ($conn->query($delete_query) === TRUE) {
                echo "<script>alert('Assignment deleted successfully.');</script>";
            } else {
                echo "<script>alert('Error deleting assignment: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Assignment not found or you do not have permission to delete this assignment.');</script>";
        }
    }
}

// Display success message if present in the URL
if (isset($_GET['success'])) {
    $success_message = urldecode($_GET['success']);
}
?>

<!-- HTML Form for Teacher to Upload Assignment -->
<?php if ($success_message): ?>
    <div style="color: green;"> <?= $success_message ?> </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label for="teacher_course_id">Course:</label>
    <select name="teacher_course_id" required>
        <?php
        // Get the teacher's assigned courses
        $teacher_id = $_SESSION['user_id'];
        $query = "SELECT tc.teacher_course_id, c.course_name 
                  FROM teacher_courses tc
                  JOIN courses c ON tc.course_id = c.course_id
                  WHERE tc.teacher_id = '$teacher_id'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['teacher_course_id']}'>{$row['course_name']}</option>";
            }
        } else {
            echo "<option disabled>No courses assigned</option>";
        }
        ?>
    </select>
    <br>

    <label for="deadline">Assignment Deadline:</label>
    <input type="date" name="deadline" required>
    <br>

    <label for="file">Upload Assignment (PDF):</label>
    <input type="file" name="file" accept=".pdf" required>
    <br>

    <button type="submit" name="upload_assignment">Upload Assignment</button>
</form>

<!-- Table for displaying assignments uploaded by the logged-in teacher -->
<h2>Uploaded Assignments</h2>
<table border="1">
    <tr>
        <th>Course</th>
        <th>Upload Date</th>
        <th>Assignment</th>
        <th>Action</th>
    </tr>
    <?php
    // Fetch the assignments uploaded by the logged-in teacher
    $query = "SELECT a.assignment_id, c.course_name, a.deadline, a.file_path 
              FROM assignments a
              JOIN teacher_courses tc ON a.teacher_course_id = tc.teacher_course_id
              JOIN courses c ON tc.course_id = c.course_id
              WHERE a.uploaded_by = '$teacher_id'";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $deadline_class = (strtotime($row['deadline']) > strtotime(date('Y-m-d'))) ? 'style="color: green;"' : '';
        echo "<tr>
                <td>{$row['course_name']}</td>
                <td $deadline_class>{$row['deadline']}</td>
                <td><a href='{$row['file_path']}' target='_blank'>View Assignment</a></td>
                <td>
                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this assignment?\")'>
                        <input type='hidden' name='assignment_id' value='{$row['assignment_id']}'>
                        <button type='submit' name='delete_assignment'>Delete</button>
                    </form>
                </td>
              </tr>";
    }
    ?>
</table>

<!-- Table for displaying student submissions -->
<h2>Student Submissions</h2>
<table border="1">
    <tr>
        <th>Course</th>
        <th>Assignment Deadline</th>
        <th>Submission Date</th>
        <th>Submission</th>
        <th>Student Name</th>
    </tr>
    <?php
    // Fetch and display submissions for the assignments uploaded by the logged-in teacher
    $query = "SELECT DISTINCT s.submission_id, c.course_name, a.deadline, s.submitted_at, s.file_path, u.first_name, u.last_name 
              FROM submissions s
              JOIN assignments a ON s.assignment_id = a.assignment_id
              JOIN teacher_courses tc ON a.teacher_course_id = tc.teacher_course_id
              JOIN courses c ON tc.course_id = c.course_id
              JOIN users u ON s.student_id = u.user_id
              WHERE tc.teacher_id = '$teacher_id'";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['course_name']}</td>
                <td>{$row['deadline']}</td>
                <td>{$row['submitted_at']}</td>
                <td><a href='{$row['file_path']}' target='_blank'>View Submission</a></td>
                <td>{$row['first_name']} {$row['last_name']}</td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
