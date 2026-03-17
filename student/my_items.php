<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';

$stmt = $pdo->prepare("
    SELECT * FROM lost_items
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['student_id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>My Reported Lost Items</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Public</th>
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
                    <td><?= $item['is_public'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <?php if ($item['status'] === 'Unverified'): ?>
                            <a class="btn" href="edit_item.php?id=<?= $item['lost_item_id'] ?>"onclick="return confirm('Are you sure you want to edit this item?');">Edit</a>
                            <a class="btn" href="delete_item.php?id=<?= $item['lost_item_id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        <?php else: ?>
                            <span class="muted-text">Locked</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>