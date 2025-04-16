<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to create a wedding website.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $couple_name = $_POST['couple_name'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $rsvp_link = $_POST['rsvp_link'];
    $theme = $_POST['theme'];
    $story = $_POST['story'];

    // Ensure the uploads directory exists
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file uploads
    $gallery = [];
    foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['gallery']['error'][$key] == 0) { // Ensure no error
            $filename = basename($_FILES['gallery']['name'][$key]);
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($tmp_name, $destination)) {
                $gallery[] = $filename;
            } else {
                echo "Error uploading file: " . $filename;
            }
        }
    }

    $galleryString = implode(",", $gallery); // Convert array to string

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO wedding_websites 
        (user_id, title, couple_name, event_date, venue, rsvp_link, theme, story, gallery) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("issssssss", $user_id, $title, $couple_name, $event_date, $venue, $rsvp_link, $theme, $story, $galleryString);

    if ($stmt->execute()) {
        header("Location: my_wedding.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
