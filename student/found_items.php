<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';
$items = $pdo->query("SELECT * FROM found_items WHERE status='Available' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Available Found Items</h2>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Item</th><th>Category</th><th>Color</th><th>Location Found</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['category']) ?></td>
                    <td><?= htmlspecialchars($item['color']) ?></td>
                    <td><?= htmlspecialchars($item['location_found']) ?></td>
                    <td><a class="btn" href="claim_item.php?found_item_id=<?= $item['found_item_id'] ?>">Claim</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>