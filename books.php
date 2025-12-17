<?php
include 'config/database.php';
include 'config/session.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
// Preserve filters for pagination links
$qs = [];
if ($search !== '') $qs['search'] = $search;
if ($category > 0) $qs['category'] = $category;
if ($year > 0) $qs['year'] = $year;
$qs_string = http_build_query($qs);

// Build query
$where_conditions = ["b.status = 'approved'"];
$params = [];
$types = "";

if ($search !== '') {
    $where_conditions[] = "(b.title LIKE ? OR b.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

if ($category > 0) {
    $where_conditions[] = "b.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($year > 0) {
    $where_conditions[] = "b.publish_year = ?";
    $params[] = $year;
    $types .= "i";
}

$where_clause = implode(" AND ", $where_conditions);

// Get total count
$count_query = "SELECT COUNT(*) as total FROM books b 
                JOIN categories c ON b.category_id = c.id 
                WHERE $where_clause";
$count_stmt = mysqli_prepare($conn, $count_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_books = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_books / $limit);

// Get books
$books_query = "SELECT b.*, c.name as category_name, u.name as uploader_name 
                FROM books b 
                JOIN categories c ON b.category_id = c.id 
                JOIN users u ON b.user_id = u.id 
                WHERE $where_clause 
                ORDER BY b.created_at DESC 
                LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $books_query);
$all_params = array_merge($params, [$limit, $offset]);
$all_types = $types . "ii";
if (!empty($all_params)) {
    mysqli_stmt_bind_param($stmt, $all_types, ...$all_params);
}
mysqli_stmt_execute($stmt);
$books_result = mysqli_stmt_get_result($stmt);

// Get categories for filter
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Get years for filter
$years_query = "SELECT DISTINCT publish_year FROM books WHERE status = 'approved' ORDER BY publish_year DESC";
$years_result = mysqli_query($conn, $years_query);

include 'includes/header.php';
?>

<div class="container py-5">
    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search for a book..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="year">
                        <option value="">All Years</option>
                        <?php while ($yr = mysqli_fetch_assoc($years_result)): ?>
                            <option value="<?php echo $yr['publish_year']; ?>" 
                                    <?php echo ($year == $yr['publish_year']) ? 'selected' : ''; ?>>
                                <?php echo $yr['publish_year']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <h2 class="mb-4">Latest E-Books</h2>

    <!-- Books Grid -->
    <div class="row">
        <?php if (mysqli_num_rows($books_result) > 0): ?>
            <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if ($book['cover_image']): ?>
                                <!-- Updated to use Cloudinary URLs directly -->
                                <img src="<?php echo $book['cover_image']; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 250px;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="text-muted small mb-2"><?php echo $book['publish_year']; ?></p>
                            <p class="card-text flex-grow-1">
                                <?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?>
                            </p>
                            <div class="mt-auto">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($book['category_name']); ?></span>
                                <a href="book-detail.php?id=<?php echo $book['id']; ?>" 
                                   class="btn btn-warning w-100">Read Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h4>No books found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Books pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $qs_string ? '&' . $qs_string : ''; ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $qs_string ? '&' . $qs_string : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $qs_string ? '&' . $qs_string : ''; ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<?php // qs computation moved above ?>
?>
