<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$id = $_POST['found_item_id'] ?? '';
$item_name = trim($_POST['item_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$color = trim($_POST['color'] ?? '');
$date_found = $_POST['date_found'] ?? '';
$location_found = trim($_POST['location_found'] ?? '');

$stmt = $pdo->prepare("
    SELECT *
    FROM found_items
    WHERE found_item_id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Found item not found.");
}

if (!$item_name || !$description || !$category || !$date_found || !$location_found) {
    header("Location: edit_found_item.php?id=" . urlencode($id) . "&error=" . urlencode("Please fill in all required fields."));
    exit;
}

/* duplicate check excluding current row */
$dupStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM found_items
    WHERE item_name = ?
      AND category = ?
      AND date_found = ?
      AND location_found = ?
      AND found_item_id != ?
");
$dupStmt->execute([$item_name, $category, $date_found, $location_found, $id]);

if ($dupStmt->fetchColumn() > 0) {
    header("Location: edit_found_item.php?id=" . urlencode($id) . "&error=" . urlencode("Another similar found item already exists."));
    exit;
}

$image_path = $item['image_path'];

if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['item_image']['error'] !== 0) {
        header("Location: edit_found_item.php?id=" . urlencode($id) . "&error=" . urlencode("Image upload failed."));
        exit;
    }

    $uploadDir = "../uploads/found_items/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES['item_image']['tmp_name'];
    $originalName = basename($_FILES['item_image']['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        header("Location: edit_found_item.php?id=" . urlencode($id) . "&error=" . urlencode("Invalid image type."));
        exit;
    }

    $newFileName = time() . "_" . uniqid() . "." . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        header("Location: edit_found_item.php?id=" . urlencode($id) . "&error=" . urlencode("Image upload failed."));
        exit;
    }

    $image_path = "uploads/found_items/" . $newFileName;
}

$updateStmt = $pdo->prepare("
    UPDATE found_items
    SET item_name = ?, description = ?, category = ?, color = ?, image_path = ?, date_found = ?, location_found = ?
    WHERE found_item_id = ?
");
$updateStmt->execute([
    $item_name,
    $description,
    $category,
    $color,
    $image_path,
    $date_found,
    $location_found,
    $id
]);

logActivity($pdo, 'Admin', $_SESSION['admin_id'], 'Updated Found Item', 'Found item ID: ' . $id);

header("Location: edit_found_item.php?id=" . urlencode($id) . "&message=" . urlencode("Found item updated successfully."));
exit;
?>