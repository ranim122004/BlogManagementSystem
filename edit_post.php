<?php
// Step 1: Ensure the user is logged in and db is connected
require_once 'session.php';
require_once 'connection.php';

$message = '';

// Step 2: Get the post ID and validate it
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($post_id <= 0) {
    die("<div class='text-center text-danger mt-5'>Invalid post ID.</div>");
}

// Step 3: If the form is submitted, update the post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND author_id = ?");
        $stmt->bind_param("ssii", $title, $content, $post_id, $user_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Post updated successfully. <a href='dashboard.php'>Back to Dashboard</a></div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Error updating post: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning text-center'>Title and content cannot be empty.</div>";
    }
}

// Step 4: Load the existing post data for the form
$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ? AND author_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("<div class='text-center text-danger mt-5'>Post not found or unauthorized.</div>");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Poppins', sans-serif;
        }
        .edit-post-container {
            max-width: 700px;
            margin: 80px auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
        }
    </style>
</head>
<body>

<div class="container edit-post-container">
    <?php if (!empty($message)) echo $message; ?>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center text-primary mb-4">Edit Blog Post</h3>

            <!-- Step 5: Display form pre-filled with current post data -->
            <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="8" class="form-control" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Update Post</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="text-decoration-none">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
