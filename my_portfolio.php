<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch all seedlings owned by this user
    // We select only the columns we are 100% sure exist: id, tree_type, status
    $stmt = $conn->prepare("SELECT id, tree_type, status FROM seedlings WHERE owner_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $my_trees = $stmt->fetchAll();
    
    $total_count = count($my_trees);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Error loading your forest.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio | Tawi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --forest: #1b4332; --mint: #2d6a4f; }
        body { background-color: #f4f7f5; font-family: 'Inter', sans-serif; }
        .sidebar { background: var(--forest); min-height: 100vh; padding: 40px 20px; color: white; position: fixed; width: 260px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .tree-card { background: white; border-radius: 20px; border: none; padding: 20px; transition: 0.3s; }
        .progress { height: 8px; border-radius: 10px; background: #e9ecef; }
        @media (max-width: 768px) { .sidebar { width: 100%; min-height: auto; position: relative; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-auto">
            <div class="sidebar">
                <h2 class="fw-bold mb-5 px-3">TAWI</h2>
                <nav class="nav flex-column">
                    <a class="nav-link text-white-50" href="beneficiary_dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i> Overview</a>
                    <a class="nav-link text-white-50" href="buy_seedlings.php"><i class="bi bi-shop me-2"></i> Marketplace</a>
                    <a class="nav-link active text-white" href="#"><i class="bi bi-tree-fill me-2"></i> My Forest</a>
                    <hr class="my-4 opacity-25">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
                </nav>
            </div>
        </div>

        <div class="col main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">My Reforestation Impact</h2>
                    <p class="text-muted">Tracking the growth and CO2 contribution of your trees.</p>
                </div>
                <span class="badge bg-success px-3 py-2 rounded-pill">Total Trees: <?php echo $total_count; ?></span>
            </div>

            <?php if ($total_count > 0): ?>
                <div class="row g-4">
                    <?php foreach ($my_trees as $tree): ?>
                        <div class="col-md-4">
                            <div class="tree-card shadow-sm border-start border-success border-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-light p-3 rounded-3 me-3">
                                        <i class="bi bi-tree text-success fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($tree['tree_type']); ?></h6>
                                        <small class="text-muted">ID: #TW-<?php echo $tree['id']; ?></small>
                                    </div>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <small class="fw-bold">Growth Progress</small>
                                    <small class="text-success"><?php echo $tree['status']; ?>%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: <?php echo $tree['status']; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 mt-5">
                    <i class="bi bi-tree text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                    <h4 class="mt-4 text-muted">Your forest is empty.</h4>
                    <p class="text-muted">Visit the Marketplace to plant your first tree!</p>
                    <a href="buy_seedlings.php" class="btn btn-success px-4 py-2 mt-2" style="border-radius: 12px; background: #1b4332;">Browse Seedlings</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>