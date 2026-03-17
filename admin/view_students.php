<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$stmt = $pdo->query("
    SELECT student_id, full_name, email, class_name, created_at
    FROM students
    ORDER BY created_at DESC
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Total Students</h2>
    <div class="btn-group">
        <a class="btn" href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['class_name']) ?></td>
                        <td><?= htmlspecialchars($student['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>