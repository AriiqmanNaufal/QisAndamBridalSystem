<?php
include '../config/conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $website_id = $_POST['website_id'];
    $guest_name = $_POST['guest_name'];
    $phone_number = $_POST['phone_number'];
    $family_members = $_POST['family_members'];

    $stmt = $conn->prepare("INSERT INTO rsvp (website_id, guest_name, phone_number, family_members) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $website_id, $guest_name, $phone_number, $family_members);

    if ($stmt->execute()) {
        echo "RSVP submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
