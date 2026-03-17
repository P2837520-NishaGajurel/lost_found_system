<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Make sure database connection exists */
if (!isset($pdo)) {
    require __DIR__ . '/../config/db.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Item Management System</title>
    <link rel="stylesheet" href="/lost_found_system/assets/style.css">
</head>
<body>
<header>
    <div class="topbar">
        <h1>Lost &amp; Found Item Management System</h1>
        <button class="menu-toggle" id="menuToggle">☰</button>
    </div>

    <nav id="mainNav">
        <a href="/lost_found_system/index.php">Home</a>
        <a href="/lost_found_system/all_items.php">All Items</a>
        <a href="/lost_found_system/about.php">About Us</a>


        <?php if (!isset($_SESSION['student_id']) && !isset($_SESSION['admin'])): ?>
            <a href="/lost_found_system/register.php">Register</a>
            <a href="/lost_found_system/login.php">Student Login</a>
            <a href="/lost_found_system/admin/login.php">Admin Login</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['student_id'])): ?>
            <a href="/lost_found_system/student/dashboard.php">Dashboard</a>
            <a href="/lost_found_system/student/report_lost.php">Report Lost Item</a>
            <a href="/lost_found_system/student/found_items.php">Found Items</a>
            <?php
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    $notifStmt = $pdo->prepare("
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
    $notifStmt->execute([$student_id, $student_id]);
    $notifCount = $notifStmt->fetchColumn();
}
?>

             <a href="/lost_found_system/student/notifications.php">
    Notifications 
    <?php if (!empty($notifCount)): ?>
        <span class="notif-badge"><?= $notifCount ?></span>
    <?php endif; ?>
</a>
            <a href="/lost_found_system/student/my_claims.php">My Claims</a>
            <a href="/lost_found_system/logout.php">Logout</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin'])): ?>
            <a href="/lost_found_system/admin/dashboard.php">Admin Dashboard</a>
            <a href="/lost_found_system/admin/add_found.php">Report Found Item</a>
            <a href="/lost_found_system/admin/verify_items.php">Verify Items</a>
            <a href="/lost_found_system/admin/claims.php">Manage Claims</a>
            <a href="/lost_found_system/admin/notifications.php">Notifications</a>
            <a href="/lost_found_system/admin/activity_logs.php">Activity Logs</a>
            <a href="/lost_found_system/logout.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menuToggle");
    const mainNav = document.getElementById("mainNav");
    menuToggle.addEventListener("click", function () {
        mainNav.classList.toggle("show");
    });
});
</script>
