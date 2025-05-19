<?php
require_once 'session.php';
require_once 'connection.php';
require_once 'check_admin.php';

// Fetch users and posts
$users = $conn->query("SELECT id, username, email, is_admin, is_active FROM users");
$posts = $conn->query("SELECT id, title, created_at FROM posts");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
        }
        .section-title {
            font-weight: 600;
            color: #34495e;
        }
        .btn-sm {
            margin: 2px;
        }
        .badge {
            font-size: 0.75rem;
        }
        .table thead {
            background-color: #f1f3f5;
        }
    </style>
</head>
<body class="container py-5">

    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h2 class="text-dark"><i class="fas fa-user-shield me-2 text-primary"></i>Admin Dashboard</h2>
        <a href="logout.php" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="card mb-5 p-4">
        <h4 class="section-title mb-3"><i class="fas fa-users text-secondary me-2"></i>All Users</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= $u['is_admin'] ? '<span class="badge bg-primary">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td><?= $u['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-dark">Inactive</span>' ?></td>
                            <td>
                                <?php if (!$u['is_admin']): ?>
                                    <?php if ($u['is_active']): ?>
                                        <a href="deactivate_user.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-user-slash"></i> Deactivate
                                        </a>
                                    <?php else: ?>
                                        <a href="reactivate_user.php?id=<?= $u['id'] ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-user-check"></i> Activate
                                        </a>
                                    <?php endif; ?>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card p-4">
        <h4 class="section-title mb-3"><i class="fas fa-blog text-secondary me-2"></i>All Posts</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $posts->fetch_assoc()): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= $p['created_at'] ?></td>
                            <td>
                                <a href="admin_delete_post.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
