-- Migration: add toiletry logging table
-- Run after: sql/migrations/add_sitter_info.sql

-- Toiletry logs for tracking pet bathroom activity
CREATE TABLE IF NOT EXISTS toiletry_logs (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    pet_id          INT UNSIGNED    NOT NULL,
    log_type        ENUM('pee', 'poo') NOT NULL,
    is_accident      BOOLEAN         NOT NULL DEFAULT FALSE,
    logged_at        DATETIME        NOT NULL,
    created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_toiletry_pet FOREIGN KEY (pet_id) REFERENCES pets (id) ON DELETE CASCADE,
    INDEX idx_pet_logged (pet_id, logged_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

