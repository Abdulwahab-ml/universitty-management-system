<?php
require 'auth.php';
require 'db_connection.php';

if ($_SESSION['role'] != 'Teacher') {
    header("Location: login.php");
    exit();
}

$teacherId = $_SESSION['user_id'];

// Fetching teacher details
$query = "SELECT first_name, last_name, email FROM users WHERE role = 'Teacher' AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
} else {
    die("Teacher details not found.");
}

// Fetching assigned courses
$queryCourses = "SELECT courses.course_name FROM courses 
                 INNER JOIN teacher_courses ON courses.course_id = teacher_courses.course_id 
                 WHERE teacher_courses.teacher_id = ?";
$stmt = $conn->prepare($queryCourses);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$resultCourses = $stmt->get_result();

$courses = [];
if ($resultCourses->num_rows > 0) {
    while ($course = $resultCourses->fetch_assoc()) {
        $courses[] = $course['course_name'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f7;
            color: #333;
            text-align: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            text-align: left;
        }
        .card h3 {
            margin-bottom: 15px;
            color: #6a1b9a;
        }
        .card p {
            margin: 5px 0;
            color: #333;
        }
        .card ul {
            list-style: none;
            padding: 0;
        }
        .card ul li {
            margin: 5px 0;
        }
        a {
            display: inline-block;
            margin: 20px auto;
            font-size: 1.2em;
            color: #6a1b9a;
            text-decoration: none;
            padding: 10px 15px;
            background: #fff;
            border: 2px solid #6a1b9a;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }
        a:hover {
            background: #6a1b9a;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="card">
    <h3>Teacher Profile</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($teacher['first_name'] . " " . $teacher['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>
    <p><strong>Assigned Courses:</strong></p>
    <?php if (!empty($courses)) { ?>
        <ul>
            <?php foreach ($courses as $course) { ?>
                <li><?php echo htmlspecialchars($course); ?></li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No courses assigned.</p>
    <?php } ?>
</div>
<a href="teacher_dashboard.php">Back to Dashboard</a>
</body>
</html>
