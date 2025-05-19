<?php
require_once 'session.php';
require_once 'connection.php';
require_once 'check_admin.php';

// Confirm admin status
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin || $admin['is_admin'] != 1) {
    die("Unauthorized access.");
}

$user_id = $_GET['id'];
if ($user_id != $_SESSION['user_id']) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
