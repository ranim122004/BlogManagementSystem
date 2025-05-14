<?php
// Step 1: Connect to the db
require_once 'connection.php';

// Step 2: Get the post ID from the URL and validate it
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($post_id <= 0) {
    die("<div class='text-center mt-5 text-danger'>Invalid post ID.</div>");
}

// Step 3: Fetch the post and author information from the database
$stmt = $conn->prepare("
    SELECT posts.title, posts.content, posts.created_at, users.username
    FROM posts
    JOIN users ON posts.author_id = users.id
    WHERE posts.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

// Step 4: If no post found, show error
if ($result->num_rows !== 1) {
    die("<div class='text-center mt-5 text-danger'>Post not found.</div>");
}

// Step 5: Store post data
$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Step 6: Include Bootstrap and Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Step 7: Custom Styling -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 60px auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.07);
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
    </style>
</head>
<body>

<!-- Step 8: Post Display Section -->
<div class="container">
    <div class="card p-4">
        <h2 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
        <p class="text-muted mb-3">By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></p>
        <hr>
        <p style="white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    </div>

    <!-- Step 9: Back Link -->
    <div class="text-center mt-4">
        <a href="index.php" class="back-link">&larr; Back to Blog</a>
    </div>
</div>

</body>
</html>
