<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$logs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Activity Logs</h2>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>User Type</th><th>User ID</th><th>Action</th><th>Details</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['user_type']) ?></td>
                    <td><?= htmlspecialchars((string)$log['user_id']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars((string)$log['details']) ?></td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>