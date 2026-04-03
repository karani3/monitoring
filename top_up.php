<?php
session_start();

/**
 * 1. DATABASE CONFIGURATION
 * We use 'require' to pull your live InfinityFree credentials from config.php
 */
require 'config.php'; 

// 2. Identify User
if (!isset($_SESSION['user_id'])) {
    // Fallback for development if session isn't set, though login is preferred
    $user_id = 1; 
    $name = 'Partner';
} else {
    $user_id = $_SESSION['user_id'];
    $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Partner';
}

// 3. Handle Top-Up Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    
    if ($amount > 0) {
        try {
            // Check if user has a wallet row yet in the 'wallet' table
            $check = $conn->prepare("SELECT user_id FROM wallet WHERE user_id = ?");
            $check->execute([$user_id]);
            
            if ($check->rowCount() > 0) {
                // Row exists, update the existing balance
                $sql = "UPDATE wallet SET balance = balance + ?, last_updated = NOW() WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$amount, $user_id]);
            } else {
                // Row doesn't exist (new user), create the wallet entry
                $sql = "INSERT INTO wallet (user_id, balance, last_updated) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $amount]);
            }
            
            // Redirect back to dashboard with a success message
            header("Location: beneficiary_dashboard.php?success=" . urlencode("Recharged KES " . number_format($amount, 2)));
            exit();
            
        } catch (PDOException $e) {
            // This will catch issues like missing tables or columns
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Impact | Top Up Wallet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 280px); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .topup-card { background: white; border-radius: 25px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 450px; width: 100%; }
        .amount-btn { border: 2px solid #e9ecef; border-radius: 15px; padding: 12px; cursor: pointer; transition: 0.3s; font-weight: 700; text-align: center; background: none; width: 100%; color: #495057; }
        .amount-btn:hover { border-color: #2d6a4f; color: #2d6a4f; background: #f7fdf9; }
        .btn-confirm { background: #1b4332; color: white; border-radius: 50px; padding: 12px; font-weight: 700; width: 100%; border: none; transition: 0.3s; }
        .btn-confirm:hover { background: #2d6a4f; transform: translateY(-2px); }
        .input-group-text { background: #f8f9fa; border-right: none; border-radius: 15px 0 0 15px; }
        .form-control { border-left: none; border-radius: 0 15px 15px 0; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; width: 100%; padding: 20px; }
        }
    </style>
</head>
<body>

    <?php include('beneficiary_sidebar.php'); ?>

    <div class="main-content">
        <div class="topup-card shadow">
            <div class="text-center mb-4">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block text-success mb-3">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <h3 class="fw-bold">Top Up Wallet</h3>
                <p class="text-muted small">Add funds for your next tree purchase, <?php echo htmlspecialchars($name); ?>.</p>
            </div>

            <form action="top_up.php" method="POST">
                <label class="form-label fw-bold small text-muted text-uppercase">Quick Select (KES)</label>
                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <button type="button" class="amount-btn" onclick="document.getElementById('amt_input').value='500'">500</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="amount-btn" onclick="document.getElementById('amt_input').value='1000'">1000</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="amount-btn" onclick="document.getElementById('amt_input').value='2000'">2000</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Enter Custom Amount</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">KES</span>
                        <input type="number" name="amount" id="amt_input" class="form-control form-control-lg shadow-sm" placeholder="0.00" required min="1">
                    </div>
                </div>

                <button type="submit" class="btn btn-confirm shadow-sm">
                    Confirm Recharge
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="beneficiary_dashboard.php" class="text-decoration-none text-muted small fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

</body>
</html>