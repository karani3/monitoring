<?php
session_start();
// Use your central config file for consistency
require 'config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// FETCH ALL TREES FOR THIS BENEFICIARY from the 'trees' table
try {
    /**
     * Updated Query:
     * 1. Target the 'trees' table.
     * 2. Use 'owner_id' to match the logged-in user.
     * 3. Use 'tree_name' and 'date_planted' to match your table schema.
     */
    $query = "SELECT id, tree_name, status, date_planted, purchase_price 
              FROM trees 
              WHERE owner_id = :uid 
              ORDER BY date_planted DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute(['uid' => $user_id]);
    $plantings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Error fetching plantings: " . $e->getMessage();
    $plantings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plantings | Tawi Impact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --tawi-dark: #1b4332; }
        body { background: #f8fafc; display: flex; margin: 0; }
        .sidebar { width: 280px; height: 100vh; background: var(--tawi-dark); position: fixed; color: white; padding: 40px 20px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.6); transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .main-content { margin-left: 280px; width: 100%; padding: 40px; }
        .table-card { background: white; border-radius: 20px; padding: 30px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .status-badge { padding: 6px 16px; border-radius: 50px; font-weight: bold; font-size: 0.75rem; }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <h2 class="fw-bold px-3">TAWI</h2>
    <p class="text-success fw-bold small mb-5 px-3">IMPACT</p>
    <nav class="nav flex-column">
        <a href="beneficiary_dashboard.php" class="nav-link mb-3"><i class="bi bi-grid me-2"></i> Overview</a>
        <a href="buy_seedlings.php" class="nav-link mb-3"><i class="bi bi-shop me-2"></i> Marketplace</a>
        <a href="my_portfolio.php" class="nav-link active rounded-3 mb-3"><i class="bi bi-tree-fill me-2"></i> My Forest</a>
        <hr class="opacity-25">
        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-power me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <h2 class="fw-bold mb-4 text-dark">My Reforestation Progress</h2>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger rounded-4"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="table-card">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small text-uppercase">
                    <th>Tree ID</th>
                    <th>Species</th>
                    <th>Investment</th>
                    <th>Date Planted</th>
                    <th>Growth Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plantings)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-tree fs-1 d-block mb-3 opacity-25"></i>
                            No trees recorded yet.
                        </td>
                    </tr>
                <?php else: foreach ($plantings as $tree): ?>
                    <tr>
                        <td class="text-muted small">#<?php echo $tree['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                                    <i class="bi bi-tree"></i>
                                </div>
                                <span class="fw-bold"><?php echo htmlspecialchars($tree['tree_name']); ?></span>
                            </div>
                        </td>
                        <td class="text-dark">KES <?php echo number_format($tree['purchase_price'], 2); ?></td>
                        <td class="text-muted"><?php echo date('M d, Y', strtotime($tree['date_planted'])); ?></td>
                        <td>
                            <?php 
                                // Logic: If status is 100, it's 'MATURE'. Otherwise, it's 'GROWING'.
                                $is_mature = ($tree['status'] >= 100);
                                $badge_class = $is_mature ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary';
                                $status_text = $is_mature ? 'MATURE' : 'GROWING (' . $tree['status'] . '%)';
                            ?>
                            <span class="status-badge <?php echo $badge_class; ?>">
                                <i class="bi <?php echo $is_mature ? 'bi-check-circle-fill' : 'bi-clock-history'; ?> me-1"></i> 
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>