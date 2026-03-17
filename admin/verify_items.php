<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$stmt = $pdo->query("
    SELECT li.*, s.full_name
    FROM lost_items li
    JOIN students s ON li.student_id = s.student_id
    WHERE li.status = 'Unverified'
    ORDER BY li.created_at DESC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Verify Items</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Student</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Color</th>
                    <th>Date Lost</th>
                    <th>Location Lost</th>
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
                    <td><?= htmlspecialchars($item['full_name']) ?></td>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['category']) ?></td>
                    <td><?= htmlspecialchars($item['color']) ?></td>
                    <td><?= htmlspecialchars($item['date_lost']) ?></td>
                    <td><?= htmlspecialchars($item['location_lost']) ?></td>
                    <td><a class="btn" href="verify_lost.php?id=<?= $item['lost_item_id'] ?>">Verify</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>