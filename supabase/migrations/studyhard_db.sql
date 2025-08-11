-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2025 at 12:55 PM
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
-- Database: `studyhard_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Lo Dat', 'giang@study.com', 'Feedback', 'hello', '2025-08-05 09:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `subject_id`, `teacher_id`, `grade`, `status`, `created_at`) VALUES
(1, 'Algebra 10', 'Basic Algebra Course Grade 10', 1, 11, 10, 'active', '2025-08-05 04:21:04'),
(2, 'Geometry 10', 'Basic Geometry Course Grade 10', 1, 11, 10, 'active', '2025-08-05 04:21:04'),
(3, 'Algebra 11', 'Advanced Algebra Course Grade 11', 1, 11, 11, 'active', '2025-08-05 04:21:04'),
(4, 'Geometry 11', 'Advanced Geometry Course Grade 11', 1, 11, 11, 'active', '2025-08-05 04:21:04'),
(5, 'Calculus 12', 'Grade 12 Calculus Course', 1, 11, 12, 'active', '2025-08-05 04:21:04'),
(6, 'Geometry 12', 'Grade 12 Geometry Course', 1, 11, 12, 'active', '2025-08-05 04:21:04'),
(7, 'Math High School Exam Preparation', 'Review and practice for high school math exam', 1, 11, 12, 'active', '2025-08-05 04:21:04'),
(8, 'Applied Mathematics', 'Applications of Mathematics in practice', 1, 11, 12, 'active', '2025-08-05 04:21:04'),
(9, 'Vietnamese Literature 10', 'Vietnamese literature works grade 10', 2, 12, 10, 'active', '2025-08-05 04:21:04'),
(10, 'Writing 10', 'Writing and composition skills grade 10', 2, 12, 10, 'active', '2025-08-05 04:21:04'),
(11, 'Vietnamese Literature 11', 'Vietnamese literature works grade 11', 2, 12, 11, 'active', '2025-08-05 04:21:04'),
(12, 'Literary essay 11', 'Literary essay writing skills', 2, 12, 11, 'active', '2025-08-05 04:21:04'),
(13, 'Vietnamese Literature 12', 'Vietnamese literature works grade 12', 2, 12, 12, 'active', '2025-08-05 04:21:04'),
(14, 'Social commentary 12', 'Social essay writing skills', 2, 12, 12, 'active', '2025-08-05 04:21:04'),
(15, 'Literature High School Exam Preparation', 'Review and practice for high school entrance exam in Literature', 2, 12, 12, 'active', '2025-08-05 04:21:04'),
(16, 'World Literature', 'Learn about literature of other countries', 2, 12, 12, 'active', '2025-08-05 04:21:04'),
(17, 'English 10', 'English grammar and vocabulary grade 10', 3, 13, 10, 'active', '2025-08-05 04:22:39'),
(18, 'English 11', 'English reading and writing skills grade 11', 3, 13, 11, 'active', '2025-08-05 04:22:39'),
(19, 'English 12', 'Prepare listening, speaking, reading and writing skills for high school', 3, 13, 12, 'active', '2025-08-05 04:22:39'),
(20, 'English High School Exam Preparation', 'Review and practice high school English exam questions', 3, 13, 12, 'active', '2025-08-05 04:22:39'),
(21, 'Physics 10', 'Mechanics and Thermodynamics Grade 10', 4, 14, 10, 'active', '2025-08-05 04:22:39'),
(22, 'Physics 11', 'Electricity and Optics Grade 11', 4, 14, 11, 'active', '2025-08-05 04:22:39'),
(23, 'Physics 12', 'Nuclear and Wave Physics Grade 12', 4, 14, 12, 'active', '2025-08-05 04:22:39'),
(24, 'High School Physics Exam Preparation', 'Synthesize knowledge and practice Physics questions', 4, 14, 12, 'active', '2025-08-05 04:22:39'),
(25, 'Chemistry 10', 'Basic knowledge of Chemistry grade 10', 5, 15, 10, 'active', '2025-08-05 04:22:39'),
(26, 'Chemistry 11', 'Chemical reactions and compounds grade 11', 5, 15, 11, 'active', '2025-08-05 04:22:39'),
(27, 'Chemistry 12', 'Organic and inorganic chemistry grade 12', 5, 15, 12, 'active', '2025-08-05 04:22:39'),
(28, 'Chemistry High School Exam Preparation', 'Review Chemistry knowledge for high school exam', 5, 15, 12, 'active', '2025-08-05 04:22:39'),
(29, 'History 10', 'Ancient and medieval world history', 6, 16, 10, 'active', '2025-08-05 04:22:39'),
(30, 'History 11', 'Modern history of Vietnam and the world', 6, 16, 11, 'active', '2025-08-05 04:22:39'),
(31, 'History 12', 'Modern Vietnamese History', 6, 16, 12, 'active', '2025-08-05 04:22:39'),
(32, 'History High School Exam Preparation', 'Review history knowledge for high school exam', 6, 16, 12, 'active', '2025-08-05 04:22:39'),
(33, 'Geography 10', 'Basic geographical concepts', 7, 17, 10, 'active', '2025-08-05 04:22:39'),
(34, 'Geography 11', 'Economic and social geography grade 11', 7, 17, 11, 'active', '2025-08-05 04:22:39'),
(35, 'Geography 12', 'Geography of Vietnam and the world grade 12', 7, 17, 12, 'active', '2025-08-05 04:22:39'),
(36, 'High School Geography Exam Preparation', 'Review Geography knowledge', 7, 17, 12, 'active', '2025-08-05 04:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

CREATE TABLE `course_materials` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` enum('pdf','video','slide','link') NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `teacher_name`, `description`) VALUES
(1, 'Math', 'Mr Huu Giang', 'Mathematics for grades 10, 11, 12'),
(2, 'Literature', 'Ms Suong Mai', 'Literature subject for grades 10, 11, 12'),
(3, 'English', 'Ms Thu Hang', 'English subject for grades 10, 11, 12'),
(4, 'Physics', 'Mr Lam Sung', 'Physics subject for grades 10, 11, 12'),
(5, 'Chemistry', 'Mr Xuan Bach', 'Chemistry subject for grades 10, 11, 12'),
(6, 'History', 'Mr Vo Nguyen Giap', 'History subject for grades 10, 11, 12'),
(7, 'Geography', 'Ms Thu Thao', 'Geography subject for grades 10, 11, 12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher') NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `grade`, `subject_id`, `profile_image`, `created_at`, `updated_at`) VALUES
(8, 'Lâm Ba', 'lamba@study.com', '$2y$10$oxdw2b/dVfHJj3qUQ8oUgOt2XDmdfHBOCCyLi7pQbGywHTSoKLGpK', 'student', 10, NULL, 'default.jpg', '2025-08-05 07:46:58', '2025-08-05 07:47:54'),
(9, 'Lỗ Đạt', 'lodat@gmail.com', '$2y$10$8LMLR8gAt76fnWXZYz1NAuvkK/wRuuw36zBXytUAQKk8crv5UpUyK', 'student', 10, NULL, 'default.jpg', '2025-08-05 07:48:40', '2025-08-05 07:48:40'),
(10, 'Yến Thanh', 'yenthanh@gmail.com', '$2y$10$LPMGL4wTLxIanEwCZZd4..RE14rPospKC8KQaRAdQy6i/FBVBSfo6', 'student', 10, NULL, 'default.jpg', '2025-08-05 07:49:17', '2025-08-05 07:49:17'),
(11, 'Mr Huu Giang', 'giang@study.com', '$2y$10$Xur2LyaQUziIrnaO926utOKDgMjaDtDmTyz/v9HP5lG9kbGjqyHNm', 'teacher', NULL, 1, 'assets/img/giang.jpg', '2025-08-05 07:49:49', '2025-08-05 09:14:37'),
(12, 'Ms Suong Mai', 'mai@study.com', '$2y$10$dPIQ7LpDVkFgcVI7/WN4N.8/qCXFB4xSrkx8HQ9j.pTPxKL9tpzRS', 'teacher', NULL, 2, 'assets/img/mai.jpg', '2025-08-05 07:50:21', '2025-08-05 09:14:52'),
(13, 'Ms Thu Hang', 'hang@study.com', '$2y$10$exuIS61Xup7KsLWcE5NaNezLxm43Lp7MBmeELJzo.jrybYLUZP2Vy', 'teacher', NULL, 3, 'assets/img/hang.png\r\n', '2025-08-05 07:50:49', '2025-08-05 09:15:08'),
(14, 'Mr Lam Sung', 'sung@study.com', '$2y$10$wPBmAVSGSPEbmW4JfLmw2Otc7Iq4LJM59YUHt/jXkN9fUzV/Rnp96', 'teacher', NULL, 4, 'assets/img/sung.jpg', '2025-08-05 07:51:22', '2025-08-05 09:15:21'),
(15, 'Mr Xuan Bach', 'bach@study.com', '$2y$10$CLsqS7kKVcmzhbkC2GXLsuH3loFoonzmCUYzmK0Nvg5uME1UX9MV6', 'teacher', NULL, 5, 'assets/img/bach.jpg', '2025-08-05 07:51:49', '2025-08-05 09:15:36'),
(16, 'Mr Vo Nguyen Giap', 'giap@study.com', '$2y$10$99ginNRaiKjh8okLcVMR/.CIiZAid8vfnK5goysS4hyY/c9OJ4ep2', 'teacher', NULL, 6, 'assets/img/giap.jpg', '2025-08-05 07:52:18', '2025-08-05 09:15:56'),
(17, 'Ms Thu Thao', 'thao@study.com', '$2y$10$B5XmlPyTT0REpE0k3CXJEuvwLV2NjN9RzrkNc1gpba14Mz4pfM016', 'teacher', NULL, 7, 'assets/img/huong.jgp', '2025-08-05 07:52:46', '2025-08-05 09:16:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `course_materials`
--
ALTER TABLE `course_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD CONSTRAINT `course_materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
