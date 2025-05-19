<?php
require_once 'connection.php';
require_once 'config_email.php';     
require_once 'vendor/autoload.php';  

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Step 1: Validate user exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $username = $user['username'];

        // Step 2: Generate secure token and expiry
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Step 3: Store token in DB
        $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $token, $expiry, $user_id);
        $stmt_update->execute();

        // Step 4: Prepare email with reset link
        $resetLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/blog_project/reset_password.php?token=" . urlencode($token);

        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = GMAIL_USER;
            $mail->Password = GMAIL_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email content
            $mail->setFrom(GMAIL_USER, 'Blog App');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = <<<EOT
Hi $username,

We received a request to reset your password.

Click the link below to set a new password:
$resetLink

This link is valid for 1 hour. If you did not request this, simply ignore this message.

Best regards,
Blog App Team
EOT;

            $mail->send();
            $message = "<div class='alert alert-success text-center'>A password reset link has been sent to your email address.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger text-center'>Email sending failed: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>No user found with that email address.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?= $message ?>
    <form method="post" class="card p-4 shadow-sm border-0">
        <h4 class="mb-3">Forgot Password</h4>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
</body>
</html>
