-- Migration: Add admin_users and health_notes tables
-- Run after sql/init.sql has been executed.
-- MySQL 8.0

USE `pet_profiles`;

-- Admin users table
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(100)  NOT NULL,
    `password_hash` VARCHAR(255)  NOT NULL,
    `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Health notes table (admin-only)
CREATE TABLE IF NOT EXISTS `health_notes` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pet_id`     INT UNSIGNED  NOT NULL,
    `note_date`  DATE          NOT NULL,
    `weight_kg`  DECIMAL(5,2)  NULL,
    `type`       ENUM('injection','physio','fleaing','vet_visit','medication','other')
                               NOT NULL DEFAULT 'other',
    `notes`      TEXT          NOT NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_health_notes_pet` FOREIGN KEY (`pet_id`)
        REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
