<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$weddingWebsite = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM wedding_websites WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $weddingWebsite = $result->fetch_assoc();
}
$venueOptions = [];
$stmt = $conn->prepare("SELECT v.venue_id, v.name, v.location FROM bookings b 
                        JOIN venues v ON b.venue_id = v.venue_id 
                        WHERE b.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $venueOptions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QIS ANDAM BRIDAL SYSTEM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <!-- Hero Section -->
    <header class="hero position-relative">
        <!-- Background Video -->
        <video autoplay muted loop playsinline class="position-absolute w-100 h-100 object-fit-cover" id="hero-video">
            <source src="assets/hero-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Dark Overlay -->
        <div class="overlay-hero position-absolute w-100 h-100"></div>

        <!-- Hero Content -->
        <div class="container text-center position-relative">
            <h1 class="display-4 fw-bold text-white">Plan Your Dream Wedding</h1>
            <p class="lead text-white">Stress-free wedding planning starts here. Discover, plan, and book all in one place.</p>
            <a href="package.php" class="btn btn-light btn-lg">Explore Packages</a>
        </div>
    </header>

    <!-- Featured Packages -->
    <section class="container mt-5 text-center">
        <h2 class="fw-bold">Our Wedding Packages</h2>
        <p class="text-muted">Find the perfect package that suits your wedding style.</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="assets/traditional-wedding.jpg" class="card-img-top" alt="Traditional Package">
                    <div class="card-body">
                        <h5 class="card-title">Traditional Wedding</h5>
                        <p class="card-text">A beautiful, classic Malay wedding setup.</p>
                        <a href="package_detail.php?package_id=1" class="btn btn-primary">View Package</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="assets/garden-wedding.jpg" class="card-img-top" alt="Garden Wedding">
                    <div class="card-body">
                        <h5 class="card-title">Garden Wedding</h5>
                        <p class="card-text">A romantic outdoor garden-themed wedding.</p>
                        <a href="package_detail.php?package_id=2" class="btn btn-primary">View Package</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="assets/luxury-wedding.jpg" class="card-img-top" alt="Luxury Wedding">
                    <div class="card-body">
                        <h5 class="card-title">Luxury Wedding</h5>
                        <p class="card-text">A premium wedding package with VIP treatment.</p>
                        <a href="package_detail.php?package_id=3" class="btn btn-primary">View Package</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->

    <section class="planning bg-light py-5 mt-5">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Plan Your Wedding with QIS ANDAM</h2>
            <div class="grid-container">
                <div class="grid-item item1">
                    <div class="overlay">
                        <h3>Everything you need to plan the wedding you want</h3>
                        <p>For all the days along the way</p>
                    </div>
                </div>

                <div class="grid-item" onclick=checkLoginRegistry()>
                    <img src="assets/doorgift.png" alt="Registry">
                    <div class="overlay">
                        <h4>Registry ></h4>
                        <p>The gift you want, the way you want them.</p>
                    </div>
                </div>

                <div class="grid-item" data-bs-toggle="modal" data-bs-target="#weddingModal" onclick="openWeddingWebsiteModal()">
                    <img src="assets/website-wedding.jpg" alt="Wedding Website">
                    <div class="overlay">
                        <h4>Wedding Website ></h4>
                        <p>Your own website to keep everything organized.</p>
                    </div>
                </div>

                <div class="grid-item" onclick="navigateToCategory('venues')">
                    <img src="assets/weddingvenues.jpg" alt="Venues & Vendors">
                    <div class="overlay">
                        <h4>Venues & Vendors ></h4>
                        <p>Find the perfect place & professionals for your day.</p>
                    </div>
                </div>

                <div class="grid-item" onclick=checkLoginGuest()>
                    <img src="assets/guest-list.jpg" alt="Guest List">
                    <div class="overlay">
                        <h4>Guest List ></h4>
                        <p>Manage your guests & RSVPs with ease.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="vendors-section">
        <h2>Explore Wedding Vendors by Category</h2>
        <div class="vendors-grid">
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                <p>Venues</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-camera"></i></div>
                <p>Photographer</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-video"></i></div>
                <p>Videographer</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-seedling"></i></div>
                <p>Florist</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-utensils"></i></div>
                <p>Caterers</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-birthday-cake"></i></div>
                <p>Cakes & Desserts</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-music"></i></div>
                <p>Bands & DJs</p>
            </div>
            <div class="vendor-item">
                <div class="icon"><i class="fas fa-brush"></i></div>
                <p>Makeup Artist</p>
            </div>
        </div>
    </section>
    <div class="modal fade" id="weddingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Your Wedding Website</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="create_website.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Couple Name</label>
                            <input type="text" name="couple_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Date</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <select name="venue" class="form-control" required>
                                <?php if (!empty($venueOptions)): ?>
                                    <?php foreach ($venueOptions as $venue): ?>
                                        <option value="<?= $venue['venue_id'] ?>">
                                            <?= htmlspecialchars($venue['name']) ?>, <?= htmlspecialchars($venue['location']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled selected>Please choose your venue first.</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">RSVP Link</label>
                            <input type="url" name="rsvp_link" class="form-control" placeholder="Optional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Theme</label>
                            <select name="theme" id="theme" class="form-control">
                                <option value="#" selected disabled>Please Select</option>
                                <option value="1">Classic</option>
                                <option value="2">Modern</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Story</label>
                            <textarea name="story" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gallery (Upload Images)</label>
                            <input type="file" name="gallery[]" class="form-control" multiple>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Create Website</button>
                            <a href="my_wedding.php" class="btn btn-secondary">My Website</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
    <script>
        function navigateToCategory(category) {
            window.location.href = 'services.php?category=' + encodeURIComponent(category);
        }

        function checkLogin() {
            var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            if (!isLoggedIn) {
                alert("You need to login first!");
                window.location.href = "auth/login.php"; // Redirect to login page
            } else {
                window.location.href = "services.php?category=venues"; // Proceed if logged in
            }
        }

        function checkLoginRegistry() {
            var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            if (!isLoggedIn) {
                alert("You need to login first!");
                window.location.href = "auth/login.php"; // Redirect to login page
            } else {
                window.location.href = "registry.php"; // Allow access if logged in
            }
        }

        function checkLoginGuest() {
            var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            if (!isLoggedIn) {
                alert("You need to login first!");
                window.location.href = "login.php"; // Redirect to login page
            } else {
                window.location.href = "guestlist.php"; // Allow access if logged in
            }
        }

        function openWeddingWebsiteModal() {
            var modal = new bootstrap.Modal(document.getElementById('weddingModal'));
            modal.show();
        }
    </script>
</body>

</html>