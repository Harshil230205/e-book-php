<?php
require_once 'config/database.php';

// Create admin user if not exists
$admin_email = 'admin@ebook.com';
$admin_password = 'admin123';
$admin_name = 'Administrator';

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create admin user
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')");
    $stmt->bind_param("sss", $admin_name, $admin_email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_password . "<br>";
        echo "<a href='login.php'>Login as Admin</a>";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
} else {
    echo "Admin user already exists!<br>";
    echo "Email: " . $admin_email . "<br>";
    echo "Password: " . $admin_password . "<br>";
    echo "<a href='login.php'>Login as Admin</a>";
}
?>
