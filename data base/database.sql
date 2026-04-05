-- Animal Adoption Database Schema
-- This SQL file creates the database structure for the animal adoption management system
-- It includes tables for admins, animals, and adoptions

-- Create Database
CREATE DATABASE IF NOT EXISTS `animal_adoption`;
USE `animal_adoption`;

-- Admin users table
-- Stores administrator accounts with secure password hashing
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user for initial setup
-- Username: admin
-- Password: password123 (hashed using PHP's password_hash)
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$TVpZjbf/OrLO0Mx8eWaZyu31iZRhxkYLuwLuTKwUZCb4lAZQNfkzu');

-- Animals table
-- Stores information about animals available for adoption
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

-- Adoptions table
-- Records completed adoptions with adopter information
CREATE TABLE IF NOT EXISTS `adoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_name` varchar(255) NOT NULL,
  `species` varchar(50) NOT NULL,
  `color` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `health_status` enum('Healthy','Under Treatment') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `adopter_fname` varchar(100) NOT NULL,
  `adopter_lname` varchar(100) NOT NULL,
  `adopter_phone` varchar(20) NOT NULL,
  `adopter_address` text,
  `adopted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

