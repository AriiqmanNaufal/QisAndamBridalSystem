<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_POST['booking_id'])) {
    die("Error: Booking ID not provided.");
}

$booking_id = $_POST['booking_id'];

// Fetch existing booking details
$query = "SELECT venue_id, photographer_id, videographer_id, florist_id, caterer_id, cake_id, artist_id, total_price FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$old_booking = $result->fetch_assoc();

if (!$old_booking) {
    die("Error: Booking not found.");
}

// Get new selections (keep old values if not changed)
$venue_id = $_POST['venue_id'] ?? $old_booking['venue_id'];
$photographer_id = $_POST['photographer_id'] ?? $old_booking['photographer_id'];
$videographer_id = $_POST['videographer_id'] ?? $old_booking['videographer_id'];
$florist_id = $_POST['florist_id'] ?? $old_booking['florist_id'];
$caterer_id = $_POST['caterer_id'] ?? $old_booking['caterer_id'];
$cake_id = !empty($_POST['cake_id']) ? $_POST['cake_id'] : NULL; // Allow NULL
$artist_id = $_POST['artist_id'] ?? $old_booking['artist_id'];

// Check if any changes were made
if (
    $venue_id == $old_booking['venue_id'] &&
    $photographer_id == $old_booking['photographer_id'] &&
    $videographer_id == $old_booking['videographer_id'] &&
    $florist_id == $old_booking['florist_id'] &&
    $caterer_id == $old_booking['caterer_id'] &&
    $cake_id == $old_booking['cake_id'] &&
    $artist_id == $old_booking['artist_id']
) {
    header("Location: ../dashboard.php?message=No changes detected.");
    exit();
}

// Calculate new total price
$total_price = 0;

$tables = [
    'venues' => 'venue_id',
    'photographers' => 'photographer_id',
    'videographers' => 'videographer_id',
    'florists' => 'florist_id',
    'caterers' => 'caterer_id',
    'cakes_desserts' => 'cake_id', // Ensure correct table name
    'artists' => 'artist_id'
];

$ids = [
    'venue_id' => $venue_id,
    'photographer_id' => $photographer_id,
    'videographer_id' => $videographer_id,
    'florist_id' => $florist_id,
    'caterer_id' => $caterer_id,
    'cake_id' => $cake_id,
    'artist_id' => $artist_id
];

foreach ($tables as $table => $column) {
    if (!empty($ids[$column])) {  // If user selected an item
        $query = "SELECT price FROM $table WHERE $column = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ids[$column]);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if ($data) {
            $total_price += $data['price'];
        }
    }
}

// Validate that selected cake_id exists in cakes_desserts before updating
if (!empty($cake_id)) {
    $query = "SELECT cake_id FROM cakes_desserts WHERE cake_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cake_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        die("Error: Selected cake does not exist.");
    }
}

// Debugging output (Remove after testing)
var_dump($cake_id);
var_dump($total_price);

// Update booking details with NULL handling
$update_query = "UPDATE bookings SET 
    venue_id = ?, 
    photographer_id = ?, 
    videographer_id = ?, 
    florist_id = ?, 
    caterer_id = ?, 
    cake_id = ?, 
    artist_id = ?, 
    total_price = ? 
    WHERE booking_id = ?";

$stmt = $conn->prepare($update_query);
$stmt->bind_param("iiiiiiidi", $venue_id, $photographer_id, $videographer_id, $florist_id, $caterer_id, $cake_id, $artist_id, $total_price, $booking_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: ../dashboard.php?message=Booking updated successfully.");
} else {
    header("Location: ../dashboard.php?message=No changes were made.");
}
exit();
