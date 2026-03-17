<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$unverifiedItems = $pdo->query("
    SELECT 
        li.lost_item_id,
        li.item_name,
        li.category,
        li.created_at,
        s.full_name
    FROM lost_items li
    JOIN students s ON li.student_id = s.student_id
    WHERE li.status = 'Unverified'
    ORDER BY li.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$pendingClaims = $pdo->query("
    SELECT 
        c.claim_id,
        c.created_at,
        c.status,
        s.full_name,
        f.item_name
    FROM claims c
    JOIN students s ON c.student_id = s.student_id
    JOIN found_items f ON c.found_item_id = f.found_item_id
    WHERE c.status = 'Pending'
    ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Admin Notifications</h2>
</div>

<div class="card">
    <h3>New Lost Item Reports</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Reported At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($unverifiedItems)): ?>
                <?php foreach ($unverifiedItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['full_name']) ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['created_at']) ?></td>
                        <td><span class="status-badge pending">Unverified</span></td>
                        <td>
                            <a class="btn" href="verify_lost.php?id=<?= $item['lost_item_id'] ?>">Verify</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No new lost item reports.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h3>Student Claim Requests</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Claimed Item</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($pendingClaims)): ?>
                <?php foreach ($pendingClaims as $claim): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['full_name']) ?></td>
                        <td><?= htmlspecialchars($claim['item_name']) ?></td>
                        <td><span class="status-badge pending"><?= htmlspecialchars($claim['status']) ?></span></td>
                        <td><?= htmlspecialchars($claim['created_at']) ?></td>
                        <td>
                            <a class="btn" href="claims.php">Open Claims</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No pending claim requests.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>