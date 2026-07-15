-- Migration: add toiletry access tokens to pets
-- Run after: sql/migrations/add_toiletry_logs.sql

ALTER TABLE pets ADD COLUMN toiletry_access_token VARCHAR(36) UNIQUE NULL DEFAULT NULL COMMENT 'UUID for accessing toiletry logs page';

-- Create index for faster lookups by token
CREATE INDEX idx_toiletry_token ON pets(toiletry_access_token);

-- Generate tokens for existing pets (UUIDs)
UPDATE pets SET toiletry_access_token = UUID() WHERE toiletry_access_token IS NULL;

-- Make token NOT NULL after populating
ALTER TABLE pets MODIFY COLUMN toiletry_access_token VARCHAR(36) NOT NULL UNIQUE;

