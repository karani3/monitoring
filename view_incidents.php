<?php
session_start();

// 1. SECURITY: Only Admins can view the incident log
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. DATABASE CONFIGURATION
$host = "localhost";
$db_name = "tawi system";
$db_user = "root";
$db_pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. FETCH INCIDENTS WITH OFFICER & SEEDLING DETAILS
    $query = "SELECT 
                r.id, 
                r.issue_type, 
                r.description, 
                u.full_name as officer_name,
                s.tree_type
              FROM reports r
              JOIN users u ON r.officer_id = u.id
              JOIN seedlings s ON r.seedling_id = s.id
              ORDER BY r.id DESC";
              
    $stmt = $conn->query($query);
    $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tawi Admin | Incident Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --tawi-forest: #1b4332; --tawi-emerald: #2d6a4f; --bg-pearl: #f8fafc; }
        body { background-color: var(--bg-pearl); font-family: 'Inter', sans-serif; display: flex; margin: 0; }
        
        .sidebar { width: 280px; height: 100vh; background: var(--tawi-forest); position: fixed; color: white; padding: 30px 20px; display: flex; flex-direction: column; }
        .nav-link { color: #b7e4c7; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; text-decoration: none; display: flex; align-items: center; transition: 0.3s; }
        .nav-link.active { background: var(--tawi-emerald); color: white; }
        
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        .incident-card { background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
        .badge-pest { background: #fee2e2; color: #dc2626; }
        .badge-drought { background: #fef3c7; color: #d97706; }
        .exit-btn { margin-top: auto; color: #ff8a8a; background: rgba(255,255,255,0.05); border-radius: 12px; padding: 12px; text-decoration: none; text-align: center; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="mb-5 pb-3 border-bottom border-white border-opacity-10">
        <h3 class="fw-bold mb-0">TAWI ADMIN</h3>
    </div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Overview</a>
        <a href="user_management.php" class="nav-link"><i class="bi bi-people me-2"></i> User Management</a>
        <a href="field_regions.php" class="nav-link"><i class="bi bi-map me-2"></i> Field Regions</a>
        <a href="view_incidents.php" class="nav-link active"><i class="bi bi-exclamation-octagon me-2"></i> Incident Reports</a>
        <a href="data_reports.php" class="nav-link"><i class="bi bi-file-earmark-bar-graph me-2"></i> Data Reports</a>
    </nav>
    <a href="logout.php" class="exit-btn"><i class="bi bi-box-arrow-left me-2"></i> Exit System</a>
</div>

<div class="main-content">
    <div class="mb-5">
        <h2 class="fw-bold text-dark">Field Incident Log</h2>
        <p class="text-muted">Monitoring tree health issues reported by officers.</p>
    </div>

    <div class="incident-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Issue</th>
                        <th class="py-3">Target Tree</th>
                        <th class="py-3">Reported By</th>
                        <th class="py-3">Description</th>
                        <th class="py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($incidents)): ?>
                        <?php foreach($incidents as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge rounded-pill px-3 <?php echo ($row['issue_type'] == 'Pest Infestation') ? 'badge-pest' : 'badge-drought'; ?>">
                                    <?php echo htmlspecialchars($row['issue_type']); ?>
                                </span>
                            </td>
                            <td><span class="fw-bold">#<?php echo $row['id']; ?></span> (<?php echo htmlspecialchars($row['tree_type']); ?>)</td>
                            <td><i class="bi bi-person-badge me-1"></i> <?php echo htmlspecialchars($row['officer_name']); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-pill">Resolve</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-shield-check text-success display-4"></i>
                                <p class="text-muted mt-2">All clear. No incidents reported from the field.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>