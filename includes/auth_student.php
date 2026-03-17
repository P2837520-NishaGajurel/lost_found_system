<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['student_id'])) {
    header("Location: /lost_found_system/login.php");
    exit;
}
?>