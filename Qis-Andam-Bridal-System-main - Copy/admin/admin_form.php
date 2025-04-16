<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$table = $_GET['table'];
$id = $_GET['id'] ?? null;

$primaryKeyQuery = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
$primaryKeyResult = mysqli_query($conn, $primaryKeyQuery);
$primaryKeyRow = mysqli_fetch_assoc($primaryKeyResult);

if ($primaryKeyRow) {
    $primaryKey = $primaryKeyRow['Column_name'];
} else {
    die("Error: Unable to determine primary key for table $table");
}

$query = $id ? "SELECT * FROM $table WHERE $primaryKey = $id" : null;
$data = $id ? mysqli_fetch_assoc(mysqli_query($conn, $query)) : null;

$tableColumns = [
    "cakes_desserts" => ["name", "price", "description", "image", "rate"],
    "caterers" => ["name", "price", "menu", "contact", "image", "rate"],
    "doorgift_vendors" => ["vendor_name", "price_per_pax", "image", "description"],
    "florists" => ["name", "price", "contact", "description", "image", "rate"],
    "makeup_artists" => ["name", "price", "contact", "description", "image", "rate"],
    "packages" => ["package_name", "description", "price", "image"],
    "photographers" => ["name", "price", "contact", "description", "image", "rate"],
    "venues" => ["name", "location", "capacity", "price", "description", "rate", "image"],
    "videographers" => ["name", "price", "contact", "description", "image", "rate"],
];

$columns = $tableColumns[$table] ?? [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [];
    $values = [];
    $updateFields = [];

    foreach ($_POST as $key => $value) {
        if ($key !== "image") {
            $fields[] = $key;
            $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
            $updateFields[] = "$key = '" . mysqli_real_escape_string($conn, $value) . "'";
        }
    }

    if (!empty($_FILES['image']['name'])) {
        $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
        $imageData = mysqli_real_escape_string($conn, $imageData);

        $fields[] = "image";
        $values[] = "'$imageData'";
        $updateFields[] = "image = '$imageData'";
    }

    if ($id) {
        $sql = "UPDATE $table SET " . implode(',', $updateFields) . " WHERE $primaryKey = $id";
    } else {
        $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES (" . implode(',', $values) . ")";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_manage.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage <?= ucfirst($table) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex">
    <?php include 'components/sidebar.php'; ?>
    <div class="container mt-4" style="margin-left: 250px;">
        <h2><?= $id ? "Edit" : "Add New" ?> <?= ucfirst($table) ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php foreach ($columns as $col): ?>
                <div class="mb-3">
                    <label class="form-label"><?= ucfirst(str_replace('_', ' ', $col)) ?></label>
                    <?php if ($col === "image"): ?>
                        <?php if ($id && !empty($data['image'])): ?>
                            <img src="image_display.php?table=<?= $table ?>&id=<?= $id ?>" class="img-fluid mb-2" width="150">
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control">
                    <?php else: ?>
                        <input type="text" name="<?= $col ?>" class="form-control" value="<?= $data[$col] ?? '' ?>" required>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</body>

</html>