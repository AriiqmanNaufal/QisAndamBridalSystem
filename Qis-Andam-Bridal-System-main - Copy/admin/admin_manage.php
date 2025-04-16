<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$tables = [
    'cakes_desserts' => ['cake_id', 'name', 'price', 'description', 'image', 'rate'],
    'caterers' => ['caterer_id', 'name', 'price', 'menu', 'contact', 'image', 'rate'],
    'doorgift_vendors' => ['id', 'vendor_name', 'price_per_pax', 'image', 'description'],
    'florists' => ['florist_id', 'name', 'price', 'contact', 'description', 'image', 'rate'],
    'makeup_artists' => ['artist_id', 'name', 'price', 'contact', 'description', 'image', 'rate'],
    'packages' => ['package_id', 'package_name', 'description', 'price', 'image'],
    'photographers' => ['photographer_id', 'name', 'price', 'contact', 'description', 'image', 'rate'],
    'venues' => ['venue_id', 'name', 'location', 'capacity', 'price', 'description', 'rate', 'image'],
    'videographers' => ['videographer_id', 'name', 'price', 'contact', 'description', 'image', 'rate']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="d-flex">
        <?php include 'components/sidebar.php'; ?>
        <div class="container-fluid p-4" style="margin-left: 250px;">
            <h2>Admin Management</h2>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs">
                <?php
                $tables = [
                    "cakes_desserts" => ["cake_id", "name", "price", "description", "image", "rate"],
                    "caterers" => ["caterer_id", "name", "price", "menu", "contact", "image", "rate"],
                    "doorgift_vendors" => ["id", "vendor_name", "price_per_pax", "image", "description"],
                    "florists" => ["florist_id", "name", "price", "contact", "description", "image", "rate"],
                    "makeup_artists" => ["artist_id", "name", "price", "contact", "description", "image", "rate"],
                    "packages" => ["package_id", "package_name", "description", "price", "image"],
                    "photographers" => ["photographer_id", "name", "price", "contact", "description", "image", "rate"],
                    "venues" => ["venue_id", "name", "location", "capacity", "price", "description", "rate", "image"],
                    "videographers" => ["videographer_id", "name", "price", "contact", "description", "image", "rate"]
                ];

                $firstTable = array_key_first($tables); // Set first table as default active tab

                foreach ($tables as $table => $columns): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $table == $firstTable ? 'active' : '' ?>" data-bs-toggle="tab" href="#<?= $table ?>">
                            <?= ucfirst(str_replace('_', ' ', $table)) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content mt-3">
                <?php foreach ($tables as $table => $columns): ?>
                    <div id="<?= $table ?>" class="tab-pane fade <?= $table == $firstTable ? 'show active' : '' ?>">
                        <h4><?= ucfirst(str_replace('_', ' ', $table)) ?></h4>
                        <button class="btn btn-success" onclick="openForm('<?= $table ?>')">Add New</button>

                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $col) echo "<th>" . ucfirst(str_replace('_', ' ', $col)) . "</th>"; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include '../config/conn.php';
                                $query = "SELECT * FROM $table";
                                $result = mysqli_query($conn, $query);

                                if (!$result) {
                                    echo "<tr><td colspan='100%'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
                                } elseif (mysqli_num_rows($result) == 0) {
                                    echo "<tr><td colspan='100%'>No records found.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <?php foreach ($columns as $col): ?>
                                                <td>
                                                    <?php if ($col === "image"): ?>
                                                        <img src="display_image.php?table=<?= $table ?>&id=<?= $row[$columns[0]] ?>" alt="Image" width="80">
                                                    <?php else: ?>
                                                        <?= $row[$col] ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="editEntry('<?= $table ?>', <?= $row[$columns[0]] ?>)">Edit</button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteEntry('<?= $table ?>', <?= $row[$columns[0]] ?>)">Delete</button>
                                            </td>
                                        </tr>
                                <?php endwhile;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function openForm(table) {
            window.location.href = `admin_form.php?table=${table}`;
        }

        function editEntry(table, id) {
            window.location.href = `admin_form.php?table=${table}&id=${id}`;
        }

        function deleteEntry(table, id) {
            if (confirm("Are you sure you want to delete this?")) {
                window.location.href = `admin_delete.php?table=${table}&id=${id}`;
            }
        }
    </script>
</body>

</html>