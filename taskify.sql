-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 04:39 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taskify`
--

-- --------------------------------------------------------

--
-- Table structure for table `collaborators`
--

CREATE TABLE `collaborators` (
  `id_collab` int NOT NULL,
  `id_workspace` int NOT NULL,
  `id_user` int NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int NOT NULL,
  `id_user` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id_task` int NOT NULL,
  `id_user` int NOT NULL,
  `task_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `subject` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cover_color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id_task`, `id_user`, `task_name`, `category`, `deadline`, `subject`, `cover_color`, `status`, `description`, `created_at`) VALUES
(1, 5, 'Adsi', 'Project', '2025-06-22 23:00:00', 'proyek akhir', '', 'in_progress', '', '2025-06-21 08:02:27'),
(2, 5, 'Adsi', 'Project', '2025-06-22 00:00:00', 'proyek akhir', '#000000', 'pending', 'yea', '2025-06-21 08:02:46'),
(3, 5, 'Web', 'Project', '2025-06-23 22:59:00', 'Proyek', 'from-bs to-be', 'in_progress', '123123', '2025-06-22 15:59:54'),
(4, 5, 'Web', 'Excercise', '2025-06-23 23:00:00', 'proyek akhir', 'from-ps to-pe', 'completed', '12123123', '2025-06-22 16:00:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cover_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bio` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_picture`, `cover_picture`, `created_at`, `bio`) VALUES
(1, 'faizahmadnadhif', 'faizahmadnadhif@gmail.com', '$2y$10$Gz7/v8HhS.JJukVrlK.fROtOsMxINy1u2WZJQo1pqPYB7x0W2gdKa', NULL, NULL, '2025-06-12 05:58:12', NULL),
(2, 'admin123', 'admin123@gmail.com', '$2y$10$IVKK/M52YQ7UNu0SinyteuI90YLILuO/amnW2SeX6urRWvihwHsBi', NULL, NULL, '2025-06-12 06:04:01', NULL),
(3, 'admin22', 'admin22@gmail.com', '$2y$10$SVyfkbyHncj4XKeFP2rjpu7Y8OMOs8nZkSZ7/oRmQ0swBUjR8oYKO', NULL, NULL, '2025-06-12 09:19:58', NULL),
(4, 'admin1', 'admin1@gmail.com', '$2y$10$.JJ4Cxj2To4xVwLTdFqGIOoCalbtgR3Oa37SR53aPf/ge.qf2Yjfi', NULL, NULL, '2025-06-12 10:22:26', NULL),
(5, 'nadip123123', 'nadip123@gmail.com', '$2y$10$aX7Wzn31YtEtG7VIvxI8Y.LyFmE2Qq8U96aERG4XuHEzC5SQv5Wi2', '', 'gradient-5.png', '2025-06-21 04:44:15', 'hello there');

-- --------------------------------------------------------

--
-- Table structure for table `workspace`
--

CREATE TABLE `workspace` (
  `id_workspace` int NOT NULL,
  `id_user` int NOT NULL,
  `id_task` int NOT NULL,
  `nama_workspace` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id_task`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id_task` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
