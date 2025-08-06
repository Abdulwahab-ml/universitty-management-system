<?php
require 'db_connection.php';

if (!isset($_GET['admission_id'])) {
    echo "Admission ID is required.";
    exit();
}

$admission_id = $_GET['admission_id'];

// Fetch student details
$query = "
    SELECT 
        users.first_name, 
        users.last_name, 
        users.email, 
        admissions.age, 
        admissions.metric_marks, 
        admissions.metric_stream, 
        admissions.fsc_marks, 
        admissions.fsc_stream, 
        admissions.profile_picture, 
        admissions.address, 
        admissions.phone_number
    FROM admissions
    JOIN users ON admissions.user_id = users.user_id
    WHERE admissions.admission_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admission_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No student found with this Admission ID.";
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Details</title>
    <link rel="stylesheet" href="view_student.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
    <h1>Student Details</h1>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
    <p><strong>Metric Marks:</strong> <?php echo htmlspecialchars($row['metric_marks']); ?> (<?php echo htmlspecialchars($row['metric_stream']); ?>)</p>
    <p><strong>FSC Marks:</strong> <?php echo htmlspecialchars($row['fsc_marks']); ?> (<?php echo htmlspecialchars($row['fsc_stream']); ?>)</p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></p>
    <p><strong>Profile Picture:</strong></p>
    <img src="uploads/<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture" width="150" height="150">
    <br><br>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
