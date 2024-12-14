-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 14, 2024 at 09:47 AM
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
-- Database: `attendance_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `admin_id` varchar(50) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `add_course`
--

CREATE TABLE `add_course` (
  `admin_id` varchar(50) NOT NULL,
  `course_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`) VALUES
('ad_01', 9);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `atten_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `std_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `attendance_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`atten_id`, `session_id`, `std_id`, `date`, `time`, `status`, `attendance_time`) VALUES
(1, 4111, 101, '2024-11-14', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(2, 4131, 101, '2024-11-13', '09:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(3, 4311, 101, '2024-11-13', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(4, 4151, 101, '2024-11-14', '10:50:00', 'PRESENT', '2024-12-07 13:46:19'),
(5, 4111, 102, '2024-11-14', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(6, 4131, 102, '2024-11-13', '09:00:00', 'ABSENT', '2024-12-07 13:46:19'),
(7, 4311, 102, '2024-11-13', '10:00:00', 'ABSENT', '2024-12-07 13:46:19'),
(8, 4151, 102, '2024-11-14', '10:50:00', 'PRESENT', '2024-12-07 13:46:19'),
(9, 4111, 103, '2024-11-14', '10:00:00', 'ABSENT', '2024-12-07 13:46:19'),
(10, 4131, 103, '2024-11-13', '09:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(11, 4311, 103, '2024-11-13', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(12, 4151, 103, '2024-11-14', '10:50:00', 'ABSENT', '2024-12-07 13:46:19'),
(13, 4111, 104, '2024-11-14', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(14, 4131, 104, '2024-11-13', '09:00:00', 'ABSENT', '2024-12-07 13:46:19'),
(15, 4311, 104, '2024-11-13', '10:00:00', 'PRESENT', '2024-12-07 13:46:19'),
(16, 4151, 104, '2024-11-14', '10:50:00', 'PRESENT', '2024-12-07 13:46:19'),
(34, 4329, 101, '2024-12-14', '13:09:06', 'ABSENT', '2024-12-14 12:36:20'),
(35, 4329, 102, '2024-12-14', '07:40:59', 'PRESENT', '2024-12-14 12:37:41'),
(36, 4329, 103, '2024-12-14', '07:41:33', 'PRESENT', '2024-12-14 12:41:33'),
(37, 4330, 101, '2024-12-14', '13:11:01', 'PRESENT', '2024-12-14 13:11:01'),
(38, 4330, 102, '2024-12-14', '13:21:41', 'ABSENT', '2024-12-14 13:21:41');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` varchar(50) NOT NULL,
  `course_title` varchar(100) DEFAULT NULL,
  `credit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_title`, `credit`) VALUES
('CSE-411', 'Design and Analysis of Algorithms', 3),
('CSE-412', 'Algorithm Lab', 2),
('CSE-413', 'Database management system ', 3),
('CSE-414', 'Database Management System Lab', 2),
('CSE-415', 'Microprocessor', 3),
('MAT-431', 'Numerical Methods', 3);

-- --------------------------------------------------------

--
-- Table structure for table `creates`
--

CREATE TABLE `creates` (
  `teacher_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participates`
--

CREATE TABLE `participates` (
  `std_id` int(11) NOT NULL,
  `course_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participates`
--

INSERT INTO `participates` (`std_id`, `course_id`) VALUES
(101, 'CSE-411'),
(101, 'CSE-413'),
(101, 'CSE-415'),
(101, 'MAT-431'),
(102, 'CSE-411'),
(102, 'CSE-413'),
(102, 'CSE-415'),
(102, 'MAT-431'),
(103, 'CSE-411'),
(103, 'CSE-413'),
(103, 'CSE-415'),
(103, 'MAT-431'),
(104, 'CSE-411'),
(104, 'CSE-413'),
(104, 'CSE-415'),
(104, 'MAT-431');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `course_id` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session_id`, `course_id`, `duration`, `start_time`, `Date`, `teacher_id`, `code`) VALUES
(4111, 'CSE-411', 45, '10:00:00', '2024-11-14', 2002, NULL),
(4131, 'CSE-413', 60, '09:00:00', '2024-11-13', 2001, NULL),
(4151, 'CSE-415', 45, '10:50:00', '2024-11-14', 2004, NULL),
(4311, 'MAT-431', 45, '10:00:00', '2024-11-13', 2003, NULL),
(4324, 'CSE-413', 60, '20:47:00', '2024-12-07', 2001, 'hAtPLI'),
(4325, 'CSE-413', 30, '21:54:00', '2024-12-07', 2001, 'eA7KPD'),
(4326, 'CSE-413', 60, '09:15:00', '2024-12-08', 2001, '41za6Y'),
(4327, 'CSE-413', 30, '11:45:00', '2024-12-08', 2001, 'gpOTE3'),
(4329, 'CSE-413', 30, '12:10:00', '2024-12-14', 2001, '2jy7Oz'),
(4330, 'CSE-413', 30, '13:10:00', '2024-12-14', 2001, 'MUCmL2');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `std_id` int(11) NOT NULL,
  `std_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`std_id`, `std_name`, `user_id`, `email`) VALUES
(101, 'A', 1, 'a101@gmail.com'),
(102, 'B', 2, 'b102@gmail.com'),
(103, 'C', 3, 'c103@gmail.com'),
(104, 'D', 4, 'd.104@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `takes`
--

CREATE TABLE `takes` (
  `std_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `takes`
--

INSERT INTO `takes` (`std_id`, `session_id`) VALUES
(101, 4111),
(101, 4131),
(101, 4151),
(101, 4311),
(102, 4111),
(102, 4131),
(102, 4151),
(102, 4311),
(103, 4111),
(103, 4131),
(103, 4151),
(103, 4311),
(104, 4111),
(104, 4131),
(104, 4151),
(104, 4311);

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `teacher_name`, `title`, `email`, `user_id`) VALUES
(2001, 'Rudra Pratap Dev Nath ', 'Associate Professor', 'rudra@gmail.com', 5),
(2002, 'Mohammad Sanaullah Chowdhury', 'Professor', 'msc@gmail.com', 6),
(2003, 'Kazi Ashrafuzzaman', 'Professor', 'ashraf@gmail.com', 7),
(2004, 'Atiqur Rahman', 'Associate Professor', 'atiq@gmail.com', 8);

-- --------------------------------------------------------

--
-- Table structure for table `teaches`
--

CREATE TABLE `teaches` (
  `teacher_id` int(11) NOT NULL,
  `course_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teaches`
--

INSERT INTO `teaches` (`teacher_id`, `course_id`) VALUES
(2001, 'CSE-413'),
(2001, 'CSE-414'),
(2002, 'CSE-411'),
(2003, 'MAT-431'),
(2004, 'CSE-415');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `role`, `email`, `password`) VALUES
(1, 'A', 'student', 'a101@gmail.com', '$2y$10$JtYbVo8FmfLxzDIkYuWHM.rey7JOrklmfovBEOylcbBSXkvrswMVy'),
(2, 'B', 'student', 'b102@gmail.com', '$2y$10$squNNxCD9G2T/hIe/mASwOAj7Uvy0GElgqI71Zr3RFUKIyOI487we'),
(3, 'C', 'student', 'c103@gmail.com', '$2y$10$03GQWG7LuoMz3v4L0/VCLe41kQQB868jv5mJWf5HA28Aozpkgs8jC'),
(4, 'D', 'student', 'd.104@gmail.com', '$2y$10$0ktmuqMSHAtH/UZjsZ5.UeNrzE1rBVrjYqXGZTkPO1yUiHkPCtBTe'),
(5, 'Rudra Pratap Dev Nath', 'teacher', 'rudra@gmail.com', '$2y$10$l9HfrPvG7avmDC4QTPWgW.xSGKm3QYwv7Ttw1mH4JFJWXRTXnYqKq'),
(6, 'Mohammad Sanaullah Chowdhury', 'teacher', 'msc@gmail.com', '$2y$10$D0WaZTXzGSHMOW5bRNlmeOBC0ARtGjWhtr4ZK5/PL3QLPgBLTLaAK'),
(7, 'Kazi Ashrafuzzaman', 'teacher', 'ashraf@gmail.com', '$2y$10$3a8oMAtjDZ86e6jI2LgHPOEUKGw0DkUrU0l1R/HKs/yd9QFEJgsVC'),
(8, 'Atiqur Rahman', 'teacher', 'atiq@gmail.com', '$2y$10$qCKJI6PH0KLBfKe/vRyAPOIXJGzW7O7DqdqKSigtSb5eEp8Aes7GG'),
(9, 'Admin', 'admin', 'admin@example.com', '$2y$10$oLFGGHXBjvqX5CazaxWXkeoT0oZJ/CSZM6iAiPc7llbEdaHXsbVga'),
(10, 'Admin2', 'admin', 'admin@example2.com', '$2y$10$TrGoQ10GEgSlU8Kak41iIu7HuQSoosxUz.kKmYmYIsdp0w/Z5XF.W');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adds`
--
ALTER TABLE `adds`
  ADD PRIMARY KEY (`admin_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `add_course`
--
ALTER TABLE `add_course`
  ADD PRIMARY KEY (`admin_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`atten_id`),
  ADD KEY `std_id` (`std_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `creates`
--
ALTER TABLE `creates`
  ADD PRIMARY KEY (`teacher_id`,`session_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `participates`
--
ALTER TABLE `participates`
  ADD PRIMARY KEY (`std_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`std_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `takes`
--
ALTER TABLE `takes`
  ADD PRIMARY KEY (`std_id`,`session_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `fk_user_email` (`email`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `teaches`
--
ALTER TABLE `teaches`
  ADD PRIMARY KEY (`teacher_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `atten_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4331;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `std_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2006;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adds`
--
ALTER TABLE `adds`
  ADD CONSTRAINT `adds_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `adds_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `add_course`
--
ALTER TABLE `add_course`
  ADD CONSTRAINT `add_course_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `add_course_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`std_id`) REFERENCES `student` (`std_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`);

--
-- Constraints for table `creates`
--
ALTER TABLE `creates`
  ADD CONSTRAINT `creates_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `creates_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`);

--
-- Constraints for table `participates`
--
ALTER TABLE `participates`
  ADD CONSTRAINT `participates_ibfk_1` FOREIGN KEY (`std_id`) REFERENCES `student` (`std_id`),
  ADD CONSTRAINT `participates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `session_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `takes`
--
ALTER TABLE `takes`
  ADD CONSTRAINT `takes_ibfk_1` FOREIGN KEY (`std_id`) REFERENCES `student` (`std_id`),
  ADD CONSTRAINT `takes_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`);

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `fk_user_email` FOREIGN KEY (`email`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `teaches`
--
ALTER TABLE `teaches`
  ADD CONSTRAINT `teaches_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `teaches_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
