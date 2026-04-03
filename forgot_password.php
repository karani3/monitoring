<?php
session_start();
require 'config.php'; // Ensure database connection

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            // Update password directly (using password_hash for security)
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($update->execute([$hashed_password, $email])) {
                $message = "Password reset successfully! <a href='login.php'>Login here</a>";
            }
        } else {
            $error = "Email address not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Access | Tawi System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1b4332; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; }
        .reset-card { background: white; padding: 40px; border-radius: 25px; width: 100%; max-width: 450px; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        .btn-tawi { background-color: #2d6a4f; color: white; border-radius: 12px; padding: 12px; width: 100%; border: none; font-weight: 600; margin-top: 20px; }
        .btn-tawi:hover { background-color: #1b4332; color: white; }
        .form-control { border-radius: 10px; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="reset-card">
    <h2 class="fw-bold mb-3">Reset Access</h2>
    <p class="text-muted mb-4">Enter your details below to update your password immediately.</p>

    <?php if($message) echo "<div class='alert alert-success small'>$message</div>"; ?>
    <?php if($error) echo "<div class='alert alert-danger small'>$error</div>"; ?>

    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        <input type="password" name="password" class="form-control" placeholder="New Password" required>
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
        
        <button type="submit" class="btn-tawi">Update Password</button>
    </form>

    <div class="mt-4">
        <a href="login.php" class="text-decoration-none text-muted small">Back to Login</a>
    </div>
</div>

</body>
</html>