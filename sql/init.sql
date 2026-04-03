-- Pet Profiles Database Initialization
-- Compatible with MySQL 5.7+ and MariaDB.
--
-- ⚠️  IONOS / shared hosting – READ BEFORE IMPORTING:
--   The two lines below (CREATE DATABASE … and USE …) are for
--   LOCAL development only.  On IONOS (or any host where your database
--   already exists and your DB user lacks CREATE DATABASE privilege):
--     1. Select your database in phpMyAdmin first.
--     2. Delete or comment out the CREATE DATABASE and USE lines below.
--     3. Then click Import.
--
-- Local development: leave the lines as-is and run:
--   mysql -u root -p < sql/init.sql

CREATE DATABASE IF NOT EXISTS `pet_profiles`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `pet_profiles`;

-- Pets table
CREATE TABLE IF NOT EXISTS `pets` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)     NOT NULL,
    `species`     VARCHAR(50)      NOT NULL DEFAULT 'Dog',
    `breed`       VARCHAR(100)     NOT NULL,
    `gender`      ENUM('Male','Female','Unknown') NOT NULL DEFAULT 'Unknown',
    `birthday`    DATE             NULL,
    `weight_kg`   DECIMAL(5,2)     NULL,
    `color`       VARCHAR(100)     NOT NULL,
    `description` TEXT             NULL,
    `personality` TEXT             NULL,
    `favourite_toy`   VARCHAR(150) NULL,
    `favourite_food`  VARCHAR(150) NULL,
    `photo_url`   VARCHAR(500)     NULL,
    `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vaccinations table
CREATE TABLE IF NOT EXISTS `vaccinations` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pet_id`        INT UNSIGNED  NOT NULL,
    `vaccine_name`  VARCHAR(150)  NOT NULL,
    `date_given`    DATE          NOT NULL,
    `next_due_date` DATE          NULL,
    `vet_name`      VARCHAR(150)  NULL,
    `notes`         VARCHAR(500)  NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_vaccinations_pet` FOREIGN KEY (`pet_id`)
        REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medical records table
CREATE TABLE IF NOT EXISTS `medical_records` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pet_id`      INT UNSIGNED  NOT NULL,
    `record_date` DATE          NOT NULL,
    `record_type` VARCHAR(100)  NOT NULL,
    `description` TEXT          NULL,
    `vet_name`    VARCHAR(150)  NULL,
    `notes`       TEXT          NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_medical_pet` FOREIGN KEY (`pet_id`)
        REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Seed data: Jack-Jack and Nagi
-- -------------------------------------------------------

INSERT INTO `pets` (
    `name`, `species`, `breed`, `gender`, `birthday`,
    `weight_kg`, `color`, `description`, `personality`,
    `favourite_toy`, `favourite_food`, `photo_url`
) VALUES
(
    'Jack-Jack',
    'Dog',
    'Shiba Inu',
    'Male',
    '2020-03-15',
    9.50,
    'Red sesame',
    'Jack-Jack is a playful and energetic Shiba Inu who loves long walks and chasing squirrels. He has the classic bold, confident personality of his breed and enjoys being the centre of attention.',
    'Bold, confident, energetic, and fiercely loyal. Jack-Jack loves to play but also appreciates a cosy nap in the sun.',
    'Squeaky tennis ball',
    'Salmon treats',
    'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=600'
),
(
    'Nagi',
    'Dog',
    'Shiba Inu',
    'Female',
    '2021-07-22',
    7.80,
    'Cream',
    'Nagi is a gentle and curious cream Shiba Inu. She is slightly more reserved than Jack-Jack but warms up quickly to people she trusts. She loves exploring new smells on her walks.',
    'Gentle, curious, sweet-natured, and occasionally mischievous. Nagi has a quiet dignity about her and loves cuddle sessions on the sofa.',
    'Stuffed bunny toy',
    'Chicken strips',
    'https://images.unsplash.com/photo-1601979031925-424e53b6caaa?w=600'
);

-- Sample vaccinations for Jack-Jack (pet_id = 1)
INSERT INTO `vaccinations` (`pet_id`, `vaccine_name`, `date_given`, `next_due_date`, `vet_name`, `notes`) VALUES
(1, 'Rabies',                   '2021-03-15', '2024-03-15', 'Dr. Smith',  'Annual booster required'),
(1, 'DHPP (Distemper combo)',   '2021-03-15', '2022-03-15', 'Dr. Smith',  'Puppy series complete'),
(1, 'Bordetella',               '2022-01-10', '2023-01-10', 'Dr. Johnson','Kennel cough prevention');

-- Sample vaccinations for Nagi (pet_id = 2)
INSERT INTO `vaccinations` (`pet_id`, `vaccine_name`, `date_given`, `next_due_date`, `vet_name`, `notes`) VALUES
(2, 'Rabies',                   '2022-07-22', '2025-07-22', 'Dr. Smith',  'Annual booster required'),
(2, 'DHPP (Distemper combo)',   '2022-07-22', '2023-07-22', 'Dr. Smith',  'Puppy series complete'),
(2, 'Bordetella',               '2023-02-14', '2024-02-14', 'Dr. Johnson','Kennel cough prevention');

-- Sample medical records for Jack-Jack (pet_id = 1)
INSERT INTO `medical_records` (`pet_id`, `record_date`, `record_type`, `description`, `vet_name`, `notes`) VALUES
(1, '2022-06-10', 'Check-up',    'Annual wellness exam. Healthy weight and coat condition.', 'Dr. Smith',   'Recommended dental clean next visit'),
(1, '2023-02-20', 'Dental',      'Routine dental cleaning performed under anaesthetic.',     'Dr. Johnson', 'All teeth in good condition');

-- Sample medical records for Nagi (pet_id = 2)
INSERT INTO `medical_records` (`pet_id`, `record_date`, `record_type`, `description`, `vet_name`, `notes`) VALUES
(2, '2023-01-15', 'Check-up',    'Annual wellness exam. Excellent health.', 'Dr. Smith',   'Weight within normal range'),
(2, '2023-08-05', 'Microchip',   'Microchip implanted for identification.', 'Dr. Johnson', 'Chip number registered with national database');
