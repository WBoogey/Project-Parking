-- Migration: Update subscriptions table
-- Add rate_id column (FK to rates), Stripe payment columns, and remove old rate column

-- Step 1: Add rate_id column if it doesn't exist
ALTER TABLE subscriptions
ADD COLUMN IF NOT EXISTS rate_id CHAR(36) NULL AFTER end_date;

-- Step 2: Add Stripe payment columns
ALTER TABLE subscriptions
ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS stripe_payment_status ENUM('pending', 'success', 'failed', 'refunded', 'cancelled') NULL,
ADD COLUMN IF NOT EXISTS amount INT NULL,
ADD COLUMN IF NOT EXISTS currency VARCHAR(10) NULL DEFAULT 'eur',
ADD COLUMN IF NOT EXISTS paid_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS refunded_at TIMESTAMP NULL;

-- Step 3: Migrate existing rate values to rate_id (if rate column exists and has data)
-- This creates a new rate entry for each unique rate value and links it
-- Note: Run this manually if you have existing data to migrate

-- Step 4: Add foreign key constraint for rate_id
-- First check if constraint doesn't already exist
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'subscriptions'
    AND CONSTRAINT_NAME = 'fk_subscriptions_rate_id'
);

-- Add FK if it doesn't exist (MySQL 8.0+ syntax)
-- ALTER TABLE subscriptions
-- ADD CONSTRAINT fk_subscriptions_rate_id
-- FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE RESTRICT;

-- Step 5: Drop old rate column after migration is verified
-- WARNING: Only run this after verifying data migration
-- ALTER TABLE subscriptions DROP COLUMN IF EXISTS rate;

-- Verification query
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'subscriptions'
ORDER BY ORDINAL_POSITION;
