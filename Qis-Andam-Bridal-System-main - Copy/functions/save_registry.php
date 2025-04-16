<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}
$user_id = $_SESSION['user_id']; // Corrected session variable
$vendor_id = $_POST['vendor_id'];
$capacity = $_POST['capacity'];

// Get selected vendor price
$stmt = $conn->prepare("SELECT price_per_pax FROM doorgift_vendors WHERE id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();
$price_per_pax = $vendor['price_per_pax'];

// Calculate total price
$total_price = $price_per_pax * $capacity;

// Insert into user_registry
$stmt = $conn->prepare("INSERT INTO user_registry (user_id, vendor_id, venue_capacity, total_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiid", $user_id, $vendor_id, $capacity, $total_price);
$stmt->execute();

header("Location: ../registry.php?success=1");
