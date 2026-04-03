<?php
session_start();
// Security check: Only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: user_management.php?msg=User Deleted");
    exit();
}

// Fetch all users
$users = $conn->query("SELECT id, username, role, email FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tawi Admin | User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --tawi-forest: #1b4332; --tawi-emerald: #2d6a4f; --bg-pearl: #f8fafc; }
        body { background-color: var(--bg-pearl); font-family: 'Inter', sans-serif; display: flex; margin: 0; }
        .sidebar { width: 280px; height: 100vh; background: var(--tawi-forest); position: fixed; color: white; padding: 30px 20px; display: flex; flex-direction: column; }
        .nav-link { color: #b7e4c7; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; text-decoration: none; display: flex; align-items: center; }
        .nav-link.active { background: var(--tawi-emerald); color: white; }
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5">TAWI ADMIN</h3>
    <nav>
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Overview</a>
        <a href="user_management.php" class="nav-link active"><i class="bi bi-people me-2"></i> User Management</a>
        <a href="field_regions.php" class="nav-link"><i class="bi bi-map me-2"></i> Field Regions</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">User Management</h2>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-2"></i> Add New User
        </button>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($_GET['msg']); ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card p-4">
        <table class="table align-middle">
            <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Email</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['id']; ?></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><span class="badge <?php echo $u['role'] == 'admin' ? 'bg-danger' : 'bg-success'; ?>"><?php echo strtoupper($u['role']); ?></span></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="text-end">
                        <a href="user_management.php?delete_id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="process_add_user.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Register New Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="username" class="form-control rounded-3" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="email@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">System Role</label>
                        <select name="role" class="form-select rounded-3">
                            <option value="beneficiary">Beneficiary</option>
                            <option value="officer">Field Officer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Initial Password</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>