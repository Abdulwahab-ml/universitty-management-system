<?php
session_start();
require 'db_connection.php';

$error_message = "";
$success_message = "";

// Check for the success message in the session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $email = $_POST['email'];
    $description = $_POST['description'];

    // Validate input
    if (empty($title) || empty($email) || empty($description)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Insert into notifications table
        $query = "INSERT INTO notifications (title, email, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $title, $email, $description);

        if ($stmt->execute()) {
            // Store the success message in the session
            $_SESSION['success_message'] = "Your message has been submitted successfully!";

            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Error message
            $error_message = "Error submitting your message: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="contact_us.css">
    <link rel="icon" href="favicon.png" type="image/png">
    
</head>
<body>
    <h2>Contact Us</h2>

    <!-- Display success or error message -->
    <?php if ($error_message): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php elseif ($success_message): ?>
        <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>

    <!-- Contact Form -->
    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5" required></textarea>

        <button type="submit">Submit</button>
    </form>

    <!-- Back Button -->
    <div class="back-button">
        <a href="index.php">Back to Home</a>
    </div>

</body>
</html>
