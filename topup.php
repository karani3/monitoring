<?php
session_start();

// 1. Database Connection
$host = "localhost"; 
$db_user = "root"; 
$db_pass = ""; 
$db_name = "tawi system";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { 
    die("Connection failed: " . $e->getMessage()); 
}

// 2. Identify User (Default to 1 for Peris)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// 3. Handle Top-Up Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    
    if ($amount > 0) {
        // Update the wallet balance in the database
        $sql = "UPDATE wallet SET balance = balance + ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$amount, $user_id]);
        
        // Redirect back to seedlings page with a success message
        header("Location: available_seedlings.php?success=Recharged KES " . number_format($amount, 2));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    </style>
</head>
<body>

    <?php include('beneficiary_sidebar.php'); ?>

    <div class="main-content">
        <div class="topup-card">
            <div class="text-center mb-4">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block text-success mb-3">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <h3 class="fw-bold">Top Up Wallet</h3>
                <p class="text-muted small">Add funds to plant more trees today.</p>
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
                    <label class="form-label fw-bold small text-muted text-uppercase">Enter Amount</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-4">KES</span>
                        <input type="number" name="amount" id="amt_input" class="form-control form-control-lg border-start-0 rounded-end-4" placeholder="0.00" required min="1">
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