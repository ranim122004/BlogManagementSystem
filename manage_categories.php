<?php
require_once 'session.php';
require_once 'connection.php';
require_once 'check_Admin.php';

$message = '';

// Add new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_category'])) {
    $newCategory = trim($_POST['new_category']);
    if (!empty($newCategory)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $newCategory);
        $stmt->execute();
        $stmt->close();
        $message = "<div class='alert alert-success text-center'>Category added successfully.</div>";
    } else {
        $message = "<div class='alert alert-warning text-center'>Category name cannot be empty.</div>";
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $cat_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php");
    exit;
}

// Fetch all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f9ff;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 60px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-inline input {
            flex: 1;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h3 class="mb-4 text-center text-primary"><i class="fas fa-tags me-2"></i>Manage Categories</h3>

        <?php if (!empty($message)) echo $message; ?>

        <!-- Add Category -->
        <form method="POST" class="d-flex gap-2 mb-4">
            <input type="text" name="new_category" class="form-control" placeholder="New category name" required>
            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
        </form>

        <!-- Category List -->
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="10%">ID</th>
                    <th>Category Name</th>
                    <th width="20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
