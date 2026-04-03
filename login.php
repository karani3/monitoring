<?php
session_start();

/**
 * DATABASE CONFIGURATION
 * Updated for InfinityFree Hosting
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887";         
$db_pass = "Karani2007";           
$db_name = "if0_41483887_db_tawicity"; 

try {
    // Establishing connection with UTF-8 support
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { 
    // If it fails, we show the error. Once live, you might want to change this to a generic message.
    die("Connection failed: " . $e->getMessage()); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    // Check for both username or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$user_input, $user_input]);
    $user = $stmt->fetch();

    // Verify password against the hash in the database
    if ($user && password_verify($pass_input, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the role (e.g., admin_dashboard.php or user_dashboard.php)
        header("Location: " . $user['role'] . "_dashboard.php");
        exit();
    } else {
        $error = "Invalid username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Access | Tawi System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            /* Fallback color if image fails to load */
            background: #1b4332;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e') center/cover; 
            margin: 0; 
        }
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            width: 90%;
            max-width: 380px; 
            text-align: center; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
        }
        .header-box { 
            background: #1b4332; 
            color: white; 
            margin: -40px -40px 30px -40px; 
            padding: 30px; 
            border-radius: 20px 20px 0 0; 
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
        }
        .btn-login { 
            background: #2d6a4f; 
            color: white; 
            border: none; 
            width: 100%; 
            padding: 15px; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 10px;
        }
        .btn-login:hover { background: #1b4332; transform: translateY(-1px); }
        .footer-text { margin-top: 25px; font-size: 14px; color: #666; }
        .footer-text a { color: #2d6a4f; text-decoration: none; font-weight: bold; }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header-box">
        <i class="fas fa-shield-alt" style="font-size: 30px;"></i>
        <h2 style="margin: 10px 0 0 0;">Portal Access</h2>
    </div>

    <?php if(isset($error)): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn-login">Sign In</button>
    </form>

    <a href="forgot_password.php" style="display:block; margin-top:15px; color:#2d6a4f; font-size:13px; text-decoration:none;">Forgot Password?</a>
    
    <div class="footer-text">
        Don't have an account? <a href="signup.php">Create one</a>
    </div>
</div>

</body>
</html>