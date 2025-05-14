<?php
// Step 1: Connect to the DB
require_once 'connection.php';

// Step 2: Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Step 3: Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Step 4: Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success text-center'>Registration successful. <a href='login.php'>Login here</a></div>";
    } else {
        $message = "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #f8fdff, #e0f7fa);
            font-family: 'Poppins', sans-serif;
        }
        .register-container {
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

<div class="container register-container">
    <?php if (!empty($message)) echo $message; ?>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center text-primary mb-4">Create Your Account</h3>

            <form action="register.php" method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>

            <div class="text-center mt-3">
                Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
