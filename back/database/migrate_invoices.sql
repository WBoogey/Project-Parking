-- Migration: Create invoices table
-- Run this migration to create the invoices table

CREATE TABLE IF NOT EXISTS invoices (
    id CHAR(36) PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    type ENUM('reservation', 'stationing', 'subscription') NOT NULL,
    reference_id CHAR(36) NOT NULL,
    amount INT NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'eur',
    status ENUM('draft', 'issued', 'paid', 'cancelled') NOT NULL DEFAULT 'issued',
    description TEXT NOT NULL,
    issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE
);

-- Indexes for better performance
CREATE INDEX idx_invoices_user ON invoices(user_id);
CREATE INDEX idx_invoices_reference ON invoices(type, reference_id);
CREATE INDEX idx_invoices_number ON invoices(invoice_number);
CREATE INDEX idx_invoices_status ON invoices(status);
