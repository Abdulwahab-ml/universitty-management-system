<?php
require 'auth.php';
require 'db_connection.php';

if ($_SESSION['role'] != 'Teacher') {
    header("Location: login.php");
    exit();
}

// Mark attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['students'])) {
    $course_id = $_POST['course_id'];
    $attendance_date = date('Y-m-d'); // Current date

    // Begin a transaction to mark attendance
    $conn->begin_transaction();

    try {
        foreach ($_POST['students'] as $student_id => $status) {
            // Fetch the student's name
            $student_query = "SELECT first_name, last_name FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($student_query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $student_result = $stmt->get_result();
            $student_name = '';

            if ($student_result->num_rows > 0) {
                $student_row = $student_result->fetch_assoc();
                $student_name = $student_row['first_name'] . ' ' . $student_row['last_name'];
            }

            // Check if attendance is already marked for this course and student on the same day
            $check_query = "SELECT * FROM Attendance WHERE course_id = ? AND student_id = ? AND attendance_date = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("iis", $course_id, $student_id, $attendance_date);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // If attendance is not marked, insert it
                $query = "INSERT INTO Attendance (course_id, student_id, attendance_date, status) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiss", $course_id, $student_id, $attendance_date, $status);
                $stmt->execute();
            } else {
                // Attendance is already marked for this student on this date
                echo "Attendance for student $student_name is already marked for $attendance_date.<br>";
            }
        }

        // Commit the transaction if all queries were successful
        $conn->commit();

        // Redirect to avoid resubmission
        header("Location: mark_attendance.php?success=true");
        exit;
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo "Failed to mark attendance. Error: " . $e->getMessage();
    }
}

// Fetch courses assigned to the teacher
$courses = $conn->query("
    SELECT c.course_id, c.course_name 
    FROM Courses c 
    JOIN teacher_courses tc ON c.course_id = tc.course_id 
    WHERE tc.teacher_id = {$_SESSION['user_id']}
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="mark_attendance.css">
</head>
<body>
<h2>Mark Attendance</h2>

<?php if (isset($_GET['success'])): ?>
    <p style="color: green;">Attendance marked successfully!</p>
<?php endif; ?>

<?php while ($course = $courses->fetch_assoc()): ?>
    <h3>Course: <?= htmlspecialchars($course['course_name']) ?></h3>
    <?php
    // Fetch students enrolled in this course
    $students = $conn->query("
        SELECT u.user_id, u.first_name, u.last_name 
        FROM student_enrollments e 
        JOIN users u ON e.student_id = u.user_id 
        WHERE e.course_id = {$course['course_id']}
    ");
    ?>

    <?php if ($students->num_rows > 0): ?>
        <form method="POST" action="mark_attendance.php">
            <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
            <table border="1">
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td>
                            <select name="students[<?= $student['user_id'] ?>]">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <button type="submit">Submit Attendance</button>
        </form>
    <?php else: ?>
        <p>No students enrolled in this course.</p>
    <?php endif; ?>
<?php endwhile; ?>

</body>
</html>
