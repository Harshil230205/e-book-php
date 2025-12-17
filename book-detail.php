<?php
include 'config/database.php';
include 'config/session.php';
include 'config/cloudinary.php'; // Include cloudinary functions

$book_id = $_GET['id'] ?? 0;

if (!$book_id) {
    header('Location: books.php');
    exit();
}

// Get book details
$book_query = "SELECT b.*, c.name as category_name, u.name as uploader_name 
               FROM books b 
               JOIN categories c ON b.category_id = c.id 
               JOIN users u ON b.user_id = u.id 
               WHERE b.id = ? AND b.status = 'approved'";

$stmt = mysqli_prepare($conn, $book_query);
mysqli_stmt_bind_param($stmt, "i", $book_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: books.php');
    exit();
}

$book = mysqli_fetch_assoc($result);

// Extract public_id from PDF URL for proper download
$pdf_public_id = '';
if ($book['pdf_file']) {
    $url_parts = parse_url($book['pdf_file']);
    $path_parts = explode('/', $url_parts['path']);
    $pdf_public_id = end($path_parts);
    // Remove file extension if present
    $pdf_public_id = pathinfo($pdf_public_id, PATHINFO_FILENAME);
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow">
                <?php if ($book['cover_image']): ?>
                    <img src="<?php echo $book['cover_image']; ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" 
                         style="height: 400px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo htmlspecialchars($book['title']); ?></h1>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            </div>
            
            <div class="row mb-4">
                <div class="col-sm-6">
                    <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?>
                </div>
                <div class="col-sm-6">
                    <strong>Category:</strong> <?php echo htmlspecialchars($book['category_name']); ?>
                </div>
                <div class="col-sm-6">
                    <strong>Publish Year:</strong> <?php echo $book['publish_year']; ?>
                </div>
                <div class="col-sm-6">
                    <strong>Uploaded by:</strong> <?php echo htmlspecialchars($book['uploader_name']); ?>
                </div>
            </div>
            
            <div class="d-flex gap-3">
                <?php if ($book['pdf_file']): ?>
                    <!-- Read Now - Proxy through server for correct headers -->
                     <a href="<?php echo 'download.php?id=' . $book['id'] . '&action=view'; ?>" 
           class="btn btn-warning btn-lg" 
           target="_blank">
            <i class="fas fa-book-open"></i> Read Now
        </a>
                    <!-- Download - Proxy ensures proper filename and MIME -->
                         <a href="<?php echo 'download.php?id=' . $book['id'] . '&action=download'; ?>" 
           class="btn btn-outline-secondary">
            <i class="fas fa-download"></i> Download
        </a>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>