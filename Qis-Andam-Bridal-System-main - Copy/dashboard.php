<?php
include 'config/conn.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's latest booking
$query = "SELECT b.booking_id, p.package_name, v.venue_id, v.name AS venue, 
                 ph.photographer_id, ph.name AS photographer, vi.videographer_id, vi.name AS videographer, 
                 f.florist_id, f.name AS florist, c.caterer_id, c.name AS caterer, 
                 ck.cake_id, ck.name AS cake, m.artist_id, m.name AS makeup, b.total_price
          FROM bookings b
          LEFT JOIN packages p ON b.package_id = p.package_id
          LEFT JOIN venues v ON b.venue_id = v.venue_id
          LEFT JOIN photographers ph ON b.photographer_id = ph.photographer_id
          LEFT JOIN videographers vi ON b.videographer_id = vi.videographer_id
          LEFT JOIN florists f ON b.florist_id = f.florist_id
          LEFT JOIN caterers c ON b.caterer_id = c.caterer_id
          LEFT JOIN cakes_desserts ck ON b.cake_id = ck.cake_id
          LEFT JOIN makeup_artists m ON b.artist_id = m.artist_id
          WHERE b.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch dropdown options
function fetchOptions($conn, $table, $idField, $nameField)
{
    $query = "SELECT $idField, $nameField FROM $table";
    return $conn->query($query);
}

$venues = fetchOptions($conn, 'venues', 'venue_id', 'name');
$photographers = fetchOptions($conn, 'photographers', 'photographer_id', 'name');
$videographers = fetchOptions($conn, 'videographers', 'videographer_id', 'name');
$florists = fetchOptions($conn, 'florists', 'florist_id', 'name');
$caterers = fetchOptions($conn, 'caterers', 'caterer_id', 'name');
$cakes = fetchOptions($conn, 'cakes_desserts', 'cake_id', 'name');
$makeupArtists = fetchOptions($conn, 'makeup_artists', 'artist_id', 'name');

// Fetch the user's budget
$budget_query = "SELECT budget FROM bookings WHERE user_id = ?";
$stmt_budget = $conn->prepare($budget_query);
$stmt_budget->bind_param("i", $user_id);
$stmt_budget->execute();
$result_budget = $stmt_budget->get_result();
$budget_row = $result_budget->fetch_assoc();
$budget = $budget_row['budget'] ?? null;

// Fetch total spending from bookings
$spending_query = "SELECT SUM(total_price) AS total_spent FROM bookings WHERE user_id = ?";
$stmt_spending = $conn->prepare($spending_query);
$stmt_spending->bind_param("i", $user_id);
$stmt_spending->execute();
$result_spending = $stmt_spending->get_result();
$spending_row = $result_spending->fetch_assoc();
$total_spent = $spending_row['total_spent'] ?? 0;

