<?php
require '../config/db.php';
require '../includes/auth_student.php';
require '../includes/log_activity.php';

$student_id = $_SESSION['student_id'];

$id = $_POST['lost_item_id'] ?? '';
$item_name = trim($_POST['item_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$color = trim($_POST['color'] ?? '');
$date_lost = $_POST['date_lost'] ?? '';
$location_lost = trim($_POST['location_lost'] ?? '');

$stmt = $pdo->prepare("
    SELECT * FROM lost_items
    WHERE lost_item_id = ?
      AND student_id = ?
      AND status = 'Unverified'
    LIMIT 1
");
$stmt->execute([$id, $student_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found or cannot be updated.");
}

if (!$item_name || !$description || !$category || !$date_lost || !$location_lost) {
    header("Location: edit_item.php?id=" . urlencode($id) . "&error=" . urlencode("Please fill in all required fields."));
    exit;
}

/* Duplicate check excluding current item */
$dupStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM lost_items
    WHERE student_id = ?
      AND item_name = ?
      AND category = ?
      AND date_lost = ?
      AND location_lost = ?
      AND lost_item_id != ?
");
$dupStmt->execute([$student_id, $item_name, $category, $date_lost, $location_lost, $id]);

if ($dupStmt->fetchColumn() > 0) {
    header("Location: edit_item.php?id=" . urlencode($id) . "&error=" . urlencode("Another similar item already exists."));
    exit;
}

$image_path = $item['image_path'];

/* Optional image replacement */
if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['item_image']['error'] !== 0) {
        header("Location: edit_item.php?id=" . urlencode($id) . "&error=" . urlencode("Image upload failed."));
        exit;
    }

    $uploadDir = "../uploads/lost_items/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES['item_image']['tmp_name'];
    $originalName = basename($_FILES['item_image']['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        header("Location: edit_item.php?id=" . urlencode($id) . "&error=" . urlencode("Invalid image type."));
        exit;
    }

    $newFileName = time() . "_" . uniqid() . "." . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        header("Location: edit_item.php?id=" . urlencode($id) . "&error=" . urlencode("Image upload failed."));
        exit;
    }

    $image_path = "uploads/lost_items/" . $newFileName;
}

$updateStmt = $pdo->prepare("
    UPDATE lost_items
    SET item_name = ?, description = ?, category = ?, color = ?, image_path = ?, date_lost = ?, location_lost = ?
    WHERE lost_item_id = ? AND student_id = ?
");
$updateStmt->execute([
    $item_name,
    $description,
    $category,
    $color,
    $image_path,
    $date_lost,
    $location_lost,
    $id,
    $student_id
]);

logActivity($pdo, 'Student', $student_id, 'Updated Lost Item', 'Lost item ID: ' . $id);

header("Location: edit_item.php?id=" . urlencode($id) . "&message=" . urlencode("Item updated successfully."));
exit;
?>