<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';

$student_id = $_SESSION['student_id'];
$id = $_GET['id'] ?? '';

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
    die("Item not found or cannot be edited.");
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<div class="card">
    <h2>Edit Lost Item</h2>

    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="update_item.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="lost_item_id" value="<?= htmlspecialchars($item['lost_item_id']) ?>">

        <label>Item Name</label>
        <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" required>

        <label>Color</label>
        <input type="text" name="color" value="<?= htmlspecialchars($item['color']) ?>">

        <label>Date Lost</label>
        <input type="date" name="date_lost" value="<?= htmlspecialchars($item['date_lost']) ?>" required>

        <label>Location Lost</label>
        <input type="text" name="location_lost" value="<?= htmlspecialchars($item['location_lost']) ?>" required>

        <label>Replace Image (Optional)</label>
        <input type="file" name="item_image" accept="image/*">

        <?php if (!empty($item['image_path'])): ?>
            <p>Current Image:</p>
            <img src="/lost_found_system/<?= htmlspecialchars($item['image_path']) ?>" width="100" alt="Current Image">
        <?php endif; ?>

        <button type="submit" class="btn">Update Item</button>
        <a href="my_items.php" class="btn">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>