-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 05:46 PM
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
-- Database: `royaluni`
--

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `admission_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `degree` enum('BS Computer Science','BS Information Technology','BS Software Engineering') DEFAULT NULL,
  `metric_marks` int(11) NOT NULL,
  `metric_stream` enum('Science','Arts') NOT NULL DEFAULT 'Science',
  `fsc_marks` int(11) NOT NULL,
  `fsc_stream` enum('Pre-Engineering','ICS','ICOM','Pre-Medical') NOT NULL DEFAULT 'Pre-Engineering',
  `profile_picture` varchar(255) NOT NULL,
  `admission_status` enum('Pending','Approved','Rejected','not applied') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`admission_id`, `user_id`, `dob`, `age`, `phone_number`, `address`, `degree`, `metric_marks`, `metric_stream`, `fsc_marks`, `fsc_stream`, `profile_picture`, `admission_status`) VALUES
(4, 1, '2002-02-26', 22, '030043893849', 'knjjkn', 'BS Computer Science', 999, 'Science', 99090, 'ICOM', 'Screenshot 2024-10-01 055824.png', 'Approved'),
(7, 18, '2000-01-01', 24, '030043893849', 'jncdksj', 'BS Computer Science', 112, 'Science', 122, 'Pre-Engineering', 'Screenshot 2024-09-30 142941.png', 'Approved'),
(8, 19, '2005-07-27', 19, '030043893849', 'njj', 'BS Computer Science', 789, 'Science', 786, 'Pre-Engineering', 'Screenshot 2024-09-30 162059.png', 'Approved'),
(9, 23, '2005-01-30', 19, '030043893849', 'weds', 'BS Computer Science', 223, 'Science', 32, 'ICOM', 'WhatsApp Image 2024-11-11 at 9.58.36 AM.jpeg', 'Approved'),
(10, 24, '2004-02-02', 20, '030043893849', ', sc,cm ', 'BS Information Technology', 23, 'Arts', 132, 'ICOM', 'download.jpg', 'not applied'),
(11, 26, '2000-01-01', 24, '030043893849', 'kjkjbjjkhb', 'BS Software Engineering', 230, 'Arts', 342, 'ICOM', '1733233710746.jpg', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `created_by`, `created_at`) VALUES
(8, 'red', 'sc ds', 6, '2024-11-27 16:12:27'),
(9, 'red', 'sc ds', 6, '2024-11-27 16:17:57'),
(10, 'u', 'hbjh', 6, '2024-11-27 16:19:17'),
(11, 'u', 'hbjh', 6, '2024-11-27 16:19:22'),
(12, 'u', 'hbjh', 6, '2024-11-27 16:21:04'),
(13, 'ujh', 'hbhjbj', 6, '2024-11-27 16:21:18'),
(14, 'mnd', 'mdv', 6, '2024-11-27 16:23:28'),
(15, 'hy', 'everyone', 6, '2024-11-27 18:02:05'),
(16, 'hy', 'mnjkn,', 6, '2024-11-28 22:02:42'),
(17, 'ji', 'm lk', 6, '2024-11-28 22:02:56');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `deadline` date NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` enum('Present','Absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `course_id`, `student_id`, `attendance_date`, `status`) VALUES
(4, 10, 1, '2024-11-29', 'Absent'),
(5, 8, 1, '2024-11-29', 'Absent'),
(6, 7, 1, '2024-11-29', 'Absent'),
(7, 8, 1, '2024-11-30', 'Present'),
(8, 8, 23, '2024-11-30', 'Absent'),
(9, 7, 1, '2024-11-30', 'Absent'),
(10, 7, 1, '2024-12-01', 'Absent'),
(11, 7, 19, '2024-12-01', 'Absent'),
(12, 1, 24, '2024-12-02', 'Absent'),
(13, 10, 1, '2024-12-12', 'Absent'),
(14, 1, 24, '2024-12-12', 'Absent'),
(15, 1, 18, '2024-12-12', 'Absent'),
(16, 10, 1, '2024-12-14', 'Absent'),
(17, 10, 1, '2024-12-16', 'Absent'),
(18, 10, 26, '2024-12-16', 'Absent'),
(19, 1, 24, '2024-12-16', 'Present'),
(20, 1, 18, '2024-12-16', 'Present'),
(21, 1, 26, '2024-12-16', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `course_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_description`) VALUES
(1, 'Introduction to Programming', 'Learn the basics of programming with a focus on problem-solving using languages like Python.'),
(2, 'Data Structures and Algorithms', 'Study the core concepts of data structures and algorithms, including their implementation and optimization.'),
(3, 'Operating Systems', 'Explore how operating systems work, including process management, memory management, and file systems.'),
(4, 'Database Management Systems', 'Understand database concepts, design, and SQL for managing and querying data.'),
(5, 'Software Engineering', 'Learn the principles of software development, including requirements analysis, design, and testing.'),
(6, 'Software Project Management', 'Gain knowledge on managing software projects, including planning, execution, and monitoring.'),
(7, 'Software Design and Architecture', 'Learn about design patterns, software architecture, and best practices for scalable systems.'),
(8, 'Agile Development Methods', 'Study agile methodologies such as Scrum and Kanban for effective team collaboration.'),
(9, 'Web Application Development', 'Develop full-stack web applications using modern frameworks and technologies.'),
(10, 'Software Quality Assurance', 'Learn techniques for ensuring software reliability through testing and validation.'),
(11, 'Introduction to Networking', 'Understand the fundamentals of computer networks, including protocols and configurations.'),
(12, 'Cybersecurity Basics', 'Learn how to protect systems from cyber threats with a focus on security principles.'),
(13, 'Cloud Computing', 'Explore cloud technologies, including virtualization and cloud service models.'),
(14, 'IT Infrastructure Management', 'Gain insight into managing IT systems, hardware, and software in organizations.'),
(15, 'Mobile App Development', 'Develop mobile applications for Android and iOS platforms using modern tools.'),
(16, 'it', 'my course');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `title`, `email`, `description`, `created_at`, `status`) VALUES
(9, 'aa', 'teacher@gmail.com', 'zJNAKZ', '2024-11-30 10:19:47', 'completed'),
(10, 'svdkmnkcj', 'admin@gmail.com', 'axkmlsk\r\n', '2024-12-01 08:55:01', 'completed'),
(11, 'bjmbh', 'royalch@gmail.com', 'uhh', '2024-12-01 09:34:14', 'completed'),
(12, 'sedf', 'teacher@gmail.com', 'wfwaf\r\n\\', '2024-12-01 09:35:49', 'completed'),
(13, 'rwer', 'royalch@gmail.com', 'rwr', '2024-12-01 09:35:55', 'completed'),
(14, 'rew34rq', 'royalch@gmail.com', 'as,dmlqwdl;kmq;lwd\r\nqwd\r\n\r\nqwe\r\ndqed\r\nqewd\r\nqw\r\ned\r\nqw\r\ne\r\nq\r\new\r\n\r\nqwde\r\nq\r\nwed\r\n\r\nqe\r\nd\r\nq\r\ne\r\nq\r\new\r\nq\r\nwd\r\nqe\r\nd\r\nqe\r\nfd\r\nqe\r\nf\r\ne\r\nf\r\nef\r\n\r\nqw', '2024-12-01 09:36:14', 'completed'),
(15, 'da', 'teacher@gmail.com', 'suijdciusnc', '2024-12-01 09:36:39', 'completed'),
(16, 'fail', 'royalch@gmail.com', 'djasdj', '2024-12-03 10:04:22', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `payment_date`, `course_id`) VALUES
(25, 1, '2024-11-29 15:43:42', 12),
(26, 1, '2024-11-29 15:48:02', 11),
(27, 1, '2024-11-29 15:53:19', 7),
(28, 23, '2024-11-30 08:52:38', 8),
(29, 19, '2024-12-01 11:20:57', 15),
(30, 19, '2024-12-01 11:21:04', 7),
(31, 24, '2024-12-02 04:07:20', 13),
(32, 24, '2024-12-02 04:07:26', 1),
(33, 18, '2024-12-02 16:05:19', 7),
(34, 18, '2024-12-11 13:08:34', 1),
(35, 18, '2024-12-13 13:09:53', 2),
(36, 26, '2024-12-14 01:51:43', 1),
(37, 19, '2024-12-16 17:37:10', 8),
(38, 1, '2024-12-17 02:35:06', 10);

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`enrollment_id`, `student_id`, `course_id`, `enrollment_date`) VALUES
(3, 1, 10, '2024-11-28 22:42:15'),
(4, 1, 7, '2024-11-28 23:36:28'),
(5, 1, 8, '2024-11-28 23:39:51'),
(6, 1, 15, '2024-11-28 23:42:34'),
(7, 1, 16, '2024-11-28 23:42:39'),
(8, 1, 12, '2024-11-28 23:42:44'),
(9, 23, 13, '2024-11-30 08:51:19'),
(10, 23, 8, '2024-11-30 08:51:29'),
(11, 19, 7, '2024-12-01 11:20:35'),
(12, 19, 15, '2024-12-01 11:20:44'),
(13, 18, 7, '2024-12-01 12:09:36'),
(14, 24, 13, '2024-12-02 04:06:57'),
(15, 24, 1, '2024-12-02 04:07:08'),
(16, 18, 1, '2024-12-03 04:24:05'),
(17, 18, 2, '2024-12-03 04:24:08'),
(18, 18, 12, '2024-12-03 04:24:15'),
(19, 18, 9, '2024-12-13 13:09:26'),
(20, 18, 16, '2024-12-13 13:10:28'),
(21, 26, 13, '2024-12-14 01:51:14'),
(22, 26, 1, '2024-12-14 01:51:23'),
(23, 26, 10, '2024-12-14 01:54:35'),
(24, 19, 8, '2024-12-16 17:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_courses`
--

CREATE TABLE `teacher_courses` (
  `teacher_course_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_courses`
--

INSERT INTO `teacher_courses` (`teacher_course_id`, `teacher_id`, `course_id`) VALUES
(12, 22, 8),
(14, 20, 10),
(15, 21, 10),
(16, 16, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Teacher','Student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'royal', 'ch', 'royalch@gmail.com', '$2y$10$MRBMG/IMMgW9rscGCuTl2eAXX3DNplVG6/RPPG/AKH7mDW7aEBGim', 'Student', '2024-11-26 17:11:59'),
(6, 'admin', 'ch', 'admin@gmail.com', '$2y$10$TcyXa5DY4ld.0lWC4GEUee99/6si3545qFueDyKPRBYM403iSJbIG', 'Admin', '2024-11-27 02:13:41'),
(16, 'Teacher', 'Name', 'teacher@gmail.com', '$2y$10$Om.duSyAkKzs5Ul28PlWcOc83nRRBcw74wLOioqn0U.lOmFwmG6gC', 'Teacher', '2024-11-27 03:22:58'),
(18, 'abdul', 'ch', 'a.w.comsats41@gmail.com', '$2y$10$YvpsATgQcQhj/Hl9nlbLpOq0YDDP1GmBdAc39KWBw8priilmDfCPy', 'Student', '2024-11-27 14:25:42'),
(19, 'a', 'h', 'earner404@gmail.com', '$2y$10$Aj1w/fpTS4RXgH28XEyS..6PAbqbI.qnbW8PC0H/gORx86dbPsF5O', 'Student', '2024-11-27 16:45:10'),
(20, 'John', 'Doe', 'johndoe@gmail.com', '$2y$10$Om.duSyAkKzs5Ul28PlWcOc83nRRBcw74wLOioqn0U.lOmFwmG6gC', 'Teacher', '2024-11-28 23:38:23'),
(21, 'Jane', 'Smith', 'janesmith@gmail.com', '$2y$10$Om.duSyAkKzs5Ul28PlWcOc83nRRBcw74wLOioqn0U.lOmFwmG6gC', 'Teacher', '2024-11-28 23:38:23'),
(22, 'Mark', 'son', 'markjohnson@gmail.com', '$2y$10$Om.duSyAkKzs5Ul28PlWcOc83nRRBcw74wLOioqn0U.lOmFwmG6gC', 'Teacher', '2024-11-28 23:38:23'),
(23, 'royal', 'q', 'st1@gmail.com', '$2y$10$pijUqv9ZK0a/2JeqscYBSeCCcE7kX.gimk8GDsSJnROQkGaww.TyK', 'Student', '2024-11-30 08:49:34'),
(24, 'rafy', 'rafy', 'rafy@gmail.com', '$2y$10$vQgmKR.HzuBpaMJgD7GR9OZRbqgkgiPILAZNNUDJ4RUC7rhzIGW2G', 'Student', '2024-12-02 04:04:59'),
(26, 'royal', 'ch', 'royalch1@gmail.com', '$2y$10$pn8abdhwO5alNz2Onm8UL.TcovXGhdgdpQPiJ73xwERqC3benO8ei', 'Student', '2024-12-14 01:42:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`admission_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `teacher_course_id` (`teacher_course_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_ibfk_1` (`student_id`),
  ADD KEY `payments_ibfk_2` (`course_id`);

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  ADD PRIMARY KEY (`teacher_course_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `admission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  MODIFY `teacher_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `admissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`teacher_course_id`) REFERENCES `teacher_courses` (`teacher_course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `student_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  ADD CONSTRAINT `teacher_courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `teacher_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
