<?php
require 'config/db.php';
require 'includes/log_activity.php';
session_start();

$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $class_name = trim($_POST['class_name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO students (full_name, email, class_name, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $class_name, $password]);
        $studentId = $pdo->lastInsertId();
        logActivity($pdo, 'Student', $studentId, 'Student Registered', 'New student account created.');
        $message = "Registration successful. Please log in.";
    } catch (PDOException $e) {
        $isError = true;
        $message = "Email already exists.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <h2>Student Registration</h2>
    <?php if ($message): ?><p class="<?= $isError ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Class</label>
        <input type="text" name="class_name" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">Register</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>