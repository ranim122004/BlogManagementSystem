-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 07:25 PM
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
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `author_id`, `created_at`) VALUES
(3, '🧠Technology & Programming', 'In this post, I walk you through the process of building a fully functional blog system with PHP and MySQL from scratch.', 1, '2025-05-14 12:20:30'),
(4, '🌱 Lifestyle & Wellness', 'Learn the best productivity-boosting habits to start your day right and stay focused', 1, '2025-05-14 13:42:38'),
(5, '🌍 Trending Topics', 'From smart homes to personalized learning—how AI is becoming a part of our daily routines.', 1, '2025-05-14 13:43:49'),
(6, '📝 Creative Writing', 'A reflective piece on dreams, goals, and lessons I hope to remember.', 1, '2025-05-14 13:45:06'),
(8, '🔧 Tech & Career', 'A personal story about the struggles of managing code before Git, and how version control became a life-saver.', 1, '2025-05-14 16:52:29'),
(9, '✍️ Writing & Communication', 'Writing isn’t just for authors  it trains clarity, focus, and confidence.', 1, '2025-05-14 16:54:46'),
(11, '🧘 Wellness & Simplicity', 'less noise, more focus', 2, '2025-05-14 17:00:46'),
(12, '🎯 Goal-Setting & Planning', 'A practical guide to organizing tasks that actually get done', 2, '2025-05-14 17:01:30'),
(17, '🌐 Digital & Culture', 'online courses and vedioes', 1, '2025-05-14 17:20:54'),
(18, '🌐 Digital & Culture', 'online courses', 1, '2025-05-14 17:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'Raneem Al Zarif', '$2y$10$MFI44uqIY..VIvJHU/s15eL44FjhwWj1nffFScYAes4ou0/FEtpge'),
(2, 'rama', '$2y$10$4yHHl9iWOQlIjQbNzrF2juv5ceZPfiAJfmJvS62kpek/n7m0NkLKW'),
(3, 'Raneem Al Zarif', '$2y$10$TnDzLEEY/kkBbHvDI44Nwudh7PfqYmAk7Pn69w3dnThRYWth2QrPa'),
(4, 'Raneem Al Zarif', '$2y$10$OH3to3NAyjImNOq8GHYgL.bJOOe/an1BfGPLy8doRnEwxXcIrnug6'),
(5, 'Raneem Al Zarif', '$2y$10$A17EwZDSAiOu50PuL.EnT.EcrHwI77Pz6ijKC97s1kAs3SZbfuuri'),
(6, 'sara', '$2y$10$bIVTyKfDic1FccnAZvkiSOIU/g3UB0PPbd8L/fsy2AiviRr1rXAbC'),
(7, 'rima', '$2y$10$LePmzjg6qn/3COK3hNjNPe52Q/KtFUHazHjm2.Nx6OPmsyu582hQ.'),
(8, 'rima', '$2y$10$n1mwukdVqjDjtvsUR2wNNOqdHpk1Xz4QMILjKmdCfDL9Mu224LYZq'),
(9, 'sima', '$2y$10$VG4wWeVOfNIWPvqQNgpiuuwWd/FurgHh/65XdKZGYiyC.Pt/YHbVm'),
(10, 'sima', '$2y$10$sEWXn8ODe7Z5KCVrKg8I8OxeylxazzVHi2rtOvr0zsFVkXyaTrtXm'),
(11, 'cyrine', '$2y$10$KyRPMsH2iU.40EZYnHG/0eSTpwwwFByVoO8CCBFy.gAwy4HbamBme');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
