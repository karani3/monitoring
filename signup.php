<?php
session_start();

/**
 * DATABASE CONFIGURATION
 * Updated for InfinityFree Hosting
 */
$host     = "sql100.infinityfree.com"; 
$db_user  = "if0_41483887";         
$db_pass  = "Karani2007";           
$db_name  = "if0_41483887_db_tawicity"; 
$message  = "";
$msg_type = ""; 

try {
    // Establishing connection with UTF-8 support
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Using a more user-friendly error for the front-end
    die("Database connection failed. Please try again later.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $role      = $_POST['role']; 
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    // Validation: Check if passwords match
    if ($password !== $confirm) {
        $message = "Passwords do not match!";
        $msg_type = "danger";
    } 
    // Validation: Check password length
    elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $msg_type = "danger";
    }
    else {
        try {
            // Check if username or email already exists
            $check = $conn->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            
            if ($check->rowCount() > 0) {
                $message = "Username or Email already registered!";
                $msg_type = "danger";
            } else {
                // Hashing password using PASSWORD_DEFAULT (standard for PHP login systems)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (full_name, username, email, role, password_hash) 
                        VALUES (:full_name, :username, :email, :role, :password_hash)";
                
                $stmt = $conn->prepare($sql);
                $success = $stmt->execute([
                    ':full_name'     => $full_name,
                    ':username'      => $username,
                    ':email'         => $email,
                    ':role'          => $role,
                    ':password_hash' => $hashed_password
                ]);

                if ($success) {
                    // Redirect to login page with a success flag
                    header("Location: login.php?signup=success");
                    exit();
                }
            }
        } catch(PDOException $e) {
            $message = "Registration Error: " . $e->getMessage();
            $msg_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Digital | Join Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --forest: #1b4332; --emerald: #2d6a4f; }
        body { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1600&q=80'); 
            background-size: cover; 
            background-attachment: fixed; 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            margin: 0;
        }
        .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 24px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .sidebar-brand { background: var(--forest); color: white; padding: 40px; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; }
        .btn-signup { background: var(--emerald); color: white; border-radius: 12px; padding: 12px; font-weight: 600; border: none; width: 100%; transition: 0.3s; }
        .btn-signup:hover { background: var(--forest); transform: translateY(-2px); }
        input, select { border-radius: 10px !important; background: #f8fafc !important; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <nav class="col-md-4 d-none d-md-flex sidebar-brand">
            <div class="text-center w-100">
                <i class="bi bi-tree fs-1 text-success mb-3"></i>
                <h1 class="fw-bold">TAWI DIGITAL</h1>
                <p class="opacity-75">Secure reforestation management.</p>
            </div>
        </nav>
        
        <main class="col-md-8 d-flex align-items-center justify-content-center p-5">
            <div class="glass-card w-100" style="max-width: 600px;">
                <h2 class="fw-bold mb-4">Create Account</h2>
                
                <?php if($message): ?>
                    <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="small fw-bold mb-1">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Livingstone Karani" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="karani2026" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="info@tawidigital.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Designated Role</label>
                        <select name="role" class="form-select" required>
                            <option value="" selected disabled>Select your role...</option>
                            <option value="beneficiary">Beneficiary</option>
                            <option value="officer">Field Officer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="small fw-bold mb-1">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-signup">Create Account</button>
                </form>

                <p class="mt-4 text-center small">
                    Already have an account? <a href="login.php" class="text-success fw-bold text-decoration-none">Log In</a>
                </p>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>