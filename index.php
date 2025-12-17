<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section position-relative ">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">READ AND ADD YOUR INSIGHT</h1>
                <p class="lead mb-4">Find your favorite book. And Read it free or low cost. And also you can add your own book.</p>
                <div class="search-box">
                    <form action="books.php" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search for a book..." value="">
                        <button type="submit" class="btn btn-light">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="./images/e-book-svg.webp" alt="Reading" class="img-fluid rounded">
            </div>
        </div>
    </div>
    
    <!-- Wave Shape Divider -->
 <div class="custom-shape-divider-bottom-1739035285">
          <svg
            data-name="Layer 1"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 1200 120"
            preserveAspectRatio="none"
          >
            <path
              d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
              opacity="0.25"
              class="shape-fill"
            ></path>
            <path
              d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
              opacity="0.5"
              class="shape-fill"
            ></path>
            <path
              d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
              class="shape-fill"
              opacity="1"
                style = "color:#ffffff;"
            ></path>
          </svg>
        </div>
</section>

<!-- Latest Books Section -->
<section class="py-5 color-e5e5e5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Latest E-Books</h2>
            </div>
        </div>
        
        <div class="row">
            <?php
            // Get latest approved books
            $query = "SELECT b.*, c.name as category_name, u.name as uploader_name 
                     FROM books b 
                     LEFT JOIN categories c ON b.category_id = c.id 
                     LEFT JOIN users u ON b.user_id = u.id 
                     WHERE b.status = 'approved' 
                     ORDER BY b.created_at DESC 
                     LIMIT 8";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0):
                while ($book = mysqli_fetch_assoc($result)):
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card book-card h-100 shadow-sm">
                        <!-- Updated to use Cloudinary URLs instead of local file paths -->
                        <img src="<?php echo $book['cover_image'] ? $book['cover_image'] : '/placeholder.svg?height=300&width=200'; ?>" 
                             class="card-img-top book-cover" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="text-muted small mb-2"><?php echo $book['publish_year']; ?></p>
                            <p class="card-text small flex-grow-1"><?php echo substr(htmlspecialchars($book['description']), 0, 100) . '...'; ?></p>
                            <div class="mt-auto">
                                <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm w-100">Read Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No books available yet.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="books.php" class="btn btn-outline-primary">View All Books</a>
        </div>
    </div>
</section>

<!-- Why Choose Section -->
<section class="py-5  bg-light-subtle">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Our E-Books?</h2>
        </div>
        
        <div class="row">
             <div class="col-md-4 text-center mb-4 add-shadow " >
                <div class="mb-3 ">
                    <i class="fas fa-book-open fa-3x text-warning"></i>
                </div>
                <h5>Vast Collection</h5>
                <p class="text-muted">Access thousands of books across multiple categories and genres.</p>
            </div>
            <div class="col-md-4 text-center mb-4 add-shadow" >
                <div class="mb-3 ">
                    <i class="fas fa-download fa-3x text-warning"></i>
                </div>
                <h5>Instant Access</h5>
                <p class="text-muted">Download and read books instantly on any device, anywhere.</p>
            </div>
            <div class="col-md-4 text-center mb-4 add-shadow">
                <div class="mb-3 ">
                    <i class="fas fa-users fa-3x text-warning"></i>
                </div>
                <h5>Best Experience</h5>
                <p class="text-muted">Easy to use interface with powerful search and filtering options.</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5  bg-body-tertiary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h3 class="fw-bold mb-3">Stay Updated</h3>
                <p class="text-muted mb-4">Subscribe to get notified about new books and updates.</p>
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Enter your email">
                    <button type="submit" class="btn btn-warning">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
