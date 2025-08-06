<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $fake_captcha = $_POST['fake_captcha'];

    // Check if the fake CAPTCHA is checked
    if ($fake_captcha !== 'on') {
        $_SESSION['error_message'] = "Please confirm you're not a robot.";
        header("Location: forgot_password.php");
        exit();
    }

    // Validate password confirmation
    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: forgot_password.php");
        exit();
    }

    // Hash the new password and update in the database
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $hashed_password, $email);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Password updated successfully!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update password. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot_password.css">
    <link rel="icon" href="favicon.png" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #6a1b9a, #f50057);
            color: #333;
            text-align: center;
            padding: 50px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        h2 {
            font-size: 2em;
            color: white;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 0.5s;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            text-align: left;
            opacity: 0;
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 1s;
        }

        label {
            display: block;
            font-size: 1.1em;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #f50057;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #6a1b9a;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #f50057;
            transform: scale(1.05);
        }

        footer {
            margin-top: 30px;
            color: white;
            font-size: 0.9em;
            text-align: center;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #ffeb3b;
        }

        p {
            color: #fff;
            font-size: 1.1em;
            line-height: 1.6;
            opacity: 0;
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 2s;
        }

        a {
            color: #ffffff;
            font-size: 1.1em;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #ffeb3b;
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 1.8em;
            }

            form {
                width: 90%;
                padding: 20px;
            }

            button {
                font-size: 1.1em;
            }

            footer {
                font-size: 0.8em;
            }

            p {
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 1.6em;
            }

            form {
                width: 100%;
                padding: 15px;
            }

            p {
                font-size: 0.9em;
            }

            footer {
                font-size: 0.75em;
            }

            a {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <h2>Forgot Password</h2>

    <form method="POST" action="forgot_password.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <!-- Fake CAPTCHA -->
        <label>
            <input type="checkbox" name="fake_captcha"> I am not a robot
        </label>

        <button type="submit">Confirm Password</button>
    </form>

    <footer>
        <p>&copy; 2024 Royal University. All rights reserved.</p>
    </footer>
</body>
</html>