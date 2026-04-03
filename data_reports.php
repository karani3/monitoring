<?php
session_start();

/**
 * 1. DATABASE CONNECTION (InfinityFree Production)
 * Updated with your hosting credentials
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887"; 
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity"; 

try {
    // Added charset=utf8 for better compatibility with regional names
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Friendly error for live users
    die("Unable to connect to the reporting database. Please try again later.");
}

/**
 * 2. FETCH DATA 
 * Fetching from the live InfinityFree database
 */
try {
    $sql = "SELECT id, region_name, description FROM regions ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If the table doesn't exist yet, we prevent a crash
    $reports = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Admin | Data Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --forest: #1b4332; --emerald: #2d6a4f; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; display: flex; margin: 0; }
        
        /* Ensure sidebar doesn't overlap on small screens */
        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            width: calc(100% - 280px); 
            min-height: 100vh; 
        }

        .report-card { 
            background: white; 
            border-radius: 20px; 
            border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            padding: 20px; 
            overflow: hidden;
        }

        .table thead { 
            background-color: #fdfdfd; 
            color: #adb5bd; 
            text-transform: uppercase; 
            font-size: 12px; 
            letter-spacing: 1px; 
        }

        .table th { border: none; padding: 20px; }
        .table td { padding: 20px; vertical-align: middle; border-top: 1px solid #f8f9fa; }
        
        .status-badge { 
            background-color: #d8f3dc; 
            color: #2d6a4f; 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 600; 
        }

        .print-btn { 
            border: 2px solid var(--emerald); 
            color: var(--emerald); 
            border-radius: 10px; 
            padding: 8px 20px; 
            font-weight: 600; 
            transition: all 0.3s;
        }

        .print-btn:hover { 
            background-color: var(--emerald); 
            color: white; 
            box-shadow: 0 4px 10px rgba(45, 106, 79, 0.2);
        }

        /* Print-specific styles to hide sidebar and buttons when printing */
        @media print {
            .sidebar, .print-btn, .text-muted { display: none !important; }
            .main-content { margin-left: 0; width: 100%; padding: 0; }
            .report-card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

    <?php 
    // Ensure sidebar.php also uses the InfinityFree credentials if it has a connection
    include('sidebar.php'); 
    ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-bold">Data Reports</h1>
                <p class="text-muted m-0">Monitoring reforestation progress across all zones.</p>
            </div>
            <button class="btn print-btn" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Print Report
            </button>
        </div>

        <div class="report-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ZONE NAME</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($reports) > 0): ?>
                            <?php foreach($reports as $row): ?>
                                <tr>
                                    <td class="text-muted">#<?php echo $row['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($row['region_name']); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><span class="status-badge"><i class="fas fa-check-circle me-1"></i> Active</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-database mb-3 fa-2x d-block"></i>
                                    No report data found in the live database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>