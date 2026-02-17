<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$old = $_POST['old_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    die("Passwords do not match. <a href='profile.php'>Go back</a>");
}

// Get current password hash
$stmt = $conn->prepare("SELECT password FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// FIX: Verify old password
if (!password_verify($old, $result["password"])) {
    die("Old password is incorrect. <a href='profile.php'>Go back</a>");
}

// FIX: Hash new password securely
$newHash = password_hash($new, PASSWORD_DEFAULT);

$stmtUpdate = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmtUpdate->bind_param("si", $newHash, $user_id);
$stmtUpdate->execute();

header("Location: profile.php?password=updated");
exit;