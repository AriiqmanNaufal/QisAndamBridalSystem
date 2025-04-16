<?php
session_start();
include 'config/conn.php'; // Database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}
$user_id = $_SESSION['user_id']; // Corrected session variable

// Fetch doorgift vendors
$result = $conn->query("SELECT id, vendor_name, price_per_pax, description, image FROM doorgift_vendors");
$vendors = $result->fetch_all(MYSQLI_ASSOC);

// Fetch user's venue capacity
$venue_result = $conn->query("
    SELECT v.capacity 
    FROM venues v 
    JOIN bookings b ON v.venue_id = b.venue_id 
    WHERE b.user_id = $user_id
    LIMIT 1
");
$venue = $venue_result->fetch_assoc();
$capacity = $venue['capacity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doorgift Registry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to custom CSS -->
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Select Your Doorgift Vendor</h2>
        <form action="functions/save_registry.php" method="POST">
            <input type="hidden" name="capacity" value="<?= $capacity ?>">

            <div class="row g-4">
                <?php foreach ($vendors as $vendor): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow-sm vendor-card h-100">
                            <img src="data:image/jpeg;base64,<?= base64_encode($vendor['image']) ?>" class="card-img-top" alt="Doorgift Image">
                            <div class="card-body d-flex flex-column text-center">
                                <h5 class="card-title"><?= htmlspecialchars($vendor['vendor_name']) ?></h5>
                                <p class="card-text flex-grow-1"><?= htmlspecialchars($vendor['description']) ?></p>
                                <p class="fw-bold">Price per Pax: RM<?= number_format($vendor['price_per_pax'], 2) ?></p>
                                <div class="mt-auto">
                                    <input type="radio" name="vendor_id" value="<?= $vendor['id'] ?>" required> Select
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Save Selection</button>
            </div>
        </form>
    </div>
</body>

</html>