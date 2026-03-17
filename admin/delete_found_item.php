<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$id = $_GET['id'] ?? '';

$stmt = $pdo->prepare("
    SELECT fi.*,
           (SELECT COUNT(*) FROM claims c WHERE c.found_item_id = fi.found_item_id) AS claim_count
    FROM found_items fi
    WHERE fi.found_item_id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Found item not found.");
}

if ((int)$item['claim_count'] > 0) {
    die("Cannot delete item because claims already exist.");
}

/* delete possible matches */
$pdo->prepare("DELETE FROM potential_matches WHERE found_item_id = ?")->execute([$id]);

/* delete item */
$pdo->prepare("DELETE FROM found_items WHERE found_item_id = ?")->execute([$id]);

logActivity($pdo, 'Admin', $_SESSION['admin_id'], 'Deleted Found Item', 'Found item ID: ' . $id);

header("Location: manage_found_items.php");
exit;
?>