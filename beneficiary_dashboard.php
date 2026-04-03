<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // UPDATED QUERY: Joins users with the separate wallet table
    $stmt = $conn->prepare("
        SELECT u.name, COALESCE(w.balance, 0) as wallet_balance 
        FROM users u 
        LEFT JOIN wallet w ON u.id = w.user_id 
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $user_name = !empty($user['name']) ? $user['name'] : "Partner";
    // We use the joined balance here
    $display_balance = $user['wallet_balance'];

    // Fetch Tree Stats
    $treeStmt = $conn->prepare("SELECT COUNT(*) as total, AVG(status) as avg_growth FROM seedlings WHERE owner_id = ?");
    $treeStmt->execute([$user_id]);
    $stats = $treeStmt->fetch();
    
    $total_trees = $stats['total'] ?? 0;
    $avg_growth = round($stats['avg_growth'] ?? 0);
    $co2_offset = number_format($total_trees * 0.05, 2); 

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("A system error occurred. Details: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Tawi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --forest: #1b4332; --mint: #2d6a4f; --glass: rgba(255, 255, 255, 0.95); }
        body { background-color: #f4f7f5; font-family: 'Inter', sans-serif; color: #2d3436; }
        .sidebar { background: var(--forest); min-height: 100vh; padding: 40px 20px; color: white; position: fixed; width: 260px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.6); padding: 12px 20px; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .glass-card { background: var(--glass); border-radius: 25px; border: 1px solid rgba(255,255,255,0.3); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); height: 100%; transition: 0.3s; }
        .icon-box { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 20px; }
        .bg-soft-green { background: #d8f3dc; color: #1b4332; }
        .btn-tawi { background: var(--forest); color: white; border-radius: 12px; padding: 12px; font-weight: 600; border: none; width: 100%; }
        @media (max-width: 768px) { .sidebar { width: 100%; min-height: auto; position: relative; } .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-auto">
            <div class="sidebar">
                <h2 class="fw-bold mb-5 px-3">TAWI</h2>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#"><i class="bi bi-grid-1x2-fill me-2"></i> Overview</a>
                    <a class="nav-link" href="buy_seedlings.php"><i class="bi bi-shop me-2"></i> Marketplace</a>
                    <a class="nav-link" href="my_portfolio.php"><i class="bi bi-tree-fill me-2"></i> My Forest</a>
                    <hr class="my-4 opacity-25">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
                </nav>
            </div>
        </div>

        <div class="col main-content">
            <header class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-start">
                <div>
                    <h1 class="fw-bold mb-1">Habari, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="text-muted"><i class="bi bi-calendar3 me-2"></i> <span id="current-date"></span></p>
                </div>
                <div class="glass-card py-2 px-4 d-flex align-items-center shadow-sm" style="height: auto; border-radius: 18px;">
                    <div class="text-end me-3">
                        <small class="text-muted d-block">Wallet Balance</small>
                        <span class="fw-bold text-success fs-5">KES <?php echo number_format($display_balance, 2); ?></span>
                    </div>
                    <div class="bg-soft-green p-2 rounded-3"><i class="bi bi-wallet2 fs-4"></i></div>
                </div>
            </header>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="icon-box bg-soft-green"><i class="bi bi-lightning-charge"></i></div>
                        <h4 class="fw-bold">Quick Recharge</h4>
                        <p class="text-muted small">Top up your digital wallet via M-Pesa.</p>
                        <form action="recharge_handler.php" method="POST">
                            <div class="input-group mb-3 border rounded-3 overflow-hidden">
                                <span class="input-group-text border-0 bg-white text-muted">KES</span>
                                <input type="number" name="amount" class="form-control border-0" placeholder="Amount" required>
                            </div>
                            <button class="btn btn-tawi">Top Up Wallet</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card" style="border-top: 5px solid var(--forest);">
                        <div class="icon-box bg-soft-green"><i class="bi bi-tree"></i></div>
                        <h1 class="fw-bold display-5 mb-0"><?php echo $total_trees; ?></h1>
                        <p class="text-muted">Trees in Your Forest</p>
                        <div class="progress mt-4" style="height: 7px; border-radius: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $avg_growth; ?>%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Growth: <?php echo $avg_growth; ?>%</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card bg-success text-white">
                        <div class="icon-box bg-white text-success"><i class="bi bi-cloud-check"></i></div>
                        <h1 class="fw-bold display-5 mb-0"><?php echo $co2_offset; ?>t</h1>
                        <p class="opacity-75">Estimated CO2 Offset</p>
                        <hr class="my-4 opacity-25">
                        <p class="small mb-0">Helping restore <?php echo ($total_trees * 0.1); ?> hectares!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateDateTime() {
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    }
    updateDateTime();
</script>
</body>
</html>