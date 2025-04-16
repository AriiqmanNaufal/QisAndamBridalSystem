<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if (!isset($_GET['id'])) {
    die("Wedding website not found.");
}

$wedding_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch existing data
$stmt = $conn->prepare("SELECT title, couple_name, event_date FROM wedding_websites WHERE website_id = ? AND user_id = ?");
$stmt->bind_param("ii", $wedding_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wedding = $result->fetch_assoc();

if (!$wedding) {
    die("Wedding website not found.");
}

// Handle updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $couple_name = $_POST['couple_name'];
    $event_date = $_POST['event_date'];

    $update_stmt = $conn->prepare("UPDATE wedding_websites SET title = ?, couple_name = ?, event_date = ? WHERE website_id = ? AND user_id = ?");
    $update_stmt->bind_param("sssii", $title, $couple_name, $event_date, $wedding_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: my_wedding_websites.php");
        exit();
    } else {
        echo "<script>alert('Failed to update wedding website.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Wedding Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Edit Wedding Website</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($wedding['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Couple Name</label>
                <input type="text" class="form-control" name="couple_name" value="<?= htmlspecialchars($wedding['couple_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date</label>
                <input type="date" class="form-control" name="event_date" value="<?= $wedding['event_date'] ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="my_wedding_websites.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>