<?php
require 'config/db.php';
require 'includes/log_activity.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['student_name'] = $student['full_name'];
        logActivity($pdo, 'Student', $student['student_id'], 'Student Login', 'Student logged into the system.');
        header("Location: student/dashboard.php");
        exit;
    } else {
        $error = "Invalid login.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <h2>Student Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>