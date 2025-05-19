<?php
require_once 'session.php';
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['content']) && isset($_SESSION['user_id'])) {
    $post_id = (int) $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);

    if ($content !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        $stmt->execute();
    }
    header("Location: view_post.php?id=$post_id");
    exit;
} else {
    echo "Invalid request.";
}
