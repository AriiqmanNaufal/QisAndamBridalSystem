<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $package_id = $_POST['package_id'];
    $booking_date = $_POST['booking_date'];

    // Check if the user has already booked this package
    $query = "SELECT * FROM bookings WHERE user_id = ? AND package_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $package_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "You have already booked this package. You cannot book again.";
        header("Location: ../package_detail.php?package_id=$package_id");
        exit();
    }

    // Check if the selected date is already booked
    $query = "SELECT * FROM bookings WHERE package_id = ? AND booking_date = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $package_id, $booking_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "This date is already booked. Please choose another date.";
        header("Location: ../package_detail.php?package_id=$package_id");
        exit();
    }

    // Fetch package price from the packages table
    $query = "SELECT price FROM packages WHERE package_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $package_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $package_price = $row['price'];

    // Insert new booking with price
    $query = "INSERT INTO bookings (user_id, package_id, booking_date, total_price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iisd", $user_id, $package_id, $booking_date, $package_price);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Booking successful!";
        header("Location: ../package_detail.php?package_id=$package_id");
    } else {
        $_SESSION['error'] = "Failed to book the package. Please try again.";
        header("Location: ../package_detail.php?package_id=$package_id");
    }
}
