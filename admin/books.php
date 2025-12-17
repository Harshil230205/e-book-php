<?php
    require_once '../config/database.php';
    require_once '../config/session.php';
    $settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);
$site_name = $settings['site_name'] ?? 'MYBOOK';

    requireAdmin();

    // Handle book actions
    if ($_POST) {
        if (isset($_POST['action']) && isset($_POST['book_id'])) {
            $book_id = (int) $_POST['book_id'];
            $action  = $_POST['action'];

            if ($action == 'approve') {
                $stmt = $pdo->prepare("UPDATE books SET status = 'approved' WHERE id = ?");
                $stmt->execute([$book_id]);
                $success = "Book approved successfully!";
            } elseif ($action == 'reject') {
                $stmt = $pdo->prepare("UPDATE books SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$book_id]);
                $success = "Book rejected successfully!";
            } elseif ($action == 'delete') {
                // Get book info to delete files
                $stmt = $pdo->prepare("SELECT cover_image, pdf_file FROM books WHERE id = ?");
                $stmt->execute([$book_id]);
                $book = $stmt->fetch();

                if ($book) {
                    // Delete files
                    if ($book['cover_image'] && file_exists('../' . $book['cover_image'])) {
                        unlink('../' . $book['cover_image']);
                    }
                    if ($book['pdf_file'] && file_exists('../' . $book['pdf_file'])) {
                        unlink('../' . $book['pdf_file']);
                    }

                    // Delete from database
                    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
                    $stmt->execute([$book_id]);
                    $success = "Book deleted successfully!";
                }
            }
        }
    }

    // Pagination
    $page     = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $per_page = 10;
    $offset   = ($page - 1) * $per_page;

    // Filter by status
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
    $where_clause  = $status_filter != 'all' ? "WHERE b.status = ?" : "";

    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM books b $where_clause";
    $count_stmt  = $pdo->prepare($count_query);
    if ($status_filter != 'all') {
        $count_stmt->execute([$status_filter]);
    } else {
        $count_stmt->execute();
    }
    $total_books = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_books / $per_page);

    // Get books
    $query = "SELECT b.*, u.name, u.email FROM books b
          JOIN users u ON b.user_id = u.id
          $where_clause
          ORDER BY b.created_at DESC
          LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($query);
    if ($status_filter != 'all') {
        $stmt->execute([$status_filter]);
    } else {
        $stmt->execute();
    }
    $books = $stmt->fetchAll();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - <?php echo $site_name; ?> Admin</title>
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
        .book-cover {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar" style="position:relative">
                   <div class="p-3 text-center border-bottom " style=" display: flex; align-items: center; justify-content: center; gap: 5px; ">
                       <div  onClick="window.location.href = 'http://localhost:3000/index.php'"> <img src="../images/left-arrow (2).png" alt="left arrow" width="25px" > </div>
                        <h4 class="text-white" > <?php echo $site_name; ?> Admin</h4>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link active" href="books.php">
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
                        </div>

                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Books</h2>
                        <div class="d-flex gap-2">
                            <a href="?status=all" class="btn btn-outline-secondary                                                                                   <?php echo $status_filter == 'all' ? 'active' : ''; ?>">All Books</a>
                            <a href="?status=pending" class="btn btn-outline-warning                                                                                     <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Pending Books</a>
                            <a href="?status=approved" class="btn btn-outline-success                                                                                      <?php echo $status_filter == 'approved' ? 'active' : ''; ?>">Approved Books</a>
                            <a href="?status=rejected" class="btn btn-outline-danger                                                                                     <?php echo $status_filter == 'rejected' ? 'active' : ''; ?>">Rejected Books</a>
                        </div>
                    </div>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Book</th>
                                            <th>Details</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // echo $books;
                                        if (empty($books)){
                                            echo '<tr><td colspan="5" class="text-center" style="color:red;">No books found.</td></tr>';
                                        };
                                        
                                         foreach ($books as $book): ?>
                                    
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                                                     alt="Cover" class="book-cover">
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($book['title']); ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php echo $book['publish_year']; ?><br>
                                                        Uploaded by:                                                                     <?php echo htmlspecialchars($book['name']); ?><br>
                                                        <?php echo date('M d, Y', strtotime($book['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $book['status'] == 'approved' ? 'success' : ($book['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($book['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($book['status'] == 'pending'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this book?')">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reject this book?')">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book permanently?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Books pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item<?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>">Next</a>
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