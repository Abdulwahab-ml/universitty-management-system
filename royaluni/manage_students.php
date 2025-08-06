

<?php
require 'auth.php';
require 'db_connection.php';
if ($_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Update admission status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];

    $query = "UPDATE Admissions SET admission_status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $student_id);
    $stmt->execute();
}

// Get all students
$students = $conn->query("
    SELECT Admissions.user_id, Users.first_name, Users.last_name, Users.email, Admissions.admission_status 
    FROM Admissions 
    JOIN Users ON Admissions.user_id = Users.user_id 
    WHERE Users.role = 'Student'
");
?>
<link rel="stylesheet" href="path/to/style.css">
<link rel="icon" href="favicon.png" type="image/png">
<h2>Manage Students</h2>
<table border="1">
    <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($student = $students->fetch_assoc()): ?>
        <tr>
            <td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
            <td><?= $student['email'] ?></td>
            <td><?= $student['admission_status'] ?></td>
            <td>
                <form method="POST" action="manage_students.php" style="display: inline-block;">
                    <input type="hidden" name="student_id" value="<?= $student['user_id'] ?>">
                    <select name="status">
                        <option value="Pending" <?= $student['admission_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= $student['admission_status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $student['admission_status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
