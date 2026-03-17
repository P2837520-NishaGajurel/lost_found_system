<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$item_id = $_GET['item'] ?? null;

if ($item_id) {
    $pdo->prepare("UPDATE found_items SET status = 'Handed Over', handed_over_at = NOW() WHERE found_item_id = ?")->execute([$item_id]);
    $pdo->prepare("UPDATE claims SET status = 'Completed' WHERE found_item_id = ? AND status = 'Approved'")->execute([$item_id]);
    logActivity($pdo, 'Admin', $_SESSION['admin_id'], 'Handed Over Item', 'Found item ID: ' . $item_id);
}
header("Location: claims.php");
exit;
?>