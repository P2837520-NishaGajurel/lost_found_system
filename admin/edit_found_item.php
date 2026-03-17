<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$id = $_GET['id'] ?? '';

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

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<div class="card">
    <h2>Edit Found Item</h2>

    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="update_found_item.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="found_item_id" value="<?= htmlspecialchars($item['found_item_id']) ?>">

        <label>Item Name</label>
        <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" required>

        <label>Color</label>
        <input type="text" name="color" value="<?= htmlspecialchars($item['color']) ?>">

        <label>Date Found</label>
        <input type="date" name="date_found" value="<?= htmlspecialchars($item['date_found']) ?>" required>

        <label>Location Found</label>
        <input type="text" name="location_found" value="<?= htmlspecialchars($item['location_found']) ?>" required>

        <label>Replace Image (Optional)</label>
        <input type="file" name="item_image" accept="image/*">

        <?php if (!empty($item['image_path'])): ?>
            <p>Current Image:</p>
            <img src="/lost_found_system/<?= htmlspecialchars($item['image_path']) ?>" width="100" alt="Current Image">
        <?php endif; ?>

        <button type="submit" class="btn">Update Found Item</button>
        <a href="manage_found_items.php" class="btn">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>