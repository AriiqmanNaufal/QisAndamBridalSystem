<?php
include '../config/conn.php';

$table = $_GET['table'];
$id = $_GET['id'];

$primaryKeyQuery = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
$primaryKeyResult = mysqli_query($conn, $primaryKeyQuery);
$primaryKeyRow = mysqli_fetch_assoc($primaryKeyResult);
$primaryKey = $primaryKeyRow['Column_name'] ?? die("Error: Primary key not found");

$query = "SELECT image FROM $table WHERE $primaryKey = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($data && !empty($data['image'])) {
    header("Content-Type: image/jpeg");
    echo $data['image'];
} else {
    echo "Image not found.";
}
