<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch total users with role = 'user'
$user_query = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$total_users = $user_data['total_users'] ?? 0;

// Fetch total bookings
$booking_query = "SELECT COUNT(*) AS total_bookings FROM bookings";
$booking_result = mysqli_query($conn, $booking_query);
$booking_data = mysqli_fetch_assoc($booking_result);
$total_bookings = $booking_data['total_bookings'] ?? 0;

// Fetch total packages
$package_query = "SELECT COUNT(DISTINCT package_id) AS total_packages FROM packages";
$package_result = mysqli_query($conn, $package_query);
$package_data = mysqli_fetch_assoc($package_result);
$total_packages = $package_data['total_packages'] ?? 0;

// Fetch payment records
$payment_query = "SELECT * FROM payments ORDER BY payment_date DESC";
$payment_result = mysqli_query($conn, $payment_query);

// Fetch bookings that have made a payment by joining the bookings and payments tables
$booking_table_query = "
    SELECT DISTINCT b.*
    FROM bookings b
    INNER JOIN payments p 
        ON b.user_id = p.user_id AND b.total_price = p.total_price
    ORDER BY b.booking_date DESC
";
$booking_table_result = mysqli_query($conn, $booking_table_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .dashboard-container {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            margin-top: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include 'components/sidebar.php'; ?>
        <div class="container-fluid dashboard-container">
            <h2>Welcome, <?= $_SESSION['fullname'] ?></h2>

            <!-- Dashboard Summary Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white p-3">
                        <h5>Total Users</h5>
                        <h3><?= $total_users ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white p-3">
                        <h5>Total Bookings</h5>
                        <h3><?= $total_bookings ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white p-3">
                        <h5>Total Packages</h5>
                        <h3><?= $total_packages ?></h3>
                    </div>
                </div>
            </div>

            <!-- Payment Table -->
            <div class="table-container mt-4">
                <h4>Payment Records</h4>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Payment ID</th>
                            <th>User ID</th>
                            <th>Total Price (RM)</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($payment_result)) : ?>
                            <tr>
                                <td><?= $row['payment_id']; ?></td>
                                <td><?= $row['user_id']; ?></td>
                                <td>RM <?= number_format($row['total_price'], 2); ?></td>
                                <td><?= $row['payment_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Booking Details Table -->
<div class="table-container mt-4">
    <h4>Booking Details (with Payment)</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Booking ID</th>
                <th>User ID</th>
                <th>Package ID</th>
                <th>Venue ID</th>
                <th>Photographer ID</th>
                <th>Videographer ID</th>
                <th>Florist ID</th>
                <th>Caterer ID</th>
                <th>Cake ID</th>
                <th>Artist ID</th>
                <th>Booking Date</th>
                <th>Total Price (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($booking_table_result)) : ?>
                <tr>
                    <td><?= $row['booking_id']; ?></td>
                    <td><?= $row['user_id']; ?></td>
                    <td><?= $row['package_id']; ?></td>
                    <td><?= $row['venue_id']; ?></td>
                    <td><?= $row['photographer_id']; ?></td>
                    <td><?= $row['videographer_id']; ?></td>
                    <td><?= $row['florist_id']; ?></td>
                    <td><?= $row['caterer_id']; ?></td>
                    <td><?= $row['cake_id']; ?></td>
                    <td><?= $row['artist_id']; ?></td>
                    <td><?= $row['booking_date']; ?></td>
                    <td>RM <?= number_format($row['total_price'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

        </div>
    </div>
</body>

</html>