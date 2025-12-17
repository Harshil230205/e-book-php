<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$total_users = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as total_books FROM books");
$total_books = $stmt->fetch()['total_books'];

$stmt = $pdo->query("SELECT COUNT(*) as pending_books FROM books WHERE status = 'pending'");
$pending_books = $stmt->fetch()['pending_books'];

$stmt = $pdo->query("SELECT COUNT(*) as approved_books FROM books WHERE status = 'approved'");
$approved_books = $stmt->fetch()['approved_books'];
$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);
$site_name = $settings['site_name'] ?? 'MYBOOK';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo $site_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }
        .sidebar .nav-link:hover {
            background: #34495e;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #e67e22;
            color: #fff;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.green {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-card.purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar"  style="position:relative">
                    <div class="p-3 text-center border-bottom " style=" display: flex; align-items: center; justify-content: center; gap: 5px; ">
                       <div  onClick="window.location.href = 'http://localhost:3000/index.php'"> <img src="../images/left-arrow (2).png" alt="left arrow" width="25px" > </div>
                        <h4 class="text-white" > <?php echo $site_name; ?> Admin</h4>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="books.php">
                            <i class="fas fa-book me-2"></i> Manage Books
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i> Manage Users
                        </a>
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                         <div style= "position: absolute; bottom: 0; width: 100%; background:#bb2d3b;">
                            <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2 "></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="p-4">
                    <h2 class="mb-4">Dashboard Overview</h2>
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $total_users; ?></h3>
                                        <p class="mb-0">Total Users</p>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card orange">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $total_books; ?></h3>
                                        <p class="mb-0">Total Books</p>
                                    </div>
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card green">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $pending_books; ?></h3>
                                        <p class="mb-0">Pending Books</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card purple">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $approved_books; ?></h3>
                                        <p class="mb-0">Approved Books</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Books -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Book Uploads</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $stmt = $pdo->query("SELECT b.*, u.name FROM books b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT 5");
                                    $recent_books = $stmt->fetchAll();
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Author</th>
                                                    <th>Uploaded By</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_books as $book): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                                    <!-- Changed from full_name to name to match database column -->
                                                    <td><?php echo htmlspecialchars($book['name']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $book['status'] == 'approved' ? 'success' : ($book['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                            <?php echo ucfirst($book['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
