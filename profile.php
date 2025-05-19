<?php
require_once 'connection.php';

// Step 1: Get and validate user ID
$user_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if ($user_id <= 0) {
    die("<div class='text-danger text-center mt-5'>Invalid user ID.</div>");
}

// Step 2: Fetch user info
$user_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows !== 1) {
    die("<div class='text-danger text-center mt-5'>User not found.</div>");
}
$user = $user_result->fetch_assoc();

// Step 3: Fetch posts by user
$post_stmt = $conn->prepare("
    SELECT posts.id, posts.title, posts.created_at, categories.name as category
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.author_id = ?
    ORDER BY posts.created_at DESC
");
$post_stmt->bind_param("i", $user_id);
$post_stmt->execute();
$posts = $post_stmt->get_result();
$post_count = $posts->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user['username']); ?>'s Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f5;
            font-family: 'Poppins', sans-serif;
        }
        .container { max-width: 900px; margin-top: 60px; }
        .post-card { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center mb-4">
        <h2><?= htmlspecialchars($user['username']); ?>'s Profile</h2>
        <p class="text-muted">Total Posts: <?= $post_count; ?></p>
        <a href="index.php" class="btn btn-outline-primary btn-sm">&larr; Back to Blog</a>
    </div>

    <?php if ($post_count > 0): ?>
        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post-card">
                <h5><a href="view_post.php?id=<?= $post['id']; ?>" class="text-decoration-none"><?= htmlspecialchars($post['title']); ?></a></h5>
                <small class="text-muted">Posted on <?= $post['created_at']; ?> | <?= htmlspecialchars($post['category'] ?? 'Uncategorized'); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">This user has not written any posts yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
