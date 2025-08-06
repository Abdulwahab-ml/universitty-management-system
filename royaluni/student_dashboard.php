<?php
session_start();
require 'auth.php'; // Handles user authentication
require 'db_connection.php'; // Database connection

// Check if the logged-in user is a student
if ($_SESSION['role'] != 'Student') {
    header("Location: login.php");
    exit();
}

// Get the logged-in student's user ID
$userId = $_SESSION['user_id'];

// Fetch student details (name, email, profile picture, phone number, age)
$query = "SELECT users.first_name, users.last_name, users.email, 
                 admissions.profile_picture, admissions.phone_number, admissions.age 
          FROM users 
          JOIN admissions ON users.user_id = admissions.user_id 
          WHERE users.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "Error: User details not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_dashboard.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <style>
        /* CSS for minimal and stylish profile picture */
        .profile {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%; /* Makes the image circular */
            margin-right: 20px;
            border: 2px solid #007BFF;
        }
        .profile-info {
            flex: 1;
        }
        .profile-info p {
            margin: 5px 0;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center;
            color: #000;
        }
        nav ul li {
            display: inline-block;
            margin: 0 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: #007BFF;
        }
        nav ul li a:hover {
            color: #000;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>!</h1>
    </header>
    
    <div class="profile">
        <!-- Profile Picture -->
        <img src="uploads/<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture">
        
        <!-- Profile Information -->
        <div class="profile-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($student['phone_number']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($student['age']); ?></p>
        </div>
    </div>
    
    <nav>
        <ul>
            <li><a href="submit_assignment.php">Submit Assignment</a></li>
            <li><a href="enroll_course.php">Enroll in Courses</a></li>
            <li><a href="pay_fee.php">Pay Fees</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="index.php">logout</a></li>
        </ul>
    </nav>
</body>
</html>