// Calculate spending percentage
$percentage = ($budget && $budget > 0) ? ($total_spent / $budget) * 100 : 0;
$progress_color = ($total_spent > $budget) ? "bg-danger" : "bg-success";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row d-flex justify-content-center">
            <!-- Checklist -->
            <div class="col-md-2">
                <div class="card shadow-lg text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#checklistModal">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x text-primary"></i>
                        <h5 class="mt-2">Checklist</h5>
                    </div>
                </div>
            </div>

            <!-- Registry (Doorgift) -->
            <div class="col-md-2">
                <a href="registry.php" class="card shadow-lg text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-gift fa-3x text-success"></i>
                        <h5 class="mt-2">Registry</h5>
                    </div>
                </a>
            </div>

            <!-- Wedding Website -->
            <div class="col-md-2">
                <a href="my_wedding.php" class="card shadow-lg text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-globe fa-3x text-danger"></i>
                        <h5 class="mt-2">Wedding Website</h5>
                    </div>
                </a>
            </div>

            <!-- Invitation -->
            <div class="col-md-2">
                <a href="guestlist.php" class="card shadow-lg text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope-open-text fa-3x text-warning"></i>
                        <h5 class="mt-2">Invitation</h5>
                    </div>
                </a>
            </div>

            <!-- Checkout -->
            <div class="col-md-2">
                <a href="checkout.php" class="card shadow-lg text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-3x text-success"></i>
                        <h5 class="mt-2">Checkout</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container container-dashboard mt-4">
        <h2 class="text-center p-3">Budget Overview</h2>
        <?php if ($budget): ?>
            <div class="progress mb-2" style="height: 25px;">
                <div class="progress-bar <?php echo $progress_color; ?>" role="progressbar"
                    style="width: <?php echo min($percentage, 100); ?>%;" aria-valuenow="<?php echo $percentage; ?>"
                    aria-valuemin="0" aria-valuemax="100">
                    <?php echo number_format($percentage, 2); ?>%
                </div>
            </div>
            <?php if ($total_spent > $budget): ?>
                <p class="text-danger">⚠️ You have exceeded your budget!</p>
            <?php endif; ?>
            <p><strong>Total Spent:</strong> RM <?php echo number_format($total_spent, 2); ?></p>
            <p><strong>Budget:</strong> RM <?php echo number_format($budget, 2); ?></p>
            <button class="btn btn-outline-success budget-btn" data-bs-toggle="modal" data-bs-target="#editBudgetModal">Edit Budget</button>
        <?php else: ?>
            <button class="btn btn-outline-success budget-btn" data-bs-toggle="modal" data-bs-target="#setBudgetModal">Add Your Budget</button>
        <?php endif; ?>
    </div>

    <!-- Modal for Adding Budget -->
    <div class="modal fade" id="setBudgetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Your Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="functions/set_budget.php">
                        <label class="form-label">Enter Budget Amount (RM):</label>
                        <input type="number" name="budget" class="form-control" required>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Save Budget</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Budget -->
    <div class="modal fade" id="editBudgetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Your Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="functions/set_budget.php">
                        <label class="form-label">Update Budget Amount (RM):</label>
                        <input type="number" name="budget" class="form-control" value="<?php echo $budget; ?>" required>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Update Budget</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container container-dashboard mt-5">
        <h2 class="text-center">Booking Dashboard</h2>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Package</th>
                    <th>Venue</th>
                    <th>Photographer</th>
                    <th>Videographer</th>
                    <th>Florist</th>
                    <th>Caterer</th>
                    <th>Cake</th>
                    <th>Makeup</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['package_name']; ?></td>
                        <td><?php echo $row['venue'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['photographer'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['videographer'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['florist'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['caterer'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['cake'] ?: 'Not Selected'; ?></td>
                        <td><?php echo $row['makeup'] ?: 'Not Selected'; ?></td>
                        <td>RM <?php echo number_format($row['total_price'], 2); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editBooking<?php echo $row['booking_id']; ?>">Edit</button>
                        </td>
                    </tr>

                    <!-- Modal for Editing Booking -->
                    <div class="modal fade" id="editBooking<?php echo $row['booking_id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Booking</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="functions/update_booking.php">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">

                                        <label class="form-label">Venue:</label>
                                        <select name="venue_id" class="form-select">
                                            <option value="">Select Venue</option>
                                            <?php foreach ($venues as $venue) { ?>
                                                <option value="<?php echo $venue['venue_id']; ?>" <?php echo ($venue['venue_id'] == $row['venue_id']) ? "selected" : ""; ?>>
                                                    <?php echo $venue['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>

                                        <label class="form-label">Photographer:</label>
                                        <select name="photographer_id" class="form-select">
                                            <option value="">Select Photographer</option>
                                            <?php foreach ($photographers as $photographer) { ?>
                                                <option value="<?php echo $photographer['photographer_id']; ?>" <?php echo ($photographer['photographer_id'] == $row['photographer_id']) ? "selected" : ""; ?>>
                                                    <?php echo $photographer['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>

                                        <label class="form-label">Videographer:</label>
                                        <select name="videographer_id" class="form-select">
                                            <option value="">Select Videographer</option>
                                            <?php foreach ($videographers as $videographer) { ?>
                                                <option value="<?php echo $videographer['videographer_id']; ?>" <?php echo ($videographer['videographer_id'] == $row['videographer_id']) ? "selected" : ""; ?>>
                                                    <?php echo $videographer['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>

                                        <label class="form-label">Florist:</label>
                                        <select name="florist_id" class="form-select">
                                            <option value="">Select Florist</option>
                                            <?php foreach ($florists as $florist) { ?>
                                                <option value="<?php echo $florist['florist_id']; ?>" <?php echo ($florist['florist_id'] == $row['florist_id']) ? "selected" : ""; ?>>
                                                    <?php echo $florist['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label class="form-label">Caterer:</label>
                                        <select name="caterer_id" class="form-select">
                                            <option value="">Select Caterer</option>
                                            <?php foreach ($caterers as $caterer) { ?>
                                                <option value="<?php echo $caterer['caterer_id']; ?>" <?php echo ($caterer['caterer_id'] == $row['caterer_id']) ? "selected" : ""; ?>>
                                                    <?php echo $caterer['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label class="form-label">Cake:</label>
                                        <select name="cake_id" class="form-select">
                                            <option value="">Select Cake</option>
                                            <?php foreach ($cakes as $cake) { ?>
                                                <option value="<?php echo $cake['cake_id']; ?>" <?php echo ($cake['cake_id'] == $row['cake_id']) ? "selected" : ""; ?>>
                                                    <?php echo $cake['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label class="form-label">Make up:</label>
                                        <select name="artist_id" class="form-select">
                                            <option value="">Select Make up</option>
                                            <?php foreach ($makeups as $makeup) { ?>
                                                <option value="<?php echo $makeup['artist_id']; ?>" <?php echo ($makeup['artist_id'] == $row['artist_id']) ? "selected" : ""; ?>>
                                                    <?php echo $makeup['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>

                                        <button type="submit" class="btn btn-primary w-100 mt-3">Update Booking</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-4">
        <h4>Available Services</h4>

        <?php
        $categories = [
            "Venues" => "venues",
            "Photographers" => "photographers",
            "Videographers" => "videographers",
            "Caterers" => "caterers"
        ];

        // Define primary keys for each table
        $primaryKeys = [
            "venues" => "venue_id",
            "photographers" => "photographer_id",
            "videographers" => "videographer_id",
            "caterers" => "caterer_id"
        ];

        foreach ($categories as $title => $table) {
            // Ensure primary key exists for this category
            if (!isset($primaryKeys[$table])) {
                continue;
            }

            $primaryKey = $primaryKeys[$table];

            // Fetch first 4 items from the table
            $query = "SELECT * FROM $table LIMIT 4";
            $result = $conn->query($query);
        ?>

            <h5 class="mt-3"><?php echo $title; ?></h5>
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
                                    $rate = (int)$row['rate'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rate ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                                    }
                                    ?>
                                </div>

                                <!-- Debugging Output -->
                                <?php
                                echo "<!-- Debug: Category = $table, ID = " . $row[$primaryKey] . " -->";
                                ?>

                                <!-- Clickable Card -->
                                <a href="service_detail.php?category=<?php echo $table; ?>&id=<?php echo $row[$primaryKey]; ?>" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- See More Button -->
            <a href="services.php?category=<?php echo $table; ?>" class="btn btn-link">See More...</a>

        <?php } ?>
    </div>

    <!-- Service Detail Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="serviceImage" class="img-fluid" alt="Service Image">
                    <p id="serviceDescription"></p>
                    <h6>Price: RM <span id="servicePrice"></span></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success">Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Modal -->
    <!-- Checklist Modal -->
    <div class="modal fade" id="checklistModal" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Wedding Checklist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checklistForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Registry" id="registry">
                                    <label class="form-check-label" for="registry">Registry (Doorgift)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Florist" id="florist">
                                    <label class="form-check-label" for="florist">Florist</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Venue" id="venue">
                                    <label class="form-check-label" for="venue">Venue</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Website" id="website">
                                    <label class="form-check-label" for="website">Wedding Website</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Photographer" id="photographer">
                                    <label class="form-check-label" for="photographer">Photographer</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Videographer" id="videographer">
                                    <label class="form-check-label" for="videographer">Videographer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Caterer" id="caterer">
                                    <label class="form-check-label" for="caterer">Caterer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Makeup Artist" id="makeup_artist">
                                    <label class="form-check-label" for="makeup_artist">Makeup Artist</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Cakes & Desserts" id="cakes_desserts">
                                    <label class="form-check-label" for="cakes_desserts">Cakes & Desserts</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checklist[]" value="Guest List" id="guest_list">
                                    <label class="form-check-label" for="guest_list">Guest List</label>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="saveChecklist()">
                            <i class="fas fa-save"></i> Save Checklist
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                let serviceId = this.dataset.id;
                let serviceType = this.dataset.type;

                fetch(`functions/get_service_details.php?id=${serviceId}&type=${serviceType}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('serviceTitle').textContent = data.name;
                        document.getElementById('serviceImage').src = "data:image/jpeg;base64," + data.image;
                        document.getElementById('serviceDescription').textContent = data.description;
                        document.getElementById('servicePrice').textContent = data.price;
                        new bootstrap.Modal(document.getElementById('serviceModal')).show();
                    });
            });
        });

        function saveChecklist() {
            let formData = new FormData(document.getElementById("checklistForm"));

            fetch("functions/save_checklist.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Checklist saved successfully!");
                    } else {
                        alert("Failed to save checklist.");
                    }
                })
                .catch(error => console.error("Error:", error));
        }
        document.addEventListener("DOMContentLoaded", function() {
            fetch("functions/load_checklist.php")
                .then(response => response.json())
                .then(selectedItems => {
                    selectedItems.forEach(item => {
                        let checkbox = document.querySelector(`input[value="${item}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                })
                .catch(error => console.error("Error loading checklist:", error));
        });
    </script>

</body>

</html>