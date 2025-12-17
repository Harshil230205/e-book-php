-- Create database and tables for e-book management system

CREATE DATABASE IF NOT EXISTS bookbytes;


-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    category_id INT,
    publish_year INT,
    description TEXT,
    cover_image VARCHAR(255),
    pdf_file VARCHAR(255),
    user_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(100) DEFAULT 'MYBOOK',
    site_logo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@mybook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default categories
    INSERT INTO categories (name) VALUES 
    ('Fiction'),
    ('Self-Help & Personal Development'),
    ('Business & Finance'),
    ('Psychology'),
    ('Historical Fiction'),
    ('Spirituality');

-- Insert default settings
INSERT INTO settings (site_name) VALUES ('MYBOOK');

-- Update database to handle Cloudinary URLs (longer VARCHAR for URLs)
ALTER TABLE books MODIFY COLUMN cover_image VARCHAR(500);
ALTER TABLE books MODIFY COLUMN pdf_file VARCHAR(500);

-- Add indexes for better performance
CREATE INDEX idx_books_status ON books(status);
CREATE INDEX idx_books_user_id ON books(user_id);
CREATE INDEX idx_books_category_id ON books(category_id);
