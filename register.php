<?php
// Step 1: Connect to the database
require_once 'connection.php';

// Step 2: Enable strict error mode to catch exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Step 3: Initialize message variable
$message = '';

// Step 4: Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 5: Sanitize and receive input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Step 6: Define password strength pattern
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    // Step 7: Validate password strength
    if (!preg_match($passwordPattern, $password)) {
        $message = "<div class='alert alert-warning text-center'>
            Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character.
        </div>";
    } else {
        // Step 8: Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Step 9: Prepare and execute insert statement
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);
            $stmt->execute();

            $message = "<div class='alert alert-success text-center'>
                Registration successful. <a href='login.php'>Login here</a>
            </div>";
            $stmt->close();

        } catch (mysqli_sql_exception $e) {
            // Step 10: Handle duplicate username error (code 1062)
            if ($e->getCode() === 1062) {
                $message = "<div class='alert alert-danger text-center'>
                    Username already exists. Please choose another one.
                </div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>
                    Error: " . htmlspecialchars($e->getMessage()) . "
                </div>";
            }
        }
    }
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

<!-- Step 11: Display the form and messages -->
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
