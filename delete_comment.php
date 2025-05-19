<?php
require_once 'session.php';
require_once 'connection.php';

if (isset($_GET['id'], $_GET['post_id'], $_SESSION['user_id'])) {
    $comment_id = (int) $_GET['id'];
    $post_id = (int) $_GET['post_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
}

header("Location: view_post.php?id=$post_id");
exit;
