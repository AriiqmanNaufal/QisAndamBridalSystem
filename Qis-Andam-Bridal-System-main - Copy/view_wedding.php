<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    die("Wedding website not found.");
}

$wedding_id = $_GET['id'];
$stmt = $conn->prepare("
    SELECT w.*, v.name AS venue_name, v.location AS venue_location
    FROM wedding_websites w
    LEFT JOIN venues v ON w.venue = v.venue_id
    WHERE w.website_id = ?
");
$stmt->bind_param("i", $wedding_id);
$stmt->execute();
$result = $stmt->get_result();
$wedding = $result->fetch_assoc();

if (!$wedding) {
    die("Wedding website not found.");
}

// Fetch RSVPs if user owns the site
$rsvp_data = [];
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $wedding['user_id']) {
    $rsvp_stmt = $conn->prepare("SELECT guest_name, phone_number, family_members FROM rsvp WHERE website_id = ?");
    $rsvp_stmt->bind_param("i", $wedding_id);
    $rsvp_stmt->execute();
    $rsvp_result = $rsvp_stmt->get_result();
    $rsvp_data = $rsvp_result->fetch_all(MYSQLI_ASSOC);
}

$gallery_images = explode(",", $wedding['gallery']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= htmlspecialchars($wedding['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
        background-color: #fef8f8;
        font-family: 'Poppins', sans-serif;
        color: #5a5a5a;
    }

    .container {
        max-width: 800px;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    h1 {
        font-size: 32px;
        font-weight: bold;
        color: #d56f9a;
    }

    h3 {
        font-size: 24px;
        font-weight: normal;
        color: #a64d79;
    }

    .invite-header {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        color: #ff6b81;
        margin-bottom: 10px;
    }

    .card {
        background: #fff5f7;
        border: none;
        border-radius: 10px;
        padding: 20px;
    }

    button.btn-success {
        background-color: #ff6b81;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        border-radius: 8px;
        transition: 0.3s;
    }

    button.btn-success:hover {
        background-color: #e05267;
    }

    .modal-content {
        background: #fff5f7;
        border-radius: 12px;
    }

    .list-group-item {
        background: #fff5f7;
        border: none;
        border-radius: 6px;
        margin-bottom: 5px;
    }

    img {
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    input,
    select {
        border-radius: 8px !important;
        border: 1px solid #ff6b81 !important;
        padding: 10px !important;
    }
</style>

<body>
    <div class="container mt-5">
        <p class="invite-header">You Are Invited To</p>
        <h1 class="text-center"><?= htmlspecialchars($wedding['title']) ?></h1>
        <h3 class="text-center"><?= htmlspecialchars($wedding['couple_name']) ?></h3>
        <p class="text-center">
            <strong>Date:</strong> <?= $wedding['event_date'] ?> |
            <strong>Venue:</strong>
            <?= $wedding['venue_name'] ? htmlspecialchars($wedding['venue_name']) . ', ' . htmlspecialchars($wedding['venue_location']) : 'Please choose your venue first' ?>
        </p>

        <?php if (!empty($wedding['story'])): ?>
            <div class="card p-3 mt-3">
                <h4>Our Love Story</h4>
                <p><?= nl2br(htmlspecialchars($wedding['story'])) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($gallery_images[0])): ?>
            <div class="row mt-3">
                <?php foreach ($gallery_images as $image): ?>
                    <div class="col-md-4">
                        <img src="uploads/<?= htmlspecialchars($image) ?>" class="img-fluid rounded">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- RSVP Button -->
        <div class="text-center mt-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rsvpModal">RSVP Now</button>
        </div>

        <!-- RSVP Modal -->
        <div class="modal fade" id="rsvpModal" tabindex="-1" aria-labelledby="rsvpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rsvpModalLabel">RSVP for <?= htmlspecialchars($wedding['title']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="rsvpForm">
                            <input type="hidden" name="website_id" value="<?= $wedding_id ?>">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="guest_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Family Members Attending</label>
                                <input type="number" class="form-control" name="family_members" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-success">Submit RSVP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($rsvp_data)): ?>
            <div class="mt-5">
                <h3>Guest List</h3>
                <ul class="list-group">
                    <?php foreach ($rsvp_data as $rsvp): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($rsvp['guest_name']) ?> - <?= htmlspecialchars($rsvp['phone_number']) ?> (<?= $rsvp['family_members'] ?> attendees)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            $("#rsvpForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "functions/save_rsvp.php",
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>