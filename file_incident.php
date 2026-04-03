<?php
session_start();

/**
 * 1. SECURITY: Only logged-in Officers can file reports
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: login.php");
    exit();
}

/**
 * 2. DATABASE CONFIGURATION (InfinityFree Production)
 * Updated from localhost to your live hosting details
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887"; 
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity"; 

try {
    // Standard connection with UTF-8 support
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $officer_id = $_SESSION['user_id'];

    /**
     * 3. FETCH SEEDLINGS ASSIGNED TO THIS OFFICER
     * This fills your dropdown with the trees the officer is monitoring
     */
    $seedling_query = "SELECT id, tree_type FROM seedlings WHERE officer_id = :oid ORDER BY id DESC";
    $stmt = $conn->prepare($seedling_query);
    $stmt->execute(['oid' => $officer_id]);
    $seedlings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
     * 4. HANDLE FORM SUBMISSION
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $seedling_id = $_POST['seedling_id'];
        $issue_type = $_POST['issue_type'];
        $description = $_POST['description'];

        // Insert into the live reports table
        $sql = "INSERT INTO reports (seedling_id, officer_id, issue_type, description) 
                VALUES (:sid, :oid, :itype, :desc)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':sid' => $seedling_id,
            ':oid' => $officer_id,
            ':itype' => $issue_type,
            ':desc' => $description
        ]);

        // Redirect back to dashboard with the success message
        header("Location: officer_dashboard.php?msg=Incident Report Filed Successfully");
        exit();
    }
} catch(PDOException $e) {
    // This catches connection errors (like the one in your screenshot)
    $error = "System Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Ops | File Incident Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #1b4332; font-family: 'Inter', sans-serif; color: white; min-height: 100vh; }
        .form-container { background: white; border-radius: 30px; padding: 30px; color: #333; margin-top: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .btn-report { background: #ffb703; border: none; padding: 15px; border-radius: 15px; font-weight: 700; width: 100%; color: #1b4332; transition: transform 0.2s; }
        .btn-report:hover { transform: scale(1.02); background: #fdb000; }
        .form-control, .form-select { background: #f1f5f9; border: none; padding: 12px; border-radius: 12px; }
        .back-link { color: #80ed99; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="officer_dashboard.php" class="back-link"><i class="bi bi-arrow-left me-1"></i> Back to Ops</a>
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">Urgent Report</span>
    </div>

    <h2 class="fw-bold mb-1">Incident Report</h2>
    <p class="opacity-75 small">Report health issues or hazards in the field for Tawi Digital.</p>

    <div class="form-container">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold text-muted uppercase">AFFECTED SEEDLING</label>
                <select name="seedling_id" class="form-select" required>
                    <option value="">Select the tree...</option>
                    <?php if (empty($seedlings)): ?>
                        <option disabled>No seedlings registered under your ID</option>
                    <?php else: ?>
                        <?php foreach($seedlings as $s): ?>
                            <option value="<?php echo $s['id']; ?>">
                                Tree #<?php echo $s['id']; ?> (<?php echo htmlspecialchars($s['tree_type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label small fw-bold text-muted uppercase">ISSUE CATEGORY</label>
                <select name="issue_type" class="form-select" required>
                    <option value="Pest Infestation">Pest Infestation</option>
                    <option value="Drought/Dehydration">Drought/Dehydration</option>
                    <option value="Physical Damage">Physical Damage</option>
                    <option value="Disease">Disease</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-4 text-start">
                <label class="form-label small fw-bold text-muted uppercase">DETAILED DESCRIPTION</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe the symptoms or damage observed (e.g. yellow leaves, broken stem)..." required></textarea>
            </div>

            <button type="submit" class="btn btn-report shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> File Field Report
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>