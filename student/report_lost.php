<?php
require '../includes/auth_student.php';
include '../includes/header.php';

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<div class="card">
    <h2>Report Lost Item</h2>

    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="save_lost.php" method="POST" enctype="multipart/form-data">
        <label>Item Name</label>
        <input type="text" name="item_name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label class="required">Category</label>
                <select name="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="book" <?php echo (isset($_POST['category']) && $_POST['category'] == 'book') ? 'selected' : ''; ?>>📚 Book / Notebook</option>
                    <option value="electronics" <?php echo (isset($_POST['category']) && $_POST['category'] == 'electronics') ? 'selected' : ''; ?>>💻 Electronics (Phone, Laptop, Calculator)</option>
                    <option value="stationery" <?php echo (isset($_POST['category']) && $_POST['category'] == 'stationery') ? 'selected' : ''; ?>>✏️ Stationery (Pen, Pencil, Eraser)</option>
                    <option value="clothing" <?php echo (isset($_POST['category']) && $_POST['category'] == 'clothing') ? 'selected' : ''; ?>>👕 Clothing (Uniform, Jacket, Cap)</option>
                    <option value="accessories" <?php echo (isset($_POST['category']) && $_POST['category'] == 'accessories') ? 'selected' : ''; ?>>⌚ Accessories (Watch, Glasses, Bag)</option>
                    <option value="other" <?php echo (isset($_POST['category']) && $_POST['category'] == 'other') ? 'selected' : ''; ?>>🔍 Other</option>
                </select>

        <label>Color</label>
        <input type="text" name="color">

        <label>Upload Item Image (Optional)</label>
        <input type="file" name="item_image" accept="image/*">

        <label>Date Lost</label>
        <input type="date" name="date_lost" required>

        <label>Location Lost</label>
        <input type="text" name="location_lost" required>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>