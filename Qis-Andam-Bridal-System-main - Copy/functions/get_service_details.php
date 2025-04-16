<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = $_GET['id'];
$type = $_GET['type'];

$query = "SELECT * FROM $type WHERE {$type}_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode([
    "name" => $data[$type . '_name'],
    "image" => base64_encode($data['image']),
    "description" => $data['description'],
    "price" => number_format($data['price'], 2)
]);
