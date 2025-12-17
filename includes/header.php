<?php
    include_once 'config/database.php';
    include_once 'config/session.php';



    // Get site settings
    $settings_query  = "SELECT * FROM settings LIMIT 1";
    $settings_result = mysqli_query($conn, $settings_query);
    $settings        = mysqli_fetch_assoc($settings_result);
    $site_name       = $settings['site_name'] ?? 'MYBOOK';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Book Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Updated CSS for attractive navbar and hero section */
        body {
            padding-top: 76px; /* Account for fixed navbar */
        }

        .book-card {
            transition: transform 0.2s;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-cover {
            height: 300px;
            object-fit: cover;
        }

        /* Enhanced Hero Section with Gradient */
        .hero-section {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 25%, #ffa726 50%, #ff9800 75%, #f57c00 100%);
            color: white;
            padding: 100px 0 150px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .hero-section > .container {
            position: relative;
            z-index: 2;
        }

        /* Enhanced Navbar with Gradient */
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
        }

        .header-bg {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 25%, #ffa726 50%, #ff9800 75%, #f57c00 100%);
            color: #ffffff !important;
            padding: 15px 0;
           box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
            position: fixed !important;
            top: 0;
            width: 100%;
            z-index: 1050;
            backdrop-filter: blur(10px);
        }

        .pos-relative {
            position: relative;
        }

        .white {
            color: #ffffff !important;
        }

        .nav-link.white:hover {
            color: #ffe0b3 !important;
            transition: color 0.3s ease;
            transform: translateY(-1px);
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Search Box Styling */
        .search-box .form-control {
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .search-box .btn {
            border-radius: 50px;
            padding: 12px 20px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .color-e5e5e5{
            background-color: #e5e5e5;
        }

        /* Hero Section Text Effects */
        .hero-section h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
        }

        .hero-section .lead {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        /* Hero Image Animation */
        .hero-section img {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
/* **** why choose *****/
        .add-shadow{
            padding: 30px;

        }
        .add-shadow:hover{
            background-color: #ffffff;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

/* **** shape divider *****/
.custom-shape-divider-bottom-1739035285 {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.custom-shape-divider-bottom-1739035285 svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 117px;
}

.custom-shape-divider-bottom-1739035285 .shape-fill {
    fill: #ffffff !important;
}

/***** End shape divider *****/


    </style>
</head>
<body>


<!-- UPDATED HEADER.PHP NAVBAR -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- HEADER NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light pos-relative header-bg">
    <div class="container">
        <a class="navbar-brand white" href="index.php">BookBytes</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link white" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link white" href="books.php"><i class="fas fa-book me-1"></i>Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link white" href="upload.php"><i class="fas fa-upload me-1"></i>Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link white" href="my-books.php"><i class="fas fa-user-book me-1"></i>My Books</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Hello, username -->
                    <li class="nav-item">
                        <!-- <span class="nav-link white"> Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span> -->
                    </li>

                    <!-- Admin Panel button -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link white" href="admin/"><i class="fas fa-cog me-1"></i>Admin Panel</a>
                        </li>
                    <?php endif; ?>

                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link white" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Log Out</a>
                    </li>
                <?php else: ?>
                    <!-- If not logged in -->
                    <li class="nav-item">
                        <a class="nav-link white" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Log In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white" href="register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
