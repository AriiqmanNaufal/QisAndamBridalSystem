<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your wedding websites.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT website_id, title, couple_name, event_date, story, gallery FROM wedding_websites WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle deletion request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM wedding_websites WHERE website_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $delete_id, $user_id);
    if ($delete_stmt->execute()) {
        header("Location: my_wedding.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete the wedding website.');</script>";
    }
}

// Handle edit request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'] ?? '';
    $couple_name = $_POST['couple_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $story = $_POST['story'] ?? '';
    $existing_images = $_POST['existing_gallery'] ?? '';
    $delete_images = $_POST['delete_images'] ?? [];

    // Convert existing images to an array
    $existing_images_array = !empty($existing_images) ? explode(",", $existing_images) : [];

    // Remove selected images
    if (!empty($delete_images)) {
        $existing_images_array = array_diff($existing_images_array, $delete_images);
    }

    // Handle new image uploads
    $uploaded_images = [];
    if (!empty($_FILES["images"]["name"][0])) {
        $target_dir = "uploads/";

        foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
            $image_name = basename($_FILES["images"]["name"][$key]);
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_images[] = $image_name;
            }
        }
    }

    // Merge remaining and new images
    $final_gallery = implode(",", array_merge($existing_images_array, $uploaded_images));

    $update_stmt = $conn->prepare("UPDATE wedding_websites SET title = ?, couple_name = ?, event_date = ?, story = ?, gallery = ? WHERE website_id = ? AND user_id = ?");
    $update_stmt->bind_param("ssssssi", $title, $couple_name, $event_date, $story, $final_gallery, $edit_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: my_wedding.php");
        exit();
    } else {
        echo "<script>alert('Failed to update wedding website.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Wedding Websites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-5">
        <h1>My Wedding Websites</h1>
        <?php if ($result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <h3><?= htmlspecialchars($row['title'] ?? '') ?></h3>
                        <p><strong>Couple:</strong> <?= htmlspecialchars($row['couple_name'] ?? '') ?></p>
                        <p><strong>Event Date:</strong> <?= htmlspecialchars($row['event_date'] ?? '') ?></p>
                        <p><strong>Story:</strong> <?= htmlspecialchars($row['story'] ?? '') ?></p>
                        <div class="mb-2">
                            <?php
                            $images = !empty($row['gallery']) ? explode(",", $row['gallery']) : [];
                            foreach ($images as $image):
                            ?>
                                <img src="uploads/<?= htmlspecialchars(trim($image)) ?>" class="img-thumbnail" width="100" alt="Wedding Image">
                            <?php endforeach; ?>
                        </div>
                        <a href="view_wedding.php?id=<?= $row['website_id'] ?>" class="btn btn-primary">View</a>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['website_id'] ?>">Edit</button>
                        <button class="btn btn-success" onclick="copyShareLink(<?= $row['website_id'] ?>)">Share</button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="delete_id" value="<?= $row['website_id'] ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this wedding website?')">Delete</button>
                        </form>
                        <div class="modal fade" id="editModal<?= $row['website_id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Wedding Website</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="edit_id" value="<?= $row['website_id'] ?>">
                                            <label>Title:</label>
                                            <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" class="form-control" required>
                                            <label>Couple Name:</label>
                                            <input type="text" name="couple_name" value="<?= htmlspecialchars($row['couple_name']) ?>" class="form-control" required>
                                            <label>Event Date:</label>
                                            <input type="date" name="event_date" value="<?= htmlspecialchars($row['event_date']) ?>" class="form-control" required>
                                            <label>Story:</label>
                                            <textarea name="story" class="form-control"><?= htmlspecialchars($row['story']) ?></textarea>
                                            <label>Existing Images:</label>
                                            <div>
                                                <?php foreach ($images as $image): ?>
                                                    <div>
                                                        <img src="uploads/<?= htmlspecialchars($image) ?>" width="100">
                                                        <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($image) ?>"> Delete
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <label>Upload New Images:</label>
                                            <input type="file" name="images[]" multiple class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">You haven't created any wedding websites yet.</p>
        <?php endif; ?>
    </div>
    <script>
        function copyShareLink(websiteId) {
            let shareLink = "http://192.168.43.192/Qis-Andam-Bridal-System/view_wedding.php?id=" + websiteId;
            navigator.clipboard.writeText(shareLink).then(() => {
                alert("Link copied!");
            });
        }
    </script>
</body>

</html>