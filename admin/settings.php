<?php
require_once '../config/database.php';
require_once '../config/session.php';
$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);
$site_name = $settings['site_name'] ?? 'MYBOOK';

requireAdmin();

// Handle settings update
if ($_POST) {
    if (isset($_POST['site_name'])) {
        $site_name = trim($_POST['site_name']);
        
        // Update or insert site name setting
        $stmt = $pdo->prepare("UPDATE settings SET site_name = ? WHERE id = 1");
        $stmt->execute([$site_name]);
        
        $success = "Settings updated successfully!";
    }
}

// Get current settings
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch();

$site_name = $settings ? $settings['site_name'] : 'MYBOOK';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo $site_name; ?> Admin</title>
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
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="books.php">
                            <i class="fas fa-book me-2"></i> Manage Books
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i> Manage Users
                        </a>
                        <a class="nav-link active" href="settings.php">
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
                    <h2 class="mb-4">Site Settings</h2>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>General Settings</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                                   value="<?php echo htmlspecialchars($site_name); ?>" required>
                                            <div class="form-text">This will be displayed as the site title.</div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>System Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                                    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                                    <p><strong>Upload Max Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
                                    <p><strong>Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
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
