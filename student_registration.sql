-- Database: student_registration
CREATE DATABASE IF NOT EXISTS `student_registration`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `student_registration`;

-- Table: applications
CREATE TABLE IF NOT EXISTS `applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `usn` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `semester` VARCHAR(30) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `dob` DATE NOT NULL,
  `address` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_applications_usn` (`usn`),
  KEY `idx_applications_email` (`email`),
  KEY `idx_applications_department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;