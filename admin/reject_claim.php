<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$claim_id = $_GET['id'] ?? null;
$item_id = $_GET['item'] ?? null;

if ($claim_id && $item_id) {
    $pdo->prepare("UPDATE claims SET status = 'Rejected', reviewed_at = NOW() WHERE claim_id = ?")->execute([$claim_id]);
    $pdo->prepare("UPDATE found_items SET status = 'Available' WHERE found_item_id = ?")->execute([$item_id]);
    logActivity($pdo, 'Admin', $_SESSION['admin_id'], 'Rejected Claim', 'Claim ID: ' . $claim_id . ', Found item ID: ' . $item_id);
}
header("Location: claims.php");
exit;
?>