-- Update script to bring a pre-RBAC database to version 1.1
-- This adds the permissions and role_permissions tables and seeds the initial permissions.

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

-- Seed initial permissions.
-- Using INSERT IGNORE to prevent errors if the permissions already exist for some reason.
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
