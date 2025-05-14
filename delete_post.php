<?php
// Step 1: Ensure the user is logged in and db is connected
require_once 'session.php';
require_once 'connection.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$message = '';
$showConfirmation = false;

// Step 2: Validate post ID
if ($post_id <= 0) {
    $message = "<div class='alert alert-danger text-center'>Invalid post ID.</div>";
} else {
    // Step 3: Ask for confirmation before deletion
    if (!isset($_GET['confirm'])) {
        $showConfirmation = true;
    } else {
        // Step 4: Perform deletion
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND author_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Post deleted successfully. <a href='dashboard.php'>Back to Dashboard</a></div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Error deleting post: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 100px auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            padding: 30px;
        }
        .btn {
            min-width: 120px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card text-center">
        <h3 class="text-danger mb-4">Delete Blog Post</h3>

        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php elseif ($showConfirmation): ?>
            <p class="fs-5">Are you sure you want to permanently delete this post?</p>
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="delete_post.php?id=<?php echo $post_id; ?>&confirm=yes" class="btn btn-danger">Yes, delete</a>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
