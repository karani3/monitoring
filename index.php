<?php
session_start();

/**
 * DATABASE CONFIGURATION
 * Updated for InfinityFree Hosting
 */
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887";         
$db_pass = "Karani2007";           
$db_name = "if0_41483887_db_tawicity"; 

try {
    // Establishing the connection
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetching total seedlings for the dynamic tracking badge
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM seedlings");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_seedlings = $stats['total'] ?? 0;
} catch(PDOException $e) {
    // If connection fails, we set count to 0. 
    // You can uncomment the line below to debug connection issues:
    // die("Database Error: " . $e->getMessage());
    $total_seedlings = 0; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tawi Digital | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { 
            --forest-green: #1b4332; 
            --leaf-green: #2d6a4f; 
            --pearl-white: #fcfcfc;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--pearl-white); color: #333; }
        
        /* Navigation */
        .navbar-brand { color: var(--forest-green) !important; letter-spacing: 1px; }
        .nav-link { font-weight: 500; transition: 0.3s; color: #555 !important; }
        .nav-link:hover { color: var(--leaf-green) !important; }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), 
                        url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1600&q=80');
            background-size: cover; background-position: center; height: 80vh; color: white; display: flex; align-items: center;
        }
        .btn-green { background-color: var(--leaf-green); color: white; border: none; transition: 0.3s; }
        .btn-green:hover { background-color: var(--forest-green); color: white; transform: translateY(-2px); }
        
        /* Features/Impact Section */
        .impact-icon { font-size: 2.5rem; color: var(--leaf-green); margin-bottom: 1rem; }
        
        /* Footer Styling */
        footer { background-color: var(--forest-green); color: rgba(255, 255, 255, 0.8); }
        .footer-title { color: white; font-weight: 700; margin-bottom: 1.5rem; }
        .footer-link { color: rgba(255, 255, 255, 0.7); text-decoration: none; transition: 0.3s; display: block; margin-bottom: 0.5rem; }
        .footer-link:hover { color: #b7e4c7; padding-left: 5px; }
        .copyright-bar { background-color: #081c15; padding: 20px 0; margin-top: 50px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-tree-fill text-success"></i> TAWI DIGITAL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="about.php">About Project</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="features.php">Features</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-success px-4 rounded-pill fw-bold" href="login.php">Log in</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-2 fw-bold mb-3">Monitoring Growth, <br>Securing the Future.</h1>
            <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 700px;">
                A centralized digital system for tracking seedling survival and reforestation impact.
            </p>
            <div class="d-grid d-sm-flex justify-content-sm-center gap-3">
                <a href="signup.php" class="btn btn-green btn-lg px-5 py-3 rounded-pill shadow fw-bold">Join the Project</a>
                <a href="login.php" class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow fw-bold">Get Started</a>
            </div>
            
            <?php if($total_seedlings > 0): ?>
                <div class="mt-5">
                    <span class="badge rounded-pill bg-success px-4 py-3 shadow-sm">
                        <i class="bi bi-seedling me-2"></i> Currently Tracking: <?php echo number_format($total_seedlings); ?> Seedlings
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <section class="py-5 bg-white">
        <div class="container py-5 text-center">
            <h2 class="fw-bold mb-5">Why Reforestation Tracking Matters</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="impact-icon"><i class="bi bi-geo-alt"></i></div>
                    <h5 class="fw-bold">GPS Mapping</h5>
                    <p class="text-muted small">Pinpoint exactly where trees are planted to ensure they aren't lost to memory.</p>
                </div>
                <div class="col-md-4">
                    <div class="impact-icon"><i class="bi bi-graph-up"></i></div>
                    <h5 class="fw-bold">Growth Analytics</h5>
                    <p class="text-muted small">Track health milestones from sapling to full maturity.</p>
                </div>
                <div class="col-md-4">
                    <div class="impact-icon"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold">Accountability</h5>
                    <p class="text-muted small">Transparency for donors and stakeholders in reforestation projects.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="pt-5">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-4">
                    <h5 class="footer-title">🌲 TAWI DIGITAL</h5>
                    <p class="small">A specialized platform for the digital tracking of reforestation efforts, ensuring every seedling planted has a story of survival.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h6 class="footer-title">Quick Links</h6>
                    <a href="index.php" class="footer-link">Home</a>
                    <a href="about.php" class="footer-link">About Project</a>
                    <a href="features.php" class="footer-link">Features</a>
                </div>
                <div class="col-lg-3">
                    <h6 class="footer-title">Contact Support</h6>
                    <p class="small mb-1"><i class="bi bi-envelope me-2"></i> info@tawidigital.com</p>
                    <p class="small mb-1"><i class="bi bi-geo-alt me-2"></i> Nairobi, Kenya</p>
                </div>
            </div>
            <div class="copyright-bar text-center">
                <p class="mb-0 small">© 2026 Tawi Digital Tree Monitoring System. Developed by Livingstone Bundi Karani.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>