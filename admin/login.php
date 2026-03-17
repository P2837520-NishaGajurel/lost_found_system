<?php
require '../config/db.php';
require '../includes/log_activity.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['admin_id'];
        logActivity($pdo, 'Admin', $admin['admin_id'], 'Admin Login', 'Admin logged into the system.');
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="card">
    <h2>Admin Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>