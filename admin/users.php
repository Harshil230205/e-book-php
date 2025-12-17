<?php
    require_once '../config/database.php';
    require_once '../config/session.php';
    $settings_query  = "SELECT * FROM settings LIMIT 1";
    $settings_result = mysqli_query($conn, $settings_query);
    $settings        = mysqli_fetch_assoc($settings_result);
    $site_name       = $settings['site_name'] ?? 'MYBOOK';

    requireAdmin();

    // Handle user actions
    if ($_POST) {
        if (isset($_POST['action']) && isset($_POST['user_id'])) {
            $user_id = (int) $_POST['user_id'];
            $action  = $_POST['action'];

            if ($action == 'delete') {
                // Don't allow deleting admin users
                $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && $user['role'] != 'admin') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $success = "User deleted successfully!";
                } else {
                    $error = "Cannot delete admin users!";
                }
            }
        }
    }

    // Pagination
    $page     = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $per_page = 15;
    $offset   = ($page - 1) * $per_page;

    // Get total count
    $stmt        = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    $total_pages = ceil($total_users / $per_page);

    // Get users
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    $stmt->execute();
    $users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users -                          <?php echo $site_name; ?> Admin</title>
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
                        <a class="nav-link active" href="users.php">
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
                    <h2 class="mb-4">Manage Users</h2>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Joined Date</th>
                                            <th>Books Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                        <?php
                                            // Get book count for this user
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as book_count FROM books WHERE user_id = ?");
                                            $stmt->execute([$user['id']]);
                                            $book_count = $stmt->fetch()['book_count'];
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td><?php echo $book_count; ?> books</td>
                                            <td>
                                                <?php if ($user['role'] != 'admin'): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user and all their books?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Protected</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Users pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item<?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
