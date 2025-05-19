<?php
require_once 'connection.php';

$token = $_GET['token'] ?? '';
$message = '';

// POST form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate token exists and is not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Update password & clear token
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        $stmt_update->bind_param("si", $hashedPassword, $user_id);
        $stmt_update->execute();

        $message = "<div class='alert alert-success text-center'>Password updated successfully. <a href='login.php'>Login</a></div>";
    } else {
        $message = "<div class='alert alert-danger text-center'>Invalid or expired reset link.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?= $message ?>
    
    <?php if (empty($message) || str_contains($message, 'expired')): ?>
    <form method="post" class="card p-4 shadow-sm border-0">
        <h4 class="mb-3">Reset Your Password</h4>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success w-100">Reset Password</button>
    </form>
    <?php endif; ?>
</body>
</html>
