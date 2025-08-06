<?php
session_start();
require 'auth.php';
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$student_id = $_SESSION['user_id'];

$query = "SELECT c.course_id, c.course_name, c.course_description, 
          10000 AS course_fee, 
          IF(p.payment_id IS NOT NULL, 1, 0) AS payment_made
          FROM courses c
          JOIN student_enrollments e ON c.course_id = e.course_id
          LEFT JOIN payments p ON c.course_id = p.course_id AND p.student_id = ?
          WHERE e.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fees</title>
    <link rel="stylesheet" href="pay fee.css">
    <link rel="icon" href="favicon.png" type="image/png">
    
</head>
<body>
    <h1>Pay Fees</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Description</th>
                    <th>Course Fee (PKR)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_description']) ?></td>
                        <td><?= number_format($row['course_fee']) ?></td>
                        <td>
                            <?php if ($row['payment_made']): ?>
                                <button class="pay-btn disabled" disabled>Paid</button>
                            <?php else: ?>
                                <button class="pay-btn" data-course-id="<?= $row['course_id'] ?>">Pay Now</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not enrolled in any courses.</p>
    <?php endif; ?>

    <!-- Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <h2>Select Payment Method</h2>
            <form id="paymentForm" action="process_payment.php" method="post">
                <input type="hidden" name="student_id" value="<?= $student_id ?>">
                <input type="hidden" id="course_id" name="course_id" value="">
                <label><input type="radio" name="payment_method" value="Debit Card" required> Debit Card</label><br>
                <label><input type="radio" name="payment_method" value="Credit Card"> Credit Card</label><br>
                <label><input type="radio" name="payment_method" value="Bank Transfer"> Bank Transfer</label><br><br>
                <button type="submit">Confirm Payment</button>
            </form>
        </div>
    </div>

    <script>
        const payButtons = document.querySelectorAll('.pay-btn:not(.disabled)');
        const modal = document.getElementById('paymentModal');
        const courseIdInput = document.getElementById('course_id');

        payButtons.forEach(button => {
            button.addEventListener('click', () => {
                courseIdInput.value = button.getAttribute('data-course-id');
                modal.style.display = 'block';
            });
        });

        window.onclick = (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>
    <a href="student_dashboard.php">Back to Dashboard</a>
</body>
</html>
