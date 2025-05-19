<?php
// Step 1: Ensure the user is logged in and db is connected
require_once 'session.php';
require_once 'connection.php';

// Step 2: Fetch all blog posts by the logged-in user with category names
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT posts.id, posts.title, posts.content, posts.created_at, categories.name AS category_name
                        FROM posts
                        LEFT JOIN categories ON posts.category_id = categories.id
                        WHERE posts.author_id = ?
                        ORDER BY posts.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8ff;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 60px auto;
        }
        .post-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            background-color: white;
        }
        .dashboard-header {
            margin-bottom: 40px;
        }
        .btn-sm {
            font-size: 0.85rem;
        }
        .category-badge {
            font-size: 0.75rem;
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 4px 8px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container dashboard-container">
    <div class="dashboard-header text-center">
        <h2 class="text-primary">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
        <p>
            <a href="create_post.php" class="btn btn-success btn-sm">+ Create New Post</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </p>
        <hr>
    </div>

    <h4>Your Blog Posts</h4>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post-card">
                <h5 class="mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                <small class="text-muted">Posted on: <?php echo $row['created_at']; ?></small><br>
                <?php if (!empty($row['category_name'])): ?>
                    <span class="category-badge mt-1 d-inline-block">Category: <?php echo htmlspecialchars($row['category_name']); ?></span>
                <?php endif; ?>
                <p class="mt-2"><?php echo htmlspecialchars(substr($row['content'], 0, 120)); ?>...</p>
                <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">You have not written any posts yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
