<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include 'config/conn.php'; // Include database connection

// Fetch packages from the database
$query = "SELECT * FROM packages"; // Ensure table name matches your SQL
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Our Packages</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <?php if (!empty($row['image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" class="card-img-top" alt="Package Image">
                        <?php else: ?>
                            <img src="assets/images/default-package.jpg" class="card-img-top" alt="Default Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['package_name']); ?></h5>
                            <p class="card-text">Price: RM<?php echo htmlspecialchars($row['price']); ?></p>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <a href="package_detail.php?package_id=<?php echo $row['package_id']; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>