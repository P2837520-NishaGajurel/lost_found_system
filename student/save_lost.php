<?php
require '../config/db.php';
require '../includes/auth_student.php';
require '../includes/log_activity.php';

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: report_lost.php?error=" . urlencode("Invalid request."));
    exit;
}

$item_name = trim($_POST['item_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$color = trim($_POST['color'] ?? '');
$date_lost = $_POST['date_lost'] ?? '';
$location_lost = trim($_POST['location_lost'] ?? '');

if (!$item_name || !$description || !$category || !$date_lost || !$location_lost) {
    header("Location: report_lost.php?error=" . urlencode("Please fill in all required fields."));
    exit;
}

/* Duplicate check */
$duplicateStmt = $pdo->prepare("
    SELECT lost_item_id
    FROM lost_items
    WHERE student_id = ?
      AND item_name = ?
      AND category = ?
      AND date_lost = ?
      AND location_lost = ?
    LIMIT 1
");
$duplicateStmt->execute([
    $student_id,
    $item_name,
    $category,
    $date_lost,
    $location_lost
]);
$existingItem = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

if ($existingItem) {
    header("Location: report_lost.php?error=" . urlencode("Item already reported."));
    exit;
}

/* Optional image upload */
$image_path = null;

if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['item_image']['error'] !== 0) {
        header("Location: report_lost.php?error=" . urlencode("Image upload failed."));
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
        header("Location: report_lost.php?error=" . urlencode("Invalid image type. Please upload JPG, PNG, GIF, or WEBP."));
        exit;
    }

    $newFileName = time() . "_" . uniqid() . "." . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        header("Location: report_lost.php?error=" . urlencode("Image upload failed."));
        exit;
    }

    $image_path = "uploads/lost_items/" . $newFileName;
}

/* Save item */
try {
    $stmt = $pdo->prepare("
        INSERT INTO lost_items (
            student_id, item_name, description, category, color, image_path,
            date_lost, location_lost, status, is_public
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Unverified', 0)
    ");

    $stmt->execute([
        $student_id,
        $item_name,
        $description,
        $category,
        $color,
        $image_path,
        $date_lost,
        $location_lost
    ]);

    $lostItemId = $pdo->lastInsertId();

    logActivity(
        $pdo,
        'Student',
        $student_id,
        'Reported Lost Item',
        'Lost item ID: ' . $lostItemId
    );

    header("Location: report_lost.php?message=" . urlencode("Item submitted successfully."));
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header("Location: report_lost.php?error=" . urlencode("Item already reported."));
        exit;
    }

    header("Location: report_lost.php?error=" . urlencode("An unexpected error occurred."));
    exit;
}
?>