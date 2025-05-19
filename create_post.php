<?php
// Step 1: Require session and DB connection
require_once 'session.php';
require_once 'connection.php';

$message = '';

// Step 2: Fetch categories for the dropdown
$category_result = $conn->query("SELECT id, name FROM categories");

// Step 3: Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 4: Sanitize user input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];
    $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : null;

    // Step 5: Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        $fileSize = $_FILES['image']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) { // 2MB max
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadDir = 'uploads/';
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $message = "<div class='alert alert-danger text-center'>Failed to upload image.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning text-center'>Invalid image type or file size exceeds 2MB.</div>";
        }
    }

    // Step 6: Validate and insert post into database
    if (!empty($title) && !empty($content) && $category_id) {
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id, category_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $title, $content, $author_id, $category_id, $imagePath);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Post created successfully. <a href='dashboard.php'>Go to Dashboard</a></div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning text-center'>All fields are required.</div>";
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

            <form action="create_post.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="8" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select a Category</option>
                        <?php while ($cat = $category_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
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
