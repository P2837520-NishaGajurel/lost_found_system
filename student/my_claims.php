<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';

$stmt = $pdo->prepare("
    SELECT c.*, f.item_name
    FROM claims c
    JOIN found_items f ON c.found_item_id = f.found_item_id
    WHERE c.student_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['student_id']]);
$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>My Claims</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($claims)): ?>
                <?php foreach ($claims as $claim): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['item_name']) ?></td>
                        <td>
                            <span class="status-badge <?= strtolower($claim['status']) ?>">
                                <?= htmlspecialchars($claim['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($claim['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">You have not made any claims yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>