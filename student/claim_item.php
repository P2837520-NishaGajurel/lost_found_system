<?php
require '../config/db.php';
require '../includes/auth_student.php';
require '../includes/log_activity.php';

$student_id = $_SESSION['student_id'];
$found_item_id = $_GET['found_item_id'] ?? $_POST['found_item_id'] ?? null;

if (!$found_item_id) {
    die("Invalid item.");
}

/* Check item exists and is claimable */
$itemStmt = $pdo->prepare("
    SELECT found_item_id, item_name, status
    FROM found_items
    WHERE found_item_id = ?
");
$itemStmt->execute([$found_item_id]);
$item = $itemStmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found.");
}

/*
Only allow claim if item is Available or Claim Pending.
You can relax this if needed.
*/
if (!in_array($item['status'], ['Available', 'Claim Pending'], true)) {
    die("This item is no longer available for claim.");
}

/* Prevent duplicate claims by same student */
$checkStmt = $pdo->prepare("
    SELECT claim_id, status
    FROM claims
    WHERE student_id = ?
      AND found_item_id = ?
    LIMIT 1
");
$checkStmt->execute([$student_id, $found_item_id]);
$existingClaim = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($existingClaim) {
    header("Location: notifications.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_message = trim($_POST['claim_message'] ?? '');

    if ($claim_message === '') {
        die("Please provide a claim reason.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO claims (student_id, found_item_id, claim_message, status)
        VALUES (?, ?, ?, 'Pending')
    ");
    $stmt->execute([$student_id, $found_item_id, $claim_message]);

    $claimId = $pdo->lastInsertId();

    /*
    If item was Available, mark it Claim Pending.
    If already Claim Pending, keep it.
    */
    $upd = $pdo->prepare("
        UPDATE found_items
        SET status = 'Claim Pending'
        WHERE found_item_id = ?
          AND status = 'Available'
    ");
    $upd->execute([$found_item_id]);

    logActivity(
        $pdo,
        'Student',
        $student_id,
        'Submitted Claim',
        'Claim ID: ' . $claimId . ', Found item ID: ' . $found_item_id
    );

    header("Location: notifications.php");
    exit;
}

include '../includes/header.php';
?>

<div class="card">
    <h2>Claim Found Item</h2>
    <p><strong>Item:</strong> <?= htmlspecialchars($item['item_name']) ?></p>

    <form method="POST">
        <input type="hidden" name="found_item_id" value="<?= htmlspecialchars($found_item_id) ?>">

        <label>Why do you think this item is yours?</label>
        <textarea name="claim_message" required></textarea>

        <button type="submit" class="btn">Submit Claim</button>
        <a href="notifications.php" class="btn">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>