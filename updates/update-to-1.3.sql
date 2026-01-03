-- Update script to bring the database to version 1.3
-- Adds author tracking to announcements.

ALTER TABLE `announcements` ADD COLUMN `author_id` INT(11) DEFAULT NULL AFTER `content`;
