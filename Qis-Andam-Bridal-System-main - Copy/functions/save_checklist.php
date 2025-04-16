<?php
session_start();
include '../config/conn.php'; // Your database connection file

$user_id = $_SESSION['user_id']; // Get user ID from session
$selected_items = $_POST['checklist'] ?? []; // Get selected checklist items

// Remove existing checklist for the user before inserting new selections
$stmt = $conn->prepare("DELETE FROM wedding_checklist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Insert new checklist items
$stmt = $conn->prepare("INSERT INTO wedding_checklist (user_id, item_name) VALUES (?, ?)");
foreach ($selected_items as $item) {
    $stmt->bind_param("is", $user_id, $item);
    $stmt->execute();
}

echo json_encode(["status" => "success"]);
