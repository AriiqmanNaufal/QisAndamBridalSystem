<?php
include 'config/conn.php';

if (!isset($_GET['user'])) {
    die("Invalid wedding website.");
}

$user = $_GET['user'];

// Fetch user wedding website details
$stmt = $conn->prepare("SELECT * FROM wedding_websites w JOIN users u ON w.user_id = u.id WHERE u.username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$wedding = $result->fetch_assoc();

if (!$wedding) {
    die("Wedding website not found.");
}

$gallery = explode(",", $wedding['gallery']); // Convert string to array
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= htmlspecialchars($wedding['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center"><?= htmlspecialchars($wedding['title']) ?></h1>
        <h3 class="text-center"><?= htmlspecialchars($wedding['couple_name']) ?></h3>
        <p class="text-center"><strong>Event Date:</strong> <?= htmlspecialchars($wedding['event_date']) ?></p>
        <p class="text-center"><strong>Venue:</strong> <?= htmlspecialchars($wedding['venue']) ?></p>
        <p class="text-center"><strong>Our Story:</strong> <?= nl2br(htmlspecialchars($wedding['story'])) ?></p>

        <h4 class="text-center mt-4">Gallery</h4>
        <div class="row">
            <?php foreach ($gallery as $img): ?>
                <div class="col-md-4">
                    <img src="uploads/<?= htmlspecialchars($img) ?>" class="img-fluid rounded">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= htmlspecialchars($wedding['rsvp_link']) ?>" class="btn btn-success">RSVP Now</a>
        </div>
    </div>
</body>

</html>