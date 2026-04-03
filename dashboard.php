<?php
session_start();

// Security Check: If no user_id in session, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tawi Digital | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f4f7f6; }
        .sidebar { width: 280px; background: #1a3c34; color: white; padding: 20px; }
        .main-content { flex-grow: 1; padding: 40px; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 10px; display: block; text-decoration: none; padding: 10px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="fw-bold mb-4"><i class="bi bi-tree-fill me-2"></i>TAWI DIGITAL</h4>
        <nav>
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-grid-1x2-fill me-2"></i> Overview</a>
            <a href="user_management.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Users</a>
            <a href="data_reports.php" class="nav-link"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Reports</a>
            <hr>
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Welcome back, <?php echo $_SESSION['username']; ?>!</h2>
            <span class="badge bg-success p-2">System Active</span>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="text-muted">Total Seedlings</h6>
                    <h3>1,240</h3>
                </div>
            </div>
        </div>
    </div>

</body>
</html>