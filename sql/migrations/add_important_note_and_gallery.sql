-- Migration: Add important_note column to pets and pet_photos gallery table
-- Run after the base schema (sql/init.sql) and previous migrations.
-- MySQL 8.0
--
-- ⚠️  IONOS / shared hosting: Do NOT include a USE statement here.
-- In phpMyAdmin, select your database first, then import this file.

-- Add important_note column to pets (safe to run even if column already exists)
ALTER TABLE `pets`
    ADD COLUMN IF NOT EXISTS `important_note` TEXT NULL
        AFTER `photo_url`;

-- Gallery photos table
CREATE TABLE IF NOT EXISTS `pet_photos` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pet_id`        INT UNSIGNED  NOT NULL,
    `photo_url`     VARCHAR(500)  NOT NULL,
    `caption`       VARCHAR(255)  NULL,
    `display_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_pet_photos_pet` FOREIGN KEY (`pet_id`)
        REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update existing pets to use relative image paths
UPDATE `pets` SET
    `photo_url`       = '/assets/images/jack-jack.jpg',
    `important_note`  = 'Jack-Jack is allergic to beef products. Please do not feed him any beef-based treats or food. He is also reactive to unfamiliar dogs on lead — always greet calmly and give him space.'
WHERE `name` = 'Jack-Jack';

UPDATE `pets` SET
    `photo_url`       = '/assets/images/nagi.jpg',
    `important_note`  = 'Nagi is microchipped (chip registered with national database). She is shy around loud noises and sudden movements — please approach slowly and let her come to you at her own pace.'
WHERE `name` = 'Nagi';

-- Gallery photos for Jack-Jack
INSERT IGNORE INTO `pet_photos` (`pet_id`, `photo_url`, `caption`, `display_order`)
SELECT `id`, '/assets/images/jack-jack-2.jpg', 'Jack-Jack enjoying the park', 1
FROM `pets` WHERE `name` = 'Jack-Jack';

INSERT IGNORE INTO `pet_photos` (`pet_id`, `photo_url`, `caption`, `display_order`)
SELECT `id`, '/assets/images/jack-jack-3.jpg', 'Nap time in the sun', 2
FROM `pets` WHERE `name` = 'Jack-Jack';

-- Gallery photos for Nagi
INSERT IGNORE INTO `pet_photos` (`pet_id`, `photo_url`, `caption`, `display_order`)
SELECT `id`, '/assets/images/nagi-2.jpg', 'Nagi on her morning walk', 1
FROM `pets` WHERE `name` = 'Nagi';

INSERT IGNORE INTO `pet_photos` (`pet_id`, `photo_url`, `caption`, `display_order`)
SELECT `id`, '/assets/images/nagi-3.jpg', 'Snuggle time on the sofa', 2
FROM `pets` WHERE `name` = 'Nagi';
