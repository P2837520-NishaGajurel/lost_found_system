<?php
require '../config/db.php';
require '../includes/auth_student.php';
include '../includes/header.php';

$student_id = $_SESSION['student_id'];

$totalFound = $pdo->query("
    SELECT COUNT(*) 
    FROM found_items 
    WHERE status = 'Available'
")->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM lost_items 
    WHERE student_id = ?
");
$stmt->execute([$student_id]);
$myLost = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM potential_matches pm
    JOIN lost_items li ON pm.lost_item_id = li.lost_item_id
    LEFT JOIN claims c 
        ON c.found_item_id = pm.found_item_id
       AND c.student_id = ?
    WHERE li.student_id = ?
      AND pm.student_seen = 0
      AND c.claim_id IS NULL
");
$stmt->execute([$student_id, $student_id]);
$potentialClaims = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM claims 
    WHERE student_id = ? AND status = 'Pending'
");
$stmt->execute([$student_id]);
$pendingClaims = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM claims 
    WHERE student_id = ? AND status = 'Approved'
");
$stmt->execute([$student_id]);
$approvedClaims = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM lost_items 
    WHERE student_id = ? AND status = 'Unverified'
");
$stmt->execute([$student_id]);
$unverifiedItems = $stmt->fetchColumn();
?>

<div class="card">
    <h2>Student Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['student_name']) ?></p>
</div>

<div class="dashboard-grid">
    <div class="card"><h3>Total Found Items</h3><p><?= $totalFound ?></p><a class="btn" href="found_items.php">View</a></div>
    <div class="card"><h3>My Lost Items</h3><p><?= $myLost ?></p><a class="btn" href="my_items.php">View</a></div>
    <div class="card"><h3>Potential Claims</h3><p><?= $potentialClaims ?></p><a class="btn" href="notifications.php">View</a></div>
    <div class="card"><h3>Pending Claims</h3><p><?= $pendingClaims ?></p><a class="btn" href="my_claims.php">View</a></div>
    <div class="card"><h3>Approved Claims</h3><p><?= $approvedClaims ?></p><a class="btn" href="my_claims.php">View</a></div>
    <div class="card"><h3>Unverified Items</h3><p><?= $unverifiedItems ?></p><a class="btn" href="my_items.php">View</a></div>
</div>

<div class="card">
    <div class="btn-group">
        <a class="btn" href="report_lost.php">Report Lost Item</a>
        <a class="btn" href="found_items.php">Found Items</a>
        <a class="btn" href="notifications.php">Notifications</a>
        <a class="btn" href="my_claims.php">My Claims</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>