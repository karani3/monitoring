<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * DATABASE CONFIGURATION
 * Information pulled from your InfinityFree MySQL Connection Details
 */
$host     = "sql100.infinityfree.com"; 
$db_user  = "if0_41483887";         
$db_pass  = "Karani2007";           
$db_name  = "if0_41483887_db_tawicity"; 

try {
    // Establishing connection with UTF-8 to prevent character issues
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { 
    // In production, we keep the error message simple
    die("Connection failed: Please check your database settings."); 
}

// Fetch dynamic counts (using fetchColumn for efficiency)
$reg_count = $conn->query("SELECT COUNT(*) FROM regions")->fetchColumn();
$user_count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Get current admin name (fallback to 'Administrator' if not set)
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Administrator';
$current_date = date('l, F j, Y'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Admin | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; margin: 0; font-family: 'Segoe UI', sans-serif; }
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 280px); min-height: 100vh; }
        
        /* Sidebar layout adjustments */
        @media (max-width: 992px) {
            .main-content { margin-left: 0; width: 100%; padding: 20px; }
        }

        .welcome-section { margin-bottom: 40px; }
        .welcome-text h2 { font-weight: 800; color: #1b4332; margin-bottom: 5px; }
        .date-badge { background: #e8f5e9; color: #2d6a4f; padding: 8px 15px; border-radius: 10px; font-weight: 600; font-size: 0.9rem; height: fit-content; }
        
        .stat-card { 
            background: white; 
            border-radius: 20px; 
            padding: 30px; 
            border: none; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.03); 
            transition: 0.3s; 
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .letter-spacing-1 { letter-spacing: 1px; }
    </style>
</head>
<body>

<?php 
// Ensure sidebar.php is lowercase in your File Manager
if(file_exists('sidebar.php')) {
    include('sidebar.php'); 
}
?>

<div class="main-content">
    <div class="welcome-section d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div class="welcome-text">
            <h2>Welcome back, <?php echo htmlspecialchars($admin_name); ?>! 👋</h2>
            <p class="text-muted">Here is what is happening with Project TAWI today.</p>
        </div>
        <div class="date-badge">
            <i class="far fa-calendar-alt me-2"></i><?php echo $current_date; ?>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-4 rounded-4 me-4 text-success">
                    <i class="fas fa-map-marked-alt fa-2x"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold letter-spacing-1">Total Regions</small>
                    <h2 class="fw-bold m-0"><?php echo number_format($reg_count); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-4 rounded-4 me-4 text-primary">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold letter-spacing-1">Active Users</small>
                    <h2 class="fw-bold m-0"><?php echo number_format($user_count); ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5 p-4 bg-white rounded-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-success">Quick Data Summary</h4>
            <a href="data_reports.php" class="btn btn-success btn-sm rounded-pill px-3">View Full Report</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>CATEGORY</th>
                        <th>TOTAL COUNT</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold py-3">Field Regions</td>
                        <td><?php echo $reg_count; ?></td>
                        <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Updated</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3">System Users</td>
                        <td><?php echo $user_count; ?></td>
                        <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>