-- Create Database Database
CREATE DATABASE IF NOT EXISTS `animal_adoption`;
USE `animal_adoption`;

-- Table structure for table `admins`
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin user. 
-- Username: admin
-- Password: password123 (hashed using PHP's password_hash)
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$TVpZjbf/OrLO0Mx8eWaZyu31iZRhxkYLuwLuTKwUZCb4lAZQNfkzu');

-- Table structure for table `animals`
CREATE TABLE IF NOT EXISTS `animals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `species` varchar(50) NOT NULL,
  `color` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `health_status` enum('Healthy','Under Treatment') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
