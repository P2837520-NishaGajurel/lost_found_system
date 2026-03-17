<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';

$student_id = $_SESSION['student_id'];

/*
Get possible matches + claim status for this student
*/
$stmt = $pdo->prepare("
    SELECT 
        pm.match_id,
        li.item_name AS lost_name,
        fi.item_name AS found_name,
        fi.found_item_id,
        pm.confidence_note,
        c.status AS claim_status
    FROM potential_matches pm
    JOIN lost_items li ON pm.lost_item_id = li.lost_item_id
    JOIN found_items fi ON pm.found_item_id = fi.found_item_id
    LEFT JOIN claims c 
        ON c.found_item_id = fi.found_item_id
       AND c.student_id = ?
    WHERE li.student_id = ?
    ORDER BY pm.created_at DESC
");
$stmt->execute([$student_id, $student_id]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* mark notifications as seen */
$ids = [];
foreach ($matches as $m) {
    $ids[] = (int)$m['match_id'];
}
if (!empty($ids)) {
    $pdo->exec("UPDATE potential_matches SET student_seen = 1 WHERE match_id IN (" . implode(',', $ids) . ")");
}
?>

<div class="card">
    <h2>Potential Match Notifications</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Your Lost Item</th>
                    <th>Possible Found Item</th>
                    <th>Match Note</th>
                    <th>Claim Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($matches)): ?>
                <?php foreach ($matches as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['lost_name']) ?></td>
                        <td><?= htmlspecialchars($m['found_name']) ?></td>
                        <td><?= htmlspecialchars($m['confidence_note']) ?></td>
                        <td>
                            <?php if (!empty($m['claim_status'])): ?>
                                <span class="status-badge <?= strtolower($m['claim_status']) ?>">
                                    <?= htmlspecialchars($m['claim_status']) ?>
                                </span>
                            <?php else: ?>
                                <span class="status-badge new">New</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (empty($m['claim_status'])): ?>
                                <a class="btn" href="claim_item.php?found_item_id=<?= $m['found_item_id'] ?>">
                                    Claim
                                </a>
                            <?php else: ?>
                                <span class="muted-text">Already processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No match notifications available.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>