<?php
require '../config/db.php';
require '../includes/auth_admin.php';
require '../includes/log_activity.php';

$pdo->exec("UPDATE found_items SET status = 'Archived', archived_at = NOW() WHERE status = 'Handed Over' AND archived_at IS NULL");
logActivity($pdo, 'System', null, 'Archived Handed Over Items', 'System archived handed over items.');

$pdo->exec("DELETE FROM found_items WHERE status = 'Archived' AND archived_at IS NOT NULL AND archived_at <= NOW() - INTERVAL 15 DAY");
logActivity($pdo, 'System', null, 'Deleted Archived Items', 'System deleted archived items older than 15 days.');

header("Location: dashboard.php");
exit;
?>