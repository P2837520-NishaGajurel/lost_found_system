<?php
require '../config/db.php';
require '../includes/auth_student.php';
require '../includes/log_activity.php';

$student_id = $_SESSION['student_id'];
$id = $_GET['id'] ?? '';

$stmt = $pdo->prepare("
    SELECT * FROM lost_items
    WHERE lost_item_id = ?
      AND student_id = ?
      AND status = 'Unverified'
    LIMIT 1
");
$stmt->execute([$id, $student_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found or cannot be deleted.");
}

/* delete related potential matches if any */
$pdo->prepare("DELETE FROM potential_matches WHERE lost_item_id = ?")->execute([$id]);

/* delete item */
$pdo->prepare("
    DELETE FROM lost_items
    WHERE lost_item_id = ?
      AND student_id = ?
      AND status = 'Unverified'
")->execute([$id, $student_id]);

logActivity($pdo, 'Student', $student_id, 'Deleted Lost Item', 'Lost item ID: ' . $id);

header("Location: my_items.php");
exit;
?>