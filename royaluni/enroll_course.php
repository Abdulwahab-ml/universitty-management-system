<?php
require 'auth.php';
require 'db_connection.php';

if ($_SESSION['role'] != 'Student') {
    header("Location: login.php");
    exit();
}

// Handle AJAX request for enrolling in a course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    $student_id = $_SESSION['user_id'];

    // Check if the student has already enrolled in 6 courses
    $check_query = "SELECT COUNT(*) AS enrolled_courses FROM student_enrollments WHERE student_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $enrolled_courses = $row['enrolled_courses'];

    // If the student is already enrolled in 6 courses, show an error
    if ($enrolled_courses >= 6) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot enroll in more than 6 courses.']);
        exit;
    }

    // Check if the student is already enrolled in the course
    $check_enroll_query = "SELECT * FROM student_enrollments WHERE course_id = ? AND student_id = ?";
    $stmt = $conn->prepare($check_enroll_query);
    $stmt->bind_param("ii", $course_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You are already enrolled in this course.']);
        exit;
    }

    // Enroll the student in the course
    $query = "INSERT INTO student_enrollments (course_id, student_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ii", $course_id, $student_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course enrolled successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to enroll in the course.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
    exit;
}

$total_lectures = 32; // Total lectures for all courses

// Fetch all enrolled courses with attendance data for the student
$enrolled_courses = $conn->query("
    SELECT 
        se.course_id, 
        c.course_name, 
        c.course_description, 
        CONCAT(u.first_name, ' ', u.last_name) AS teacher_name,
        COUNT(a.attendance_id) AS absences, 
        SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) AS presents,
        ($total_lectures - COUNT(a.attendance_id)) * 100 / $total_lectures AS attendance_percentage
    FROM 
        student_enrollments se
    JOIN 
        courses c ON se.course_id = c.course_id
    LEFT JOIN 
        teacher_courses tc ON c.course_id = tc.course_id
    LEFT JOIN 
        users u ON tc.teacher_id = u.user_id
    LEFT JOIN 
        Attendance a ON se.course_id = a.course_id AND se.student_id = a.student_id
    WHERE 
        se.student_id = {$_SESSION['user_id']}
    GROUP BY 
        se.course_id, c.course_name, c.course_description, u.first_name, u.last_name
");

// Fetch all available courses
$courses = $conn->query("SELECT * FROM courses");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Enroll in Courses</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="enroll_course.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
<h2>Enroll in Courses</h2>

<form id="enrollForm">
    <label>Course:</label>
    <select name="course_id" id="courseSelect" required>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
        <?php endwhile; ?>
    </select><br>
    <button type="submit">Enroll</button>
</form>

<h3>Enrolled Courses</h3>
<table border="1" id="enrolledCourses">
    <tr>
        <th>Course Name</th>
        <th>Description</th>
        <th>Teacher</th>
        <th>Attendance Percentage</th>
    </tr>
    <?php while ($row = $enrolled_courses->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['course_description']) ?></td>
            <td><?= htmlspecialchars($row['teacher_name']) ?></td>
            <td class="<?= isset($row['attendance_percentage']) && $row['attendance_percentage'] <= 70 ? 'attendance-low' : 'attendance-high' ?>">
    <?= isset($row['attendance_percentage']) ? number_format($row['attendance_percentage'], 2) . '%' : '100%' ?>
</td>

        </tr>
    <?php endwhile; ?>
</table>

<script>
$(document).ready(function() {
    $('#enrollForm').on('submit', function(e) {
        e.preventDefault();
        const course_id = $('#courseSelect').val();

        $.ajax({
            url: 'enroll_course.php', // Same file for processing
            type: 'POST',
            data: { course_id: course_id },
            dataType: 'json',
            success: function(response) {
                alert(response.message);
                if (response.status === 'success') {
                    // Reload the list of enrolled courses without refreshing the page
                    $('#enrolledCourses').load(location.href + ' #enrolledCourses > *');
                    // Remove the enrolled course from the dropdown
                    $('#courseSelect option[value="' + course_id + '"]').remove();
                }
            },
        });
    });
});
</script>
<a href="student_dashboard.php">Back to Dashboard</a>

</body>
</html>
