<?php
session_start(); // Start session to get logged-in user ID
include 'config/conn.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch last payment details
$query = "SELECT * FROM payments WHERE user_id = ? ORDER BY payment_date DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();

// Check if payment exists
if (!$payment) {
    echo "<script>alert('No payment record found!'); window.location.href='checkout.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .receipt-container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .btn-print {
            margin-top: 20px;
        }
    </style>
</head>

<body onload="window.print();"> <!-- Auto print when opened -->
    <div class="receipt-container">
        <img src="assets/logo-qis.png" alt="Logo" class="logo"> <!-- Change logo path -->

        <h2 class="text-center">Payment Receipt</h2>
        <hr>
        <p><strong>Transaction ID:</strong> <?php echo $payment['payment_id']; ?></p>
        <p><strong>Amount Paid:</strong> RM <?php echo number_format($payment['total_price'], 2); ?></p>
        <p><strong>Cardholder Name:</strong> <?php echo $payment['card_name']; ?></p>
        <p><strong>Payment Date:</strong> <?php echo $payment['payment_date']; ?></p>
        <hr>
        <button class="btn btn-primary btn-print" onclick="window.print();">Print Receipt</button>
        <button class="btn btn-primary btn-print"><a href="index.php" class="text-white text-decoration-none">Return to Home</a></button>
    </div>

    <script>
        // Auto-open in new tab when redirected
        if (window.opener) {
            window.opener.location.reload();
        }
    </script>
</body>

</html>