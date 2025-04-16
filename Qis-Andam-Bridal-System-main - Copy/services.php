<?php
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!$category) {
    echo "<p>Invalid category</p>";
    exit;
}

// Define primary keys for each table
$primaryKeys = [
    "cakes_desserts" => "cake_id",
    "caterers" => "caterer_id",
    "florists" => "florist_id",
    "makeup_artists" => "artist_id",
    "photographers" => "photographer_id",
    "venues" => "venue_id",
    "videographers" => "videographer_id"
];

// Ensure the category exists
if (!array_key_exists($category, $primaryKeys)) {
    echo "<p>Invalid category</p>";
    exit;
}

$primaryKey = $primaryKeys[$category]; // Get the correct primary key

// Fetch all services in this category
$query = "SELECT * FROM $category";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($category); ?> Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-4">
        <h4 class="mb-4 text-center">All <?php echo ucfirst($category); ?> Services</h4>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card service-card h-100 shadow-sm">
                        <?php
                        // Convert BLOB to Image
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" class="card-img-top" alt="Service Image">';
                        ?>
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h6>
                            <p class="card-text text-primary fw-bold">RM <?php echo number_format($row['price'], 2); ?></p>

                            <!-- Star Rating -->
                            <div class="rating">
                                <?php
                                $rate = (int)$row['rate']; // Convert rate to integer
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rate ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                                }
                                ?>
                            </div>

                            <!-- Corrected Link with Dynamic Primary Key -->
                            <a href="service_detail.php?category=<?php echo $category; ?>&id=<?php echo $row[$primaryKey]; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-4">Back</a>
    </div>
</body>

</html>