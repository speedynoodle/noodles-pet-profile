-- Migration: add sitter information tables
-- Run after: sql/migrations/add_admin_and_health_notes.sql

-- Household-wide information for pet sitters (single row; upsert on id=1)
CREATE TABLE IF NOT EXISTS sitter_household_info (
    id                      INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    emergency_contact_name  VARCHAR(120)    NOT NULL DEFAULT '',
    emergency_contact_phone VARCHAR(40)     NOT NULL DEFAULT '',
    vet_name                VARCHAR(120)    NOT NULL DEFAULT '',
    vet_phone               VARCHAR(40)     NOT NULL DEFAULT '',
    vet_address             VARCHAR(255)    NOT NULL DEFAULT '',
    general_notes           TEXT,
    created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Household walk schedule entries (applies to all pets)
CREATE TABLE IF NOT EXISTS walk_schedules (
    id               INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    label            VARCHAR(80)     NOT NULL,
    walk_time        TIME            NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 30,
    notes            TEXT,
    sort_order       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-pet feeding schedule entries
CREATE TABLE IF NOT EXISTS feeding_schedules (
    id               INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    pet_id           INT UNSIGNED    NOT NULL,
    meal_label       VARCHAR(80)     NOT NULL,
    feed_time        TIME            NOT NULL,
    food_description VARCHAR(255)    NOT NULL,
    notes            TEXT,
    sort_order       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feeding_pet FOREIGN KEY (pet_id) REFERENCES pets (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Seed data
-- -------------------------------------------------------

INSERT INTO sitter_household_info
    (id, emergency_contact_name, emergency_contact_phone, vet_name, vet_phone, vet_address, general_notes)
VALUES
    (1,
     'Alex & Jordan',
     '+61 400 000 000',
     'Happy Paws Vet Clinic',
     '+61 2 9000 0000',
     '123 Woof Street, Sydney NSW 2000',
     'Both dogs are friendly but can be reactive on leash with other dogs – keep a safe distance.\nThey love their routine, so try to stick to the scheduled walk and feed times.\nPlease ensure the back gate is always latched after use.')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO walk_schedules (label, walk_time, duration_minutes, notes, sort_order) VALUES
    ('Morning Walk',  '07:00:00', 30, 'Take both dogs together. Allow off-leash time in the park if available.', 1),
    ('Evening Walk',  '17:30:00', 30, 'Take both dogs together. Keep on-leash near the main road.', 2);

-- Feeding schedules for Jack-Jack (pet id = 1)
INSERT INTO feeding_schedules (pet_id, meal_label, feed_time, food_description, notes, sort_order) VALUES
    (1, 'Breakfast', '07:30:00', '1 cup Acana Regionals dry food', 'Mix with a small splash of warm water to soften.', 1),
    (1, 'Dinner',    '17:00:00', '1 cup Acana Regionals dry food', 'Add half a pouch of Ziwi Peak wet food on top as a topper.', 2);

-- Feeding schedules for Nagi (pet id = 2)
INSERT INTO feeding_schedules (pet_id, meal_label, feed_time, food_description, notes, sort_order) VALUES
    (2, 'Breakfast', '07:30:00', '¾ cup Acana Regionals dry food', 'Nagi is a slow eater – leave her bowl in her designated corner.', 1),
    (2, 'Dinner',    '17:00:00', '¾ cup Acana Regionals dry food', 'Add quarter pouch of Ziwi Peak wet food as a topper.', 2);
