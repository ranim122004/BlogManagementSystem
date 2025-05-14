<?php
// Step 1: Connect to the database
require_once 'connection.php';

// Step 2: Handle pagination setup
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Step 3: Count total posts for pagination
$total_result = $conn->query("SELECT COUNT(*) as total FROM posts");
$total_row = $total_result->fetch_assoc();
$total_posts = $total_row['total'];
$total_pages = ceil($total_posts / $limit);

// Step 4: Fetch posts with author info
$query = "
    SELECT posts.id, posts.title, posts.content, posts.created_at, users.username
    FROM posts
    JOIN users ON posts.author_id = users.id
    ORDER BY posts.created_at DESC
    LIMIT ?, ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Step 5: Include Bootstrap, Google Fonts, and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Step 6: Custom Styling -->
    <style>
        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
        }

        .navbar {
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .post-card {
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            border-radius: 12px;
            transition: transform 0.2s ease;
        }

        .post-card:hover {
            transform: translateY(-3px);
            cursor: pointer;
        }

        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 20px 0;
        }

        .footer a {
            color: #ffc107;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Step 7: Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <i class="bi bi-journal-text me-2 fs-4"></i> MyBlog
        </a>
        <div>
            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
            <a href="register.php" class="btn btn-warning btn-sm">Register</a>
        </div>
    </div>
</nav>

<!-- Step 8: Main Content Section -->
<main>
    <div class="container my-5">
        <h2 class="text-center mb-4 text-primary">Latest Blog Posts</h2>

        <div class="row">
            <!-- Step 9: Loop through posts and display each -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card post-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="view_post.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </h5>
                            <small class="text-muted">
                                By <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?>
                            </small>
                            <p class="mt-3"><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 180))); ?>...</p>
                            <a href="view_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Step 10: Pagination links -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</main>

<!-- Step 11:Footer -->
<footer class="footer text-center mt-auto">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MyBlog. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
