<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$type = $_GET['type'] ?? 'pending';

if ($type === 'claimed') {
    $title = 'Claimed Items';
    $stmt = $pdo->query("
        SELECT 
            c.claim_id,
            c.status,
            c.created_at,
            s.full_name,
            s.email,
            f.item_name
        FROM claims c
        JOIN students s ON c.student_id = s.student_id
        JOIN found_items f ON c.found_item_id = f.found_item_id
        WHERE c.status IN ('Approved', 'Completed')
        ORDER BY c.created_at DESC
    ");
} else {
    $title = 'Pending Claims';
    $stmt = $pdo->query("
        SELECT 
            c.claim_id,
            c.status,
            c.created_at,
            s.full_name,
            s.email,
            f.item_name
        FROM claims c
        JOIN students s ON c.student_id = s.student_id
        JOIN found_items f ON c.found_item_id = f.found_item_id
        WHERE c.status = 'Pending'
        ORDER BY c.created_at DESC
    ");
}

$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="btn-group">
        <a class="btn" href="dashboard.php">Back to Dashboard</a>
        <a class="btn" href="claims.php">Manage Claims</a>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($claims)): ?>
                <?php foreach ($claims as $claim): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['full_name']) ?></td>
                        <td><?= htmlspecialchars($claim['email']) ?></td>
                        <td><?= htmlspecialchars($claim['item_name']) ?></td>
                        <td><?= htmlspecialchars($claim['status']) ?></td>
                        <td><?= htmlspecialchars($claim['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No claims found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>