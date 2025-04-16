<?php
include '../config/conn.php';

if (isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']);  // Convert to integer for security

    // Identify the primary key dynamically
    $primaryKeys = [
        "cakes_desserts" => "cake_id",
        "caterers" => "caterer_id",
        "doorgift_vendors" => "id",
        "florists" => "florist_id",
        "makeup_artists" => "artist_id",
        "packages" => "package_id",
        "photographers" => "photographer_id",
        "venues" => "venue_id",
        "videographers" => "videographer_id"
    ];

    if (!array_key_exists($table, $primaryKeys)) {
        die("Invalid table.");
    }

    $primaryKey = $primaryKeys[$table];

    $query = "SELECT image FROM $table WHERE $primaryKey = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $image);
    mysqli_stmt_fetch($stmt);

    if ($image) {
        header("Content-Type: image/jpeg");  // Adjust content type if storing PNG
        echo $image;
    } else {
        echo "No image found.";
    }
} else {
    echo "Invalid request.";
}
