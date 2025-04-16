<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['budget'])) {
    die("Error: Missing user ID or budget.");
}

$user_id = $_SESSION['user_id'];
$budget = $_POST['budget'];

// Check if user_id exists in bookings
$checkQuery = "SELECT booking_id FROM bookings WHERE user_id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("i", $user_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    die("Error: User ID not found in bookings.");
}

// Update budget
$sql = "UPDATE bookings SET budget = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("di", $budget, $user_id);
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

echo "Budget updated successfully!";
header("Location: ../dashboard.php");
exit();
