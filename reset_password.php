<?php
$conn = new mysqli("localhost", "root", "", "tawi system");

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Verify token exists and is not expired
    $res = $conn->query("SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW()");
    $user = $res->fetch_assoc();

    if (!$user) { die("Invalid or expired token."); }
}

if (isset($_POST['update_password'])) {
    $new_pass = $_POST['password']; // For now, storing as plain text to match your current login
    $token = $_POST['token'];
    
    $sql = "UPDATE users SET password = '$new_pass', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
    if ($conn->query($sql)) {
        echo "<script>alert('Password updated! You can now login.'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Password | Tawi System</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 15px; width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { background: #1b4332; color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Create New Password</h3>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit" name="update_password">Update Password</button>
        </form>
    </div>
</body>
</html>