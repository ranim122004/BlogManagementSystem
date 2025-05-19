<?php
require_once 'session.php';
require_once 'connection.php';
require_once 'check_admin.php';

// Step 1: Confirm the user is an admin
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Step 2: Get post ID and delete directly
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id > 0) {
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
}

// Step 3: Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit;
?>
