<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Partner';

// Hardcoded seedlings to restore the images and buttons immediately
$seedlings = [
    ['id' => 1, 'name' => 'Bamboo', 'price' => 150, 'co2' => '0.02t', 'image' => 'https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?w=400'],
    ['id' => 2, 'name' => 'Acacia', 'price' => 300, 'co2' => '0.05t', 'image' => 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=400'],
    ['id' => 3, 'name' => 'Moringa', 'price' => 250, 'co2' => '0.03t', 'image' => 'https://images.unsplash.com/photo-1596715611218-9bb215f3c177?w=400']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seedling Marketplace | Tawi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --forest-green: #1b4332; --emerald: #2d6a4f; --glass: rgba(255, 255, 255, 0.95); }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; margin: 0; }
        .sidebar { background: var(--forest-green); min-height: 100vh; color: white; padding: 30px 20px; position: fixed; width: 260px; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .nav-link { color: rgba(255,255,255,0.7); transition: 0.3s; padding: 12px 15px; border-radius: 12px; text-decoration: none; display: block; }
        .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .tree-card { border: none; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; transition: 0.3s; background: white; }
        .tree-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        .balance-card { background: #e8f5e9; border-radius: 15px; padding: 10px 20px; display: inline-flex; align-items: center; gap: 10px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; width: 100%; padding: 20px; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 class="fw-bold mb-5 px-3">TAWI</h2>
    <nav class="nav flex-column gap-2">
        <a class="nav-link" href="beneficiary_dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link active" href="available_seedlings.php"><i class="bi bi-cart-fill me-2"></i> Buy Seedlings</a>
        <a class="nav-link" href="my_portfolio.php"><i class="bi bi-leaf me-2"></i> My Forest</a>
        <hr class="opacity-25 mt-4">
        <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark m-0">Seedling Marketplace</h2>
            <p class="text-muted">Invest in nature and expand your reforestation impact.</p>
        </div>
        <div class="balance-card">
            <small class="text-success fw-bold d-block" style="font-size: 0.7rem;">Current Balance</small>
            <span class="fw-bold text-dark">KES 6,050.00</span>
            <button class="btn btn-success btn-sm rounded-circle p-0" style="width:24px; height:24px;"><i class="bi bi-plus"></i></button>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach($seedlings as $tree): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card tree-card h-100">
                <div class="position-relative">
                    <img src="<?php echo $tree['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-success rounded-pill shadow-sm">
                        <?php echo $tree['co2']; ?> CO2 Offset
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold m-0"><?php echo $tree['name']; ?></h5>
                        <span class="text-success fw-bold">KES <?php echo number_format($tree['price']); ?></span>
                    </div>
                    <p class="text-muted small mb-4">High-quality seedling, vetted for health and ready for planting in your designated zone.</p>
                    
                    <form action="purchase_handler.php" method="POST">
                        <input type="hidden" name="tree_id" value="<?php echo $tree['id']; ?>">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success rounded-pill fw-bold">
                                <i class="bi bi-cart-plus me-2"></i> Buy Seedling
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>