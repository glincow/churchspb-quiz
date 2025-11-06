-- Database initialization script for ChurchSPB Quiz Application
-- Creates all necessary tables for the quiz system

CREATE TABLE IF NOT EXISTS `questionnaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `header` text,
  `comment` text,
  `type` varchar(50) DEFAULT 'quiz',
  `sort` int(11) DEFAULT 0,
  `limits` text,
  `required` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `questionnaire_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_list` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT 'text',
  `sort` int(11) DEFAULT 0,
  `limits` text,
  `required` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_list` (`id_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `questionnaire_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_list` varchar(50) NOT NULL,
  `value` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_list` (`id_list`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a sample questionnaire for testing
INSERT INTO `questionnaire` (`name`, `header`, `comment`, `type`, `sort`, `required`) VALUES
('Sample Quiz', 'Welcome to the Sample Quiz', 'This is a test questionnaire', 'quiz', 1, 0);

-- Get the last inserted ID for the questionnaire
SET @questionnaire_id = LAST_INSERT_ID();

-- Insert sample questions
INSERT INTO `questionnaire_list` (`id_list`, `name`, `type`, `sort`, `required`) VALUES
(CONCAT('q_', @questionnaire_id), 'What is your name?', 'text', 1, 1),
(CONCAT('q_', @questionnaire_id), 'What is your email?', 'text', 2, 1),
(CONCAT('q_', @questionnaire_id), 'How would you rate this quiz?', 'text', 3, 0);
