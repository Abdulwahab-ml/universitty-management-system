<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = 'Student'; // Default role for signup

    $conn->begin_transaction(); // Start transaction
    try {
        // Insert the new user into the users table
        $query = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);
        $stmt->execute();
        
        // Get the user_id of the newly inserted user
        $user_id = $conn->insert_id;

        // Insert default data into the admissions table
        $admission_query = "INSERT INTO admissions (user_id, admission_status) VALUES (?, 'Not Applied')";
        $admission_stmt = $conn->prepare($admission_query);
        $admission_stmt->bind_param("i", $user_id);
        $admission_stmt->execute();

        // Commit transaction
        $conn->commit();

        // Redirect to login page with success notification
        $_SESSION['success_message'] = "Account created successfully. Please log in.";
        header("Location: login.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback(); // Rollback transaction on error

        if ($e->getCode() == 1062) { // Duplicate email error
            $_SESSION['error_message'] = "The email is already registered. Please log in.";
        } else {
            $_SESSION['error_message'] = "An error occurred. Please try again.";
        }
        header("Location: signup.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <script>
        // Display pop-up notifications
        function showNotification(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <h2>Sign Up</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
        <script>showNotification("<?= $_SESSION['error_message'] ?>");</script>
        <?php unset($_SESSION['error_message']); ?>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <script>showNotification("<?= $_SESSION['success_message'] ?>");</script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form method="POST" action="signup.php">
        <label>First Name:</label>
        <input type="text" name="first_name" required><br>
        <label>Last Name:</label>
        <input type="text" name="last_name" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>
