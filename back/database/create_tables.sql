-- Table des utilisateurs (Single Table Inheritance avec rôle)
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('customer', 'owner', 'admin') NOT NULL DEFAULT 'customer',
    stripe_customer_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des parkings (Parking)
CREATE TABLE parkings (
    id CHAR(36) PRIMARY KEY,
    location VARCHAR(191) NOT NULL,
    capacity INT NOT NULL,
    owner_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des tarifs (Rate) - liée à un parking spécifique
CREATE TABLE rates (
    id CHAR(36) PRIMARY KEY,
    parking_id CHAR(36) NOT NULL,
    type ENUM(
        'hourly',
        'daily',
        'weekly_subscription',
        'monthly_subscription',
        'yearly_subscription'
    ) NOT NULL,
    calculation_rule VARCHAR(191) NOT NULL,
    price FLOAT NOT NULL,
    hourly_discount FLOAT NULL,
    duration VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE
);

-- Table des réservations (Reservation)
CREATE TABLE reservations (
    id CHAR(36) PRIMARY KEY,
    day_of_week VARCHAR(20) NOT NULL,
    start_hour VARCHAR(10) NOT NULL,
    end_hour VARCHAR(10) NOT NULL,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    rate_id CHAR(36) NULL,
    -- Stripe payment fields
    stripe_session_id VARCHAR(255) NULL,
    stripe_payment_status ENUM('pending', 'success', 'failed', 'refunded', 'cancelled') NULL,
    amount INT NULL,
    currency VARCHAR(10) NULL DEFAULT 'eur',
    paid_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE SET NULL
);

-- Table des abonnements (Subscription)
CREATE TABLE subscriptions (
    id CHAR(36) PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    rate_id CHAR(36) NOT NULL,
    weekly_slots JSON,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    -- Stripe payment fields
    stripe_session_id VARCHAR(255) NULL,
    stripe_payment_status ENUM('pending', 'success', 'failed', 'refunded', 'cancelled') NULL,
    amount INT NULL,
    currency VARCHAR(10) NULL DEFAULT 'eur',
    paid_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE RESTRICT
);

-- Table des stationnements (Stationing)
CREATE TABLE stationings (
    id CHAR(36) PRIMARY KEY,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('available', 'unavailable') NOT NULL,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    rate_id CHAR(36) NULL,
    -- Stripe payment fields
    stripe_session_id VARCHAR(255) NULL,
    stripe_payment_status ENUM('pending', 'success', 'failed', 'refunded', 'cancelled') NULL,
    amount INT NULL,
    currency VARCHAR(10) NULL DEFAULT 'eur',
    paid_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE SET NULL
);

-- Table des plages d'ouvertures (Schedule)
CREATE TABLE schedules (
    id CHAR(36) PRIMARY KEY,
    opening_days VARCHAR(100) NOT NULL,
    opening_hours VARCHAR(100) NOT NULL
);

-- Table de liaison parking <-> schedule (many-to-many)
CREATE TABLE parking_schedules (
    parking_id CHAR(36) NOT NULL,
    schedule_id CHAR(36) NOT NULL,
    PRIMARY KEY (parking_id, schedule_id),
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);

-- Index pour optimiser les recherches
CREATE INDEX idx_users_stripe_customer_id ON users(stripe_customer_id);
CREATE INDEX idx_rates_parking_id ON rates(parking_id);
CREATE INDEX idx_reservations_stripe_session ON reservations(stripe_session_id);
CREATE INDEX idx_subscriptions_stripe_session ON subscriptions(stripe_session_id);
CREATE INDEX idx_stationings_stripe_session ON stationings(stripe_session_id);
CREATE INDEX idx_subscriptions_rate_id ON subscriptions(rate_id);
CREATE INDEX idx_stationings_rate_id ON stationings(rate_id);
