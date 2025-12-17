<?php
include 'config/database.php';
include 'config/session.php';

requireLogin();

$book_id = $_GET['id'] ?? 0;

if (!$book_id) {
    header('Location: mybooks.php');
    exit();
}

// Get book details - only allow editing own books
$book_query = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $book_query);
mysqli_stmt_bind_param($stmt, "ii", $book_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: mybooks.php');
    exit();
}

$book = mysqli_fetch_assoc($result);

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Handle form submission
if ($_POST) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = (int)$_POST['category_id'];
    $publish_year = (int)$_POST['publish_year'];
    $description = trim($_POST['description']);
    
    $errors = [];
    
    if (empty($title)) $errors[] = "Title is required";
    if (empty($author)) $errors[] = "Author is required";
    if ($category_id <= 0) $errors[] = "Please select a category";
    if ($publish_year < 1000 || $publish_year > date('Y')) $errors[] = "Invalid publish year";
    if (empty($description)) $errors[] = "Description is required";
    
    if (empty($errors)) {
        $update_query = "UPDATE books SET title = ?, author = ?, category_id = ?, publish_year = ?, description = ? WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssiisii", $title, $author, $category_id, $publish_year, $description, $book_id, $_SESSION['user_id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Book updated successfully!";
            // Refresh book data
            $book['title'] = $title;
            $book['author'] = $author;
            $book['category_id'] = $category_id;
            $book['publish_year'] = $publish_year;
            $book['description'] = $description;
        } else {
            $errors[] = "Failed to update book. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Book</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($book['title']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo htmlspecialchars($book['author']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $category['id'] == $book['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="publish_year" class="form-label">Publish Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="publish_year" name="publish_year" 
                                       min="1000" max="<?php echo date('Y'); ?>" 
                                       value="<?php echo $book['publish_year']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($book['description']); ?></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Note:</strong> To change the cover image or PDF file, please contact the administrator.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Book
                            </button>
                            <a href="mybooks.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to My Books
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
