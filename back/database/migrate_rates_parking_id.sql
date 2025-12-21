-- Migration: Add parking_id to rates table
-- This links rates to specific parkings, allowing owners to set prices per parking

-- Step 1: Add parking_id column (nullable initially for existing data)
ALTER TABLE rates ADD COLUMN parking_id CHAR(36) NULL AFTER id;

-- Step 2: Add foreign key constraint
ALTER TABLE rates ADD CONSTRAINT fk_rates_parking_id
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE;

-- Step 3: Create index for performance
CREATE INDEX idx_rates_parking_id ON rates(parking_id);

-- Note: After migrating existing data, make the column NOT NULL:
-- ALTER TABLE rates MODIFY COLUMN parking_id CHAR(36) NOT NULL;
