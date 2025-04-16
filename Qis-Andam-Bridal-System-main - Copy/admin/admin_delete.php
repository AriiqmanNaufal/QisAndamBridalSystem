<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$table = $_GET['table'];
$id = $_GET['id'];

// Fetch the primary key column dynamically
$query = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $primaryKey = $row['Column_name']; // Get the actual primary key column
    $sql = "DELETE FROM $table WHERE $primaryKey = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: admin_manage.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Error: Unable to determine primary key for table $table";
}
