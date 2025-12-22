-- Migration: Update reservations table for new model
-- Run this migration to update the reservations table structure

-- Step 1: Drop old columns and add new ones
-- First backup any existing data if needed

-- Add new columns to reservations table
ALTER TABLE reservations ADD COLUMN start_time DATETIME NULL AFTER parking_id;
ALTER TABLE reservations ADD COLUMN end_time DATETIME NULL AFTER start_time;
ALTER TABLE reservations ADD COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'refunded', 'completed') NOT NULL DEFAULT 'pending' AFTER end_time;
ALTER TABLE reservations ADD COLUMN is_free TINYINT(1) NOT NULL DEFAULT 0 AFTER currency;

-- Migrate old data if exists (day_of_week, start_hour, end_hour -> start_time, end_time)
-- This is optional - skip if no data exists
-- UPDATE reservations SET 
--   start_time = CONCAT(CURDATE(), ' ', start_hour, ':00:00'),
--   end_time = CONCAT(CURDATE(), ' ', end_hour, ':00:00'),
--   status = 'confirmed'
-- WHERE start_time IS NULL;

-- Make new columns NOT NULL after migration
-- ALTER TABLE reservations MODIFY COLUMN start_time DATETIME NOT NULL;
-- ALTER TABLE reservations MODIFY COLUMN end_time DATETIME NOT NULL;

-- Drop old columns (optional - can keep for backward compatibility)
-- ALTER TABLE reservations DROP COLUMN day_of_week;
-- ALTER TABLE reservations DROP COLUMN start_hour;
-- ALTER TABLE reservations DROP COLUMN end_hour;

-- Add indexes for better performance
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_reservations_time_range ON reservations(parking_id, start_time, end_time);
CREATE INDEX idx_reservations_user_status ON reservations(user_id, status);
