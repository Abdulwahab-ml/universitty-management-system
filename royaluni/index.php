<?php
session_start();

if (isset($_SESSION['flash_message'])) {
    echo "<script>alert('" . $_SESSION['flash_message'] . "');</script>";
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Royal University</title>
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="favicon.png" type="image/png">
   
</head>
<body>
    <header>
        <img src="favicon.png" alt="Royal University Logo">
        <nav>
            <a href="courses.php">Courses</a>
            <a href="announcements.php">Announcements</a>
            <a href="contact_us.php">Contact Us</a>
        </nav>
    </header>
    <div class="login-signup">
        <?php if (isset($_SESSION['user_name'])): ?>
            <a href="index.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </div>
    <div class="hero">
        <h1>Welcome to Royal University</h1>
        <p>Providing world-class education and innovation for a brighter future.</p>
    </div>
    <div class="container">
        <h2>About Royal University</h2>
        <p>At Royal University, we are committed to providing world-class education and fostering an environment for innovation and learning. Our LMS helps students, teachers, and administrators manage their activities efficiently.</p>
        <h2>Features of the LMS</h2>
        <ul>
            <li>Secure Login for Students, Teachers, and Admins.</li>
            <li>Manage Courses, Assignments, and Attendance.</li>
            <li>Online Fee Payment for Students.</li>
            <li>Real-time Notifications and Announcements.</li>
            <li>Comprehensive Dashboard for All Users.</li>
        </ul>
    </div>
    <footer>
        <p>&copy; <?= date("Y"); ?> Royal University. All rights reserved.</p>
        <p><a href="privacy_policy.php">Privacy Policy</a> | <a href="terms_of_service.php">Terms of Service</a></p>
    </footer>
</body>
</html>
