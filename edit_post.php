<?php
require_once 'session.php';
require_once 'connection.php';

$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = '';

// Fetch the post to edit
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

// Fetch categories
$category_result = $conn->query("SELECT id, name FROM categories");

// Process form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $imagePath = $post['image']; // Use existing image by default

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        $fileSize = $_FILES['image']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) { // Max 2MB
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadDir = 'uploads/';
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Optionally delete old image
                if (!empty($post['image']) && file_exists($post['image'])) {
                    unlink($post['image']);
                }
                $imagePath = $targetFile;
            } else {
                $message = "<div class='alert alert-danger text-center'>Failed to upload image.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning text-center'>Invalid image type or file size exceeds 2MB.</div>";
        }
    }

    // Update post in database
    if (!empty($title) && !empty($content) && $category_id) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $title, $content, $category_id, $imagePath, $post_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Post updated successfully. <a href='dashboard.php'>Go to Dashboard</a></div>";
            // Refresh updated values
            $post['title'] = $title;
            $post['content'] = $content;
            $post['category_id'] = $category_id;
            $post['image'] = $imagePath;
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
    <title>Edit Post</title>
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
            <h3 class="card-title text-center text-primary mb-4">Edit Blog Post</h3>

            <form action="edit_post.php?id=<?= $post_id ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="8" class="form-control" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select a Category</option>
                        <?php while ($cat = $category_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                    <?php if (!empty($post['image'])): ?>
                        <div class="mt-2 text-muted">Current Image: <?= htmlspecialchars(basename($post['image'])) ?></div>
                    <?php endif; ?>
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
