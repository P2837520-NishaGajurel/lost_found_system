<?php
function logActivity($pdo, $userType, $userId, $action, $details = null) {
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (user_type, user_id, action, details)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$userType, $userId, $action, $details]);
}
?>