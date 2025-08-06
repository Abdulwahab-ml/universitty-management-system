<?php
session_start(); // Start the session

require 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Pending</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
    <h1>Your Application is Under Review</h1>
    <p>Thank you for submitting your application. Your application is currently being reviewed. Please check back later for updates.</p>
    <a href="index.php">Return to Home Page</a>
</body>
</html>