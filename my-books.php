<?php
include 'config/database.php';
include 'config/session.php';

requireLogin();

// Pagination
$page = $_GET['page'] ?? 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Get user's books
$books_query = "SELECT b.*, c.name as category_name 
                FROM books b 
                JOIN categories c ON b.category_id = c.id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC 
                LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $books_query);
mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user_id'], $limit, $offset);
mysqli_stmt_execute($stmt);
$books_result = mysqli_stmt_get_result($stmt);

// Get total count
$count_query = "SELECT COUNT(*) as total FROM books WHERE user_id = ?";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_books = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_books / $limit);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Uploaded E-Books</h2>
        <a href="upload.php" class="btn btn-warning">
            <i class="fas fa-plus"></i> Upload New Book
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="#all" data-status="all">All Books</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#pending" data-status="pending">Pending Books</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved" data-status="approved">Approved Books</a>
        </li>
    </ul>

    <!-- Books Grid -->
    <div class="row" id="books-container">
        <?php if (mysqli_num_rows($books_result) > 0): ?>
            <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                <div class="col-lg-4 col-md-6 mb-4 book-item" data-status="<?php echo $book['status']; ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if ($book['cover_image']): ?>
                                <!-- Updated to use Cloudinary URLs directly -->
                                <img src="<?php echo $book['cover_image']; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if ($book['status'] == 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($book['status'] == 'rejected'): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar"></i> <?php echo $book['publish_year']; ?>
                            </p>
                            <p class="card-text flex-grow-1">
                                <?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <span class="badge bg-secondary mb-2">
                                    <?php echo htmlspecialchars($book['category_name']); ?>
                                </span>
                                <div class="d-flex gap-2">
                                    <?php if ($book['status'] == 'approved'): ?>
                                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" 
                                           class="btn btn-warning flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <a href="edit-book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-outline-primary flex-fill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Uploaded: <?php echo date('M j, Y', strtotime($book['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h4>No books uploaded yet</h4>
                    <p class="text-muted mb-4">Start sharing your knowledge by uploading your first book!</p>
                    <a href="upload.php" class="btn btn-warning">
                        <i class="fas fa-plus"></i> Upload Your First Book
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="My books pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
// Status filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusLinks = document.querySelectorAll('.nav-link[data-status]');
    const bookItems = document.querySelectorAll('.book-item');
    
    statusLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active tab
            statusLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const status = this.dataset.status;
            
            // Filter books
            bookItems.forEach(item => {
                if (status === 'all' || item.dataset.status === status) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
