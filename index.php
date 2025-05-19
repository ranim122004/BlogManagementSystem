<?php
require_once 'connection.php';

// Step 1: Get and sanitize query parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_safe = "%$search%";
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$valid_limits = [5, 10];
$limit = (isset($_GET['limit']) && in_array((int)$_GET['limit'], $valid_limits)) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Step 2: Get categories for dropdown
$category_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

// Step 3: Prepare query filters
$where = '1';
$params = [];
$types = '';

if ($search !== '') {
    $where .= ' AND (title LIKE ? OR content LIKE ?)';
    $params[] = $search_safe;
    $params[] = $search_safe;
    $types .= 'ss';
}
if ($category_id > 0) {
    $where .= ' AND category_id = ?';
    $params[] = $category_id;
    $types .= 'i';
}

// Step 4: Count total filtered posts
$count_sql = "SELECT COUNT(*) as total FROM posts WHERE $where";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total_posts = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);

// Step 5: Fetch paginated posts — ADDED `users.id AS user_id`
$params[] = $offset;
$params[] = $limit;
$types .= 'ii';

$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.id AS user_id, users.username, categories.name as category
        FROM posts
        JOIN users ON posts.author_id = users.id
        LEFT JOIN categories ON posts.category_id = categories.id
        WHERE $where
        ORDER BY posts.created_at DESC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html, body { height: 100%; display: flex; flex-direction: column; }
        main { flex: 1; }
        body { font-family: 'Poppins', sans-serif; background-color: #f9f9f9; }
        .navbar { box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .post-card { box-shadow: 0 4px 10px rgba(0,0,0,0.06); border-radius: 12px; transition: transform 0.2s ease; }
        .post-card:hover { transform: translateY(-3px); cursor: pointer; }
        .footer { background-color: #343a40; color: #fff; padding: 20px 0; }
        .footer a { color: #ffc107; text-decoration: none; }
    </style>
</head>
<body>
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
<main>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="text-primary mb-0">Latest Blog Posts</h2>
            <form method="get" class="d-inline-flex align-items-center gap-2">
                <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control form-control-sm" placeholder="Search..." autofocus />
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <input type="hidden" name="category" value="<?= $category_id ?>">
                <button class="btn btn-outline-primary btn-sm" type="submit" title="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <form method="get" class="d-inline-flex align-items-center gap-2">
                <label for="limit" class="form-label mb-0">Posts per page:</label>
                <select id="limit" name="limit" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="5" <?= $limit === 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $limit === 10 ? 'selected' : '' ?>>10</option>
                </select>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                <input type="hidden" name="category" value="<?= $category_id ?>">
                <input type="hidden" name="page" value="1">
            </form>
            <form method="get" class="d-inline-flex align-items-center gap-2">
                <label for="category" class="form-label mb-0">Category:</label>
                <select id="category" name="category" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="0">All</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <input type="hidden" name="page" value="1">
            </form>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card post-card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="view_post.php?id=<?= $row['id']; ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($row['title']); ?>
                                    </a>
                                </h5>
                                <small class="text-muted">
                                    By <a href="profile.php?user=<?= $row['user_id']; ?>" class="text-decoration-none text-primary">
                                        <?= htmlspecialchars($row['username']); ?>
                                    </a> on <?= $row['created_at']; ?> | <?= htmlspecialchars($row['category'] ?? 'Uncategorized'); ?>
                                </small>
                                <p class="mt-3"><?= nl2br(htmlspecialchars(substr($row['content'], 0, 180))); ?>...</p>
                                <a href="view_post.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-warning">No posts found for your search.</div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search); ?>&limit=<?= $limit ?>&category=<?= $category_id ?>&page=<?= max($page - 1, 1) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search); ?>&limit=<?= $limit ?>&category=<?= $category_id ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search); ?>&limit=<?= $limit ?>&category=<?= $category_id ?>&page=<?= min($page + 1, $total_pages) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>
<footer class="footer text-center mt-auto">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y'); ?> MyBlog. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
