<?php
session_start();
include '../config/conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all users
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

// Handle form submission (Add Admin)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'Admin'; // Only allows adding Admins

    $sql = "INSERT INTO users (fullname, email, password, role) VALUES ('$fullname', '$email', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_user.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $deleteId");
    header("Location: manage_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex">
    <?php include 'components/sidebar.php' ?>
    <div class="container mt-4" style="margin-left: 250px;">
        <h2>Manage Users</h2>

        <!-- Add Admin Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#adminModal">Add Admin</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <a href="manage_user.php?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage_user.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add Admin Modal -->
        <div class="modal fade" id="adminModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>