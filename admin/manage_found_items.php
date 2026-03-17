<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$stmt = $pdo->query("
    SELECT fi.*,
           (SELECT COUNT(*) FROM claims c WHERE c.found_item_id = fi.found_item_id) AS claim_count
    FROM found_items fi
    ORDER BY fi.created_at DESC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Manage Found Items</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Claims</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image_path'])): ?>
                            <img src="/lost_found_system/<?= htmlspecialchars($item['image_path']) ?>" width="70" alt="Item Image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['category']) ?></td>
                    <td><?= htmlspecialchars($item['status']) ?></td>
                    <td><?= (int)$item['claim_count'] ?></td>
                    <td>
                        <a class="btn" href="edit_found_item.php?id=<?= $item['found_item_id'] ?> "onclick="return confirm('Are you sure you want to edit this found item?');">Edit</a>

                        <?php if ((int)$item['claim_count'] === 0): ?>
                            <a class="btn" href="delete_found_item.php?id=<?= $item['found_item_id'] ?>" onclick="return confirm('Are you sure you want to delete this found item?');">Delete</a>
                        <?php else: ?>
                            <span class="muted-text">Cannot delete</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>