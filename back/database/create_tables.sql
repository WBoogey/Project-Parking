-- Table des utilisateurs (Single Table Inheritance avec rôle)
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('customer', 'owner', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des tarifs (Rate)
CREATE TABLE rates (
    id CHAR(36) PRIMARY KEY,
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
    duration VARCHAR(50) NULL
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

-- Table des réservations (Reservation)
CREATE TABLE reservations (
    id CHAR(36) PRIMARY KEY,
    day_of_week VARCHAR(20) NOT NULL,
    start_hour VARCHAR(10) NOT NULL,
    end_hour VARCHAR(10) NOT NULL,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    rate_id CHAR(36) NULL,
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
    rate FLOAT NOT NULL,
    weekly_slots JSON,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE
);

-- Table des stationnements (Stationing)
CREATE TABLE stationings (
    id CHAR(36) PRIMARY KEY,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('available', 'unavailable') NOT NULL,
    user_id CHAR(36) NOT NULL,
    parking_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE
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
