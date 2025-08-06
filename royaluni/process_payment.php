<?php
require 'auth.php'; // Ensure user authentication
require 'db_connection.php'; // Database connection

$student_id = $_POST['student_id']; // Ensure this key matches what is being sent from your form
$course_id = $_POST['course_id'];
$payment_method = $_POST['payment_method'];

// Insert payment record into the database
$sql = "INSERT INTO payments (student_id, course_id, payment_date) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $course_id);

try {
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo "<script>alert('Payment has been made successfully!'); window.location.href='pay_fee.php';</script>";
} catch (mysqli_sql_exception $e) {
    die("Error: " . $e->getMessage());
}
?>
