<?php
require 'config/db.php';
include 'includes/header.php';

$lostItems = $pdo->query("SELECT item_name, category, date_lost, location_lost FROM lost_items WHERE is_public = 1 ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$foundItems = $pdo->query("SELECT item_name, category, date_found, location_found FROM found_items WHERE status IN ('Available','Claim Pending','Claim Approved') ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <h1>Lost &amp; Found Item Management System</h1>
    <p>A responsive digital platform for reporting, tracking, and recovering lost items in school.</p>
    <div class="btn-group">
        <a class="btn" href="register.php">Register</a>
        <a class="btn" href="login.php">Student Login</a>
        <a class="btn" href="admin/login.php">Admin Login</a>
        <a class="btn" href="all_items.php">View All Items</a>
    </div>
</section>

<section class="card">
    <h2>System Features</h2>
    <ul>
        <li>Students can report lost items online.</li>
        <li>Admin verifies lost items before public display.</li>
        <li>Admin can report found items.</li>
        <li>Students receive possible match notifications.</li>
        <li>Students can claim found items.</li>
        <li>Admin manages claims and handover.</li>
        <li>Items are archived after handover and removed after 15 days.</li>
    </ul>
</section>


<?php include 'includes/footer.php'; ?>