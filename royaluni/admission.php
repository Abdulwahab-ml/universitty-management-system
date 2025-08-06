<?php
require 'db_connection.php';
require 'auth.php';

// Ensure only students can access this page
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $dob = $_POST['dob'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $metric_marks = $_POST['metric_marks'];
    $metric_stream = $_POST['metric_stream'];
    $fsc_marks = $_POST['fsc_marks'];
    $fsc_stream = $_POST['fsc_stream'];
    $degree = $_POST['degree'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $upload_dir = "uploads/";

    // Validate and parse DOB
    $dob_date = new DateTime($dob);
    $current_date = new DateTime();
    $age = $current_date->diff($dob_date)->y;

    if ($age <= 17) {
        $error_message = "Error: Age must be greater than 17 to submit the admission form.";
    }

    if (empty($error_message)) {
        // Check if the student already submitted an admission form
        $check_query = "SELECT * FROM admissions WHERE user_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the existing admission record
            $target_file = $upload_dir . basename($profile_picture);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $update_query = "
                    UPDATE admissions
                    SET dob = ?, age = ?, phone_number = ?, address = ?, 
                        metric_marks = ?, metric_stream = ?, fsc_marks = ?, fsc_stream = ?, 
                        profile_picture = ?, degree = ?, admission_status = 'Pending'
                    WHERE user_id = ?
                ";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param(
                    "sississsssi",
                    $dob, $age, $phone_number, $address, $metric_marks, $metric_stream, 
                    $fsc_marks, $fsc_stream, $profile_picture, $degree, $user_id
                );

                if (!$stmt->execute()) {
                    $error_message = "Error: " . $conn->error;
                }
            } else {
                $error_message = "Error uploading profile picture.";
            }
        } else {
            // Insert a new admission record
            $target_file = $upload_dir . basename($profile_picture);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $insert_query = "
                    INSERT INTO admissions (
                        user_id, dob, age, phone_number, address, metric_marks, 
                        metric_stream, fsc_marks, fsc_stream, profile_picture, degree, admission_status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
                ";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param(
                    "isississsss",
                    $user_id, $dob, $age, $phone_number, $address, $metric_marks, 
                    $metric_stream, $fsc_marks, $fsc_stream, $profile_picture, $degree
                );

                if (!$stmt->execute()) {
                    $error_message = "Error: " . $conn->error;
                }
            } else {
                $error_message = "Error uploading profile picture.";
            }
        }
    }
}

// Ensure `$stmt` is executed only when it has been initialized successfully
if (!empty($error_message)) {
    echo "<script>alert('$error_message');</script>";
} elseif (isset($stmt) && $stmt->execute()) {
    echo "<script>
        alert('Admission form submitted successfully!');
        setTimeout(() => { window.location.href = 'index.php'; }, 3000);
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>admission form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    

<h2>Student Admission Form</h2>
<form method="POST" action="admission.php" enctype="multipart/form-data">
    <label>Date of Birth:</label>
    <input type="date" name="dob" required><br>

    <label>Phone Number:</label>
    <input type="text" name="phone_number" required><br>

    <label>Address:</label>
    <textarea name="address" required></textarea><br>

    <label>Metric Marks:</label>
    <input type="number" name="metric_marks" required><br>

    <label>Metric Stream:</label>
    <select name="metric_stream" required>
        <option value="Science">Science</option>
        <option value="Arts">Arts</option>
    </select><br>

    <label>FSC Marks:</label>
    <input type="number" name="fsc_marks" required><br>

    <label>FSC Stream:</label>
    <select name="fsc_stream" required>
        <option value="Pre-Engineering">Pre-Engineering</option>
        <option value="ICS">ICS</option>
        <option value="ICOM">ICOM</option>
        <option value="Pre-Medical">Pre-Medical</option>
    </select><br>

    <label>Degree:</label>
    <select name="degree" required>
        <option value="BS Computer Science">BS Computer Science</option>
        <option value="BS Software Engineering">BS Software Engineering</option>
        <option value="BS Information Technology">BS Information Technology</option>
    </select><br>

    <label>Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*" required><br>

    <button type="submit">Submit Admission Form</button>
</form>

</body>
</html>
