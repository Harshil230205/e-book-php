<?php
include 'config/database.php';
include 'config/session.php';
include 'config/cloudinary.php';

requireLogin();

$error = '';
$success = '';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verify user exists in database
$user_check = "SELECT id FROM users WHERE id = ?";
$user_stmt = mysqli_prepare($conn, $user_check);
mysqli_stmt_bind_param($user_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);

if (mysqli_num_rows($user_result) == 0) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category_id'];
    $publish_year = $_POST['publish_year'];
    $description = trim($_POST['description']);
    
    // Validation
    if (empty($title) || empty($author) || empty($category_id) || empty($publish_year) || empty($description)) {
        $error = 'All fields are required.';
    } elseif (!is_numeric($publish_year) || $publish_year < 1000 || $publish_year > date('Y')) {
        $error = 'Please enter a valid publish year.';
    } else {
        $cover_image_url = '';
        $pdf_file_url = '';
        
        // Cover Image Upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['cover_image']['type'], $allowed_types)) {
                // Try upload with folder first
                $upload_result = uploadToCloudinary($_FILES['cover_image']['tmp_name'], 'image', 'book_covers');
                
                // If folder upload fails, try simple upload
                if (!$upload_result['success']) {
                    $upload_result = uploadToCloudinarySimple($_FILES['cover_image']['tmp_name'], 'image');
                }
                
                if ($upload_result['success']) {
                    $cover_image_url = $upload_result['url'];
                } else {
                    $error = 'Failed to upload cover image: ' . $upload_result['error'];
                }
            } else {
                $error = 'Cover image must be JPG, JPEG, or PNG.';
            }
        }
        
        // PDF Upload
        if (!$error && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
            if ($_FILES['pdf_file']['type'] == 'application/pdf') {
                // Try upload with folder first
                $upload_result = uploadToCloudinary($_FILES['pdf_file']['tmp_name'], 'raw', 'book_pdfs');
                
                // If folder upload fails, try simple upload
                if (!$upload_result['success']) {
                    $upload_result = uploadToCloudinarySimple($_FILES['pdf_file']['tmp_name'], 'raw');
                }
                
                if ($upload_result['success']) {
                    $pdf_file_url = $upload_result['url'];
                } else {
                    $error = 'Failed to upload PDF file: ' . $upload_result['error'];
                }
            } else {
                $error = 'Please upload a valid PDF file.';
            }
        } elseif (!$error) {
            $error = 'PDF file is required.';
        }
        
        if (!$error) {
            $user_id = (int)$_SESSION['user_id'];
            $insert_query = "INSERT INTO books (title, author, category_id, publish_year, description, cover_image, pdf_file, user_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sssisssi", $title, $author, $category_id, $publish_year, $description, $cover_image_url, $pdf_file_url, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Book uploaded successfully! It will be reviewed by admin before being published.';
                // Clear form data
                $_POST = array();
            } else {
                $error = 'Failed to upload book. Please try again. Error: ' . mysqli_error($conn);
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">Upload New Book</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Cover Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">Cover Image</label>
                            <div class="border-2 border-dashed border-secondary rounded p-4 text-center">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-2">Upload cover image</p>
                                <input type="file" class="form-control" name="cover_image" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="author" class="form-label">Author</label>
                                    <input type="text" class="form-control" id="author" name="author" 
                                           value="<?php echo $_POST['author'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="publish_year" class="form-label">Publish Year</label>
                                    <input type="number" class="form-control" id="publish_year" name="publish_year" 
                                           min="1000" max="<?php echo date('Y'); ?>" value="<?php echo $_POST['publish_year'] ?? date('Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <!-- PDF Upload -->
                        <div class="mb-4">
                            <label class="form-label">PDF File</label>
                            <div class="border-2 border-dashed border-secondary rounded p-4 text-center">
                                <i class="fas fa-file-pdf fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-2">Upload PDF file</p>
                                <input type="file" class="form-control" name="pdf_file" accept=".pdf" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100 py-2 fw-bold">Upload Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>