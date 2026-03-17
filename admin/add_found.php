<?php require '../includes/auth_admin.php';
 include '../includes/header.php'; ?>

<div class="card">
    <h2>Report Found Item</h2>
    <form action="save_found.php" method="POST" enctype="multipart/form-data">
        <label>Item Name</label><input type="text" name="item_name" required>
        <label>Description</label><textarea name="description" required></textarea>
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
        <label>Color</label><input type="text" name="color">
        <label>Date Found</label><input type="date" name="date_found" required>
        <label>Location Found</label><input type="text" name="location_found" required>
        <label>Upload Item Image (Optional)</label>
        <input type="file" name="item_image" accept="image/*">

        <button type="submit" class="btn">Save Found Item</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>

