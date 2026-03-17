<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$admin_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add_found.php?error=" . urlencode("Invalid request."));
    exit;
}

$item_name = trim($_POST['item_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$color = trim($_POST['color'] ?? '');
$date_found = $_POST['date_found'] ?? '';
$location_found = trim($_POST['location_found'] ?? '');

if (!$item_name || !$description || !$category || !$date_found || !$location_found) {
    header("Location: add_found.php?error=" . urlencode("Please fill in all required fields."));
    exit;
}

/* Duplicate check */
$duplicateStmt = $pdo->prepare("
    SELECT found_item_id
    FROM found_items
    WHERE item_name = ?
      AND category = ?
      AND date_found = ?
      AND location_found = ?
    LIMIT 1
");
$duplicateStmt->execute([
    $item_name,
    $category,
    $date_found,
    $location_found
]);
$existingItem = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

if ($existingItem) {
    header("Location: add_found.php?error=" . urlencode("Duplicate found item already exists."));
    exit;
}

/* Image upload */
$image_path = null;

if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['item_image']['error'] !== 0) {
        header("Location: add_found.php?error=" . urlencode("Image upload failed."));
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
        header("Location: add_found.php?error=" . urlencode("Invalid image type. Please upload JPG, PNG, GIF, or WEBP."));
        exit;
    }

    $newFileName = time() . "_" . uniqid() . "." . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        header("Location: add_found.php?error=" . urlencode("Image upload failed."));
        exit;
    }

    $image_path = "uploads/found_items/" . $newFileName;
}

/* Save found item */
try {
    $stmt = $pdo->prepare("
        INSERT INTO found_items (
            admin_id, item_name, description, category, color, image_path,
            date_found, location_found, status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')
    ");

    $stmt->execute([
        $admin_id,
        $item_name,
        $description,
        $category,
        $color,
        $image_path,
        $date_found,
        $location_found
    ]);

    $found_item_id = $pdo->lastInsertId();

    logActivity(
        $pdo,
        'Admin',
        $admin_id,
        'Added Found Item',
        'Found item ID: ' . $found_item_id
    );

    /* Matching logic */
    $matchStmt = $pdo->prepare("
        SELECT lost_item_id, item_name, color, image_path
        FROM lost_items
        WHERE status = 'Verified'
          AND category = ?
          AND (
                item_name LIKE ?
                OR color = ?
              )
    ");

    $searchName = '%' . $item_name . '%';
    $matchStmt->execute([$category, $searchName, $color]);
    $matches = $matchStmt->fetchAll(PDO::FETCH_ASSOC);

    $insertMatch = $pdo->prepare("
        INSERT INTO potential_matches (lost_item_id, found_item_id, confidence_note)
        VALUES (?, ?, ?)
    ");

    foreach ($matches as $match) {
        $note = "Possible match found based on category, item details, and uploaded evidence.";
        $insertMatch->execute([
            $match['lost_item_id'],
            $found_item_id,
            $note
        ]);
    }

    header("Location: add_found.php?message=" . urlencode("Found item saved successfully."));
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header("Location: add_found.php?error=" . urlencode("Duplicate found item already exists."));
        exit;
    }

    header("Location: add_found.php?error=" . urlencode("An unexpected error occurred."));
    exit;
}
?>