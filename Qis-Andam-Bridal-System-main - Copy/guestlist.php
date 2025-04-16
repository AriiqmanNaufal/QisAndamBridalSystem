<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view the guest list.");
}

$user_id = $_SESSION['user_id'];

// Fetch user's wedding websites
$websites_stmt = $conn->prepare("SELECT website_id, title FROM wedding_websites WHERE user_id = ?");
$websites_stmt->bind_param("i", $user_id);
$websites_stmt->execute();
$websites_result = $websites_stmt->get_result();
$websites = $websites_result->fetch_all(MYSQLI_ASSOC);

// Determine selected website
if (count($websites) === 1) {
    $website_id = $websites[0]['website_id'];
} elseif (isset($_GET['website_id'])) {
    $website_id = $_GET['website_id'];
} else {
    $website_id = null;
}

// Fetch RSVP guest list
$rsvp_result = [];
$max_capacity = "Not Set";
$total_attendees = 0;

if ($website_id) {
    $rsvp_stmt = $conn->prepare("SELECT id, guest_name, phone_number, family_members FROM rsvp WHERE website_id = ?");
    $rsvp_stmt->bind_param("i", $website_id);
    $rsvp_stmt->execute();
    $rsvp_result = $rsvp_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get max capacity from venue booking
    $capacity_stmt = $conn->prepare("
    SELECT v.capacity 
    FROM wedding_websites w
    JOIN venues v ON w.venue = v.venue_id 
    WHERE w.website_id = ?
");
    $capacity_stmt->bind_param("i", $website_id);
    $capacity_stmt->execute();
    $capacity_stmt->bind_result($max_capacity);
    $capacity_stmt->fetch();
    $capacity_stmt->close();

    // Get total confirmed attendees
    $total_stmt = $conn->prepare("SELECT SUM(family_members) FROM rsvp WHERE website_id = ?");
    $total_stmt->bind_param("i", $website_id);
    $total_stmt->execute();
    $total_stmt->bind_result($total_attendees);
    $total_stmt->fetch();
    $total_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-5">
        <h1>Guest List</h1>

        <?php if (count($websites) > 1): ?>
            <form method="GET" class="mb-3">
                <label for="website_id" class="form-label">Select Wedding Website:</label>
                <select name="website_id" id="website_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose a Website --</option>
                    <?php foreach ($websites as $site): ?>
                        <option value="<?= $site['website_id'] ?>" <?= ($site['website_id'] == $website_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($site['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <?php if ($website_id): ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card p-3 bg-info text-white">
                        <h5>Max Capacity</h5>
                        <h3><?= $max_capacity ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3 bg-success text-white">
                        <h5>Total Confirmed Attendees</h5>
                        <h3><?= $total_attendees ?></h3>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest Name</th>
                        <th>Phone Number</th>
                        <th>Family Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rsvp_result as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['guest_name']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= $row['family_members'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Please select a wedding website to view the guest list.</p>
        <?php endif; ?>
    </div>
</body>

</html>