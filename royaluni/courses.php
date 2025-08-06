<?php
require 'db_connection.php';

$query = "
    SELECT 
        c.course_name,
        c.course_description,
        CONCAT(u.first_name, ' ', u.last_name) AS teacher_name,
        u.email AS teacher_email
    FROM 
        courses c
    INNER JOIN 
        teacher_courses tc ON c.course_id = tc.course_id
    INNER JOIN 
        users u ON tc.teacher_id = u.user_id
    WHERE 
        u.role = 'teacher'
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses and Teachers</title>
    <link rel="stylesheet" href="courses.css">
    <link rel="icon" href="favicon.png" type="image/png">
    
</head>
<body>
    <h1>Courses and Their Teachers</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Description</th>
                    <th>Teacher Name</th>
                    <th>Teacher Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_email']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No courses or teachers found.</p>
    <?php endif; ?>

    <a href="index.php" class="go-back">Go Back</a>

</body>
</html>

<?php
$conn->close();
?>
