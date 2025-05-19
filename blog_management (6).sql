-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 10:23 AM
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
-- Database: `blog_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Technology'),
(2, 'Lifestyle'),
(3, 'Career'),
(6, 'Education'),
(7, 'Mental Health'),
(8, 'Personal Development');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(2, 29, 1, 'interesting', '2025-05-18 13:15:47');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `author_id`, `created_at`, `category_id`, `image`) VALUES
(3, '🧠Technology & Programming', 'In this post, I walk you through the process of building a fully functional blog system with PHP and MySQL from scratch.', 1, '2025-05-14 12:20:30', 1, 'uploads/68285ae6073bf_techprog.jpeg'),
(4, '🌱 Lifestyle', 'Learn the best productivity-boosting habits to start your day right and stay focused', 1, '2025-05-14 13:42:38', 2, 'uploads/68285ad5b2adc_lifestyle.webp'),
(5, '🌍 Trending Topics', 'From smart homes to personalized learning—how AI is becoming a part of our daily routines.', 1, '2025-05-14 13:43:49', 2, NULL),
(6, '📝 Creative Writing', 'A reflective piece on dreams, goals, and lessons I hope to remember.', 1, '2025-05-14 13:45:06', 6, NULL),
(8, '🔧 Tech & Career', 'A personal story about the struggles of managing code before Git, and how version control became a life-saver.', 1, '2025-05-14 16:52:29', 1, NULL),
(9, '✍️ Writing & Communication', 'Writing isn’t just for authors  it trains clarity, focus, and confidence.', 1, '2025-05-14 16:54:46', 6, 'uploads/68285aa3d44f3_writing.jpg'),
(11, '🧘 Wellness & Simplicity', 'less noise, more focus', 2, '2025-05-14 17:00:46', 2, NULL),
(12, '🎯 Goal-Setting & Planning', 'A practical guide to organizing tasks that actually get done', 2, '2025-05-14 17:01:30', 3, NULL),
(17, '🌐 Digital & Culture', 'online courses and vedioes', 1, '2025-05-14 17:20:54', 1, 'uploads/6828585a64310_digital.jpg'),
(25, '🌼 Wellness & Simplicity', 'less noise, more focus', 1, '2025-05-16 12:19:00', 2, 'uploads/68285713cd9cb_simplicit.jpeg'),
(26, '💻 Technology & Programming', 'In this post, I walk you through the process of  building a simple web application using PHP and MySQL.', 17, '2025-05-16 12:25:04', 1, 'uploads/68285b1ae9e13_68285ae6073bf_techprog.jpeg'),
(27, '🌿 Lifestyle & Wellness', 'Learn the best productivity-boosting habits to stay focused and balanced.', 17, '2025-05-16 12:25:49', 2, 'uploads/68285b10c545a_lifestyle.webp'),
(28, '🔥 Trending Topics', 'From smart homes to personalized learning—how AI is transforming our lives.', 17, '2025-05-16 12:26:29', 8, NULL),
(29, '🧘 Mindfulness in a Distracted World', 'With constant notifications and digital noise, staying present has become a challenge.', 17, '2025-05-16 12:27:54', 7, 'uploads/68285afc466e4_healthy.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `reset_token`, `token_expiry`, `email`, `is_admin`, `is_active`) VALUES
(1, 'Raneem Al Zarif', '$2y$10$MFI44uqIY..VIvJHU/s15eL44FjhwWj1nffFScYAes4ou0/FEtpge', '318f0a93c057cdb64fd71fbc6b6379c3c783cd3bee4cc15426982bce51e13ac2', '2025-05-19 12:00:00', 'raneem.alzarif4@gmail.com', 0, 1),
(2, 'rama', '$2y$10$4yHHl9iWOQlIjQbNzrF2juv5ceZPfiAJfmJvS62kpek/n7m0NkLKW', NULL, NULL, '', 0, 0),
(6, 'sara', '$2y$10$bIVTyKfDic1FccnAZvkiSOIU/g3UB0PPbd8L/fsy2AiviRr1rXAbC', NULL, NULL, '', 0, 0),
(7, 'rima', '$2y$10$LePmzjg6qn/3COK3hNjNPe52Q/KtFUHazHjm2.Nx6OPmsyu582hQ.', NULL, NULL, '', 0, 1),
(9, 'sima', '$2y$10$VG4wWeVOfNIWPvqQNgpiuuwWd/FurgHh/65XdKZGYiyC.Pt/YHbVm', NULL, NULL, '', 0, 1),
(11, 'cyrine', '$2y$10$KyRPMsH2iU.40EZYnHG/0eSTpwwwFByVoO8CCBFy.gAwy4HbamBme', NULL, NULL, '', 0, 1),
(12, 'reem', '$2y$10$mpJrCsDIZmrIcsu1KtWe1.kARyBge/qRBMSuHQj8RFTCMz3tZdsw6', NULL, NULL, '', 0, 1),
(17, 'rana', '$2y$10$.tpNbx8v9T8ACExWqQ/c.e01Jjtwn.LtFHvm5Qp7ZHhbxS77EHns2', NULL, NULL, '', 0, 1),
(21, 'admin@gmail.com', '$2y$10$jb5UIrucGuPiBNpxCrgdi.9uO7ADKhy0BHEkip2H8pKdrdyNBHMCu', NULL, NULL, '', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_post_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `username_6` (`username`),
  ADD KEY `username` (`username`),
  ADD KEY `username_2` (`username`),
  ADD KEY `username_3` (`username`),
  ADD KEY `username_4` (`username`),
  ADD KEY `username_5` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_post_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
