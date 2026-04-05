-- Migration: add sitter access code to sitter_household_info
-- Run after: sql/migrations/add_sitter_info.sql

ALTER TABLE sitter_household_info
    ADD COLUMN sitter_access_code_hash VARCHAR(255) DEFAULT NULL
        COMMENT 'bcrypt hash of the sitter access passcode; NULL means no code set'
    AFTER general_notes;
