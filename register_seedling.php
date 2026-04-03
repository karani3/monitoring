<?php
session_start();

/**
 * 1. DATABASE CONNECTION (InfinityFree Production)
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887"; 
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch Regions for the dropdown
    $regions = $conn->query("SELECT id, region_name FROM regions")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // These names match your <select> and <input> 'name' attributes
        $rid = $_POST['region_id'];
        $ttype = $_POST['tree_type'];
        
        // Use the logged-in user's ID, or default to 1 for testing
        $oid = $_SESSION['user_id'] ?? 1; 

        // Ensure these column names match your phpMyAdmin table exactly
        $sql = "INSERT INTO seedlings (region_id, officer_id, tree_type) VALUES (:rid, :oid, :ttype)";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':rid'   => $rid,
            ':oid'   => $oid,
            ':ttype' => $ttype
        ]);

        // Redirect back to dashboard with success message
        header("Location: officer_dashboard.php?msg=Seedling Registered Successfully!");
        exit();
    }
} catch(PDOException $e) {
    // This will print the error if something goes wrong (e.g., missing table)
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Seedling | Tawi OS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background-color: #1b4332; 
            font-family: 'Inter', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0;
        }
        .form-card { 
            background: white; 
            border-radius: 25px; 
            padding: 40px; 
            width: 100%; 
            max-width: 450px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .btn-submit {
            background: #2d6a4f; 
            border: none;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background: #1b4332;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="form-card text-center">
        <div class="mb-3">
            <i class="fas fa-seedling fa-3x" style="color: #2d6a4f;"></i>
        </div>
        
        <h2 class="fw-bold mb-2" style="color: #1b4332;">Register Seedling</h2>
        <p class="text-muted small mb-4">Add a new entry to the reforestation database</p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small text-start">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Select Region</label>
                <select name="region_id" class="form-select" required>
                    <option value="">-- Choose a Region --</option>
                    <?php foreach($regions as $r): ?>
                        <option value="<?php echo $r['id']; ?>">
                            <?php echo htmlspecialchars($r['region_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4 text-start">
                <label class="form-label small fw-bold">Tree Species</label>
                <input type="text" name="tree_type" class="form-control" placeholder="e.g. Cypress, Bamboo, Mahogany" required>
            </div>

            <button type="submit" class="btn btn-success btn-submit w-100 fw-bold">
                Confirm Registration
            </button>
            
            <a href="officer_dashboard.php" class="d-block mt-4 text-muted text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Cancel and Return
            </a>
        </form>
    </div>

</body>
</html>