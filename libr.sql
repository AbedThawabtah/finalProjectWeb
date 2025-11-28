-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 09:46 PM
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
-- Database: `libr`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `first_name`, `last_name`, `country`, `bio`) VALUES
(1, 'Omar', 'Khaled', 'Egypt', 'Specialist in classical Arabic literature.'),
(2, 'Layla', 'Hassan', 'Jordan', 'Writes about modern poetry.'),
(3, 'Ahmed', 'Mansour', 'Palestine', 'Researcher in Middle Eastern history.'),
(4, 'Fatima', 'Zahra', 'Morocco', 'Novelist focused on social issues.'),
(5, 'George', 'Smith', 'USA', 'Author of science fiction novels.'),
(6, 'Emily', 'Johnson', 'UK', 'Writes children’s literature.'),
(7, 'Hiroshi', 'Tanaka', 'Japan', 'Expert in Japanese culture and history.'),
(8, 'Maria', 'Rossi', 'Italy', 'Italian historian and travel writer.'),
(9, 'David', 'Cohen', 'Palestine', 'Researcher in computer science.'),
(10, 'Sara', 'Al-Sabah', 'Kuwait', 'Focuses on women’s rights and society.');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `book_type` varchar(50) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `title`, `publisher_id`, `category`, `book_type`, `original_price`, `available`) VALUES
(1, 'Book Title20000', 2, 'Category2', 'Type2', 600.00, 0),
(2, 'Book Title 2', 3, 'Category3', 'Type3', 16.00, 0),
(3, 'Book Title 3', 4, 'Category4', 'Type1', 19.00, 0),
(4, 'Book Title 4', 5, 'Category5', 'Type2', 888888.00, 0),
(5, 'Book Title 5', 6, 'Category1', 'Type3', 25.00, 0),
(6, 'Book Title 6', 7, 'Category2', 'Type1', 28.00, 0),
(7, 'Book Title 7', 8, 'Category3', 'Type2', 31.00, 0),
(8, 'Book Title 8', 9, 'Category4', 'Type3', 34.00, 0),
(9, 'Book Title 9', 10, 'Category5', 'Type1', 37.00, 0),
(10, 'Book Title 10', 1, 'Category1', 'Type2', 40.00, 0),
(11, 'Book Title 11', 2, 'Category2', 'Type3', 43.00, 1),
(12, 'Book Title 12', 3, 'Category3', 'Type1', 46.00, 1),
(13, 'Book Title 13', 4, 'Category4', 'Type2', 49.00, 1),
(14, 'Book Title 14', 5, 'Category5', 'Type3', 52.00, 1),
(15, 'Book Title 15', 6, 'Category1', 'Type1', 55.00, 1),
(16, 'Book Title 16', 7, 'Category2', 'Type2', 58.00, 1),
(17, 'Book Title 17', 8, 'Category3', 'Type3', 61.00, 1),
(18, 'Book Title 18', 9, 'Category4', 'Type1', 64.00, 1),
(19, 'Book Title 19', 10, 'Category5', 'Type2', 67.00, 1),
(20, 'Book Title 20', 1, 'Category1', 'Type3', 70.00, 1),
(21, 'Book Title 21', 2, 'Category2', 'Type1', 73.00, 1),
(22, 'Book Title 22', 3, 'Category3', 'Type2', 76.00, 1),
(23, 'Book Title 23', 4, 'Category4', 'Type3', 79.00, 1),
(24, 'Book Title 24', 5, 'Category5', 'Type1', 82.00, 1),
(25, 'Book Title 25', 6, 'Category1', 'Type2', 85.00, 1),
(26, 'Book Title 26', 7, 'Category2', 'Type3', 88.00, 1),
(27, 'Book Title 27', 8, 'Category3', 'Type1', 91.00, 1),
(28, 'Book Title 28', 9, 'Category4', 'Type2', 94.00, 1),
(29, 'Book Title 29', 10, 'Category5', 'Type3', 97.00, 1),
(30, 'Book Title 30', 1, 'Category1', 'Type1', 100.00, 1),
(31, '3333rrr', NULL, 'ققققققق', 'Type2', 33.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bookauthor`
--

CREATE TABLE `bookauthor` (
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookauthor`
--

INSERT INTO `bookauthor` (`book_id`, `author_id`) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6),
(6, 7),
(7, 8),
(8, 9),
(9, 10),
(10, 1),
(11, 2),
(12, 3),
(13, 4),
(14, 5),
(15, 6),
(16, 7),
(17, 8),
(18, 9),
(19, 10),
(20, 1),
(21, 2),
(22, 3),
(23, 4),
(24, 5),
(25, 6),
(26, 7),
(27, 8),
(28, 9),
(29, 10),
(30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrower`
--

CREATE TABLE `borrower` (
  `borrower_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  `contact_info` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrower`
--

INSERT INTO `borrower` (`borrower_id`, `first_name`, `last_name`, `type_id`, `contact_info`) VALUES
(1, 'First1', 'Last1', 2, 'user1@mail.com'),
(2, 'First2', 'Last2', 3, 'user2@mail.com'),
(3, 'First3', 'Last3', 1, 'user3@mail.com'),
(4, 'First4', 'Last4', 2, 'user4@mail.com'),
(5, 'First5', 'Last5', 3, 'user5@mail.com'),
(6, 'First6', 'Last6', 1, 'user6@mail.com'),
(7, 'First7', 'Last7', 2, 'user7@mail.com'),
(8, 'First8', 'Last8', 3, 'user8@mail.com'),
(9, 'First9', 'Last9', 1, 'user9@mail.com'),
(10, 'First10', 'Last10', 2, 'user10@mail.com'),
(11, 'First11', 'Last11', 3, 'user11@mail.com'),
(12, 'First12', 'Last12', 1, 'user12@mail.com'),
(13, 'First13', 'Last13', 2, 'user13@mail.com'),
(14, 'First14', 'Last14', 3, 'user14@mail.com'),
(15, 'First15', 'Last15', 1, 'user15@mail.com'),
(16, 'First16', 'Last16', 2, 'user16@mail.com'),
(17, 'First17', 'Last17', 3, 'user17@mail.com'),
(18, 'First18', 'Last18', 1, 'user18@mail.com'),
(19, 'First19', 'Last19', 2, 'user19@mail.com'),
(20, 'First20', 'Last20', 3, 'user20@mail.com'),
(21, 'First21', 'Last21', 1, 'user21@mail.com'),
(22, 'First22', 'Last22', 2, 'user22@mail.com'),
(23, 'First23', 'Last23', 3, 'user23@mail.com'),
(24, 'First24', 'Last24', 1, 'user24@mail.com'),
(25, 'First25', 'Last25', 2, 'user25@mail.com'),
(26, 'First26', 'Last26', 3, 'user26@mail.com'),
(27, 'First27', 'Last27', 1, 'user27@mail.com'),
(28, 'First28', 'Last28', 2, 'user28@mail.com'),
(29, 'First29', 'Last29', 3, 'user29@mail.com'),
(30, 'First30', 'Last30', 1, 'user30@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `borrowertype`
--

CREATE TABLE `borrowertype` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowertype`
--

INSERT INTO `borrowertype` (`type_id`, `type_name`) VALUES
(3, 'Citizen'),
(1, 'School Student'),
(2, 'University Student');

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `loan_id` int(11) NOT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`loan_id`, `borrower_id`, `book_id`, `period_id`, `loan_date`, `due_date`, `return_date`) VALUES
(1, 2, 2, 2, '2025-01-02', '2025-02-02', NULL),
(2, 3, 3, 3, '2025-01-03', '2025-02-03', NULL),
(3, 4, 4, 4, '2025-01-04', '2025-02-04', NULL),
(4, 5, 5, 5, '2025-01-05', '2025-02-05', NULL),
(5, 6, 6, 6, '2025-01-06', '2025-02-06', NULL),
(6, 7, 7, 7, '2025-01-07', '2025-02-07', NULL),
(7, 8, 8, 8, '2025-01-08', '2025-02-08', NULL),
(8, 9, 9, 9, '2025-01-09', '2025-02-09', NULL),
(9, 10, 10, 10, '2025-01-10', '2025-02-10', NULL),
(10, 11, 11, 1, '2025-01-11', '2025-02-11', NULL),
(11, 12, 12, 2, '2025-01-12', '2025-02-12', NULL),
(12, 13, 13, 3, '2025-01-13', '2025-02-13', NULL),
(13, 14, 14, 4, '2025-01-14', '2025-02-14', NULL),
(14, 15, 15, 5, '2025-01-15', '2025-02-15', NULL),
(15, 16, 16, 6, '2025-01-16', '2025-02-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loanperiod`
--

CREATE TABLE `loanperiod` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(50) NOT NULL,
  `days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loanperiod`
--

INSERT INTO `loanperiod` (`period_id`, `period_name`, `days`) VALUES
(1, '3 Days', 3),
(2, '1 Week', 7),
(3, '2 Weeks', 14),
(4, '3 Weeks', 21),
(5, '1 Month', 30),
(6, '6 Weeks', 42),
(7, '2 Months', 60),
(8, '3 Months', 90),
(9, 'Half Year', 180),
(10, 'Full Year', 365);

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `publisher_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `contact_info` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`publisher_id`, `name`, `city`, `country`, `contact_info`) VALUES
(1, 'Dar Al-Hikma Publishing', 'Cairo', 'Egypt', 'info@daralhikma.eg'),
(2, 'Al-Quds Press', 'Ramallah', 'Palestine', 'contact@alqpress.ps'),
(3, 'Beirut Book House', 'Beirut', 'Lebanon', 'support@beirutbooks.lb'),
(4, 'Maktabat Al-Fikr', 'Amman', 'Jordan', 'info@fikr.jo'),
(5, 'Royal Publishing', 'Dubai', 'UAE', 'contact@royalpub.ae'),
(6, 'Oxford Press', 'Oxford', 'UK', 'help@oxpress.co.uk'),
(7, 'Cambridge Books', 'Cambridge', 'UK', 'info@cambooks.co.uk'),
(8, 'McGraw Global', 'New York', 'USA', 'service@mcgrawglobal.com'),
(9, 'Shinobi Press', 'Tokyo', 'Japan', 'contact@shinobipress.jp'),
(10, 'La Storia Editore', 'Rome', 'Italy', 'info@lastoria.it');

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `sale_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `sale_date` date NOT NULL,
  `sale_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`sale_id`, `book_id`, `borrower_id`, `sale_date`, `sale_price`) VALUES
(1, 1, 2, '2025-03-02', 45.00),
(2, 2, 3, '2025-03-03', 50.00),
(3, 3, 4, '2025-03-04', 55.00),
(4, 4, 5, '2025-03-05', 60.00),
(5, 5, 6, '2025-03-06', 65.00),
(6, 6, 7, '2025-03-07', 70.00),
(7, 7, 8, '2025-03-08', 75.00),
(8, 8, 9, '2025-03-09', 80.00),
(9, 9, 10, '2025-03-10', 85.00),
(10, 10, 11, '2025-03-11', 90.00);

--
-- Triggers `sale`
--
DELIMITER $$
CREATE TRIGGER `trg_book_sold` AFTER INSERT ON `sale` FOR EACH ROW BEGIN
    UPDATE Book SET available = FALSE WHERE book_id = NEW.book_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','student') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$AbIY2R1sHhtO0lVvC5x3H.DUOa3kC1X5A1x74Yz9yH8nK3o.YuXzO', 'admin'),
(2, 'rayyan', 'r@r.com', '$2y$10$c16vxNFNuisCUDO5d6Fa1.k16sSAZE7eGHTQG4/P4JJNKE.xf6G8S', 'admin'),
(5, 'eeee', 'eeeee@eee.com', '$2y$10$AbIY2R1sHhtO0lVvC5x3H.DUOa3kC1X5A1x74Yz9yH8nK3o.YuXzO', 'staff'),
(7, 'yyyy', 'yyyy@yyy.com', '$2y$10$u5fByFEcM6NpTfhdCcaL1OsFFja3S/toIcHK6JSIsR0ovnnD69LgS', 'student'),
(8, 'jovani', 'sss@ss.com', '$2y$10$AbIY2R1sHhtO0lVvC5x3H.DUOa3kC1X5A1x74Yz9yH8nK3o.YuXzO', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `publisher_id` (`publisher_id`);

--
-- Indexes for table `bookauthor`
--
ALTER TABLE `bookauthor`
  ADD PRIMARY KEY (`book_id`,`author_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `borrower`
--
ALTER TABLE `borrower`
  ADD PRIMARY KEY (`borrower_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `borrowertype`
--
ALTER TABLE `borrowertype`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `borrower_id` (`borrower_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `loanperiod`
--
ALTER TABLE `loanperiod`
  ADD PRIMARY KEY (`period_id`),
  ADD UNIQUE KEY `period_name` (`period_name`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`sale_id`),
  ADD UNIQUE KEY `book_id` (`book_id`),
  ADD KEY `borrower_id` (`borrower_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `borrower`
--
ALTER TABLE `borrower`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `borrowertype`
--
ALTER TABLE `borrowertype`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `loanperiod`
--
ALTER TABLE `loanperiod`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `publisher`
--
ALTER TABLE `publisher`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`publisher_id`) REFERENCES `publisher` (`publisher_id`);

--
-- Constraints for table `bookauthor`
--
ALTER TABLE `bookauthor`
  ADD CONSTRAINT `bookauthor_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookauthor_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE CASCADE;

--
-- Constraints for table `borrower`
--
ALTER TABLE `borrower`
  ADD CONSTRAINT `borrower_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `borrowertype` (`type_id`);

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `borrower` (`borrower_id`),
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `loan_ibfk_3` FOREIGN KEY (`period_id`) REFERENCES `loanperiod` (`period_id`);

--
-- Constraints for table `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `sale_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `sale_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `borrower` (`borrower_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
