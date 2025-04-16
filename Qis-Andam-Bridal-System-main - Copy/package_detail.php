<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if package_id is provided
if (!isset($_GET['package_id'])) {
    header("Location: index.php");
    exit();
}

$package_id = $_GET['package_id'];

// Fetch package details
$query = "SELECT * FROM packages WHERE package_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $package_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$package = mysqli_fetch_assoc($result);

if (!$package) {
    echo "Package not found!";
    exit();
}

// Fetch booked dates for the package
$booked_dates = [];
$query_dates = "SELECT booking_date FROM bookings WHERE package_id = ?";
$stmt_dates = mysqli_prepare($conn, $query_dates);
mysqli_stmt_bind_param($stmt_dates, "i", $package_id);
mysqli_stmt_execute($stmt_dates);
$result_dates = mysqli_stmt_get_result($stmt_dates);

while ($row = mysqli_fetch_assoc($result_dates)) {
    $booked_dates[] = $row['booking_date'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container mt-5">
        <!-- Display Success or Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($package['image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($package['image']); ?>" class="img-fluid" alt="Package Image">
                <?php else: ?>
                    <img src="assets/images/default-package.jpg" class="img-fluid" alt="Default Image">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($package['package_name']); ?></h2>
                <p><strong>Price:</strong> RM<?php echo htmlspecialchars($package['price']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($package['description'])); ?></p>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="functions/book_package.php" method="POST">
                        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                        <label for="booking_date">Select Booking Date:</label>
                        <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                        <button type="submit" class="btn btn-primary mt-3">Book Now</button>
                    </form>
                <?php else: ?>
                    <p class="text-danger">Please <a href="auth/login.php">login</a> to book this package.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function disableBookedDates() {
            let bookedDates = <?php echo json_encode($booked_dates); ?>;
            let dateInput = document.getElementById("booking_date");
            let today = new Date().toISOString().split('T')[0];

            dateInput.min = today;

            dateInput.addEventListener("input", function() {
                if (bookedDates.includes(this.value)) {
                    alert("This date is already booked. Please choose another date.");
                    this.value = "";
                }
            });
        }

        window.onload = disableBookedDates;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>