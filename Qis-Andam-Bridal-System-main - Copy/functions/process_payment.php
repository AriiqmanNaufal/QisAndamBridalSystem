<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
    $card_name = $_POST['card_name'];
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // Validate inputs (basic example)
    if (empty($card_name) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
        die("Error: All fields are required.");
    }

    // Validate card details
    if (!preg_match('/^\d{16}$/', $card_number)) {
        die("Error: Invalid card number.");
    }
    if (!preg_match('/^\d{3}$/', $cvv)) {
        die("Error: Invalid CVV.");
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
        die("Error: Invalid expiry date format (MM/YY).");
    }

    // Get total price from bookings
    $query = "SELECT total_price FROM bookings WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_price = $row['total_price'] ?? 0;

    if ($total_price <= 0) {
        die("Error: No valid booking found for payment.");
    }

    // Mask card details (store only last 4 digits)
    $masked_card_number = str_repeat('*', 12) . substr($card_number, -4);

    // Insert payment record
    $insert_query = "INSERT INTO payments (user_id, total_price, card_name, card_number, expiry_date, cvv, payment_date) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("idssss", $user_id, $total_price, $card_name, $masked_card_number, $expiry_date, $cvv);

    if ($insert_stmt->execute()) {
        // Redirect to receipt page
        header("Location: ../receipt.php");
        exit();
    } else {
        echo "Error processing payment.";
    }
} else {
    echo "Invalid request.";
}
