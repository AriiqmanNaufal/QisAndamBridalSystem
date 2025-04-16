<?php
// Include database connection
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get total price from bookings table
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in."); // Prevent unauthorized access
}
$user_id = $_SESSION['user_id'];

$query = "SELECT total_price FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_price = $row['total_price'] ?? 0;

// Check if the user has already made a payment
$payment_query = "SELECT * FROM payments WHERE user_id = ?";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->bind_param("i", $user_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$has_paid = $payment_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .checkout-container {
            max-width: 500px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .total-amount {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .disabled-btn {
            background-color: #ccc !important;
            cursor: not-allowed;
        }

        .payment-message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="checkout-container">
        <h2 class="text-center">Checkout</h2>
        <p class="total-amount">Total Payment: RM <?php echo number_format($total_price, 2); ?></p>

        <?php if ($has_paid): ?>
            <p class="payment-message">✅ You have already made a payment.</p>
            <button class="btn btn-secondary disabled-btn" disabled>Payment Completed</button>
        <?php else: ?>
            <form action="functions/process_payment.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Cardholder Name</label>
                    <input type="text" class="form-control" name="card_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Card Number</label>
                    <input type="text" class="form-control" name="card_number" required pattern="\d{16}" maxlength="16">
                </div>

                <div class="mb-3 row">
                    <div class="col">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" class="form-control" name="expiry_date" placeholder="MM/YY" required>
                    </div>
                    <div class="col">
                        <label class="form-label">CVV</label>
                        <input type="password" class="form-control" name="cvv" required pattern="\d{3}" maxlength="3">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Payment</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>