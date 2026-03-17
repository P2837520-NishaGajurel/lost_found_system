<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$totalLostItems = $pdo->query("SELECT COUNT(*) FROM lost_items")->fetchColumn();
$totalFoundItems = $pdo->query("SELECT COUNT(*) FROM found_items")->fetchColumn();
$totalItems = $totalLostItems + $totalFoundItems;

$claimedItems = $pdo->query("
    SELECT COUNT(*) 
    FROM claims
    WHERE status IN ('Approved', 'Completed')
")->fetchColumn();

$pendingClaims = $pdo->query("
    SELECT COUNT(*) 
    FROM claims
    WHERE status = 'Pending'
")->fetchColumn();

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

$newLostNotifications = $pdo->query("
    SELECT COUNT(*) 
    FROM lost_items
    WHERE status = 'Unverified'
")->fetchColumn();

$newClaimNotifications = $pdo->query("
    SELECT COUNT(*) 
    FROM claims
    WHERE status = 'Pending'
")->fetchColumn();

$totalNotifications = $newLostNotifications + $newClaimNotifications;
?>

<div class="card">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Admin</p>
</div>

<div class="dashboard-grid">
    <a class="dashboard-link-card" href="view_items.php?type=all">
        <div class="card">
            <h3>Total Items</h3>
            <p><?= $totalItems ?></p>
        </div>
    </a>

    <a class="dashboard-link-card" href="view_items.php?type=lost">
        <div class="card">
            <h3>Lost Items</h3>
            <p><?= $totalLostItems ?></p>
        </div>
    </a>

    <a class="dashboard-link-card" href="view_items.php?type=found">
        <div class="card">
            <h3>Found Items</h3>
            <p><?= $totalFoundItems ?></p>
        </div>
    </a>

    <a class="dashboard-link-card" href="view_claims.php?type=claimed">
        <div class="card">
            <h3>Claimed Items</h3>
            <p><?= $claimedItems ?></p>
        </div>
    </a>

    <a class="dashboard-link-card" href="view_claims.php?type=pending">
        <div class="card">
            <h3>Pending Claims</h3>
            <p><?= $pendingClaims ?></p>
        </div>
    </a>

    <a class="dashboard-link-card" href="view_students.php">
        <div class="card">
            <h3>Total Students</h3>
            <p><?= $totalStudents ?></p>
        </div>
    </a>
</div>

<div class="card">
    <h3>Admin Actions</h3>
    <div class="btn-group">
        <a class="btn" href="add_found.php">Report Found Item</a>
        <a class="btn" href="manage_found_items.php">Manage Found Items</a>
        <a class="btn" href="verify_items.php">Verify Items</a>
        <a class="btn" href="claims.php">Manage Claims</a>
        <a class="btn" href="notifications.php">Notifications (<?= $totalNotifications ?>)</a>
        <a class="btn" href="activity_logs.php">Activity Logs</a>
        <a class="btn" href="archive_cleanup.php">Run Archive Cleanup</a>
        <a class="btn" href="../logout.php">Log Out</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>