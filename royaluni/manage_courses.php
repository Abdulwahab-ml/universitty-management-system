<?php
require 'auth.php';
require 'db_connection.php';

// Ensure only admins can access this page
if ($_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";

// Fetch all teachers
$teachers = $conn->query("SELECT user_id, first_name, last_name FROM users WHERE role = 'Teacher'");

// Fetch all courses
$courses = $conn->query("SELECT * FROM courses");

// Handle course assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_course'])) {
    $teacher_id = $_POST['teacher_id'];
    $course_id = $_POST['course_id'];

    // Check if the course is already assigned to the teacher
    $check_query = "SELECT * FROM teacher_courses WHERE teacher_id = ? AND course_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $teacher_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "This course is already assigned to the selected teacher.";
    } else {
        // Insert assignment into teacher_courses table
        $query = "INSERT INTO teacher_courses (teacher_id, course_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $teacher_id, $course_id);

        if ($stmt->execute()) {
            $success_message = "Course successfully assigned to the teacher.";
        } else {
            $error_message = "An error occurred while assigning the course.";
        }
    }
}

// Handle course unassignment via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unassign_course'])) {
    $teacher_course_id = intval($_POST['teacher_course_id']);

    $query = "DELETE FROM teacher_courses WHERE teacher_course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $teacher_course_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unassign course.']);
    }
    exit();
}

// Fetch assigned courses
$assigned_courses = $conn->query("
    SELECT 
        tc.teacher_course_id, 
        c.course_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS teacher_name 
    FROM teacher_courses tc
    JOIN courses c ON tc.course_id = c.course_id
    JOIN users u ON tc.teacher_id = u.user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Courses</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="manage_courses.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>

<h2>Manage Courses</h2>

<!-- Back to Admin Dashboard -->
<a href="admin_dashboard.php" style="display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Back to Admin Dashboard</a>

<!-- Display Messages -->
<?php if (!empty($success_message)): ?>
    <div style="color: green;"><?= $success_message ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div style="color: red;"><?= $error_message ?></div>
<?php endif; ?>

<h3>Assign Course to Teacher</h3>
<form method="POST" action="manage_courses.php">
    <label>Select Teacher:</label>
    <select name="teacher_id" required>
        <option value="">-- Select Teacher --</option>
        <?php while ($teacher = $teachers->fetch_assoc()): ?>
            <option value="<?= $teacher['user_id'] ?>">
                <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit" name="assign_course">Assign Course</button>
</form>

<h3>Assigned Courses</h3>
<table border="1" id="assignedCoursesTable">
    <tr>
        <th>Course Name</th>
        <th>Teacher Name</th>
        <th>Action</th>
    </tr>
    <?php while ($assignment = $assigned_courses->fetch_assoc()): ?>
        <tr id="row-<?= $assignment['teacher_course_id'] ?>">
            <td><?= htmlspecialchars($assignment['course_name']) ?></td>
            <td><?= htmlspecialchars($assignment['teacher_name']) ?></td>
            <td>
                <button 
                    class="unassign-course" 
                    data-id="<?= $assignment['teacher_course_id'] ?>">
                    Unassign
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<script>
$(document).ready(function () {
    // Handle Unassign Course button click
    $('.unassign-course').on('click', function () {
        const teacherCourseId = $(this).data('id');

        // Send AJAX request to unassign course
        $.ajax({
            url: 'manage_courses.php',
            method: 'POST',
            data: { unassign_course: true, teacher_course_id: teacherCourseId },
            success: function (response) {
                const result = JSON.parse(response);
                if (result.success) {
                    // Remove the row from the table
                    $('#row-' + teacherCourseId).remove();
                } else {
                    alert(result.message || 'Error unassigning course.');
                }
            },
            error: function () {
                alert('An error occurred while unassigning the course.');
            }
        });
    });
});
</script>

</body>
</html>
