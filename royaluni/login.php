<?php
session_start();
require 'db_connection.php';
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check user credentials
    $query = "SELECT user_id, first_name, last_name, role, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];

            if ($user['role'] === 'Student') {
                // Check admission status
                $status_query = "SELECT admission_status FROM admissions WHERE user_id = ?";
                $status_stmt = $conn->prepare($status_query);
                $status_stmt->bind_param("i", $user['user_id']);
                $status_stmt->execute();
                $status_result = $status_stmt->get_result();

                if ($status_result->num_rows > 0) {
                    $status = $status_result->fetch_assoc()['admission_status'];

                    if ($status === 'Pending') {
                        header("Location: pending.php");
                        exit();
                    } elseif ($status === 'Rejected') {
                        $_SESSION['error_message'] = "Your admission has been rejected. Please contact the admin.";
                        header("Location: login.php");
                        exit();
                    } elseif ($status === 'Approved') {
                        header("Location: student_dashboard.php");
                        exit();
                    } elseif ($status === 'not applied') {
                        header("Location: admission.php");
                        exit();
                    }
                } else {
                    // Insert 'not applied' status if no record exists
                    $insert_status_query = "INSERT INTO admissions (user_id, admission_status) VALUES (?, 'not applied')";
                    $insert_stmt = $conn->prepare($insert_status_query);
                    $insert_stmt->bind_param("i", $user['user_id']);
                    $insert_stmt->execute();

                    // Redirect to admission page
                    header("Location: admission.php");
                    exit();
                }
            } elseif ($user['role'] === 'Admin') {
                header("Location: admin_dashboard.php");
                exit();
            } elseif ($user['role'] === 'Teacher') {
                header("Location: teacher_dashboard.php");
                exit();
            }
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error_message'] = "No account found with this email.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
    <h2>ROYAL UNI</h2>
    <h2>Login</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red;"><?= $_SESSION['error_message'] ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    <p><a href="index.php">Go to Home</a></p>

    <!-- Additional Features -->
    <h3>New to Royal University?</h3>
    <p>We are committed to providing a seamless learning experience. <strong><a href="signup.php">Sign up today</a></strong> and become part of a vibrant learning community!</p>


    <h3>Forgot Password?</h3>
    <p>Don't worry! <a href="forgot_password.php">Reset your password</a>.</p>

    
    <h3>About Royal University LMS</h3>
    <p>Our Learning Management System (LMS) offers students, teachers, and administrators an easy way to manage their tasks efficiently. Join us to take your learning experience to the next level!</p>

    <footer>
        <p>&copy; <?= date("Y"); ?> Royal University. All rights reserved.</p>
    </footer>
</body>
</html>
