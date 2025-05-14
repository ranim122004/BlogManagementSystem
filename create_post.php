<?php
// Step 1: Require session and DB connection
require_once 'session.php';
require_once 'connection.php';  

$message = '';

// Step 2: Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 3: Sanitize user input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];

    // Step 4: Validate and insert post into database
    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $author_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Post created successfully. <a href='dashboard.php'>Go to Dashboard</a></div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning text-center'>Title and content cannot be empty.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Poppins', sans-serif;
        }
        .post-form-container {
            max-width: 700px;
            margin: 80px auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container post-form-container">
    <?php if (!empty($message)) echo $message; ?>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center text-primary mb-4">Create a New Blog Post</h3>

            <form action="create_post.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="8" class="form-control" required></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Publish</button>
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
