<?php
session_start();

/**
 * 1. SECURITY & DATABASE CONNECTION
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: login.php");
    exit();
}

$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887"; 
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $officer_id = $_SESSION['user_id'];

    /**
     * 2. FETCH DYNAMIC METRICS
     */
    // This counts ONLY trees registered by the logged-in officer
    $stmt1 = $conn->prepare("SELECT COUNT(*) FROM seedlings WHERE officer_id = :oid");
    $stmt1->execute(['oid' => $officer_id]);
    $tree_count = $stmt1->fetchColumn();

    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM reports WHERE officer_id = :oid");
    $stmt2->execute(['oid' => $officer_id]);
    $report_count = $stmt2->fetchColumn();

    /**
     * 3. FETCH RECENT SEEDLINGS
     */
    $s_query = "SELECT s.id, s.tree_type, r.region_name, s.status 
                FROM seedlings s 
                LEFT JOIN regions r ON s.region_id = r.id 
                WHERE s.officer_id = :oid 
                ORDER BY s.id DESC LIMIT 5";
    $s_stmt = $conn->prepare($s_query);
    $s_stmt->execute(['oid' => $officer_id]);
    $seedlings = $s_stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
     * 4. FETCH RECENT INCIDENTS
     */
    $r_query = "SELECT r.id, s.tree_type, r.issue_type, r.description 
                FROM reports r 
                LEFT JOIN seedlings s ON r.seedling_id = s.id 
                WHERE r.officer_id = :oid 
                ORDER BY r.id DESC LIMIT 5";
    $r_stmt = $conn->prepare($r_query);
    $r_stmt->execute(['oid' => $officer_id]);
    $reports = $r_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) { 
    $error = "System Error: " . $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Ops | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --tawi-forest: #1b4332; --tawi-emerald: #2d6a4f; }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; display: flex; margin: 0; }
        .sidebar { width: 280px; height: 100vh; background: var(--tawi-forest); position: fixed; color: white; padding: 30px 20px; display: flex; flex-direction: column; }
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        .nav-link { color: #b7e4c7; padding: 12px 15px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; margin-bottom: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: var(--tawi-emerald); color: white; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; border: none; }
        .data-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none; }
        .badge-status { font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="mb-5">
        <h4 class="fw-bold m-0 text-white">TAWI <span class="text-success">DIGITAL</span></h4>
        <small class="opacity-50 text-uppercase">Field Officer</small>
    </div>
    <nav>
        <a href="officer_dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="register_seedling.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> New Seedling</a>
        <a href="file_incident.php" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i> Report Incident</a>
    </nav>
    <div class="mt-auto">
        <a href="logout.php" class="text-danger text-decoration-none small d-flex align-items-center justify-content-center p-3 rounded-3 bg-white bg-opacity-10">
            <i class="bi bi-box-arrow-left me-2"></i> Exit System
        </a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Field Activity</h2>
        <div class="text-muted small"><?php echo date('D, d M Y'); ?></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="bg-success-subtle p-3 rounded-4 me-3"><i class="bi bi-tree-fill text-success fs-3"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?php echo $tree_count; ?></h3>
                    <p class="text-muted small text-uppercase mb-0">Trees Registered</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="bg-warning-subtle p-3 rounded-4 me-3"><i class="bi bi-file-earmark-text text-warning fs-3"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?php echo $report_count; ?></h3>
                    <p class="text-muted small text-uppercase mb-0">Reports Filed</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="data-card h-100">
                <h5 class="fw-bold mb-4">Recent Entries</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Species</th><th>Region</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if(empty($seedlings)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">No trees found.</td></tr>
                            <?php else: ?>
                                <?php foreach($seedlings as $s): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($s['tree_type']); ?></td>
                                    <td><i class="bi bi-geo-alt text-success me-1"></i><?php echo htmlspecialchars($s['region_name'] ?? 'General'); ?></td>
                                    <td><span class="badge bg-success-subtle text-success badge-status"><?php echo htmlspecialchars($s['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="data-card h-100">
                <h5 class="fw-bold mb-4">Incident Log</h5>
                <?php if(empty($reports)): ?>
                    <p class="text-center py-4 text-muted">No recent incidents.</p>
                <?php else: ?>
                    <?php foreach($reports as $r): ?>
                    <div class="mb-3 p-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small"><?php echo htmlspecialchars($r['tree_type'] ?? 'General'); ?></span>
                            <span class="badge bg-danger-subtle text-danger" style="font-size: 0.6rem;"><?php echo htmlspecialchars($r['issue_type']); ?></span>
                        </div>
                        <p class="text-muted small m-0 mt-1"><?php echo htmlspecialchars($r['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>