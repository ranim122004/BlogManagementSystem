<?php
require_once 'session.php';
require_once 'connection.php';

// Step 1: Get the post ID from the URL and validate it
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($post_id <= 0) {
    die("<div class='text-center mt-5 text-danger'>Invalid post ID.</div>");
}

// Step 2: Fetch the post
$stmt = $conn->prepare("
    SELECT posts.title, posts.content, posts.created_at, posts.image, users.username, categories.name AS category
    FROM posts
    JOIN users ON posts.author_id = users.id
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("<div class='text-center mt-5 text-danger'>Post not found.</div>");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']); ?> - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.07);
            background-color: #fff;
        }
        .card-title {
            font-size: 1.75rem;
            font-weight: 600;
        }
        .back-link {
            text-decoration: none;
            font-weight: 500;
            color: #0d6efd;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .post-image {
            max-width: 400px;
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4 mb-4">
        <h2 class="card-title"><?= htmlspecialchars($post['title']); ?></h2>

        <?php if (!empty($post['image']) && file_exists($post['image'])): ?>
            <img src="<?= htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
        <?php endif; ?>

        <p class="text-muted mb-1">
            By <?= htmlspecialchars($post['username']); ?> 
            on <?= $post['created_at']; ?>
        </p>
        <p class="text-muted mb-3">
            Category: <strong><?= htmlspecialchars($post['category'] ?? 'Uncategorized'); ?></strong>
        </p>
        <hr>
        <p style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($post['content'])); ?></p>
    </div>

    <!-- Comments Section -->
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Comments</h5>
        <?php
        $cstmt = $conn->prepare("
            SELECT comments.id, comments.content, comments.created_at, users.username, users.id AS commenter_id
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = ?
            ORDER BY comments.created_at DESC
        ");
        $cstmt->bind_param("i", $post_id);
        $cstmt->execute();
        $comments = $cstmt->get_result();

        if ($comments->num_rows > 0):
            while ($comment = $comments->fetch_assoc()):
        ?>
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <span class="text-muted small">on <?= $comment['created_at'] ?></span>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['commenter_id']): ?>
                        <a href="delete_comment.php?id=<?= $comment['id'] ?>&post_id=<?= $post_id ?>" class="text-danger small">Delete</a>
                    <?php endif; ?>
                </div>
                <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            </div>
        <?php
            endwhile;
        else:
            echo "<p class='text-muted'>No comments yet.</p>";
        endif;
        ?>
    </div>

    <!-- Comment Form -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="card p-4 mb-5">
            <h5 class="mb-3">Leave a Comment</h5>
            <form action="post_comment.php" method="post">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3" required placeholder="Write your comment here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">You must <a href="login.php">log in</a> to post a comment.</div>
    <?php endif; ?>

    <!-- Back Link -->
    <div class="text-center">
        <a href="index.php" class="back-link">&larr; Back to Blog</a>
    </div>
</div>

</body>
</html>
