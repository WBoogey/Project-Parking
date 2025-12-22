-- Migration: Update stationings table for enter/exit workflow
-- Run this migration to update the stationings table structure

-- Step 1: Update status enum to use new values (active, completed, paid)
ALTER TABLE stationings 
MODIFY COLUMN status ENUM('available', 'unavailable', 'active', 'completed', 'paid') NOT NULL DEFAULT 'active';

-- Step 2: Make end_time nullable (null when user enters, set when user exits)
ALTER TABLE stationings 
MODIFY COLUMN end_time DATETIME NULL;

-- Step 3: Add is_free column (true if user has active subscription covering the period)
ALTER TABLE stationings 
ADD COLUMN is_free TINYINT(1) NOT NULL DEFAULT 0 AFTER amount;

-- Step 4: Migrate old data (if any) - convert old statuses to new ones
UPDATE stationings SET status = 'completed' WHERE status = 'available';
UPDATE stationings SET status = 'completed' WHERE status = 'unavailable';

-- Step 5: Now remove old enum values (optional - can keep for backward compatibility)
-- ALTER TABLE stationings 
-- MODIFY COLUMN status ENUM('active', 'completed', 'paid') NOT NULL DEFAULT 'active';

-- Add index for faster lookup of active stationings
CREATE INDEX idx_stationings_active ON stationings(parking_id, status);
CREATE INDEX idx_stationings_user_active ON stationings(user_id, parking_id, status);
