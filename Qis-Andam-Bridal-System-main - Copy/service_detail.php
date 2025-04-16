<?php
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get category and ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($id == 0 || empty($category)) {
    echo "<p>Invalid service request</p>";
    exit;
}

// Define table names and their primary key columns
$tables = [
    'cakes_desserts' => 'cake_id',
    'caterers' => 'caterer_id',
    'florists' => 'florist_id',
    'makeup_artists' => 'artist_id',
    'photographers' => 'photographer_id',
    'venues' => 'venue_id',
    'videographers' => 'videographer_id'
];

// Validate the category
if (!array_key_exists($category, $tables)) {
    echo "<p>Invalid service category</p>";
    exit;
}

$primaryKey = $tables[$category];

// Now fetch the correct service using category and id
$query = "SELECT * FROM $category WHERE $primaryKey = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) {
    echo "<p>Service not found</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($service['name']); ?> - Service Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <div class="row">
                <!-- Service Image -->
                <div class="col-md-5">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($service['image']); ?>" class="img-fluid rounded" alt="Service Image">
                </div>

                <!-- Service Details -->
                <div class="col-md-7">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p class="text-primary fw-bold">Price: RM <?php echo number_format($service['price'], 2); ?></p>

                    <!-- Dynamic Fields Based on Category -->
                    <?php if ($category == "venues") : ?>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($service['location']); ?></p>
                        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($service['capacity']); ?> people</p>
                    <?php endif; ?>

                    <?php if (in_array($category, ['caterers', 'florists', 'makeup_artists', 'photographers', 'videographers'])) : ?>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($service['contact']); ?></p>
                    <?php endif; ?>

                    <?php if ($category == "caterers") : ?>
                        <p><strong>Menu:</strong> <?php echo nl2br(htmlspecialchars($service['menu'])); ?></p>
                    <?php endif; ?>

                    <?php if (isset($service['description'])) : ?>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    <?php endif; ?>

                    <!-- Star Rating -->
                    <div class="rating mb-3">
                        <strong>Rating:</strong>
                        <?php
                        $rate = isset($service['rate']) ? (int)$service['rate'] : 0;
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rate ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                        }
                        ?>
                    </div>

                    <a href="services.php?category=<?php echo $category; ?>" class="btn btn-secondary">Back to Services</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>