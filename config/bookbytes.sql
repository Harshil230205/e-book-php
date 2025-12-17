-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2025 at 06:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookbytes`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `publish_year` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `pdf_file` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category_id`, `publish_year`, `description`, `cover_image`, `pdf_file`, `user_id`, `status`, `created_at`) VALUES
(1, 'To Kill a Mockingbird', 'Harper Lee', 1, 1960, 'A classic American novel exploring race, justice, and morality in the Deep South.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757583405/book_covers/uqxrsaiemg5siqysx56w.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757583415/book_pdfs/bjwit6ws3ur5uekucc8w.tmp', 2, 'approved', '2025-09-11 09:37:09'),
(2, 'The Da Vinci Code', 'Dan Brown', 3, 2003, 'A symbologist uncovers hidden codes and secret societies in a high-stakes mystery.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757583634/book_covers/ij0m7gbvrvupdg7musb7.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757583648/book_pdfs/v74frtgjwtxjrjsmfuwt.tmp', 2, 'approved', '2025-09-11 09:40:49'),
(3, 'Dune', 'Frank Herbert', 1, 1965, 'A science fiction epic about politics, religion, and ecology on the desert planet Arrakis.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757585312/book_covers/ucaq2gdho2faxlzxaqsw.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757585319/book_pdfs/yfvygzaqvpdk3xcuoe4p.tmp', 2, 'pending', '2025-09-11 10:08:40'),
(4, 'The Hobbit', 'J.R.R. Tolkien', 5, 1937, 'A hobbit embarks on an epic journey with dwarves to reclaim a dragon’s treasure.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757585398/book_covers/akjhjxhfwpg0az0zckhk.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757585412/book_pdfs/wvuqqjpszuptmfa2z0y1.tmp', 2, 'approved', '2025-09-11 10:10:13'),
(5, 'Pride and Prejudice', 'Jane Austen', 5, 1813, 'A witty love story about manners, morality, and marriage in 19th-century England.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757606093/book_covers/f9gk5nmzkho7uwgy4gqh.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757606116/book_pdfs/yzupzuqzqcfpndi0pibz.tmp', 3, 'pending', '2025-09-11 15:55:16'),
(6, 'Dracula', 'Bram Stoker', 5, 1900, 'The iconic vampire tale blending horror, mystery, and dark romance.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757606669/book_covers/ocil33jog9whsy1cqw3w.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757606709/book_pdfs/outwpzmaz3gz9jeaa5bu.tmp', 3, 'pending', '2025-09-11 16:05:09'),
(7, 'The Book Thief', 'Markus Zusak', 19, 2005, 'A young girl in Nazi Germany finds solace in books while facing unimaginable horrors.', 'https://res.cloudinary.com/dkh2cam0q/image/upload/v1757607122/book_covers/u1dv3kvftztasfzvmg3h.jpg', 'https://res.cloudinary.com/dkh2cam0q/raw/upload/v1757607223/book_pdfs/avsufaohlkxpm0b4ps2s.tmp', 3, 'pending', '2025-09-11 16:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Fiction', '2025-09-11 09:19:59'),
(2, 'Self-Help & Personal Development', '2025-09-11 09:19:59'),
(3, 'Business & Finance', '2025-09-11 09:19:59'),
(4, 'Psychology', '2025-09-11 09:19:59'),
(5, 'Historical Fiction', '2025-09-11 09:19:59'),
(6, 'Spirituality', '2025-09-11 09:19:59'),
(13, 'Literary Fiction', '2025-09-11 16:00:25'),
(14, 'Detective Fiction', '2025-09-11 16:00:25'),
(15, 'Cyberpunk', '2025-09-11 16:00:25'),
(16, 'High Fantasy', '2025-09-11 16:00:25'),
(17, 'Contemporary Romance', '2025-09-11 16:00:25'),
(18, 'Psychological Horror', '2025-09-11 16:00:25'),
(19, 'World War II Fiction', '2025-09-11 16:00:25'),
(20, 'Political Biography', '2025-09-11 16:00:25'),
(21, 'Life Coaching', '2025-09-11 16:00:25'),
(22, 'Computer Science', '2025-09-11 16:00:25'),
(23, 'True Crime', '2025-09-11 16:00:25'),
(24, 'Young Adult Fantasy', '2025-09-11 16:00:25'),
(25, 'Poetry', '2025-09-11 16:00:25'),
(26, 'Manga', '2025-09-11 16:00:25'),
(27, 'Climate Fiction', '2025-09-11 16:00:25'),
(28, 'Classic Fiction', '2025-09-11 16:02:45'),
(29, 'Cozy Mystery', '2025-09-11 16:02:45'),
(30, 'Space Opera', '2025-09-11 16:02:45'),
(31, 'Dark Fantasy', '2025-09-11 16:02:45'),
(32, 'Paranormal Romance', '2025-09-11 16:02:45'),
(33, 'Gothic Horror', '2025-09-11 16:02:45'),
(34, 'Medieval Fiction', '2025-09-11 16:02:45'),
(35, 'Scientific Biography', '2025-09-11 16:02:45'),
(36, 'Personal Finance', '2025-09-11 16:02:45'),
(37, 'Engineering Textbooks', '2025-09-11 16:02:45'),
(38, 'Travel Writing', '2025-09-11 16:02:45'),
(39, 'Young Adult Mystery', '2025-09-11 16:02:45'),
(40, 'Graphic Novels', '2025-09-11 16:02:45'),
(41, 'Fashion', '2025-09-11 16:02:45'),
(42, 'Speculative Fiction', '2025-09-11 16:02:45'),
(43, 'Contemporary Fiction', '2025-09-11 16:02:57'),
(44, 'Police Procedural', '2025-09-11 16:02:57'),
(45, 'Steampunk', '2025-09-11 16:02:57'),
(46, 'Urban Fantasy', '2025-09-11 16:02:57'),
(47, 'Historical Romance', '2025-09-11 16:02:57'),
(48, 'Supernatural Horror', '2025-09-11 16:02:57'),
(49, 'Renaissance Fiction', '2025-09-11 16:02:57'),
(50, 'Art & Artist Biography', '2025-09-11 16:02:57'),
(51, 'Mindfulness', '2025-09-11 16:02:57'),
(52, 'Legal Studies', '2025-09-11 16:02:57'),
(53, 'Food & Cooking', '2025-09-11 16:02:57'),
(54, 'Young Adult Sci-Fi', '2025-09-11 16:02:57'),
(55, 'Comic Books', '2025-09-11 16:02:57'),
(56, 'Film Studies', '2025-09-11 16:02:57'),
(57, 'Afrofuturism', '2025-09-11 16:02:57'),
(58, 'Contemporary Fiction', '2025-09-11 16:04:16'),
(59, 'Police Procedural', '2025-09-11 16:04:16'),
(60, 'Steampunk', '2025-09-11 16:04:16'),
(61, 'Urban Fantasy', '2025-09-11 16:04:16'),
(62, 'Historical Romance', '2025-09-11 16:04:16'),
(63, 'Supernatural Horror', '2025-09-11 16:04:16'),
(64, 'Renaissance Fiction', '2025-09-11 16:04:16'),
(65, 'Art & Artist Biography', '2025-09-11 16:04:16'),
(66, 'Mindfulness', '2025-09-11 16:04:16'),
(67, 'Legal Studies', '2025-09-11 16:04:16'),
(68, 'Food & Cooking', '2025-09-11 16:04:16'),
(69, 'Young Adult Sci-Fi', '2025-09-11 16:04:16'),
(70, 'Comic Books', '2025-09-11 16:04:16'),
(71, 'Film Studies', '2025-09-11 16:04:16'),
(72, 'Afrofuturism', '2025-09-11 16:04:16');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) DEFAULT 'MYBOOK',
  `site_logo` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_logo`, `updated_at`) VALUES
(1, 'bookbytes', NULL, '2025-09-11 10:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Smit Dobariya', 'admin.smit@gmail.com', '$2y$10$B9WQf5vFO6h1mI5pK0ux7uZxP4a27Yy76OjGe4hdMrdMajq9yNRaa', 'admin', '2025-09-11 09:24:08'),
(2, 'Smit', 'smit@gmail.com', '$2y$10$bppJrJ4qjZZ94NRWLh4E8OWVeWTzXR0y0I1m.noB8D.pLjsAGK8Cy', 'user', '2025-09-11 09:34:21'),
(3, 'Sujal', 'sujal@gmail.com', '$2y$10$Cqdub3/ZFMuQ6LUTV43VR.OXEwoMeRfgu9MM33o5FNLrOflLPjN2K', 'user', '2025-09-11 09:59:24'),
(4, 'Sujal', 'admin.sujal@gmail.com', '$2y$10$FNuRAlNgwS6qfgQ8VTkLVemN6vl5/59dl8690Mz5GyMeJhNCXy4Z2', 'admin', '2025-09-11 10:11:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_books_status` (`status`),
  ADD KEY `idx_books_user_id` (`user_id`),
  ADD KEY `idx_books_category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
