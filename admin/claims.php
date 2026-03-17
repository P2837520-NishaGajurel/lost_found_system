<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$claims = $pdo->query("SELECT c.claim_id, c.status, c.claim_message, c.created_at, s.full_name, s.email, f.item_name, f.found_item_id FROM claims c JOIN students s ON c.student_id = s.student_id JOIN found_items f ON c.found_item_id = f.found_item_id ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Manage Claims</h2>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Student</th><th>Email</th><th>Item</th><th>Claim Reason</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($claims as $claim): ?>
                <tr>
                    <td><?= htmlspecialchars($claim['full_name']) ?></td>
                    <td><?= htmlspecialchars($claim['email']) ?></td>
                    <td><?= htmlspecialchars($claim['item_name']) ?></td>
                    <td><?= htmlspecialchars($claim['claim_message']) ?></td>
                    <td><?= htmlspecialchars($claim['status']) ?></td>
                    <td>
                        <?php if ($claim['status'] === 'Pending'): ?>
                            <a class="btn" href="approve_claim.php?id=<?= $claim['claim_id'] ?>&item=<?= $claim['found_item_id'] ?>">Approve</a>
                            <a class="btn" href="reject_claim.php?id=<?= $claim['claim_id'] ?>&item=<?= $claim['found_item_id'] ?>">Reject</a>
                        <?php elseif ($claim['status'] === 'Approved'): ?>
                            <a class="btn" href="handover_item.php?item=<?= $claim['found_item_id'] ?>">Hand Over</a>
                        <?php else: ?>
                            No action
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>