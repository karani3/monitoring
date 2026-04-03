<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle Purchase Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_id'])) {
    $tree_id = $_POST['buy_id'];
    $price = floatval($_POST['price']);

    try {
        $conn->beginTransaction();

        // 1. Check Balance in the WALLET table (matches your DB structure)
        $stmt = $conn->prepare("SELECT balance FROM wallet WHERE user_id = ? FOR UPDATE");
        $stmt->execute([$user_id]);
        $wallet = $stmt->fetch();

        if ($wallet && $wallet['balance'] >= $price) {
            // 2. Deduct Money from the WALLET table
            $updateWallet = $conn->prepare("UPDATE wallet SET balance = balance - ? WHERE user_id = ?");
            $updateWallet->execute([$price, $user_id]);

            // 3. Add to User's Forest
            // We fetch the name from inventory and set initial status to 10%
            $addTree = $conn->prepare("INSERT INTO seedlings (owner_id, tree_type, status) VALUES (?, (SELECT name FROM inventory WHERE id = ? LIMIT 1), 10)");
            $addTree->execute([$user_id, $tree_id]);

            $conn->commit();
            $message = "<div class='alert alert-success border-0 shadow-sm'>Purchase successful! KES " . number_format($price, 2) . " deducted. Check 'My Forest'.</div>";
        } else {
            $conn->rollBack();
            $message = "<div class='alert alert-danger border-0 shadow-sm'>Insufficient wallet balance. Please top up first.</div>";
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $message = "<div class='alert alert-danger border-0 shadow-sm'>Transaction failed: " . $e->getMessage() . "</div>";
    }
}

// Fetch available trees from inventory
try {
    $inventory = $conn->query("SELECT * FROM inventory WHERE stock > 0")->fetchAll();
} catch (PDOException $e) {
    die("Inventory Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace | Tawi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --forest: #1b4332; --mint: #2d6a4f; }
        body { background-color: #f4f7f5; font-family: 'Inter', sans-serif; }
        .sidebar { background: var(--forest); min-height: 100vh; padding: 40px 20px; color: white; position: fixed; width: 260px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .tree-card { background: white; border-radius: 20px; border: none; transition: 0.3s; overflow: hidden; }
        .tree-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .btn-buy { background: var(--forest); color: white; border-radius: 10px; width: 100%; font-weight: 600; border: none; padding: 10px; }
        .price-tag { color: var(--mint); font-weight: 800; font-size: 1.2rem; }
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
                    <a class="nav-link active text-white" href="#"><i class="bi bi-shop me-2"></i> Marketplace</a>
                    <a class="nav-link text-white-50" href="my_portfolio.php"><i class="bi bi-tree-fill me-2"></i> My Forest</a>
                    <hr class="my-4 opacity-25">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
                </nav>
            </div>
        </div>

        <div class="col main-content">
            <h2 class="fw-bold mb-4">Tree Marketplace</h2>
            <?php echo $message; ?>

            <div class="row g-4">
                <?php if (empty($inventory)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No trees available in the marketplace right now.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($inventory as $item): ?>
                    <div class="col-md-4">
                        <div class="card tree-card h-100 shadow-sm">
                            <div class="p-4 text-center bg-light">
                                 <i class="bi bi-tree-fill display-1 text-success"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <span class="price-tag">KES <?php echo number_format($item['price'], 2); ?></span>
                                    <form method="POST" style="width: 50%;">
                                        <input type="hidden" name="buy_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                        <button type="submit" class="btn btn-buy">Buy Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>