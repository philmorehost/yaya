-- Comprehensive update script to bring a v1.0 database to version 1.3

-- Add tables for RBAC system
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL UNIQUE,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add other missing tables
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `giving_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) NOT NULL,
  `account_details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `connection_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255),
  `workforce_unit_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add the author_id column to the announcements table
-- Check if the column exists before adding it to avoid errors on re-run
SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_schema = DATABASE()
     AND table_name = 'announcements'
     AND column_name = 'author_id') > 0,
    "SELECT 1",
    "ALTER TABLE `announcements` ADD COLUMN `author_id` INT(11) DEFAULT NULL AFTER `content`;"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Seed initial permissions
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
('manage_members', 'Full CRUD access to members'),
('view_members', 'View members list'),
('edit_members', 'Edit member details'),
('delete_members', 'Delete members'),
('manage_attendance', 'Full access to attendance records'),
('manage_finance', 'Full access to financial records'),
('manage_events', 'Full CRUD access to events'),
('manage_media', 'Full CRUD access to media'),
('manage_departments', 'Full CRUD access to departments'),
('manage_roles', 'Full CRUD access to roles and permissions'),
('manage_settings', 'Full access to system settings');
