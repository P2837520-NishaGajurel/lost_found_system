<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("UPDATE lost_items SET status = 'Verified', is_public = 1 WHERE lost_item_id = ?");
    $stmt->execute([$id]);
    logActivity($pdo, 'Admin', $_SESSION['admin_id'], 'Verified Lost Item', 'Lost item ID: ' . $id);
}
header("Location: verify_items.php");
exit;
?>