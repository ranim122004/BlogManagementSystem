<?php
session_start();
require_once 'connection.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user with admin and active status
    $stmt = $conn->prepare("SELECT id, password, is_admin, is_active FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $is_admin, $is_active);
        $stmt->fetch();

        if (!password_verify($password, $hashed_password)) {
            $message = "<div class='alert alert-danger text-center'>Invalid password.</div>";
        } elseif ($is_active != 1) {
            $message = "<div class='alert alert-warning text-center'>Your account is deactivated. Please contact support.</div>";
        } else {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $is_admin;

            if ($is_admin) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>No user found with that username.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f9f9f9, #e0f7fa);
            font-family: 'Poppins', sans-serif;
        }
        .login-container {
            max-width: 450px;
            margin: 90px auto;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <?php if (!empty($message)) echo $message; ?>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center text-primary mb-4">Login to Your Account</h3>

            <form action="login.php" method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>

                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="text-decoration-none">Forgot your password?</a>
                </div>
            </form>

            <div class="text-center mt-3">
                Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
