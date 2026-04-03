<?php
session_start();

/**
 * DATABASE CONNECTION
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887"; 
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { 
    die("Database connection error."); 
}

/**
 * HANDLE FORM SUBMISSION (Add New Region)
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_region'])) {
    $name = $_POST['region_name'];
    $desc = $_POST['description'];

    $insert_sql = "INSERT INTO regions (region_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_sql);
    if ($stmt->execute([$name, $desc])) {
        header("Location: field_regions.php?success=1");
        exit();
    }
}

/**
 * FETCH REGIONS
 */
try {
    $sql = "SELECT region_name, description FROM regions";
    $stmt = $conn->query($sql);
    $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $regions = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Admin | Field Regions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --forest: #1b4332; --emerald: #2d6a4f; --bg: #f8f9fa; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; margin: 0; }
        .sidebar { width: 280px; height: 100vh; background: var(--forest); color: white; padding: 30px; position: fixed; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 280px); }
        .region-card { background: white; border-radius: 20px; padding: 25px; border: none; transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(0,0,0,0.05); text-align: center; }
        .region-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .icon-box { width: 65px; height: 65px; background: #d8f3dc; color: #2d6a4f; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; }
        .nav-link { transition: all 0.3s; border-radius: 10px; margin-bottom: 5px; text-decoration: none; display: block; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .active-link { background: var(--emerald) !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="mb-5">
        <h2 class="fw-bold m-0 text-white">TAWI</h2>
        <small class="text-success fw-bold text-uppercase">Admin Panel</small>
    </div>
    <div class="nav flex-column">
        <a href="admin_dashboard.php" class="nav-link text-white opacity-75 py-3"><i class="fas fa-th-large me-2"></i> Overview</a>
        <a href="user_management.php" class="nav-link text-white opacity-75 py-3"><i class="fas fa-users me-2"></i> User Management</a>
        <a href="field_regions.php" class="nav-link text-white fw-bold py-3 active-link"><i class="fas fa-map-marked-alt me-2"></i> Field Regions</a>
        <a href="logout.php" class="text-danger mt-auto pt-5 text-decoration-none fw-bold"><i class="fas fa-sign-out-alt me-2"></i> Exit System</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold m-0">Field Regions</h2>
            <p class="text-muted">Monitoring active reforestation zones</p>
        </div>
        <button class="btn btn-success rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addRegionModal">
            <i class="fas fa-plus me-2"></i>Add New Region
        </button>
    </div>

    <div class="row g-4">
        <?php if (empty($regions)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No regions found. Click "Add New Region" to begin.</p>
            </div>
        <?php else: ?>
            <?php foreach($regions as $r): ?>
            <div class="col-md-6 col-lg-4">
                <div class="region-card">
                    <div class="icon-box"><i class="fas fa-seedling"></i></div>
                    <h4 class="fw-bold text-capitalize m-0"><?php echo htmlspecialchars($r['region_name']); ?></h4>
                    <hr class="my-3 opacity-25">
                    <p class="text-muted small"><?php echo htmlspecialchars($r['description']); ?></p>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3">View Details</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="addRegionModal" tabindex="-1" aria-labelledby="addRegionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="addRegionModalLabel">Register New Region</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="field_regions.php" method="POST">
        <div class="modal-body py-4">
            <div class="mb-3">
                <label class="form-label small fw-bold">Region Name</label>
                <input type="text" name="region_name" class="form-control rounded-pill px-3" placeholder="e.g. Mberee North" required>
            </div>
            <div class="mb-0">
                <label class="form-label small fw-bold">Soil Description</label>
                <textarea name="description" class="form-control" rows="3" style="border-radius: 15px;" placeholder="Describe soil type and topography..." required></textarea>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_region" class="btn btn-success rounded-pill px-4">Save Region</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>