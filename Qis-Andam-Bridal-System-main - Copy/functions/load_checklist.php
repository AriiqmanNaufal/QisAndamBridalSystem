<?php
session_start();
include '../config/conn.php';

$user_id = $_SESSION['user_id'];
$selected_items = [];

$stmt = $conn->prepare("SELECT item_name FROM wedding_checklist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selected_items[] = $row['item_name'];
}

echo json_encode($selected_items);
